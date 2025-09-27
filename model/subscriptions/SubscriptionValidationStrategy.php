<?php
/**
 * SubscriptionValidationStrategy.php
 *
 * @package model\subscriptions
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace model\subscriptions;
/**
 * Superclass defining interface for concrete validation strategies specific to an application
 * Implements the design patterns 'Strategy' and 'Template Method'
 *
 * @link ../graphs/subscriptions%20Class%20Diagram.svg Subscriptions class diagram
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
abstract class SubscriptionValidationStrategy implements \audit\AuditableItem {
    use \audit\AuditableItemTrait;

    /**
     * Checks the validity of a subscription. If no error, it returns as well the item description and the total amount to be paid.
     * If there is an error, it returns the error description.
     * 
     * @param \members\Member $member
     * @param \activities\SubscribableItem $subscribableitem
     * @param int $quantity
     * @return array Format: 'errorcode' => code, 
     * if code = 0 then 'description' contains subscribed item description and we add the 'subscriptionamount' (float)
     * else the 'description' contains the error description.
     */
    public function checkSubscription(\model\members\Member $member, \model\activities\Costitem $subscribableitem): array {
        return $this->doCheckSubscription($member, $subscribableitem);
    }
 
    /**
     * Subscribe to a costitem of an activity. If the subscription is not valid, returns an errorcode
     * 
     * Implements design pattern 'Template Method'
     * 
     * @param \model\Member $member
     * @param model\activities\Costitem $subscribableitem
     * @param array $properties
     * @return array Format: 'errorcode', 'description'
     */
    public function subscribe(\model\members\Member $member, \model\activities\Costitem $subscribableitem, array $properties): array {
        $check = $this->docheckSubscription($member, $subscribableitem);
        if($check['errorcode']) {
            return $check;            
        }
        
        $subscription = \model\Subscription::insert($properties);
                
//        $this->notifyAuditTrace(__FUNCTION__, func_get_args());
        $q = $properties['quantity'];
        $this->notifyAuditTrace(__FUNCTION__, [
            $member->name .' '. $member->lastname, 
            " schrijft $subscription->quantity maal in voor ".
            $subscribableitem->description]);
        return $check;
    }
    
    /**
     * Abstract function for concrete implementations in the subsclasses
     * 
     * @param \members\Member $member Subscribing member
     * @param \activities\SubscribableItem $subscribableitem Subscribed Item (Costitem or Activity)
     * @return array Returns an array with potential error codes as 'errorcode' => 'description'. 
     *          If no errors, return array with error code = '0'
     */
    abstract public function doCheckSubscription(\model\members\Member $member, \model\activities\Costitem $subscribableitem): array;
    
    /**
     * Returns error (id and description)
     * 
     * @param int $errorcodeid
     * @param string $errorcodedescription
     * @return array
     */
    protected function errorcode($errorcodeid, $errorcodedescription = ''): array {
        return array('errorcode' => $errorcodeid, 'description' => $errorcodedescription);
    }   
}