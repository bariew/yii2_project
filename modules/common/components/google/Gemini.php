<?php
/**
 * Gemini class file
 */

namespace app\modules\common\components\google;

use GuzzleHttp\Client;
use RollingCurl\Request;
use RollingCurl\RollingCurl;

/**
 * Class Gemini
 * @package app\modules\common\components
 */
class Gemini
{
    private $url = 'https://generativelanguage.googleapis.com/v1/';
    public $version = 'gemini-pro';//'gemini-1.5-pro';//
    private $key;

    public function __construct($key)
    {
        $this->key = $key;
    }

    /**
     * @return static
     */
    public static function yii()
    {
        return new static(\Yii::$app->params['google']['ai_api_key']);
    }

    /**
     * @param $data
     * @param $callback
     * @throws \Exception
     */
    public function multiRequest($data, $callback)
    {
        $rollingCurl = new RollingCurl();
        $rollingCurl->setSimultaneousLimit(3);
        foreach ($data as $id => $content) {
            if (!$id) {
                throw new \Exception("Missing request ID");
            }
            $request = new Request($this->url."models/{$this->version}:generateContent?".http_build_query(['key' => $this->key]), 'POST');
            $post = json_encode(['contents' => [['parts' => ['text' => $content]]]]);
            $request->setOptions([
                CURLOPT_POST => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'Content-Length: '. strlen($post)],
                CURLOPT_POSTFIELDS => $post,
                CURLOPT_TIMEOUT => 120,
                CURLOPT_CONNECTTIMEOUT => 120,
            ])->setExtraInfo(['id' => $id]);
            $rollingCurl->add($request);
        }

        $rollingCurl->setCallback($callback)->execute(); /** @return \RollingCurl\Request */
    }

    public function request($url= null, $post = [])
    {
        $url = $url ? : "models/{$this->version}:generateContent";
        $post = is_array($post) ? $post : [
            'contents' => [['parts' => ['text' => $post]]],
            'safetySettings' => [
               ["category"=> "HARM_CATEGORY_HARASSMENT", "threshold"=> "BLOCK_NONE",],
                ["category"=> "HARM_CATEGORY_HATE_SPEECH", "threshold"=> "BLOCK_NONE",],
                ["category"=> "HARM_CATEGORY_SEXUALLY_EXPLICIT", "threshold"=> "BLOCK_NONE",],
                ["category"=> "HARM_CATEGORY_DANGEROUS_CONTENT", "threshold"=> "BLOCK_NONE",],
            ]
        ];
        $result = (new Client())->request($post ? 'POST' : 'GET', $this->url.$url.'?'.http_build_query(['key' => $this->key]), [
            'headers' => array_filter([
                'Content-Type' => $post ? 'application/json' : null,
                'Content-Length' => $post ? strlen(json_encode($post)) : null,
            ]),
            'body' => $post ? json_encode($post) : null,
        ])->getBody()->getContents();
        return json_decode($result, true)['candidates'][0]['content']['parts'][0]['text'] ?? $result;
    }
}