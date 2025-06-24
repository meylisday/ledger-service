<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250624204529 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE INDEX idx_transaction_currency ON transaction (currency)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX idx_transaction_currency');
    }
}
