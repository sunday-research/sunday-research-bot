<?php

declare(strict_types=1);

namespace App\Tests\Module\BotGetUpdates\Fixtures;

use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Entities\Update;
use PHPUnit\Framework\TestCase;

final class ServerResponseFixture
{
    public function __construct(
        private TestCase $testCase
    ) {
    }

    public function create(array $updates): ServerResponse
    {
        // Convert Update objects to raw data if needed
        $rawUpdates = array_map(function($update) {
            return $update instanceof Update ? $update->getRawData() : $update;
        }, $updates);
        
        $responseData = [
            'ok' => true,
            'result' => $rawUpdates,
        ];
        
        return new ServerResponse($responseData, 'sunday_research_bot');
    }

    public function createEmpty(): ServerResponse
    {
        return $this->create([]);
    }

    public function createWithSingleUpdate(Update $update): ServerResponse
    {
        return $this->create([$update]);
    }

    public function createWithError(int $errorCode = 400, string $description = 'Bad Request'): ServerResponse
    {
        $responseData = [
            'ok' => false,
            'error_code' => $errorCode,
            'description' => $description,
        ];
        
        return new ServerResponse($responseData, 'sunday_research_bot');
    }
}
