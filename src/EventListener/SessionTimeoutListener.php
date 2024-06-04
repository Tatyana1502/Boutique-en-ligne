<?php
namespace App\EventListener;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SessionTimeoutListener
{
    public function onKernelRequest(RequestEvent $event, Request $request, int $maxIdleTime=30)
    {
        $session->start();
        if (time() - $session->getMetadataBag()->getLastUsed() > $maxIdleTime)
        {
            dd(time());
            $session->invalidate();
            throw new SessionExpired(); // redirect to expired session page
        }

    }
}