<?php

namespace Topo\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Topo\AdminBundle\Form\Type\RequestType;
use Topo\AdminBundle\Form\Type\ResettingType;

class ResettingController extends Controller
{
    /**
     * Renders the request form.
     *
     * @return Response
     */
    public function requestAction()
    {
        $form = $this->createForm(RequestType::class);

        return $this->render('@TopoAdmin/Resetting/request.html.twig', [
            'base_template' => $this->get('sonata.admin.pool')->getTemplate('layout'),
            'form' => $form->createView(),
        ]);
    }

    /**
     * Sends the password reset email after the form submit.
     *
     * @param Request $request
     *
     * @return RedirectResponse If the user exists or renders the request otherwise
     */
    public function sendEmailAction(Request $request)
    {
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('topo_admin.admin_user.manager');

        $username = $request->request->get('username');

        /** @var $user \FOS\UserBundle\Model\UserInterface */
        $user = $userManager->findUserByUsernameOrEmail($username);
        if (null === $user) {
            $form = $this->createForm(RequestType::class);

            return $this->render('@TopoAdmin/Resetting/request.html.twig', [
                'base_template' => $this->get('sonata.admin.pool')->getTemplate('layout'),
                'invalid_username' => $username,
                'form' => $form->createView(),
            ]);
        }

        if (null === $user->getConfirmationToken()) {
            /** @var $tokenGenerator \FOS\UserBundle\Util\TokenGeneratorInterface */
            $tokenGenerator = $this->get('fos_user.util.token_generator');
            $user->setConfirmationToken($tokenGenerator->generateToken());
        }

        $this->get('topo_admin.admin_user.mailer')->sendResettingEmailMessage($user);
        $user->setPasswordRequestedAt(new \DateTime());
        $userManager->updateUser($user);

        $this->addFlash('sonata_flash_info', $this->get('translator')->trans('resetting.check_email', [
            '%email%' => $user->getEmail(),
        ], 'FOSUserBundle'));

        return $this->redirectToRoute('topo_admin_security_login');
    }

    /**
     * Resets the password.
     *
     * @param string  $token   The confirmation token
     * @param Request $request
     *
     * @return RedirectResponse
     *
     * @throws NotFoundHttpException If the user not exists with the confirmation token
     */
    public function resetAction($token, Request $request)
    {
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('topo_admin.admin_user.manager');

        $user = $userManager->findUserByConfirmationToken($token);
        if (null === $user) {
            throw $this->createNotFoundException(sprintf('The user with "confirmation token" does not exist for value "%s"', $token));
        }

        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $form = $this->createForm(ResettingType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // FOSUser event
            $user->setConfirmationToken(null);
            $user->setPasswordRequestedAt(null);
            $user->setEnabled(true);

            $userManager->updateUser($user);

            // user authentication
            $firewallName = $this->container->getParameter('topo_admin.admin_user.firewall_name');
            /** \FOS\UserBundle\Security\LoginManagerInterface */
            $this->get('fos_user.security.login_manager')->logInUser($firewallName, $user);

            return $this->redirectToRoute('sonata_admin_dashboard');
        }

        return $this->render('@TopoAdmin/Resetting/reset.html.twig', [
            'base_template' => $this->get('sonata.admin.pool')->getTemplate('layout'),
            'token' => $token,
            'form' => $form->createView(),
        ]);
    }
}
