<?php

declare(strict_types=1);

namespace App\Command;

use App\Bundle\BotCommands\DTO\SetBotCommandsDTO;
use App\Bundle\BotCommands\Exception\SetBotCommandsException;
use App\Bundle\BotCommands\Service\BotCommandsService;
use App\Domain\BotCommands\Enum\BotCommandsEnum;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:bot-set-commands',
    description: 'Команда для установки боту списка команд',
)]
final class BotSetCommandsCommand extends Command
{
    public function __construct(private readonly BotCommandsService $botCommandsService)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        try {
            $this->botCommandsService->setBotCommands(SetBotCommandsDTO::makeDTO(BotCommandsEnum::getAllCommands()));
            $io->success('setBotCommands method is completed');
            return Command::SUCCESS;
        } catch (SetBotCommandsException $e) {
            $io->error('setBotCommands method is failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
