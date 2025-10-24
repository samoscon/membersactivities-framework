<?php
/**
 * Specialization of a Command
 *
 * @package commands\admin
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace commands\viewrender\admin;

/**
 * Specialization of a Command
 *
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
class DeleteActivityCommand extends \controllers\Command {
    
    /**
     * Specialization of the execute method of Command
     * 
     * @param \registry\Request $request
     * @return int
     */
    public function doExecute(\registry\Request $request): int {
        /** Variables */
        $id = filter_var($request->get('id'), FILTER_VALIDATE_INT);
        if(!$id) {
            $request->addFeedback("Geen correct id opgegeven.");
            return self::CMD_ERROR;
        }
                
        try {
            $activity = \model\Activity::find($id);
        } catch (\Exception $exc) {
            $request->addFeedback($exc->getMessage());
            return self::CMD_ERROR;
        }
//        if($activity->isComposite()) {
//            $request->addFeedback("Activity has subactivities. Please remove subactivities from this activity before deleting this activity.");
//            return self::CMD_ERROR;
//        }
//        $activity->activity = \model\Activity::find($activity->activity_id);
        $activity->date = date('d/m/Y', strtotime($activity->date));

        /** Check that the page was requested from itself via the POST method. */
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            try {
                $activity->delete();
            } catch (\Exception $exc) {
                $request->addFeedback($exc->getMessage());
                return self::CMD_ERROR;
            }
            
            return self::CMD_OK;
        }

        /** the page was requested via the GET method or the POST method did not return a status. */
        $responses = array();
        $responses['costitemsExisting'] = \model\Costitem::findAll('WHERE activity_id = '.$activity->getId())->count() > 0;
        $responses['activity'] = $activity;
        $responses['returnpath'] = 'admin';
        
        $this->addResponses($request, $responses);
        return self::CMD_DEFAULT;
    }

    /**
     * Specialization of getLevelOfLoginRequired
     */
    protected function getLevelOfLoginRequired(): void {
        $this->setLoginLevel(new \sessions\AdminLogin());
    }

}