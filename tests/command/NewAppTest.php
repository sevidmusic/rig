<?php

namespace tests\command;

use PHPUnit\Framework\TestCase;
use ddms\classes\command\NewApp;
use ddms\classes\ui\CommandLineUI;

final class NewAppTest extends TestCase
{
    public function testRunThrowsRuntimeExceptionIf_name_IsNotSpecified() : void
    {
        $newApp = new NewApp();
        $ui = new CommandLineUI();
        $this->expectException(\RuntimeException::class);
        $newApp->run($ui, $newApp->prepareArguments(['--new-app']));
    }

    public function testRunCreatesNewAppDirectoryAtPathAssignedTo_ddms_internal_flag_pwd_Flag(): void
    {
        $newApp = new NewApp();
        $ui = new CommandLineUI();
        $name = 'Foo';
        $argv = ['--new-app', '--name', $name ];
        $preparedArguments = $newApp->prepareArguments($argv);
        ['flags' => $flags] = $preparedArguments;
        $expectedAppDirectoryPath = $flags['ddms-internal-flag-pwd'][0] . DIRECTORY_SEPARATOR . $name;
        $newApp->run($ui, $preparedArguments);
        $this->assertTrue(file_exists($expectedAppDirectoryPath));
        $this->assertTrue(is_dir($expectedAppDirectoryPath));
        $this->removeDirectory($expectedAppDirectoryPath);
    }

    public function testRunCreatesNewAppsCssDirectory(): void
    {
        $newApp = new NewApp();
        $ui = new CommandLineUI();
        $name = 'Foo';
        $argv = ['--new-app', '--name', $name ];
        $preparedArguments = $newApp->prepareArguments($argv);
        ['flags' => $flags] = $preparedArguments;
        $expectedAppDirectoryPath = $flags['ddms-internal-flag-pwd'][0] . DIRECTORY_SEPARATOR . $name;
        $expectedCssDirectoryPath = $expectedAppDirectoryPath . DIRECTORY_SEPARATOR . 'css';
        $newApp->run($ui, $preparedArguments);
        $this->assertTrue(file_exists($expectedCssDirectoryPath));
        $this->assertTrue(is_dir($expectedCssDirectoryPath));
        $this->removeDirectory($expectedAppDirectoryPath);
    }

    private function removeDirectory(string $dir): void
    {
        if (is_dir($dir)) {
            $contents = scandir($dir);
            $contents = (is_array($contents) ? $contents : []);
            foreach ($contents as $item) {
                if ($item != "." && $item != "..") {
                    $itemPath = $dir . DIRECTORY_SEPARATOR . $item;
                    (is_dir($itemPath) === true && is_link($itemPath) === false)
                        ? $this->removeDirectory($itemPath)
                        : unlink($itemPath);
                }
            }
            rmdir($dir);
        }
    }

}
