<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230308140425 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE command (id INT AUTO_INCREMENT NOT NULL, id_customer_id INT NOT NULL, shop_id INT NOT NULL, date_created DATETIME NOT NULL, recup_date VARCHAR(255) NOT NULL, INDEX IDX_8ECAEAD48B870E04 (id_customer_id), INDEX IDX_8ECAEAD44D16C4DD (shop_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE customer_command (id INT AUTO_INCREMENT NOT NULL, command_id INT NOT NULL, products_id INT NOT NULL, quantity INT DEFAULT NULL, INDEX IDX_92174E6A33E1689A (command_id), INDEX IDX_92174E6A6C8A81A9 (products_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messages (id INT AUTO_INCREMENT NOT NULL, seller_id INT NOT NULL, message VARCHAR(255) NOT NULL, mail VARCHAR(255) NOT NULL, INDEX IDX_DB021E968DE820D9 (seller_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, price DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_shop (id INT AUTO_INCREMENT NOT NULL, id_product_id INT NOT NULL, id_shop_id INT NOT NULL, quantity INT DEFAULT NULL, INDEX IDX_21826E03E00EE68D (id_product_id), INDEX IDX_21826E03938B6DAD (id_shop_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE shop (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, phone VARCHAR(255) DEFAULT NULL, state VARCHAR(255) NOT NULL, open_time VARCHAR(255) NOT NULL, closing_time VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE command ADD CONSTRAINT FK_8ECAEAD48B870E04 FOREIGN KEY (id_customer_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE command ADD CONSTRAINT FK_8ECAEAD44D16C4DD FOREIGN KEY (shop_id) REFERENCES shop (id)');
        $this->addSql('ALTER TABLE customer_command ADD CONSTRAINT FK_92174E6A33E1689A FOREIGN KEY (command_id) REFERENCES command (id)');
        $this->addSql('ALTER TABLE customer_command ADD CONSTRAINT FK_92174E6A6C8A81A9 FOREIGN KEY (products_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE messages ADD CONSTRAINT FK_DB021E968DE820D9 FOREIGN KEY (seller_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE product_shop ADD CONSTRAINT FK_21826E03E00EE68D FOREIGN KEY (id_product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE product_shop ADD CONSTRAINT FK_21826E03938B6DAD FOREIGN KEY (id_shop_id) REFERENCES shop (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE command DROP FOREIGN KEY FK_8ECAEAD48B870E04');
        $this->addSql('ALTER TABLE command DROP FOREIGN KEY FK_8ECAEAD44D16C4DD');
        $this->addSql('ALTER TABLE customer_command DROP FOREIGN KEY FK_92174E6A33E1689A');
        $this->addSql('ALTER TABLE customer_command DROP FOREIGN KEY FK_92174E6A6C8A81A9');
        $this->addSql('ALTER TABLE messages DROP FOREIGN KEY FK_DB021E968DE820D9');
        $this->addSql('ALTER TABLE product_shop DROP FOREIGN KEY FK_21826E03E00EE68D');
        $this->addSql('ALTER TABLE product_shop DROP FOREIGN KEY FK_21826E03938B6DAD');
        $this->addSql('DROP TABLE command');
        $this->addSql('DROP TABLE customer_command');
        $this->addSql('DROP TABLE messages');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE product_shop');
        $this->addSql('DROP TABLE shop');
        $this->addSql('DROP TABLE user');
    }
}
