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
class CreatePaymentCommand extends \controllers\Command {
   
    /**
     * Specialization of the execute method of Command
     * 
     * @param \registry\Request $request
     * @return int
     */
    #[\Override]
    public function doExecute(\registry\Request $request): int {
        /** variables */
        $orderid = $request->get('id')/171963;
        if($orderid) {
            try {
                $payment = \model\Payment::find($orderid);

            } catch (Exception $exc) {
                return self::CMD_ERROR;
            }
        } else {
            $request->addFeedback('No order id provided');
            return self::CMD_ERROR;
        }
        
        $choice = '';
        
        /** Check that the page was requested from itself via the POST method. */
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $choice = $request->get('paymentmethod');
            
            if ($choice == "online") {   
            	$request->set('forwardqueryparams', array('id' => $orderid, 'amount' => $payment->amount));            
                return self::CMD_OK;
            } else {
                return self::CMD_CONTINUE;            
            }
        }
        
        /** the page was requested via the GET method or the POST method did not return a status. */
        $this->addResponses($request, [
            'payment' => $payment
        ]);
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
