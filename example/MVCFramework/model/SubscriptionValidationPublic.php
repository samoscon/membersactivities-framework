<?php
/**
 * SubscriptionValidationPublic.php
 *
 * @package model
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
class SubscriptionValidationPublic extends \membersactivities\model\subscriptions\SubscriptionValidationStrategy {
    
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
        return $this->errorcode(0);
    }
    
}