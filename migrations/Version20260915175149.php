<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260915175149 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adiciona índice único em autor.nome, seguindo o mesmo padrão de assunto.descricao.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE UNIQUE INDEX uniq_autor_nome ON autor (nome)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX uniq_autor_nome ON autor');
    }
}
