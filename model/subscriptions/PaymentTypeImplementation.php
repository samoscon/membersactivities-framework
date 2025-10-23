<?php
/**
 * PaymentTypeImplementation.php
 *
 * @package model\subscriptions
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace model\subscriptions;

/**
 * Different types of PaymentTypeImplementation (e.g. RGLR (standard payment of an order), YRLY (Yearly Fee), etc.)
 * 
 * Implementation of this class follows the design pattern 'Bridge'
 * 
 * @author Dirk Van Meirvenne <dirk.van.meirvenne at samosconsulting.be>
 */
abstract class PaymentTypeImplementation {
    
    protected function initiateGoogleWalletTickets(\model\Payment $payment, \model\activities\Activity $activity): void {
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
        
    public function getSubscription(\model\Payment $payment): \model\Subscription {
        foreach (\model\Subscription::findAll("WHERE payment_id = ".$payment->getId()) as $subscription) {
            return $subscription;
        }        
    }

    abstract public function statusReceived(\model\Payment $payment, string $status): void;
}
