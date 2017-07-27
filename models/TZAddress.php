<?php
/**
 * Created by PhpStorm.
 * User: luogw
 * Date: 2017-07-18
 * Time: 9:32
 */
namespace  app\models;

use yii\db\ActiveRecord;

class TZAddress extends ActiveRecord{
    private $tz_name;
    private $tz_address;

    public static function tableName(){
        return 'tz_address';
    }
}

