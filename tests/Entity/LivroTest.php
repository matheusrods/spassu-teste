<?php

namespace App\Tests\Entity;

use App\Entity\Assunto;
use App\Entity\Autor;
use App\Entity\Livro;
use PHPUnit\Framework\TestCase;

class LivroTest extends TestCase
{
    public function testSettersAndGetters(): void
    {
        $livro = new Livro();
        $livro->setTitulo('Dom Casmurro')
            ->setEditora('Editora Nacional')
            ->setEdicao(1)
            ->setAnoPublicacao('1899')
            ->setValor('39.90');

        $this->assertSame('Dom Casmurro', $livro->getTitulo());
        $this->assertSame('Editora Nacional', $livro->getEditora());
        $this->assertSame(1, $livro->getEdicao());
        $this->assertSame('1899', $livro->getAnoPublicacao());
        $this->assertSame('39.90', $livro->getValor());
    }

    public function testAddAutorIsIdempotent(): void
    {
        $livro = new Livro();
        $autor = new Autor();
        $autor->setNome('Machado de Assis');

        $livro->addAutor($autor);
        $livro->addAutor($autor);

        $this->assertCount(1, $livro->getAutores());
        $this->assertTrue($livro->getAutores()->contains($autor));
    }

    public function testRemoveAutor(): void
    {
        $livro = new Livro();
        $autor = new Autor();
        $autor->setNome('Machado de Assis');

        $livro->addAutor($autor);
        $livro->removeAutor($autor);

        $this->assertCount(0, $livro->getAutores());
    }

    public function testAddAndRemoveAssunto(): void
    {
        $livro = new Livro();
        $assunto = new Assunto();
        $assunto->setDescricao('Romance');

        $livro->addAssunto($assunto);
        $this->assertTrue($livro->getAssuntos()->contains($assunto));

        $livro->removeAssunto($assunto);
        $this->assertCount(0, $livro->getAssuntos());
    }

    public function testToStringReturnsTitulo(): void
    {
        $livro = new Livro();
        $livro->setTitulo('Iracema');

        $this->assertSame('Iracema', (string) $livro);
    }
}
