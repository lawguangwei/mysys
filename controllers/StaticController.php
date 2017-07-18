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
use app\models\FaultList;
use app\models\Faults;
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
        echo date('Y-m-d H:i:s');
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
        $filePath='uploads/20170718list.xlsx';
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
        //$allColumn=$currentSheet->getHighestColumn();
        $allRow=$currentSheet->getHighestRow();

        $faultList = new FaultList();
        $faultList->fault_list_id=md5($PHPExcel->getID());
        $faultList->fault_list_name=$currentSheet->getTitle();
        $faultList->create_date=date('Y-m-d H:i:s');
        $faultList->owner='system';
        $faultList->state='normal';
        $faultList->info='test';
        if($faultList->save()){
            set_time_limit(0);
            for($rowIndex=2;$rowIndex<=$allRow;$rowIndex++){
                $fault=new Faults();
                $fault->fault_list_id=$faultList->fault_list_id;
                $fault->fault_code=$currentSheet->getCell('A'.$rowIndex);
                $fault->fault_title=$currentSheet->getCell('B'.$rowIndex);
                $fault->fault_type=$currentSheet->getCell('C'.$rowIndex);
                $fault->fault_content=$currentSheet->getCell('D'.$rowIndex);
                $fault->fault_state=$currentSheet->getCell('E'.$rowIndex);
                $fault->fault_reason=$currentSheet->getCell('F'.$rowIndex);
                $fault->create_date=$currentSheet->getCell('G'.$rowIndex);
                $fault->repair_date=$currentSheet->getCell('H'.$rowIndex);
                $fault->start_date=$currentSheet->getCell('I'.$rowIndex);
                $fault->end_date=$currentSheet->getCell('J'.$rowIndex);
                $fault->save();
            }
        }
        /*
        for($rowIndex=2;$rowIndex<=$allRow;$rowIndex++){

            for($colIndex='A';$colIndex<=$allColumn;$colIndex++){
                $addr = $colIndex.$rowIndex;
                echo $currentSheet->getCell($addr)->getValue(). ' ';
            }
            echo '<br/>';
        }
        */
    }
}
