<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250704122933 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE subscriber (id UUID NOT NULL, telegram_user_id BIGINT NOT NULL, username VARCHAR(255) DEFAULT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) DEFAULT NULL, language_code VARCHAR(10) DEFAULT NULL, is_premium BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_AD005B69FC28B263 ON subscriber (telegram_user_id)');
        $this->addSql('COMMENT ON COLUMN subscriber.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE subscriber_messages (id UUID NOT NULL, subscriber_id UUID NOT NULL, chat_id BIGINT NOT NULL, message_id BIGINT NOT NULL, message_text TEXT NOT NULL, message_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, is_bot_sender BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A8CC81A97808B1AD ON subscriber_messages (subscriber_id)');
        $this->addSql('COMMENT ON COLUMN subscriber_messages.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN subscriber_messages.subscriber_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE subscriber_messages ADD CONSTRAINT FK_A8CC81A97808B1AD FOREIGN KEY (subscriber_id) REFERENCES subscriber (id) ON DELETE RESTRICT NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE subscriber_messages DROP CONSTRAINT FK_A8CC81A97808B1AD');
        $this->addSql('DROP TABLE subscriber');
        $this->addSql('DROP TABLE subscriber_messages');
    }
}
