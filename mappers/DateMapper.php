<?php

namespace smart\mappers;

use Yii;
use IntlDateFormatter;

class DateMapper extends Mapper
{
    /**
     * @var boolean format date format
     */
    public $format = 'yyyy-MM-dd';

    /**
     * @inheritdoc
     */
    protected function assignFromValue($value)
    {
        if (empty($value)) {
            return '';
        }
        return Yii::$app->getFormatter()->asDate($value, $this->format);
    }

    /**
     * @inheritdoc
     */
    protected function assignToValue($value)
    {
        if (empty($value)) {
            return '';
        }
        $formatter = Yii::$app->getFormatter();
        $intl = new IntlDateFormatter($formatter->locale, null, null, $formatter->timeZone, $formatter->calendar, $this->format);
        if (($date = $intl->parse($value)) === false) {
            return '';
        }
        return date('Y-m-d', $date);
    }
}
