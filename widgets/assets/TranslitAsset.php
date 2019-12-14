<?php

namespace smart\widgets\assets;

use yii\web\AssetBundle;

class TranslitAsset extends AssetBundle
{
    public $js = [
        'translit.js',
    ];

    /**
     * @inheritdoc
     */
    public $depends = [
        'yii\web\JqueryAsset',
    ];

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = __DIR__ . '/translit';
    }
}
