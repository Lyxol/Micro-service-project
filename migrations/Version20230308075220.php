<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230308075220 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE product_shop (id INT AUTO_INCREMENT NOT NULL, id_product_id INT NOT NULL, id_shop_id INT NOT NULL, quantity INT DEFAULT NULL, INDEX IDX_21826E03E00EE68D (id_product_id), INDEX IDX_21826E03938B6DAD (id_shop_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE shop (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, phone VARCHAR(255) DEFAULT NULL, state VARCHAR(255) NOT NULL, open_time TIME NOT NULL, closing_time TIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE product_shop ADD CONSTRAINT FK_21826E03E00EE68D FOREIGN KEY (id_product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE product_shop ADD CONSTRAINT FK_21826E03938B6DAD FOREIGN KEY (id_shop_id) REFERENCES shop (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product_shop DROP FOREIGN KEY FK_21826E03E00EE68D');
        $this->addSql('ALTER TABLE product_shop DROP FOREIGN KEY FK_21826E03938B6DAD');
        $this->addSql('DROP TABLE product_shop');
        $this->addSql('DROP TABLE shop');
    }
}
