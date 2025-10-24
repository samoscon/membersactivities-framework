<?php
/**
 * Specialization of a Command
 *
 * @package commands\admin
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace commands\admin;

/**
 * Specialization of a Command
 *
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
class DeleteMemberCommand extends \controllers\Command {
    
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
            $member = \model\Member::find($id);
        } catch (\Exception $exc) {
            $request->addFeedback($exc->getMessage());
            return self::CMD_ERROR;
        }

        /** Check that the page was requested from itself via the POST method. */
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            try {
                $member->delete();
            } catch (\Exception $exc) {
                $request->addFeedback($exc->getMessage());
                return self::CMD_ERROR;
            }
            
            return self::CMD_OK;
        }

        /** the page was requested via the GET method or the POST method did not return a status. */
        $responses = array();
        $responses['subscriptionsExisting'] = \model\Subscription::findAll('WHERE member_id = '.$member->getId())->count() > 0;
        $responses['member'] = $member;
        $responses['returnpath'] = 'searchMembers';
        
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