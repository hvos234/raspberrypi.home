<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit16f3508247a73b640426e67fbf57f71f
{
    public static $files = array (
        '2cffec82183ee1cea088009cef9a6fc3' => __DIR__ . '/..' . '/ezyang/htmlpurifier/library/HTMLPurifier.composer.php',
    );

    public static $prefixLengthsPsr4 = array (
        'y' => 
        array (
            'yii\\composer\\' => 13,
            'yii\\bootstrap\\' => 14,
            'yii\\' => 4,
        ),
        'd' => 
        array (
            'dosamigos\\datetimepicker\\' => 25,
            'dosamigos\\datepicker\\' => 21,
        ),
        'c' => 
        array (
            'cebe\\markdown\\' => 14,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'yii\\composer\\' => 
        array (
            0 => __DIR__ . '/..' . '/yiisoft/yii2-composer',
        ),
        'yii\\bootstrap\\' => 
        array (
            0 => __DIR__ . '/..' . '/yiisoft/yii2-bootstrap',
        ),
        'yii\\' => 
        array (
            0 => __DIR__ . '/..' . '/yiisoft/yii2',
        ),
        'dosamigos\\datetimepicker\\' => 
        array (
            0 => __DIR__ . '/..' . '/2amigos/yii2-date-time-picker-widget/src',
        ),
        'dosamigos\\datepicker\\' => 
        array (
            0 => __DIR__ . '/..' . '/2amigos/yii2-date-picker-widget/src',
        ),
        'cebe\\markdown\\' => 
        array (
            0 => __DIR__ . '/..' . '/cebe/markdown',
        ),
    );

    public static $prefixesPsr0 = array (
        'H' => 
        array (
            'HTMLPurifier' => 
            array (
                0 => __DIR__ . '/..' . '/ezyang/htmlpurifier/library',
            ),
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit16f3508247a73b640426e67fbf57f71f::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit16f3508247a73b640426e67fbf57f71f::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit16f3508247a73b640426e67fbf57f71f::$prefixesPsr0;

        }, null, ClassLoader::class);
    }
}
