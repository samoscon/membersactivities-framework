<?php
/**
 * GoogleWalletTicket.php
 *
 * @package wallet
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */

namespace model\wallet;

require './vendor/autoload.php';

// [START imports]
use Firebase\JWT\JWT;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Google\Client as GoogleClient;
use Google\Service\Walletobjects;
use Google\Service\Walletobjects\EventSeat;
use Google\Service\Walletobjects\LatLongPoint;
use Google\Service\Walletobjects\Barcode;
use Google\Service\Walletobjects\ImageModuleData;
use Google\Service\Walletobjects\LinksModuleData;
use Google\Service\Walletobjects\TextModuleData;
use Google\Service\Walletobjects\ImageUri;
use Google\Service\Walletobjects\Image;
use Google\Service\Walletobjects\EventTicketObject;
use Google\Service\Walletobjects\Message;
use Google\Service\Walletobjects\AddMessageRequest;
use Google\Service\Walletobjects\Uri;
use Google\Service\Walletobjects\TranslatedString;
use Google\Service\Walletobjects\LocalizedString;
use Google\Service\Walletobjects\EventTicketClass;
// [END imports]

/**
 * class for creating and managing Event tickets in Google Wallet.
 *
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
abstract class GoogleWalletTicket {
    /**
     * The Google API Client
     * https://github.com/google/google-api-php-client
     */
    public GoogleClient $client;

    /**
     * Path to service account key file from Google Cloud Console. Environment
     * variable: GOOGLE_APPLICATION_CREDENTIALS.
     */
    public string $keyFilePath;

    /**
     * Service account credentials for Google Wallet APIs.
     */
    public ServiceAccountCredentials $credentials;

    /**
     * Google Wallet service client.
     */
    public Walletobjects $service;

    public function __construct() {
        $this->keyFilePath = getenv('GOOGLE_APPLICATION_CREDENTIALS') ?: _WALLETCREDENTIALS;


        $this->auth();
    }

    /**
     * Create authenticated HTTP client using a service account file.
     */
    public function auth() {
        $this->credentials = new ServiceAccountCredentials(
            Walletobjects::WALLET_OBJECT_ISSUER,
            $this->keyFilePath
        );

        // Initialize Google Wallet API service
        $this->client = new GoogleClient();
        $this->client->setApplicationName('APPLICATION_NAME');
        $this->client->setScopes(Walletobjects::WALLET_OBJECT_ISSUER);
        $this->client->setAuthConfig($this->keyFilePath);

        $this->service = new Walletobjects($this->client);
    }

    /**
    * Create a class.
     *
     * @param string $issuerId The issuer ID being used for this request.
     * @param string $classSuffix Developer-defined unique ID for this pass class.
     *
     * @return string The pass class ID: "{$issuerId}.{$classSuffix}"
    */
    public function createClass(int $id, string $description) {
        $issuerId = _WALLETISSUERID;
        $classSuffix = APP.'_activityid_'.$id;
        // Check if the class exists
        try {
            $this->service->eventticketclass->get("{$issuerId}.{$classSuffix}");

            print("Class {$issuerId}.{$classSuffix} already exists!");
            return "{$issuerId}.{$classSuffix}";
        } catch (\Google\Service\Exception $ex) {
            if (empty($ex->getErrors()) || $ex->getErrors()[0]['reason'] != 'classNotFound') {
                // Something else went wrong...
                print_r($ex);
                return "{$issuerId}.{$classSuffix}";
            }
        }

        // See link below for more information on required properties
        // https://developers.google.com/wallet/tickets/events/rest/v1/eventticketclass
        $newClass = new EventTicketClass([
            "id" => "{$issuerId}.{$classSuffix}",
            'eventId' => "{$issuerId}.{$classSuffix}",
            "issuerName" => _MAILFROMNAME,
            "localizedIssuerName" => new LocalizedString([
                "defaultValue" => new TranslatedString([
                    "language" => "en-US",
                    "value" => _MAILFROMNAME
                ])
            ]),
            "logo"=> new Image([
                "sourceUri" => new ImageUri([
                    "uri" => _ASSETDIR."apple-touch-icon.png"
                ]),
                "contentDescription" => new LocalizedString([
                    "defaultValue" => new TranslatedString([
                        "language" => "en-US",
                        "value" => "LOGO_IMAGE_DESCRIPTION"
                    ])
                ])
            ]),
            "eventName" => new LocalizedString([
                "defaultValue" => new TranslatedString([
                    "language" => "en-US",
                    "value" => $description
                ])
            ]),
            'reviewStatus' => 'UNDER_REVIEW',
            "hexBackgroundColor" => "#cec1f0",
            'heroImage' => new Image([
              'sourceUri' => new ImageUri([
                'uri' => _LOGO
              ]),
              'contentDescription' => new LocalizedString([
                'defaultValue' => new TranslatedString([
                  'language' => 'en-US',
                  'value' => 'HERO_IMAGE_DESCRIPTION'
                ])
              ])
            ])
        ]);

        $response = $this->service->eventticketclass->insert($newClass);
        return $response->id;
    }

    /**
     * Update a class in Google Wallet API.
     *
     * **Warning:** This replaces all existing class attributes!
     *
     * @param \model\Activity $activity
     *
     * @return string The pass class ID: "{$issuerId}.{$classSuffix}"
     */
    public function updateClass(\model\activities\Activity $activity): void {
        $issuerId = _WALLETISSUERID;
        $classSuffix = APP.'_activityid_'.$activity->getId();
      
        // Check if the class exists
        try {
            $updatedClass = $this->service->eventticketclass->get("{$issuerId}.{$classSuffix}");
        } catch (\Google\Service\Exception $ex) {
            if (!empty($ex->getErrors()) && $ex->getErrors()[0]['reason'] == 'classNotFound') {
                // Class does not exist yet
                $this->createClass($activity->getId(), $activity->description);
                $updatedClass = $this->service->eventticketclass->get("{$issuerId}.{$classSuffix}");
            } else {
                // Something else went wrong...
                print_r($ex);
                return;
            }
        }
        
        $updatedClass = $this->doUpdateClass($updatedClass, $activity);    
        // Note: reviewStatus must be 'UNDER_REVIEW' or 'DRAFT' for updates
        $updatedClass->setReviewStatus('UNDER_REVIEW');

        $response = $this->service->eventticketclass->update("{$issuerId}.{$classSuffix}", $updatedClass);
    }  
    
    /**
     * Create a Ticket object.
     *
     * @param int $id The subscription ID being used for this request.
     * @param int $activityid The ID of the activity for which has been subscribed.
     * @param int $i The ticket # (needed if quantity of subscription is > 1. 
     *              In this case multiple Ticket Objects for the same subscription ID will be created.
     *
     * @return string The pass object ID: "{$issuerId}.{$objectSuffix}"
     */
    public function createObject(int $id, int $activityid, int $i): string {
        $issuerId = _WALLETISSUERID;
        $objectSuffix = APP.'_subscriptionid_'.$id.'_ticket_'.$i;
        $classSuffix = APP.'_activityid_'.$activityid;
        
        // Check if the object exists
        try {
          $this->service->eventticketobject->get("{$issuerId}.{$objectSuffix}");
          return "{$issuerId}.{$objectSuffix}";
        } catch (\Google\Service\Exception $ex) {
          if (empty($ex->getErrors()) || $ex->getErrors()[0]['reason'] != 'resourceNotFound') {
            // Something else went wrong...
            print_r($ex);
            return "{$issuerId}.{$objectSuffix}";
          }
        }

        // See link below for more information on required properties
        // https://developers.google.com/wallet/tickets/events/rest/v1/eventticketobject
        $newObject = new EventTicketObject([
          'id' => "{$issuerId}.{$objectSuffix}",
          'classId' => "{$issuerId}.{$classSuffix}",
          'state' => 'ACTIVE',
          'ticketNumber' => APP.' subscription# '.$id.' Ticket# '.$i
        ]);

        $response = $this->service->eventticketobject->insert($newObject);
        return $response->id;
      }
      
    /**
     * Update an object.
     *
     * **Warning:** This replaces all existing object attributes!
     *
     * @param \model\Subscription $subscription The subscription being used for this request.
     * @param int $i The number of the ticket if the quantity of the subscription > 1.
     *
     * @return string The pass object ID: "{$issuerId}.{$objectSuffix}"
     */
    public function updateObject(\model\Subscription $subscription, int $i): string {
        $issuerId = _WALLETISSUERID;
        $objectSuffix = APP.'_subscriptionid_'.$subscription->getId().'_ticket_'.$i;

        // Check if the object exists
        try {
            $updatedObject = $this->service->eventticketobject->get("{$issuerId}.{$objectSuffix}");
        } catch (\Google\Service\Exception $ex) {
            if (!empty($ex->getErrors()) && $ex->getErrors()[0]['reason'] == 'resourceNotFound') {
                print("Object {$issuerId}.{$objectSuffix} not found!");
                return "{$issuerId}.{$objectSuffix}";
            } else {
                // Something else went wrong...
                print_r($ex);
                return "{$issuerId}.{$objectSuffix}";
            }
        }

        $updatedObject = $this->doUpdateObject($updatedObject, $subscription, $i);

        $response = $this->service->eventticketobject->update("{$issuerId}.{$objectSuffix}", $updatedObject);
        return $response->id;
    }

    /**
     * Generate a signed JWT that references an existing pass object.
     *
     * When the user opens the "Add to Google Wallet" URL and saves the pass to
     * their wallet, the pass objects defined in the JWT are added to the
     * user's Google Wallet app. This allows the user to save multiple pass
     * objects in one API call.
     *
     * The objects to add must follow the below format:
     *
     *  {
     *    'id': 'ISSUER_ID.OBJECT_SUFFIX',
     *    'classId': 'ISSUER_ID.CLASS_SUFFIX'
     *  }
     *
     * @param string $id The payment ID being used for this request.
     *
     * @return string An "Add to Google Wallet" link.
     */
    public function createJwt(int $id) : string
    {
        $issuerId = _WALLETISSUERID;
        $objectsToAdd = [];
        $eventTicketObjects = [];
                
        foreach (\model\Subscription::findAll("WHERE payment_id = ".$id) as $subscription) {
            $classSuffix = APP.'_activityid_'.$subscription->costitem->activity->getId();
            for($i = 0; $i < $subscription->quantity; $i++) {
                $objectSuffix = APP.'_subscriptionid_'.$subscription->getId().'_ticket_'.$i+1;
                $eventTicketObjects[] = [
                    'id' => "{$issuerId}.{$objectSuffix}",
                    'classId' => "{$issuerId}.{$classSuffix}"
                ];
            }
        }
        $objectsToAdd['eventTicketObjects'] = $eventTicketObjects;
                
        // The service account credentials are used to sign the JWT
        $serviceAccount = json_decode(file_get_contents($this->keyFilePath), true);

        // Create the JWT as an array of key/value pairs
        $claims = [
          'iss' => $serviceAccount['client_email'],
          'aud' => 'google',
          'origins' => ['ticketingsystem.link'],
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
     * Returns the updated EventTicketClass as defined in the specific client project
     * 
     * @param EventTicketClass $class The Event Ticket in the Google API that needs to be updated
     * @param \model\Activity $activity The updated activity that has the info to update the Event Ticket
     * @return EventTicketClass The updated Event Ticket
     */
    abstract protected function doUpdateClass(EventTicketClass $class, \model\activities\Activity $activity): EventTicketClass;
    
    /**
     * Returns the updated EventTicketObject as defined in the specific client project
     * 
     * @param EventTicketObject $object The Event Object in the Google API that needs to be updated
     * @param \model\Subscription $subscription The subscription that has the info to update the Event Ticket
     * @return EventTicketObject The updated Event Object
     */
    abstract protected function doUpdateObject(EventTicketObject $object, \model\Subscription $subscription, int $i): EventTicketObject;
    
    protected function setEventName(EventTicketClass $updatedClass, string $description): EventTicketClass {
        $updatedClass->setEventName(new LocalizedString([
            "defaultValue" => new TranslatedString([
                "language" => "en-US",
                "value" => $description
            ])
        ]));
        return $updatedClass;
    }
    
    protected function setDateTime(EventTicketClass $updatedClass, string $date, string $start, string $end): EventTicketClass {
         $updatedClass->setDateTime(new Walletobjects\EventDateTime([
            'start' => "{$date}T{$start}Z",
            "end" => "{$date}T{$end}Z"
        ]));
        return $updatedClass;
    }
    
    protected function setVenue(EventTicketClass $updatedClass, string $location): EventTicketClass {
         $updatedClass->setVenue(new Walletobjects\EventVenue([
            'name' => new LocalizedString([
                "defaultValue" => new TranslatedString([
                    "language" => "en-US",
                    "value" => $location
                ])
            ]),
            'address' => new LocalizedString([
                "defaultValue" => new TranslatedString([
                    "language" => "en-US",
                    "value" => "-"
                ])
            ])
        ]));
        return $updatedClass;
    }
    
    protected function setTicketType(EventTicketObject $updatedObject, string $description): EventTicketObject {
        $updatedObject->setTicketType(new LocalizedString([
            "defaultValue" => new TranslatedString([
                "language" => "en-US",
                "value" => $description
            ])
        ]));
        return $updatedObject;
    }

    protected function setBarcode(EventTicketObject $updatedObject, string $description): EventTicketObject {
        $updatedObject->setBarcode(new Barcode([
            'type' => 'QR_CODE',
            'value' => $description
        ]));
        return $updatedObject;
    }
}
