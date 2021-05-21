<?php

namespace tests\command;

use PHPUnit\Framework\TestCase;
use ddms\classes\command\ConfigureAppOutput;
use ddms\classes\ui\CommandLineUI;
use ddms\interfaces\ui\UserInterface;
use tests\traits\TestsCreateApps;
use \RuntimeException;

final class ConfigureAppOutputTest extends TestCase
{

    use TestsCreateApps;
    private UserInterface $ui;
    private ConfigureAppOutput $configureAppOutput;

    private function getConfigureAppOutput(): ConfigureAppOutput
    {
        if(!isset($this->configureAppOutput)) {
            $this->configureAppOutput = new ConfigureAppOutput();
        }
        return $this->configureAppOutput;
    }

    private function getUserInterface(): UserInterface
    {
        if(!isset($this->ui)) {
            $this->ui = new CommandLineUI();
        }
        return $this->ui;
    }

    public function testRunThrowsRuntimeExceptionIfForAppIsNotSpecified(): void
    {
        $this->expectException(RuntimeException::class);
        $this->getConfigureAppOutput()->run(
            $this->getUserInterface(),
            $this->getConfigureAppOutput()->prepareArguments(['--configure-app-output'])
        );
    }

    public function testRunThrowsRuntimeExceptionIfNameIsNotSpecified(): void
    {
        $appName = $this->getRandomAppName();
        $this->expectException(RuntimeException::class);
        $this->getConfigureAppOutput()->run(
            $this->getUserInterface(),
            $this->getConfigureAppOutput()->prepareArguments(
                ['--configure-app-output', '--for-app', $appName]
            )
        );
    }

    public function testRunThrowsRuntimeExceptionIfNitherOutputOrOutputSourceFileAreSpecified(): void
    {
        $appName = $this->getRandomAppName();
        $outputName = $appName . 'TestRunThrowsEIfOutputNotSpecified';
        $this->expectException(RuntimeException::class);
        $this->getConfigureAppOutput()->run(
            $this->getUserInterface(),
            $this->getConfigureAppOutput()->prepareArguments(
                    ['--configure-app-output', '--for-app', $appName, '--name', $outputName]
            )
        );
    }

    public function testRunCreatesAppSpecifiedByForAppIfAppDoesNotAlreadyExist(): void
    {
        $appName = $this->getRandomAppName();
        $outputName = $appName . 'TestOutputCreatesAppIfAppDoesNotExist';
        $output = $outputName . ' output';
        $prepareArguments = $this->getConfigureAppOutput()->prepareArguments(
            [
                '--configure-app-output',
                '--for-app',
                $appName,
                '--name',
                $outputName,
                '--output',
                $output
            ]
        );
        $expectedAppDirectoryPath = $prepareArguments['flags']['ddms-apps-directory-path'][0] . DIRECTORY_SEPARATOR . $appName;
        $expectedCssDirectoryPath = $expectedAppDirectoryPath . DIRECTORY_SEPARATOR . 'css';
        $expectedJsDirectoryPath = $expectedAppDirectoryPath . DIRECTORY_SEPARATOR . 'js';
        $expectedDynamicOutputDirectoryPath = $expectedAppDirectoryPath . DIRECTORY_SEPARATOR . 'DynamicOutput';
        $expectedOutputComponentsDirectoryPath = $expectedAppDirectoryPath . DIRECTORY_SEPARATOR . 'OutputComponents';
        $expectedResponsesDirectoryPath = $expectedAppDirectoryPath . DIRECTORY_SEPARATOR . 'Responses';
        $expectedRequestsDirectoryPath = $expectedAppDirectoryPath . DIRECTORY_SEPARATOR . 'Requests';
        $expectedResourcesDirectoryPath = $expectedAppDirectoryPath . DIRECTORY_SEPARATOR . 'resources';
        $expectedComponentsPhpFilePath = $expectedAppDirectoryPath . DIRECTORY_SEPARATOR . 'Components.php';
        $this->getConfigureAppOutput()->run($this->getUserInterface(), $prepareArguments);
        $this->assertTrue(is_dir($expectedAppDirectoryPath), "ddms --configure-app-output MUST create the App specified by the --for-app flag if the App does not already exist. An App should have been created at $expectedAppDirectoryPath");
        $this->assertTrue(is_dir($expectedCssDirectoryPath), "ddms --configure-app-output MUST create the App's css directory if the App does not already exist. An css directory for the App should have been created at $expectedCssDirectoryPath");
        $this->assertTrue(is_dir($expectedJsDirectoryPath), "ddms --configure-app-output MUST create the App's js directory if the App does not already exist. An js directory for the App should have been created at $expectedJsDirectoryPath");
        $this->assertTrue(is_dir($expectedDynamicOutputDirectoryPath), "ddms --configure-app-output MUST create the App's DynamicOutput directory if the App does not already exist. An DynamicOutput directory for the App should have been created at $expectedDynamicOutputDirectoryPath");
        $this->assertTrue(is_dir($expectedOutputComponentsDirectoryPath), "ddms --configure-app-output MUST create the App's OutputComponents directory if the App does not already exist. An OutputComponents directory for the App should have been created at $expectedOutputComponentsDirectoryPath");
        $this->assertTrue(is_dir($expectedResponsesDirectoryPath), "ddms --configure-app-output MUST create the App's Responses directory if the App does not already exist. An Responses directory for the App should have been created at $expectedResponsesDirectoryPath");
        $this->assertTrue(is_dir($expectedRequestsDirectoryPath), "ddms --configure-app-output MUST create the App's Requests directory if the App does not already exist. An Requests directory for the App should have been created at $expectedRequestsDirectoryPath");
        $this->assertTrue(is_dir($expectedResourcesDirectoryPath), "ddms --configure-app-output MUST create the App's resources directory if the App does not already exist. An resources directory for the App should have been created at $expectedResourcesDirectoryPath");
        $this->assertTrue(file_exists($expectedComponentsPhpFilePath), "ddms --configure-app-output MUST create the App's Components.php directory if the App does not already exist. An Components.php directory for the App should have been created at $expectedComponentsPhpFilePath");
    }

    public function testRunConfiguresADynamicOutputComponentForTheOutputIfStaticFlagsIsNotSpecified(): void
    {
        $appName = $this->getRandomAppName();
        $outputName = $appName . 'TestRunConfigsDynamicOutputComponentIfStaticNotSpecified';
        $output = $outputName . ' output';
        $prepareArguments = $this->getConfigureAppOutput()->prepareArguments(
            [
                '--configure-app-output',
                '--for-app',
                $appName,
                '--name',
                $outputName,
                '--output',
                $output
            ]
        );
        $expectedAppDirectoryPath = $prepareArguments['flags']['ddms-apps-directory-path'][0] . DIRECTORY_SEPARATOR . $appName;
        $expectedDynamicOutputComponentConfigurationFileFilePath = $expectedAppDirectoryPath . DIRECTORY_SEPARATOR . 'OutputComponents' . DIRECTORY_SEPARATOR . $outputName . '.php';
        $this->getConfigureAppOutput()->run($this->getUserInterface(), $prepareArguments);
        $this->assertTrue(file_exists($expectedDynamicOutputComponentConfigurationFileFilePath), "ddms --configure-app-output MUST configure a DynamicOutputComponent for the output if the --static flag is not specified. A DynamicOutputComponent configuration file should have been created at $expectedDynamicOutputComponentConfigurationFileFilePath");
        $this->assertTrue(str_contains(strval(file_get_contents($expectedDynamicOutputComponentConfigurationFileFilePath)), 'appComponentsFactory->buildDynamicOutputComponent'));
    }

    public function testRunThrowsRuntimeExceptionIfNameIsNotUnique(): void
    {
        $appName = $this->getRandomAppName();
        $outputName = $appName . 'TestOutputComponent';
        $output = $outputName . ' output';
        $prepareArguments = $this->getConfigureAppOutput()->prepareArguments(
            [
                '--configure-app-output',
                '--for-app',
                $appName,
                '--name',
                $outputName,
                '--output',
                $output
            ]
        );
        $this->getConfigureAppOutput()->run($this->getUserInterface(), $prepareArguments);
        $this->expectException(RuntimeException::class);
        $this->getConfigureAppOutput()->run($this->getUserInterface(), $prepareArguments);
    }

    public function testRunThrowsRuntimeExceptionIfSpecifiedOutputSourceFileDoesNotExist(): void
    {
        $appName = $this->getRandomAppName();
        $outputName = $appName . 'TestOutputComponent';
        $badFilePath = __DIR__ . DIRECTORY_SEPARATOR . strval(rand(PHP_INT_MIN, PHP_INT_MAX));
        $prepareArguments = $this->getConfigureAppOutput()->prepareArguments(
            [
                '--configure-app-output',
                '--for-app',
                $appName,
                '--name',
                $outputName,
                '--output-source-file',
                $badFilePath
            ]
        );
        $this->expectException(RuntimeException::class);
        $this->getConfigureAppOutput()->run($this->getUserInterface(), $prepareArguments);
    }

    public function testRunThrowsRuntimeExceptionIfSpecifiedOutputSourceFileIsNotAFile(): void
    {
        $appName = $this->getRandomAppName();
        $outputName = $appName . 'TestOutputComponent';
        $badFilePath = __DIR__;
        $prepareArguments = $this->getConfigureAppOutput()->prepareArguments(
            [
                '--configure-app-output',
                '--for-app',
                $appName,
                '--name',
                $outputName,
                '--output-source-file',
                $badFilePath
            ]
        );
        $this->expectException(RuntimeException::class);
        $this->getConfigureAppOutput()->run($this->getUserInterface(), $prepareArguments);
    }
}
