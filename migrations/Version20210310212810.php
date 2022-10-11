<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210310212810 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` ADD shipping_first_name VARCHAR(255) DEFAULT NULL, ADD shipping_last_name VARCHAR(255) DEFAULT NULL, ADD shipping_address VARCHAR(255) DEFAULT NULL, ADD shipping_city VARCHAR(255) DEFAULT NULL, ADD shipping_province VARCHAR(255) DEFAULT NULL, ADD shipping_postal_code VARCHAR(255) DEFAULT NULL, ADD shipping_lat DOUBLE PRECISION DEFAULT NULL, ADD shipping_lng DOUBLE PRECISION DEFAULT NULL, ADD shipping_email VARCHAR(255) DEFAULT NULL, ADD shipping_phone VARCHAR(255) DEFAULT NULL, DROP delivery');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` ADD delivery LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, DROP shipping_first_name, DROP shipping_last_name, DROP shipping_address, DROP shipping_city, DROP shipping_province, DROP shipping_postal_code, DROP shipping_lat, DROP shipping_lng, DROP shipping_email, DROP shipping_phone');
    }
}
