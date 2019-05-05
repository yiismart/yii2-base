<?php

namespace smart\imperavi;

use Yii;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;

trait ImperaviControllerTrait
{
    /**
     * @inheritdoc
     * Disable csrf validation for image and file uploading
     */
    public function beforeAction($action)
    {
        if ($action->id == 'imperavi-image' || $action->id == 'imperavi-file') {
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }

    /**
     * Image upload
     * @return string
     */
    public function actionImperaviImage()
    {
        $name = Yii::$app->storage->prepare('file', [
            'image/png',
            'image/jpg',
            'image/gif',
            'image/jpeg',
            'image/pjpeg',
        ]);

        if ($name === false) {
            throw new BadRequestHttpException(Yii::t('cms', 'Error occurred while image uploading.'));
        }

        return Json::encode([
            ['filelink' => $name],
        ]);
    }

    /**
     * File upload
     * @return string
     */
    public function actionImperaviFile()
    {
        $name = Yii::$app->storage->prepare('file');

        if ($name === false) {
            throw new BadRequestHttpException(Yii::t('cms', 'Error occurred while file uploading.'));
        }

        return Json::encode([
            ['filelink' => $name, 'filename' => urldecode(basename($name))],
        ]);
    }
}
