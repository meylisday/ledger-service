<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250622200424 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE ledger (id UUID NOT NULL, base_currency VARCHAR(3) NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN ledger.id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE transaction (id UUID NOT NULL, ledger_id UUID NOT NULL, transaction_id VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, amount NUMERIC(15, 4) NOT NULL, currency VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_723705D12FC0CB0F ON transaction (transaction_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_723705D1A7B913DD ON transaction (ledger_id)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN transaction.id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN transaction.ledger_id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN transaction.created_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE transaction ADD CONSTRAINT FK_723705D1A7B913DD FOREIGN KEY (ledger_id) REFERENCES ledger (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE transaction DROP CONSTRAINT FK_723705D1A7B913DD
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE ledger
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE transaction
        SQL);
    }
}
