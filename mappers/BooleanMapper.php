<?php

namespace smart\mappers;

class BooleanMapper extends Mapper
{
    /**
     * @inheritdoc
     */
    protected function assignFromValue($value)
    {
        return $value ? '1' : '0';
    }

    /**
     * @inheritdoc
     */
    protected function assignToValue($value)
    {
        return $value == 0 ? false : true;
    }
}
