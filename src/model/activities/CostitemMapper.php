<?php
/**
 * CostitemMapper.php
 *
 * @package membersactivities\model\activities
 * @version 1.0
 * @copyright (c) 2025, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace membersactivities\model\activities;

/**
 * Instantiation of the DB Mapper for Costitem class
 * 
 * Might be further subclassed for application specific behavior
 *
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
abstract class CostitemMapper extends \controllerframework\db\Mapper  {
    /**
     * @var string Contains the name of the related table to the Costitem in the database
     */
    private string $tablename = 'costitem';
    
    /**
     * Returns the associated tablename for the Costitem class; i.e. costitem
     * 
     * @return string
     */
    #[\Override]
    public function tablename(): string {
        return $this->tablename;
    }
    
    /**
     * Returns on the basis of a database row the associated object
     * 
     * @param string $classname Name of the class
     * @param array $row Database row
     * @return \model\Costitem Returns the object found on the basis of database row
     */
    #[\Override]
    protected function doCreateObject(string $classname, array $row): \model\Costitem {
        return $classname::getInstance($row);
    }
}
