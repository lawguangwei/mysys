<?php
/**
 * Created by PhpStorm.
 * User: luogw
 * Date: 2017-07-18
 * Time: 9:32
 */
namespace  app\models;

use yii\db\ActiveRecord;

class FaultRst extends ActiveRecord{
    private $fault_id;
    private $fault_list_id;
    private $fault_code;
    private $fault_state;
    private $create_date;
    private $repair_date;
    private $start_date;
    private $end_date;
    private $base_name;
    private $base_area;
    private $tz_address;
    private $fault_times;

    public static function tableName(){
        return 'fault_result';
    }
}

