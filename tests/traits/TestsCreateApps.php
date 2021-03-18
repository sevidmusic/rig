<?php

namespace tests\traits;

use ddms\classes\command\NewApp;
use ddms\classes\ui\CommandLineUI;
use ddms\interfaces\ui\UserInterface;

trait TestsCreateApps
{

    /** @var array <int, string> $createdApps */
    protected static $createdApps = [];

    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $preparedArguments
     */
    protected static function expectedAppDirectoryPath(array $preparedArguments) : string
    {
        ['flags' => $flags] = $preparedArguments;
        $path = ($flags['ddms-apps-directory-path'][0] ?? DIRECTORY_SEPARATOR . 'tmp') . DIRECTORY_SEPARATOR . ($flags['name'][0] ?? 'BadTestArgToNewAppNameFlagError');
        return $path;
    }

    protected static function removeDirectory(string $dir): void
    {
        if (is_dir($dir)) {
            $contents = scandir($dir);
            $contents = (is_array($contents) ? $contents : []);
            foreach ($contents as $item) {
                if ($item != "." && $item != "..") {
                    $itemPath = $dir . DIRECTORY_SEPARATOR . $item;
                    (is_dir($itemPath) === true && is_link($itemPath) === false)
                        ? self::removeDirectory($itemPath)
                        : unlink($itemPath);
                }
            }
            rmdir($dir);
        }
    }

    protected static function registerAppName(string $appName): void
    {
        array_push(self::$createdApps, $appName);
    }

    protected static function getRandomAppName(): string
    {
        $appName = 'App' . strval(rand(1000,9999));
        self::registerAppName($appName);
        return $appName;
    }

    public static function tearDownAfterClass(): void
    {
        $newApp = new NewApp();
        foreach(self::$createdApps as $appName) {
            $preparedArguments = $newApp->prepareArguments(['--name', $appName]);
            self::removeDirectory(self::expectedAppDirectoryPath($preparedArguments));
        }
    }



}