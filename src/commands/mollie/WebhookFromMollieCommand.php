<?php
/**
 * Specialization of a Command
 *
 * @package membersactivities\commands\mollie
 * @version 1.0
 * @copyright (c) 2025, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace membersactivities\commands\mollie;

/**
 * Specialization of a Command
 *
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
class WebhookFromMollieCommand extends \controllerframework\controllers\Command {
    
    /**
     * Specialization of the execute method of Command
     * 
     * @param \controllerframework\registry\Request $request
     * @return int Returns a state as defined in the constants of Command
     */
    #[\Override]
    public function doExecute(\controllerframework\registry\Request $request): int {
        try
        {
            require_once("vendor/autoload.php");

            /*
             * Initialize the Mollie API library with your API key.
             *
             * See: https://www.mollie.com/beheer/account/profielen/
             */
            $mollie = new \Mollie\Api\MollieApiClient();
            $mollie->setApiKey(_MOLLIECONFIG);
            
            /*
             * Retrieve the payment's current state.
             */
            $payment  = $mollie->payments->get($request->get('id'));
            $order_id = $payment->metadata->order_id;

            /*
             * Update the order in the database.
             */
            $pmt = \model\Payment::find($order_id);
            $pmt->update(array('status' => $payment->status));
            $pmt->paymenttypeimplementation->statusReceived($pmt, $payment->status);
            return self::CMD_DEFAULT;
        }
        catch (\Mollie\Api\Exceptions\ApiException $e)
        {
            $request->addFeedback("API call to mollie failed: " . htmlspecialchars($e->getMessage()));
            return self::CMD_ERROR;
        }
    }

    /**
     * Specialization of getLevelOfLoginRequired
     */
    #[\Override]
    protected function getLevelOfLoginRequired(): void {
        $this->setLoginLevel(new \controllerframework\sessions\NoLoginRequired());
    }

}
