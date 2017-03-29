<?php

namespace michaeldomo\robokassa\actions;

use Yii;
use michaeldomo\robokassa\models\Robokassa;
use michaeldomo\robokassa\Api;
use yii\web\BadRequestHttpException;

/**
 * Class ResultAction
 * @package michaeldomo\robokassa\actions
 */
class ResultAction extends BaseAction
{
    /**
     * Runs the action.
     * @throws \yii\web\BadRequestHttpException
     */
    public function run()
    {
        $model = new Robokassa(Yii::$app->request->bodyParams, Api::PASSWORD_TYPE_SECOND);
        if ($model->validate()) {
            return $this->callback($model);
        }
        throw new BadRequestHttpException('Invalid request');
    }
}
