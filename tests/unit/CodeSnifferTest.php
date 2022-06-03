<?php
/**
 * CodeSnifferTest class file.
 */

/**
 * Searches for unwanted code appearances through the all app php files using regular expressions
 */
class CodeSnifferTest extends  \Codeception\Test\Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;

    /**
     */
    public function testCode()
    {
        $date = date('Y-m-d');
        $errors = [];
        $expressions = [
            '#TODO\!#', // uppercase TODO with ! means "do not push in prod!"
        ];
        $files = \yii\helpers\FileHelper::findFiles(Yii::getAlias('@app/modules'), ['only' => ['*.php']]);
        foreach ($files as $file) {
            $content = file_get_contents($file);
            foreach ($expressions as $regex) {
                if (preg_match($regex, $content)) {
                    $errors[] = "You have ".str_replace(['\\','#'],['',''],$regex)." in {$file}";
                }
            }
            if (preg_match_all('#todo (\d{4}.\d{2}.\d{2})#', $content, $matches)) {
                foreach ($matches[1] as $match) {
                    if ($match <= $date) {
                        $errors[$match] = "You have outdated todo for {$match} in {$file}";
                    }
                }
            }
        }

        $this->assertTrue(!$errors, "\n" . implode("\n", $errors)."\n");
    }
}
