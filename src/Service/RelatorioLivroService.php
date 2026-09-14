<?php

namespace App\Service;

use Doctrine\DBAL\Connection;

class RelatorioLivroService
{
    public function __construct(
        private readonly Connection $connection,
    ) {
    }

    /**
     * @return array<int, array{nome: string, livros: array<int, array<string, mixed>>, total: string}>
     */
    public function buscarLivrosAgrupadosPorAutor(): array
    {
        $linhas = $this->connection->fetchAllAssociative(
            'SELECT * FROM vw_relatorio_livros ORDER BY autor_nome, livro_titulo'
        );

        $autores = [];
        foreach ($linhas as $linha) {
            $codAutor = $linha['autor_cod_au'];

            if (!isset($autores[$codAutor])) {
                $autores[$codAutor] = [
                    'nome' => $linha['autor_nome'],
                    'livros' => [],
                    'total' => '0',
                ];
            }

            $autores[$codAutor]['livros'][] = $linha;
            $autores[$codAutor]['total'] = bcadd($autores[$codAutor]['total'], $linha['livro_valor'], 2);
        }

        return $autores;
    }
}
