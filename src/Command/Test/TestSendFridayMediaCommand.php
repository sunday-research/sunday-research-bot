<?php

declare(strict_types=1);

namespace App\Command\Test;

use App\Scheduler\Handler\SendFridayMediaHandler;
use App\Scheduler\Message\SendFridayMediaMessage;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[AsCommand(
    name: 'test:send-friday-media',
    description: 'Test sending Friday media message immediately',
)]
class TestSendFridayMediaCommand extends Command
{
    public function __construct(
        private readonly SendFridayMediaHandler $handler,
        private readonly ParameterBagInterface $parameterBag
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->info('Testing Friday media message sending...');

        try {
            $message = new SendFridayMediaMessage(
                chatId: $this->parameterBag->get('app.telegram.sunday_research_chat_id'),
                media: 'https://media0.giphy.com/media/v1.Y2lkPTc5MGI3NjExanNzemdrcXo4OGI2d3h5eTJ3dzgwMHI0c3ZidnNmdHkzb2tkbnk0biZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/ijaZWxvcVbI5y/giphy.gif',
                caption: 'Happy Sunday! (it\'s test)',
                mediaType: 'animation'
            );
            $this->handler->__invoke($message);
            $io->success('Friday media message sent successfully!');
            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $io->error('Failed to send Friday media message: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
