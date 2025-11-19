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
class DeleteCostitemCommand extends \controllerframework\controllers\Command {
    
    /**
     * Specialization of the execute method of Command
     * 
     * @param \controllerframework\registry\Request $request
     * @return int
     */
    public function doExecute(\controllerframework\registry\Request $request): int {
        /** Variables */
        $id = filter_var($request->get('id'), FILTER_VALIDATE_INT);
        if(!$id) {
            $request->set('errorcode', 'wrongID');
            $request->addFeedback("Wrong ID");
            return self::CMD_ERROR;
        }
                
        try {
            $costitem = \model\Costitem::find($id);
            $costitem->activity->date =date('d/m/Y', strtotime($costitem->activity->date));
            $costitem->subscriptionsExisting = \model\Subscription::findAll('WHERE costitem_id = '.$costitem->getId())->count() > 0;
        } catch (\Exception $exc) {
            $request->addFeedback($exc->getMessage());
            return self::CMD_ERROR;
        }
        
        /** Check that the page was requested from itself via the POST method. */
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $costitem->delete();
            
            $request->set('forwardqueryparams', ['id' => $costitem->activity_id]);
            return self::CMD_OK;
        }

        /** the page was requested via the GET method or the POST method did not return a status. */
        $responses = array();
        $responses['costitem'] = $costitem;
        $responses['returnpath'] = 'editActivity?id='. $costitem->activity_id;
        
        $this->addResponses($request, $responses);
        return self::CMD_DEFAULT;
    }

    /**
     * Specialization of getLevelOfLoginRequired
     */
    protected function getLevelOfLoginRequired(): void {
        $this->setLoginLevel(new \controllerframework\sessions\AdminLogin());
    }

}