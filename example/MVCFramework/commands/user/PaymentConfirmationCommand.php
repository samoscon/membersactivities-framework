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
    public function doExecute(\registry\Request $request): int {
        $id = filter_var($request->get('order_id'), FILTER_VALIDATE_INT)/171963;

        try {
            $payment = \model\Payment::find($id); 
        } catch (\Exception $exc) {
            $request->addFeedback('Your payment could not be found in the database: '.$exc->getMessage());
            return self::CMD_ERROR;
        }
        
        if (!$payment->isPaid()) {
            $request->addFeedback('Your payment has not been executed. In case of problems, please contact '._MAILREPLYTO);
            return self::CMD_ERROR;
        }
        
        $activity = $payment->paymenttypeimplementation->getSubscription($payment)->costitem->activity;

        $responses['payment'] = $payment;
        $responses['activity'] = $activity;
        
        $this->addResponses($request, $responses);
        return self::CMD_DEFAULT;
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
