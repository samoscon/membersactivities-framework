<?php
/**
 * Specialization of a Command
 *
 * @package commands\mollie
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace commands\mollie;

/**
 * Specialization of a Command
 *
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
class PaymentToMollieCommand extends \commands\datarequest\mollie\PaymentToMollieCommand {
    
    /**
     * Specialization of the execute method of Command
     * 
     * @param \registry\Request $request
     */
    #[\Override]
    public function doExecuteDecorator(\registry\Request $request): void {        
        $id = filter_var($request->get('id'), FILTER_VALIDATE_INT);
        if($id) {        
            try {
                $payment = \model\Payment::find($id);
            } catch (\Exception $exc) {
                echo $exc->getMessage();
            }
        }


        $request->set('paymentConfirmation', 'paymentConfirmation');
        $request->set('orderDescription', APP." orderid=".$id);
    }

    
    /**
     * Concrete specialization of setLoginLevel method in Command.
     * Needs to set 1 of the concrete subclasses of \sessions\Login
     */
    #[\Override]
    protected function getLevelOfLoginRequired(): void {
        $this->setLoginLevel(new \sessions\NoLoginRequired());
    }
}
