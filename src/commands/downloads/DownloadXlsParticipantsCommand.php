<?php
/**
 * Specialization of a Command
 *
 * @package membersactivities\commands\downloads
 * @version 1.0
 * @copyright (c) 2025, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace membersactivities\commands\downloads;

use controllerframework\security\AccessToken;

/**
 * Specialization of a Command
 *
 * @author Dirk Van Meirvenne <dirk.van.meirvenne at samosconsulting.be>
 */
class DownloadXlsParticipantsCommand extends \controllerframework\controllers\Command {
    
    /**
     * Specialization of the execute method of Command
     * 
     * @param \controllerframework\registry\Request $request
     * @return int
     */
    #[\Override]
    public function doExecute(\controllerframework\registry\Request $request): int {
        $id = filter_var($request->get('id'), FILTER_VALIDATE_INT);
        $token = ($request->get('token'));

        if (
            $id === false ||
            $token === '' ||
            !AccessToken::validate(
                'participants',
                $id,
                $token
            )
        ) {
            $request->addFeedback("Wrong ID / token combination");
            return self::CMD_ERROR;
        }

        try {
            $activity = \model\Activity::find($id);
        } catch (\Exception $exc) {
            $request->addFeedback("Unable to retrieve the activity");
            return self::CMD_ERROR;
        }

        $tickets = \model\Ticket::findAll(
            'WHERE subscription_id IN (
                SELECT s.id
                FROM subscription s
                INNER JOIN costitem c ON c.id = s.costitem_id
                WHERE c.activity_id = ' . (int) $id . '
            ) ORDER BY id'
        );

        $result = [];
        $totalQuantity = 0;
        $totalPrice = 0.0;

        foreach ($tickets as $ticket) {
            $subscription = $ticket->subscription;
            $costitem = $subscription->costitem ?? null;
            $member = $subscription->member ?? null;

            $quantity = 1;
            $price = (float) ($costitem->price ?? 0);

            $row = [
                'name' => trim($member->name ?? ''),
                'mail' => trim($member->email ?? ''),
                'costitem' => $costitem->description ?? '',
                'quantity' => $quantity,
                'price' => number_format($price, 2, '.', '')
            ];

            $result[] = $row;

            // Totalen berekenen
            $totalQuantity += $quantity;
            $totalPrice += $price;
        }

        // Sorteren op naam
        usort($result, static fn($a, $b) =>
            strcasecmp($a['name'], $b['name'])
        );

        // Totalen toevoegen als laatste rij
        $result[] = [
            'name' => '',
            'mail' => '',
            'costitem' => 'TOTAAL',
            'quantity' => $totalQuantity,
            'price' => number_format($totalPrice, 2, '.', '')
        ];

        $this->addResponses($request, [
            'filename' => $activity->date . '_Aanwezigheidslijst.csv',
            'columnNames' => 'name; mail; subscription; quantity; price',
            'results' => $result
        ]);

        return self::CMD_DEFAULT;     

        $this->addResponses($request, [
            'filename' => $activity->date . '_Aanwezigheidslijst.csv',
            'columnNames' => 'name; mail; subscription; quantity; price',
            'results' => $result
        ]);
         return self::CMD_DEFAULT;
    }

    /**
     * Specialization of getLevelOfLoginRequired
     */
    #[\Override]
    protected function getLevelOfLoginRequired(): void {
        $this->setLoginLevel(new \controllerframework\sessions\NoLoginRequired());
    }

}