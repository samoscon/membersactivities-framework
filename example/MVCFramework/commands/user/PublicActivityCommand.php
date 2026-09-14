<?php
/**
 * Specialization of a Command
 *
 * @package commands\user
 * @version 1.0
 * @copyright (c) 2025, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace commands\user;

/**
 * Specialization of a Command
 *
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
class PublicActivityCommand extends \controllerframework\controllers\CommandDecorator {

    /**
     * Specialization of the execute method of Command
     * 
     * @param \controllerframework\registry\Request $request
     * @return ?int Status or null
     */
    #[\Override]
    public function doExecuteDecorator(\controllerframework\registry\Request $request): ?int {
        /** Put your code here.  */
        $id = filter_var($request->get('id'), FILTER_VALIDATE_INT);
        if(!$id) {
            $request->set('errorcode', 'wrongID');
            $request->addFeedback("Wrong ID");
            return self::CMD_ERROR;
        }
        
        try {
            $activity = \model\Activity::find($id);
        } catch (\Exception $exc) {
            $request->addFeedback($exc->getMessage());
            return self::CMD_ERROR;
        }
        
       $seatmap = $activity->activitytypeimplementation->seatmap();
        
        $this->addResponses($request, [
            'seatmap' => $seatmap]);
        return null;
    }
    
    /**
     * Specialization of initCommand
     */
    #[\Override]
    public function initCommand(): void {
//        $classname = '\\'.(new \ReflectionClass(get_called_class()))->getName();
//        $classname = str_replace('commands', 'membersactivities\commands', $classname);
//        $this->setCommand(new $classname);
        $this->setCommand(new \membersactivities\commands\user\PublicActivityCommand);
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
