<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200823140059 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE titles ADD name_female VARCHAR(50) DEFAULT NULL');
        $this->addSql('UPDATE titles SET name_female = \'Președinta Republicii Moldova\' WHERE id = \'72610c91-e881-4798-8f84-277f0663aeed\'');
        $this->addSql('UPDATE titles SET name_female = \'Deputată\' WHERE id = \'656f8b23-9ac1-4aef-9918-ba7823bbca20\'');
        $this->addSql('ALTER TABLE titles ALTER name_female SET NOT NULL');

        $this->addSql('ALTER TABLE titles ADD the_name_female VARCHAR(50) DEFAULT NULL');
        $this->addSql('UPDATE titles SET the_name_female = \'Președinta Republicii Moldova\' WHERE id = \'72610c91-e881-4798-8f84-277f0663aeed\'');
        $this->addSql('UPDATE titles SET the_name_female = \'Deputata\' WHERE id = \'656f8b23-9ac1-4aef-9918-ba7823bbca20\'');
        $this->addSql('ALTER TABLE titles ALTER the_name_female SET NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE titles DROP name_female');
        $this->addSql('ALTER TABLE titles DROP the_name_female');
    }
}
