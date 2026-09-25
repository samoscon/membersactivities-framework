<?php
/**
 * TicketMapper.php
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
abstract class TicketMapper extends \controllerframework\db\Mapper {
    
    /**
     *
     * @var string Name of the associated table for Subscription class 
     */
    private string $tablename = 'ticket';
    
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
     * Returns object instance of Ticket in the client code
     * 
     * @param string $classname Name of the class
     * @param array $row Database row
     * @return \model\Ticket
     */
    #[\Override]
    protected function doCreateObject(string $classname, array $row): \model\Ticket {
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
                'subscription_id',
                'token',
                'seat',
                'status',
                'used',
                'used_at'
            ]
        );
    }

    /**
     * Marks a valid and unused ticket as used.
     *
     * The update is atomic so that two scanners cannot validate
     * the same ticket simultaneously.
     *
     * @param int $ticketId
     * @return bool True when the ticket was successfully claimed.
     */
    public function claimTicket(int $ticketId): bool
    {
        $sql = $this->db->prepare(
            "UPDATE `ticket`
             SET
                 `used` = 1,
                 `used_at` = CURRENT_TIMESTAMP
             WHERE
                 `id` = :id
                 AND `status` = 'valid'
                 AND `used` = 0"
        );

        $sql->execute([
            ':id' => $ticketId
        ]);

        return $sql->rowCount() === 1;
    }

    /**
     * Finds a ticket by its token.
     *
     * @param string $token
     * @return \model\Ticket|null
     */
    public function findByToken(string $token): ?\model\Ticket
    {
        $sql = $this->db->prepare(
            "SELECT *
             FROM `ticket`
             WHERE `token` = :token
             LIMIT 1"
        );

        $sql->execute([
            ':token' => $token
        ]);

        $row = $sql->fetch();

        if (!is_array($row)) {
            return null;
        }

        $classname = '\\model\\Ticket';

        return $this->createObject($classname, $row);
    }    
    
}
