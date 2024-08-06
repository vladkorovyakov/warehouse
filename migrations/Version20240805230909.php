<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240805230909 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE documents (id INT NOT NULL, product_id INT NOT NULL, type VARCHAR(50) NOT NULL, created TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, value INT NOT NULL, current_remainder INT DEFAULT 0 NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX created_idx ON documents (created)');
        $this->addSql('CREATE INDEX u_type_created_idx ON documents (type, created)');
        $this->addSql('CREATE TABLE inventory_errors (id INT NOT NULL, document_id INT NOT NULL, error_value INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3C8DCFB6C33F7837 ON inventory_errors (document_id)');
        $this->addSql('CREATE TABLE price_per_product (id INT NOT NULL, document_id INT NOT NULL, value INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B9C7B1D7C33F7837 ON price_per_product (document_id)');
        $this->addSql('CREATE TABLE product_remainder (id INT NOT NULL, product_id INT NOT NULL, value INT DEFAULT 0 NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('COMMENT ON COLUMN messenger_messages.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.available_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.delivered_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
        $this->addSql('ALTER TABLE inventory_errors ADD CONSTRAINT FK_3C8DCFB6C33F7837 FOREIGN KEY (document_id) REFERENCES documents (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE price_per_product ADD CONSTRAINT FK_B9C7B1D7C33F7837 FOREIGN KEY (document_id) REFERENCES documents (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE inventory_errors DROP CONSTRAINT FK_3C8DCFB6C33F7837');
        $this->addSql('ALTER TABLE price_per_product DROP CONSTRAINT FK_B9C7B1D7C33F7837');
        $this->addSql('DROP TABLE documents');
        $this->addSql('DROP TABLE inventory_errors');
        $this->addSql('DROP TABLE price_per_product');
        $this->addSql('DROP TABLE product_remainder');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
