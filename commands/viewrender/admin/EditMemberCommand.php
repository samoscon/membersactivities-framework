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
class EditMemberCommand extends \controllers\Command {
    
    /**
     * Specialization of the execute method of Command
     * 
     * @param \registry\Request $request
     * @return int
     */
    #[\Override]
    public function doExecute(\registry\Request $request): int {
        /** Variables */
        $nameIsEmpty  = $emailIsEmpty = false;
        $emailAlreadyExists = false;               
        $properties = array();
        
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
        if($member->isComposite()) {
            $member->children = $member->getChildren();
        }
        
        $member->payments = \model\Payment::findAll('WHERE member_id = '. $member->getId());
        foreach ($member->payments as $payment) {
            $payment->subscriptions = \model\Subscription::findAll('WHERE payment_id = '. $payment->getId());
        }
 
        $potentialParents = \model\Member::findAll("WHERE parent_id <> {$id} AND id <> {$id} ORDER BY name");

        /** Check that the page was requested from itself via the POST method. */
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $properties['name'] = $name = filter_var($request->get('name'), FILTER_UNSAFE_RAW);
            $responses['nameIsEmpty'] = $nameIsEmpty = $name ? false : true;
            
            $properties['lastname'] = $lastname = 'lastname';
            $responses['lastnameIsEmpty'] = $lastnameIsEmpty = $lastname ? false : true;
            
            $properties['email'] = $email = filter_var($request->get('email'), FILTER_VALIDATE_EMAIL);
            $responses['emailIsEmpty'] = $emailIsEmpty = $email ? false : true;

            if (!$nameIsEmpty && !$lastnameIsEmpty && !$emailIsEmpty) {
                $member->update($properties);                
                return self::CMD_OK;
            }
        } 
            
        /** the page was requested via the GET method or the POST method did not return a status. */
        $responses['member'] = $member;        
        $responses['potentialParents'] = $potentialParents;
        $responses['returnpath'] = 'searchMembers';        
        $this->addResponses($request, $responses);
        return self::CMD_DEFAULT;
    }

    /**
     * Specialization of getLevelOfLoginRequired
     */
    #[\Override]
    protected function getLevelOfLoginRequired(): void {
        $this->setLoginLevel(new \sessions\NoLoginRequired());
    }

}