<?php

declare(strict_types=1);

namespace App\Module\BotGetUpdates\Pipeline\Stage;

use App\Module\BotGetUpdates\Pipeline\Payload;
use App\Module\BotGetUpdates\Service\SubscriberMessageService;
use App\Module\BotGetUpdates\Service\SubscriberService;
use Longman\TelegramBot\Entities\Update;

final readonly class CreateTextMessageStage
{
    public function __construct(
        private SubscriberService $subscriberService,
        private SubscriberMessageService $subscriberMessageService,
    ) {
    }

    public function __invoke(Payload $payload): Payload
    {
        if ($payload->processed) {
            return $payload;
        }

        if ($payload->update->getUpdateType() !== Update::TYPE_MESSAGE) {
            return $payload;
        }

        $message = $payload->update->getMessage();
        if ($message->getType() !== 'text') {
            return $payload;
        }

        $domainSubscriber = $this->subscriberService->create($message->getFrom());
        $this->subscriberMessageService->create($message, $domainSubscriber);

        return $payload->withProcessed(true);
    }
}
