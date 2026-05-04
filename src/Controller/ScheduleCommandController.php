<?php

namespace Fastbolt\CommandScheduler\Controller;

use Fastbolt\CommandScheduler\Entity\CommandSchedule;
use Fastbolt\CommandScheduler\Persistence\CommandLogPersister;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Environment;

#[Route('/command-scheduler/schedule/{id}', name: 'command_scheduler_schedule_command', methods: ['GET'])]
final class ScheduleCommandController extends BaseController
{
    /**
     * @param RequestStack        $requestStack
     * @param Environment         $environment
     * @param Router              $router
     * @param CommandLogPersister $persister
     */
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly Environment $environment,
        private readonly Router $router,
        private readonly CommandLogPersister $persister,
    ) {
        parent::__construct($this->requestStack, $this->environment, $this->router);
    }

    /**
     * @param Request         $request
     * @param CommandSchedule $commandSchedule
     *
     * @return RedirectResponse
     */
    public function __invoke(Request $request, CommandSchedule $commandSchedule): RedirectResponse
    {
        $this->persister->createScheduleLog($commandSchedule);

        $this->addFlash('success', 'Command scheduled for execution.');

        return $this->redirectToRoute('command_scheduler_logs_list');
    }
}
