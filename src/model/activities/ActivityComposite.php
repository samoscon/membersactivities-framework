<?php
/**
 * ActivityComposite.php
 *
 * @package membersactivities\model\activities
 * @version 1.0
 * @copyright (c) 2025, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace membersactivities\model\activities;

/**
 * Specific implementation of an Activity tree within client code
 *
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
class ActivityComposite extends Activity {
    /**
     * @var \db\Objectmap $childeren
     */
    protected $children;

    public function __construct(int $id) {
        parent::__construct($id);
        $this->children = new \controllerframework\db\ObjectMap();
    }

    /**
     * Creates on the basis of a database row the corresponding object in a subclass of Activity
     * 
     * Based on design pattern 'Abstract Factory'
     * 
     * @param array $row
     * @return Activity
     */
    #[\Override]
    public static function getInstance(array $row): Activity {
        $classname = '\\'.(new \ReflectionClass(get_called_class()))->getName();
        $activity = new $classname($row['id']);
        $activity->initProperties($row);
        if($row['parent_id']) {
            $activity->parent = $classname::find($row['parent_id']);
        }
        return $activity;        
    }

    /**
     * Returns an ObjectMap of Activities that belongs to this Composite
     * 
     * @return ObjectMap of Activities
     */
    public function getChildren(): \controllerframework\db\ObjectMap {
        if(!$this->children->valid()) {
            $this->setChildren();
        }
        return $this->children;
    }
    
    /**
     * Set the childeren variable with an ObjectMap of Activities
     * 
    */
    private function setChildren(): void {
        $this->children =  self::mapper()->getChildren($this);
    }

    /**
     * Returns the fact that this Composite is a composite.
     * 
     * @return boolean Always true
     */
    #[\Override]
    public function isComposite(): bool {
        return true;
    }
    
    /**
     * The Composite executes its primary logic in a particular way. It
     * traverses recursively through all its children, collecting and summing
     * their results. Since the composite's children pass these calls to their
     * children and so forth, the whole object tree is traversed as a result.
     * 
     * @return boolean If one of the childeren's subscription period is over, return true.
     */
    #[\Override]
    public function subscriptionPeriodOver(): bool {
        foreach ($this->children as $child) {
            if($child->subscriptionPeriodOver()) {
                return true;
            }            
        }
        return false;
    }
}
