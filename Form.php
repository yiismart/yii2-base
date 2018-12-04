<?php

namespace smart\base;

use ArrayObject;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\helpers\Html;
use yii\validators\Validator;
use smart\base\FormValidator;

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
     * @var string|null HTML form name
     */
    public $formName;

    /**
     * @inheritdoc
     */
    public function __get($name)
    {
        return array_key_exists($name, $this->_forms) ? $this->_forms[$name] : parent::__get($name);
    }

    /**
     * @inheritdoc
     */
    public function __set($name, $value)
    {
        if (array_key_exists($name, $this->_forms)) {
            if ($this->_config[$name]['type'] == self::HAS_ONE) {
                $this->formSet($this->_forms[$name], $value);
            } else {
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
        } else {
            parent::__set($name, $value);
        }
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
     */
    public function formName()
    {
        return $this->formName === null ? parent::formName() : $this->formName;
    }

    /**
     * Configure nested forms
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
        $rules = $this->rules();
        foreach ($this->_config as $attribute => $config) {
            $rules[] = [$attribute, FormValidator::classNAme(), 'type' => $config['type']];
        }

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
     * @param array $data 
     * @return void
     */
    private function formSet(Form $form, $data)
    {
        $form->setAttributes($data);
    }

}
