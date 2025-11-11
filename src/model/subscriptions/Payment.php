<?php
/**
 * Payment.php
 *
 * @package model/subscriptions
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace membersactivities\model\subscriptions;

/**
 * Registers payments made via third party app or directly on the account of the organisation
 * in order to pay for subscriptions or yearly fees to activities
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
     * @return \model\Payment
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
     * Update an object in the database through the respective mapper of the calling class
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
    
    public function statusReceived(string $status): void {
        $this->paymenttypeimplementation->statusReceived($this, $status);
    }
}
