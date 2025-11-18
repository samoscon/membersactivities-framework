<?php
/**
 * Specialization of a Command
 *
 * @package membersactivities\commands\admin
 * @version 1.0
 * @copyright (c) 2025, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace membersactivities\commands\admin;

/**
 * Specialization of a Command
 *
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
class AddMemberToCompositeCommand extends \controllerframework\controllers\Command {
    
    /**
     * Specialization of the execute method of Command
     * 
     * @param \controllerframework\registry\Request $request
     * @return int
     */
    #[\Override]
    public function doExecute(\controllerframework\registry\Request $request): int {
        /** Variables */
        $properties =array();
        
        $id = filter_var($request->get('id'), FILTER_VALIDATE_INT);
        if(!$id) {
            $request->set('errorcode', 'wrongID');
            $request->addFeedback("Wrong ID");
            return self::CMD_ERROR;
        }
        
        try {
            $member = \model\Member::find($id);
        } catch (\Exception $exc) {
            $request->addFeedback($exc->getMessage());
            return self::CMD_ERROR;
        }        

        $parent_id = filter_var($request->get('parent_id'), FILTER_VALIDATE_INT);
        if(!$parent_id) {
            $request->set('errorcode', 'wrongID');
            $request->addFeedback("Wrong ID");
            return self::CMD_ERROR;
        }
        
        try {
            $parentMember = \model\Member::find($parent_id);
        } catch (\Exception $exc) {
            $request->addFeedback($exc->getMessage());
            return self::CMD_ERROR;
        }        

        $properties['parent_id'] = $parent_id;

        $member->update($properties);

        $request->set('forwardqueryparams', ['id' => $id]);
        return self::CMD_OK;
    }

    /**
     * Specialization of getLevelOfLoginRequired
     */
    #[\Override]
    protected function getLevelOfLoginRequired(): void {
        $this->setLoginLevel(new \controllerframework\sessions\AdminLogin());
    }
}