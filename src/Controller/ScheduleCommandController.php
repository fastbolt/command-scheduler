<?php

namespace Fastbolt\CommandScheduler\Controller;

use Fastbolt\CommandScheduler\Entity\CommandSchedule;
use Fastbolt\CommandScheduler\Persistence\CommandLogPersister;
use Fastbolt\CommandScheduler\Provider\CommandLogProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

#[Route('/command-scheduler/schedule/{id}', name: 'command_scheduler_schedule_command', methods: ['GET'])]
class ScheduleCommandController
{
    public function __construct(
        private readonly CommandLogPersister $persister,
        private readonly RouterInterface $router,
    ) {
    }

    public function __invoke(Request $request, CommandSchedule $commandSchedule): RedirectResponse
    {
        $this->persister->createScheduleLog($commandSchedule);

        $request->getSession()->getFlashBag()->add('success', 'Command scheduled for execution.');

        return new RedirectResponse(
            $this->router->generate('command_scheduler_logs_list')
        );
    }
}

