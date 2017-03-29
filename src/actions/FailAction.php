<?php

namespace michaeldomo\robokassa\actions;

use Yii;
use michaeldomo\robokassa\models\Robokassa;
use yii\web\BadRequestHttpException;

/**
 * Class FailAction
 * @package michaeldomo\robokassa\actions
 */
class FailAction extends BaseAction
{
    /**
     * Runs the action.
     * @throws \yii\web\BadRequestHttpException
     */
    public function run()
    {
        $model = new Robokassa(Yii::$app->request->bodyParams);
        if ($model->validate(['InvId', 'OutSum'])) {
            return $this->callback($model);
        }
        throw new BadRequestHttpException('Invalid request');
    }
}
