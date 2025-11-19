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
class CreateMemberCommand extends \controllerframework\controllers\Command {
    
    /**
     * Specialization of the execute method of Command
     * 
     * @param \controllerframework\registry\Request $request
     * @return int
     */
    
    #[\Override]
    public function doExecute(\controllerframework\registry\Request $request): int {
        /** variables */
        $nameIsEmpty = $emailIsEmpty = false;
        $emailAlreadyExists = false;               
        $properties = array();
        
        /** Check that the page was requested from itself via the POST method. */
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $properties['name'] = $name = filter_var($request->get('name'), FILTER_UNSAFE_RAW);
            $nameIsEmpty = $name ? false : true;
                       
            $properties['email'] = $email = filter_var($request->get('email'), FILTER_VALIDATE_EMAIL);
            $emailIsEmpty = $email ? false : true;

            $memberid = $this->reg->getLoginManager()->validateUsername($email);
            $emailAlreadyExists = $memberid ? true : false;
            
            if (!$nameIsEmpty && !$emailIsEmpty && !$emailAlreadyExists) {
                \model\Member::insert($properties);
                return self::CMD_OK;
            }
        }
        
        /** the page was requested via the GET method or the POST method did not return a status. */
        $this->addResponses($request, [
            'nameIsEmpty' => $nameIsEmpty,
            'emailIsEmpty' => $emailIsEmpty,
            'emailAlreadyExists' => $emailAlreadyExists,
            'returnpath' => 'admin']);
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