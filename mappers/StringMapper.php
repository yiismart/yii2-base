<?php

namespace smart\mappers;

class StringMapper extends Mapper
{
    /**
     * @var boolean allow to set null value to object attribute when form attribute value is empty
     */
    public $allowNull = true;

    /**
     * @inheritdoc
     */
    protected function assignFromValue($value)
    {
        return $value;
    }

    /**
     * @inheritdoc
     */
    protected function assignToValue($value)
    {
        if ($this->allowNull && $value === '') {
            return null;
        }
        return $value;
    }
}
