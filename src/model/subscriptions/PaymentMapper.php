<?php
/**
 * PaymentMapper.php
 *
 * @package membersactivities\model\subscriptions
 * @version 1.0
 * @copyright (c) 2025, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace membersactivities\model\subscriptions;

/**
 * Specialization of the Mapper class for Payments
 *
 * @link ../graphs/subscriptions%20Class%20Diagram.svg Subscriptions class diagram
 * @author Dirk Van Meirvenne <dirk.van.meirvenne at samosconsulting.be>
 */
abstract class PaymentMapper extends \controllerframework\db\Mapper {
    /**
     *
     * @var string Name of the associated table for Payment class 
     */
    private $tablename = 'payment';

    /**
     * Returns the table name
     * 
     * @return string table name
     */
    #[\Override]
    public function tablename(): string {
        return $this->tablename;
    }

    /**
     * Returns object instance of a Payment based on the corresponding database row
     * 
     * @param string $classname Name of the class
     * @param array $row Database row
     * @return \model\Payment
     */
    #[\Override]
    protected function doCreateObject(string $classname, array $row): \model\Payment  {
        return $classname::getInstance($row);
    }
}