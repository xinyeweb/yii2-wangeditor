<?php
/**
 * Created by PhpStorm.
 * User: hoter.zhang
 * Date: 2018/5/9
 * Time: 上午 08:50
 */

namespace xinyeweb\wangeditor;


use yii\helpers\Html;
use yii\widgets\InputWidget;

class WANGEditor extends InputWidget
{
    /// 客户端配置
    public $clientOptions = [];

    //是否启用七牛云上传
    public $qiniu = 0;
    //启用七牛云上传需要传入获取token的url
    public $uptokenurl;
    //七牛获取图片的url //http://p8c2d3xyx.bkt.clouddn.com
    public $qiniudomain;

    /// 编辑器ID
    protected $_editorId;

    public function init()
    {
        parent::init();
        if (isset($this->options['id'])) {
            $this->id = $this->options['id'];
        } else {
            $this->id = $this->hasModel() ? Html::getInputId($this->model, $this->attribute) : $this->id;
        }
        $this->_editorId = 'editor-' . $this->id;
    }

    public function run()
    {
        if ($this->hasModel()) {
            echo Html::activeHiddenInput($this->model, $this->attribute, $this->options);
            $content = Html::getAttributeValue($this->model, $this->attribute);
        } else {
            echo Html::hiddenInput($this->name, $this->value, $this->options);
            $content = $this->value;
        }
        echo Html::tag('div', $content, ['id'=>$this->_editorId]);
        if ($this->qiniu == 1) {
            $this->registerQiNiuClentJs();
        } else {
            $this->registerClentJs();
        }

    }

    public function registerClentJs() {
        $view = $this->getView();
        WANGEditorAsset::register($view);
        /// 全屏插件
        WANGEditorFullScreenAsset::register($view);
        $editorId = $this->_editorId;
        $editorName = 'editor';
        $hiddenInputId = $this->options['id'];
        $jsStr = <<<EOF
var WangEditor = window.wangEditor;
var {$editorName} = new WangEditor('#{$editorId}');
/*将富文本的数据更改后加入到隐藏域*/
{$editorName}.customConfig.onchange = function (html) {
    $('#{$hiddenInputId}').val(html);
}
{$editorName}.create();
/*全屏插件*/
WangEditor.fullscreen.init('#{$editorId}');
EOF;
$view->registerJs($jsStr);
    }

    public function registerQiNiuClentJs() {
        $view = $this->getView();
        WANGEditorAsset::register($view);
        /// 全屏插件
        WANGEditorFullScreenAsset::register($view);
        $editorId = $this->_editorId;
        $editorName = 'editor';
        $hiddenInputId = $this->options['id'];
        $jsStr = <<<EOF
var WangEditor = window.wangEditor;
var {$editorName} = new WangEditor('#{$editorId}');
/*将富文本的数据更改后加入到隐藏域*/
{$editorName}.customConfig.onchange = function (html) {
    $('#{$hiddenInputId}').val(html);
}
{$editorName}.customConfig.qiniu = true;
{$editorName}.create();
/*全屏插件*/
WangEditor.fullscreen.init('#{$editorId}');
/*初始化七牛上传*/
uploadInit();
/*初始化七牛上传的方法*/
function uploadInit() {
    /* 获取相关 DOM 节点的 ID*/
    var btnId = {$editorName}.imgMenuId;
    var containerId = {$editorName}.toolbarElemId;
    var textElemId = {$editorName}.textElemId;
    /*创建上传对象*/
    var uploader = Qiniu.uploader({
        runtimes: 'html5,flash,html4',
        browse_button: btnId,
        uptoken_url: '{$this->uptokenurl}',/*Ajax请求upToken的Url*/
        /*uptoken: '',*/
        unique_names: true,/*默认 false，key为文件名。若开启该选项，SDK会为每个文件自动生成key（文件名）*/
        domain: '{$this->qiniudomain}',
        container: containerId,
        max_file_size: '100mb',
        flash_swf_url: '../js/plupload/Moxie.swf', 
        filters: {
               mime_types: [
                 /*只允许上传图片文件 （注意，extensions中，逗号后面不要加空格）*/
                 { title: "图片文件", extensions: "jpg,gif,png,bmp" }
               ]
        },
        max_retries: 3, /*上传失败最大重试次数*/
        dragdrop: true, /*开启可拖曳上传*/
        drop_element: textElemId,
        chunk_size: '4mb', /*分块上传时，每片的体积*/
        auto_start: true, /*择文件后自动上传，若关闭需要自己绑定事件触发上传*/
        init: {
            'FilesAdded': function(up, files) {
                plupload.each(files, function(file) {
                    /*文件添加进队列后,处理相关的事情*/ 
                    printLog('on FilesAdded');
                });
            },
            'BeforeUpload': function(up, file) {
                /*每个文件上传前,处理相关的事情*/ 
                printLog(uploader.uptoken);
                printLog('on BeforeUpload');
            },
            'UploadProgress': function(up, file) {
                /*显示进度*/ 
                printLog('进度 ' + file.percent)
            },
            'FileUploaded': function(up, file, info) {
                printLog(info);
                var domain = up.getOption('domain');
                var res = $.parseJSON(info);
                var sourceLink = domain + res.key; /*获取上传成功后的文件的Url*/
                printLog(sourceLink);
                /*插入图片到editor*/
                editor.cmd.do('insertHtml', '<img src="' + sourceLink + '" style="max-width:100%;"/>')
            },
            'Error': function(up, err, errTip) {
               /*上传出错时,处理相关的事情*/
               printLog('on Error');
            },
            'UploadComplete': function() {
                /*队列文件处理完毕后,处理相关的事情*/
                printLog('on UploadComplete');
            }
        }
    });
    function printLog(title, info) {
        window.console && console.log(title, info);
    }
}
EOF;
        $view->registerJs($jsStr);
    }
}