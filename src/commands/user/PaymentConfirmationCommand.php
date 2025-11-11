<?php
/**
 * Specialization of a Command
 *
 * @package commands\user
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace membersactivities\commands\user;

/**
 * Specialization of a Command
 *
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
class PaymentConfirmationCommand extends \controllerframework\controllers\Command {
    
    /**
     * Specialization of the execute method of Command
     * 
     * @param \registry\Request $request
     * @return int
     */
    #[\Override]
    public function doExecute(\controllerframework\registry\Request $request): int {        
        $id = filter_var($request->get('order_id'), FILTER_VALIDATE_INT)/171963;

        try {
            $payment = \model\Payment::find($id); 
        } catch (\Exception $exc) {
            $request->addFeedback($request->get('feedbackPaymentNotFound'));
            return self::CMD_ERROR;
        }
        
        if (!$payment->isPaid()) {
            $request->addFeedback($request->get('feedbackPaymentNotPaid'));
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
    #[\Override]
    protected function getLevelOfLoginRequired(): void {
        $this->setLoginLevel(new \controllerframework\sessions\NoLoginRequired());
    }

}