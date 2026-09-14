<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260910192319 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Cria a view vw_relatorio_livros usada pelo relatório de livros agrupado por autor.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE VIEW vw_relatorio_livros AS
            SELECT
                au.cod_au AS autor_cod_au,
                au.nome AS autor_nome,
                l.cod_l AS livro_cod_l,
                l.titulo AS livro_titulo,
                l.editora AS livro_editora,
                l.edicao AS livro_edicao,
                l.ano_publicacao AS livro_ano_publicacao,
                l.valor AS livro_valor,
                GROUP_CONCAT(DISTINCT a.descricao ORDER BY a.descricao SEPARATOR ', ') AS livro_assuntos
            FROM autor au
            INNER JOIN livro_autor la ON la.autor_cod_au = au.cod_au
            INNER JOIN livro l ON l.cod_l = la.livro_cod_l
            LEFT JOIN livro_assunto lla ON lla.livro_cod_l = l.cod_l
            LEFT JOIN assunto a ON a.cod_as = lla.assunto_cod_as
            GROUP BY au.cod_au, au.nome, l.cod_l, l.titulo, l.editora, l.edicao, l.ano_publicacao, l.valor
            ORDER BY au.nome, l.titulo
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP VIEW vw_relatorio_livros');
    }
}
