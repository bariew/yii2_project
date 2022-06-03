<?php
/**
 * HtmlPurifierValidator class file.
 */


namespace app\modules\common\components\validators;


use yii\helpers\HtmlPurifier;
use yii\validators\Validator;

/**
 * Validates model attribute for having html code
 *
 * Doesn't change the value, just makes sure there is no
 * non-allowed HTML
 */
class HtmlPurifierValidator extends Validator
{
    /**
     * @var array like ['HTML.AllowedElements' => ['a', 'p']]
     * @see http://htmlpurifier.org/live/configdoc/plain.html
     */
    public $config = [];

    const WYSIWYG_CONFIG = ['HTML.AllowedElements' => ['a', 'p', 'div', 'br', 'i', 'b', 'h1', 'h2', 'h3', 'h4', 'h5',
        'pre', 'blockquote', 'strong', 'em', 'del', 'ul', 'ol', 'li', 'hr',
    ]];

    /**
     * @inheritDoc
     */
    public function init()
    {
        $this->message = $this->message ?: \Yii::t('common', 'HTML supplied includes dangerous or broken elements.  We have cleaned up the HTML, please check that the content is still correct and try saving again.');
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function validateAttribute($model, $attribute)
    {
        if ($this->skipOnEmpty && !$model->$attribute) {
            return;
        }
        $result = (array) $model->$attribute;
        $isArray = is_array($model->$attribute);
        $hasError = false;
        foreach ($result as $key => $item) {
            $original =  $item;
            $purified = HtmlPurifier::process($original, function (\HTMLPurifier_Config $config) {
                $this->config['Attr.DefaultImageAlt'] = ''; // remove automatically created link alt
                $this->config['URI.AllowedSchemes'] = ['data' => true, 'http' => true, 'https' => true, 'mailto' => true]; // allow link href and image data src
                $this->config['Attr.AllowedFrameTargets'] = ['_blank'];
                $config->loadArray($this->config);
                $config->getHTMLDefinition(true)->info_global_attr['data-filename'] = new \HTMLPurifier_AttrDef_Text;
            });
            $replace = [
                [ '<br>', '&gt;', '>', ' ', 'alt=""', '/>', "\r", "\n", "\t", 'rel="noreferrernoopener"', '&amp;'],
                [ '<br />', '', '', '', '', '>', '', '', '', '', '&']]
            ;
            // compare original and purified, error if the purifier found something to do
            if (str_replace($replace[0], $replace[1], $original) != str_replace($replace[0], $replace[1], $purified)) {
                $hasError = true;
                $result[$key] = $purified;
            }
        }
        if ($hasError) {
            $this->addError($model, $attribute, $this->message);
            $model->$attribute = $isArray ? $result : reset($result);
        }
    }
}
