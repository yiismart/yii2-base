<?php

namespace smart\widgets;

class ActiveForm extends \yii\bootstrap4\ActiveForm
{
    /**
     * @inheritdoc
     */
    public $enableClientValidation = false;

    /**
     * @inheritdoc
     */
    public $layout = self::LAYOUT_HORIZONTAL;

    /**
     * @inheritdoc
     */
    public $fieldClass = 'smart\widgets\ActiveField';
}
