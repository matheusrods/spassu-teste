<?php

namespace App\Tests\Entity;

use App\Entity\Assunto;
use App\Entity\Autor;
use App\Entity\Livro;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class LivroValidationTest extends TestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        $this->validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();
    }

    private function livroValido(): Livro
    {
        $autor = new Autor();
        $autor->setNome('Machado de Assis');

        $assunto = new Assunto();
        $assunto->setDescricao('Romance');

        $livro = new Livro();
        $livro->setTitulo('Dom Casmurro')
            ->setEditora('Editora Nacional')
            ->setEdicao(1)
            ->setAnoPublicacao('1899')
            ->setValor('39.90')
            ->addAutor($autor)
            ->addAssunto($assunto);

        return $livro;
    }

    public function testLivroValidoNaoGeraViolacoes(): void
    {
        $violations = $this->validator->validate($this->livroValido());

        $this->assertCount(0, $violations);
    }

    public function testAnoPublicacaoComFormatoInvalidoGeraViolacao(): void
    {
        $livro = $this->livroValido()->setAnoPublicacao('99');

        $violations = $this->validator->validate($livro);

        $this->assertGreaterThan(0, count($violations));
    }

    public function testLivroSemAutorGeraViolacao(): void
    {
        $livro = $this->livroValido();
        $livro->removeAutor($livro->getAutores()->first());

        $violations = $this->validator->validate($livro);

        $this->assertGreaterThan(0, count($violations));
    }

    public function testValorNegativoGeraViolacao(): void
    {
        $livro = $this->livroValido()->setValor('-10.00');

        $violations = $this->validator->validate($livro);

        $this->assertGreaterThan(0, count($violations));
    }
}
