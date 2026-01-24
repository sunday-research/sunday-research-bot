<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251216200550 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE subscriber_issue (id UUID NOT NULL, subscriber_id UUID DEFAULT NULL, chat_id BIGINT NOT NULL, message_id BIGINT NOT NULL, message_text TEXT NOT NULL, status VARCHAR(24) DEFAULT \'new\' NOT NULL, priority VARCHAR(24) DEFAULT \'medium\' NOT NULL, decision_comment TEXT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, resolved_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_290DA1D97808B1AD ON subscriber_issue (subscriber_id)');
        $this->addSql('CREATE INDEX idx_status_priority ON subscriber_issue (status, priority)');
        $this->addSql('CREATE INDEX idx_created_at ON subscriber_issue (created_at)');
        $this->addSql('COMMENT ON COLUMN subscriber_issue.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN subscriber_issue.subscriber_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN subscriber_issue.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN subscriber_issue.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN subscriber_issue.resolved_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE subscriber_issue ADD CONSTRAINT FK_290DA1D97808B1AD FOREIGN KEY (subscriber_id) REFERENCES subscriber (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subscriber_issue DROP CONSTRAINT FK_290DA1D97808B1AD');
        $this->addSql('DROP TABLE subscriber_issue');
    }
}
