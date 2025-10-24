<?php
/**
 * Specialization of a Command
 *
 * @package commands\admin
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace commands\viewrender\admin;

/**
 * Specialization of a Command
 *
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
class EditPaymentCommand extends \controllers\Command {
    
    /**
     * Specialization of the execute method of Command
     * 
     * @param \registry\Request $request
     * @return int
     */
    #[\Override]
    public function doExecute(\registry\Request $request): int {
        /** Variables */
        $properties = array();
        
        $id = filter_var($request->get('id'), FILTER_VALIDATE_INT);
        if(!$id) {
            $request->addFeedback("Geen correct id opgegeven.");
            return self::CMD_ERROR;
        }
        
        try {
            $payment = \model\Payment::find($id);
        } catch (\Exception $exc) {
            $request->addFeedback($exc->getMessage());
            return self::CMD_ERROR;
        }

        /** Check that the page was requested from itself via the POST method. */
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $properties['status'] = $status = filter_var($request->get('status'), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $properties['amount'] = $amount = filter_var($request->get('amount'), FILTER_VALIDATE_FLOAT);

            if (!false) {
                $payment->update($properties);
                
                $payment->paymenttypeimplementation->statusReceived($payment, $status);
                $request->set('forwardqueryparams', ['id' => $payment->member_id]);
                return self::CMD_OK;
            }
        } 
            
        /** the page was requested via the GET method or the POST method did not return a status. */
        $responses['payment'] = $payment;        
        $responses['returnpath'] = 'editMember?id='.$payment->member_id;
        
        $this->addResponses($request, $responses);
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