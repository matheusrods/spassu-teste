<?php

namespace App\Exception;

/**
 * Lançada ao persistir um Livro referenciando um Autor ou Assunto que foi
 * excluído entre a validação do formulário (o EntityType já rejeita IDs
 * inexistentes) e o flush; é a rede de segurança contra essa corrida.
 */
final class RelacionamentoInexistenteException extends \RuntimeException
{
}
