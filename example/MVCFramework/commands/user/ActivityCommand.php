<?php
/**
 * Specialization of a Command
 *
 * @package commands\user
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace commands\user;

/**
 * Specialization of a Command
 *
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
class ActivityCommand extends \controllerframework\controllers\CommandDecorator {

    #[\Override]
    public function doExecuteDecorator(\controllerframework\registry\Request $request): void {
        /** Put your code here. */
        $this->addResponses($request, [
            'validator' => '\model\SubscriptionValidationUser']);
        
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

    #[\Override]
    protected function getLevelOfLoginRequired(): void {
        if(_MINLEVELTOLOGIN === 'A') {               
            $this->setLoginLevel(new \controllerframework\sessions\NoLoginRequired());
        } else {
            $this->setLoginLevel(new \controllerframework\sessions\UserLogin());
        }
    }
}
