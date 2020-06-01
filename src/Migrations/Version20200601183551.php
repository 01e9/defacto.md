<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200601183551 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('alter table categories rename to promise_categories');
        $this->addSql('alter table promise_category rename to promise_promise_category');
        $this->addSql('ALTER TABLE promise_promise_category DROP CONSTRAINT fk_25e1f7de12469de2');
        $this->addSql('DROP INDEX idx_25e1f7de12469de2');
        $this->addSql('ALTER TABLE promise_promise_category RENAME COLUMN category_id TO promise_category_id');
        $this->addSql('ALTER TABLE promise_promise_category ADD CONSTRAINT FK_F0065EF76DA678BB FOREIGN KEY (promise_category_id) REFERENCES promise_categories (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_F0065EF76DA678BB ON promise_promise_category (promise_category_id)');
        $this->addSql('ALTER INDEX promise_category_pkey RENAME TO promise_promise_category_pkey');
        $this->addSql('ALTER INDEX idx_25e1f7de2c4d4611 RENAME TO IDX_F0065EF72C4D4611');
        $this->addSql('ALTER INDEX category_unique_slug RENAME TO promise_category_unique_slug');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER INDEX promise_category_unique_slug RENAME TO category_unique_slug');
        $this->addSql('ALTER TABLE promise_promise_category DROP CONSTRAINT FK_F0065EF76DA678BB');
        $this->addSql('DROP INDEX IDX_F0065EF76DA678BB');
        $this->addSql('ALTER TABLE promise_promise_category RENAME COLUMN promise_category_id TO category_id');
        $this->addSql('ALTER TABLE promise_promise_category ADD CONSTRAINT fk_25e1f7de12469de2 FOREIGN KEY (category_id) REFERENCES promise_categories (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_25e1f7de12469de2 ON promise_promise_category (category_id)');
        $this->addSql('ALTER INDEX idx_f0065ef72c4d4611 RENAME TO idx_25e1f7de2c4d4611');
        $this->addSql('alter table promise_categories rename to categories');
        $this->addSql('alter table promise_promise_category rename to promise_category');
        $this->addSql('ALTER INDEX promise_promise_category_pkey RENAME TO promise_category_pkey');
    }
}
