<?php
/**
 * Created by PhpStorm.
 * User: luogw
 * Date: 2017-07-18
 * Time: 9:32
 */
namespace  app\models;

use yii\db\ActiveRecord;

class Faults extends ActiveRecord{
    private $fault_list_id;
    private $fault_code;
    private $fault_title;
    private $fault_type;
    private $fault_content;
    private $fault_state;
    private $fault_reason;
    private $create_date;
    private $repair_date;
    private $start_date;
    private $end_date;

    public static function tableName(){
        return 'faults';
    }
}

