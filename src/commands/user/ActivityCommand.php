<?php
/**
 * Specialization of a Command
 *
 * @package membersactivities\commands\user
 * @version 1.0
 * @copyright (c) 2025, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace membersactivities\commands\user;

/**
 * Specialization of a Command
 *
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
class ActivityCommand extends \controllerframework\controllers\Command {

    /**
     * Specialization of the execute method of Command
     * 
     * @param \controllerframework\registry\Request $request
     * @return int
     */
    #[\Override]
    public function doExecute(\controllerframework\registry\Request $request): int {
        // CSRF requires an active session.
        $this->reg->getLoginManager();
        
        /** Variables */

        $propertiesMember = array();
        $propertiesSubscription = array();
        $propertiesPayment = array();
        $emailIsEmpty = $nameIsEmpty = false;
        $quantityIsEmpty = false;
        
        if(_MINLEVELTOLOGIN !== 'A') {
            $responses['member'] = $member = \controllerframework\sessions\User::getInstance();
        }
        
        $id = filter_var($request->get('id'), FILTER_VALIDATE_INT);
        if(!$id) {
            $request->set('errorcode', 'wrongID');
            $request->addFeedback("Wrong ID");
            return self::CMD_ERROR;
        }
        
        try {
            $activity = \model\Activity::find($id);
        } 
        catch (\Throwable $ex) {
            \controllerframework\error\ErrorHandler::handleException($ex);
            $request->addFeedback('Unable to retrieve the requested item.');
            return self::CMD_ERROR;
        }

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
            $costitem->descriptionWithPrice = "$costitem->description ($costitem->price EUR)";
            $costitems[] = $costitem;
        }
        $activity->costitems = $costitems;
        

        /** Check that the page was requested from itself via the POST method. */
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            /** Validate CSRF token before processing. */ 
            if (!$this->validateCsrfToken($request)) { 
                $request->set('errorcode', 'InvalidCsrfToken');
                return self::CMD_ERROR;
            }
            
            if(_MINLEVELTOLOGIN === 'A') {
                $propertiesMember['name'] = $name = filter_var($request->get('name'), FILTER_UNSAFE_RAW);
                $responses['nameIsEmpty'] = $nameIsEmpty = $name ? false : true;
                $propertiesMember['email'] = $email = filter_var($request->get("email"), FILTER_VALIDATE_EMAIL);
                $responses['emailIsEmpty'] = $emailIsEmpty = $email ? false : true;
            }
            $total = 0;
            foreach ($costitems as $costitem) {
                if($request->get('seatmap')) {
                    $seat = rtrim($request->get($costitem->getId().'Seats', true), ';');

                    //check seat against reserved seats in related subscriptions
                    foreach (\model\Subscription::findAll('WHERE costitem_id = '. $costitem->getId()) as $subscription) {

                        foreach (explode(';', rtrim($subscription->remark,';')) as $reservedSeat) {

                            if($seat && $seat === $reservedSeat) {
                                $request->addFeedback('Sorry ! Your seats have been reserved in the meantime by somebody else. '
                                        . '<a href="/activity?id='.$id.'">Please try again by clicking here</a>');
                                return self::CMD_ERROR;
                            }                        
                        }
                    }                    
                }
                $total += (filter_var($request->get($costitem->getId()), FILTER_VALIDATE_INT) * $costitem->price);
            }
            $quantityIsEmpty = $total ? false :true;

            if (!$nameIsEmpty && !$emailIsEmpty) {
                $validator = $request->get('validator');

                if (!$validator instanceof \membersactivities\model\subscriptions\SubscriptionValidationStrategy) {
                    $request->set('errorcode', 'InvalidValidationStrategy');
                    $request->addFeedback('Invalid validation strategy.');
                    return self::CMD_ERROR;
                }  
                
                if(_MINLEVELTOLOGIN === 'A') {               
                    $memberid = $this->reg->getLoginManager()->validateUsername($email);   
                    if(! $memberid) {
                        $propertiesMember['active'] = 1;
                        $propertiesMember['subscriptionuntil'] = '2099-12-31';
                        \model\Member::insert($propertiesMember); //Member is not found and should be created
                        $memberid = $this->reg->getLoginManager()->validateUsername($email);
                    }
                    try {
                        $member = \model\Member::find($memberid);
                    } 
                    catch (\Throwable $ex) {
                        \controllerframework\error\ErrorHandler::handleException($ex);
                        $request->addFeedback('Unable to retrieve the requested memberid in Member.');
                        return self::CMD_ERROR;
                    }

                    $member->update($propertiesMember);
                    $member->active = 1;
                    $member->subscriptionuntil = '2099-12-31';
                }
            }


            $propertiesSubscription['member_id'] = $propertiesPayment['member_id'] = $member->getId();
            $propertiesPayment['amount'] = $total;
            $propertiesPayment['status'] = $total ? 'open' : 'paid';
            $orderid = \model\Payment::insert($propertiesPayment)->getId();
            
            foreach ($costitems as $costitem) {
                $propertiesSubscription['quantity'] = $quantity = filter_var($request->get($costitem->getId()), FILTER_VALIDATE_INT);
                $reservedSeats = $costitem->getId().'Seats';
                $propertiesSubscription['remark'] = $request->get($reservedSeats);
                $propertiesSubscription['costitem_id'] = $costitem->getId();
                $propertiesSubscription['payment_id'] = $orderid;

                if ($quantity) {
                    $check = $validator->subscribe($member, $costitem, $propertiesSubscription);
                   if($check['errorcode']) {
                        $request->addFeedback($check['description']);
                        return self::CMD_ERROR;
                    }                                       
                }
            }

             if ($total <= 0) {
                $request->set('forwardqueryparams', array('order_id' => $orderid));
                return self::CMD_ADMIN;
            }
            if(_WTALLOWED === 'Y') {
                $request->set('forwardqueryparams', array('id' => $orderid));
                return self::CMD_OK;
            } else {
                $request->set('forwardqueryparams', array('id' => $orderid));
                return self::CMD_CONTINUE;
            }
        } 
            
        /** the page was requested via the GET method or the POST method did not return a status. */
        $responses['csrf_token'] = $this->getCsrfToken();
        $responses['activity'] = $activity;
        $responses['returnpath'] = 'admin';
        
        $this->addResponses($request, $responses);
        return self::CMD_DEFAULT;
    }
    
    #[\Override]
    protected function getLevelOfLoginRequired(): void {
        $this->setLoginLevel(new \controllerframework\sessions\NoLoginRequired());
    }
}
