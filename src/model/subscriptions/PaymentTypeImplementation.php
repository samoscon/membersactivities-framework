<?php
/**
 * PaymentTypeImplementation.php
 *
 * @package membersactivities\model\subscriptions
 * @version 1.0
 * @copyright (c) 2025, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace membersactivities\model\subscriptions;

/**
 * Different types of PaymentTypeImplementation (e.g. RGLR (standard payment of an order), YRLY (Yearly Fee), etc.)
 * The default type is RGLR. The type is set on the basis of the value in the column classification.
 * 
 * Implementation of this class follows the design pattern 'Bridge'
 * 
 * @author Dirk Van Meirvenne <dirk.van.meirvenne at samosconsulting.be>
 */
abstract class PaymentTypeImplementation {
    
    /**
     * Creates all the tickets in the Google Wallet API related to this payment
     * 
     * @param \model\Payment $payment The payment 
     * @param \membersactivities\model\activities\Activity $activity The associated class for the Google Wallet API
     */
    protected function initiateGoogleWalletTickets(\model\Payment $payment, \membersactivities\model\activities\Activity $activity): void {
        if($activity->isComposite()) {
            return;
        }
        foreach (\model\Subscription::findAll("WHERE payment_id = ".$payment->getId()) as $subscription) {
            for($i = 0; $i < $subscription->quantity; $i++) {
                (new \model\GoogleWalletTicket())->createObject($subscription->getId(), $activity->getId(), $i+1);
                (new \model\GoogleWalletTicket())->updateObject($subscription, $i+1);
            }
        }        
    }
        
    /**
     * Return the first subscription that is linked to this payment. Often used to find the underlying activity related to this payment
     * 
     * @param \model\Payment $payment The payment that received the new status
     * @return \model\Subscription The first subscription linked to this payment that was found in the database
     */
    public function getSubscription(\model\Payment $payment): \model\Subscription {
        foreach (\model\Subscription::findAll("WHERE payment_id = ".$payment->getId()) as $subscription) {
            return $subscription;
        }        
    }

    /**
     * Handle the change of the status in your subclass Paymenttype implementation
     * 
     * @param \model\Payment $payment The payment that received the new status
     * @param string $status The status as received from the payment system or as provided by the User
     */
    abstract public function statusReceived(\model\Payment $payment, string $status): void;
}
