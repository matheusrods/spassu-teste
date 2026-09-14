<?php

namespace App\Controller;

use App\Entity\Assunto;
use App\Exception\AssuntoDuplicadoException;
use App\Exception\RegistroVinculadoException;
use App\Form\AssuntoType;
use App\Repository\AssuntoRepository;
use App\Service\AssuntoService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/assuntos')]
class AssuntoController extends AbstractController
{
    private const int POR_PAGINA = 10;

    public function __construct(
        private readonly AssuntoService $assuntoService,
    ) {
    }

    #[Route('/', name: 'assunto_index', methods: ['GET'])]
    public function index(Request $request, AssuntoRepository $assuntoRepository, PaginatorInterface $paginator): Response
    {
        $descricao = $request->query->get('descricao');

        $assuntos = $paginator->paginate(
            $assuntoRepository->findFiltrados($descricao),
            $request->query->getInt('page', 1),
            self::POR_PAGINA,
        );

        return $this->render('assunto/index.html.twig', [
            'assuntos' => $assuntos,
            'filtroDescricao' => $descricao,
        ]);
    }

    #[Route('/novo', name: 'assunto_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $assunto = new Assunto();
        $form = $this->createForm(AssuntoType::class, $assunto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->assuntoService->criar($assunto);

                $this->addFlash('success', 'assunto.cadastrado');

                return $this->redirectToRoute('assunto_index');
            } catch (AssuntoDuplicadoException $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('assunto/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}/editar', name: 'assunto_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Assunto $assunto): Response
    {
        $form = $this->createForm(AssuntoType::class, $assunto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->assuntoService->atualizar($assunto);

                $this->addFlash('success', 'assunto.atualizado');

                return $this->redirectToRoute('assunto_index');
            } catch (AssuntoDuplicadoException $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('assunto/edit.html.twig', [
            'assunto' => $assunto,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/excluir', name: 'assunto_delete', methods: ['POST'])]
    public function delete(Request $request, Assunto $assunto): Response
    {
        if (!$this->isCsrfTokenValid('delete-assunto-'.$assunto->getId(), $request->getPayload()->getString('_token'))) {
            $this->addFlash('error', 'csrf.invalido');

            return $this->redirectToRoute('assunto_index');
        }

        try {
            $this->assuntoService->excluir($assunto);
            $this->addFlash('success', 'assunto.excluido');
        } catch (RegistroVinculadoException $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('assunto_index');
    }
}
