<?php

namespace smart\grid;

use yii\helpers\Html;

class GridView extends \yii\grid\GridView
{
    /**
     * @inheritdoc
     */
    public $layout = "{items}\n{pager}";

    /**
     * @inheritdoc
     */
    public $options = ['class' => 'grid-view table-responsive'];

    /**
     * @inheritdoc
     */
    public $tableOptions = ['class' => 'table table-bordered table-hover table-sm'];

    /**
     * This property is needed until https://github.com/yiisoft/yii2/issues/17297 will beresolved
     */
    public $headOptions = ['class' => 'thead-light'];

    /**
     * @inheritdoc
     */
    public function renderTableHeader()
    {
        $cells = [];
        foreach ($this->columns as $column) {
            /* @var $column Column */
            $cells[] = $column->renderHeaderCell();
        }
        $content = Html::tag('tr', implode('', $cells), $this->headerRowOptions);
        if ($this->filterPosition === self::FILTER_POS_HEADER) {
            $content = $this->renderFilters() . $content;
        } elseif ($this->filterPosition === self::FILTER_POS_BODY) {
            $content .= $this->renderFilters();
        }
        return Html::tag('thead', $content, $this->headOptions);
    }
}
