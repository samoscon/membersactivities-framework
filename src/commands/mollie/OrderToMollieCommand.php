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
class OrderToMollieCommand extends \controllerframework\controllers\Command {
    
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
             * Generate a unique order id. It is important to include this unique attribute
             * in the redirectUrl (below) so a proper return page can be shown to the customer.
             */
            $orderid = filter_var($request->get('id'), FILTER_VALIDATE_INT);
            if (!$orderid) {
                $request->set('errorcode', 'wrongID');
                $request->addFeedback("Wrong ID");
                return self::CMD_ERROR;
            }
            $extendedorderid = $orderid * 171963;

            $accessToken = $request->get('access_token');

            if (!is_string($accessToken) ||
                !\controllerframework\security\AccessToken::validate(
                    'mollie-order',
                    (string) $orderid,
                    $accessToken
                )
            ) {
                $request->set('errorcode', 'InvalidAccessToken');
                return self::CMD_ERROR;
            }


            $paymentconfirmation = $request->get('paymentConfirmation');
            $paymentwebhook = "webhookFromMollie";

            /*
             * Determine the url parts to these files.
             */
            $protocol = isset($_SERVER['HTTPS']) && strcasecmp('off', $_SERVER['HTTPS']) !== 0 ? "https" : "http";
            $hostname = $_SERVER['HTTP_HOST'];
            $path     = dirname(isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $_SERVER['PHP_SELF']);

            /*
             * Payment parameters:
             *   amount        Amount in EUROs.
             *   description   Description of the payment.
             *   redirectUrl   Redirect location. The customer will be redirected there after the payment.
             *   webhookUrl    Webhook location, used to report when the payment changes state.
             *   metadata      Custom metadata that is stored with the payment.
             */
            $pmt = \model\Payment::find($orderid);

            if ($pmt === null) {
                $request->set('errorcode', 'wrongID');
                $request->addFeedback("Wrong ID");
                return self::CMD_ERROR;
            }

            $value = number_format(
                (float) $pmt->amount,
                2,
                '.',
                ''
            );
            
            $payment = $mollie->payments->create(array(
                    'amount'       => [
                                        'currency' => "EUR",
                                        'value' => $value,],
                    'description'  => $request->get('orderDescription'),
                    'redirectUrl'  => "{$protocol}://{$hostname}{$path}{$paymentconfirmation}?order_id={$extendedorderid}&access_token={$accessToken}",
                    'webhookUrl'   => "{$protocol}://{$hostname}{$path}{$paymentwebhook}",
                    'metadata'     => array('order_id' => $orderid),
            ));

            /*
             * Store the order with its payment status in a database.
             */
            $pmt->update(['status' => $payment->status]);

            /*
             * Send the customer off to complete the payment.
             */
            $request->set('results', $payment->getCheckoutUrl());
            return self::CMD_DEFAULT;
        } 
        catch (\Mollie\Api\Exceptions\ApiException $e) {
            \controllerframework\error\ErrorHandler::handleException($e);
            $request->addFeedback('Unable to initiate your payment. Please try again later.');
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
