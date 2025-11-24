<?php
/**
 * SubscriptionValidationUser.php
 *
 * @package subscriptions
 * @version 1.0
 * @copyright (c) 2025, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace model;

/**
 * Implements design pattern 'Strategy'
 *
 * @link ../graphs/subscriptions%20Class%20Diagram.svg Subscriptions class diagram
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
class SubscriptionValidationUser extends \membersactivities\model\subscriptions\SubscriptionValidationStrategy {
    
    /**
     * Checks subscription when subscription is executed by a User
     * 
     * @param \controllerframework\members\Member $member
     * @param \membersactivities\model\activities\Costitem $subscribableitem
     * @return array Format: 'errorcode' => int and 'description' => string
     */
    public function doCheckSubscription(\controllerframework\members\Member $member, \membersactivities\model\activities\Costitem $subscribableitem): array {
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