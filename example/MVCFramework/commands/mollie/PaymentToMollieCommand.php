<?php
/**
 * Specialization of a Command
 *
 * @package commands\mollie
 * @version 1.0
 * @copyright (c) 2025, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace commands\mollie;

/**
 * Specialization of a Command
 *
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
class PaymentToMollieCommand extends \membersactivities\commands\mollie\PaymentToMollieCommand {
    
    /**
     * Specialization of the execute method of Command
     * 
     * @param \controllerframework\registry\Request $request
     * @return ?int Status or null
     */
    #[\Override]
    public function doExecuteDecorator(\controllerframework\registry\Request $request): ?int {        
        $id = filter_var($request->get('id'), FILTER_VALIDATE_INT);

        $request->set('paymentConfirmation', 'paymentConfirmation');
        $request->set('orderDescription', APP." orderid=".$id);
        return null;
    }

    
    /**
     * Concrete specialization of setLoginLevel method in Command.
     * Needs to set 1 of the concrete subclasses of \sessions\Login
     */
    #[\Override]
    protected function getLevelOfLoginRequired(): void {
        $this->setLoginLevel(new \controllerframework\sessions\NoLoginRequired());
    }
}
