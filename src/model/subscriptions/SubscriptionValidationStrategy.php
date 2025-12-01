<?php
/**
 * SubscriptionValidationStrategy.php
 *
 * @package membersactivities\model\subscriptions
 * @version 1.0
 * @copyright (c) 2025, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace membersactivities\model\subscriptions;
/**
 * Superclass defining interface for concrete validation strategies specific to an application
 * Implements the design patterns 'Strategy' and 'Template Method'
 *
 * @link ../graphs/subscriptions%20Class%20Diagram.svg Subscriptions class diagram
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
abstract class SubscriptionValidationStrategy implements \controllerframework\audit\AuditableItem {
    use \controllerframework\audit\AuditableItemTrait;

    /**
     * Checks the validity of a subscription. If no error, it returns as well the item description and the total amount to be paid.
     * If there is an error, it returns the error description.
     * 
     * @param \controllerframework\members\Member $member
     * @param \membersactivities\model\activities\SubscribableItem $subscribableitem
     * @return array Format: 'errorcode' => code, 
     * if code = 0 then 'description' contains subscribed item description and we add the 'subscriptionamount' (float)
     * else the 'description' contains the error description.
     */
//    public function checkSubscription(\controllerframework\members\Member $member, \membersactivities\model\activities\Costitem $subscribableitem): array {
//        return $this->doCheckSubscription($member, $subscribableitem);
//    }
 
    /**
     * Subscribe to a costitem of an activity. If the subscription is not valid, returns an errorcode
     * 
     * Implements design pattern 'Template Method'
     * 
     * @param \controllerframework\model\Member $member
     * @param \membersactivities\model\activities\Costitem $subscribableitem
     * @param array $properties
     * @return array Format: 'errorcode', 'description'
     */
    public function subscribe(\controllerframework\members\Member $member, \membersactivities\model\activities\Costitem $subscribableitem, array $properties): array {
        $check = $this->docheckSubscription($member, $subscribableitem);
        if($check['errorcode']) {
            return $check;            
        }
        
        $subscription = \model\Subscription::insert($properties);
                
//        $this->notifyAuditTrace(__FUNCTION__, func_get_args());
//        $q = $properties['quantity'];
//        $this->notifyAuditTrace(__FUNCTION__, [
//            $member->name .' '. $member->lastname, 
//            " subscribes $subscription->quantity times for ".
//            $subscribableitem->description]);
        return $check;
    }
    
    /**
     * Abstract function for concrete implementations in the subsclasses
     * 
     * @param \controllerframework\members\Member $member
     * @param \membersactivities\model\activities\SubscribableItem $subscribableitem
     * @return array Returns an array with potential error codes as 'errorcode' => 'description'. 
     *          If no errors, return array with error code = '0'
     */
    abstract protected function doCheckSubscription(\controllerframework\members\Member $member, \membersactivities\model\activities\Costitem $subscribableitem): array;
    
    /**
     * Returns error (id and description)
     * 
     * @param int $errorcodeid
     * @param string $errorcodedescription
     * @return array
     */
    protected function errorcode(int $errorcodeid, string $errorcodedescription = ''): array {
        return array('errorcode' => $errorcodeid, 'description' => $errorcodedescription);
    }   
}