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
class CreateActivityCommand extends \controllers\Command {
    
    /**
     * Specialization of the execute method of Command
     * 
     * @param \registry\Request $request
     * @return int
     */
    public function doExecute(\registry\Request $request): int {
        /** variables */
        $dateIsEmpty = $descriptionIsEmpty = false;
        $properties =[];
        
        /** Check that the page was requested from itself via the POST method. */
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $date = strtotime(str_replace("/", "-", $request->get('date')));
            $properties['date'] = date("Y-m-d", $date);
            $dateIsEmpty = $date ? false : true;
            
            $properties['description'] = $description = filter_var($request->get('description'), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $descriptionIsEmpty = $description ? false : true;
            
            if (!$dateIsEmpty && !$descriptionIsEmpty) {
                $properties['duedate'] = (new \DateTime($properties['date']))->sub(new \DateInterval('P0D'))->format('Y-m-d');

                $activity = \model\Activity::insert($properties);

                (new \model\GoogleWalletTicket())->createClass($activity->getId(), $description);
                
                return self::CMD_OK;
            }
        }
        
        /** the page was requested via the GET method or the POST method did not return a status. */
        $this->addResponses($request, [
            'dateIsEmpty' =>$dateIsEmpty, 
            'descriptionIsEmpty' => $descriptionIsEmpty,
            'returnpath' => 'adminhome']);        
        return self::CMD_DEFAULT;
    }

    /**
     * Specialization of getLevelOfLoginRequired
     */
    protected function getLevelOfLoginRequired(): void {
        $this->setLoginLevel(new \sessions\AdminLogin());
    }

}
