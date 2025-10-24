<?php
/**
 * Specialization of a Command
 *
 * @package commands\admin
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace commands\viewrender\admin;

/**
 * Specialization of a Command
 *
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
class SearchMembersCommand extends \controllers\Command {
    //put your code here
    #[\Override]
    public function doExecute(\registry\Request $request): int {
        $this->addResponses($request, ['members' => \model\Member::findAll('ORDER BY name')]);
         
         return self::CMD_DEFAULT;
    }
    
    #[\Override]
    protected function getLevelOfLoginRequired(): void {
        $this->setLoginLevel(new \sessions\AdminLogin());
    }
}
