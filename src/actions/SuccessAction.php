<?php

namespace michaeldomo\robokassa\actions;

use Yii;
use michaeldomo\robokassa\models\Robokassa;
use michaeldomo\robokassa\Api;
use yii\web\BadRequestHttpException;

/**
 * Class SuccessAction
 * @package michaeldomo\robokassa\actions
 */
class SuccessAction extends BaseAction
{
    /**
     * Runs the action.
     * @throws \yii\web\BadRequestHttpException
     */
    public function run()
    {
        $model = new Robokassa(Yii::$app->request->bodyParams, Api::PASSWORD_TYPE_FIRST);
        if ($model->validate()) {
            return $this->callback($model);
        }
        throw new BadRequestHttpException('Invalid request');
    }
}
