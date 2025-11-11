<?php
/**
 * Specialization of a Command
 *
 * @package commands\admin
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace commands\admin;

/**
 * Specialization of a Command
 *
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
class EditPaymentCommand extends \controllerframework\controllers\CommandDecorator {
    
    /**
     * Specialization of the execute method of Command
     * 
     * @param \registry\Request $request
     */
    #[\Override]
    public function doExecuteDecorator(\controllerframework\registry\Request $request): void {
        /** Put your code here.  */
        
    }

    /**
     * Specialization of initCommand
     */
    #[\Override]
    public function initCommand(): void {
        $classname = '\\'.(new \ReflectionClass(get_called_class()))->getName();
        $classname = str_replace('commands', 'membersactivities\commands', $classname);
        $this->setCommand(new $classname);        
    }

    /**
     * Specialization of getLevelOfLoginRequired
     */
    #[\Override]
    protected function getLevelOfLoginRequired(): void {
        $this->setLoginLevel(new \controllerframework\sessions\AdminLogin());
    }

}