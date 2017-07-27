<?php
/**
 * Created by PhpStorm.
 * User: luoguangwei
 * Date: 2017/7/26
 * Time: 10:12
 */
namespace app\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class StaticsAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'bootstrap/dist/css/bootstrap.min.css',
    ];
    public $js = [
        'jquery/jquery.min.js',
        'bootstrap/dist/js/bootstrap.min.js',
    ];
    /*
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
    */
}
