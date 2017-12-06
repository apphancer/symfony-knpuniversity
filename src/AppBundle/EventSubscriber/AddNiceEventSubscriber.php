<?php


namespace AppBundle\EventSubscriber;


use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class AddNiceEventSubscriber implements EventSubscriberInterface
{

    private $logger;
    private $messageManager;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->messageManager = false;
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        $this->logger->info('Adding a nice header!');

        $event->getResponse()
            ->headers->set('X-NICE-MESSAGE', 'That was a great request!');
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::RESPONSE => 'onKernelResponse',
        ];
    }

}