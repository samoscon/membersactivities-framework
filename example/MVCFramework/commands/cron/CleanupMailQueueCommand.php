<?php
/**
 * Specialization of a Command
 *
 * @package controllers\commands\cron
 * @version 1.0
 * @copyright (c) 2026, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne
 */

namespace commands\cron;

/**
 * Cleanup old sent mails from the mail queue.
 *
 * @author Dirk Van Meirvenne
 */
class CleanupMailQueueCommand extends \controllerframework\controllers\Command
{
    
    /**
     * Specialization of the execute method of Command
     *
     * Removes sent mail queue records older than 7 days.
     *
     * @param \registry\Request $request
     * @return int
     */
    public function doExecute(\controllerframework\registry\Request $request): int
    {
        // ---------------------------------------------------------------------
        // Database connection
        // ---------------------------------------------------------------------

        $pdo = $this->reg->getDb();

        // ---------------------------------------------------------------------
        // Determine cutoff date
        // ---------------------------------------------------------------------

        $before = (new \DateTime())
                ->modify('-7 days')
                ->format('Y-m-d H:i:s');

        // ---------------------------------------------------------------------
        // Cleanup mail queue
        // ---------------------------------------------------------------------

        $stmt = $pdo->prepare("
            DELETE FROM mail_queue
            WHERE status = 'sent'
              AND created_at < :before
        ");

        $stmt->execute([
            ':before' => $before
        ]);

        // ---------------------------------------------------------------------
        // Log result
        // ---------------------------------------------------------------------

        file_put_contents(
            '/home/ticketingsystem/logs/mailqueue_cleanup.log',
            date('Y-m-d H:i:s')
            . ' - '
            . $stmt->rowCount()
            . " sent records verwijderd\n",
            FILE_APPEND
        );
        
        return self::CMD_DEFAULT;
    }
    
    /**
     * Specialization of getLevelOfLoginRequired
     */
    protected function getLevelOfLoginRequired(): void
    {
        $this->setLoginLevel(new \controllerframework\sessions\NoLoginRequired());
    }

}