<?php
/**
 * Specialization of a Command
 *
 * @package commands\user
 * @version 1.0
 * @copyright (c) 2025, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace commands\user;

/**
 * Specialization of a Command
 *
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
class PaymentConfirmationCommand extends \controllerframework\controllers\CommandDecorator {
    
    /**
     * Specialization of the execute method of Command
     * 
     * @param \controllerframework\registry\Request $request
     * @return ?int Status or null
     */
    #[\Override]
    public function doExecuteDecorator(\controllerframework\registry\Request $request): ?int {
        /** Put your code here.  */
        $responses['feedbackPaymentNotFound'] = 'We could not find your payment. Please contact '._MAILREPLYTO;
        $responses['feedbackPaymentNotPaid'] = 'Your payment has not been executed. In case of problems, please contact '._MAILREPLYTO;
        
        $this->addResponses($request, $responses);
        return null;
    }
    
    /**
     * Specialization of initCommand
     */
    #[\Override]
    public function initCommand(): void {
        $classname = '\\'.(new \ReflectionClass(get_called_class()))->getName();
        $classname = str_replace('commands', 'membersactivities\commands', $classname);
       $this->setCommand(new $classname);        
    }
    
    /**
     * Specialization of getLevelOfLoginRequired
     */
    protected function getLevelOfLoginRequired(): void {
        if(_MINLEVELTOLOGIN === 'A') {               
            $this->setLoginLevel(new \controllerframework\sessions\NoLoginRequired());
        } else {
            $this->setLoginLevel(new \controllerframework\sessions\UserLogin());
        }
    }

}
