<?php
/**
 * GoogleWalletTicket.php
 *
 * @package membersactivities\model\wallet
 * @version 1.0
 * @copyright (c) 2025, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */

namespace membersactivities\model\wallet;

// [START imports]
use Firebase\JWT\JWT;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Google\Client as GoogleClient;
use Google\Service\Walletobjects;
use Google\Service\Walletobjects\Barcode;
use Google\Service\Walletobjects\EventTicketClass;
use Google\Service\Walletobjects\EventTicketObject;
use Google\Service\Walletobjects\Image;
use Google\Service\Walletobjects\ImageUri;
use Google\Service\Walletobjects\LocalizedString;
use Google\Service\Walletobjects\TranslatedString;
// [END imports]

/**
 * Class for creating and managing Event tickets in Google Wallet.
 *
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
abstract class GoogleWalletTicket
{
    /**
     * The Google API Client.
     *
     * @var GoogleClient
     */
    public GoogleClient $client;

    /**
     * Path to service account key file from Google Cloud Console.
     * Environment variable: GOOGLE_APPLICATION_CREDENTIALS.
     *
     * @var string
     */
    public string $keyFilePath;

    /**
     * Service account credentials for Google Wallet APIs.
     *
     * @var ServiceAccountCredentials
     */
    public ServiceAccountCredentials $credentials;

    /**
     * Google Wallet service client.
     *
     * @var Walletobjects
     */
    public Walletobjects $service;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->keyFilePath = getenv('GOOGLE_APPLICATION_CREDENTIALS')
            ?: _WALLETCREDENTIALS;

        $this->auth();
    }

    /**
     * Create authenticated HTTP client using a service account file.
     *
     * @return void
     */
    public function auth(): void
    {
        $this->credentials = new ServiceAccountCredentials(
            Walletobjects::WALLET_OBJECT_ISSUER,
            $this->keyFilePath
        );

        // Initialize Google Wallet API service.
        $this->client = new GoogleClient();
        $this->client->setApplicationName('APPLICATION_NAME');
        $this->client->setScopes(
            Walletobjects::WALLET_OBJECT_ISSUER
        );
        $this->client->setAuthConfig($this->keyFilePath);

        $this->service = new Walletobjects($this->client);
    }

    /**
     * Check whether a Google API exception contains the specified
     * error reason.
     *
     * @param \Google\Service\Exception $ex
     * @param string $reason
     *
     * @return bool
     */
    protected function isGoogleError(
        \Google\Service\Exception $ex,
        string $reason
    ): bool {
        $errors = $ex->getErrors();

        return !empty($errors)
            && isset($errors[0]['reason'])
            && $errors[0]['reason'] === $reason;
    }

    /**
     * Create a class.
     *
     * @param int $id
     *        The id of the activity being used for this request.
     * @param string $description
     *        Event name (title of the event).
     *
     * @return string
     *         The pass class ID: "{$issuerId}.{$classSuffix}"
     *
     * @throws \Google\Service\Exception
     */
    public function createClass(
        int $id,
        string $description
    ): string {
        $issuerId = _WALLETISSUERID;
        $classSuffix = APP . '_activityid_' . $id;
        $classId = "{$issuerId}.{$classSuffix}";

        // Check if the class exists.
        try {
            $this->service->eventticketclass->get($classId);

            // Class already exists. This is a normal situation.
            return $classId;
        } catch (\Google\Service\Exception $ex) {
            if (!$this->isGoogleError($ex, 'classNotFound')) {
                // Unexpected Google API error.
                // Let the Controller Framework ErrorHandler handle it.
                throw $ex;
            }
        }

        // See link below for more information on required properties:
        // https://developers.google.com/wallet/tickets/events/rest/v1/eventticketclass
        $newClass = new EventTicketClass([
            'id' => $classId,
            'eventId' => $classId,
            'issuerName' => _MAILFROMNAME,
            'localizedIssuerName' => new LocalizedString([
                'defaultValue' => new TranslatedString([
                    'language' => 'en-US',
                    'value' => _MAILFROMNAME
                ])
            ]),
            'logo' => new Image([
                'sourceUri' => new ImageUri([
                    'uri' => _ASSETDIR . 'apple-touch-icon.png'
                ]),
                'contentDescription' => new LocalizedString([
                    'defaultValue' => new TranslatedString([
                        'language' => 'en-US',
                        'value' => 'LOGO_IMAGE_DESCRIPTION'
                    ])
                ])
            ]),
            'eventName' => new LocalizedString([
                'defaultValue' => new TranslatedString([
                    'language' => 'en-US',
                    'value' => $description
                ])
            ]),
            'reviewStatus' => 'UNDER_REVIEW',
            'hexBackgroundColor' => '#cec1f0',
            'heroImage' => new Image([
                'sourceUri' => new ImageUri([
                    'uri' => _APPDIR . _LOGO
                ]),
                'contentDescription' => new LocalizedString([
                    'defaultValue' => new TranslatedString([
                        'language' => 'en-US',
                        'value' => 'HERO_IMAGE_DESCRIPTION'
                    ])
                ])
            ])
        ]);

        $response = $this->service->eventticketclass->insert(
            $newClass
        );

        return $response->id;
    }

    /**
     * Update a class in Google Wallet API.
     *
     * Warning: This replaces all existing class attributes!
     *
     * @param \membersactivities\model\activities\Activity $activity
     *
     * @return void
     *
     * @throws \Google\Service\Exception
     */
    public function updateClass(
        \membersactivities\model\activities\Activity $activity
    ): void {
        $issuerId = _WALLETISSUERID;
        $classSuffix = APP . '_activityid_' . $activity->getId();
        $classId = "{$issuerId}.{$classSuffix}";

        // Check if the class exists.
        try {
            $updatedClass = $this->service->eventticketclass->get(
                $classId
            );
        } catch (\Google\Service\Exception $ex) {
            if ($this->isGoogleError($ex, 'classNotFound')) {
                // Class does not exist yet.
                $this->createClass(
                    $activity->getId(),
                    $activity->description
                );

                // Retrieve the newly created class.
                $updatedClass = $this->service->eventticketclass->get(
                    $classId
                );
            } else {
                // Unexpected Google API error.
                throw $ex;
            }
        }

        $updatedClass = $this->doUpdateClass(
            $updatedClass,
            $activity
        );

        // Note: reviewStatus must be 'UNDER_REVIEW' or 'DRAFT'
        // for updates.
        $updatedClass->setReviewStatus('UNDER_REVIEW');

        $this->service->eventticketclass->update(
            $classId,
            $updatedClass
        );
    }

    /**
     * Create a Ticket object.
     *
     * @param int $id
     *        The subscription ID being used for this request.
     * @param int $activityid
     *        The ID of the activity for which has been subscribed.
     * @param int $i
     *        The ticket # (needed if quantity of subscription is > 1).
     *        In this case multiple Ticket Objects for the same
     *        subscription ID will be created.
     *
     * @return string
     *         The pass object ID: "{$issuerId}.{$objectSuffix}"
     *
     * @throws \Google\Service\Exception
     */
    public function createObject(
        int $id,
        int $activityid,
        int $i
    ): string {
        $issuerId = _WALLETISSUERID;

        $objectSuffix = APP
            . '_subscriptionid_'
            . $id
            . '_ticket_'
            . $i;

        $objectId = "{$issuerId}.{$objectSuffix}";

        $classSuffix = APP . '_activityid_' . $activityid;

        // Check if the object exists.
        try {
            $this->service->eventticketobject->get($objectId);

            // Object already exists. This is a normal situation.
            return $objectId;
        } catch (\Google\Service\Exception $ex) {
            if (!$this->isGoogleError($ex, 'resourceNotFound')) {
                // Unexpected Google API error.
                throw $ex;
            }
        }

        // See link below for more information on required properties:
        // https://developers.google.com/wallet/tickets/events/rest/v1/eventticketobject
        $newObject = new EventTicketObject([
            'id' => $objectId,
            'classId' => "{$issuerId}.{$classSuffix}",
            'state' => 'ACTIVE',
            'ticketNumber' => APP
                . ' subscription# '
                . $id
                . ' Ticket# '
                . $i
        ]);

        $response = $this->service->eventticketobject->insert(
            $newObject
        );

        return $response->id;
    }

    /**
     * Update an object.
     *
     * Warning: This replaces all existing object attributes!
     *
     * @param \model\Subscription $subscription
     *        The subscription being used for this request.
     * @param int $i
     *        The number of the ticket if the quantity of the
     *        subscription > 1.
     *
     * @return string
     *         The pass object ID: "{$issuerId}.{$objectSuffix}"
     *
     * @throws \Google\Service\Exception
     */
    public function updateObject(
        \model\Subscription $subscription,
        int $i
    ): string {
        $issuerId = _WALLETISSUERID;

        $objectSuffix = APP
            . '_subscriptionid_'
            . $subscription->getId()
            . '_ticket_'
            . $i;

        $objectId = "{$issuerId}.{$objectSuffix}";

        // Check if the object exists.
        try {
            $updatedObject = $this->service->eventticketobject->get(
                $objectId
            );
        } catch (\Google\Service\Exception $ex) {
            // A missing object is an error for an update.
            // Do not expose technical details to the browser.
            // Let the Controller Framework ErrorHandler handle it.
            throw $ex;
        }

        $updatedObject = $this->doUpdateObject(
            $updatedObject,
            $subscription,
            $i
        );

        $response = $this->service->eventticketobject->update(
            $objectId,
            $updatedObject
        );

        return $response->id;
    }

    /**
     * Generate a signed JWT that references existing pass objects.
     *
     * When the user opens the "Add to Google Wallet" URL and saves
     * the pass to their wallet, the pass objects defined in the JWT
     * are added to the user's Google Wallet app.
     *
     * @param int $id
     *        The payment ID being used for this request.
     *
     * @return string
     *         An "Add to Google Wallet" link.
     *
     * @throws \JsonException
     * @throws \RuntimeException
     */
    public function createJwt(int $id): string
    {
        $issuerId = _WALLETISSUERID;
        $objectsToAdd = [];
        $eventTicketObjects = [];

        foreach (
            \model\Subscription::findAll(
                'WHERE payment_id = ' . $id
            ) as $subscription
        ) {
            $classSuffix = APP
                . '_activityid_'
                . $subscription->costitem->activity->getId();

            for ($i = 0; $i < $subscription->quantity; $i++) {
                $ticketNumber = $i + 1;

                $objectSuffix = APP
                    . '_subscriptionid_'
                    . $subscription->getId()
                    . '_ticket_'
                    . $ticketNumber;

                $eventTicketObjects[] = [
                    'id' => "{$issuerId}.{$objectSuffix}",
                    'classId' => "{$issuerId}.{$classSuffix}"
                ];
            }
        }

        $objectsToAdd['eventTicketObjects'] = $eventTicketObjects;

        // The service account credentials are used to sign the JWT.
        $serviceAccountJson = file_get_contents(
            $this->keyFilePath
        );

        if ($serviceAccountJson === false) {
            throw new \RuntimeException(
                'Unable to read Google Wallet service account credentials.'
            );
        }

        $serviceAccount = json_decode(
            $serviceAccountJson,
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        if (
            empty($serviceAccount['client_email'])
            || empty($serviceAccount['private_key'])
        ) {
            throw new \RuntimeException(
                'Invalid Google Wallet service account credentials.'
            );
        }

        // Create the JWT as an array of key/value pairs.
        $claims = [
            'iss' => $serviceAccount['client_email'],
            'aud' => 'google',
            'origins' => [_WALLETORIGIN],
            'typ' => 'savetowallet',
            'payload' => $objectsToAdd
        ];

        $token = JWT::encode(
            $claims,
            $serviceAccount['private_key'],
            'RS256'
        );

        return "https://pay.google.com/gp/v/save/{$token}";
    }

    /**
     * Returns the updated EventTicketClass as defined in the
     * specific client project.
     *
     * @param EventTicketClass $class
     *        The Event Ticket in the Google API that needs
     *        to be updated.
     * @param \membersactivities\model\activities\Activity $activity
     *        The updated activity that has the info to update
     *        the Event Ticket.
     *
     * @return EventTicketClass
     */
    abstract protected function doUpdateClass(
        EventTicketClass $class,
        \membersactivities\model\activities\Activity $activity
    ): EventTicketClass;

    /**
     * Returns the updated EventTicketObject as defined in the
     * specific client project.
     *
     * @param EventTicketObject $object
     *        The Event Object in the Google API that needs
     *        to be updated.
     * @param \model\Subscription $subscription
     *        The subscription that has the info to update
     *        the Event Ticket.
     * @param int $i
     *        The ticket number.
     *
     * @return EventTicketObject
     */
    abstract protected function doUpdateObject(
        EventTicketObject $object,
        \model\Subscription $subscription,
        int $i
    ): EventTicketObject;

    /**
     * Set event name.
     *
     * @param EventTicketClass $updatedClass
     * @param string $description
     *
     * @return EventTicketClass
     */
    protected function setEventName(
        EventTicketClass $updatedClass,
        string $description
    ): EventTicketClass {
        $updatedClass->setEventName(
            new LocalizedString([
                'defaultValue' => new TranslatedString([
                    'language' => 'en-US',
                    'value' => $description
                ])
            ])
        );

        return $updatedClass;
    }

    /**
     * Set date/time.
     *
     * @param EventTicketClass $updatedClass
     * @param string $date
     * @param string $start
     * @param string $end
     *
     * @return EventTicketClass
     */
    protected function setDateTime(
        EventTicketClass $updatedClass,
        string $date,
        string $start,
        string $end
    ): EventTicketClass {
        $updatedClass->setDateTime(
            new Walletobjects\EventDateTime([
                'start' => "{$date}T{$start}Z",
                'end' => "{$date}T{$end}Z"
            ])
        );

        return $updatedClass;
    }

    /**
     * Set venue.
     *
     * @param EventTicketClass $updatedClass
     * @param string $location
     *
     * @return EventTicketClass
     */
    protected function setVenue(
        EventTicketClass $updatedClass,
        string $location
    ): EventTicketClass {
        $updatedClass->setVenue(
            new Walletobjects\EventVenue([
                'name' => new LocalizedString([
                    'defaultValue' => new TranslatedString([
                        'language' => 'en-US',
                        'value' => $location
                    ])
                ]),
                'address' => new LocalizedString([
                    'defaultValue' => new TranslatedString([
                        'language' => 'en-US',
                        'value' => '-'
                    ])
                ])
            ])
        );

        return $updatedClass;
    }

    /**
     * Set ticket type.
     *
     * @param EventTicketObject $updatedObject
     * @param string $description
     *
     * @return EventTicketObject
     */
    protected function setTicketType(
        EventTicketObject $updatedObject,
        string $description
    ): EventTicketObject {
        $updatedObject->setTicketType(
            new LocalizedString([
                'defaultValue' => new TranslatedString([
                    'language' => 'en-US',
                    'value' => $description
                ])
            ])
        );

        return $updatedObject;
    }

    /**
     * Set barcode.
     *
     * @param EventTicketObject $updatedObject
     * @param string $description
     *
     * @return EventTicketObject
     */
    protected function setBarcode(
        EventTicketObject $updatedObject,
        string $description
    ): EventTicketObject {
        $updatedObject->setBarcode(
            new Barcode([
                'type' => 'QR_CODE',
                'value' => $description
            ])
        );

        return $updatedObject;
    }
}