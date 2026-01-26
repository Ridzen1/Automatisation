<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260126145317 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__film AS SELECT id, titre, duree FROM film');
        $this->addSql('DROP TABLE film');
        $this->addSql('CREATE TABLE film (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, realisateur_id INTEGER DEFAULT NULL, titre VARCHAR(255) NOT NULL, duree VARCHAR(255) DEFAULT NULL, CONSTRAINT FK_8244BE22F1D8422E FOREIGN KEY (realisateur_id) REFERENCES realisateur (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO film (id, titre, duree) SELECT id, titre, duree FROM __temp__film');
        $this->addSql('DROP TABLE __temp__film');
        $this->addSql('CREATE INDEX IDX_8244BE22F1D8422E ON film (realisateur_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__film AS SELECT id, titre, duree FROM film');
        $this->addSql('DROP TABLE film');
        $this->addSql('CREATE TABLE film (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, duree VARCHAR(255) DEFAULT NULL)');
        $this->addSql('INSERT INTO film (id, titre, duree) SELECT id, titre, duree FROM __temp__film');
        $this->addSql('DROP TABLE __temp__film');
    }
}
