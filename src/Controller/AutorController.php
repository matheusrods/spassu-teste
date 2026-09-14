<?php

namespace App\Controller;

use App\Entity\Autor;
use App\Exception\RegistroVinculadoException;
use App\Form\AutorType;
use App\Repository\AutorRepository;
use App\Service\AutorService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/autores')]
class AutorController extends AbstractController
{
    private const int POR_PAGINA = 10;

    public function __construct(
        private readonly AutorService $autorService,
    ) {
    }

    #[Route('/', name: 'autor_index', methods: ['GET'])]
    public function index(Request $request, AutorRepository $autorRepository, PaginatorInterface $paginator): Response
    {
        $nome = $request->query->get('nome');

        $autores = $paginator->paginate(
            $autorRepository->findFiltrados($nome),
            $request->query->getInt('page', 1),
            self::POR_PAGINA,
        );

        return $this->render('autor/index.html.twig', [
            'autores' => $autores,
            'filtroNome' => $nome,
        ]);
    }

    #[Route('/novo', name: 'autor_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $autor = new Autor();
        $form = $this->createForm(AutorType::class, $autor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->autorService->criar($autor);

            $this->addFlash('success', 'autor.cadastrado');

            return $this->redirectToRoute('autor_index');
        }

        return $this->render('autor/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}/editar', name: 'autor_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Autor $autor): Response
    {
        $form = $this->createForm(AutorType::class, $autor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->autorService->atualizar($autor);

            $this->addFlash('success', 'autor.atualizado');

            return $this->redirectToRoute('autor_index');
        }

        return $this->render('autor/edit.html.twig', [
            'autor' => $autor,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/excluir', name: 'autor_delete', methods: ['POST'])]
    public function delete(Request $request, Autor $autor): Response
    {
        if (!$this->isCsrfTokenValid('delete-autor-'.$autor->getId(), $request->getPayload()->getString('_token'))) {
            $this->addFlash('error', 'csrf.invalido');

            return $this->redirectToRoute('autor_index');
        }

        try {
            $this->autorService->excluir($autor);
            $this->addFlash('success', 'autor.excluido');
        } catch (RegistroVinculadoException $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('autor_index');
    }
}
