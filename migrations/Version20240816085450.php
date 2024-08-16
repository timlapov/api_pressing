<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240816085450 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE item DROP FOREIGN KEY FK_1F1B251EF8E98E09');
        $this->addSql('CREATE TABLE service_coefficients (id INT AUTO_INCREMENT NOT NULL, express_coefficient DOUBLE PRECISION NOT NULL, delicate_coefficient DOUBLE PRECISION NOT NULL, perfuming_coefficient DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('DROP TABLE additional_service');
        $this->addSql('DROP INDEX IDX_1F1B251EF8E98E09 ON item');
        $this->addSql('ALTER TABLE item ADD delicate TINYINT(1) NOT NULL, ADD perfuming TINYINT(1) NOT NULL, DROP additional_service_id');
        $this->addSql('ALTER TABLE `order` ADD express TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE additional_service (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, price_coefficient DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('DROP TABLE service_coefficients');
        $this->addSql('ALTER TABLE item ADD additional_service_id INT DEFAULT NULL, DROP delicate, DROP perfuming');
        $this->addSql('ALTER TABLE item ADD CONSTRAINT FK_1F1B251EF8E98E09 FOREIGN KEY (additional_service_id) REFERENCES additional_service (id)');
        $this->addSql('CREATE INDEX IDX_1F1B251EF8E98E09 ON item (additional_service_id)');
        $this->addSql('ALTER TABLE `order` DROP express');
    }
}
