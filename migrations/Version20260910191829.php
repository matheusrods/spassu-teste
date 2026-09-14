<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260910191829 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE assunto (cod_as INT AUTO_INCREMENT NOT NULL, descricao VARCHAR(20) NOT NULL, PRIMARY KEY (cod_as)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE autor (cod_au INT AUTO_INCREMENT NOT NULL, nome VARCHAR(40) NOT NULL, PRIMARY KEY (cod_au)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE livro (cod_l INT AUTO_INCREMENT NOT NULL, titulo VARCHAR(40) NOT NULL, editora VARCHAR(40) NOT NULL, edicao INT NOT NULL, ano_publicacao VARCHAR(4) NOT NULL, valor NUMERIC(10, 2) NOT NULL, PRIMARY KEY (cod_l)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE livro_autor (livro_cod_l INT NOT NULL, autor_cod_au INT NOT NULL, INDEX IDX_67499927A7FD5E9 (livro_cod_l), INDEX IDX_67499921AE83779 (autor_cod_au), PRIMARY KEY (livro_cod_l, autor_cod_au)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE livro_assunto (livro_cod_l INT NOT NULL, assunto_cod_as INT NOT NULL, INDEX IDX_53C2C52A7A7FD5E9 (livro_cod_l), INDEX IDX_53C2C52A568DC9E5 (assunto_cod_as), PRIMARY KEY (livro_cod_l, assunto_cod_as)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE livro_autor ADD CONSTRAINT FK_67499927A7FD5E9 FOREIGN KEY (livro_cod_l) REFERENCES livro (cod_l)');
        $this->addSql('ALTER TABLE livro_autor ADD CONSTRAINT FK_67499921AE83779 FOREIGN KEY (autor_cod_au) REFERENCES autor (cod_au)');
        $this->addSql('ALTER TABLE livro_assunto ADD CONSTRAINT FK_53C2C52A7A7FD5E9 FOREIGN KEY (livro_cod_l) REFERENCES livro (cod_l)');
        $this->addSql('ALTER TABLE livro_assunto ADD CONSTRAINT FK_53C2C52A568DC9E5 FOREIGN KEY (assunto_cod_as) REFERENCES assunto (cod_as)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE livro_autor DROP FOREIGN KEY FK_67499927A7FD5E9');
        $this->addSql('ALTER TABLE livro_autor DROP FOREIGN KEY FK_67499921AE83779');
        $this->addSql('ALTER TABLE livro_assunto DROP FOREIGN KEY FK_53C2C52A7A7FD5E9');
        $this->addSql('ALTER TABLE livro_assunto DROP FOREIGN KEY FK_53C2C52A568DC9E5');
        $this->addSql('DROP TABLE assunto');
        $this->addSql('DROP TABLE autor');
        $this->addSql('DROP TABLE livro');
        $this->addSql('DROP TABLE livro_autor');
        $this->addSql('DROP TABLE livro_assunto');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
