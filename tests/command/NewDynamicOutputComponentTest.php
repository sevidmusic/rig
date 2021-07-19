<?php

namespace tests\command;

use PHPUnit\Framework\TestCase;
use ddms\classes\command\NewApp;
use ddms\classes\command\NewDynamicOutputComponent;
use ddms\classes\ui\CommandLineUI;
use ddms\interfaces\ui\UserInterface;
use \RuntimeException;
use tests\traits\TestsCreateApps;

final class NewDynamicOutputComponentTest extends TestCase
{

    use TestsCreateApps;

    public function testTest(): void
    {
        $this->assertTrue(true);
    }

    public function testRunThrowsRuntimeExceptionIf_name_IsNotSpecified(): void
    {
        $newDynamicOutputComponent = new NewDynamicOutputComponent();
        $this->expectException(RuntimeException::class);
        $newDynamicOutputComponent->run(new CommandLineUI(), $newDynamicOutputComponent->prepareArguments([]));
    }

    public function testRunThrowsRuntimeExceptionIf_for_app_IsNotSpecified(): void
    {
        $newDynamicOutputComponent = new NewDynamicOutputComponent();
        $this->expectException(RuntimeException::class);
        $newDynamicOutputComponent->run(new CommandLineUI(), $newDynamicOutputComponent->prepareArguments(['--name', 'Foo']));
    }

    public function testRunThrowsRuntimeExceptionIfSpecifiedAppDoesNotExist(): void
    {
        $newDynamicOutputComponent = new NewDynamicOutputComponent();
        $this->expectException(RuntimeException::class);
        $newDynamicOutputComponent->run(new CommandLineUI(), $newDynamicOutputComponent->prepareArguments(['--name', 'Foo', '--for-app', 'Baz' . strval(rand(10000,9999))]));
    }

    public function testRunCreatesNewDynamicOutputComponentForSpecifiedApp(): void
    {
        $appName = $this->createTestAppReturnName();
        $dynamicOutputComponentName = $appName . 'DynamicOutputComponent';
        $newDynamicOutputComponent = new NewDynamicOutputComponent();
        $preparedArguments = $newDynamicOutputComponent->prepareArguments(['--name', $dynamicOutputComponentName, '--for-app', $appName]);
        $newDynamicOutputComponent->run(new CommandLineUI(), $preparedArguments);
        $this->assertTrue(file_exists($this->expectedDynamicOutputComponentPath($preparedArguments)));
    }

    public function testRunSetsContainerTo_DynamicOutputComponents_IfContainerIsNotSpecified(): void
    {
        $appName = $this->createTestAppReturnName();
        $dynamicOutputComponentName = $appName . 'DynamicOutputComponent';
        $newDynamicOutputComponent = new NewDynamicOutputComponent();
        $preparedArguments = $newDynamicOutputComponent->prepareArguments(['--name', $dynamicOutputComponentName, '--for-app', $appName]);
        $newDynamicOutputComponent->run(new CommandLineUI(), $preparedArguments);
        $this->assertEquals($this->determineExpectedDynamicOutputComponentPhpContent($preparedArguments), $this->getNewDynamicOutputComponentContent($preparedArguments));
    }

    public function testRunSetsContainerTo_DynamicOutputComponents_IfContainerIsSpecifiedWithNoValue(): void
    {
        $appName = $this->createTestAppReturnName();
        $dynamicOutputComponentName = $appName . 'DynamicOutputComponent';
        $newDynamicOutputComponent = new NewDynamicOutputComponent();
        $preparedArguments = $newDynamicOutputComponent->prepareArguments(['--name', $dynamicOutputComponentName, '--for-app', $appName, '--container']);
        $newDynamicOutputComponent->run(new CommandLineUI(), $preparedArguments);
        $this->assertEquals($this->determineExpectedDynamicOutputComponentPhpContent($preparedArguments), file_get_contents($this->expectedDynamicOutputComponentPath($preparedArguments)));
    }

    public function testRunThrowsRuntimeExceptionIfSpecifiedContainerIsNotAlphaNumeric(): void
    {
        $appName = $this->createTestAppReturnName();
        $dynamicOutputComponentName = $appName . 'DynamicOutputComponent';
        $newDynamicOutputComponent = new NewDynamicOutputComponent();
        $this->expectException(RuntimeException::class);
        $newDynamicOutputComponent->run(new CommandLineUI(), $newDynamicOutputComponent->prepareArguments(['--name', $dynamicOutputComponentName, '--for-app', $appName, '--container', 'FooBarBaz*#$%*']));
    }

    public function testRunSetsContainerToSpecifiedContainerIfSpecifiedContainerIsAlphaNumeric(): void
    {
        $appName = $this->createTestAppReturnName();
        $dynamicOutputComponentName = $appName . 'DynamicOutputComponent';
        $newDynamicOutputComponent = new NewDynamicOutputComponent();
        $preparedArguments = $newDynamicOutputComponent->prepareArguments(['--name', $dynamicOutputComponentName, '--for-app', $appName, '--container', 'ValidContainerName']);
        $newDynamicOutputComponent->run(new CommandLineUI(), $preparedArguments);
        $this->assertEquals($this->determineExpectedDynamicOutputComponentPhpContent($preparedArguments), file_get_contents($this->expectedDynamicOutputComponentPath($preparedArguments)));
    }

    public function testRunSetsPositionTo_0_IfPositionIsNotSpecified(): void
    {
        $appName = $this->createTestAppReturnName();
        $dynamicOutputComponentName = $appName . 'DynamicOutputComponent';
        $newDynamicOutputComponent = new NewDynamicOutputComponent();
        $preparedArguments = $newDynamicOutputComponent->prepareArguments(['--name', $dynamicOutputComponentName, '--for-app', $appName]);
        $newDynamicOutputComponent->run(new CommandLineUI(), $preparedArguments);
        $this->assertEquals($this->determineExpectedDynamicOutputComponentPhpContent($preparedArguments), $this->getNewDynamicOutputComponentContent($preparedArguments));
    }

    public function testRunSetsPositionTo_0_IfPositionIsSpecifiedWithNoValue(): void
    {
        $appName = $this->createTestAppReturnName();
        $dynamicOutputComponentName = $appName . 'DynamicOutputComponent';
        $newDynamicOutputComponent = new NewDynamicOutputComponent();
        $preparedArguments = $newDynamicOutputComponent->prepareArguments(['--name', $dynamicOutputComponentName, '--for-app', $appName, '--position']);
        $newDynamicOutputComponent->run(new CommandLineUI(), $preparedArguments);
        $this->assertEquals($this->determineExpectedDynamicOutputComponentPhpContent($preparedArguments), file_get_contents($this->expectedDynamicOutputComponentPath($preparedArguments)));
    }

    public function testRunThrowsRuntimeExceptionIfSpecifiedPositionIsNotNumeric(): void
    {
        $appName = $this->createTestAppReturnName();
        $dynamicOutputComponentName = $appName . 'DynamicOutputComponent';
        $newDynamicOutputComponent = new NewDynamicOutputComponent();
        $this->expectException(RuntimeException::class);
        $newDynamicOutputComponent->run(new CommandLineUI(), $newDynamicOutputComponent->prepareArguments(['--name', $dynamicOutputComponentName, '--for-app', $appName, '--position', 'FooBarBaz']));
    }

    public function testRunSetsPositionToSpecifiedPositionIfSpecifiedPositionIsNumeric(): void
    {
        $appName = $this->createTestAppReturnName();
        $dynamicOutputComponentName = $appName . 'DynamicOutputComponent';
        $newDynamicOutputComponent = new NewDynamicOutputComponent();
        $preparedArguments = $newDynamicOutputComponent->prepareArguments(['--name', $dynamicOutputComponentName, '--for-app', $appName, '--position', '420']);
        $newDynamicOutputComponent->run(new CommandLineUI(), $preparedArguments);
        $this->assertEquals($this->determineExpectedDynamicOutputComponentPhpContent($preparedArguments), file_get_contents($this->expectedDynamicOutputComponentPath($preparedArguments)));
    }

    public function testRunThrowsRuntimeExceptionIfDynamicOutputComponentAlreadyExists(): void
    {
        $appName = $this->createTestAppReturnName();
        $dynamicOutputComponentName = $appName . 'DynamicOutputComponent';
        $newDynamicOutputComponent = new NewDynamicOutputComponent();
        $preparedArguments = $newDynamicOutputComponent->prepareArguments(['--name', $dynamicOutputComponentName, '--for-app', $appName]);
        $newDynamicOutputComponent->run(new CommandLineUI(), $preparedArguments);
        $this->expectException(RuntimeException::class);
        $newDynamicOutputComponent->run(new CommandLineUI(), $preparedArguments);
    }

    public function testRunThrowsRuntimeExpceptionIfSpecifiedNameIsNotAlphaNumeric(): void
    {
        $appName = $this->createTestAppReturnName();
        $dynamicOutputComponentName = $appName . 'DynamicOutputComponent';
        $newDynamicOutputComponent = new NewDynamicOutputComponent();
        $preparedArguments = $newDynamicOutputComponent->prepareArguments(['--name', $dynamicOutputComponentName . '!@#$%^&*()_+=-\][\';"\\,.', '--for-app', $appName]);
        $this->expectException(RuntimeException::class);
        $newDynamicOutputComponent->run(new CommandLineUI(), $preparedArguments);
    }

    public function testRunSetsNameToSpecifiedNameIfSpecifiedNameIsAlphaNumeric(): void
    {
        $appName = $this->createTestAppReturnName();
        $dynamicOutputComponentName = $appName . 'DynamicOutputComponent';
        $newDynamicOutputComponent = new NewDynamicOutputComponent();
        $preparedArguments = $newDynamicOutputComponent->prepareArguments(['--name', $dynamicOutputComponentName, '--for-app', $appName]);
        $newDynamicOutputComponent->run(new CommandLineUI(), $preparedArguments);
        $this->assertEquals($this->determineExpectedDynamicOutputComponentPhpContent($preparedArguments), file_get_contents($this->expectedDynamicOutputComponentPath($preparedArguments)));
    }

    public function testRunSets_for_app_As_app_name(): void
    {
        $appForApp = $this->createTestAppReturnName();
        $dynamicOutputComponentForApp = $appForApp . 'DynamicOutputComponent';
        $newDynamicOutputComponent = new NewDynamicOutputComponent();
        $preparedArguments = $newDynamicOutputComponent->prepareArguments(['--name', $dynamicOutputComponentForApp, '--for-app', $appForApp]);
        $newDynamicOutputComponent->run(new CommandLineUI(), $preparedArguments);
        $this->assertEquals($this->determineExpectedDynamicOutputComponentPhpContent($preparedArguments), file_get_contents($this->expectedDynamicOutputComponentPath($preparedArguments)));
    }

    public function testRunSetsDynamicOutputFileTo_name_withExtension_php_IfFileNameIsNotSpecified(): void
    {
        $appName = $this->createTestAppReturnName();
        $dynamicOutputComponentName = $appName . 'DynamicOutputComponent';
        $newDynamicOutputComponent = new NewDynamicOutputComponent();
        $preparedArguments = $newDynamicOutputComponent->prepareArguments(['--name', $dynamicOutputComponentName, '--for-app', $appName]);
        $newDynamicOutputComponent->run(new CommandLineUI(), $preparedArguments);
        $this->assertEquals($this->determineExpectedDynamicOutputComponentPhpContent($preparedArguments), $this->getNewDynamicOutputComponentContent($preparedArguments));
    }

    public function testRunCreatesDynamicOutputFileNamed_name_WithExtension_php_InAppsDynamicOutputDirectoryIfFileNameIsNotSpecified(): void
    {
        $appName = $this->createTestAppReturnName();
        $dynamicOutputComponentName = $appName . 'DynamicOutputComponent';
        $newDynamicOutputComponent = new NewDynamicOutputComponent();
        $preparedArguments = $newDynamicOutputComponent->prepareArguments(['--name', $dynamicOutputComponentName, '--for-app', $appName]);
        $newDynamicOutputComponent->run(new CommandLineUI(), $preparedArguments);
        $this->assertTrue(
            file_exists(
                $this->expectedDynamicOutputFileDirectoryPath($preparedArguments) . DIRECTORY_SEPARATOR . $dynamicOutputComponentName . '.php'
            )
        );
    }

    public function testRunCreatesDynamicOutputFileNamed_name_WithExtension_php_InSharedDynamicOutputDirectoryIfSharedFlagsIsPresentAndFileNameIsNotSpecified(): void
    {
        $appName = $this->createTestAppReturnName();
        $dynamicOutputComponentName = $appName . 'DynamicOutputComponent';
        $newDynamicOutputComponent = new NewDynamicOutputComponent();
        $preparedArguments = $newDynamicOutputComponent->prepareArguments(['--name', $dynamicOutputComponentName, '--for-app', $appName, '--shared']);
        $newDynamicOutputComponent->run(new CommandLineUI(), $preparedArguments);
        $this->assertTrue(
            file_exists(
                $this->expectedDynamicOutputFileDirectoryPath($preparedArguments) . DIRECTORY_SEPARATOR . $dynamicOutputComponentName . '.php'
            )
        );
    }

    public function testRunSetsDynamicOutputFileTo_file_name_IfFileNameIsSpecified(): void
    {
        $appName = $this->createTestAppReturnName();
        $dynamicOutputComponentName = $appName . 'DynamicOutputComponent';
        $newDynamicOutputComponent = new NewDynamicOutputComponent();
        $preparedArguments = $newDynamicOutputComponent->prepareArguments(['--name', $dynamicOutputComponentName, '--for-app', $appName, '--file-name', 'FooBar' . strval(rand(420, 4200))]);
        $newDynamicOutputComponent->run(new CommandLineUI(), $preparedArguments);
        $this->assertEquals($this->determineExpectedDynamicOutputComponentPhpContent($preparedArguments), $this->getNewDynamicOutputComponentContent($preparedArguments));
    }

    public function testRunCreatesDynamicOutputFileNamed_file_name_InAppsDynamicOutputDirectoryIfFileNameIsSpecified(): void
    {
        $appName = $this->createTestAppReturnName();
        $fileName = 'FooBarBaz' . strval(rand(420, 4200)) . '.html';
        $dynamicOutputComponentName = $appName . 'DynamicOutputComponent';
        $newDynamicOutputComponent = new NewDynamicOutputComponent();
        $preparedArguments = $newDynamicOutputComponent->prepareArguments(['--name', $dynamicOutputComponentName, '--for-app', $appName, '--file-name', $fileName]);
        $newDynamicOutputComponent->run(new CommandLineUI(), $preparedArguments);
        $this->assertTrue(
            file_exists(
                $this->expectedDynamicOutputFileDirectoryPath($preparedArguments) . DIRECTORY_SEPARATOR . $fileName
            )
        );
    }

    public function testRunCreatesDynamicOutputFileNamed_file_name_InSharedDynamicOutputDirectoryIfSharedFlagIsPresentAndFileNameIsSpecified(): void
    {
        $appName = $this->createTestAppReturnName();
        $fileName = 'FooBarBaz' . strval(rand(420, 4200)) . '.html';
        $dynamicOutputComponentName = $appName . 'DynamicOutputComponent';
        $newDynamicOutputComponent = new NewDynamicOutputComponent();
        $preparedArguments = $newDynamicOutputComponent->prepareArguments(['--name', $dynamicOutputComponentName, '--for-app', $appName, '--file-name', $fileName, '--shared']);
        $newDynamicOutputComponent->run(new CommandLineUI(), $preparedArguments);
        $this->assertTrue(
            file_exists(
                $this->expectedDynamicOutputFileDirectoryPath($preparedArguments) . DIRECTORY_SEPARATOR . $fileName
            )
        );
    }

    public function testRunDoesNotCreateDynamicOutputFileInAppsDynamicOutputDirectoryIfDynamicOutputFileAlreadyExists(): void
    {
        $appName = $this->createTestAppReturnName();
        $fileName = 'FooBarBaz' . strval(rand(420, 4200)) . '.html';
        $dynamicOutputComponentName = $appName . 'DynamicOutputComponent';
        $newDynamicOutputComponent = new NewDynamicOutputComponent();
        $preparedArguments = $newDynamicOutputComponent->prepareArguments(['--name', $dynamicOutputComponentName, '--for-app', $appName, '--file-name', $fileName]);
        $expectedFilePath = $this->expectedDynamicOutputFileDirectoryPath($preparedArguments) . DIRECTORY_SEPARATOR . $fileName;
        $expectedContent = 'Expected Content' . strval(rand(420, PHP_INT_MAX));
        file_put_contents($expectedFilePath, $expectedContent);
        $newDynamicOutputComponent->run(new CommandLineUI(), $preparedArguments);
        $this->assertTrue(file_exists($expectedFilePath));
        $this->assertEquals($expectedContent, file_get_contents($expectedFilePath));
    }

    public function testRunDoesNotCreateDynamicOutputFileInSharedDynamicOutputDirectoryIfSharedFlagIsPresentAndDynamicOutputFileAlreadyExists(): void
    {
        $appName = $this->createTestAppReturnName();
        $fileName = 'FooBarBaz' . strval(rand(420, 4200)) . '.html';
        $dynamicOutputComponentName = $appName . 'DynamicOutputComponent';
        $newDynamicOutputComponent = new NewDynamicOutputComponent();
        $preparedArguments = $newDynamicOutputComponent->prepareArguments(['--name', $dynamicOutputComponentName, '--for-app', $appName, '--file-name', $fileName, '--shared']);
        $expectedFilePath = $this->expectedDynamicOutputFileDirectoryPath($preparedArguments) . DIRECTORY_SEPARATOR . $fileName;
        $expectedContent = 'Expected Content' . strval(rand(420, PHP_INT_MAX));
        file_put_contents($expectedFilePath, $expectedContent);
        $newDynamicOutputComponent->run(new CommandLineUI(), $preparedArguments);
        $this->assertTrue(file_exists($expectedFilePath));
        $this->assertEquals($expectedContent, file_get_contents($expectedFilePath));
    }

/**
testRunDoesNotCreateDynamicOutputFileInSharedDynamicOutputDirectoryIfSharedFlagIsPresentAndDynamicOutputFileAlreadyExists()

**/
    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $preparedArguments
     */
    private function expectedDynamicOutputFileDirectoryPath(array $preparedArguments): string
    {
        return (
            isset($preparedArguments['flags']['shared'])
            ? $this->expectedSharedDynamicOutputDirectoryPath($preparedArguments)
            : $this->expectedAppDynamicOutputDirectoryPath($preparedArguments)
        );
    }

    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $preparedArguments
     */
    private function expectedAppDynamicOutputDirectoryPath(array $preparedArguments): string
    {
        return $this->expectedAppDirectoryPath($preparedArguments) . DIRECTORY_SEPARATOR . 'DynamicOutput';
    }

    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $preparedArguments
     */
    private function expectedSharedDynamicOutputDirectoryPath(array $preparedArguments): string
    {
        return str_replace(['Apps', 'tmp'], 'SharedDynamicOutput', $preparedArguments['flags']['path-to-apps-directory'][0]);
    }


    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $preparedArguments
     */
    private function getNewDynamicOutputComponentContent($preparedArguments): string
    {
        return strval(file_get_contents($this->expectedDynamicOutputComponentPath($preparedArguments)));
    }

    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $preparedArguments
     */
    private function expectedDynamicOutputComponentPath(array $preparedArguments): string
    {
        return self::expectedAppDirectoryPath($preparedArguments) . DIRECTORY_SEPARATOR . 'OutputComponents' . DIRECTORY_SEPARATOR . $preparedArguments['flags']['name'][0] . '.php';
    }

    private function createTestAppReturnName(): string
    {
        $appName = self::getRandomAppName();
        $newApp = new NewApp();
        $newAppPreparedArguments = $newApp->prepareArguments(['--name', $appName]);
        $newApp->run(new CommandLineUI(), $newAppPreparedArguments);
        return $appName;
    }

    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $preparedArguments
     */
    private function determineExpectedDynamicOutputComponentPhpContent(array $preparedArguments): string
    {
        return str_replace(
            [
                '_NAME_',
                '_POSITION_',
                '_CONTAINER_',
                '_FOR_APP_',
                '_DYNAMIC_OUTPUT_FILE_',
            ],
            [
                $preparedArguments['flags']['name'][0],
                ($preparedArguments['flags']['position'][0] ?? '0'),
                ($preparedArguments['flags']['container'][0] ?? 'DynamicOutputComponents'),
                $preparedArguments['flags']['for-app'][0],
                ($preparedArguments['flags']['file-name'][0] ?? $preparedArguments['flags']['name'][0] . '.php'),
            ],
            strval(file_get_contents($this->expectedTemplateFilePath()))
        );
    }

    private function expectedTemplateFilePath(): string
    {
        return str_replace('tests' . DIRECTORY_SEPARATOR . 'command', 'FileTemplates', __DIR__) . DIRECTORY_SEPARATOR . 'DynamicOutputComponent.php';
    }

################

    public function testRunThrowsRuntimeExpceptionIf_initial_output_FlagIsSpecifiedAndCreationOfNewDynamicOutputFileWouldOverwriteAnExistingDynamicOutputFile(): void
    {
        $appName = $this->createTestAppReturnName();
        $arguments = [
            '--name', 'FirstDynamicOutputComponent',
            '--file-name', 'dynamicOutputFile.txt',
            '--for-app', $appName,
            '--initial-output', 'Foo bar baz'
        ];
        $newDynamicOutputComponent = new NewDynamicOutputComponent();
        $preparedArguments = $newDynamicOutputComponent->prepareArguments($arguments);
        $newDynamicOutputComponent->run(new CommandLineUI(), $preparedArguments);
        $arguments['--name'] = 'SecondDynamicOutputComponent';
        $preparedArguments = $newDynamicOutputComponent->prepareArguments($arguments);
        $this->expectException(RuntimeException::class);
        $newDynamicOutputComponent->run(new CommandLineUI(), $preparedArguments);
    }

    public function testRunThrowsRuntimeExpceptionIf_initial_output_file_FlagIsSpecifiedAndCreationOfNewDynamicOutputFileWouldOverwriteAnExistingDynamicOutputFile(): void
    {
        $appName = $this->createTestAppReturnName();
        $arguments = [
            '--name', 'FirstDynamicOutputComponent',
            '--file-name', 'dynamicOutputFile.txt',
            '--for-app', $appName,
            '--initial-output-file', __FILE__
        ];
        $newDynamicOutputComponent = new NewDynamicOutputComponent();
        $preparedArguments = $newDynamicOutputComponent->prepareArguments($arguments);
        $newDynamicOutputComponent->run(new CommandLineUI(), $preparedArguments);
        $arguments['--name'] = 'SecondDynamicOutputComponent';
        $preparedArguments = $newDynamicOutputComponent->prepareArguments($arguments);
        $this->expectException(RuntimeException::class);
        $newDynamicOutputComponent->run(new CommandLineUI(), $preparedArguments);
    }

    public function testRunThrowsRuntimeExpceptionIfSpecified_initial_output_file_DoesNotExist(): void
    {
        $appName = $this->createTestAppReturnName();
        $arguments = [
            '--name', 'FirstDynamicOutputComponent',
            '--file-name', 'dynamicOutputFile.txt',
            '--for-app', $appName,
            '--initial-output-file', 'Foo' . strval(rand(420, 4200))
        ];
        $newDynamicOutputComponent = new NewDynamicOutputComponent();
        $preparedArguments = $newDynamicOutputComponent->prepareArguments($arguments);
        $this->expectException(RuntimeException::class);
        $newDynamicOutputComponent->run(new CommandLineUI(), $preparedArguments);
    }

    public function testRunThrowsRuntimeExpceptionIfBoth_initial_output_And_initial_output_file_FlagsAreSpecified(): void
    {
        $appName = $this->createTestAppReturnName();
        $arguments = [
            '--name', $appName . 'DynamicOutputComponent',
            '--for-app', $appName,
            '--initial-output', 'Foo bar baz',
            '--initial-output-file', __FILE__
        ];
        $newDynamicOutputComponent = new NewDynamicOutputComponent();
        $preparedArguments = $newDynamicOutputComponent->prepareArguments($arguments);
        $this->expectException(RuntimeException::class);
        $newDynamicOutputComponent->run(new CommandLineUI(), $preparedArguments);
    }

    public function testRunSetDynamicOutputFilesContentToSpecifiedInitialOutputIf_initial_output_FlagIsSpecified(): void
    {
        $appName = $this->createTestAppReturnName();
        $initialOutput = 'Foo bar ' . strval(rand(420, 4200));
        $dynamicOutputComponentName = $appName . 'DynamicOutputComponent';
        $fileName = $dynamicOutputComponentName . '.html';
        $newDynamicOutputComponent = new NewDynamicOutputComponent();
        $preparedArguments = $newDynamicOutputComponent->prepareArguments(['--name', $dynamicOutputComponentName, '--for-app', $appName, '--initial-output', $initialOutput, '--file-name', $fileName]);
        $newDynamicOutputComponent->run(new CommandLineUI(), $preparedArguments);
        $this->assertEquals($initialOutput, $this->getDynamicOutputFileContents($fileName, $preparedArguments));
    }

    public function testRunSetDynamicOutputFilesContentToMatchSpecifiedFilesContentIf_initial_output_file_FlagIsSpecified(): void
    {
        $appName = $this->createTestAppReturnName();
        $expectedOutputFilePath = __FILE__;
        $expectedOutput = strval(file_get_contents($expectedOutputFilePath));
        $dynamicOutputComponentName = $appName . 'DynamicOutputComponent';
        $fileName = $dynamicOutputComponentName . '.php';
        $newDynamicOutputComponent = new NewDynamicOutputComponent();
        $preparedArguments = $newDynamicOutputComponent->prepareArguments(['--name', $dynamicOutputComponentName, '--for-app', $appName, '--initial-output-file', $expectedOutputFilePath]);
        $newDynamicOutputComponent->run(new CommandLineUI(), $preparedArguments);
        $this->assertEquals($expectedOutput, $this->getDynamicOutputFileContents($fileName, $preparedArguments));
    }

    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $preparedArguments
     */
    private function getDynamicOutputFileContents(string $fileName, array $preparedArguments): string
    {
        return strval(file_get_contents($this->determineDynamicOutputFilePath($fileName, $preparedArguments)));
    }

    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $preparedArguments
     */
    private function determineDynamicOutputFilePath(string $fileName, array $preparedArguments): string
    {
        $dynamicOutputFilePath = $this->expectedAppDynamicOutputDirectoryPath($preparedArguments) . DIRECTORY_SEPARATOR . $fileName;
        if(!file_exists($dynamicOutputFilePath)) {
            throw new RuntimeException('  NewDynamicOutputComponentTest Error: A dynamic output file does not exist at the expected path: ' . $dynamicOutputFilePath);
        }
        return $dynamicOutputFilePath;
    }

}

