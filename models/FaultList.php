<?php
/**
 * Created by PhpStorm.
 * User: luogw
 * Date: 2017-07-18
 * Time: 9:46
 */
use yii\db\ActiveRecord;

class FaultList extends ActiveRecord{
    private $fault_list_id;
    private $fault_list_name;
    private $create_date;
    private $owner;
    private $state;
    private $info;

    public static function tableName(){
        return 'fault_list';
    }

}