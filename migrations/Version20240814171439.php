<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240814171439 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE order_status ADD description LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE service ADD image_url VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE subcategory ADD image_url VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE order_status DROP description');
        $this->addSql('ALTER TABLE service DROP image_url');
        $this->addSql('ALTER TABLE subcategory DROP image_url');
    }
}
