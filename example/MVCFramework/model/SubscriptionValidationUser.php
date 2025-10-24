<?php
/**
 * SubscriptionValidationUser.php
 *
 * @package subscriptions
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace model;

/**
 * Implements design pattern 'Strategy'
 *
 * @link ../graphs/subscriptions%20Class%20Diagram.svg Subscriptions class diagram
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
class SubscriptionValidationUser extends \model\subscriptions\SubscriptionValidationStrategy {
    
    /**
     * Checks subscription for MMC when subscription is executed by a User
     * 
     * @param \members\Member $member
     * @param \model\activities\Costitem $subscribableitem
     * @return array Format: 'errorcode' => int and 'description' => string
     */
    public function doCheckSubscription(\model\members\Member $member, \model\activities\Costitem $subscribableitem): array {
         if($subscribableitem->activity->subscriptionPeriodOver()) {
            return $this->errorcode(100, 'Inschrijving of annuleren is jammer genoeg niet langer mogelijk.');
        }
        if(!$member->active) {
            return $this->errorcode(200, 'Je lidmaatschap is gedeactiveerd. Gelieve contact op te nemen met het bestuur.');
        }
        if($member->subscriptionuntil < $subscribableitem->activity->date) {
            return $this->errorcode(201, 'Gelieve eerst je lidgeld voor volgend jaar te betalen');
        }
        return $this->errorcode(0);
    }
    
}