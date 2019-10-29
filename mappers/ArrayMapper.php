<?php

namespace smart\mappers;

use Yii;
use yii\base\InvalidConfigException;

class ArrayMapper extends Mapper
{
    /**
     * @var string|array mapper type or mapper config array
     */
    public $mapper;

    /**
     * @var \Closure from mapping function for each item
     */
    public $from;

    /**
     * @var \Closure to mapping function for each item
     */
    public $to;

    /**
     * @var smart\mappers\Mapper item mapper
     */
    protected $_itemMapper;

    /**
     * @inheritdoc
     */
    protected function assignFromValue($value)
    {
        $items = [];

        if (is_array($value)) {
            foreach ($value as $key => $item) {
                if ($this->from instanceof \Closure) {
                    $items[$key] = call_user_func($this->from, $item);
                } elseif ($mapper = $this->getItemMapper()) {
                    $items[$key] = $mapper->assignFromValue($item);
                }
            }
        }

        return $items;
    }

    /**
     * @inheritdoc
     */
    protected function assignToValue($value)
    {
        $items = [];

        if (is_array($value)) {
            foreach ($value as $key => $item) {
                if ($this->to instanceof \Closure) {
                    $items[$key] = call_user_func($this->to, $item);
                } elseif ($mapper = $this->getItemMapper()) {
                    $items[$key] = $mapper->assignToValue($item);
                }
            }
        }

        return $items;
    }

    /**
     * Getter for item mapper. If mapper property is not set, returns null.
     * @return smart\mappers\Mapper|null
     */
    protected function getItemMapper()
    {
        $mapper = $this->mapper;

        if ($this->_itemMapper === null && $mapper !== null) {
            $params = [];
            if (isset(static::$buildInMappers[$mapper])) {
                $mapper = static::$buildInMappers[$mapper];
            }
            if (is_array($mapper)) {
                $params = array_merge($mapper, $params);
            } else {
                $params['class'] = $mapper;
            }
            $this->_itemMapper = Yii::createObject($params);
        }

        return $this->_itemMapper;
    }
}
