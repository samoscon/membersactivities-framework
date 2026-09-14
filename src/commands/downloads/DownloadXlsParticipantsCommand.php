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

use controllerframework\security\AccessToken;

/**
 * Specialization of a Command
 *
 * @author Dirk Van Meirvenne <dirk.van.meirvenne at samosconsulting.be>
 */
class DownloadXlsParticipantsCommand extends \controllerframework\controllers\Command {
    
    /**
     * Specialization of the execute method of Command
     * 
     * @param \controllerframework\registry\Request $request
     * @return int
     */
    #[\Override]
    public function doExecute(\controllerframework\registry\Request $request): int {
        $id = filter_var($request->get('id'), FILTER_VALIDATE_INT);
        $token = ($request->get('token'));

        if (
            $id === false ||
            $token === '' ||
            !AccessToken::validate(
                'participants',
                $id,
                $token
            )
        ) {
            $request->addFeedback("Wrong ID / token combination");
            return self::CMD_ERROR;
        }

        try {
            $activity = \model\Activity::find($id);
        } catch (\Exception $exc) {
            $request->addFeedback("Unable to retrieve the activity");
            return self::CMD_ERROR;
        }

        $participants = $activity->getParticipants();
        $result = array();
        foreach ($participants as $participant) {
            $p = array();
            $p[] = $participant->name;
            $p[] = $participant->lastname <> 'lastname' ? $participant->lastname : '';
            $p[] = $participant->email;
            $p[] = $participant->costitem;
            $p[] = $participant->quantity;
            $result[] = $p;
        }
                
        $this->addResponses($request, [
            'filename' => $activity->date . '_Aanwezigheidslijst.csv',
            'columnNames' => 'name; lastname; mail; subscription; quantity',
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