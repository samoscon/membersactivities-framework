<?php
/**
 * PaymentMapper.php
 *
 * @package model\subscriptions
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace model\subscriptions;

/**
 * Specialization of the Mapper class for Payments
 *
 * @link ../graphs/subscriptions%20Class%20Diagram.svg Subscriptions class diagram
 * @author Dirk Van Meirvenne <dirk.van.meirvenne at samosconsulting.be>
 */
abstract class PaymentMapper extends \db\Mapper {
    /**
     *
     * @var string Name of the associated table for Payment class 
     */
    private $tablename = 'payment';

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
     * Returns object instance of a Payment based on the corresponding database row
     * 
     * @param array $row
     * @return \model\Payment
     */
    #[\Override]
    protected function doCreateObject(string $classname, array $row): \model\Payment  {
        return $classname::getInstance($row);
    }
}