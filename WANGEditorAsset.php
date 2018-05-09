<?php
/**
 * Created by PhpStorm.
 * User: hoter.zhang
 * Date: 2018/5/9
 * Time: 上午 08:47
 */

namespace xinyeweb\wangeditor;


use yii\web\AssetBundle;

class WANGEditorAsset extends AssetBundle
{

    public $css = [];
    public $js = [
        'plupload/plupload.full.min.js',
        'qiniu.min.js',
        'wangEditor.min.js'
    ];

    public function init()
    {
        $this->sourcePath = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'release';
        parent::init();
    }
}