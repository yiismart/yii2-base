<?php

namespace smart\mappers;

class TimeMapper extends Mapper
{
    /**
     * @var boolean format time format
     */
    public $format = 'HH:mm';

    /**
     * @inheritdoc
     */
    protected function assignFromValue($value)
    {
        if (empty($value)) {
            return '';
        }
        return Yii::$app->getFormatter()->asTime($value, $this->format);
    }

    /**
     * @inheritdoc
     */
    protected function assignToValue($value)
    {
        return $value;
    }
}
