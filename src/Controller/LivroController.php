<?php

namespace App\Controller;

use App\Entity\Livro;
use App\Exception\RelacionamentoInexistenteException;
use App\Form\LivroType;
use App\Repository\AssuntoRepository;
use App\Repository\AutorRepository;
use App\Repository\LivroRepository;
use App\Service\LivroService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/livros')]
class LivroController extends AbstractController
{
    private const int POR_PAGINA = 10;

    public function __construct(
        private readonly LivroService $livroService,
    ) {
    }

    #[Route('/', name: 'livro_index', methods: ['GET'])]
    public function index(
        Request $request,
        LivroRepository $livroRepository,
        AutorRepository $autorRepository,
        AssuntoRepository $assuntoRepository,
        PaginatorInterface $paginator,
    ): Response {
        $titulo = $request->query->get('titulo') ?: null;
        // getInt() lança BadRequestException para string vazia; o <select>
        // "Todos os autores/assuntos" envia exatamente isso (autor=, assunto=).
        $autorId = $this->filtroComoId($request->query->get('autor'));
        $assuntoId = $this->filtroComoId($request->query->get('assunto'));

        $livros = $paginator->paginate(
            $livroRepository->findFiltrados($titulo, $autorId, $assuntoId),
            $request->query->getInt('page', 1),
            self::POR_PAGINA,
        );

        return $this->render('livro/index.html.twig', [
            'livros' => $livros,
            'autores' => $autorRepository->findAllOrdenadosPorNome()->getResult(),
            'assuntos' => $assuntoRepository->findAllOrdenadosPorDescricao()->getResult(),
            'filtroTitulo' => $titulo,
            'filtroAutor' => $autorId,
            'filtroAssunto' => $assuntoId,
        ]);
    }

    #[Route('/novo', name: 'livro_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $livro = new Livro();
        $form = $this->createForm(LivroType::class, $livro);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->livroService->criar($livro);

                $this->addFlash('success', 'livro.cadastrado');

                return $this->redirectToRoute('livro_index');
            } catch (RelacionamentoInexistenteException $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('livro/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}/editar', name: 'livro_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Livro $livro): Response
    {
        $form = $this->createForm(LivroType::class, $livro);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->livroService->atualizar($livro);

                $this->addFlash('success', 'livro.atualizado');

                return $this->redirectToRoute('livro_index');
            } catch (RelacionamentoInexistenteException $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('livro/edit.html.twig', [
            'livro' => $livro,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/excluir', name: 'livro_delete', methods: ['POST'])]
    public function delete(Request $request, Livro $livro): Response
    {
        if (!$this->isCsrfTokenValid('delete-livro-'.$livro->getId(), $request->getPayload()->getString('_token'))) {
            $this->addFlash('error', 'csrf.invalido');

            return $this->redirectToRoute('livro_index');
        }

        $this->livroService->excluir($livro);

        $this->addFlash('success', 'livro.excluido');

        return $this->redirectToRoute('livro_index');
    }

    private function filtroComoId(?string $valor): ?int
    {
        return ($valor !== null && $valor !== '' && ctype_digit($valor)) ? (int) $valor : null;
    }
}
