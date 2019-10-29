<?php

namespace smart\mappers;

use Yii;
use yii\base\Component;
use yii\base\NotSupportedException;

class Mapper extends Component
{
    /**
     * @var array list of built-in mappers (name => class or configuration)
     */
    public static $buildInMappers = [
        'string' => 'smart\mappers\StringMapper',
        'boolean' => 'smart\mappers\BooleanMapper',
        'array' => 'smart\mappers\ArrayMapper',
        'integer' => 'smart\mappers\IntegerMapper',
        'html' => 'smart\mappers\HtmlMapper',
        'date' => 'smart\mappers\DateMapper',
        'time' => 'smart\mappers\TimeMapper',
        'double' => 'smart\mappers\DoubleMapper',
    ];

    /**
     * @var array|string attributes to be mapped by this mapper. For multiple attributes,
     * please specify them as an array; for single attribute, you may use either a string or an array.
     */
    public $attributes = [];

    public static function createMapper($type, $attributes, $params = [])
    {
        $params['attributes'] = $attributes;

        if (isset(static::$buildInMappers[$type])) {
            $type = static::$buildInMappers[$type];
        }
        if (is_array($type)) {
            $params = array_merge($type, $params);
        } else {
            $params['class'] = $type;
        }

        return Yii::createObject($params);
    }

    public function init()
    {
        parent::init();
        $this->attributes = (array) $this->attributes;
    }

    public function getMapperAttributes($attributes)
    {
        if ($attributes === null) {
            return $this->attributes;
        }

        if (is_scalar($attributes)) {
            $attributes = [$attributes];
        }

        return array_intersect($attributes, $this->attributes);
    }

    public function assignFromAttributes($form, $object, $attributes = null)
    {
        $attributes = $this->getMapperAttributes($attributes);

        foreach ($attributes as $attribute) {
            $this->assignFromAttribute($form, $object, $attribute);
        }
    }

    public function assignFromAttribute($form, $object, $attribute)
    {
        $form->$attribute = $this->assignFromValue($object->$attribute);
    }

    protected function assignFromValue($value)
    {
        throw new NotSupportedException(get_class($this) . ' does not support assignFromValue().');
    }

    public function assignToAttributes($form, $object, $attributes = null)
    {
        $attributes = $this->getMapperAttributes($attributes);

        foreach ($attributes as $attribute) {
            $this->assignToAttribute($form, $object, $attribute);
        }
    }

    public function assignToAttribute($form, $object, $attribute)
    {
        $object->$attribute = $this->assignToValue($form->$attribute);
    }

    protected function assignToValue($value)
    {
        throw new NotSupportedException(get_class($this) . ' does not support assignToValue().');
    }
}
