<?php

namespace App\Tests\Controller;

use App\Entity\Assunto;
use App\Entity\Autor;
use App\Entity\Livro;

class AssuntoControllerTest extends AuthenticatedWebTestCase
{
    public function testIndexListsAssuntos(): void
    {
        $client = $this->client;

        $assunto = new Assunto();
        $assunto->setDescricao('Romance');
        $this->em->persist($assunto);
        $this->em->flush();

        $client->request('GET', '/assuntos/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('body', 'Romance');
    }

    public function testCreatingAssuntoWithDuplicateDescricaoShowsError(): void
    {
        $client = $this->client;

        $assunto = new Assunto();
        $assunto->setDescricao('Romance');
        $this->em->persist($assunto);
        $this->em->flush();

        $client->request('GET', '/assuntos/novo');
        $client->submitForm('Salvar', ['assunto[descricao]' => 'Romance']);

        $this->assertResponseStatusCodeSame(422);
        $this->assertSelectorTextContains('.invalid-feedback', 'Já existe um assunto com esta descrição.');
    }

    public function testDeletingAssuntoLinkedToLivroIsBlocked(): void
    {
        $client = $this->client;

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

        $client->request('GET', '/assuntos/');
        $client->submitForm('Excluir');

        $this->assertResponseRedirects('/assuntos/');
        $client->followRedirect();
        $this->assertSelectorTextContains('[data-flash-alert-type-value="error"]', 'Não é possível excluir este assunto');

        $this->assertNotNull($this->em->getRepository(Assunto::class)->find($assunto->getId()));
    }
}
