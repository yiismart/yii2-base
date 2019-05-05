<?php

namespace smart\widgets;

use Yii;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\InputWidget;
use smart\widgets\assets\DatepickerAsset;

class Datepicker extends InputWidget
{
    /**
     * @var array additional options for jquery bootstrap datepicker widget
     */
    public $clientOptions = [];

    /**
     * @inheritdoc
     */
    public $options = ['class' => 'form-control'];

    /**
     * @var boolean provides a way to avoid conflict with jQuery UI datepicker plugin
     */
    public $juiNoConflict = false;

    /**
     * @var string
     */
    private $_language;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (empty($this->options['id'])) {
            $this->options['id'] = $this->id;
        }

        $this->prepareLanguage();
        $this->registerClientScripts();
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        echo Html::activeTextInput($this->model, $this->attribute, $this->options);
    }

    private function prepareLanguage()
    {
        if (empty($this->clientOptions['language'])) {
            $this->clientOptions['language'] = strtolower(substr(Yii::$app->language, 0, 2));
        }

        $this->_language = $this->clientOptions['language'];
        if ($this->clientOptions['language'] == 'en') {
            unset($this->clientOptions['language']);
        }
    }

    /**
     * Registration client scripts and initializing plugin
     * @return void
     */
    private function registerClientScripts()
    {
        $view = $this->getView();

        DatepickerAsset::$language = $this->_language;
        DatepickerAsset::$juiNoConflict = $this->juiNoConflict;
        DatepickerAsset::register($view);

        $clientOptions = array_replace([
            'format' => 'yyyy-mm-dd',
        ], $this->clientOptions);
        $options = Json::htmlEncode($clientOptions);

        $view->registerJs("jQuery('#{$this->options['id']}').datepicker(jQuery.extend({zIndexOffset: 100}, $options));");
    }
}
