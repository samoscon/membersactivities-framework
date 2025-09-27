<?php
/**
 * PaymentTypeImplementation.php
 *
 * @package model\subscriptions
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace model\subscriptions;

/**
 * Different types of PaymentTypeImplementation (e.g. RGLR (standard payment of an order), YRLY (Yearly Fee), etc.)
 * 
 * Implementation of this class follows the design pattern 'Bridge'
 * 
 * @author Dirk Van Meirvenne <dirk.van.meirvenne at samosconsulting.be>
 */
abstract class PaymentTypeImplementation {
    
    abstract public function statusReceived(\model\Payment $payment, string $status): void;
}
