<?php
/**
 * Specialization of a Command
 *
 * @package commands\datarequest\mollie
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace membersactivities\commands\mollie;

/**
 * Specialization of a Command
 *
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
abstract class PaymentToMollieCommand extends \controllerframework\controllers\CommandDecorator {
    
    /**
     * Specialization of initCommand
     */
    #[\Override]
    public function initCommand(): void {
        $this->setCommand(new \membersactivities\commands\mollie\OrderToMollieCommand());
    }

}
