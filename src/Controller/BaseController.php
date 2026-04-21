<?php

namespace Fastbolt\CommandScheduler\Controller;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Twig\Environment;

abstract class BaseController
{
    /**
     * @param RequestStack $requestStack
     * @param Environment  $environment
     * @param Router       $router
     */
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly Environment $environment,
        private readonly Router $router,
    ) {
    }

    /**
     * @param string $route
     *
     * @return RedirectResponse
     */
    protected function redirectToRoute(string $route): RedirectResponse
    {
        return new RedirectResponse($this->router->generate($route));
    }

    /**
     * @param string $type
     * @param mixed  $message
     *
     * @return void
     */
    protected function addFlash(string $type, mixed $message): void
    {
        $this->getFlashBag()->add($type, $message);
    }

    /**
     * @param string $template
     * @param array  $parameters
     *
     * @return Response
     */
    protected function renderView(string $template, array $parameters = []): Response
    {
        return new Response(
            $this->environment->render(
                $template,
                $parameters
            )
        );
    }

    /**
     * @return Session
     */
    private function getSession(): Session
    {
        /** @var Session $session */
        $session = $this->requestStack->getSession();

        return $session;
    }

    /**
     * @return FlashBagInterface
     */
    private function getFlashBag(): FlashBagInterface
    {
        return $this->getSession()->getFlashBag();
    }
}
