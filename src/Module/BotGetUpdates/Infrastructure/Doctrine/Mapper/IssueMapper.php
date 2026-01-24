<?php

declare(strict_types=1);

namespace App\Module\BotGetUpdates\Infrastructure\Doctrine\Mapper;

use App\Module\BotGetUpdates\Entity\Issue as DomainIssue;
use App\Module\BotGetUpdates\Infrastructure\Doctrine\Model\Issue as DoctrineIssue;
use App\Module\BotGetUpdates\Infrastructure\Doctrine\Model\Subscriber as DoctrineSubscriber;

class IssueMapper
{
    public static function map(
        DomainIssue $domainIssue,
        DoctrineSubscriber $doctrineSubscriber,
        ?DoctrineIssue $doctrineIssue = null,
    ): DoctrineIssue {
        $issue = $doctrineIssue ?? new DoctrineIssue();

        $issue
            ->setSubscriber($doctrineSubscriber)
            ->setChatId($domainIssue->getChatId())
            ->setMessageId($domainIssue->getMessageId())
            ->setMessageText($domainIssue->getMessageText())
            ->setStatus($domainIssue->getStatus())
            ->setPriority($domainIssue->getPriority())
            ->setDecisionComment($domainIssue->getDecisionComment())
            ->setCreatedAt($domainIssue->getCreatedAt())
            ->setUpdatedAt($domainIssue->getUpdatedAt())
            ->setResolvedAt($domainIssue->getResolvedAt())
        ;

        return $issue;
    }
}
