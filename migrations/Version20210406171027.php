<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210406171027 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tag_ar_product DROP FOREIGN KEY FK_64C1548318AAC4D3');
        $this->addSql('CREATE TABLE tog (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tog_product (tog_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_FFB219B45E2DD70 (tog_id), INDEX IDX_FFB219B44584665A (product_id), PRIMARY KEY(tog_id, product_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tog_product ADD CONSTRAINT FK_FFB219B45E2DD70 FOREIGN KEY (tog_id) REFERENCES tog (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tog_product ADD CONSTRAINT FK_FFB219B44584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE tag_ar');
        $this->addSql('DROP TABLE tag_ar_product');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tog_product DROP FOREIGN KEY FK_FFB219B45E2DD70');
        $this->addSql('CREATE TABLE tag_ar (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE tag_ar_product (tag_ar_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_64C154834584665A (product_id), INDEX IDX_64C1548318AAC4D3 (tag_ar_id), PRIMARY KEY(tag_ar_id, product_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE tag_ar_product ADD CONSTRAINT FK_64C1548318AAC4D3 FOREIGN KEY (tag_ar_id) REFERENCES tag_ar (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tag_ar_product ADD CONSTRAINT FK_64C154834584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE tog');
        $this->addSql('DROP TABLE tog_product');
    }
}
