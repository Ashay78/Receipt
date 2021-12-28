<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211223191044 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE local ADD owner_id INT NOT NULL');
        $this->addSql('ALTER TABLE local ADD CONSTRAINT FK_8BD688E87E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_8BD688E87E3C61F9 ON local (owner_id)');
        $this->addSql('ALTER TABLE tenant ADD local_id INT NOT NULL');
        $this->addSql('ALTER TABLE tenant ADD CONSTRAINT FK_4E59C4625D5A2101 FOREIGN KEY (local_id) REFERENCES local (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_4E59C4625D5A2101 ON tenant (local_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE local DROP CONSTRAINT FK_8BD688E87E3C61F9');
        $this->addSql('DROP INDEX IDX_8BD688E87E3C61F9');
        $this->addSql('ALTER TABLE local DROP owner_id');
        $this->addSql('ALTER TABLE tenant DROP CONSTRAINT FK_4E59C4625D5A2101');
        $this->addSql('DROP INDEX IDX_4E59C4625D5A2101');
        $this->addSql('ALTER TABLE tenant DROP local_id');
    }
}
