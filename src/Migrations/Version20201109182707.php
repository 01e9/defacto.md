<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201109182707 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql('UPDATE institution_title SET prerogatives_link = \'http://lex.justice.md/md/313277/\' WHERE id = \'9d45251f-a510-4f40-a5ae-8e6db3a857e1\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('UPDATE institution_title SET prerogatives_link = NULL WHERE id = \'9d45251f-a510-4f40-a5ae-8e6db3a857e1\'');
    }
}
