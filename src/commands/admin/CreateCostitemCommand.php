<?php
/**
 * Specialization of a Command
 *
 * @package membersactivities\commands\admin
 * @version 1.0
 * @copyright (c) 2025, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace membersactivities\commands\admin;

/**
 * Specialization of a Command
 *
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
class CreateCostitemCommand extends \controllerframework\controllers\Command {
    
    /**
     * Specialization of the execute method of Command
     * 
     * @param \controllerframework\registry\Request $request
     * @return int
     */
    #[\Override]
    public function doExecute(\controllerframework\registry\Request $request): int {
        /** variables */
        $descriptionIsEmpty = $priceIsEmpty = false;
        $type = 'W';
        $properties = array();

        $activityid = filter_var($request->get('id'), FILTER_VALIDATE_INT);
        if(!$activityid) {
            $request->set('errorcode', 'wrongID');
            $request->addFeedback("Wrong ID");
            return self::CMD_ERROR;
        }               
        
        try {
            $activity = \model\Activity::find($activityid);
        } catch (\Exception $exc) {
            $request->addFeedback($exc->getMessage());
            return self::CMD_ERROR;
        }
        
        $properties['activity_id'] = $activityid;

        /** Check that the page was requested from itself via the POST method. */
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $properties['description'] = $description = filter_var($request->get('description'), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $descriptionIsEmpty = $description ? false : true;
            
            $price = filter_var($request->get('price'), FILTER_VALIDATE_FLOAT);
            if(!$price){
                if($request->get('price') == "0"){
                    $price = 0.00;
                } else {
                    $priceIsEmpty = true;
                }
            }
            $properties['price'] = $price; 
            $properties['type'] = $type;

            if (!$descriptionIsEmpty && !$priceIsEmpty) {
                \model\Costitem::insert($properties);
                
                $request->set('forwardqueryparams', ['id' => $activityid]);
                return self::CMD_OK;
            }
        }
        
        /** the page was requested via the GET method or the POST method did not return a status. */
        $this->addResponses($request, [
            'descriptionIsEmpty' => $descriptionIsEmpty,
            'priceIsEmpty' => $priceIsEmpty,
            'activity' => $activity,
            'type' => $type,
            'returnpath' => 'editActivity?id='. $activityid]);
        return self::CMD_DEFAULT;
    }

    /**
     * Specialization of getLevelOfLoginRequired
     */
    #[\Override]
    protected function getLevelOfLoginRequired(): void {
        $this->setLoginLevel(new \controllerframework\sessions\AdminLogin());
    }

}