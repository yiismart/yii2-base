<?php

namespace smart\widgets;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class ActiveField extends \yii\bootstrap4\ActiveField
{
    /**
     * @var array input group addons for textinput field
     * @see https://getbootstrap.com/docs/4.1/components/input-group/
     * Each item contains
     * 
     * text - text addon content, not encoded
     * button - button addon label, not encoded
     * options - addon tag options, default empty array
     */
    public $prepend = [];
    public $append = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if ($this->inputTemplate === null && !(empty($this->append) && empty($this->prepend))) {
            $this->prepareAddons();
        }
    }

    /**
     * Prepare addons to add into template
     * @return void
     */
    protected function prepareAddons()
    {
        $append = '';
        foreach ($this->append as $item) {
            if (array_key_exists('button', $item)) {
                $append .= $this->renderAddonButton($item);
            } else {
                $append .= $this->renderAddonText($item);
            }
        }
        if (!empty($append)) {
            $append = Html::tag('div', $append, ['class' => 'input-group-append']);
        }

        $prepend = '';
        foreach ($this->prepend as $item) {
            if (array_key_exists('button', $item)) {
                $prepend .= $this->renderAddonButton($item);
            } else {
                $prepend .= $this->renderAddonText($item);
            }
        }
        if (!empty($prepend)) {
            $prepend = Html::tag('div', $prepend, ['class' => 'input-group-prepend']);
        }

        $this->inputTemplate = '<div class="input-group">' . $prepend . '{input}' . $append . '</div>';
    }

    /**
     * Render button for input template addon
     * @param array $item 
     * @return string
     */
    protected function renderAddonButton($item)
    {
        $options = ArrayHelper::getvalue($item, 'options', []);
        if (!array_key_exists('class', $options)) {
            $options['class'] = 'btn btn-outline-secondary';
        }
        return Html::button(ArrayHelper::getValue($item, 'button', ''), $options);
    }

    /**
     * Render text for input template addon
     * @param array $item 
     * @return string
     */
    protected function renderAddonText($item)
    {
        $options = ArrayHelper::getvalue($item, 'options', []);
        Html::addCssClass($options, 'input-group-text');
        return Html::tag('span', ArrayHelper::getValue($item, 'text', ''), $options);
    }
}
