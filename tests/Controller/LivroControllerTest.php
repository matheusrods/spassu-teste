<?php

namespace App\Tests\Controller;

use App\Entity\Assunto;
use App\Entity\Autor;
use App\Entity\Livro;

class LivroControllerTest extends AuthenticatedWebTestCase
{
    private function criarAutor(string $nome): Autor
    {
        $autor = new Autor();
        $autor->setNome($nome);
        $this->em->persist($autor);
        $this->em->flush();

        return $autor;
    }

    private function criarAssunto(string $descricao): Assunto
    {
        $assunto = new Assunto();
        $assunto->setDescricao($descricao);
        $this->em->persist($assunto);
        $this->em->flush();

        return $assunto;
    }

    public function testIndexListsLivros(): void
    {
        $autor = $this->criarAutor('Machado de Assis');
        $assunto = $this->criarAssunto('Romance');

        $livro = new Livro();
        $livro->setTitulo('Dom Casmurro')
            ->setEditora('Editora Nacional')
            ->setEdicao(1)
            ->setAnoPublicacao('1899')
            ->setValor('39.90')
            ->addAutor($autor)
            ->addAssunto($assunto);
        $this->em->persist($livro);
        $this->em->flush();

        $this->client->request('GET', '/livros/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('body', 'Dom Casmurro');
    }

    public function testCreatingLivroSuccessfully(): void
    {
        $autor = $this->criarAutor('Machado de Assis');
        $assunto = $this->criarAssunto('Romance');

        $crawler = $this->client->request('GET', '/livros/novo');
        $form = $crawler->selectButton('Salvar')->form();
        $form['livro[titulo]'] = 'Dom Casmurro';
        $form['livro[editora]'] = 'Editora Nacional';
        $form['livro[edicao]'] = '1';
        $form['livro[anoPublicacao]'] = '1899';
        $form['livro[valor]'] = '39,90';
        $form['livro[autores]'] = [(string) $autor->getId()];
        $form['livro[assuntos]'] = [(string) $assunto->getId()];

        $this->client->submit($form);

        $this->assertResponseRedirects('/livros/');
        $this->client->followRedirect();
        $this->assertSelectorTextContains('[data-flash-alert-type-value="success"]', 'Livro cadastrado com sucesso.');
        $this->assertSelectorTextContains('body', 'Dom Casmurro');
    }

    public function testCreatingLivroWithoutAutorShowsValidationError(): void
    {
        $this->criarAssunto('Romance');

        $crawler = $this->client->request('GET', '/livros/novo');
        $form = $crawler->selectButton('Salvar')->form();
        $form['livro[titulo]'] = 'Livro sem autor';
        $form['livro[editora]'] = 'Editora X';
        $form['livro[edicao]'] = '1';
        $form['livro[anoPublicacao]'] = '2024';
        $form['livro[valor]'] = '10,00';

        $this->client->submit($form);

        $this->assertResponseStatusCodeSame(422);
    }

    public function testEditingLivro(): void
    {
        $autor = $this->criarAutor('Machado de Assis');
        $assunto = $this->criarAssunto('Romance');

        $livro = new Livro();
        $livro->setTitulo('Título Original')
            ->setEditora('Editora Nacional')
            ->setEdicao(1)
            ->setAnoPublicacao('1899')
            ->setValor('39.90')
            ->addAutor($autor)
            ->addAssunto($assunto);
        $this->em->persist($livro);
        $this->em->flush();

        $crawler = $this->client->request('GET', '/livros/'.$livro->getId().'/editar');
        $form = $crawler->selectButton('Salvar')->form();
        $form['livro[titulo]'] = 'Título Corrigido';

        $this->client->submit($form);

        $this->assertResponseRedirects('/livros/');
        $this->client->followRedirect();
        $this->assertSelectorTextContains('[data-flash-alert-type-value="success"]', 'Livro atualizado com sucesso.');
        $this->assertSelectorTextContains('body', 'Título Corrigido');
    }

    public function testDeletingLivro(): void
    {
        $autor = $this->criarAutor('Machado de Assis');
        $assunto = $this->criarAssunto('Romance');

        $livro = new Livro();
        $livro->setTitulo('Dom Casmurro')
            ->setEditora('Editora Nacional')
            ->setEdicao(1)
            ->setAnoPublicacao('1899')
            ->setValor('39.90')
            ->addAutor($autor)
            ->addAssunto($assunto);
        $this->em->persist($livro);
        $this->em->flush();
        $livroId = $livro->getId();

        $crawler = $this->client->request('GET', '/livros/');
        $crawler->selectButton('Excluir')->form();
        $this->client->submitForm('Excluir');

        $this->assertResponseRedirects('/livros/');
        $this->client->followRedirect();
        $this->assertSelectorTextContains('[data-flash-alert-type-value="success"]', 'Livro excluído com sucesso.');

        $this->assertNull($this->em->getRepository(Livro::class)->find($livroId));
    }
}
