<?php

/**
 * Class RobokassaControllerExample
 */
class RobokassaControllerExample extends Controller
{
    /**
     * @inheritdoc
     * @return array
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['call'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'result' => [
                'class' => ResultAction::class,
                'callback' => [$this, 'resultCallback'],
            ],
            'success' => [
                'class' => SuccessAction::class,
                'callback' => [$this, 'successCallback'],
            ],
            'fail' => [
                'class' => FailAction::class,
                'callback' => [$this, 'failCallback'],
            ],
        ];
    }

    /**
     * @return string
     */
    public function actionCall()
    {
        $model = new Transaction();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $api = Yii::$app->get('robokassa');
            $url = $api->buildResponse(
                $model->value,
                $model->id,
                'Оплата',
                Yii::$app->user->getIdentity()->email
            );
            return Yii::$app->response->redirect($url);
        }
        return $this->goBack();
    }

    /**
     * Callback.
     * @param Robokassa $model merchant.
     * @return \yii\web\Response
     */
    public function successCallback($model)
    {
        $this->loadModel($model->InvId)
            ->updateAttributes([
                'status' => Transaction::STATUS_ACCEPTED
            ]);

        return $this->goBack();
    }

    /**
     * Callback.
     * @param Robokassa $model merchant.
     * @return string
     */
    public function resultCallback($model)
    {
        $transaction = $this->loadModel($model->InvId);
        $transaction->updateAttributes([
            'status' => Transaction::STATUS_SUCCESS,
            'data' => $model->getRequest(),
        ]);

        //Do what you want.

        return 'OK' . $model->InvId;
    }

    /**
     * Callback.
     * @param Robokassa $model merchant.
     * @return string
     */
    public function failCallback($model)
    {
        $transaction = $this->loadModel($model->InvId);
        if ($transaction->status === Transaction::STATUS_PENDING) {
            $model->updateAttributes([
                'status' => Transaction::STATUS_FAIL
            ]);
            return 'Ok';
        } else {
            return 'Status has not changed';
        }
    }

    /**
     * @param integer $id
     * @return Transaction
     * @throws BadRequestHttpException
     */
    protected function loadModel($id)
    {
        $model = Transaction::findOne($id);
        if ($model === null) {
            throw new BadRequestHttpException('Model not found');
        }

        return $model;
    }
}
