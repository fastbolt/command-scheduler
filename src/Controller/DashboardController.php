<?php

namespace Fastbolt\CommandScheduler\Controller;

use Fastbolt\CommandScheduler\Provider\CommandLogProvider;
use Fastbolt\CommandScheduler\Provider\CommandScheduleProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

#[Route('/command-scheduler', name: 'command_scheduler_dashboard', methods: ['GET'])]
class DashboardController
{
    public function __construct(
        private readonly Environment $twig,
        private readonly CommandLogProvider $commandLogProvider,
        private readonly CommandScheduleProvider $commandScheduleProvider
    ) {
    }

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

