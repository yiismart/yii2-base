<?php

namespace smart\base;

use yii\filters\AccessControl;
use yii\web\Controller;

class BackendController extends Controller
{

    /**
     * @var array
     */
    public $allowRoles = ['Admin'];

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    ['allow' => true, 'roles' => $this->allowRoles],
                ],
            ],
        ];
    }

}
