<?php

declare(strict_types=1);

namespace App\Command\Test;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @deprecated Используй ./bin/console debug:dotenv вместо этой команды
 */
#[AsCommand(
    name: 'test:environment-variables',
    description: 'Выводит на экран значения переменных окружения, используемых в приложении',
)]
final class TestEnvironmentVariablesCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $envVars = $_ENV;
        if (empty($envVars)) {
            $envVars = getenv();
        }

        $io->title('Все переменные окружения:');
        foreach ($envVars as $key => $value) {
            $io->writeln("$key: " . (is_scalar($value) ? (string)$value : ''));
        }

        return Command::SUCCESS;
    }
}
