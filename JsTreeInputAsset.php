<?php

namespace igor162\JsTree;

use yii\web\AssetBundle;

/**
 * JsTree asset bundle.
 *
 * Class TreeInputAsset
 * @package igor162\JsTree
 */
class JsTreeInputAsset extends AssetBundle
{

    public $sourcePath = '@bower/jstree/dist';

    public $depends = [
        'yii\web\JqueryAsset',
    ];

    public function init()
    {
        parent::init();

        // set css & js
        $min = YII_DEBUG ? '' : '.min';
        $this->css = ["themes/default/style{$min}.css"];
        $this->js = ["jstree{$min}.js"];
    }

}
