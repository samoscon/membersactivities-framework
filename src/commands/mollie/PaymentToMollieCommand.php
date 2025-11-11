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
     * Specialization of the execute method of Command
     * 
     * @param \registry\Request $request
     */
    #[\Override]
    abstract public function doExecuteDecorator(\controllerframework\registry\Request $request): void;

    /**
     * Specialization of initCommand
     */
    #[\Override]
    public function initCommand(): void {
        $this->setCommand(new \membersactivities\commands\mollie\OrderToMollieCommand());
    }

}
