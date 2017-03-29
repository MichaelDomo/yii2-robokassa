<?php

namespace michaeldomo\robokassa\actions;

use yii\base\Action;
use yii\base\InvalidConfigException;

/**
 * Class BaseAction
 * @package michaeldomo\robokassa\actions
 */
class BaseAction extends Action
{
    /**
     * Merchant name. Set in components configuration.
     *
     * @var string
     */
    public $componentName = 'robokassa';

    /**
     * @var string
     */
    public $callback;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->controller->enableCsrfValidation = false;
    }

    /**
     * @param $model \common\components\payment\robokassa\models\Robokassa
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    protected function callback($model)
    {
        if (!is_callable($this->callback)) {
            throw new InvalidConfigException('"' . self::class . '::callback" should be a valid callback.');
        }

        return call_user_func($this->callback, $model);
    }
}
