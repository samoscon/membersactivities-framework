<?php
/**
 * TicketScanMapper.php
 *
 * @package membersactivities\model\subscriptions
 * @version 1.0
 * @copyright (c) 2025, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace membersactivities\model\subscriptions;

/**
 * Specialization of the Mapper class for Tickets
 *
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
abstract class TicketScanMapper extends \controllerframework\db\Mapper {
    
    /**
     *
     * @var string Name of the associated table for Subscription class 
     */
    private string $tablename = 'ticket_scan';
    
    /**
     * Returns table name
     * 
     * @return string table name
     */
    #[\Override]
    public function tablename(): string {
        return $this->tablename;
    }
    
    /**
     * Returns object instance of TicketScan in the client code
     * 
     * @param string $classname Name of the class
     * @param array $row Database row
     * @return \model\Ticket
     */
    #[\Override]
    protected function doCreateObject(string $classname, array $row): \model\TicketScan {
        return $classname::getInstance($row);
    }   

    /**
     * Fields that are allowed for Ticket.
     * 
     * @return array List of fields that are allowed
     */
    protected function getAllowedFields(): array
    {
        return array_merge(
            parent::getAllowedFields(),
            [
                'ticket_id',
                'scanner',
                'result'
            ]
        );
    }

}
