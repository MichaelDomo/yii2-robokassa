<?php

namespace michaeldomo\robokassa;

use Yii;
use yii\base\Component;
use michaeldomo\robokassa\helpers\SignHelper;
use yii\i18n\PhpMessageSource;

/**
 * Class Api
 * @package michaeldomo\robokassa
 */
class Api extends Component
{
    const PASSWORD_TYPE_FIRST = 1;
    const PASSWORD_TYPE_SECOND = 2;
    /**
     * Request url.
     * @var string
     */
    const PAYMENT_ACTION = 'https://auth.robokassa.ru/Merchant/Index.aspx';
    /**
     * Robocassa login.
     * @var string
     */
    public $login;
    /**
     * Robocassa pass 1.
     * @var string
     */
    public $firstPassword;
    /**
     * Robocassa pass 2.
     * @var string
     */
    public $secondPassword;
    /**
     * Is test call, default true.
     * @var bool
     */
    public $isTest = true;
    /**
     * @var string
     */
    public $messagesCategory = 'robokassa';
    /**
     * @var string
     */
    public $logCategory = 'robokassa';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->registerMessages();
    }

    /**
     * @param float $outSum
     * @param integer $invId
     * @param null|string $desc
     * @param null|string $incCurrLabel
     * @param null|string $email
     * @param array $shp
     * @return string
     */
    public function buildResponse($outSum, $invId, $desc = null, $email = null, $incCurrLabel = null, array $shp = [])
    {
        $url = self::PAYMENT_ACTION . '?' . http_build_query([
                'MrchLogin' => $this->login,
                'OutSum' => $outSum,
                'InvId' => $invId,
                'Desc' => $desc,
                'SignatureValue' => $this->createSignature($outSum, $invId, $shp),
                'IncCurrLabel' => $incCurrLabel,
                'Email' => $email,
                'Culture' => explode('-', Yii::$app->language)[0],
                'Encoding' => 'utf-8',
                'IsTest' => (int) $this->isTest,
            ]);

        return $this->addExtraParameters($url, $shp);
    }

    /**
     * @param $outSum
     * @param $invId
     * @param $shp
     * @return string
     */
    private function createSignature($outSum, $invId, $shp)
    {
        $signature = "{$this->login}:{$outSum}:{$invId}:{$this->firstPassword}";
        if (!empty($shp)) {
            $signature .= ':' . SignHelper::implodeShp($shp);
        }

        return md5($signature);
    }

    /**
     * @param $url
     * @param $shp
     * @return string
     */
    private function addExtraParameters($url, $shp)
    {
        if (!empty($shp) && ($query = http_build_query($shp)) !== '') {
            $url .= '&' . $query;
        }

        return $url;
    }

    /**
     * @return void Registers widget translations
     */
    protected function registerMessages()
    {
        if (!array_key_exists($this->messagesCategory, Yii::$app->i18n->translations)) {
            Yii::$app->i18n->translations[$this->messagesCategory] = [
                'class' => PhpMessageSource::class,
                'sourceLanguage' => 'en-US',
                'basePath' => __DIR__ . '/messages',
                'fileMap' => [
                    'robokassa' => 'robokassa.php'
                ],
            ];
        }
    }
}
