<?php
/**
 * ActivityComposite.php
 *
 * @package model
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace model\activities;

/**
 * Specific implementation of an Activity tree within client code
 * App
 *
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
class ActivityComposite extends \model\activities\Activity {
    /**
     * @var \db\Objectmap $childeren
     */
    protected $children;

    public function __construct(int $id) {
        parent::__construct($id);
        $this->children = new \db\ObjectMap();
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
        $activity = new $classname($row['id']);
        $activity->initProperties($row);
        if($row['parent_id']) {
            $activity->parent = $classname::find($row['parent_id']);
        }
        return $activity;        
    }

    public function getChildren(): \db\ObjectMap {
        if(!$this->children->valid()) {
            $this->setChildren();
        }
        return $this->children;
    }
    
    private function setChildren(): void {
        $this->children =  self::mapper()->getChildren($this);
    }

    #[\Override]
    public function isComposite(): bool {
        return true;
    }
    
    /**
     * The Composite executes its primary logic in a particular way. It
     * traverses recursively through all its children, collecting and summing
     * their results. Since the composite's children pass these calls to their
     * children and so forth, the whole object tree is traversed as a result.
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
