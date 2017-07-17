<?php
/**
 * Created by PhpStorm.
 * User: luogw
 * Date: 2017-07-17
 * Time: 16:40
 */
namespace app\models;

use yii\base\Model;
use yii\web\UploadedFile;

class UploadForm extends Model{
    public $file;

    public function rules(){
        return[
            [['file'],'file']
        ];
    }
}