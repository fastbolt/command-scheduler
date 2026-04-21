<?php

namespace Fastbolt\CommandScheduler\Controller;

use Fastbolt\CommandScheduler\Provider\CommandScheduleProvider;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Environment;

#[AsController]
#[Route('/command-scheduler/schedules/list', name: 'command_scheduler_schedules_list', methods: ['GET'])]
class ListCommandSchedulesController
{
    /**
     * @param Environment             $twig
     * @param CommandScheduleProvider $commandScheduleProvider
     */
    public function __construct(
        private readonly Environment $twig,
        private readonly CommandScheduleProvider $commandScheduleProvider,
    ) {
    }

    /**
     * @return Response
     */
    public function __invoke(): Response
    {
        return new Response(
            $this->twig->render(
                '@CommandScheduler/schedules-list.html.twig',
                [
                    'schedules' => $this->commandScheduleProvider->getSchedules(),
                ]
            )
        );
    }
}
