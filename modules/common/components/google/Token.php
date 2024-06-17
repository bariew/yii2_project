<?php
/**
 * Token class file
 */

namespace app\modules\common\components\google;

use League\OAuth2\Client\Provider\AbstractProvider;

/**
 * Class Token
 * @package app\modules\common\components\google
 */
class Token
{
    const TYPE_GOOGLE = 'google';
    const TYPE_MICROSOFT = 'microsoft';

    public $owner, $access_token, $refresh_token, $expires_in, $created;

    /**
     * @param $array
     * @return static
     */
    public static function fromArray($array)
    {
        $model = new static();
        if (!is_array($array)) {
            return $model;
        }
        foreach ($array as $key => $value) {
            $model->$key = $value;
        }
        return $model;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return get_object_vars($this);
    }

    /**
     * @param AbstractProvider $provider
     * @param $code
     * @return static
     */
    public static function fromProvider(AbstractProvider $provider, $code)
    {
        $accessToken = $provider->getAccessToken('authorization_code', ['code' => $code]);
        $model = new static;
        $owner = $provider->getResourceOwner($accessToken)->toArray();
        $model->owner = empty($owner['email']) ? $owner['name'] : $owner['email'];
        $model->access_token = $accessToken->getToken();
        $model->refresh_token = $accessToken->getRefreshToken();
        $model->expires_in = $accessToken->getExpires() - $accessToken->getTimeNow();
        $model->created = $accessToken->getTimeNow();
        return $model;
    }

    /**
     * @return bool
     */
    public function isExpired()
    {
        return $this->created + $this->expires_in < time();
    }

    /**
     * @param AbstractProvider $provider
     * @return $this
     */
    public function refresh(AbstractProvider $provider)
    {
        $accessToken = $provider->getAccessToken('refresh_token', ['refresh_token' => $this->refresh_token]);
        $this->access_token = $accessToken->getToken();
        $this->refresh_token = $accessToken->getRefreshToken() ? : $this->refresh_token;
        $this->expires_in = $accessToken->getExpires() - $accessToken->getTimeNow();
        $this->created = $accessToken->getTimeNow();
        return $this;
    }

    /**
     * @param $scopes ['https://www.googleapis.com/auth/contacts', 'https://www.googleapis.com/auth/youtube.force-ssl']
     * @return \Google_Client
     * @throws \Google\Exception
     */
    public function google($scopes)
    {
        $clientID = \Yii::$app->params['oauth']['google']['clientId'];
        $clientSecret = \Yii::$app->params['oauth']['google']['clientSecret'];
        if ($this->isExpired()) {
            $this->refresh(static::authClient(static::TYPE_GOOGLE, $clientID, $clientSecret));
        }
        $client = new \Google_Client();
        $client->setScopes($scopes);
        $client->setAuthConfig(['client_id' => $clientID, 'client_secret' => $clientSecret]);
        $client->setAccessToken($this->toArray());
        $client->setAccessType('offline');
        return $client;
    }

    /**
     * @param $type
     * @param $clientId
     * @param $clientSecret
     * @param string $redirectUri
     * @return \League\OAuth2\Client\Provider\AbstractProvider
     * @throws \Exception
     */
    public static function authClient($type, $clientId, $clientSecret, $redirectUri = '')
    {
        $options = ['clientId' => $clientId, 'clientSecret' => $clientSecret, 'redirectUri' => $redirectUri, 'urlResourceOwnerDetails' => ''];
        switch ($type) {
            case static::TYPE_GOOGLE:
                return new \League\OAuth2\Client\Provider\Google(array_merge($options, [
                    'urlAuthorize'            => 'https://accounts.google.com/o/oauth2/auth',
                    'urlAccessToken'          => 'https://oauth2.googleapis.com/token',
                    'scopes'                  => [
                        'https://www.googleapis.com/auth/youtube.force-ssl',
//                        'https://www.googleapis.com/auth/generative-language.tuning',
//                        'https://www.googleapis.com/auth/generative-language.retriever'
                    ],
                    'accessType'              => 'offline',
                    'prompt'                  => 'consent',
                ]));
            case static::TYPE_MICROSOFT:
                return new \League\OAuth2\Client\Provider\GenericProvider(array_merge($options, [
                    'urlAuthorize'            => 'https://login.microsoftonline.com/common/oauth2/v2.0/authorize',
                    'urlAccessToken'          => 'https://login.microsoftonline.com/common/oauth2/v2.0/token',
                    'urlResourceOwnerDetails' => 'https://graph.microsoft.com/oidc/userinfo',
                    'scopes'                  => ['openid', 'profile', 'offline_access', 'user.read'],
                    'resource'                => 'https://graph.microsoft.com/',
                ]));
            default: throw new \Exception("Illegal calendar type: {$type}");
        }
    }
}