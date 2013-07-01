<?php
namespace Cerad\Bundle\AccountBundle\EventListener;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use FOS\UserBundle\FOSUserEvents;

class RegistrationListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array
        (
            FOSUserEvents::REGISTRATION_INITIALIZE => 'onRegistrationInitialize',
            FOSUserEvents::REGISTRATION_SUCCESS    => 'onRegistrationSuccess',
            FOSUserEvents::REGISTRATION_COMPLETED  => 'onRegistrationCompleted',
            FOSUserEvents::REGISTRATION_CONFIRM    => 'onRegistrationConfirm',
            FOSUserEvents::REGISTRATION_CONFIRMED  => 'onRegistrationConfirmed',
        );
    }
    public function onRegistrationInitialize(Event $event)
    {
        echo sprintf("### onRegistrationInitialize\n");
    }
}
?>
