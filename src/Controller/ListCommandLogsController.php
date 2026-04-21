<?php

namespace Fastbolt\CommandScheduler\Controller;

use Fastbolt\CommandScheduler\Provider\CommandLogProvider;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Twig\Environment;

#[Route('/command-scheduler/logs/list', name: 'command_scheduler_logs_list', methods: ['GET'])]
final class ListCommandLogsController extends BaseController
{
    /**
     * @param RequestStack       $requestStack
     * @param Environment        $environment
     * @param Router    $router
     * @param CommandLogProvider $commandLogProvider
     */
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly Environment $environment,
        private readonly Router $router,
        private readonly CommandLogProvider $commandLogProvider
    ) {
        parent::__construct($this->requestStack, $this->environment, $this->router);
    }

    /**
     * @return Response
     */
    public function __invoke(): Response
    {
        $logs = $this->commandLogProvider->getAllLogs();

        return $this->renderView(
            '@CommandScheduler/logs-list.html.twig',
            [
                'logs' => $logs,
            ]
        );
    }
}
