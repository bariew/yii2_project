<?php
/**
 * Youtube class file
 */

namespace app\modules\common\components\google;

use app\modules\user\models\Auth;
use GuzzleHttp\Client;
use Madcoda\Youtube\Youtube as YoutubeAPI;
use YoutubeDl\Options;
use YoutubeDl\YoutubeDl;

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
     * @param bool $raw
     * @return mixed|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function videoCaptions($id, $language, $raw = true)
    {
        $content = str_replace('\u0026', '&', (new Client())->get("https://www.youtube.com/watch?v={$id}")->getBody()->getContents());
        if (!preg_match('/\{"baseUrl":"([^\"]+lang='.$language.')"/', $content, $matches)) {
            return null;
        }
        $content = simplexml_load_string((new Client())->get($matches[1])->getBody()->getContents());
        $result = [];
        foreach ($content->children() as $v) {
            $data = json_decode(json_encode($v), true);
            $result[] = array_merge($data['@attributes'], ['text' => $data[0]]);
        }
        return $raw ? implode("\n", array_column($result, 'text')) : $result;
    }

    /**
     * @param $id
     * @param $path
     * @return
     */
    public function videoDownload($id, $path, $user, $password, $cookies)
    {
        $yt = new YoutubeDl();
        $yt->setBinPath('/usr/bin/yt-dlp');
        return static::exec("/usr/bin/yt-dlp -f136+140 https://www.youtube.com/watch?v={$id} -u{$user} -p{$password} --cookies {$cookies} -P {$path}");
    }

//    /**
//     * @param $id
//     * @param $path
//     * @return \YoutubeDl\Entity\VideoCollection
//     */
//    public function videoDownload($id, $path, $options = [])
//    {
//        $yt = new YoutubeDl();
//        $yt->setBinPath('/usr/bin/yt-dlp');
//        return $yt->download(Options::create()->authenticate(@$options['username'], @$options['password'])->cookies(@$options['cookies'])
//            ->format(Options::MERGE_OUTPUT_FORMAT_MP4)
//            ->audioFormat(Options::AUDIO_FORMAT_M4A)
//            ->downloadPath($path)->url('https://www.youtube.com/watch?v='.$id));
//    }

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
        foreach (($this->request('captions.txt', ['part' => 'snippet', 'videoId' => $id,])['items'] ?? []) as $item) {
            if ($item['snippet']['language'] != $language) {
                continue;
            }
            var_dump($item);
            $result = Auth::token()->google(['https://www.googleapis.com/auth/youtube.force-ssl'])
                ->authorize()->request('GET', '/youtube/v3/captions.txt/'.$item['id'].'?key='.$this->key);
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

    public function channelPlaylists($id)
    {
        $data = array_map(function ($v) {
            return array_merge(['id' => $v['id']], $v['snippet']);
        }, $this->request('playlists', ['part' => 'snippet', 'channelId' => $id])['items']);
        return array_combine(array_column($data, 'title'), $data);
    }

    /**
     * @param $url
     * @param string $playlist
     * @return array|mixed    ['kind' => 'youtube#playlistItem', 'etag' => 'BXhlXNGHKyAdtaUs27whWRjen2c', 'id' => 'UExrcWNJcE1lRHlWMGVoYjdLZEFtdEVwZ2k0RVZkdkF2ZS40QTA3NTU2RkM1QzlCMzYx',
        'snippet' => ['publishedAt' => '2023-09-01T07:39:22Z', 'channelId' => 'UCUTTx5ART70fUXmUUiwlRRA','title' => 'Остров, на котором есть дом (Герои 3)', 'description' => 'Оригинал называется так же)  #Герои3',
            'thumbnails' => [
                'default' => ['url' => 'https://i.ytimg.com/vi/mhpX5Y0vuo8/default.jpg','width' => 120,'height' => 90,],
                'medium' => ['url' => 'https://i.ytimg.com/vi/mhpX5Y0vuo8/mqdefault.jpg','width' => 320,'height' => 180,],
                'high' => ['url' => 'https://i.ytimg.com/vi/mhpX5Y0vuo8/hqdefault.jpg','width' => 480,'height' => 360,],
                'standard' => ['url' => 'https://i.ytimg.com/vi/mhpX5Y0vuo8/sddefault.jpg', 'width' => 640, 'height' => 480,],
                'maxres' => ['url' => 'https://i.ytimg.com/vi/mhpX5Y0vuo8/maxresdefault.jpg','width' => 1280,'height' => 720,],
            ],
            'channelTitle' => 'Pavel T','playlistId' => 'PLkqcIpMeDyV0ehb7KdAmtEpgi4EVdvAve','position' => 26,
            'resourceId' => ['kind' => 'youtube#video', 'videoId' => 'mhpX5Y0vuo8',],
            'videoOwnerChannelTitle' => 'Gangena', 'videoOwnerChannelId' => 'UCe4TR_FzNPj8HtZpcCOlzKQ',
        ],
    ]
     * @throws \Exception
     */
    public function playlistVideos($url, $playlist = 'uploads')
    {
        set_time_limit(0);
        if (!$data = $this->channelDataByUrl($url)) {
            throw new \Exception("Channel not found");
        }
        $playlistId = ($playlist == 'uploads')
            ? $data['contentDetails']['relatedPlaylists'][$playlist]
            : $this->channelPlaylists($data['id'])[$playlist]['id'];
        return $this->requestPaginated('playlistItems', [
            'part' => 'snippet',
            'playlistId' => $playlistId,
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
     * @param $string
     * @return int|null
     */
    private static function exec($string)
    {
        $handle = proc_open($string, [STDIN, STDOUT, STDERR], $pipes);
        $output = null;
        if (is_resource($handle)) {
            $output = proc_close($handle);
        }
        return $output;
    }

    /**
     * @param $point
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    public function request($point, $params)
    {
        return json_decode($this->api->api_get('https://www.googleapis.com/youtube/v3/'.$point, $params), true);
    }

}