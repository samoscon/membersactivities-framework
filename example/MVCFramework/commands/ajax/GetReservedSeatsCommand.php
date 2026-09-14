<?php
/**
 * Specialization of a Command
 *
 * @package commands\ajax
 * @version 1.0
 * @copyright (c) 2025, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace commands\ajax;

/**
 * Specialization of a Command
 *
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
class GetReservedSeatsCommand extends \controllerframework\controllers\Command {
    
    /**
     * Specialization of the execute method of Command
     * 
     * @param \controllerframework\registry\Request $request
     * @return int Status
     */
    #[\Override]
    public function doExecute(\controllerframework\registry\Request $request): int {
        $results = array();
        $id = filter_var($request->get('id'), FILTER_VALIDATE_INT);
        try {
            $activity = \model\Activity::find($id);
        } catch (\Exception $exc) {
            $request->addFeedback($exc->getMessage());
            return self::CMD_ERROR;
        }
        
        //related costitems
        $costitems = array();
        $reservedSeatsDescription = '';
        $default = $first = $reduced = 0;
        foreach (\model\Costitem::findAll('WHERE activity_id = '. $activity->getId()) as $costitem) {
            switch ($costitem->description) {
                case "default":
                    $default = intval($costitem->price);
                    break;
                case "reduced":
                    $reduced = intval($costitem->price);
                    break;
                case "first":
                    $first = intval($costitem->price);
                    break;
                default:
                    break;
            }
            //related subscriptions
            foreach (\model\Subscription::findAll('WHERE costitem_id = '. $costitem->getId()) as $subscription) {
                $reservedSeatsDescription .= $subscription->remark;
            }
            $costitems[] = $costitem;
        }
        $activity->costitems = $costitems;
        
        $reservedSeatsArray = array();
        foreach(explode(';',$reservedSeatsDescription) as $seat) {
            if($seat) {                
                $reservedSeatsArray[] = json_decode($seat, true);
            }
        }
        
        $reservedSeats = array('reservedSeats' => $reservedSeatsArray);
        $priceDefault = ['priceDefault' => $default];
        $priceFirst = ['priceFirst' => $first];
        $priceReduced = ['priceReduced' => $reduced];

        
        $results[] = array('map' => [$reservedSeats, $priceDefault, $priceFirst, $priceReduced]);
        $this->addResponses($request, ['results' => $results]);        
        return self::CMD_DEFAULT;        
    }
    
    /**
     * Specialization of getLevelOfLoginRequired
     */
    #[\Override]
    protected function getLevelOfLoginRequired(): void {
        $this->setLoginLevel(new \controllerframework\sessions\NoLoginRequired());        
    }
}