<?php

namespace smart\widgets;

use yii\base\InvalidConfigException;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\widgets\InputWidget;
use smart\grid\DropdownInputColumn;
use smart\grid\MovingColumn;
use smart\grid\TextInputColumn;
use smart\widgets\assets\ArrayInputAsset;

/**
 * Widget for array input field
 */
class ArrayInput extends InputWidget
{

    /**
     * @var string the class of the every row in input array
     */
    public $itemClass;

    /**
     * @var string[]|array[] array of attributes or array of columns configs
     * @see [[dkhlystov\grid\TextInputColumn]]
     * @see [[yii\grid\GridView::columns]]
     */
    public $columns;

    /**
     * @var array HTML options for table
     */
    public $tableOptions = ['class' => 'table table-bordered table-sm'];

    /**
     * @var array HTML options for add button
     */
    public $addButtonOptions = ['class' => 'btn btn-secondary'];

    /**
     * @var string label for add button
     */
    public $addLabel = 'Add';

    /**
     * @var string label for remove link
     */
    public $removeLabel = 'Remove';

    /**
     * @var string|null
     */
    public $readOnlyAttribute;

    /**
     * @var boolean allow adding items
     */
    public $canAdd = true;

    /**
     * @var boolean allow removing items
     */
    public $canRemove = true;

    /**
     * @var boolean moving support (sorting)
     */
    public $canMove = false;

    /**
     * @var array models with empty template
     */
    private $_items;

    /**
     * @var array prepared columns with action column
     */
    private $_columns;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if ($this->columns === null) {
            throw new InvalidConfigException('Property "columns" must be set.');
        }

        $this->registerClientScript();
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->prepareItems();
        $this->prepareColumns();

        $dataProvider = new ArrayDataProvider([
            'allModels' => $this->_items,
            'pagination' => false,
        ]);

        $hidden = Html::activeHiddenInput($this->model, $this->attribute, ['value' => '']);
        $button = $this->canAdd ? Html::button($this->addLabel, $this->addButtonOptions) : '';

        $tableOptions = $this->tableOptions;
        Html::addCssClass($options, 'array-input-table');

        $grid = GridView::begin([
            'layout' => $hidden . '{items}' . $button,
            'tableOptions' => $tableOptions,
            'dataProvider' => $dataProvider,
            'showHeader' => false,
            'emptyText' => false,
            'columns' => $this->_columns,
        ]);

        $options = $this->options;
        Html::addCssClass($options, 'array-input');

        $class = $this->itemClass;
        $model = $class === null ? [] : new $class;
        $options['data-array-input-template'] = $grid->renderTableRow($model, 0, 0);

        $grid->options = $options;

        GridView::end();
    }

    /**
     * Register CSS classes and js
     * @return void
     */
    private function registerClientScript()
    {
        ArrayInputAsset::register($this->view);
    }

    /**
     * Prepares items for render in table
     * @return void
     */
    private function prepareItems()
    {
        $attribute = $this->attribute;
        $items = $this->model->$attribute;
        if (!is_array($items)) {
            $items = [];
        }

        $this->_items = $items;
    }

    /**
     * Prepares columns for table
     * @return void
     */
    private function prepareColumns()
    {
        $readOnlyAttribute = $this->readOnlyAttribute;

        $columns = [];
        if ($this->canMove) {
            $columns[] = [
                'class' => MovingColumn::className(),
                'movingDisabledAttribute' => $readOnlyAttribute,
            ];
        }
        foreach ($this->columns as $column) {
            if (is_string($column)) {
                $column = ['attribute' => $column];
            }

            if (empty($column['class'])) {
                $column['class'] = isset($column['items']) ? DropdownInputColumn::className() : TextInputColumn::className();
            }

            if ($this->itemClass === null && empty($column['label'])) {
                $column['label'] = Inflector::camel2words($column['attribute'], true);
            }

            $column = array_merge([
                'readOnlyAttribute' => $readOnlyAttribute,
            ], $column);

            $columns[] = $column;
        }

        if ($this->canRemove) {
            $columns[] = [
                'class' => 'smart\grid\ActionColumn',
                'options' => ['style' => 'width: 25px;'],
                'template' => '{remove}',
                'buttons' => [
                    'remove' => function ($url, $model, $key) use ($readOnlyAttribute) {
                        $readOnly = false;
                        if ($readOnlyAttribute !== null) {
                            $readOnly = ArrayHelper::getValue($model, $readOnlyAttribute);
                        }

                        if ($readOnly) {
                            return '';
                        }

                        return Html::a('<span class="fas fa-remove"></span>', '#', [
                            'class' => 'item-remove',
                            'title' => $this->removeLabel,
                        ]);
                    },
                ],
            ];
        }

        $this->_columns = $columns;
    }

}
