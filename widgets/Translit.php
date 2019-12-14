<?php

namespace smart\widgets;

use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\InputWidget;
use dkhlystov\helpers\Translit as TranslitHelper;
use smart\widgets\assets\TranslitAsset;

class Translit extends InputWidget
{
    /**
     * @inheritdoc
     */
    public $options = ['class' => 'form-control'];

    /**
     * @var string source attribute name
     */
    public $source;

    /**
     * @var array button options
     */
    public $buttonOptions = ['class' => 'btn btn-outline-secondary'];

    /**
     * @var string button text
     */
    public $buttonText = '<i class="fas fa-sync"></i>';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if ($this->source === null) {
            throw new InvalidConfigException('Property "source" must be set.');
        }

        if (empty($this->options['id'])) {
            $this->options['id'] = $this->id;
        }

        $this->registerClientScripts();
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $input = Html::activeTextInput($this->model, $this->attribute, $this->options);

        $options = $this->buttonOptions;
        $options['type'] = 'button';
        $options['data-translit'] = 'button';
        $options['data-translit-source'] = Html::getInputId($this->model, $this->source);
        $options['data-translit-target'] = Html::getInputId($this->model, $this->attribute);
        $options['data-translit-auto'] = true;
        $button = Html::tag('button', $this->buttonText, $options);
        $append = Html::tag('div', $button, ['class' => 'input-group-append']);

        echo Html::tag('div', $input . $append, ['class' => 'input-group']);
    }

    /**
     * Registration client scripts and initializing plugin
     * @return void
     */
    private function registerClientScripts()
    {
        $view = $this->getView();

        TranslitAsset::register($view);

        $replace = Json::htmlEncode(TranslitHelper::$replace);
        $view->registerJs("translit.replace = $replace;", $view::POS_READY, 'translit');
    }
}
