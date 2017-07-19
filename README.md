JsTree for Yii2
===============

[JsTree](http://www.jstree.com/) for Yii2.

WIP...

Installation
------------
The preferred way to install this helper is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require "igor162/yii2-jstree" "*"
```

or add

```json
"igor162/yii2-jstree" : "*"
```

to the require section of your application's `composer.json` file.

https://packagist.org/packages/igor162/yii2-jstree

Usage
-----

In the MyNameController:
```php
use devgroup\JsTreeWidget\actions\nestedset\FullTreeDataAction;
...

    public function actions()
    {
        return [
            'getTree' =>
                [
                    'class' => FullTreeDataAction::class,
                    'cacheKey' => '',
                    'querySortOrder' => 'sort',
                    'modelParentAttribute' => 'path',
                    'modelLabelAttribute' => 'name',
                    'className' => MyNameClassTree::class,
                    'cacheActive' => false,
                ],
        ];
    }
    
```
With model and ActiveForm :
```php

<?= $form->field($model, 'path')->widget(\igor162\JsTree\JsTree::className(), [
	'treeDataRoute' => ['getTree', 'selected_id' => $model->id],
]) ?>

```