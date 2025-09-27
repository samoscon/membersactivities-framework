<?php
/**
 * SubscriptionMapper.php
 *
 * @package model\subscriptions
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace model\subscriptions;

/**
 * Specialization of the Mapper class for Subscriptions
 *
 * @link ../graphs/subscriptions%20Class%20Diagram.svg Subscriptions class diagram
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
abstract class SubscriptionMapper extends \db\Mapper {
    
    /**
     *
     * @var string Name of the associated table for Subscription class 
     */
    private string $tablename = 'subscription';
    
    /**
     * Returns $tablename
     * 
     * @return string Tablename
     */
    #[\Override]
    public function tablename(): string {
        return $this->tablename;
    }
    
    /**
     * Returns object instance of Subscription in the client code
     * 
     * @param array $row
     * @return \model\Subscription
     */
    #[\Override]
    protected function doCreateObject(string $classname, array $row): \model\Subscription {
        return $classname::getInstance($row);
    }   
}        