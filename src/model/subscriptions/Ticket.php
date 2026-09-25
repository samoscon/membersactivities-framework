<?php
/**
 * Ticket.php
 *
 * @package membersactivities\model\subscriptions
 * @version 1.0
 * @copyright (c) 2025, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace membersactivities\model\subscriptions;

/**
 * Registers a ticket for a subscription.
 *
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
abstract class Ticket extends \controllerframework\db\DomainObject {
    /**
     * @var PaymentTypeImplementation Relates the ticket to a certain ticket type with a specific implementation.
     * 
     * Based on design pattern 'Builder'
     */
    public ?TicketTypeImplementation $tickettypeimplementation = null;
    

    /**
     * Returns an object instance of Ticket on the basis of a database row
     * 
     * @param array $row
     * @return \model\Ticket
     */
    #[\Override]
    public static function getInstance(array $row): \model\Ticket {
        $classname = '\\'.(new \ReflectionClass(get_called_class()))->getName();
        $ticket = new $classname($row["id"]);
        $ticket->initProperties($row);
        $tickettypeclassname = $classname.'_'.$row['classification'];
        $ticket->tickettypeimplementation = new $tickettypeclassname();
        if ($ticket->subscription_id) {
            $ticket->subscription = \model\Subscription::find($ticket->subscription_id);
        }
        return $ticket;
    }
    
    /**
     * Atomically claims this ticket.
     *
     * @return bool True when the ticket was successfully claimed.
     */
    public function claim(): bool
    {
        return self::mapper()->claimTicket($this->getId());
    }

    /**
     * Finds a ticket by its token.
     *
     * @param string $token
     * @return \model\Ticket|null
     */
    public static function findByToken(string $token): ?\model\Ticket
    {
        return self::mapper()->findByToken($token);
    }    
    
}
