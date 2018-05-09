<?php
/**
 * Created by PhpStorm.
 * User: hoter.zhang
 * Date: 2018/5/9
 * Time: 上午 08:47
 */

namespace xinyeweb\wangeditor;


use yii\web\AssetBundle;

class WANGEditorFullScreenAsset extends AssetBundle
{

    public $css = [
        'plugin/full-screen/wangEditor-fullscreen-plugin.css',
    ];
    public $js = [
        'plugin/full-screen/wangEditor-fullscreen-plugin.js'
    ];

    public function init()
    {
        $this->sourcePath =dirname(__FILE__) .DIRECTORY_SEPARATOR . 'assets';
        parent::init();
    }
}