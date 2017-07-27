<?php

namespace app\controllers;

use app\models\TZAddress;
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
        $this->layout='statics';

        if(Yii::$app->request->isPost){
            if($_FILES['file']['error']>0){
                //echo "Return Code: " . $_FILES["file"]["error"] . "<br />";
                return $this->render('upload',['info'=>'请选择Excel文件。']);
            }else{
                $filePath=$_FILES["file"]["tmp_name"];
                $listName=$_POST['list_name'];
                if(FaultList::find()->where(['fault_list_name' => $listName])->one()!==null){
                    //echo '清单名已存在';
                    return $this->render('upload',['info'=>'清单名已存在。']);
                }
                if($this->createList($filePath,$listName)=='success'){
                    //echo '数据插入成功';
                    return $this->render('upload',['info'=>'数据插入成功。']);
                }else{
                    return $this->render('upload',['info'=>'数据插入失败，请重新插入。']);
                }
            }
        }
        /*
        $model=new UploadForm();
        if(Yii::$app->request->isPost){
            $model->file=UploadedFile::getInstance($model,'file');
            if($model->file && $model->validate()){
                $model->file->saveAs('uploads/'.$model->file->baseName.'.'.$model->file->extension);
            }
        }
        return $this->render('upload',['model'=>$model]);
        */
        return $this->render('upload');
    }


    public function actionGetLists(){
        $this->layout='statics';
        $lists=FaultList::find()->orderBy('create_date')->asArray()->all();
        return $this->render('fault_lists',['lists'=>$lists]);
    }

    public function actionGetDetail(){
        $this->layout='statics';
        $fault_list_id=$_GET['fault_list_id'];
        $lists=Faults::find()->where(['fault_list_id'=>$fault_list_id])->limit(20)->asArray()->all();
        return $this->render('list_detail',['lists'=>$lists]);
    }

    public function actionExecute(){
        $fault_list_id=$_GET['fault_list_id'];
        $faults = new Faults();
        $datas = $faults->find()->where(['fault_list_id'=>$fault_list_id])->asArray()->orderBy('create_date')->all();
        for($i=0;$i<count($datas);$i++){
            //判断网络类型
            if(strpos($datas[$i]['fault_type'],'3G')!==false){
                $datas[$i]['net_type']='3G网络';
                //判断设备厂家
                $datas[$i]['factory']='中兴';
            }
            if(strpos($datas[$i]['fault_type'],'4G')!==false){
                $datas[$i]['net_type']='4G网络';
                //判断设备厂家
                if(strpos($datas[$i]['fault_title'],'网元连接中断')!==false ||
                    strpos($datas[$i]['fault_title'],'基站控制传输中断告警')!==false ||
                    strpos($datas[$i]['fault_title'],'小区不可用告警')!==false ||
                    strpos($datas[$i]['fault_title'],'射频单元驻波告警')!==false ||
                    strpos($datas[$i]['fault_title'],'RSSI')!==false){
                    $datas[$i]['factory']='华为';
                }
                if(strpos($datas[$i]['fault_title'],'基站推出服务')!==false ||
                    strpos($datas[$i]['fault_title'],'网元断链告警')!==false ||
                    strpos($datas[$i]['fault_title'],'LTE小区退出服务')!==false ||
                    strpos($datas[$i]['fault_title'],'天馈驻波比异常')!==false ||
                    strpos($datas[$i]['fault_title'],'RX通道异常')!==false){
                    $datas[$i]['factory']='中兴';
                }
            }
        }
        //基站名称
        for($i=0;$i<count($datas);$i++){
            if(strpos($datas[$i]['fault_type'],'基站退服')!==false){
                $datas[$i]['base_name']=substr($datas[$i]['fault_title'],0,strpos($datas[$i]['fault_title'],'—'));
            }
            if(strpos($datas[$i]['fault_type'],'小区退服')!==false){
                if($datas[$i]['net_type']=='3G网络'){
                    if(preg_match('/^QY.*/',$datas[$i]['fault_code'])==1){
                        $datas[$i]['base_name']=$datas[$i]['fault_content'];
                    }
                    if(preg_match('/^GD.*/',$datas[$i]['fault_code'])==1){
                        $datas[$i]['base_name']=substr($datas[$i]['fault_content'],
                            strpos($datas[$i]['fault_content'],'Alias=')+6,
                            strpos($datas[$i]['fault_content'],'Type=RRU')-strpos($datas[$i]['fault_content'],'Alias=')-8);
                    }
                }
                if($datas[$i]['net_type']=='4G网络'){
                    if($datas[$i]['factory']=='中兴'){
                        $datas[$i]['base_name']=substr($datas[$i]['fault_content'],
                            strpos($datas[$i]['fault_content'],'RRU=')+4,
                            strpos($datas[$i]['fault_content'],'设备类型')-strpos($datas[$i]['fault_content'],'RRU=')-10);
                    }
                    if($datas[$i]['factory']=='华为'){
                        $datas[$i]['base_name']=substr($datas[$i]['fault_content'],
                            strpos($datas[$i]['fault_content'],'小区名称=')+13,
                            strpos($datas[$i]['fault_content'],', eNodeB标识=')-strpos($datas[$i]['fault_content'],'小区名称=')-13);
                    }
                }
            }
        }

        //基站区域
        for($i=0;$i<count($datas);$i++){
            $datas[$i]['base_area']=substr($datas[$i]['base_name'],0,6);
        }

        //基站名
        for($i=0;$i<count($datas);$i++) {
            if (preg_match('/RRU.*/', $datas[$i]['base_name'])) {
                $datas[$i]['base_name2'] = preg_replace('/RRU.*/', 'RRU1', $datas[$i]['base_name']);
            }elseif (preg_match('/FFE.*/', $datas[$i]['base_name'])) {
                $datas[$i]['base_name2'] = preg_replace('/FFE.*/', 'FFE1', $datas[$i]['base_name']);
            }else {
                $datas[$i]['base_name2'] = $datas[$i]['base_name'];
            }
        }

        $tza = new TZAddress();
        $tzas = $tza->find()->asArray()->all();

        for($i=0;$i<count($datas);$i++){
            $datas[$i]['tz_address']='';
            for($j=0;$j<count($tzas);$j++){
                if($datas[$i]['base_name2']==$tzas[$j]['tz_name']){
                    $datas[$i]['tz_address']=$tzas[$j]['tz_address'];
                }
            }
        }

        //-------------------------------
        $tmps=array();
        $tmp2=array();
        for($i=0;$i<count($datas);$i++){
            if($datas[$i]['tz_address']!==''){
                array_push($tmps,$datas[$i]['tz_address']);
            }
            $tmp2 = array_unique($tmps);
        }

        $tmps=array();
        foreach($tmp2 as $tmp){
            $tmps[$tmp]=array();
        }

        $tmp3=$tmps;

        for($i=0;$i<count($datas);$i++){
            if($datas[$i]['tz_address']!==''){
                array_push($tmps[$datas[$i]['tz_address']],[
                    'tz_address'=>$datas[$i]['tz_address'],
                    'fault_code'=>$datas[$i]['fault_code'],
                    'fault_state'=>$datas[$i]['fault_state'],
                    'base_name'=>$datas[$i]['base_name'],
                    'create_date'=>$datas[$i]['create_date'],
                    'repair_date'=>$datas[$i]['repair_date'],
                    'start_date'=>$datas[$i]['start_date'],
                    'end_date'=>$datas[$i]['end_date'],
                    'base_area'=>$datas[$i]['base_area']
                ]);
            }
        }

        foreach($tmps as $tmp){
            array_push($tmp3[$tmp[0]['tz_address']],$tmp[0]);
            if(count($tmp)>=2){
                for($i=1;$i<count($tmp);$i++){
                    if($tmp[$i]['create_date']>$tmp3[$tmp[0]['tz_address']][count($tmp3[$tmp[0]['tz_address']])-1]['repair_date']){
                        array_push($tmp3[$tmp[0]['tz_address']],$tmp[$i]);
                    }else{
                        if($tmp[$i]['repair_date']>$tmp3[$tmp[0]['tz_address']][count($tmp3[$tmp[0]['tz_address']])-1]['repair_date']){
                            $tmp3[$tmp[0]['tz_address']][count($tmp3[$tmp[0]['tz_address']])-1]['repair_date']=$tmp[$i]['repair_date'];
                            //echo $tmp3[$tmp[0]['tz_address']][count($tmp3[$tmp[0]['tz_address']])-1]['fault_code'].','.$tmp[$i]['fault_code'].'--<br/>';
                            $tmp3[$tmp[0]['tz_address']][count($tmp3[$tmp[0]['tz_address']])-1]['fault_code']=
                                $tmp3[$tmp[0]['tz_address']][count($tmp3[$tmp[0]['tz_address']])-1]['fault_code'].','.$tmp[$i]['fault_code'];
                            $tmp3[$tmp[0]['tz_address']][count($tmp3[$tmp[0]['tz_address']])-1]['base_name']=
                                $tmp3[$tmp[0]['tz_address']][count($tmp3[$tmp[0]['tz_address']])-1]['base_name'].','.$tmp[$i]['base_name'];
                        }else{
                            $tmp3[$tmp[0]['tz_address']][count($tmp3[$tmp[0]['tz_address']])-1]['fault_code']=
                                $tmp3[$tmp[0]['tz_address']][count($tmp3[$tmp[0]['tz_address']])-1]['fault_code'].','.$tmp[$i]['fault_code'];
                            $tmp3[$tmp[0]['tz_address']][count($tmp3[$tmp[0]['tz_address']])-1]['base_name']=
                                $tmp3[$tmp[0]['tz_address']][count($tmp3[$tmp[0]['tz_address']])-1]['base_name'].','.$tmp[$i]['base_name'];
                        }
                    }
                }
            }
        }

        $tmp4=array();
        foreach($tmp3 as $tmp){
            for($i=0;$i<count($tmp);$i++){
                array_push($tmp4,$tmp[$i]);
            }
        }

        /*
        for($i=0;$i<count($tmp4);$i++){
            if($tmp4[$i]['fault_state']=='已归档'){
                $tmp4[$i]['fault_times']=(strtotime($tmp4[$i]['repair_date'])-strtotime($tmp4[$i]['create_date']));
            }else{
                $tmp4[$i]['fault_times']=(strtotime($tmp4[$i]['repair_date'])-strtotime($tmp4[$i]['create_date']));
            }
        }
        */

        for($i=0;$i<count($tmp4);$i++){
            if($tmp4[$i]['fault_state']=='已归档'){
                $str1=$tmp4[$i]['create_date'];
                $str2=$tmp4[$i]['repair_date'];

                $str1_1=date('Y-m-d',strtotime($str1));
                $str2_1=date('Y-m-d',strtotime($str2));
                //ROUND((INT(C4-B4)*18+MOD(MEDIAN(6,24,MOD(C4,1)*24)-MEDIAN(6,24,MOD(B4,1)*24),18))*60,2)
                //echo $tmp4[$i]['fault_code'].' '.$str2_1.' '.$str1_1.' ';
                $num1=(strtotime($str2_1)-strtotime($str1_1))/(24*60*60)*18;

                $num2=$this->median(6,24,((strtotime($str1)-strtotime($str1_1))/(24*60*60))*24);
                $num3=$this->median(6,24,((strtotime($str2)-strtotime($str2_1))/(24*60*60))*24);

                $tmp4[$i]['fault_times']=round(($num1+fmod($num3-$num2,18))*60,2);
            }


            if($tmp4[$i]['fault_state']=='待回单'){
                $str1=$tmp4[$i]['create_date'];
                $str2=$tmp4[$i]['end_date'];

                $str1_1=date('Y-m-d',strtotime($str1));
                $str2_1=date('Y-m-d',strtotime($str2));
                //ROUND((INT(C4-B4)*18+MOD(MEDIAN(6,24,MOD(C4,1)*24)-MEDIAN(6,24,MOD(B4,1)*24),18))*60,2)
                //echo $tmp4[$i]['fault_code'].' '.$str2_1.' '.$str1_1.' ';
                $num1=(strtotime($str2_1)-strtotime($str1_1))/(24*60*60)*18;

                $num2=$this->median(6,24,((strtotime($str1)-strtotime($str1_1))/(24*60*60))*24);
                $num3=$this->median(6,24,((strtotime($str2)-strtotime($str2_1))/(24*60*60))*24);

                $str3=$tmp4[$i]['start_date'];
                $str3_1=date('Y-m-d',strtotime($str3));
                $num4=(strtotime($str2_1)-strtotime($str3_1))/(24*60*60)*18;
                $num5=$this->median(6,24,((strtotime($str3)-strtotime($str3_1))/(24*60*60))*24);

                $tmp4[$i]['fault_times']=
                    min(round(($num1+fmod($num3-$num2,18))*60,2),round(($num4+fmod($num3-$num5,18))*60,2));
            }
        }

        $this->exportExcel($tmp4,'test');

        /*
        foreach($tmp4 as $tmp){
            print_r($tmp);
            echo '-----<br/><br/>';
            echo '---------<br/>';
            echo '-----------------------';
            echo '---------<br/>';
        }*/
    }

    function median()
    {
        $args = func_get_args();

        switch(func_num_args())
        {
            case 0:
                trigger_error('median() requires at least one parameter',E_USER_WARNING);
                return false;
                break;

            case 1:
                $args = array_pop($args);
            // fallthrough

            default:
                if(!is_array($args)) {
                    trigger_error('median() requires a list of numbers to operate on or an array of numbers',E_USER_NOTICE);
                    return false;
                }

                sort($args);

                $n = count($args);
                $h = intval($n / 2);

                if($n % 2 == 0) {
                    $median = ($args[$h] + $args[$h-1]) / 2;
                } else {
                    $median = $args[$h];
                }

                break;
        }

        return $median;
    }

    function createList($filePath,$listName){
        if(empty($filePath) or !file_exists($filePath)){
            echo 'file not exists';
            return;
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
        $faultList->fault_list_name=$listName;
        $faultList->create_date=date('Y-m-d H:i:s');
        $faultList->owner='system';
        $faultList->state='normal';
        $faultList->info='test';
        if($faultList->save()){
            set_time_limit(0);
            for($rowIndex=2;$rowIndex<=$allRow;$rowIndex++){
                $fault=new Faults();
                $fault->fault_id=md5($faultList->fault_list_id.$currentSheet->getCell('A'.$rowIndex));
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
                if(!$fault->save()){
                    return 'error';
                }
            }
        }else{
            return 'error';
        }
        return 'success';
    }

    /* 导出excel函数*/
    public function exportExcel($datas,$name){
        $objPHPExcel = new \PHPExcel();
        /*以下就是对处理Excel里的数据， 横着取数据，主要是这一步，其他基本都不要改*/
        foreach($datas as $data => $v){
            $num=$data+1;
            $objPHPExcel->setActiveSheetIndex(0)
                //Excel的第A列，uid是你查出数组的键值，下面以此类推
                ->setCellValue('A'.$num, $v['fault_code'])
                ->setCellValue('B'.$num, $v['fault_state'])
                ->setCellValue('C'.$num, $v['create_date'])
                ->setCellValue('D'.$num, $v['repair_date'])
                ->setCellValue('E'.$num, $v['start_date'])
                ->setCellValue('F'.$num, $v['end_date'])
                ->setCellValue('G'.$num, $v['base_name'])
                ->setCellValue('H'.$num, $v['base_area'])
                ->setCellValue('I'.$num, $v['tz_address'])
                ->setCellValue('J'.$num, $v['fault_times']);
            }
        $objPHPExcel->getActiveSheet()->setTitle('User');
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$name.'.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }

}
