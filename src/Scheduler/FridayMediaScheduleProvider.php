<?php

declare(strict_types=1);

namespace App\Scheduler;

use App\Scheduler\Message\SendFridayMediaMessage;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\RecurringMessage;
use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;

#[AsSchedule('friday_media')]
readonly class FridayMediaScheduleProvider implements ScheduleProviderInterface
{
    public function __construct(private readonly ParameterBagInterface $parameterBag)
    {
    }

    public function getSchedule(): Schedule
    {
        return (new Schedule())
            ->with(
                RecurringMessage::cron('0 8 * * 5', new SendFridayMediaMessage(
                    chatId: $this->parameterBag->get('app.telegram.sunday_research_chat_id'),
                    //media: 'https://media.giphy.com/media/3oEjI6SIIHBdRxXI40/giphy.gif',
                    media: 'https://media0.giphy.com/media/v1.Y2lkPTc5MGI3NjExanNzemdrcXo4OGI2d3h5eTJ3dzgwMHI0c3ZidnNmdHkzb2tkbnk0biZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/ijaZWxvcVbI5y/giphy.gif',
                    caption: 'Happy Friday!',
                    mediaType: 'animation'
                )) // Every Friday at 08:00
            );
    }
}
