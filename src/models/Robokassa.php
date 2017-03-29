<?php

namespace michaeldomo\robokassa\models;

use Yii;
use michaeldomo\robokassa\helpers\SignHelper;

/**
 * Class Robokassa
 * @package michaeldomo\robokassa\models
 *
 * @property array $request
 * @property string $password
 */
class Robokassa extends BaseModel
{
    /**
     * Transaction id.
     * @var string
     */
    public $InvId;
    /**
     * Sum of transaction.
     * @var string
     */
    public $OutSum;
    /**
     * Signature.
     * @var string
     */
    public $SignatureValue;
    /**
     * Request params from remote host.
     * @var array
     */
    private $_request;
    /**
     * Password for sign validation.
     * @var string
     */
    private $_password;

    /**
     * Robokassa constructor.
     * @param integer $passwordType
     * @param $request
     * @param array $config
     */
    public function __construct($request, $passwordType = null, $config = [])
    {
        $component = $this->getComponent();
        $this->_password = ($passwordType === $component::PASSWORD_TYPE_FIRST) ?
            $component->firstPassword :
            $component->secondPassword;

        $this->_request = $request;
        $this->load($request, '');
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[
                'SignatureValue',
                'OutSum',
                'InvId'
            ], 'required'],
            ['SignatureValue', 'validateSign']
        ];
    }

    /**
     * Checking the MD5 sign.
     * @param array $attribute payment parameters
     *
     * @return void if MD5 hash is correct
     */
    public function validateSign($attribute)
    {
        $signature = $this->_generateSignature();
        Yii::info(
            Yii::t($this->getComponent()->messagesCategory, 'String to md5: {md5}', ['md5' => $signature]),
            $this->getComponent()->logCategory
        );
        if (($md5 = strtolower(md5($signature))) === strtolower($this->$attribute)) {
            return;
        }
        Yii::error(
            Yii::t($this->getComponent()->messagesCategory, 'Wait for md5: {md5}, received md5: {recievedmd5}', [
                'md5' => $md5,
                'recievedmd5' => $this->$attribute
            ]),
            $this->getComponent()->logCategory
        );
        $this->addError(
            $attribute,
            Yii::t($this->getComponent()->messagesCategory, 'Security check failed: wrong MD5 hash')
        );
    }

    /**
     * @return bool
     */
    private function _generateSignature()
    {
        $shp = $this->_generateShpFromRequest();
        $signature = "{$this->OutSum}:{$this->InvId}:{$this->_password}";
        if (!empty($shp)) {
            $signature .= ':' . SignHelper::implodeShp($shp);
        }

        return $signature;
    }

    /**
     * @return array
     */
    private function _generateShpFromRequest()
    {
        $shp = [];
        foreach ($this->_request as $key => $param) {
            if (strpos(strtolower($key), 'shp') === 0) {
                $shp[$key] = $param;
            }
        }

        return $shp;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->_password;
    }

    /**
     * @return array
     */
    public function getRequest()
    {
        return $this->_request;
    }
}
