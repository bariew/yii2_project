<?php


/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = NULL)
 *
 * @SuppressWarnings(PHPMD)
*/
class FunctionalTester extends \Codeception\Actor
{
    use _generated\FunctionalTesterActions;

    /**
     * Logs actor in.
     * @param null $username
     * @param int|null $password
     * @return mixed|null
     */
    public function login($username = null, $password = 123123)
    {
        $this->logout();
        $this->go('/');
        if (!$username) {
            list($username, $password) = array_values(\Yii::$app->params['auth']);
        }
        $this->testForm('#login-form', 'LoginForm', [
            "username" => $username,
            "password" => $password
        ]);
    }

    public function getUrl()
    {
        return str_replace('/'.basename(Yii::$app->request->scriptFile), '', $this->grabFromCurrentUrl());
    }

    public function logout()
    {
        $this->go('/user/logout');
    }

    public function go($url)
    {
        $this->amGoingTo("Visit " . (is_array($url) ? reset($url) : $url));
        $url = @parse_url($url)['host']
            ? $url
            : \Yii::$app->getUrlManager()->createUrl($url);
        return $this->amOnPage($url);
    }

    /**
     * Sends form post with wrong or/and correct form data
     * and checks whether response has form fields errors.
     * @param $selector
     * @param $name
     * @param $correctData
     * @param array $wrongData
     * @param string $errorSelector
     */
    public function testForm($selector, $name, $correctData, $wrongData = [], $errorSelector = '.has-error')
    {
        $this->testMultipleForm($selector, [$name => ['correct' => $correctData, 'wrong' => $wrongData]], $errorSelector);
    }

    /**
     * @param $selector
     * @param array $formData (['Account' => ['correct' => ['name' => 'asd'], 'wrong'=>['id'=>123]]])
     * @param string $errorSelector
     */
    public function testMultipleForm($selector, $formData, $errorSelector = '.has-error')
    {
        $allData = [];
        foreach ($formData as $name => $data) {
            $allData[$name] = array_merge((array) @$data['correct'], (array) @$data['wrong']);
        }
        $submitData = [];
        foreach ($allData as $name => $data) {
            foreach ($data as $key => $value) {
                $key = explode('[', $key);
                $key[0] = $key[0].']';
                $key = '['.implode('[', $key);
                $submitData["{$name}{$key}"] = $value;
            }
        }
        $this->submitForm($selector, $submitData);
        foreach ($allData as $name => $data) {
            foreach ($data as $key => $value) {
                $key = preg_replace('/.*\[.*/', '$1', $key);
                $selector =
                    $errorSelector
                    . strtolower(".field-"
                        . str_replace(['[', ']'],['-', ''], $name)
                        . "-{$key}"
                    );
                if (isset($formData[$name]['wrong'][$key])) {
                    $this->expectTo("see error message for $key with value $value");
                    $this->seeElement($selector);
                } else {
                    $this->expectTo("NOT to see error message for $key with value $value");
                    $this->dontSeeElement($selector);
                }
            }
        }
    }

   /**
    * Define custom actions here
    */
}
