<?php

namespace Topo\AdminBundle\Controller;

use FOS\UserBundle\Controller\SecurityController as BaseSecurityController;
use Symfony\Component\HttpFoundation\Request;
use Topo\AdminBundle\Form\Type\LoginType;

class SecurityController extends BaseSecurityController
{
    /**
     * Renders the login form.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function loginAction(Request $request)
    {
        /** @var \Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface */
        $authorizationChecker = $this->get('security.authorization_checker');

        // check if the user is already authenticated and has the
        // admin role, if has redirect to the admin dashboard
        if ($authorizationChecker->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('sonata_admin_dashboard');
        }

        return parent::loginAction($request);
    }

    /**
     * {@inheritdoc}
     */
    protected function renderLogin(array $data)
    {
        $form = $this->createForm(LoginType::class);

        $data['base_template'] = $this->get('sonata.admin.pool')->getTemplate('layout');
        $data['form'] = $form->createView();

        return $this->render('@TopoAdmin/Security/login.html.twig', $data);
    }
}
