<?php

declare(strict_types=1);

namespace App\Module\BotGetUpdates\Pipeline\Stage;

use App\Module\BotGetUpdates\Pipeline\Payload;
use App\Module\BotGetUpdates\Service\SubscriberMessageService;
use Longman\TelegramBot\Entities\Update;

final readonly class UpdateTextMessageStage
{
    public function __construct(
        private SubscriberMessageService $subscriberMessageService,
    ) {
    }

    public function __invoke(Payload $payload): Payload
    {
        if ($payload->processed) {
            return $payload;
        }

        if ($payload->update->getUpdateType() !== Update::TYPE_EDITED_MESSAGE) {
            return $payload;
        }

        $editedMessage = $payload->update->getEditedMessage();
        if ($editedMessage->getType() !== 'text') {
            return $payload;
        }

        $this->subscriberMessageService->update($editedMessage);

        return $payload->withProcessed(true);
    }
}
