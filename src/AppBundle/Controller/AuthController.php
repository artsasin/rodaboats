<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class AuthController extends Controller
{
	/**
	 * @Route("/auth/login", name="login_route")
	 */
	public function loginAction(Request $request)
	{
		
		$authenticationUtils = $this->get('security.authentication_utils');
		
		// Check for authentication errors.
		$error = $authenticationUtils->getLastAuthenticationError();
		
		// last username entered by the user
		$lastUsername = $authenticationUtils->getLastUsername();
		
		return $this->render(
				'auth/login.html.twig',
				array(
						// last username entered by the user
						'last_username' => $lastUsername,
						'error'         => $error,
				)
		);
	}

	/**
	 * @Route("/auth/login_check", name="login_check")
	 */
	public function loginCheckAction()
	{
		// this controller will not be executed,
		// as the route is handled by the Security system
	}
	
	/**
	 * @Route("/auth/logout", name="logout_route")
	 */
	public function logoutAction()
	{
		// this controller will not be executed,
		// as the route is handled by the Security system
	}
}