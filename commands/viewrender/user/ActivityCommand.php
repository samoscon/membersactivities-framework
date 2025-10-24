<?php
/**
 * Specialization of a Command
 *
 * @package commands\user
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace commands\viewrender\user;

/**
 * Specialization of a Command
 *
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
class ActivityCommand extends \controllers\Command {

    #[\Override]
    public function doExecute(\registry\Request $request): int {
        /** Variables */
        $propertiesMember = array();
        $propertiesSubscription = array();
        $propertiesPayment = array();
        $nameIsEmpty = $emailIsEmpty = $quantityIsEmpty = false;
        
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

        //$activity->date = date('d/m/Y', strtotime($activity->date));
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
            $costitem->description = "$costitem->description ($costitem->price EUR)";
            $costitems[] = $costitem;
        }
        $activity->costitems = $costitems;
        

        /** Check that the page was requested from itself via the POST method. */
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $propertiesMember['name'] = $name = filter_var($request->get('name'), FILTER_UNSAFE_RAW);
            $responses['nameIsEmpty'] = $nameIsEmpty = $name ? false : true;
            $propertiesMember['email'] = $email = filter_var($request->get("email"), FILTER_VALIDATE_EMAIL);
            $responses['emailIsEmpty'] = $emailIsEmpty = $email ? false : true;
            $total = 0;
            foreach ($costitems as $costitem) {
                $total += (filter_var($request->get($costitem->getId()), FILTER_VALIDATE_INT) * $costitem->price);
            }
            $quantityIsEmpty = $total ? false :true;

            if (!$nameIsEmpty && !$emailIsEmpty) {
                $validator = $this->reg->getSubscriptionValidator('User');
                
                $memberid = $this->reg->getLoginManager()->validateUsername($email);   
                if(! $memberid) {
                    $propertiesMember['active'] = 1;
                    $propertiesMember['subscriptionuntil'] = '2099-12-31';
                    \model\Member::insert($propertiesMember); //Member is not found and should be created
                    $memberid = $this->reg->getLoginManager()->validateUsername($email);
                    $member = \model\Member::find($memberid);
                }
                try {
                    $member = \model\Member::find($memberid);
                } catch (\Exception $exc) {
                    $request->addFeedback($exc->getMessage());
                    return self::CMD_ERROR;
                }
                $member->update($propertiesMember);
                $member->active = 1;
                $member->subscriptionuntil = '2099-12-31';
                $user = $this->reg->getLoginManager()->login($memberid);

                $propertiesSubscription['member_id'] = $propertiesPayment['member_id'] = $memberid;
                $propertiesPayment['amount'] = $total;
                $propertiesPayment['status'] = $total ? 'open' : 'paid';
                $orderid = \model\Payment::insert($propertiesPayment)->getId();

                foreach ($costitems as $costitem) {
                    $propertiesSubscription['quantity'] = $quantity = filter_var($request->get($costitem->getId()), FILTER_VALIDATE_INT);
                    $propertiesSubscription['costitem_id'] = $costitem->getId();
                    $propertiesSubscription['payment_id'] = $orderid;
                        
                    if ($quantity) {
                        $check = $validator->subscribe($member, $costitem, $propertiesSubscription);
                        if($check['errorcode']) {
                            $request->addFeedback("Uw inschrijving is niet doorgegaan: " . $check['description']);
                            return self::CMD_ERROR;
                        }                                       
                    }
                }
                           
                if ($total > 0) {
                    $request->set('forwardqueryparams', array('id' => $orderid*171963, 'chk' => "BM*171963"));
                    return self::CMD_OK;
                } else {
                    $request->set('forwardqueryparams', array('id' => $orderid*171963, 'chk' => "BM*171963"));
                    return self::CMD_ADMIN;
                }
            }
        } 
            
        /** the page was requested via the GET method or the POST method did not return a status. */
        $responses['activity'] = $activity;
        $responses['returnpath'] = 'admin';
        
        $this->addResponses($request, $responses);
        return self::CMD_DEFAULT;
    }
    
    #[\Override]
    protected function getLevelOfLoginRequired(): void {
        $this->setLoginLevel(new \sessions\NoLoginRequired());
    }
}
