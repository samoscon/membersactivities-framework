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
class EditActivityCommand extends \controllers\Command {
    
    /**
     * Specialization of the execute method of Command
     * 
     * @param \registry\Request $request
     * @return int
     */
    #[\Override]
    public function doExecute(\registry\Request $request): int {
        /** Variables */
        $properties =array();
        $dateIsEmpty = $descriptionIsEmpty = $duedateIsEmpty = $startIsEmpty = $endIsEmpty = $locationIsEmpty = false;
        
        $id = filter_var($request->get('id'), FILTER_VALIDATE_INT);
        if(!$id) {
            $request->addFeedback("Geen correct id opgegeven.");
            return self::CMD_ERROR;
        }
        
        try {
            $activity = \model\Activity::find($id);
        } catch (\Exception $exc) {
            $request->addFeedback($exc->getMessage());
            return self::CMD_ERROR;
        }
        
        $activity->date = date('d/m/Y', strtotime($activity->date));
        $activity->duedate = date('d/m/Y', strtotime($activity->duedate));
        $activity->longdescription = $activity->longdescription ? trim($activity->longdescription) : '';
        $activity->start = $activity->start ? substr($activity->start,0,5) : '';
        $activity->end = substr($activity->end ?? '',0,5);
        
        if($activity->isComposite()) {
            $activity->children = $activity->getChildren();
        }
        
        //related costitems
        $costitems = array();
        foreach (\model\Costitem::findAll('WHERE activity_id = '. $activity->getId()) as $costitem) {
            $subscriptions = array();
            $numberOfSubscriptions = 0;
            foreach (\model\Subscription::findAll('WHERE costItem_id = '.$costitem->getId()) as $subscription) {
                if ($subscription->payment->status === 'paid') {
                    $numberOfSubscriptions += $subscription->quantity;
                }
                $subscriptions[] = $subscription;
            }
            $costitem->description = "$costitem->description ($costitem->price EUR)";
            $costitem->subscriptions = $subscriptions;
            $costitem->numberOfSubscriptions = $numberOfSubscriptions;
            $costitems[] = $costitem;
        }
        $activity->costitems = $costitems;
        
        $potentialParents = \model\Activity::findAll("WHERE duedate > CURRENT_DATE AND parent_id <> {$id} AND id <> {$id} ORDER BY date");

        /** Check that the page was requested from itself via the POST method. */
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $dateIsEmpty = $duedateIsEmpty = $locationIsEmpty = $startIsEmpty = $endIsEmpty = false;

            $date = strtotime(str_replace("/", "-", $request->get('date')));
            $responses['dateIsEmpty'] = $dateIsEmpty = $date ? false : true;
            $properties['date'] = date("Y-m-d", $date);
            
            $properties['description'] = $description = $request->get('description');
            
            $duedate = strtotime(str_replace("/", "-", $request->get('duedate')));
            $responses['duedateIsEmpty'] = $duedateIsEmpty = $duedate ? false : true;
            $properties['duedate'] = date("Y-m-d", $duedate);
            
            $properties['longdescription'] = $longdescription = trim(str_replace("'", "\'", $request->get('longdescription')));

            $properties['location'] = $location = $request->get('location');
            $responses['locationIsEmpty'] = $locationIsEmpty = $location ? false : true;

            $properties['start'] = $start = substr($request->get('start') ?? '',0,5);
            $properties['end'] = $start = substr($request->get('end') ?? '',0,5);

            if (!$dateIsEmpty && !$descriptionIsEmpty && !$duedateIsEmpty && !$startIsEmpty && !$endIsEmpty && !$locationIsEmpty) {
                $updatedActivity = $activity->update($properties);
                
                if(_WALLETISSUERID) {
                    (new \model\GoogleWalletTicket())->updateClass($updatedActivity);
                }
                                
                $request->set('forwardqueryparams', ['id' => $id]);
                return self::CMD_OK;
            }
        } 
            
        /** the page was requested via the GET method or the POST method did not return a status. */
        $responses['activity'] = $activity;
        $responses['potentialParents'] = $potentialParents;
        $responses['returnpath'] = 'admin';
        
        $this->addResponses($request, $responses);
        return self::CMD_DEFAULT;
    }

    /**
     * Specialization of getLevelOfLoginRequired
     */
    #[\Override]
    protected function getLevelOfLoginRequired(): void {
        $this->setLoginLevel(new \sessions\NoLoginRequired());
    }
}
