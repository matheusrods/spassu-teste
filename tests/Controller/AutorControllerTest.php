<?php

namespace App\Tests\Controller;

use App\Entity\Assunto;
use App\Entity\Autor;
use App\Entity\Livro;

class AutorControllerTest extends AuthenticatedWebTestCase
{
    public function testIndexListsAutores(): void
    {
        $autor = new Autor();
        $autor->setNome('Machado de Assis');
        $this->em->persist($autor);
        $this->em->flush();

        $this->client->request('GET', '/autores/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('body', 'Machado de Assis');
    }

    public function testCreatingAutorSuccessfully(): void
    {
        $crawler = $this->client->request('GET', '/autores/novo');
        $this->client->submit($crawler->selectButton('Salvar')->form([
            'autor[nome]' => 'Cecília Meireles',
        ]));

        $this->assertResponseRedirects('/autores/');
        $this->client->followRedirect();
        $this->assertSelectorTextContains('[data-flash-alert-type-value="success"]', 'Autor cadastrado com sucesso.');
        $this->assertSelectorTextContains('body', 'Cecília Meireles');
    }

    public function testCreatingAutorWithDuplicateNomeShowsError(): void
    {
        $autor = new Autor();
        $autor->setNome('Machado de Assis');
        $this->em->persist($autor);
        $this->em->flush();

        $this->client->request('GET', '/autores/novo');
        $this->client->submitForm('Salvar', ['autor[nome]' => 'Machado de Assis']);

        $this->assertResponseStatusCodeSame(422);
        $this->assertSelectorTextContains('.invalid-feedback', 'Já existe um autor com este nome.');
    }

    public function testEditingAutor(): void
    {
        $autor = new Autor();
        $autor->setNome('Nome Original');
        $this->em->persist($autor);
        $this->em->flush();

        $crawler = $this->client->request('GET', '/autores/'.$autor->getId().'/editar');
        $this->client->submit($crawler->selectButton('Salvar')->form([
            'autor[nome]' => 'Nome Corrigido',
        ]));

        $this->assertResponseRedirects('/autores/');
        $this->client->followRedirect();
        $this->assertSelectorTextContains('body', 'Nome Corrigido');
    }

    public function testDeletingAutorWithoutLivrosSucceeds(): void
    {
        $autor = new Autor();
        $autor->setNome('Autor sem livros');
        $this->em->persist($autor);
        $this->em->flush();
        $autorId = $autor->getId();

        $crawler = $this->client->request('GET', '/autores/');
        $crawler->selectButton('Excluir')->form();
        $this->client->submitForm('Excluir');

        $this->assertResponseRedirects('/autores/');
        $this->client->followRedirect();
        $this->assertSelectorTextContains('[data-flash-alert-type-value="success"]', 'Autor excluído com sucesso.');

        $this->assertNull($this->em->getRepository(Autor::class)->find($autorId));
    }

    public function testDeletingAutorLinkedToLivroIsBlocked(): void
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

        $this->em->persist($autor);
        $this->em->persist($assunto);
        $this->em->persist($livro);
        $this->em->flush();

        $crawler = $this->client->request('GET', '/autores/');
        $crawler->selectButton('Excluir')->form();
        $this->client->submitForm('Excluir');

        $this->assertResponseRedirects('/autores/');
        $this->client->followRedirect();
        $this->assertSelectorTextContains('[data-flash-alert-type-value="error"]', 'Não é possível excluir este autor');

        $this->assertNotNull($this->em->getRepository(Autor::class)->find($autor->getId()));
    }
}
