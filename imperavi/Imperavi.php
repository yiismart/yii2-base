<?php

namespace smart\imperavi;

use Yii;
use yii\helpers\Url;
use vova07\imperavi\Widget;
use smart\storage\components\StorageInterface;

class Imperavi extends Widget
{
    /**
     * @inheritdoc
     */
    public $settings = [
        'minHeight' => 250,
        'toolbarFixedTopOffset' => 56,
        'plugins' => [
            'fullscreen',
            'video',
            'table',
        ],
    ];

    /**
     * @inheritdoc
     */
    public function init()
    {
        if (isset(Yii::$app->storage) && (Yii::$app->storage instanceof StorageInterface)) {
            $this->settings['imageUpload'] = Url::toRoute(['imperavi-image']);
            $this->settings['fileUpload'] = Url::toRoute(['imperavi-file']);
        }
        parent::init();
    }
}
