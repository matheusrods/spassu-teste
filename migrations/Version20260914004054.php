<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260914004054 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Cria a tabela user e semeia o usuário admin para acesso ao sistema.';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');

        // Usuário de demonstração: admin@teste-spassu.local / Livraria@2026
        $this->addSql(<<<'SQL'
            INSERT INTO user (email, roles, password)
            VALUES ('admin@teste-spassu.local', '[]', '$2y$13$OLU1Zk5FCrhGRqgQXCA/S.GnA/TFf7cpM5qarT.7dl3R03..EZM1G')
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE user');
    }
}
