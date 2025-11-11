<?php
/**
 * Payment_RGLR.php
 *
 * @package model
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace model;

/**
 * Represents a normal Payment (of an order or a prepaid amount).
 * 
 * Implementation follows the design pattern 'Builder'
 *
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
class Payment_RGLR extends \membersactivities\model\subscriptions\PaymentTypeImplementation {
    #[\Override]
    public function statusReceived(\model\Payment $payment, string $status): void {
        // Put here your code if special things needs to be done when a payment status is received. E.g.
        if($status === 'paid'){
            $subscription = $this->getSubscription($payment);
            // Create your wallet tickets          
            if(_WALLETISSUERID) {
                $this->initiateGoogleWalletTickets($payment, $subscription->costitem->activity);
            }
            // Send an email to the member confirming his tickets have been paid
            $orderedTickets = $this->getOrderedTicketsText($payment);
            $download = $this->getDownloadText($payment, $subscription->costitem->activity);
            
$body =<<<_MAIL_
$orderedTickets
$download
<br><br>
Kind regards,<br>
Brussels Muzieque
_MAIL_;
            \controllerframework\mail\Mailer::sendMail("Tickets {$subscription->costitem->activity->description}", $body, _MAILTO, $payment->member->email);               
            $payment->update(array("source" => 'mollie tickets sent', 'status' => 'paid'));
        }
    }
    
    public function getOrderedTicketsText(\model\Payment $payment): string {
            $subscriptions = '<ul class="list-inline">';
            $total = 0;
            foreach (\model\Subscription::findAll("WHERE payment_id = ".$payment->getId()) as $subscription) {
                $subscriptions .= '<li>'.$subscription->costitem->description.' ('.$subscription->costitem->price.' EUR) * '.
                        $subscription->quantity.' = '.$subscription->quantity*$subscription->costitem->price.'.00 EUR</li>';
                $total += $subscription->quantity*$subscription->costitem->price;
            }
            $subscriptions .= '</ul><p><b>Total: </b>'.$total.'.00 EUR</p><br>';
            if($total != $payment->amount){
                $subscriptions .= '<i>Cost reduction: '. $payment->amount - $total . '.00 EUR</i><br>'.
                        '<b>Amount paid: '.$payment->amount.' EUR</b>';
            }
            
            $whenandwhere = $this->getWhenandwhere($subscription);
            return '<h3>Tickets '.$subscription->costitem->activity->description.'</h3><br>'
                    .'<br>'.$whenandwhere.'<br><b>Your ordered tickets:</b><br>'
                    .$subscriptions.'<br><br>';            
    }
    
    public function getWhenandwhere(\model\Subscription $subscription): string {
        $activity = $subscription->costitem->activity;
        if(!$activity->isComposite()) {
            return "<p><b>When:</b> " . date('d/m/Y', strtotime($activity->date)) . " (" . substr($activity->start,0,5) . " - " . substr($activity->end,0,5) . ")</p><br>
                            <p><b>Where:</b> " . $activity->location . "</p><br>";
        } else {
            return str_replace("&nbsp;", '',strip_tags($activity->longdescription)) . "<br>";
        }                
    }
    
    public function getDownloadText(\model\Payment $payment, \model\Activity $activity): string {
        if(!$activity->isComposite()) {
          if (_WALLETISSUERID) {
            $googleWalletToken = (new \model\GoogleWalletTicket())->createJWT($payment->getId());
            $imgsrc = _ASSETDIR.'enUS_add_to_google_wallet_wallet-button.png';
            return 'You can <a href="'._APPDIR.'pdfTickets?id='.($payment->getId()*171963).'&chk=BM*171963">'
                    . 'download your tickets in pdf format'
                    . '</a>'
                    . ' or <br><br>'
                    . '<a href="'.$googleWalletToken.'"><img src="'.$imgsrc.'" style="width:192px;height:48px;" alt="Google Wallet button missing"/></a>'
                    ;
          } else {
            return 'You can <a href="'._APPDIR.'pdfTickets?id='.($payment->getId()*171963).'&chk=BM*171963">'
                    . 'download your tickets in pdf format'
                    . '</a> here.'
                    ;
          }
        } else {
            return 'Your tickets for the individual concerts, will be sent to you 1 week before the event.';
        }                
    }
}
