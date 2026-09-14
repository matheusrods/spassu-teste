<?php

namespace App\Exception;

/**
 * Lançada ao tentar excluir um Autor ou Assunto que ainda está vinculado a
 * algum Livro (seja pela checagem prévia na ORM, seja pelo próprio banco ao
 * rejeitar a exclusão por violação de chave estrangeira).
 */
final class RegistroVinculadoException extends \RuntimeException
{
}
