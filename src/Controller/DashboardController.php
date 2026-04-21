<?php

namespace Fastbolt\CommandScheduler\Controller;

use Fastbolt\CommandScheduler\Provider\CommandLogProvider;
use Fastbolt\CommandScheduler\Provider\CommandScheduleProvider;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

#[Route('/command-scheduler', name: 'command_scheduler_dashboard', methods: ['GET'])]
class DashboardController
{
    /**
     * @param Environment             $twig
     * @param CommandLogProvider      $commandLogProvider
     * @param CommandScheduleProvider $commandScheduleProvider
     */
    public function __construct(
        private readonly Environment $twig,
        private readonly CommandLogProvider $commandLogProvider,
        private readonly CommandScheduleProvider $commandScheduleProvider
    ) {
    }

    /**
     * @return Response
     */
    public function __invoke(): Response
    {
        return new Response(
            $this->twig->render(
                '@CommandScheduler/dashboard.html.twig',
                [
                    'numLogs' => $this->commandLogProvider->getNumLogs(),
                    'numSchedules' => $this->commandScheduleProvider->getNumSchedules(),
                ]
            )
        );
    }
}
