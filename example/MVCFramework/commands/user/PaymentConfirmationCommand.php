<?php
/**
 * Specialization of a Command
 *
 * @package commands\user
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace commands\user;

/**
 * Specialization of a Command
 *
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
class PaymentConfirmationCommand extends \controllers\Command {
    
    /**
     * Specialization of the execute method of Command
     * 
     * @param \registry\Request $request
     */
    public function doExecuteDecorator(\registry\Request $request): void {
        /** Put your code here. */
        $responses['feedbackPaymentNotFound'] = 'Wij hebben uw betaling niet terug gevonden. Neem contact op met '._MAILREPLYTO;
        $responses['feedbackPaymentNotPaid'] = 'Je betaling werd niet uitgevoerd. In geval van problemen, contacteer '._MAILREPLYTO;
        
        $this->addResponses($request, $responses);
    }
    
    /**
     * Specialization of initCommand
     */
    #[\Override]
    public function initCommand(): void {
        $classname = '\\'.(new \ReflectionClass(get_called_class()))->getName();
        $classname = str_replace('commands', 'commands\viewrender', $classname);
        $this->setCommand(new $classname);        
    }
    
    /**
     * Specialization of getLevelOfLoginRequired
     */
    protected function getLevelOfLoginRequired(): void {
        if(_MINLEVELTOLOGIN === 'A') {               
            $this->setLoginLevel(new \sessions\NoLoginRequired());
        } else {
            $this->setLoginLevel(new \sessions\UserLogin());
        }
    }

}
