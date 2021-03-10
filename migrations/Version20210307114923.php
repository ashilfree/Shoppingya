<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210307114923 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE catalog DROP FOREIGN KEY FK_1B2C3247498DA827');
        $this->addSql('DROP INDEX IDX_1B2C3247498DA827 ON catalog');
        $this->addSql('ALTER TABLE catalog ADD size VARCHAR(255) NOT NULL, DROP size_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE catalog ADD size_id INT DEFAULT NULL, DROP size');
        $this->addSql('ALTER TABLE catalog ADD CONSTRAINT FK_1B2C3247498DA827 FOREIGN KEY (size_id) REFERENCES size (id)');
        $this->addSql('CREATE INDEX IDX_1B2C3247498DA827 ON catalog (size_id)');
    }
}
