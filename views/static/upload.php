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

<?php $form=ActiveForm::begin(['options'=>['enctype'=>'multipart/form-data']])?>
<?=$form->field($model,'file')->fileInput()?>
<button type="submit">submit</button>
<?php ActiveForm::end()?>


