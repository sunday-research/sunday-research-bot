<?php

declare(strict_types=1);

namespace App\Command\Test;

use App\Module\MediaUpload\DTO\UploadMediaDTO;
use App\Module\MediaUpload\Enum\MediaTypeEnum;
use App\Module\MediaUpload\Service\MediaUploadService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'test:media-upload',
    description: 'Test media upload functionality',
)]
class TestMediaUploadCommand extends Command
{
    public function __construct(
        private readonly MediaUploadService $mediaUploadService
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->info('Testing media upload functionality...');

        try {
            // Тестируем загрузку файла
            $uploadDTO = UploadMediaDTO::makeDTO(
                filePath: 'assets/media/images/test-image.jpg',
                mediaType: MediaTypeEnum::PHOTO,
                caption: 'Test media upload',
                parseMode: 'MarkdownV2'
            );

            $fileInfo = $this->mediaUploadService->uploadMediaWithCache($uploadDTO);

            $io->success(sprintf(
                'Media uploaded successfully! File ID: %s, Unique ID: %s',
                $fileInfo->getFileId(),
                $fileInfo->getFileUniqueId()
            ));

            // Тестируем получение file_id
            $fileId = $this->mediaUploadService->getMediaFileId(
                'assets/media/images/test-image.jpg',
                'photo'
            );

            $io->info(sprintf('File ID retrieved: %s', $fileId));

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $io->error('Failed to upload media: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
