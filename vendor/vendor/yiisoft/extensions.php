<?php

$vendorDir = dirname(__DIR__);

return array (
  'yiisoft/yii2-bootstrap' => 
  array (
    'name' => 'yiisoft/yii2-bootstrap',
    'version' => '2.0.6.0',
    'alias' => 
    array (
      '@yii/bootstrap' => $vendorDir . '/yiisoft/yii2-bootstrap',
    ),
  ),
  '2amigos/yii2-date-picker-widget' => 
  array (
    'name' => '2amigos/yii2-date-picker-widget',
    'version' => '1.0.7.0',
    'alias' => 
    array (
      '@dosamigos/datepicker' => $vendorDir . '/2amigos/yii2-date-picker-widget/src',
    ),
  ),
  '2amigos/yii2-date-time-picker-widget' => 
  array (
    'name' => '2amigos/yii2-date-time-picker-widget',
    'version' => '1.0.4.0',
    'alias' => 
    array (
      '@dosamigos/datetimepicker' => $vendorDir . '/2amigos/yii2-date-time-picker-widget/src',
    ),
  ),
);
