<?php
/**
 * GoogleWalletTicket.php
 *
 * @package model
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace model;

/**
 * Description of GoogleWalletTicket
 *
 * @author dirk
 */
class GoogleWalletTicket extends \model\wallet\GoogleWalletTicket {

    /**
     * Update a class in Google Wallet API.
     *
     * **Warning:** This replaces all existing class attributes!
     *
     * Returns the updated EventTicketClass as defined in the specific client project
     * 
     * @param EventTicketClass $class The Event Ticket in the Google API that needs to be updated
     * @param \model\Activity $activity The updated activity that has the info to update the Event Ticket
     * @return EventTicketClass The updated Event Ticket
     */
    #[\Override]
    protected function doUpdateClass(\Google\Service\Walletobjects\EventTicketClass $updatedClass, \model\activities\Activity $activity): \Google\Service\Walletobjects\EventTicketClass {
        // See link below for more information on required properties
        // https://developers.google.com/wallet/tickets/events/rest/v1/eventticketclass
        
        //You can add properties on the Event Ticket or comment-out/change below properties depending on the specific requirements of the client
        //If you add properties, use following statement template:
        // $updatedClass->setXyz(new Xyz([.....

        $updatedClass = $this->setEventName($updatedClass, $activity->description);
        $updatedClass = $this->setDateTime($updatedClass, $activity->date, $activity->start, $activity->end);
        $updatedClass = $this->setVenue($updatedClass, $activity->location);
        
        $updatedClass->setHexBackgroundColor("#cec1f0");

        return $updatedClass;
    }
    
    /**
     * Returns the updated EventTicketObject as defined in the specific client project
     * 
     * @param EventTicketObject $object The Event Object in the Google API that needs to be updated
     * @param \model\Subscription $subscription The subscription that has the info to update the Event Ticket
     * @return EventTicketObject The updated Event Object
     */
    #[\Override]
    protected function doUpdateObject(\Google\Service\Walletobjects\EventTicketObject $object, \model\Subscription $subscription, int $i): \Google\Service\Walletobjects\EventTicketObject {
        // See link below for more information on required properties
        // https://developers.google.com/wallet/tickets/events/rest/v1/eventticketobject
        // 
        //You can add properties on the Event Object or comment-out/change below properties depending on the specific requirements of the client

        $object = $this->setTicketType($object, $subscription->costitem->description);
        $object = $this->setBarcode($object, APP.' subscription# '.$subscription->getId().' Ticket# '.$i.' of '.$subscription->quantity);
        
        $object->setTicketHolderName($subscription->member->name);
        
        return $object;
    }
}
