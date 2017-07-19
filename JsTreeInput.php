<?php

namespace igor162\JsTreeInput;

use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\helpers\ArrayHelper;
use yii\web\View;

/**
 * Class TreeInput
 * @package igor162\JsTree
 *
 * @property string $field
 * @property string $attribute
 * @property string $name
 * @property string $value
 * @property array $plugins
 * @property boolean $types
 * @property string $treeType
 * @property array $typesOptions
 * @property array $treeDataRoute
 * @property array $clientOptions
 * @property string $template
 * @property array $options
 * @property boolean $multiple
 * @property string $onChanged
 * @property string $onSelect
 * @property array $selectedNodes
 *
 * @property Model $model
 *
 */
class JsTreeInput extends \yii\widgets\InputWidget
{

    /**
     * @var array Enabled jsTree plugins
     * @see http://www.jstree.com/plugins/
     */
    public $plugins = ['dnd', 'types'];

    /**
     * @var array Configuration for types plugin
     * @see http://www.jstree.com/api/#/?f=$.jstree.defaults.types
     */
    public $types = false;

    public $typesOptions = [
        'default' => [
            'icon' => 'fa fa-file',
        ],
        'demo' => [
            'icon' => 'fa fa-folder',
        ],
    ];

    /**
     * JsTree treeDataRoute
     * @see http://www.jstree.com/docs/json/
     */
    public $treeDataRoute;

    /**
     * JsTree options
     */
    public $clientOptions = [];

    /**
     * @var string the template for arranging the jstree and the hidden input tag.
     */
    public $template = '{input}{jstree}';

    /**
     * @var array the HTML attributes for the input tag.
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $options = [];

    /**
     * Multiple selection
     */
    public $multiple = false;

    /**
     * changed.jstree handler
     */
    public $onChanged;

    /**
     * select_node.jstree handler
     */
    public $onSelect;

    /** @var array selected nodes */
    public $selectedNodes = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
    }

    /**
     * Renders the widget.
     */
    public function run()
    {
        if (!is_array($this->treeDataRoute)) {
            throw new InvalidConfigException("Attribute treeDataRoute is required to use TreeWidget.");
        }

        if (count($this->selectedNodes) > 0) {
            $this->treeDataRoute['selected'] = $this->selectedNodes;
        }

        $this->registerClientScript();

        if ($this->hasModel()) {
            $input = Html::activeHiddenInput($this->model, $this->attribute, $this->options);
        } else {
            $input = Html::hiddenInput($this->name, $this->value, $this->options);
        }

        echo strtr($this->template, [
            '{input}' => $input,
            '{jstree}' => Html::tag('div', '', ['id' => $this->getJsTreeId()]),
        ]);
    }

    /**
     * Registers the needed JavaScript.
     */
    public function registerClientScript()
    {
        $inputId = $this->options['id'];
        $jsTreeId = $this->getJsTreeId();
        $options = $this->getClientOptions();
        $options = empty($options) ? '' : Json::encode($options);

        $onChanged = '';
        if ($this->onChanged)
            $onChanged = ".on('changed.jstree', {$this->onChanged})";

        $onSelect = '';
        if ($this->onSelect)
            $onSelect = ".on('select_node.jstree', {$this->onSelect})";

        $view = $this->getView();
        JsTreeInputAsset::register($view);

        $this->getView()->registerJs("
            jQuery('#$jsTreeId')
                .on('loaded.jstree', function() { jQuery(this).jstree('select_node', jQuery('#$inputId').val().split(','), true); })
                .on('changed.jstree', function(e, data) { jQuery('#$inputId').val(data.selected.join()); })
                $onChanged
                $onSelect
                .jstree($options);
                                    ", View::POS_READY);
    }

    /**
     * Returns the options for jstree
     * @return array
     */
    protected function getClientOptions()
    {

        // Удалить параметр плагина "types"
        if($this->types === false) { ArrayHelper::removeValue($this->plugins, 'types'); }

        $options = [
            'plugins' => $this->plugins,
            'types' => $this->typesOptions,
            'core' => [
                'check_callback' => true,
                'multiple' => $this->multiple,
                'data' => [
                    'url' => new JsExpression(
                        "function (node) {
                            return " . Json::encode(Url::to($this->treeDataRoute)) . ";
                        }"
                    ),
                    'success' => new JsExpression(
                        "function (node) {
                            return { 'id' : node.id };
                        }"
                    ),
                    'data' => new JsExpression(
                        "function (node) {
                        return { 'id' : node.id };
                        }"
                    ),
                    'error' => new JsExpression(
                        "function ( o, textStatus, errorThrown ) {
                            alert(o.responseText);
                        }"
                    )
                ]
            ]
        ];

        if($this->types === false) { ArrayHelper::removeValue($this->clientOptions, 'types'); }

        return $options;
    }

    /**
     * Returns the jstree container id
     * @return string
     */
    protected function getJsTreeId()
    {
        return $this->options['id'] . '_jstree';
    }
}
