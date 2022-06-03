<?php
/**
 * ClientUrlTest class file.
 */
namespace app\tests\acceptance;

use app\modules\user\models\User;
use PHPUnit\Framework\TestResult;
use Yii;
use \bariew\docTest\ClickTest;
use yii\helpers\FileHelper;

/**
 * Example for ClickTest usage.
 *
 * Usage: it is for running with yii2 command "vendor/bin/codecept run acceptance"
 *
 */
class ClientUrlTest extends \Codeception\Test\Unit
{
    protected $skip = ['/logout', '/delete', 'csv', 'pdf', 'Pdf'];
    /**
     * Clicks all app links.
     */
    public function testLinks()
    {
        $clickTest = $this->getClickTest();
        $clickTest->pageCallback = null; // uncomment to generate html files
        $clickTest->baseUrl = 'http://localhost:8081';
        $clickTest->request(
            '/user/default/logout' // first doing logout.
        )->login('/user/default/login', array( // and login.
            'Login[email]' => Yii::$app->params['auth']['username'],
            'Login[password]' => Yii::$app->params['auth']['password'],
            // click all site links recursively starting from root '/' url.
        ));
            //->login('/user/login-step-two?key=test&remember=1', ['FormLoginStepTwo[code]' => 123]);
        $clickTest->clickAllLinks('/');
        $clickTest->request('/user/default/logout');
        $clickTest->baseUrl = 'http://localhost:8081';
        $clickTest->clickAllLinks('/');
        $clickTest->request('/');
        $this->addToAssertionCount(count($clickTest->visited));
        $this->assertTrue(!$clickTest->errors, 'Errors: '.var_export($clickTest->errors, true));
        //$this->assertTrue(false, 'Urls: '.var_export($clickTest->visited, true));
    }

    /**
     * Creates click test instance.
     * @return ClickTest click test instance.
     */
    public function getClickTest()
    {
        // init clicktest with required base url param.
        $cookieFile = '/tmp/projecteamTest';
        @unlink($cookieFile);
        $clickTest = new \bariew\docTest\ClickTest('http://localhost:8081', [
           // 'formOptions' => ['values' => ['/\[code\]$/' => "123"]], // adding form sending
            'groupUrls' => false, // exclude urls with only different GET params
            'filterUrlCallback' => [$this, 'skipUrl'],
            'curlOptions' => ['cookieFile' => $cookieFile],
            'pageCallback' => function ($url, $content) {
                if (!isset(parse_url($url)['path'])) {
                    return;
                }
                static::generateHtml($url, $content);
            }
        ]);
        $clickTest->selector = 'a:not([href=""])'; // phpQuery selector for searching urls on pages.
        return $clickTest;
    }

    private $visitedPaths = [];
    public function skipUrl($url)
    {
        foreach ($this->skip as $deny) {
            if (is_numeric(strpos($url, $deny))) {
                return true;
            }
        }
        $url = @parse_url($url)['path'];
        $url = preg_replace('/^(.*)?(\/\d+)$/', '$1', $url);
        if (preg_match('/\./', basename($url))) {
            return true;
        }
        if (isset($this->visitedPaths[$url])) {
            return true;
        }
        $this->visitedPaths[$url] = true;
        return false;
    }

    /**
     * Generates html files (with url-like folders structure) while crawling the site
     * @param $url
     * @param $content
     * @throws \yii\base\Exception
     */
    private static function generateHtml($url, $content)
    {
        $path = Yii::getAlias('@app/web/html' . @parse_url($url)['path']);
        //$path = basename(@parse_url($url)['path']) ? $path : $path . 'index';
        $depth = count(explode('/', @parse_url($url)['path']))-1;
        $content = str_replace(
            ['link href="/', 'script src="/', 'src="/'],
            ['link href="'.str_repeat('../', $depth), 'script src="'.str_repeat('../', $depth), 'src="'.str_repeat('../', $depth)],
            $content
        );
        $content = preg_replace('#href="/([\w\d\/-]+)#', 'href="'.str_repeat('../', ($depth ? $depth-1 : 0))
            .'$1.html', $content);
        FileHelper::createDirectory(dirname($path));
        if (!$start = strpos($content, '<!DOCTYPE')) {
            return;
        }
        try {
            file_put_contents($path.'.html', substr($content, $start));
        } catch (\Exception $e) {}
    }
}
