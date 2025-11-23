<?php
/**
 * CostItem.php
 *
 * @package membersactivities\model\activities
 * @version 1.0
 * @copyright (c) 2025, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace membersactivities\model\activities;

/**
 * At least 1 cost item per Activity has to be defined
 * 
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
abstract class Costitem extends \controllerframework\db\DomainObject {     
    /**
     * @var CostitemTypeImplementation Relates the costitem to a certain costitem type with a specific implementation.
     * 
     * Based on design pattern 'Builder'
     */
    public ?CostitemTypeImplementation $costitemtypeimplementation;
    
    /**
     * Returns a CostItem object on the basis of a DB row.
     * 
     * @param arrary $row
     * @return Costitem
     * @throws \Exception
     */
    #[\Override]
    public static function getInstance(array $row): Costitem {
        $classname = '\\'.(new \ReflectionClass(get_called_class()))->getName();
        $costitem = new $classname($row['id']);
        $costitem->initProperties($row);
        $costitemtypeclassname = $classname.'_'.$row['classification'];
        $costitem->costitemtypeimplementation = new $costitemtypeclassname();
        if($row['activity_id']) {
            try {
                $activity = \model\Activity::find($row['activity_id']);            
            } catch (\Exception $exc) {
                throw new \Exception("The activity for this ostitem with id " . $row['id']. " does not exist.");
            }
            $costitem->activity = $activity;
        }
        return $costitem;
    }
}