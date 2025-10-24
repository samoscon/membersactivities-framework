<?php
/**
 * Specialization of a Command
 *
 * @package commands\user
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace commands\viewrender\user;

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
     * @return int
     */
    public function doExecute(\registry\Request $request): int {
        
        $id = filter_var($request->get('order_id'), FILTER_VALIDATE_INT)/171963;
        if($id) {
            try {
                $payment = \model\Payment::find($id); 
            } catch (Exception $exc) {
                return self::CMD_ERROR;
            }
            $subscription = $payment->paymenttypeimplementation->getSubscription($payment);
            $text =  '<p>We have received your payment. A mail with your tickets has also been sent to your mailaddress <b>'
                    .$payment->member->email . '</b>.<br><br> Your order:<br>'
                    .$payment->paymenttypeimplementation->getOrderedTicketsText($payment);
            $download = $payment->paymenttypeimplementation->getDownloadText($payment, $subscription->costitem->activity).'</p>';
            //$payment->paymenttypeimplementation->statusReceived($payment, $payment->status); //JUST FOR TESTING      
            $paymentstatus = $payment->isPaid() ?                
                $text.$download :
                'Your payment has not been executed. In case of problems, please contact '._MAILREPLYTO;
        } else {
            $paymentstatus =
                'Your payment has not been executed. In case of problems, please contact '._MAILREPLYTO;
        }

        $request->addFeedback($paymentstatus);
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