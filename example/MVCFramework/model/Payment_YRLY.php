<?php
/**
 * Payment_YRLY.php
 *
 * @package model
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace model;

/**
 * Represents a normal Payment (of an order or a prepaid amount).
 * 
 * Implementation follows the design pattern 'Builder'
 *
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
class Payment_YRLY extends \membersactivities\model\subscriptions\PaymentTypeImplementation {
    #[\Override]
    public function statusReceived(\model\Payment $payment, string $status): void {
        if ($status === 'paid') {
            \model\Member::find($payment->member_id)->extendMembership();
        }        
    }
}