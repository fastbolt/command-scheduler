<?php

namespace Fastbolt\CommandScheduler\Controller;

use Fastbolt\CommandScheduler\Provider\CommandScheduleProvider;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Twig\Environment;

#[AsController]
#[Route('/command-scheduler/schedules/list', name: 'command_scheduler_schedules_list', methods: ['GET'])]
final class ListCommandSchedulesController extends BaseController
{
    /**
     * @param RequestStack            $requestStack
     * @param Environment             $environment
     * @param Router         $router
     * @param CommandScheduleProvider $commandScheduleProvider
     */
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly Environment $environment,
        private readonly Router $router,
        private readonly CommandScheduleProvider $commandScheduleProvider,
    ) {
        parent::__construct($this->requestStack, $this->environment, $this->router);
    }

    /**
     * @return Response
     */
    public function __invoke(): Response
    {
        return $this->renderView(
            '@CommandScheduler/schedules-list.html.twig',
            [
                'schedules' => $this->commandScheduleProvider->getSchedules(),
            ]
        );
    }
}
