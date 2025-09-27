<?php
/**
 * CostItem.php
 *
 * @package model\activities
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace model\activities;

/**
 * At least 1 cost item per Activity has to be defined
 * 
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
abstract class Costitem extends \db\DomainObject {     
    /**
     * @var CostitemTypeImplementation Relates the costitem to a certain costitem type with a specific implementation.
     * 
     * Based on design pattern 'Builder'
     */
    public ?\model\activities\CostitemTypeImplementation $costitemtypeimplementation;
    
    /**
     * Returns a CostItem object on the basis of a DB row.
     * 
     * @param arrary $row
     * @return \model\activities\Costitem
     * @throws \Exception
     */
    #[\Override]
    public static function getInstance(array $row): \model\activities\Costitem {
        $classname = '\\'.(new \ReflectionClass(get_called_class()))->getName();
        $costitem = new $classname($row['id']);
        $costitem->initProperties($row);
        $costitemtypeclassname = $classname.'_'.$row['classification'];
        $costitem->costitemtypeimplementation = new $costitemtypeclassname();
        if($row['activity_id']) {
            try {
                $activity = \model\Activity::find($row['activity_id']);            
            } catch (\Exception $exc) {
                throw new \Exception("De activiteit voor Costitem met id " . $row['id']. " bestaat niet.");
            }
            $costitem->activity = $activity;
        }
        return $costitem;
    }
}