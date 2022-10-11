<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210301180101 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE slide DROP FOREIGN KEY FK_72EFEE623DA5256D');
        $this->addSql('DROP INDEX UNIQ_72EFEE623DA5256D ON slide');
        $this->addSql('ALTER TABLE slide ADD title_ar VARCHAR(255) NOT NULL, ADD content_ar VARCHAR(255) NOT NULL, ADD btn_title_ar VARCHAR(255) NOT NULL, ADD file_name VARCHAR(255) NOT NULL, ADD updated_at DATETIME NOT NULL, DROP image_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE slide ADD image_id INT NOT NULL, DROP title_ar, DROP content_ar, DROP btn_title_ar, DROP file_name, DROP updated_at');
        $this->addSql('ALTER TABLE slide ADD CONSTRAINT FK_72EFEE623DA5256D FOREIGN KEY (image_id) REFERENCES image (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_72EFEE623DA5256D ON slide (image_id)');
    }
}
