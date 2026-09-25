<?php
/**
 * Specialization of a Command
 *
 * @package membersactivities\commands\admin
 * @version 1.0
 * @copyright (c) 2025, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace membersactivities\commands\admin;

/**
 * Specialization of a Command
 *
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
class EditPaymentCommand extends \controllerframework\controllers\Command {
    
    /**
     * Specialization of the execute method of Command
     * 
     * @param \controllerframework\registry\Request $request
     * @return int
     */
    #[\Override]
    public function doExecute(\controllerframework\registry\Request $request): int {
        /** Variables */
        $properties = array();
        
        $id = filter_var($request->get('id'), FILTER_VALIDATE_INT);
        if(!$id) {
            $request->set('errorcode', 'wrongID');
            $request->addFeedback("Wrong ID");
            return self::CMD_ERROR;
        }
        
        try {
            $payment = \model\Payment::find($id);
        } 
        catch (\Throwable $ex) {
            \controllerframework\error\ErrorHandler::handleException($ex);
            $request->addFeedback('Unable to retrieve the requested item.');
            return self::CMD_ERROR;
        }

        /** Check that the page was requested from itself via the POST method. */
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            /** Validate CSRF token before processing. */ 
            if (!$this->validateCsrfToken($request)) { 
                $request->set('errorcode', 'InvalidCsrfToken');
                return self::CMD_ERROR;
            }
            $status = filter_var($request->get('status'), FILTER_SANITIZE_FULL_SPECIAL_CHARS); //DO NOT update the status of the payment via the update but via the statusReceived !
            $properties['amount'] = $amount = filter_var($request->get('amount'), FILTER_VALIDATE_FLOAT);

            if ($status !== false && $amount !== false) {
                $payment->update($properties);
                
                $payment->statusReceived($status);
                $request->set('forwardqueryparams', ['id' => $payment->member_id]);
                return self::CMD_OK;
            }
        } 
            
        /** the page was requested via the GET method or the POST method did not return a status. */
        $responses['csrf_token'] = $this->getCsrfToken();
        $responses['payment'] = $payment;        
        $responses['returnpath'] = 'editMember?id='.$payment->member_id;
        
        $this->addResponses($request, $responses);
        return self::CMD_DEFAULT;
    }

    /**
     * Specialization of getLevelOfLoginRequired
     */
    protected function getLevelOfLoginRequired(): void {
        $this->setLoginLevel(new \controllerframework\sessions\AdminLogin());
    }

}