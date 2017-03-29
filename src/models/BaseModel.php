<?php

namespace michaeldomo\robokassa\models;

use yii\base\Model;
use Yii;

/**
 * Class BaseModel
 * @package michaeldomo\robokassa\models
 *
 * @property \michaeldomo\robokassa\Api $component
 */
class BaseModel extends Model
{
    /**
     * @var string
     */
    public $componentName = 'robokassa';

    /**
     * @return \michaeldomo\robokassa\Api|object;
     */
    public function getComponent()
    {
        return Yii::$app->get($this->componentName);
    }
}
