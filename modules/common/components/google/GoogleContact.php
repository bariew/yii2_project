<?php
/**
 * GoogleContact class file
 */

namespace app\modules\common\components\google;


use Google\Service;
use yii\base\Event;


/**
 * Class Contact
 * @package app\modules\common\components\google
 *
 * @requires rapidwebltd/php-google-people-api
 */
class GoogleContact
{
    /**
     * @var Service
     */
    public $service;

    /**
     * @inheritDoc
     */
    public function __construct($clientID, $clientSecret, Token $token)
    {
        if ($token->isExpired()) {
            $token->refresh(Token::authClient(Token::TYPE_GOOGLE, $clientID, $clientSecret));
        }
        $client = new \Google_Client();
        $client->setScopes(['https://www.googleapis.com/auth/contacts']);
        $client->setAuthConfig(['client_id' => $clientID, 'client_secret' => $clientSecret]);
        $client->setAccessToken($token->toArray());
        $client->setAccessType('offline');
        $this->service =  new Service\PeopleService($client);
    }

    /**
     * @param $name
     * @param $lastName
     * @param $phoneNumber
     * @param $emailAddress
     * @param $company
     * @param null $imagePath
     * @return Service\PeopleService\Person|bool
     */
    public function create($name, $lastName, $phoneNumber, $emailAddress, $company, $imagePath = null)
    {
        $contact = new Service\PeopleService\Person();
        $contact->setNames([new Service\PeopleService\Name(['givenName' => $name, 'familyName' => $lastName])]);
        $contact->setPhoneNumbers([new Service\PeopleService\PhoneNumber(['value' => $phoneNumber])]);
        $contact->setEmailAddresses([new Service\PeopleService\EmailAddress(['value' => $emailAddress])]);
        $contact->setOrganizations([new Service\PeopleService\Organization(['name' => $company])]);
        if (!$result = $this->service->people->createContact($contact)) {/** @var Service\PeopleService\Person $result */
            return false;
        }
        if ($imagePath) {
            $this->service->people->updateContactPhoto($result->resourceName, new Service\PeopleService\UpdateContactPhotoRequest([
                'photoBytes' => base64_encode(file_get_contents($imagePath))
            ]));
        }
        return $result;
    }

    public static function event(Event $e)
    {
        $sender = $e->sender;
        $provider = (new GoogleContact(
            \Yii::$app->params['oauth']['google']['clientId'],
            \Yii::$app->params['oauth']['google']['clientSecret'],
            Token::fromArray($sender->store->oauth_google)
        ));
        $provider->search($sender->email ? : $sender->phone) || $provider->create(
            $sender->first_name,
            $sender->last_name . ' ('.$sender->store->name .')',
            str_replace('{phone}', $sender->phone, '033096298,9,{phone}#'),
            $sender->email,
            $sender->store->name,
            ''
        );
    }

    /**
     * @param $query
     * @param string[] $return
     * @return Service\PeopleService\SearchResponse
     */
    public function search($query, $return = ['emailAddresses'])
    {
        return array_map(function (Service\PeopleService\SearchResult $v) {
            return $v->getPerson()->toSimpleObject();
        }, $this->service->people->searchContacts(['query' => $query, 'readMask' => $return])->getResults());
    }
}
