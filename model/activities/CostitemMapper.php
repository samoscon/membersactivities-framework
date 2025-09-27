<?php
/**
 * CostitemMapper.php
 *
 * @package model\activities
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace model\activities;

/**
 * Instantiation of the DB Mapper for Costitem class
 * 
 * Might be further subclassed for application specific behavior
 *
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
abstract class CostitemMapper extends \db\Mapper  {
    /**
     * @var string Contains the name of the related table to the Costitem class(es) in the database
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
     * @param Array $row
     * @return \model\activities\Costitem
     */
    #[\Override]
    protected function doCreateObject(string $classname, array $row): \model\activities\Costitem {
        return $classname::getInstance($row);
    }
}
