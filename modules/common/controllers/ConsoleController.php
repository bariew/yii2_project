<?php
/**
 * ConsoleController class file.
 */


namespace app\modules\common\controllers;



use app\modules\common\components\google\Gemini;

use Yii;
use yii\base\ErrorException;
use yii\console\Controller;
use yii\helpers\FileHelper;

/**
 * Description:
 */
class ConsoleController extends Controller
{
    /**
     *
     */
    public function actionCron()
    {
        $date = new \DateTime('now', new \DateTimeZone('UTC'));
        $hour = $date->format('H');
        $minute = $date->format('i');
        switch ($minute) {
            case 0: // every hour at hour:00
                switch ($hour) {
                    case 6: // every day at 6:00AM
                        break;
                }
        }
    }

    /**
     * Creates db backup on deploy
     */
    public function actionBackup()
    {
        $user = \Yii::$app->db->username;
        $pass = \Yii::$app->db->password;
        $db = preg_replace('/^.*\=(\w+)$/', '$1', \Yii::$app->db->dsn);
        $name = date('Y-m-d_H-i').".sql";
        $tables = implode(" ", array_filter(\Yii::$app->db->schema->tableNames, function($v){
            return !preg_match('/^data_.*$/', $v);
        }));
        exec("mysqldump -u$user -p$pass $db $tables > " . \Yii::getAlias("@app/runtime/$name"));
    }

    /**
     * Fast clone module content into a new one with the new namespaces replaced
     * @example ./yii console/module-clone news
     * @param $oldAlias
     * @param $newAlias
     * @return bool
     * @throws ErrorException
     */
    public function actionModuleClone($newAlias = 'new', $oldAlias = '@app/modules/post')
    {
        $newAlias = "@app/modules/{$newAlias}";
        $source = Yii::getAlias($oldAlias);
        if (!file_exists($source) || !is_dir($source)) {
            throw new ErrorException("Source directory {$oldAlias} not found");
        }
        $destination = Yii::getAlias($newAlias);
        FileHelper::copyDirectory($source, $destination, [
            'afterCopy' => function ($from, $to) use ($oldAlias, $newAlias) {
                if (!is_file($to)) {
                    return true;
                }
                $oldNamespace = str_replace(['@', '/'], ['', '\\'], $oldAlias);
                $newNamespace = str_replace(['@', '/'], ['', '\\'], $newAlias);
                $oldModule = basename($oldAlias);
                $newModule = basename($newAlias);
                file_put_contents($to, str_replace(
                    [$oldNamespace, "/{$oldModule}/", "{$oldModule}_", "modules/{$oldModule}"],
                    [$newNamespace, "/{$newModule}/", "{$newModule}_", "modules/{$newModule}"],
                    file_get_contents($from)
                ));
                rename($to, str_replace("_{$oldModule}_", "_{$newModule}_", $to)); //rename migration files
                return true;
            }
        ]);
        return true;
    }

    /**
     * Connects to app database
     * @return int|null
     */
    public function actionDb()
    {
        preg_match('/host\=([^;]+);dbname=(.*)$/', \Yii::$app->db->dsn, $m);
        return static::runProgram('mysql', '-u'.\Yii::$app->db->username . ' -p'.\Yii::$app->db->password . ' -h'.$m[1] .' ' . $m[2]);
    }

    /**
     * Connects to app database
     * @return int|null
     */
    public function actionDbDump()
    {
        $dsn = explode('=', \Yii::$app->db->dsn);
        return static::runProgram('mysqldump --no-tablespaces', '-u'.\Yii::$app->db->username . ' -p'.\Yii::$app->db->password . ' ' . end($dsn) . ' >> dump.sql');
    }

    /**
     * @param $path
     * @return int|null
     */
    private static function stopScript($path)
    {
        return static::runProgram("kill $(ps aux | grep '{$path}' | awk '{print $2}')");
    }

    /**
     * run program in interactive mode
     *
     * @param $name string name of program
     * @param $stringParams string program parameters
     * @return int|null
     */
    private static function runProgram($name, $stringParams = '')
    {
        $handle = proc_open($name .' '. $stringParams, [STDIN, STDOUT, STDERR], $pipes);
        $output = null;
        if (is_resource($handle)) {
            $output = proc_close($handle);
        }
        return $output;
    }

    /**
     * @param string $dir
     */
    public function actionJsx2js($dir = 'app/modules')
    {
        $dir = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . $dir;
        $files = FileHelper::findFiles($dir, ['only' => ['*.jsx']]);
        foreach ($files as $file) {
            exec("./node_modules/.bin/babel --plugins transform-react-jsx {$file} > " . preg_replace('#^(.+)x$#', '$1', $file));
        }
    }

    public function actionTmp()
    {
        var_dump(Gemini::yii()->request('', "Куда пойти в Ижевске (согласно этим новостям): ". json_encode(
            [
                "1. Новые специальности и целевое обучение: что в Ижевске изменилось для абитуриентов в 2024 году",
               "2. Два человека пострадали в столкновении BMW и такси в Ижевске",
               "3. Процесс по делу об обманутых туристах из Удмуртии пройдет в Первомайском суде Ижевска",
                "4. Пять пенсионеров из Ижевска лишились 1 млн рублей",
               "5. Движение трамваев в городок Металлургов временно закроют в Ижевске - udmurt.media",
               "6. Новую награду для многодетных семей учредили в Ижевске - udmurt.media",
               "7. В Ижевске учредили специальный знак «Семейная доблесть»",
               "8. В Ижевске задержали замначальника Управтодора Удмуртии по подозрению в превышении полномочий",
               "9. Ещё один заместитель появится у главы Ижевска",
               "10. Замначальника «Управтодора» задержали в Ижевске по делу о превышении полномочий на 11 млн рублей",
               "11. Еще один заместитель появится у Главы Ижевска",
               "12. Жительницу Ижевска осудили за гибель бывшей подруги сожителя",
               "13. В Ижевском аэропорту силовики под аплодисменты задержали подозреваемого",
               "14. Александр Бречалов и Игорь Маковский подписали соглашение о создании в Удмуртии ситуационно-аналитического центра - Лента новостей Ижевска",
               "15. В аэропорту Ижевска мужчину задержали под аплодисменты встречающих - ГТРК Удмуртия",
               "16. 21 июня в Ижевске отметят международный день йоги",
               "17. Фотофакт: обезглавленных куриц нашли у детской школы искусств в Ижевске",
               "18. Проезд по улице Майской в Ижевске у школы №88 перекроют на четыре дня",
               "19. Мужчину жестко задержали в аэропорту Ижевска под аплодисменты встречающих",
               "20. Сквозной проезд по улице Майской закроют в Ижевске",
            ]
        )));

    }
}