<?php

namespace App\Controller;

use App\Service\RelatorioLivroService;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RelatorioController extends AbstractController
{
    public function __construct(
        private readonly RelatorioLivroService $relatorioLivroService,
    ) {
    }

    #[Route('/relatorio', name: 'relatorio_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('relatorio/index.html.twig', [
            'autores' => $this->relatorioLivroService->buscarLivrosAgrupadosPorAutor(),
        ]);
    }

    #[Route('/relatorio/pdf', name: 'relatorio_pdf', methods: ['GET'])]
    public function pdf(): Response
    {
        $html = $this->renderView('relatorio/pdf.html.twig', [
            'autores' => $this->relatorioLivroService->buscarLivrosAgrupadosPorAutor(),
        ]);

        $options = new Options();
        $options->set('isRemoteEnabled', false);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return new Response($dompdf->output(), Response::HTTP_OK, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="relatorio-livros-por-autor.pdf"',
        ]);
    }
}
