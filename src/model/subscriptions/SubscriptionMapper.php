<?php
/**
 * SubscriptionMapper.php
 *
 * @package membersactivities\model\subscriptions
 * @version 1.0
 * @copyright (c) 2025, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace membersactivities\model\subscriptions;

/**
 * Specialization of the Mapper class for Subscriptions
 *
 * @link ../graphs/subscriptions%20Class%20Diagram.svg Subscriptions class diagram
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
abstract class SubscriptionMapper extends \controllerframework\db\Mapper {
    
    /**
     *
     * @var string Name of the associated table for Subscription class 
     */
    private string $tablename = 'subscription';
    
    /**
     * Returns table name
     * 
     * @return string table name
     */
    #[\Override]
    public function tablename(): string {
        return $this->tablename;
    }
    
    /**
     * Returns object instance of Subscription in the client code
     * 
     * @param string $classname Name of the class
     * @param array $row Database row
     * @return \model\Subscription
     */
    #[\Override]
    protected function doCreateObject(string $classname, array $row): \model\Subscription {
        return $classname::getInstance($row);
    }   
}        