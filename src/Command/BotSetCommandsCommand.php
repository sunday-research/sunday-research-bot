<?php

declare(strict_types=1);

namespace App\Command;

use App\Module\BotCommands\DTO\SetBotCommandsDTO;
use App\Module\BotCommands\Enum\BotCommandsEnum;
use App\Module\BotCommands\Exception\SetBotCommandsException;
use App\Module\BotCommands\Service\BotCommandsService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:bot-set-commands',
    description: 'Устанавливает команды Telegram-бота',
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
