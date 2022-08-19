<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInite3c8be8eb452b9952f0a9efe7441ce16
{
    public static $prefixLengthsPsr4 = array (
        'a' => 
        array (
            'app\\' => 4,
        ),
        'W' => 
        array (
            'WilliamCosta\\DotEnv\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'app\\' => 
        array (
            0 => __DIR__ . '/../..' . '/app',
        ),
        'WilliamCosta\\DotEnv\\' => 
        array (
            0 => __DIR__ . '/..' . '/william-costa/dot-env/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInite3c8be8eb452b9952f0a9efe7441ce16::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInite3c8be8eb452b9952f0a9efe7441ce16::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInite3c8be8eb452b9952f0a9efe7441ce16::$classMap;

        }, null, ClassLoader::class);
    }
}