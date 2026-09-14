<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Semeia autores, assuntos e livros de exemplo (mais de 10 em cada tabela)
 * só para demonstrar a paginação das 3 listagens. IDs em faixas altas
 * (1001+/2001+/3001+) para não colidir com registros já existentes.
 */
final class Version20260914010000 extends AbstractMigration
{
    private const int AUTOR_BASE_ID = 1001;
    private const int ASSUNTO_BASE_ID = 2001;
    private const int LIVRO_BASE_ID = 3001;

    /** @var string[] */
    private const array AUTORES = [
        'Machado de Assis', 'Clarice Lispector', 'José de Alencar', 'Guimarães Rosa',
        'Jorge Amado', 'Cecília Meireles', 'Carlos Drummond de Andrade', 'Manuel Bandeira',
        'Erico Verissimo', 'Rachel de Queiroz', 'Lima Barreto', 'Euclides da Cunha',
        'Aluísio Azevedo', 'Graciliano Ramos', 'Mário de Andrade',
    ];

    /** @var string[] */
    private const array ASSUNTOS = [
        'Memórias', 'Conto', 'Poesia', 'Crônica', 'Ficção Científica', 'Literatura Infantil',
        'Drama', 'Sátira', 'Regionalismo', 'Realismo', 'Modernismo', 'Ensaio',
    ];

    /** @var array<int, array{0: string, 1: int, 2: string}> título, índice do autor (em AUTORES), ano */
    private const array LIVROS = [
        ['Dom Casmurro', 0, '1899'],
        ['Memórias Póstumas de Brás Cubas', 0, '1881'],
        ['Quincas Borba', 0, '1891'],
        ['Esaú e Jacó', 0, '1904'],
        ['A Hora da Estrela', 1, '1977'],
        ['Perto do Coração Selvagem', 1, '1943'],
        ['A Paixão Segundo G.H.', 1, '1964'],
        ['Iracema', 2, '1865'],
        ['Senhora', 2, '1875'],
        ['Lucíola', 2, '1862'],
        ['Grande Sertão: Veredas', 3, '1956'],
        ['Sagarana', 3, '1946'],
        ['Gabriela, Cravo e Canela', 4, '1958'],
        ['Capitães da Areia', 4, '1937'],
        ['Romanceiro da Inconfidência', 5, '1953'],
        ['Sentimento do Mundo', 6, '1940'],
        ['Libertinagem', 7, '1930'],
        ['O Tempo e o Vento', 8, '1949'],
        ['O Quinze', 9, '1930'],
        ['Triste Fim de Policarpo Quaresma', 10, '1911'],
        ['Os Sertões', 11, '1902'],
        ['O Cortiço', 12, '1890'],
        ['Vidas Secas', 13, '1938'],
        ['São Bernardo', 13, '1934'],
        ['Macunaíma', 14, '1928'],
    ];

    /** @var string[] */
    private const array EDITORAS = [
        'Companhia das Letras', 'Editora Record', 'Editora Globo', 'Editora Nova Fronteira', 'Editora Ática',
    ];

    public function getDescription(): string
    {
        return 'Semeia autores, assuntos e livros de exemplo para demonstrar a paginação.';
    }

    public function up(Schema $schema): void
    {
        foreach (self::AUTORES as $i => $nome) {
            $this->addSql('INSERT INTO autor (cod_au, nome) VALUES (?, ?)', [self::AUTOR_BASE_ID + $i, $nome]);
        }

        foreach (self::ASSUNTOS as $i => $descricao) {
            $this->addSql('INSERT INTO assunto (cod_as, descricao) VALUES (?, ?)', [self::ASSUNTO_BASE_ID + $i, $descricao]);
        }

        foreach (self::LIVROS as $i => [$titulo, $autorIndex, $ano]) {
            $livroId = self::LIVRO_BASE_ID + $i;
            $editora = self::EDITORAS[$i % count(self::EDITORAS)];
            $edicao = ($i % 5) + 1;
            $valor = number_format(29.90 + ($i % 8) * 6.5, 2, '.', '');

            $this->addSql(
                'INSERT INTO livro (cod_l, titulo, editora, edicao, ano_publicacao, valor) VALUES (?, ?, ?, ?, ?, ?)',
                [$livroId, $titulo, $editora, $edicao, $ano, $valor],
            );

            $this->addSql(
                'INSERT INTO livro_autor (livro_cod_l, autor_cod_au) VALUES (?, ?)',
                [$livroId, self::AUTOR_BASE_ID + $autorIndex],
            );

            $assuntoIndiceA = $i % count(self::ASSUNTOS);
            $assuntoIndiceB = ($i + 5) % count(self::ASSUNTOS);

            $this->addSql(
                'INSERT INTO livro_assunto (livro_cod_l, assunto_cod_as) VALUES (?, ?)',
                [$livroId, self::ASSUNTO_BASE_ID + $assuntoIndiceA],
            );

            if ($assuntoIndiceB !== $assuntoIndiceA) {
                $this->addSql(
                    'INSERT INTO livro_assunto (livro_cod_l, assunto_cod_as) VALUES (?, ?)',
                    [$livroId, self::ASSUNTO_BASE_ID + $assuntoIndiceB],
                );
            }
        }
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DELETE FROM livro_assunto WHERE livro_cod_l >= '.self::LIVRO_BASE_ID);
        $this->addSql('DELETE FROM livro_autor WHERE livro_cod_l >= '.self::LIVRO_BASE_ID);
        $this->addSql('DELETE FROM livro WHERE cod_l >= '.self::LIVRO_BASE_ID);
        $this->addSql('DELETE FROM assunto WHERE cod_as >= '.self::ASSUNTO_BASE_ID);
        $this->addSql('DELETE FROM autor WHERE cod_au >= '.self::AUTOR_BASE_ID);
    }
}
