<?php
/**
 * SubscriptionValidationAdmin.php
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
class SubscriptionValidationAdmin extends SubscriptionValidationUser {
    
    /**
     * Checks subscription for MMC when subscription is executed by an admin User
     * 
     * @param \model\Member $member
     * @param \model\activities\Costitem $subscribableitem
     * @return array Format: 'errorcode' => int and 'description' => string
     */
    public function doCheckSubscription(\controllerframework\members\Member $member, \membersactivities\model\activities\Costitem $subscribableitem): array {
        if(!$member->active) {
            return $this->errorcode(200, 'Lidmaatschap is gedeactiveerd. Inschrijving kan niet plaats vinden.');
        }
        return $this->errorcode(0);
    }
}