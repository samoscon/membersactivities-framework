<?php
/**
 * Payment.php
 *
 * @package membersactivities/model/subscriptions
 * @version 1.0
 * @copyright (c) 2025, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace membersactivities\model\subscriptions;

/**
 * Registers payments made via third party app or directly on the account of the organisation
 * in order to pay for subscriptions to activities or yearly fees 
 *
 * @link ../graphs/subscriptions%20Class%20Diagram.svg Subscriptions class diagram
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
abstract class Payment extends \controllerframework\db\DomainObject {
    /**
     * @var PaymentTypeImplementation Relates the costitem to a certain costitem type with a specific implementation.
     * 
     * Based on design pattern 'Builder'
     */
    public ?PaymentTypeImplementation $paymenttypeimplementation;
    
    /**
     * Returns object instance of Payment
     * 
     * @param array $row
     * @return Payment
     */
    #[\Override]
    public static function getInstance(array $row): Payment {
        $classname = '\\'.(new \ReflectionClass(get_called_class()))->getName();
        $payment = new $classname($row['id']);
        $payment->initProperties($row);
        $paymenttypeclassname = $classname.'_'.$row['classification'];
        $payment->paymenttypeimplementation = new $paymenttypeclassname();
        if ($payment->member_id) {
            $payment->member = \model\Member::find($payment->member_id);
        }
        return $payment;
    }
    
    #[\Override]
    /**
     * Delete a Payment and all its associated subscriptions
     * 
     */
    public function delete(): void {
        //delete first all related subscriptions
        foreach (\model\Subscription::findAll('WHERE payment_id = '.$this->getId()) as $subscription) {
            $subscription->delete();
        }
        self::mapper()->delete($this);
    }

    /**
     * Returns true when Payment has status 'paid'
     * 
     * @return boolean
     */
    public function isPaid(): bool {
        return $this->status === 'paid';
    }
    
    /**
     * Forward the receipt of status to the Paymenttype Implementation (e.g. \model\Payment_RGLR)
     * 
     * @param string $status The status as received from the payment system or as provided by the User
     */
    public function statusReceived(string $status): void {
        $this->paymenttypeimplementation->statusReceived($this, $status);
    }
}
