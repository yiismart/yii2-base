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
     * @var string The format used to convert the value into a date string.
     * @see http://userguide.icu-project.org/formatparse/datetime
     */
    public $format;

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
        $this->processFormat();
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

        $view->registerJs("jQuery('#{$this->options['id']}').datepicker(jQuery.extend({autoclose: true, zIndexOffset: 100}, $options));");
    }

    private function processFormat()
    {
        if (empty($this->format) || isset($this->clientOptions['format'])) {
            return;
        }

        $pattern = $this->format;

        // http://userguide.icu-project.org/formatparse/datetime#TOC-Date-Time-Format-Syntax
        // escaped text
        $escaped = [];
        if (preg_match_all('/(?<!\')\'(.*?[^\'])\'(?!\')/', $pattern, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $match[1] = str_replace('\'\'', '\'', $match[1]);
                $escaped[$match[0]] = '\\' . implode('\\', preg_split('//u', $match[1], -1, PREG_SPLIT_NO_EMPTY));
            }
        }

        $this->clientOptions['format'] = strtr($pattern, array_merge($escaped, [
            'y' => 'yyyy',      // 4digit year e.g. 2014
            'yyyy' => 'yyyy',   // 4digit year e.g. 2014
            'yy' => 'yy',       // 2digit year number eg. 14
            'M' => 'm',         // Numeric representation of a month, without leading zeros
            'MM' => 'mm',       // Numeric representation of a month, with leading zeros
            'MMM' => 'M',       // A short textual representation of a month, three letters
            'MMMM' => 'MM',     // A full textual representation of a month, such as January or March
            'd' => 'd',         // day without leading zeros
            'dd' => 'dd',       // day with leading zeros
        ]));
    }
}
