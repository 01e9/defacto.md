<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190823212031 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE mandates RENAME COLUMN cancel_reason TO ceasing_reason');
        $this->addSql('ALTER TABLE mandates RENAME COLUMN cancel_link TO ceasing_link');
        $this->addSql('ALTER TABLE mandates RENAME COLUMN cancel_date TO ceasing_date');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE mandates RENAME COLUMN ceasing_reason TO cancel_reason');
        $this->addSql('ALTER TABLE mandates RENAME COLUMN ceasing_link TO cancel_link');
        $this->addSql('ALTER TABLE mandates RENAME COLUMN ceasing_date TO cancel_date');
    }
}
