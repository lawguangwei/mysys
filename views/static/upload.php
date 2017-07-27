<?php
/**
 * Created by PhpStorm.
 * User: luogw
 * Date: 2017-07-17
 * Time: 15:57
 */
use yii\widgets\ActiveForm;
?>
<!--
<div>
    <form action="index.php?r=static/upload" method="post" enctype="multipart/form-data">
        <input name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" hidden>
        <input name="file" type="file">
        <button type="submit">提交</button>
    </form>
</div>-->
<style>
    .my-form{
        margin-top: 40px;
        border: 2px solid #757575;
        padding: 20px;
    }

    .sp-1{
        font-weight: bold;
        font-size: 16px;
    }
</style>

<form class="col-md-6 col-md-offset-3 my-form" action="index.php?r=static/upload" method="post" enctype="multipart/form-data">
    <div class="form-group">
        <label for="list_name"><span class="text-info">输入清单名</span></label>
        <input type="text" class="form-control" id="list_name" name="list_name" placeholder="请输入清单名，不能与数据库已有重复。">
    </div>
    <div class="form-group">
        <input name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" hidden>
        <label for="file"><span class="text-info">选择Excel文件</span></label>
        <input type="file" name="file" id="file">
        <p class="help-block">请选择Excel文件插入数据.</p>
    </div>
    <?php
    if(isset($info)){?>
        <div class="form-group">
            <div class="alert alert-warning alert-dismissible fade in" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                <strong>警告!</strong><?=$info?>
            </div>
        </div>
    <?php }
    ?>
    <button type="submit" class="btn btn-default col-md-4 col-md-offset-4"><span class="text-info sp-1">提交</span></button>
</form>

<?php /*$form=ActiveForm::begin(['options'=>['enctype'=>'multipart/form-data']])?>
<?=$form->field($model,'file')->fileInput()?>
<button type="submit">submit</button>
<?php ActiveForm::end() */?>


