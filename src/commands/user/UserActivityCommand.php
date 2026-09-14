<?php
/**
 * Specialization of a Command
 *
 * @package membersactivities\commands\user
 * @version 1.0
 * @copyright (c) 2025, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace membersactivities\commands\user;

/**
 * Specialization of a Command
 *
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
class UserActivityCommand extends \controllerframework\controllers\CommandDecorator {
    
    /**
     * Specialization of the execute method of Command
     * 
     * @param \controllerframework\registry\Request $request
     * @return ?int or null
     */
    #[\Override]
    public function doExecuteDecorator(\controllerframework\registry\Request $request): ?int {
        /** Put your code here. Following lines are meant as an example */
        $request->set(
            'validator',
            new \model\SubscriptionValidationUser()
        );
        return null;
    }

    /**
     * Specialization of initCommand
     */
    #[\Override]
    public function initCommand(): void {
        $this->setCommand(new \commands\user\ActivityCommand());        
    }

    /**
     * Specialization of getLevelOfLoginRequired
     */
    #[\Override]
    protected function getLevelOfLoginRequired(): void {
        $this->setLoginLevel(new \controllerframework\sessions\UserLogin());
    }
}