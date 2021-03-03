<?php

namespace ddms\classes\command;

use ddms\interfaces\command\Command as DDMSCommandInterface;
use ddms\abstractions\command\AbstractCommand as DDMSCommandBase;
use ddms\interfaces\ui\UserInterface as DDMSUserInterface;

class DDMSHelp extends DDMSCommandBase implements DDMSCommandInterface
{

    public function run(DDMSUserInterface $ddmsUI, array $preparedArguments = ['flags' => [], 'options' => []]): bool
    {
        $ddmsUI->notify($this->getHelpFileOutput('help'), 'help');
        return true;
    }

    private function getHelpFileOutput(string $helpFlagName): string
    {
        if(file_exists($this->determineHelpFilePath($helpFlagName)))
        {
            $output = file_get_contents($this->determineHelpFilePath($helpFlagName));
        }
        return (isset($output) && is_string($output) ? PHP_EOL . "\e[0m\e[45m\e[30m" . $output . "\e[0m" . PHP_EOL : '');
    }

    private function determineHelpFilePath(string $helpFlagName): string
    {
        return str_replace('ddms/classes/command','helpFiles', __DIR__) . DIRECTORY_SEPARATOR . $helpFlagName . '.txt';
    }

}
