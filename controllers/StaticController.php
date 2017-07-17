<?php

namespace app\controllers;

use app\models\UploadForm;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use yii\web\UploadedFile;
require dirname(dirname(__FILE__)).'/components/phpexcel/PHPExcel.php';

class StaticController extends Controller
{
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     *
     */
    public function actionUpload(){
        $model=new UploadForm();
        if(Yii::$app->request->isPost){
            $model->file=UploadedFile::getInstance($model,'file');
            if($model->file && $model->validate()){
                $model->file->saveAs('uploads/'.$model->file->baseName.'.'.$model->file->extension);
            }
        }
        return $this->render('upload',['model'=>$model]);
    }

    public function actionReadExcel(){
        $filePath='uploads/IPTV.xlsx';
        if(empty($filePath) or !file_exists($filePath)){
            die('file not exists');
        }
        $PHPReader = new \PHPExcel_Reader_Excel2007();
        if(!$PHPReader->canRead($filePath)){
            $PHPReader=new \PHPExcel_Reader_Excel5();
            if(!$PHPReader->canRead($filePath)){
                echo 'no excel';
                return;
            }
        }
        $PHPExcel=$PHPReader->load($filePath);
        $currentSheet=$PHPExcel->getSheet(0);
        $allColumn=$currentSheet->getHighestColumn();
        $allRow=$currentSheet->getHighestRow();

        for($rowIndex=1;$rowIndex<=$allRow;$rowIndex++){
            for($colIndex='A';$colIndex<=$allColumn;$colIndex++){
                $addr = $colIndex.$rowIndex;
                echo $currentSheet->getCell($addr)->getValue(). ' ';
            }
            echo '<br/>';
        }
    }
}
