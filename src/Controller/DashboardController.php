<?php

namespace Fastbolt\CommandScheduler\Controller;

use Fastbolt\CommandScheduler\Provider\CommandLogProvider;
use Fastbolt\CommandScheduler\Provider\CommandScheduleProvider;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Environment;

#[Route('/command-scheduler', name: 'command_scheduler_dashboard', methods: ['GET'])]
final class DashboardController extends BaseController
{
    /**
     * @param RequestStack            $requestStack
     * @param Environment             $environment
     * @param CommandLogProvider      $commandLogProvider
     * @param CommandScheduleProvider $commandScheduleProvider
     */
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly Environment $environment,
        private readonly Router $router,
        private readonly CommandLogProvider $commandLogProvider,
        private readonly CommandScheduleProvider $commandScheduleProvider
    ) {
        parent::__construct($this->requestStack, $this->environment, $this->router);
    }

    /**
     * @return Response
     */
    public function __invoke(): Response
    {
        return $this->renderView(
            '@CommandScheduler/dashboard.html.twig',
            [
                'numLogs'      => $this->commandLogProvider->getNumLogs(),
                'numSchedules' => $this->commandScheduleProvider->getNumSchedules(),
            ]
        );
    }
}
