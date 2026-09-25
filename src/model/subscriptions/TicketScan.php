<?php
/**
 * TicketScan.php
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
abstract class TicketScan extends \controllerframework\db\DomainObject {
    /**
     * @var PaymentTypeImplementation Relates the ticket to a certain ticket type with a specific implementation.
     * 
     * Based on design pattern 'Builder'
     */
    public ?TicketScanTypeImplementation $ticketscantypeimplementation = null;
    

    /**
     * Returns an object instance of TicketScan on the basis of a database row
     * 
     * @param array $row
     * @return \model\TicketScan
     */
    #[\Override]
    public static function getInstance(array $row): \model\TicketScan {
        $classname = '\\'.(new \ReflectionClass(get_called_class()))->getName();
        $ticketscan = new $classname($row["id"]);
        $ticketscan->initProperties($row);
        $ticketscantypeclassname = $classname.'_'.$row['classification'];
        $ticketscan->ticketscantypeimplementation = new $ticketscantypeclassname();
        if ($ticketscan->ticket_id) {
            $ticketscan->ticket = \model\Ticket::find($ticketscan->ticket_id);
        }
        return $ticketscan;
    }
}
