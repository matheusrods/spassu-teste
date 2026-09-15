<?php

namespace App\Exception;

/**
 * Lançada ao persistir um Autor cujo nome já existe, quando isso só é
 * detectado no banco (a validação de formulário via #[UniqueEntity] cobre o
 * caso comum; isto é a rede de segurança contra corrida entre duas requisições).
 */
final class AutorDuplicadoException extends \RuntimeException
{
}
