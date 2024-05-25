<?php
/**
 * Created by PhpStorm.
 * User: sajib
 * Date: 6/15/2015
 * Time: 3:01 AM
 */
namespace app\components;


use app\models\Backup;
use Ifsnop\Mysqldump\Mysqldump;
use Yii;

class DBUtility
{

    public static function getConnection()
    {
        $db = Yii::$app->db;
        $name = explode(';', $db->dsn);
        self::$db = str_replace('dbname=','', $name[1]);
        self::$user = $db->username;
        self::$pass = $db->password;
    }


    public static function exportData($note)
    {

        self::getConnection();


        $output = null;
        $return_var = null;

        $user = self::$user;
        $pass = self::$pass;
        $db = self::$db;
        $name = $db."_DATA_".date('d-m-Y-H-i-s').'.sql';

        echo PHP_EOL."Start exporting db data....".PHP_EOL;
        $file = Yii::getAlias('@app').'/web/db/'.$name;
        $command = "mysqldump -u {$user} -p{$pass} --no-create-db --no-create-info {$db}  >  {$file}";

        exec($command, $output, $return_var);
        
        if($return_var){
            echo "Getting something wrong..".$return_var.PHP_EOL;
            return false;
        }else{

            $model = new Backup();
            $model->name = $name;
            $model->size = CommonUtility::formatSizeUnits(filesize($file));
            $model->note = $note.'-Data';
            $model->status = Backup::STATUS_EXPORT;
            $model->date = date('Y-m-d H:i:s');
            if(!$model->save()){
                print_r($model->getErrors());
                return false;
            }

            echo "successfully exported.".PHP_EOL;
            return true;
        }

    }

    public static function export($note){

        echo PHP_EOL."Start exporting db data....".PHP_EOL;

        $db = Yii::$app->db;
        $username = $db->username;
        $password = $db->username;
        $dsn = explode(';', $db->dsn);
        $database = str_replace('dbname=','', $dsn[1]);
        $host = str_replace('mysql:host=','', $dsn[0]);
        $hour = DateTimeUtility::getDate('NOW', 'H');
        $name = "{$database}_{$hour}.sql";
        $file = Yii::getAlias("@app/web/db/$name");

        try {

            $dump = new Mysqldump("mysql:host={$host};dbname=$database", $username, $password);
            $dump->start($file);

//            $model = new Backup();
//            $model->name = $name;
//            $model->size = CommonUtility::formatSizeUnits(filesize($file));
//            $model->note = $note.'-Data';
//            $model->status = Backup::STATUS_EXPORT;
//            $model->date = date('Y-m-d H:i:s');
//            if(!$model->save()){
//                print_r($model->getErrors());
//                return false;
//            }


        }catch (\Exception $e){
            dd($e);
        }
    }


}