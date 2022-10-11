<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210511135041 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` ADD invoice_id VARCHAR(255) DEFAULT NULL, ADD invoice_key VARCHAR(255) DEFAULT NULL, ADD shipping_street_number VARCHAR(255) DEFAULT NULL, ADD shipping_house_number VARCHAR(255) DEFAULT NULL, ADD shipping_apartment VARCHAR(255) DEFAULT NULL, ADD payment_method VARCHAR(255) DEFAULT NULL, DROP stripe_session_id, DROP shipping_address');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` ADD stripe_session_id VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD shipping_address VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, DROP invoice_id, DROP invoice_key, DROP shipping_street_number, DROP shipping_house_number, DROP shipping_apartment, DROP payment_method');
    }
}
