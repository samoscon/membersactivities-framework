<?php
/**
 * DefaultCommand.php
 *
 * @package commands
 * @version 1.0
 * @copyright (c) 2025, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace commands;

/**
 * Subclass of Command. Must be at minimum present in the MVC framework. 
 * Will lead the visitor of your website to the first page (home screen or welcome screen or login screen, etc.)
 *
 * @link ../graphs/controllers%20(Application%20Controller)%20Class%20Diagram.svg Controllers class diagram
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
class DefaultCommand extends \controllerframework\controllers\CommandDecorator {
    
    /**
     * Concrete specialization of execute method in Command
     * 
     * @param \controllerframework\registry\Request $request
     * @return int Returns status of the executed command or null
     */
    #[\Override]
    public function doExecuteDecorator(\controllerframework\registry\Request $request): ?int {
        /** Put your code here. Following line are meant as an example */
        $this->addResponses($request, [
            'title' => 'Log in']);
        return null;
    }
    
    /**
     * Specialization of initCommand
     */
    public function initCommand(): void {
        $this->setCommand(new \membersactivities\commands\DefaultCommand());        
    }

    /**
     * Concrete specialization of setLoginLevel method in Command.
     * Needs to set 1 of the concrete subclasses of \sessions\Login
     */
    #[\Override]
    protected function getLevelOfLoginRequired(): void {
        $this->setLoginLevel(new \controllerframework\sessions\NoLoginRequired());
    }
}
