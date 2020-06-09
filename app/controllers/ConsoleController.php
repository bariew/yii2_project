<?php
/**
 * ConsoleController class file.
 */


namespace app\controllers;


use app\modules\ad\models\Item;
use app\modules\user\models\User;
use Google\Cloud\BigQuery\Dataset;
use Google\Cloud\BigQuery\Table;
use Google\Cloud\BigQuery\Timestamp;
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
     * @example ./yii console/module-clone @app/modules/post @app/modules/news
     * @param $oldAlias
     * @param $newAlias
     * @return bool
     * @throws ErrorException
     */
    public function actionModuleClone($oldAlias, $newAlias)
    {
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
}