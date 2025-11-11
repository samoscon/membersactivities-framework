<?php
/**
 * Specialization of a Command
 *
 * @package commands\downloads
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace membersactivities\commands\downloads;

/**
 * Specialization of a Command
 *
 * @author Dirk Van Meirvenne <dirk.van.meirvenne at samosconsulting.be>
 */
class DownloadXlsParticipantsCommand extends \controllerframework\controllers\Command {
    
    /**
     * Specialization of the execute method of Command
     * 
     * @param \registry\Request $request
     * @return int
     */
    #[\Override]
    public function doExecute(\controllerframework\registry\Request $request): int {
        $id = filter_var($request->get('id'), FILTER_VALIDATE_INT);
        try {
            $activity = \model\Activity::find($id);
        } catch (\Exception $exc) {
            echo $exc->getTraceAsString();
        }

        $participants = $activity->getParticipants();
        $result = array();
        foreach ($participants as $participant) {
            $p = array();
            $p[] = $participant->name;
            $p[] = $participant->email;
            $p[] = $participant->costitem;
            $p[] = $participant->quantity;
            $result[] = $p;
        }
                
        $this->addResponses($request, [
            'filename' => $activity->date . '_Aanwezigheidslijst.csv',
            'columnNames' => 'naam; mail; description; quantity',
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