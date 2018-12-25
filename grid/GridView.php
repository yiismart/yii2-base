<?php

namespace smart\grid;

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

}
