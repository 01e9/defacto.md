<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200726160999 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('UPDATE competence_categories SET slug = \'administratie\', name = \'Administrație\' WHERE id = \'0ee40a86-df73-4784-ac72-e5ea3bde20a5\'');
        $this->addSql('UPDATE competence_categories SET slug = \'cetateni\', name = \'Cetățeni\' WHERE id = \'05218c0d-56cd-4a51-bca0-f7b07396f0cf\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('UPDATE competence_categories SET slug = \'apc-apl\', name = \'APC/APL\' WHERE id = \'0ee40a86-df73-4784-ac72-e5ea3bde20a5\'');
        $this->addSql('UPDATE competence_categories SET slug = \'cu-cetateni\', name = \'CU/Cetățeni\' WHERE id = \'05218c0d-56cd-4a51-bca0-f7b07396f0cf\'');
    }
}
