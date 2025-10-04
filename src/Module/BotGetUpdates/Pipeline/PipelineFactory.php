<?php

declare(strict_types=1);

namespace App\Module\BotGetUpdates\Pipeline;

use League\Pipeline\PipelineBuilder;
use League\Pipeline\PipelineInterface;

final readonly class PipelineFactory
{
    public static function create(
        callable ...$stages
    ): PipelineInterface {
        $builder = new PipelineBuilder();
        
        foreach ($stages as $stage) {
            $builder->add($stage);
        }

        return $builder->build();
    }
}
