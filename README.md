JsTree for Yii2
===============

[JsTree](http://www.jstree.com/) for Yii2.

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
                    'className' => MyNameClassTree::class, // You model class name
                    'cacheActive' => false,
                ],
        ];
    }
    
```
With model and ActiveForm :
```php

<?= $form->field($model, 'path')->widget(\igor162\JsTree\JsTreeInput::className(), [
	'treeDataRoute' => ['getTree', 'selected_id' => $model->id],
]) ?>

```

Use with ["wbraganca/yii2-dynamicform"](https://github.com/wbraganca/yii2-dynamicform):
```php
$script = <<< JS
jQuery("form#{$model->formName()} .dynamicform_wrapper").on("afterInsert", function(e, item) {

var jstree = $(this).find('[data-jstree-style]'); 
       
    if (jstree.length > 0) {
      jstree.each(function() {
        var jstreeInput = $(this).attr('id');
        var jstreeName = $(this).next().attr('id');
        var jstreeOptions = eval($(this).attr('data-jstree-options'));

        $('#' + jstreeName).jstree('destroy');

        $('#' + jstreeName)
          .on('loaded.jstree', function() { jQuery(this).jstree('select_node', jQuery('#' + jstreeInput).val().split(','), true); })
          .on('changed.jstree', function(e, data) { jQuery('#' + jstreeInput).val(data.selected.join()); })
         // .on('changed.jstree', '')
         // .on('select_node.jstree', '')
          .jstree(jstreeOptions);

      });
    }
  });
JS;
$this->registerJs($script);

    <?php DynamicFormWidget::begin([
        'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
        'widgetBody' => '.container-items', // required: css class selector
        'widgetItem' => '.item', // required: css class
        'limit' => 4, // the maximum times, an element can be added (default 999)
        'min' => 0, // 0 or 1 (default 1)
        'insertButton' => '.add-item', // css class
        'deleteButton' => '.remove-item', // css class
        'model' => $modelsAddress[0],
        'formId' => 'dynamic-form',
        'formFields' => [
            'full_name',
            'address_line1',
            'address_line2',
            'city',
            'state',
            'postal_code',
        ],
    ]); ?>
     <?php foreach ($modelsAddress as $index => $modelAddress): ?>
     <?php
     // necessary for update action.
     if (! $modelAddress->isNewRecord) { echo Html::activeHiddenInput($modelAddress, "[{$index}]id"); } ?>
			
<?= $form->field($modelDetail, '[{$index}]path')->widget(\igor162\JsTree\JsTreeInput::className(), [
	'treeDataRoute' => ['getTree', 'selected_id' => $model->id],
]) ?>
	<?php endforeach; ?>
    <?php DynamicFormWidget::end(); ?>

```
