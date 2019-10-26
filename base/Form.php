<?php

namespace smart\base;

use Yii;
use ArrayObject;
use IntlDateFormatter;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
use yii\validators\Validator;
use smart\validators\FormValidator;
use smart\mappers\Mapper;

class Form extends Model
{
    // Type
    const HAS_ONE = 'one';
    const HAS_MANY = 'many';

    /**
     * @var array
     */
    private $_config = [];

    /**
     * @var array
     */
    private $_forms = [];

    /**
     * @var ArrayObject list of mappers
     */
    private $_mappers;

    /**
     * @var string|null HTML form name
     */
    public $formName;

    /**
     * @inheritdoc
     */
    public function __get($name)
    {
        // If there are no form then use inherit
        if (!array_key_exists($name, $this->_forms)) {
            return parent::__get($name);
        }

        // Return form
        return $this->_forms[$name];
    }

    /**
     * @inheritdoc
     */
    public function __set($name, $value)
    {
        // If there are no form then use inherit
        if (!array_key_exists($name, $this->_forms)) {
            parent::__set($name, $value);
            return;
        }

        // Set one form
        if ($this->_config[$name]['type'] == self::HAS_ONE) {
            $this->formSet($this->_forms[$name], $value);
            return;
        }

        // Set many forms
        if (!is_array($value)) {
            $value = [];
        }
        $class = $this->_config[$name]['class'];
        $forms = [];
        foreach ($value as $key => $data) {
            $form = new $class;
            $form->formName = Html::getInputName($this, $name) . '[' . $key . ']';
            $this->formSet($form, $data);
            $forms[$key] = $form;
        }
        $this->_forms[$name] = $forms;
    }

    /**
     * @inheritdoc
     */
    public function __construct($config = [])
    {
        // Prepare config
        $c = [];
        foreach ($this->forms() as $item) {
            $c[$item[0]] = [
                'type' => $item[1],
                'class' => $item[2],
            ];
        }
        $this->_config = $c;

        // Init nested forms
        $forms = [];
        foreach ($this->_config as $attribute => $c) {
            if ($c['type'] == self::HAS_ONE) {
                $forms[$attribute] = new $c['class'];
                $forms[$attribute]->formName = Html::getInputName($this, $attribute);
            } elseif ($c['type'] == self::HAS_MANY) {
                $forms[$attribute] = [];
            }
        }
        $this->_forms = $forms;

        // Inherit
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     * Check [[formName]] property to generate form name
     */
    public function formName()
    {
        return $this->formName === null ? parent::formName() : $this->formName;
    }

    public function map()
    {
        return [];
    }

    public function getMappers()
    {
        if ($this->_mappers === null) {
            $this->_mappers = $this->createMappers();
        }

        return $this->_mappers;
    }

    public function createMappers()
    {
        $mappers = new ArrayObject();
        foreach ($this->map() as $map) {
            if ($map instanceof Mapper) {
                $mappers->append($map);
            } elseif (is_array($map) && isset($map[0], $map[1])) {
                $mapper = Mapper::createMapper($map[1], (array) $map[0], array_slice($map, 2));
                $mappers->append($mapper);
            } else {
                throw new InvalidConfigException('Invalid mapper: a mapper must specify both attribute names and mapper type.');
            }
        }

        return $mappers;
    }

    /**
     * Nested forms configuration
     * [attribute_name, relation_type, form_class]
     * relation_type = 'one'|'many'
     * @return array
     */
    public function forms()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function createValidators()
    {
        // Create nested forms validators
        $rules = $this->rules();
        foreach ($this->_config as $attribute => $config) {
            $rules[] = [$attribute, FormValidator::className(), 'type' => $config['type']];
        }

        // Inherit
        $validators = new ArrayObject();
        foreach ($rules as $rule) {
            if ($rule instanceof Validator) {
                $validators->append($rule);
            } elseif (is_array($rule) && isset($rule[0], $rule[1])) { // attributes, validator type
                $validator = Validator::createValidator($rule[1], $this, (array) $rule[0], array_slice($rule, 2));
                $validators->append($validator);
            } else {
                throw new InvalidConfigException('Invalid validation rule: a rule must specify both attribute names and validator type.');
            }
        }
        return $validators;
    }

    /**
     * Fill form with data from object or request
     * @param Form $form 
     * @param array|yii\base\ActiveRecord $data 
     * @return void
     */
    private function formSet(Form $form, $data)
    {
        if ($data instanceof yii\base\ActiveRecord) {
            $form->assignFrom($data);
        } else {
            $form->setAttributes($data);
        }
    }

    // /**
    //  * Assign nested forms from object attribute
    //  * @param yii\base\ActiveRecord $object 
    //  * @param string $attribute object attribute
    //  * @param string $name form name
    //  * @return void
    //  */
    // public function assignFormsFrom($object, $attribute, $name)
    // {
    //     // Assign one form
    //     if ($this->_config[$name]['type'] == self::HAS_ONE) {
    //         $this->_forms[$name]->assignFrom($object->$attribute);
    //         return;
    //     }

    //     // Assign many forms
    //     $class = $this->_config[$name]['class'];
    //     $forms = [];
    //     foreach ($object->$attribute as $key => $item) {
    //         $form = new $class;
    //         $form->formName = Html::getInputName($this, $name) . '[' . $key . ']';
    //         $form->assignFrom($item);
    //         $forms[] = $form;
    //     }
    //     $this->_forms[$name] = $forms;
    // }

    /**
     * Assign form from object
     * @param yii\base\ActiveRecord $object 
     * @return void
     */
    public function assignFrom($object, $attributeNames = null)
    {
        if ($attributeNames === null) {
            $attributeNames = $this->activeAttributes();
        }

        $attributeNames = (array) $attributeNames;

        foreach ($this->getMappers() as $mapper) {
            $mapper->assignFromAttributes($this, $object, $attributeNames);
        }
    }

    /**
     * Assign object within the form
     * @param yii\base\ActiveRecord $object 
     * @return void
     */
    public function assignTo($object, $attributeNames = null)
    {
        if ($attributeNames === null) {
            $attributeNames = $this->activeAttributes();
        }

        $attributeNames = (array) $attributeNames;

        foreach ($this->getMappers() as $mapper) {
            $mapper->assignToAttributes($this, $object, $attributeNames);
        }
    }

    public static function fromDate($value, $format = 'yyyy-MM-dd')
    {
        if (empty($value)) {
            return '';
        }
        return Yii::$app->getFormatter()->asDate($value, $format);
    }

    public static function fromTime($value, $format = 'HH:mm')
    {
        if (empty($value)) {
            return '';
        }
        return Yii::$app->getFormatter()->asTime($value, $format);
    }

    public static function fromHtml($value)
    {
        return $value;
    }

    public static function fromDouble($value)
    {
        return (string) $value;
    }

    public static function fromInteger($value)
    {
        return (string) $value;
    }

    public static function fromObject($value)
    {
        if (!($value instanceof yii\base\ActiveRecord) || is_array($value)) {
            $value = [];
        }
        return $value;
    }

    public static function fromObjects($value)
    {
        if (!is_array($value)) {
            return [];
        }
        $items = [];
        foreach ($value as $key => $item) {
            $item = self::fromObject($item);
            if (!empty($item)) {
                $items[$key] = $item;
            }
        }
        return $items;
    }

    public static function toDate($value, $format = 'yyyy-MM-dd')
    {
        if (empty($value)) {
            return '';
        }
        $formatter = Yii::$app->getFormatter();
        $intl = new IntlDateFormatter($formatter->locale, null, null, $formatter->timeZone, $formatter->calendar, $format);
        if (($date = $intl->parse($value)) === false) {
            return '';
        }
        return date('Y-m-d', $date);
    }

    public static function toTime($value)
    {
        return $value;
    }

    public static function toHtml($value)
    {
        return HtmlPurifier::process($value, function($config) {
            $config->set('Attr.EnableID', true);
            $config->set('HTML.SafeIframe', true);
            $config->set('URI.SafeIframeRegexp', '%^(?:https?:)?//(?:www.youtube.com/embed/|player.vimeo.com/video/|yandex.ru/map-widget/)%');
        });
    }

    public static function toDouble($value, $allowNull = false)
    {
        if ($allowNull && $value === '') {
            return null;
        }
        return (double) $value;
    }

    public static function toInteger($value, $allowNull = false)
    {
        if ($allowNull && $value === '') {
            return null;
        }
        return (integer) $value;
    }
}
