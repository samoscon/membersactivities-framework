<?php
/**
 * Activity.php
 *
 * @package model\activities
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace model\activities;

/**
 * An Activity of the organisation. 
 * 
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
abstract class Activity extends \db\DomainObject {
    /**
     * @var ActivityTypeImplementation Relates the activity to a certain activity type with a specific implementation.
     * 
     * Based on design pattern 'Builder'
     */
    public ?\model\activities\ActivityTypeImplementation $activitytypeimplementation;
    
    public function __construct(int $id) {
        parent::__construct($id);
    }

    /**
     * Creates on the basis of a database row the corresponding object in a subclass of Activity
     * 
     * Based on design pattern 'Abstract Factory'
     * 
     * @param array $row
     * @return \model\activities\Activity
     */
    #[\Override]
    public static function getInstance(array $row): \model\activities\Activity {
        $classname = '\\'.(new \ReflectionClass(get_called_class()))->getName();
        $children = self::mapper()->checkForChildren($row['id']);
        if($children) {
           return \model\activities\ActivityComposite::getInstance($row);
        }
        $activity = new $classname($row['id']);
        $activity->initProperties($row);
        $activitytypeclassname = $classname.'_'.$row['classification'];
        $activity->activitytypeimplementation = new $activitytypeclassname();
        if($row['parent_id']) {
            $activity->parent = $classname::find($row['parent_id']);
        }
        return $activity;        
    }
    
    /**
     * Returns the subscribed Members to this Activity
     * 
     * @return ObjectMap of \members\Member
     */
    public function getParticipants(): \db\ObjectMap {
        if ($this->parent) {
            $results = $this->parent->getParticipants();
        } else {
            $results = new \db\ObjectMap();
        }
        return self::mapper()->getParticipants($this, $results);
    }
    
    /**
     * Returns true if the current date >= duedate of the Activity
     * 
     * @return boolean
     */
    public function subscriptionPeriodOver(): bool {
        $duedate = date('Y-m-d', strtotime(str_replace("/", "-", $this->duedate)));
        return $duedate < date('Y-m-d');
    }
    
    /**
     * Returns the effective paid amount for this Activity or ActivityComposite
     * 
     * @return float Returns an array with a subject and a body
     */
    public function getTotalAmountReceived(): float {
        $total = 0;
        $paymentIdsAdded = array();
        foreach ($this->costitems as $costitem) {
            foreach ($costitem->subscriptions as $subscription) {
                if(array_search($subscription->payment->getId(), $paymentIdsAdded) === false) {
                    if ($subscription->payment->status === 'paid') {
                        $total += $subscription->payment->amount;
                    }
                    $paymentIdsAdded[] = $subscription->payment->getId();
                }
            }
        }
        return $total;
    }
}
