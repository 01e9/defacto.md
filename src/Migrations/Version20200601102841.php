<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200601102841 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE promises (id VARCHAR(36) NOT NULL, election_id VARCHAR(36) NOT NULL, politician_id VARCHAR(36) NOT NULL, status_id VARCHAR(36) DEFAULT NULL, made_time DATE NOT NULL, code VARCHAR(10) DEFAULT NULL, name VARCHAR(120) NOT NULL, slug VARCHAR(120) NOT NULL, description TEXT DEFAULT NULL, published BOOLEAN DEFAULT \'false\' NOT NULL, has_prerogatives BOOLEAN DEFAULT \'false\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_EB9A506CA708DAFF ON promises (election_id)');
        $this->addSql('CREATE INDEX IDX_EB9A506C899F0176 ON promises (politician_id)');
        $this->addSql('CREATE INDEX IDX_EB9A506C6BF700BD ON promises (status_id)');
        $this->addSql('CREATE UNIQUE INDEX promise_unique_slug ON promises (slug)');
        $this->addSql('CREATE UNIQUE INDEX promise_unique_code ON promises (code)');
        $this->addSql('CREATE TABLE promise_category (promise_id VARCHAR(36) NOT NULL, category_id VARCHAR(36) NOT NULL, PRIMARY KEY(promise_id, category_id))');
        $this->addSql('CREATE INDEX IDX_25E1F7DE2C4D4611 ON promise_category (promise_id)');
        $this->addSql('CREATE INDEX IDX_25E1F7DE12469DE2 ON promise_category (category_id)');
        $this->addSql('CREATE TABLE politicians (id VARCHAR(36) NOT NULL, slug VARCHAR(50) NOT NULL, first_name VARCHAR(20) NOT NULL, last_name VARCHAR(20) NOT NULL, photo VARCHAR(255) DEFAULT NULL, birth_date DATE DEFAULT NULL, studies TEXT DEFAULT NULL, profession VARCHAR(120) DEFAULT NULL, website VARCHAR(120) DEFAULT NULL, facebook VARCHAR(120) DEFAULT NULL, email VARCHAR(120) DEFAULT NULL, phone VARCHAR(16) DEFAULT NULL, previous_titles TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX politician_unique_slug ON politicians (slug)');
        $this->addSql('CREATE TABLE actions (id VARCHAR(36) NOT NULL, mandate_id VARCHAR(36) NOT NULL, occurred_time DATE NOT NULL, name VARCHAR(120) NOT NULL, slug VARCHAR(120) NOT NULL, description TEXT DEFAULT NULL, published BOOLEAN DEFAULT \'false\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_548F1EF6C1129CD ON actions (mandate_id)');
        $this->addSql('CREATE UNIQUE INDEX action_unique_slug ON actions (slug)');
        $this->addSql('CREATE TABLE action_power (action_id VARCHAR(36) NOT NULL, power_id VARCHAR(36) NOT NULL, PRIMARY KEY(action_id, power_id))');
        $this->addSql('CREATE INDEX IDX_4C44CD1C9D32F035 ON action_power (action_id)');
        $this->addSql('CREATE INDEX IDX_4C44CD1CAB4FC384 ON action_power (power_id)');
        $this->addSql('CREATE TABLE problems (id VARCHAR(36) NOT NULL, name VARCHAR(120) NOT NULL, slug VARCHAR(120) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX problems_unique_slug ON problems (slug)');
        $this->addSql('CREATE TABLE constituencies (id VARCHAR(36) NOT NULL, slug VARCHAR(120) NOT NULL, name VARCHAR(120) NOT NULL, link VARCHAR(255) DEFAULT NULL, map JSON DEFAULT NULL, number INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX constituency_unique_slug ON constituencies (slug)');
        $this->addSql('CREATE UNIQUE INDEX constituency_unique_number ON constituencies (number)');
        $this->addSql('CREATE TABLE parties (id VARCHAR(36) NOT NULL, slug VARCHAR(120) NOT NULL, name VARCHAR(120) NOT NULL, logo VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX party_unique_slug ON parties (slug)');
        $this->addSql('CREATE TABLE promise_sources (id VARCHAR(36) NOT NULL, promise_id VARCHAR(36) NOT NULL, name VARCHAR(120) NOT NULL, link VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A6D4C5052C4D4611 ON promise_sources (promise_id)');
        $this->addSql('CREATE UNIQUE INDEX promise_source_unique_name ON promise_sources (promise_id, name)');
        $this->addSql('CREATE TABLE titles (id VARCHAR(36) NOT NULL, slug VARCHAR(50) NOT NULL, name VARCHAR(50) NOT NULL, the_name VARCHAR(50) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX title_unique_slug ON titles (slug)');
        $this->addSql('CREATE TABLE title_power (title_id VARCHAR(36) NOT NULL, power_id VARCHAR(36) NOT NULL, PRIMARY KEY(title_id, power_id))');
        $this->addSql('CREATE INDEX IDX_6B7BB7D7A9F87BD ON title_power (title_id)');
        $this->addSql('CREATE INDEX IDX_6B7BB7D7AB4FC384 ON title_power (power_id)');
        $this->addSql('CREATE TABLE categories (id VARCHAR(36) NOT NULL, slug VARCHAR(50) NOT NULL, name VARCHAR(50) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX category_unique_slug ON categories (slug)');
        $this->addSql('CREATE TABLE constituency_problems (id VARCHAR(36) NOT NULL, constituency_id VARCHAR(36) NOT NULL, election_id VARCHAR(36) NOT NULL, problem_id VARCHAR(36) NOT NULL, respondents INT DEFAULT NULL, percentage NUMERIC(5, 2) DEFAULT NULL, type VARCHAR(30) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_90A7D944693B626F ON constituency_problems (constituency_id)');
        $this->addSql('CREATE INDEX IDX_90A7D944A708DAFF ON constituency_problems (election_id)');
        $this->addSql('CREATE INDEX IDX_90A7D944A0DCED86 ON constituency_problems (problem_id)');
        $this->addSql('CREATE UNIQUE INDEX constituency_problems_unique_composite ON constituency_problems (constituency_id, election_id, problem_id, type)');
        $this->addSql('CREATE TABLE promise_updates (id VARCHAR(36) NOT NULL, action_id VARCHAR(36) NOT NULL, promise_id VARCHAR(36) NOT NULL, status_id VARCHAR(36) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_31C1B3C79D32F035 ON promise_updates (action_id)');
        $this->addSql('CREATE INDEX IDX_31C1B3C72C4D4611 ON promise_updates (promise_id)');
        $this->addSql('CREATE INDEX IDX_31C1B3C76BF700BD ON promise_updates (status_id)');
        $this->addSql('CREATE UNIQUE INDEX promise_update_unique_action_promise ON promise_updates (action_id, promise_id)');
        $this->addSql('CREATE TABLE settings (id VARCHAR(100) NOT NULL, value TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE institutions (id VARCHAR(36) NOT NULL, slug VARCHAR(50) NOT NULL, name VARCHAR(50) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX institution_unique_slug ON institutions (slug)');
        $this->addSql('CREATE TABLE institution_title (id VARCHAR(36) NOT NULL, institution_id VARCHAR(36) NOT NULL, title_id VARCHAR(36) NOT NULL, prerogatives_link VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_CA2EE3A310405986 ON institution_title (institution_id)');
        $this->addSql('CREATE INDEX IDX_CA2EE3A3A9F87BD ON institution_title (title_id)');
        $this->addSql('CREATE UNIQUE INDEX institution_title_unique_institution_title ON institution_title (institution_id, title_id)');
        $this->addSql('CREATE TABLE candidates (id VARCHAR(36) NOT NULL, politician_id VARCHAR(36) NOT NULL, election_id VARCHAR(36) NOT NULL, constituency_id VARCHAR(36) DEFAULT NULL, party_id VARCHAR(36) DEFAULT NULL, registration_date DATE DEFAULT NULL, registration_note VARCHAR(120) DEFAULT NULL, registration_link VARCHAR(255) DEFAULT NULL, electoral_platform TEXT DEFAULT NULL, electoral_platform_link VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6A77F80C899F0176 ON candidates (politician_id)');
        $this->addSql('CREATE INDEX IDX_6A77F80CA708DAFF ON candidates (election_id)');
        $this->addSql('CREATE INDEX IDX_6A77F80C693B626F ON candidates (constituency_id)');
        $this->addSql('CREATE INDEX IDX_6A77F80C213C1059 ON candidates (party_id)');
        $this->addSql('CREATE UNIQUE INDEX candidates_unique_composite ON candidates (politician_id, election_id, constituency_id)');
        $this->addSql('CREATE TABLE candidates_problems_opinions (id VARCHAR(36) NOT NULL, politician_id VARCHAR(36) NOT NULL, election_id VARCHAR(36) NOT NULL, constituency_id VARCHAR(36) NOT NULL, problem_id VARCHAR(36) NOT NULL, opinion TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B5A2263F899F0176 ON candidates_problems_opinions (politician_id)');
        $this->addSql('CREATE INDEX IDX_B5A2263FA708DAFF ON candidates_problems_opinions (election_id)');
        $this->addSql('CREATE INDEX IDX_B5A2263F693B626F ON candidates_problems_opinions (constituency_id)');
        $this->addSql('CREATE INDEX IDX_B5A2263FA0DCED86 ON candidates_problems_opinions (problem_id)');
        $this->addSql('CREATE UNIQUE INDEX candidate_problem_opinion_unique_composite ON candidates_problems_opinions (politician_id, election_id, constituency_id, problem_id)');
        $this->addSql('CREATE TABLE blog_posts (id VARCHAR(36) NOT NULL, publish_time DATE DEFAULT NULL, title VARCHAR(120) NOT NULL, slug VARCHAR(120) NOT NULL, content TEXT NOT NULL, image VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX blog_posts_unique_slug ON blog_posts (slug)');
        $this->addSql('CREATE TABLE statuses (id VARCHAR(36) NOT NULL, slug VARCHAR(50) NOT NULL, name VARCHAR(50) NOT NULL, name_plural VARCHAR(50) NOT NULL, description VARCHAR(255) DEFAULT NULL, effect INT NOT NULL, color VARCHAR(20) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX status_unique_slug ON statuses (slug)');
        $this->addSql('CREATE UNIQUE INDEX status_unique_effect ON statuses (effect)');
        $this->addSql('CREATE TABLE elections (id VARCHAR(36) NOT NULL, parent_id VARCHAR(36) DEFAULT NULL, name VARCHAR(120) NOT NULL, the_name VARCHAR(120) NOT NULL, the_elected_name VARCHAR(120) NOT NULL, slug VARCHAR(120) NOT NULL, date DATE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_1BD26F33727ACA70 ON elections (parent_id)');
        $this->addSql('CREATE UNIQUE INDEX election_unique_slug ON elections (slug)');
        $this->addSql('CREATE TABLE powers (id VARCHAR(36) NOT NULL, slug VARCHAR(50) NOT NULL, name VARCHAR(50) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX power_unique_slug ON powers (slug)');
        $this->addSql('CREATE TABLE users (id VARCHAR(36) NOT NULL, email VARCHAR(60) NOT NULL, password VARCHAR(255) NOT NULL, salt VARCHAR(64) NOT NULL, is_active BOOLEAN DEFAULT \'false\' NOT NULL, roles TEXT DEFAULT \'ROLE_USER\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX user_unique_email ON users (email)');
        $this->addSql('COMMENT ON COLUMN users.roles IS \'(DC2Type:simple_array)\'');
        $this->addSql('CREATE TABLE action_sources (id VARCHAR(36) NOT NULL, action_id VARCHAR(36) NOT NULL, name VARCHAR(120) NOT NULL, link VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8FFED139D32F035 ON action_sources (action_id)');
        $this->addSql('CREATE UNIQUE INDEX action_source_unique_name ON action_sources (action_id, name)');
        $this->addSql('CREATE TABLE mandates (id VARCHAR(36) NOT NULL, election_id VARCHAR(36) NOT NULL, constituency_id VARCHAR(36) DEFAULT NULL, politician_id VARCHAR(36) DEFAULT NULL, institution_title_id VARCHAR(36) NOT NULL, begin_date DATE NOT NULL, ceasing_date DATE DEFAULT NULL, end_date DATE NOT NULL, votes_count INT NOT NULL, votes_percent NUMERIC(5, 2) NOT NULL, decision_link VARCHAR(255) DEFAULT NULL, ceasing_reason VARCHAR(255) DEFAULT NULL, ceasing_link VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5CA57D7BA708DAFF ON mandates (election_id)');
        $this->addSql('CREATE INDEX IDX_5CA57D7B693B626F ON mandates (constituency_id)');
        $this->addSql('CREATE INDEX IDX_5CA57D7B899F0176 ON mandates (politician_id)');
        $this->addSql('CREATE INDEX IDX_5CA57D7B14FA8B23 ON mandates (institution_title_id)');
        $this->addSql('CREATE UNIQUE INDEX mandate_unique_politician_institution_begin ON mandates (politician_id, institution_title_id, begin_date)');
        $this->addSql('CREATE UNIQUE INDEX mandate_unique_politician_institution_end ON mandates (politician_id, institution_title_id, end_date)');
        $this->addSql('ALTER TABLE promises ADD CONSTRAINT FK_EB9A506CA708DAFF FOREIGN KEY (election_id) REFERENCES elections (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE promises ADD CONSTRAINT FK_EB9A506C899F0176 FOREIGN KEY (politician_id) REFERENCES politicians (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE promises ADD CONSTRAINT FK_EB9A506C6BF700BD FOREIGN KEY (status_id) REFERENCES statuses (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE promise_category ADD CONSTRAINT FK_25E1F7DE2C4D4611 FOREIGN KEY (promise_id) REFERENCES promises (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE promise_category ADD CONSTRAINT FK_25E1F7DE12469DE2 FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE actions ADD CONSTRAINT FK_548F1EF6C1129CD FOREIGN KEY (mandate_id) REFERENCES mandates (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE action_power ADD CONSTRAINT FK_4C44CD1C9D32F035 FOREIGN KEY (action_id) REFERENCES actions (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE action_power ADD CONSTRAINT FK_4C44CD1CAB4FC384 FOREIGN KEY (power_id) REFERENCES powers (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE promise_sources ADD CONSTRAINT FK_A6D4C5052C4D4611 FOREIGN KEY (promise_id) REFERENCES promises (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE title_power ADD CONSTRAINT FK_6B7BB7D7A9F87BD FOREIGN KEY (title_id) REFERENCES titles (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE title_power ADD CONSTRAINT FK_6B7BB7D7AB4FC384 FOREIGN KEY (power_id) REFERENCES powers (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE constituency_problems ADD CONSTRAINT FK_90A7D944693B626F FOREIGN KEY (constituency_id) REFERENCES constituencies (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE constituency_problems ADD CONSTRAINT FK_90A7D944A708DAFF FOREIGN KEY (election_id) REFERENCES elections (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE constituency_problems ADD CONSTRAINT FK_90A7D944A0DCED86 FOREIGN KEY (problem_id) REFERENCES problems (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE promise_updates ADD CONSTRAINT FK_31C1B3C79D32F035 FOREIGN KEY (action_id) REFERENCES actions (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE promise_updates ADD CONSTRAINT FK_31C1B3C72C4D4611 FOREIGN KEY (promise_id) REFERENCES promises (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE promise_updates ADD CONSTRAINT FK_31C1B3C76BF700BD FOREIGN KEY (status_id) REFERENCES statuses (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE institution_title ADD CONSTRAINT FK_CA2EE3A310405986 FOREIGN KEY (institution_id) REFERENCES institutions (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE institution_title ADD CONSTRAINT FK_CA2EE3A3A9F87BD FOREIGN KEY (title_id) REFERENCES titles (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE candidates ADD CONSTRAINT FK_6A77F80C899F0176 FOREIGN KEY (politician_id) REFERENCES politicians (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE candidates ADD CONSTRAINT FK_6A77F80CA708DAFF FOREIGN KEY (election_id) REFERENCES elections (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE candidates ADD CONSTRAINT FK_6A77F80C693B626F FOREIGN KEY (constituency_id) REFERENCES constituencies (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE candidates ADD CONSTRAINT FK_6A77F80C213C1059 FOREIGN KEY (party_id) REFERENCES parties (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE candidates_problems_opinions ADD CONSTRAINT FK_B5A2263F899F0176 FOREIGN KEY (politician_id) REFERENCES politicians (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE candidates_problems_opinions ADD CONSTRAINT FK_B5A2263FA708DAFF FOREIGN KEY (election_id) REFERENCES elections (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE candidates_problems_opinions ADD CONSTRAINT FK_B5A2263F693B626F FOREIGN KEY (constituency_id) REFERENCES constituencies (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE candidates_problems_opinions ADD CONSTRAINT FK_B5A2263FA0DCED86 FOREIGN KEY (problem_id) REFERENCES problems (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE elections ADD CONSTRAINT FK_1BD26F33727ACA70 FOREIGN KEY (parent_id) REFERENCES elections (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE action_sources ADD CONSTRAINT FK_8FFED139D32F035 FOREIGN KEY (action_id) REFERENCES actions (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE mandates ADD CONSTRAINT FK_5CA57D7BA708DAFF FOREIGN KEY (election_id) REFERENCES elections (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE mandates ADD CONSTRAINT FK_5CA57D7B693B626F FOREIGN KEY (constituency_id) REFERENCES constituencies (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE mandates ADD CONSTRAINT FK_5CA57D7B899F0176 FOREIGN KEY (politician_id) REFERENCES politicians (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE mandates ADD CONSTRAINT FK_5CA57D7B14FA8B23 FOREIGN KEY (institution_title_id) REFERENCES institution_title (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE promise_category DROP CONSTRAINT FK_25E1F7DE2C4D4611');
        $this->addSql('ALTER TABLE promise_sources DROP CONSTRAINT FK_A6D4C5052C4D4611');
        $this->addSql('ALTER TABLE promise_updates DROP CONSTRAINT FK_31C1B3C72C4D4611');
        $this->addSql('ALTER TABLE promises DROP CONSTRAINT FK_EB9A506C899F0176');
        $this->addSql('ALTER TABLE candidates DROP CONSTRAINT FK_6A77F80C899F0176');
        $this->addSql('ALTER TABLE candidates_problems_opinions DROP CONSTRAINT FK_B5A2263F899F0176');
        $this->addSql('ALTER TABLE mandates DROP CONSTRAINT FK_5CA57D7B899F0176');
        $this->addSql('ALTER TABLE action_power DROP CONSTRAINT FK_4C44CD1C9D32F035');
        $this->addSql('ALTER TABLE promise_updates DROP CONSTRAINT FK_31C1B3C79D32F035');
        $this->addSql('ALTER TABLE action_sources DROP CONSTRAINT FK_8FFED139D32F035');
        $this->addSql('ALTER TABLE constituency_problems DROP CONSTRAINT FK_90A7D944A0DCED86');
        $this->addSql('ALTER TABLE candidates_problems_opinions DROP CONSTRAINT FK_B5A2263FA0DCED86');
        $this->addSql('ALTER TABLE constituency_problems DROP CONSTRAINT FK_90A7D944693B626F');
        $this->addSql('ALTER TABLE candidates DROP CONSTRAINT FK_6A77F80C693B626F');
        $this->addSql('ALTER TABLE candidates_problems_opinions DROP CONSTRAINT FK_B5A2263F693B626F');
        $this->addSql('ALTER TABLE mandates DROP CONSTRAINT FK_5CA57D7B693B626F');
        $this->addSql('ALTER TABLE candidates DROP CONSTRAINT FK_6A77F80C213C1059');
        $this->addSql('ALTER TABLE title_power DROP CONSTRAINT FK_6B7BB7D7A9F87BD');
        $this->addSql('ALTER TABLE institution_title DROP CONSTRAINT FK_CA2EE3A3A9F87BD');
        $this->addSql('ALTER TABLE promise_category DROP CONSTRAINT FK_25E1F7DE12469DE2');
        $this->addSql('ALTER TABLE institution_title DROP CONSTRAINT FK_CA2EE3A310405986');
        $this->addSql('ALTER TABLE mandates DROP CONSTRAINT FK_5CA57D7B14FA8B23');
        $this->addSql('ALTER TABLE promises DROP CONSTRAINT FK_EB9A506C6BF700BD');
        $this->addSql('ALTER TABLE promise_updates DROP CONSTRAINT FK_31C1B3C76BF700BD');
        $this->addSql('ALTER TABLE promises DROP CONSTRAINT FK_EB9A506CA708DAFF');
        $this->addSql('ALTER TABLE constituency_problems DROP CONSTRAINT FK_90A7D944A708DAFF');
        $this->addSql('ALTER TABLE candidates DROP CONSTRAINT FK_6A77F80CA708DAFF');
        $this->addSql('ALTER TABLE candidates_problems_opinions DROP CONSTRAINT FK_B5A2263FA708DAFF');
        $this->addSql('ALTER TABLE elections DROP CONSTRAINT FK_1BD26F33727ACA70');
        $this->addSql('ALTER TABLE mandates DROP CONSTRAINT FK_5CA57D7BA708DAFF');
        $this->addSql('ALTER TABLE action_power DROP CONSTRAINT FK_4C44CD1CAB4FC384');
        $this->addSql('ALTER TABLE title_power DROP CONSTRAINT FK_6B7BB7D7AB4FC384');
        $this->addSql('ALTER TABLE actions DROP CONSTRAINT FK_548F1EF6C1129CD');
        $this->addSql('DROP TABLE promises');
        $this->addSql('DROP TABLE promise_category');
        $this->addSql('DROP TABLE politicians');
        $this->addSql('DROP TABLE actions');
        $this->addSql('DROP TABLE action_power');
        $this->addSql('DROP TABLE problems');
        $this->addSql('DROP TABLE constituencies');
        $this->addSql('DROP TABLE parties');
        $this->addSql('DROP TABLE promise_sources');
        $this->addSql('DROP TABLE titles');
        $this->addSql('DROP TABLE title_power');
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE constituency_problems');
        $this->addSql('DROP TABLE promise_updates');
        $this->addSql('DROP TABLE settings');
        $this->addSql('DROP TABLE institutions');
        $this->addSql('DROP TABLE institution_title');
        $this->addSql('DROP TABLE candidates');
        $this->addSql('DROP TABLE candidates_problems_opinions');
        $this->addSql('DROP TABLE blog_posts');
        $this->addSql('DROP TABLE statuses');
        $this->addSql('DROP TABLE elections');
        $this->addSql('DROP TABLE powers');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE action_sources');
        $this->addSql('DROP TABLE mandates');
    }
}
