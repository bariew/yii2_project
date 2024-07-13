<?php
/**
 * Youtube class file
 */

namespace app\modules\common\components\google;

use app\modules\common\components\google\Token;
use app\modules\common\models\Settings;
use GuzzleHttp\Client;
use Madcoda\Youtube\Youtube as YoutubeAPI;

/**
 * Class Youtube
 * @package app\modules\common\components\google
 * @link https://developers.google.com/youtube/v3/docs/search/list Quota 10,000 per day
 */
class Youtube
{
    const THUMBNAIL_TYPE_SMALL = 'default';//120x90
    const THUMBNAIL_TYPE_MEDIUM= 'mqdefault';//320x180
    const THUMBNAIL_TYPE_LARGE = 'hqdefault';//320x180

    /**
     * @var YoutubeAPI
     */
    public $api;
    public $key;

    /**
     * Youtube constructor.
     * @param $key
     * @throws \Exception
     */
    public function __construct($key)
    {
        $this->key = $key;
        $this->api = new YoutubeAPI(['key' => $key]);
    }

    /**
     * @return static
     * @throws \Exception
     */
    public static function yii()
    {
        return new static(\Yii::$app->params['google']['api_key']);
    }


    /**
     * @param $id
     * @param string $type
     * @return string
     */
    public static function videoThumbnail($id, $type = self::THUMBNAIL_TYPE_SMALL)
    {
        return "https://i.ytimg.com/vi/{$id}/{$type}.jpg";
    }

    /**
     * @param $url
     * @return string
     * @throws \Exception
     */
    public static function videoIdByUrl($url)
    {
        return YoutubeAPI::parseVIdFromURL($url);
    }

    /**
     * @param $id // iVWXd4yCcAQ
     * @param $language
     * @return mixed|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function videoCaptions($id, $language)
    {
        $content = str_replace('\u0026', '&', (new Client())->get("https://www.youtube.com/watch?v={$id}")->getBody()->getContents());
        if (!preg_match('/\{"baseUrl":"([^\"]+lang='.$language.')"/', $content, $matches)) {
            return null;
        }
        return @json_decode(json_encode(simplexml_load_string((new Client())->get($matches[1])->getBody()->getContents())),TRUE)['text'];
    }

    /**
     * @param $id
     * @param int $limit
     * @return array|array[]
     * @throws \Exception
     */
    public function videoComments($id, $limit = 100)
    {
        return array_map(function ($v) {
            return array_intersect_key($v['snippet']['topLevelComment']['snippet'], array_flip(['textOriginal', 'authorDisplayName', 'publishedAt', 'likeCount']));
        }, $this->requestPaginated('commentThreads', [
            'part' => 'snippet',
            'videoId' => $id,
            'order' => 'relevance',
            'maxResults' => 100,
        ], $limit));
    }

    /**
     * @param $id
     * @param $language
     * @return string
     */
    public function videoCaptionsMy($id, $language)
    {
        foreach (($this->request('captions', ['part' => 'snippet', 'videoId' => $id,])['items'] ?? []) as $item) {
            if ($item['snippet']['language'] != $language) {
                continue;
            }
            var_dump($item);
            $result = Token::fromArray(Settings::findOne(['name' => Settings::NAME_OAUTH_GOOGLE])->value)->google(['https://www.googleapis.com/auth/youtube.force-ssl'])
                ->authorize()->request('GET', '/youtube/v3/captions/'.$item['id'].'?key='.$this->key);
            return $result->getBody()->getContents();
        }
    }

    /**
     * @param $url
     * @return mixed
     * @throws \Exception
     */
    public function channelDataByUrl($url)
    {
        $params = ['part' => 'contentDetails'];
        if (preg_match('/youtube.com\/(\@[\w\-_]+)/', $url, $matches)) {
            $params['forHandle'] = $matches[1];
        } else if (preg_match('/youtube.com/user/(\w+)/', $url, $matches)) {
            $params['forUsername'] = $matches[1];
        } else {
            throw new \Exception("Not a valid youtube channel url");
        }
        return @$this->request('channels', $params)['items']['0'];
    }

    /**
     * @param $url
     * @return array|mixed
     * @throws \Exception
     */
    public function channelVideos($url)
    {
        set_time_limit(0);
        if (!$data = $this->channelDataByUrl($url)) {
            throw new \Exception("Channel not found");
        }
        return $this->requestPaginated('playlistItems', [
            'part' => 'snippet',
            'playlistId' => $data['contentDetails']['relatedPlaylists']['uploads'],
            'maxResults' => 50,
        ]);
    }

    /**
     * @param $q string
     * @param string $part
     * @param int $limit
     * @return mixed
     * @throws \Exception
     */
    public function videoSearch($q, $part = 'snippet', $limit = 50)
    {
        return $this->requestPaginated('search', ['q' => $q, 'part' => $part, 'type' => 'video', 'maxResults' => 50], $limit);
    }

    /**
     * @param $q string
     * @return mixed
     * @throws \Exception
     */
    public function videoSearchParsed($q)
    {
        $content = (new Client())->get("https://www.youtube.com/results?search_query={$q}")->getBody()->getContents();
        if (!preg_match_all('/\"videoRenderer\"\:\{\"videoId\"\:\"([^\"]+)\"/', $content, $matches)) {
            return null;
        }
        return $matches[1];
    }

    /**
     * @param $point
     * @param $params
     * @return array|mixed
     * @throws \Exception
     */
    private function requestPaginated($point, $params, $limit = 1000)
    {
        $result = $this->request($point, $params);
        $items = $result['items'] ?? $result;
        while (@$result['nextPageToken'] && ($limit > count($items))) {
            $params['pageToken'] = @$result['nextPageToken'];
            $result =  $this->request($point, $params);
            $items = array_merge($items, $result['items']);
        }
        return array_slice($items, 0, $limit);
    }

    /**
     * @param $point
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    private function request($point, $params)
    {
        return json_decode($this->api->api_get('https://www.googleapis.com/youtube/v3/'.$point, $params), true);
    }

}