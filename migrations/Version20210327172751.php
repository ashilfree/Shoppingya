<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210327172751 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE about ADD title_ar VARCHAR(255) NOT NULL, ADD description_ar1 LONGTEXT DEFAULT NULL, ADD description_ar2 LONGTEXT DEFAULT NULL, ADD description_ar3 LONGTEXT DEFAULT NULL, ADD word_ar LONGTEXT DEFAULT NULL, ADD word_honor_ar VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE about DROP title_ar, DROP description_ar1, DROP description_ar2, DROP description_ar3, DROP word_ar, DROP word_honor_ar');
    }
}
