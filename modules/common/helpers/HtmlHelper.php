<?php
/**
 * HtmlHelper class file
 */


namespace app\modules\common\helpers;

use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\ActiveFormAsset;

/**
 * Class HtmlHelper
 * @package app\modules\common\helpers
 */
class HtmlHelper
{
    const MONEY_OPTIONS = ['maskedInputOptions' => ['allowMinus' => false, 'rightAlign' => false]];

    /**
     * @return bool
     */
    public static function isMobile()
    {
        $useragent = strtolower(@$_SERVER['HTTP_USER_AGENT']);
        return preg_match("/(phone|iphone|itouch|ipod|symbian|android|htc|htc-|palmos|blackberry|opera mini|iemobile|windows ce|nokia|fennec|hiptop|kindle|mot |mot-|webos\/|samsung|sonyericsson|^sie-|nintendo)/", $useragent )
            || preg_match("/(mobile|pda;|avantgo|eudoraweb|minimo|netfront|brew|teleca|lg;|lge |wap;| wap )/", $useragent );
    }


    /**
     * @return array
     */
    public static function imperaviWysiwygOptions()
    {
        return ['settings' => [
            'styles' => false,
            'direction' => (static::isRtl() ? 'rtl' : 'ltr'),
            'paragraphize' => false, 'replaceDivs' => false, 'linebreaks' => true
        ]];
    }

    /**
     * An Eye image for showing the Password input value
     * @param bool $opened
     * @return string
     */
    public static function eye($opened = false)
    {
        $class = $opened ? 'fa-eye-slash' : '';
        return <<<HTML
<div onclick="$(this).toggleClass('fa-eye-slash').prev().focus()
    .attr('type', ($(this).hasClass('fa-eye-slash') ? 'text' : 'password'))" 
            class="passwordEye fa fa-eye {$class}"></div>
HTML;
    }

    /**
     * @return string[]
     */
    public static function emojis()
    {
        return [
            '😁', '😂', '😃', '😄', '😅', '😆', '😉', '😊', '😋', '😌', '😍', '😏', '😒', '😓', '😔', '😖', '😘',
            '😚', '😜', '😝', '😞', '😠', '😡', '😢', '😣', '😥', '😨', '😩', '😪', '😫', '😭', '😰', '😱', '😲',
            '😳', '😵', '😷'
        ];
    }

    /**
     * Progress bar
     * @param $value
     * @param null $text
     * @param int $width
     * @return string
     */
    public static function progress($value, $text = null, $options = [])
    {
        $text = $text ?? \Yii::$app->formatter->asPercent($value);
        Html::addCssClass($options, 'progress rounded-40 mt-2 mt-sm-0');
        Html::addCssStyle($options, 'background-color: #B3BAC1');
        return Html::tag('div', <<<HTML
<div class="progress-bar rounded-40" role="progressbar" style="width: {$value}%;overflow: unset;background-color: #60A910" aria-valuenow="{$value}" aria-valuemin="0" aria-valuemax="100">&emsp;{$text}</div>
HTML
        , $options);
    }

    /**
     * Hides too long text with a "More" link
     * @param $text
     * @param int $length
     * @return string
     */
    public static function less($text, $length = 100)
    {
        $result = mb_substr($text, 0, $length);
        $spacePos = mb_strrpos($result, ' ') ? : $length;
        $result = mb_substr($result, 0, $spacePos-1);
        if (mb_strlen($result) < mb_strlen($text)) {
            $result .=
                Html::tag('span', mb_substr($text, $spacePos), ['style' => 'display:none'])
                . ' ' . Html::a(\Yii::t('misc', 'more'), '#', [
                    'data-text1' => \Yii::t('misc', 'more'),
                    'data-text2' => \Yii::t('misc', 'less'),
                    'onclick' => <<<JS
$(this).prev().toggle(); 
$(this).text($(this).text() == $(this).data('text1') ? $(this).data('text2') : $(this).data('text1')); 
return false;
JS
                ]);
        }
        return $result;
    }

    /**
     * @param $lang
     * @return bool
     */
    public static function isRtl($lang = null)
    {
        $lang = $lang ?? \Yii::$app->language;
        return in_array($lang, ['he', 'ar']);
    }

    /**
     * @param $lang
     * @return mixed|string
     */
    public static function clearLang($lang)
    {
        foreach(['-', '_'] as $ch) {
            if (stripos($lang, $ch) !== false) {
                $parts = explode($ch, $lang);
                $lang = $parts[0];
            }
        }
        return isset(\Yii::$app->params['languages'][$lang]) ? $lang : \Yii::$app->language;
    }

    /**
     * @param $url
     * @return mixed|string|null
     */
    public static function subdomain($url)
    {
        $url = parse_url($url)['host'] ?? parse_url($url)['path'];
        $host = explode('.', preg_replace('/^(www\.)?([\w\.-]+)(:\w+)?$/', '$2',$url));
        return count($host) > 2 || @$host[1] == 'localhost' ? $host[0] : null;
    }

    /**
     * @param string $icon
     * @param string[] $url
     * @param array $options
     * @return string
     */
    public static function button($icon = 'plus', $url = ['create'], $options = [])
    {
        return Html::a('<i class="glyphicon glyphicon-'.$icon.'"></i>', $url, array_merge(
            ['class' => 'btn btn-default shadow-sm float-right rounded-circle', 'data-toggle' => "ajax-modal"],
            $options
        ));
    }
}