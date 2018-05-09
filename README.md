wangeditor
==========
yii2-wangeditor

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist xinyeweb/yii2-wangeditor "*"
```

or add

```
"xinyeweb/yii2-wangeditor": "dev-master"
```

to the require section of your `composer.json` file.


Usage
-----

Once the extension is installed, simply use it in your code by  :

```php
<?= $form->field($model, 'description')->widget(xinyeweb\wangeditor\WANGEditor::className(),[
        'qiniu' => 1, // 1 开启 0 关闭
        'uptokenurl' => Yii::$app->urlManager->createUrl(['public/get-up-token']), //获取uptoken
        'qiniudomain' => 'http://p8c2d3xyx.bkt.clouddn.com/' //储存空间的域名
]) ?>```