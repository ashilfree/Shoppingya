<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210406130435 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tag_ar (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag_ar_product (tag_ar_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_64C1548318AAC4D3 (tag_ar_id), INDEX IDX_64C154834584665A (product_id), PRIMARY KEY(tag_ar_id, product_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tag_ar_product ADD CONSTRAINT FK_64C1548318AAC4D3 FOREIGN KEY (tag_ar_id) REFERENCES tag_ar (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tag_ar_product ADD CONSTRAINT FK_64C154834584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tag_ar_product DROP FOREIGN KEY FK_64C1548318AAC4D3');
        $this->addSql('DROP TABLE tag_ar');
        $this->addSql('DROP TABLE tag_ar_product');
    }
}
