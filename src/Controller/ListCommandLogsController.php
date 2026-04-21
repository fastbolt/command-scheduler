<?php

namespace Fastbolt\CommandScheduler\Controller;

use Fastbolt\CommandScheduler\Provider\CommandLogProvider;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

#[Route('/command-scheduler/logs/list', name: 'command_scheduler_logs_list', methods: ['GET'])]
class ListCommandLogsController
{
    /**
     * @param Environment        $twig
     * @param CommandLogProvider $commandLogProvider
     */
    public function __construct(
        private readonly Environment $twig,
        private readonly CommandLogProvider $commandLogProvider
    ) {
    }

    /**
     * @return Response
     */
    public function __invoke(): Response
    {
        $logs = $this->commandLogProvider->getAllLogs();

        return new Response(
            $this->twig->render(
                '@CommandScheduler/logs-list.html.twig',
                [
                    'logs' => $logs,
                ]
            )
        );
    }
}
