<?php

namespace Topo\AdminBundle\Mailer;

use FOS\UserBundle\Mailer\TwigSwiftMailer as BaseTwigSwiftMailer;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AdminUserMailer extends BaseTwigSwiftMailer
{
    /**
     * {@inheritdoc}
     */
    public function sendResettingEmailMessage(UserInterface $user)
    {
        $url = $this->router->generate('topo_admin_resetting_reset', ['token' => $user->getConfirmationToken()], UrlGeneratorInterface::ABSOLUTE_URL);

        $context = [
            'user' => $user,
            'confirmationUrl' => $url,
        ];

        $this->sendMessage($this->getResettingTemplate(), $context, $this->parameters['from_email']['resetting'], $user->getEmail());
    }

    /**
     * Gets the resetting email template.
     *
     * @return string
     */
    private function getResettingTemplate()
    {
        return '@TopoAdmin/Resetting/email.html.twig';
    }
}
