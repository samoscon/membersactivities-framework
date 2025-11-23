<?php
/**
 * Specialization of a Command
 *
 * @package membersactivities\commands\downloads
 * @version 1.0
 * @copyright (c) 2025, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace membersactivities\commands\downloads;

/**
 * Specialization of a Command
 *
 * @author Dirk Van Meirvenne <dirk.van.meirvenne at samosconsulting.be>
 */
class DownloadXlsMembersCommand extends \controllerframework\controllers\Command {
    
    /**
     * Specialization of the execute method of Command
     * 
     * @param \controllerframework\registry\Request $request
     * @return int
     */
    #[\Override]
    public function doExecute(\controllerframework\registry\Request $request): int {
        $members = \model\Member::findAll("ORDER BY name");
        $result = array();
        foreach ($members as $member) {
            $p = array();
            $p[] = $member->name;
            $p[] = $member->lastname <> 'lastname' ? $member->lastname : '';
            $p[] = $member->email;
            $result[] = $p;
        }
                
        $this->addResponses($request, [
            'filename' => date('Y-m-d') . '-Members.csv',
            'columnNames' => 'name; lastname; mail',
            'results' => $result
        ]);
        return self::CMD_DEFAULT;
    }

    /**
     * Specialization of getLevelOfLoginRequired
     */
    #[\Override]
    protected function getLevelOfLoginRequired(): void {
        $this->setLoginLevel(new \controllerframework\sessions\NoLoginRequired());
    }

}