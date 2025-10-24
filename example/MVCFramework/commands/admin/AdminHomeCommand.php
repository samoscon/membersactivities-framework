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
class AdminHomeCommand extends \controllers\CommandDecorator {
    
    /**
     * Specialization of the execute method of Command
     * 
     * @param \registry\Request $request
     * @return int
     */
    #[\Override]
    public function doExecuteDecorator(\registry\Request $request): void {
        /** Put your code here. Following line are meant as an example */
        $this->addResponses($request, [
            'title' => _MAILFROMNAME.' Admin',
            'labelActivitiesList' => 'Concerts', //Contains the title on top of the activitieslist
            'labelMembersMenu' => 'Bezoekers' //Contains the name of the search for members in the menu
            ]); 
    }

    /**
     * Specialization of initCommand
     */
    #[\Override]
    public function initCommand(): void {
        $classname = '\\'.(new \ReflectionClass(get_called_class()))->getName();
        $classname = str_replace('commands', 'commands\viewrender', $classname);
        $this->setCommand(new $classname);        
    }

    /**
     * Specialization of getLevelOfLoginRequired
     */
    #[\Override]
    protected function getLevelOfLoginRequired(): void {
        $this->setLoginLevel(new \sessions\AdminLogin());
    }
}