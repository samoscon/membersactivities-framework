<?php
/**
 * Subscription.php
 *
 * @package membersactivities\model\subscriptions
 * @version 1.0
 * @copyright (c) 2025, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace membersactivities\model\subscriptions;

/**
 * Registers a subscription made by a member for (a costitem in) an activity.
 *
 * @link ../graphs/subscriptions%20Class%20Diagram.svg Subscriptions class diagram
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
abstract class Subscription extends \controllerframework\db\DomainObject {
    /**
     * @var PaymentTypeImplementation Relates the costitem to a certain costitem type with a specific implementation.
     * 
     * Based on design pattern 'Builder'
     */
    public ?SubscriptionTypeImplementation $subscriptiontypeimplementation;
    

    /**
     * Returns an object instance of Subscription on the basis of a database row
     * 
     * @param array $row
     * @return \model\Subscription
     */
    #[\Override]
    public static function getInstance(array $row): \model\Subscription {
        $classname = '\\'.(new \ReflectionClass(get_called_class()))->getName();
        $subscription = new $classname($row["id"]);
        $subscription->initProperties($row);
        $subscriptiontypeclassname = $classname.'_'.$row['classification'];
        $subscription->subscriptiontypeimplementation = new $subscriptiontypeclassname();
        if ($subscription->member_id) {
            $subscription->member = \model\Member::find($subscription->member_id);
        }
        if ($subscription->costitem_id) {
            $subscription->costitem = \model\Costitem::find($subscription->costitem_id);
        }
        if ($subscription->payment_id) {
            $subscription->payment = \model\Payment::find($subscription->payment_id);
        }
        return $subscription;
    }
}
