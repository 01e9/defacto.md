<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200601102842 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('INSERT INTO institutions (id, slug, name) VALUES (\'c310f444-73d1-4369-b674-401b4bb5d6ba\', \'presedintia-republicii-moldova\', \'Președinția Republicii Moldova\')');
        $this->addSql('INSERT INTO institutions (id, slug, name) VALUES (\'f7a64a6f-373c-4243-a3de-58429b05fb03\', \'parlamentul-republicii-moldova\', \'Parlamentul Republicii Moldova\')');

        $this->addSql('INSERT INTO powers (id, slug, name) VALUES (\'710bb39f-b789-4629-a7bf-1cbbf27632e1\', \'initiativa-legislativa\', \'Inițiativă legislativă\')');
        $this->addSql('INSERT INTO powers (id, slug, name) VALUES (\'0c2e6e6d-6fcf-4f98-993c-155b5a94dc4d\', \'promulgarea-abrogarea-legilor\', \'Promulgarea/Abrogarea legilor\')');
        $this->addSql('INSERT INTO powers (id, slug, name) VALUES (\'fe1aa5ac-07f0-4f4c-ae26-4a1f0ad7fe46\', \'mesaje-adresate-institutiilor\', \'Mesaje adresate instituțiilor\')');

        $this->addSql('INSERT INTO titles (id, slug, name, the_name) VALUES (\'72610c91-e881-4798-8f84-277f0663aeed\', \'presedintele-republicii-moldova\', \'Președintele Republicii Moldova\', \'Președintele Republicii Moldova\')');
        $this->addSql('INSERT INTO titles (id, slug, name, the_name) VALUES (\'656f8b23-9ac1-4aef-9918-ba7823bbca20\', \'deputat\', \'Deputat\', \'Deputatul\')');

        $this->addSql('INSERT INTO institution_title (id, institution_id, title_id, prerogatives_link) VALUES (\'9d45251f-a510-4f40-a5ae-8e6db3a857e1\', \'f7a64a6f-373c-4243-a3de-58429b05fb03\', \'656f8b23-9ac1-4aef-9918-ba7823bbca20\', NULL)');
        $this->addSql('INSERT INTO institution_title (id, institution_id, title_id, prerogatives_link) VALUES (\'e2dd9a4a-64f2-4ea3-8614-56211c3b6390\', \'c310f444-73d1-4369-b674-401b4bb5d6ba\', \'72610c91-e881-4798-8f84-277f0663aeed\', \'http://presedinte.md/statutul-si-atributiile\')');

        $this->addSql('INSERT INTO categories (id, slug, name) VALUES (\'c6881d6f-e17e-48da-8bd6-063c08227e20\', \'economie\', \'Economie\')');
        $this->addSql('INSERT INTO categories (id, slug, name) VALUES (\'bfcd9802-686d-476b-8ea3-00abb37b3df7\', \'educatie\', \'Educație\')');
        $this->addSql('INSERT INTO categories (id, slug, name) VALUES (\'d39e75e7-b616-43ee-a700-edc7adca7422\', \'politica-externa\', \'Politică externă\')');
        $this->addSql('INSERT INTO categories (id, slug, name) VALUES (\'d2987b63-7f06-410b-8c1d-cd2295f6a27a\', \'politica-interna\', \'Politică internă\')');
        $this->addSql('INSERT INTO categories (id, slug, name) VALUES (\'ff454662-c3ee-449c-a1df-ae326d711566\', \'social\', \'Social\')');

        $this->addSql('INSERT INTO statuses (id, slug, name, name_plural, effect, color, description) VALUES (\'6be684f3-cc33-40d0-bd99-a7432878852a\', \'realizate\', \'Realizată\', \'Realizate\', 1, \'green\', \'Promisiune îndeplinită în termenii stabiliți, conform criteriilor de evaluare.\')');
        $this->addSql('INSERT INTO statuses (id, slug, name, name_plural, effect, color, description) VALUES (\'4484a483-24cc-4885-8efa-e656bdddb8c2\', \'nerealizate\', \'Nerealizată\', \'Nerealizate\', -1, \'red\', \'Dacă pentru o anumită promisiune a fost stabilit un anumit termen sau condiție, iar acestea au fost încălcate - promisiunea se consideră nerealizată\')');

        $this->addSql('INSERT INTO elections (id, name, slug, date, the_name, the_elected_name, parent_id) VALUES (\'f3045cce-fb85-4efa-8a7f-a4b91fd27a29\', \'Alegeri parlamentare\', \'alegeri-parlamentare-2019\', \'2019-02-24\', \'Alegerile parlamentare\', \'Deputații aleși în circumscripții\', NULL)');
        $this->addSql('INSERT INTO elections (id, name, slug, date, the_name, the_elected_name, parent_id) VALUES (\'cd72c5bb-f110-4291-bbab-0ed28dbddad1\', \'Alegeri prezidențiale\', \'alegeri-prezidentiale-2016\', \'2016-10-30\', \'Alegerile prezidențiale\', \'Președintele ales\', NULL)');
        $this->addSql('INSERT INTO elections (id, name, slug, date, the_name, the_elected_name, parent_id) VALUES (\'f3045cce-fb85-4efa-8a7f-a4b91fd27a30\', \'Alegeri parlamentare noi\', \'alegeri-parlamentare-noi-2019\', \'2019-10-20\', \'Alegerile parlamentare noi\', \'Deputații aleși în circumscripții\', \'f3045cce-fb85-4efa-8a7f-a4b91fd27a29\')');
        $this->addSql('INSERT INTO elections (id, name, slug, date, the_name, the_elected_name, parent_id) VALUES (\'fd8a80b3-d73c-49d6-b9f8-29b9ec5e1237\', \'Alegeri parlamentare noi\', \'alegeri-parlamentare-noi-2020\', \'2020-03-15\', \'Alegerile parlamentare noi\', \'Deputații aleși în circumscripții\', \'f3045cce-fb85-4efa-8a7f-a4b91fd27a29\');');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('TRUNCATE TABLE elections');
        $this->addSql('TRUNCATE TABLE statuses');
        $this->addSql('TRUNCATE TABLE categories');
        $this->addSql('TRUNCATE TABLE titles');
        $this->addSql('TRUNCATE TABLE powers');
        $this->addSql('TRUNCATE TABLE institutions');
    }
}
