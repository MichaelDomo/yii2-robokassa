# Yii2-robokassa
Yii2 extension for robokassa payment gateway

## Warning!
This extension hasn't been tested yet!!!

## Instalation

Install through composer

```composer require michaeldomo/yii2-robokassa```

```
'components' => [
    'robokassa' => [
        'class' => '\michaeldomo\robokassa\Api',
        'login' => '',
        'firstPassword' => '',
        'secondPassword' => '',
        'isTest' => !YII_ENV_PROD,
    ],
    ...
],
```

## Examples
You can find examples in "examples" folder.

Используйте на свой страх и риск.

Widgets - soon
