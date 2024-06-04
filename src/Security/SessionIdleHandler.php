<?php
namespace App\Security;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
// use Symfony\Component\HttpFoundation\Request;

class SessionIdleHandler
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.request' => 'onKernelRequest',
        ];
    }

    public function onKernelRequest(RequestEvent $event)
    {
        // Получаем текущего пользователя (если он есть)
        $user = $event->getRequest()->getUser();
        
        // Если пользователь авторизован, обновляем время его сеанса
        if ($user) {
            // Обновляем время сеанса
            $this->session->start();
        }
    }
}
    // private $session;
    // private $router;
    // private $logger;
    // private $timeout;

    // public function __construct(SessionInterface $session, UrlGeneratorInterface $router, LoggerInterface $logger, int $timeout = 30)
    // {
    //     $this->session = $session;
    //     $this->router = $router;
    //     $this->logger = $logger;
    //     $this->timeout = $timeout;
    // }

    // public function onKernelRequest(RequestEvent $event)
    // {
    //     if (!$event->isMainRequest()) {
    //         return;
    //     }

    //     $this->session->start();
    //     $lastActivity = $this->session->get('last_activity');
    //     dd($lastActivity);
    //     $this->logger->info('Last activity time: ' . $lastActivity);

    //     if ($lastActivity !== null) {
    //         $timeSinceLastActivity = time() - $lastActivity;
    //         $this->logger->info('Time since last activity: ' . $timeSinceLastActivity);

    //         if ($timeSinceLastActivity > $this->timeout) {
    //             $this->logger->info('Session timeout, invalidating session.');
    //             $this->session->invalidate();
    //             $response = new RedirectResponse($this->router->generate('login_route'));
    //             $event->setResponse($response);
    //             return;
    //         }
    //     }

    //     $this->session->set('last_activity', time());
    // }
// }