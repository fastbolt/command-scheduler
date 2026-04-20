<?php

namespace Fastbolt\CommandScheduler\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Environment;

#[AsController]
#[Route('/command-scheduler/schedules/list', name: 'command_scheduler_schedules_list', methods: ['GET'])]
class ListCommandSchedulesController
{
    /**
     * @param Environment $twig
     */
    public function __construct(
        private readonly Environment $twig,
    )
    {
    }

    /**
     * @return Response
     */
    #[Route('/')]
    public function renderAction(): Response
    {
        return $this->renderView(
            '@CommandScheduler/schedules-list.html.twig',
        );
    }

    /**
     * @param string $view
     * @param array  $parameters
     *
     * @return Response
     */
    protected function renderView(string $view, array $parameters = []): Response
    {
        return new Response($this->twig->render($view, $parameters));
    }
}
