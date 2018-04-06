<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Boat;
use AppBundle\Entity\User;
use AppBundle\Entity\Location;
use AppBundle\Entity\EmailTemplate;

class AdminController extends Controller
{
    /**
     * @Route("/admin", name="admin")
     */
    public function indexAction(Request $request)
    {
    	
    	$em = $this->getDoctrine()->getManager();
    	$users = $this->getDoctrine()->getRepository('AppBundle:User')->findAll();
    	
    	$boats = $this->getDoctrine()->getRepository('AppBundle:Boat')->findAll();
    	$locations = $this->getDoctrine()->getRepository('AppBundle:Location')->findBy(array(
    			'status' => Location::STATUS_ACTIVE
    	));
    	$templates = $this->getDoctrine()->getRepository('AppBundle:EmailTemplate')->findAll();
    	
    	$adminEmail = $this->container->getParameter("admin_email");
    	
        return $this->render('admin/index.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..'),
        		'users' => $users,
        		'boats' => $boats,
        		'locations' => $locations,
        		'templates' => $templates,
        		'adminEmail' => $adminEmail
        ));
    }
    
    /**
     * @Route("/admin/users/add", name="adduser")
     */
    public function addUser(Request $request)
    {
    	 
    	// just setup a fresh $task object (remove the dummy data)
    	$user = new User();
    	 
    	$form = $this->buildCreateForm($user);
    	$form->handleRequest($request);
    	 
    	if ($form->isValid()) {
    
    		$user->setEmail($user->getUsername());
    		$user->setApiKey($this->generateRandomString(32));
    		
    		// Encode the password
			$plainPassword = $user->getPlainPassword();
			$encoder = $this->container->get('security.password_encoder');
			$encoded = $encoder->encodePassword($user, $plainPassword);
			$user->setPassword($encoded);
			
			// Save the user
    		$em = $this->getDoctrine()->getManager();
    		$em->persist($user);
    		$em->flush();
    
    		$this->addFlash('notice', 'The user has been created.');
    		return $this->redirectToRoute('admin');
    	}
    	 
    	return $this->render('admin/adduser.html.twig', array(
    			'form' => $form->createView(),
    	));
    }
    
    /**
     * Not cryptographically secure
     * @param number $length
     */
    protected function generateRandomString($length = 10) {
    	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    	$charactersLength = strlen($characters);
    	$randomString = '';
    	for ($i = 0; $i < $length; $i++) {
    		$randomString .= $characters[rand(0, $charactersLength - 1)];
    	}
    	return $randomString;
    }
    
    protected function buildCreateForm($user)
    {
    	
    	$form = $this->createFormBuilder($user)
    	->add('username', 'text')
    	->add('plainPassword', 'repeated', array(
    			'type' => 'password',
    			'first_options' => array('label' => 'Password'),
    			'second_options' => array('label' => 'Repeat password')
    	))
    	->add('type', 'choice', array(
    			'choices' => array(
    					'Admin' => User::TYPE_ADMIN,
    					'Sales' => User::TYPE_SALES,
    					'Maintenance' => User::TYPE_MAINTENANCE,
    					'Bookkeeping' => User::TYPE_BOOKKEEPING,
    					'Representative' => User::TYPE_REPRESENTATIVE
    			),
    			'choices_as_values' => true
    	))
    	->add('location', 'entity', array(
    			'class' => 'AppBundle:Location',
    			'choice_label' => 'name',
    			'required' => false,
    	))
    	->add('isActive', 'checkbox')
    	->add('save', 'submit', array('label' => 'Create user'))
    	->getForm();
    	
    	return $form;
    	
    }
    
    protected function buildEditForm($user)
    {
    	
    	$form = $this->createFormBuilder($user)
    	->add('type', 'choice', array(
    			'choices' => array(
    					'Admin' => User::TYPE_ADMIN,
    					'Sales' => User::TYPE_SALES,
    					'Maintenance' => User::TYPE_MAINTENANCE,
    					'Bookkeeping' => User::TYPE_BOOKKEEPING,
    					'Representative' => User::TYPE_REPRESENTATIVE
    			),
    			'choices_as_values' => true
    	))
    	->add('location', 'entity', array(
    			'class' => 'AppBundle:Location',
    			'choice_label' => 'name',
    			'required' => false,
    	))
    	->add('isActive', 'checkbox', array('required' => false))
    	->add('save', 'submit', array('label' => 'Save changes'))
    	->getForm();
    	
    	return $form;
    }
    
    protected function buildPasswordForm($user)
    {
    	
    	// Create the change password form
    	$form = $this->createFormBuilder($user)
    	->setAction($this->generateUrl('changeuserpassword', array('id' => $user->getId())))
    	->add('plainPassword', 'repeated', array(
    			'type' => 'password',
    			'first_options' => array('label' => 'Password'),
    			'second_options' => array('label' => 'Repeat password')
    	))
    	->add('save', 'submit', array('label' => 'Change password'))
    	->getForm();
    	
    	return $form;
    }
    
    /**
     * @Route("/admin/users/{id}", name="edituser")
     */
    public function editUser(Request $request, $id)
    {
    	
    	$user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
    	
    	$editForm = $this->buildEditForm($user);
    	$passwordForm = $this->buildPasswordForm($user);
    	
    	// Accept edit requests to the user.
    	$editForm->handleRequest($request);
    	if($editForm->isValid())
    	{
    		
    		$em = $this->getDoctrine()->getManager();
    		$em->flush();
    			
    		// Notify and redirect.
    		$this->addFlash('notice', 'The changs to the user have been saved.');
    		return $this->redirectToRoute('admin');
    	}
    	
    	return $this->render('admin/edituser.html.twig', array(
    			'editForm' => $editForm->createView(),
    			'passwordForm' => $passwordForm->createView(),
    			'user' => $user,
    	));
    }
    
    /**
     * @Route("/admin/users/{id}/changePassword", name="changeuserpassword")
     */
    public function changeUserPassword(Request $request, $id)
    {
    	
    	$user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
    	
    	$form = $this->buildPasswordForm($user);
    	$form->handleRequest($request);
    	
    	// Handle invalid forms.
    	if(!$form->isValid())
    	{
    		$this->addFlash('error', 'Unable to change the user password.');
    		return $this->redirectToRoute('edituser', array('id' => $id));
    	}
    	
    	// Encode the password
    	$plainPassword = $user->getPlainPassword();
    	$encoder = $this->container->get('security.password_encoder');
    	$encoded = $encoder->encodePassword($user, $plainPassword);
    	$user->setPassword($encoded);
    	
    	// Persist changes and redirect the user.
    	$em = $this->getDoctrine()->getManager();
    	$em->flush();
    	
    	$this->addFlash('notice', 'The user password has been changed.');
    	return $this->redirectToRoute('admin');
    }
    
    protected function buildLocationForm($location)
    {
    	
    	$form = $this->createFormBuilder($location)
    	->add('name', 'text')
    	->add('status', 'choice', array(
    			'choices' => array(
    					'Active' => Location::STATUS_ACTIVE,
    					'Deleted' => Location::STATUS_DELETED,
    			),
    			'choices_as_values' => true
    	))
    	->add('notificationEmail', 'email', array('required' => false))
    	->add('save', 'submit', array('label' => 'Save'))
    	->getForm();
    	 
    	return $form;
    }
    
    /**
     * @Route("/admin/location/add", name="addlocation")
     */
    public function addLocation(Request $request)
    {
    
    	$location = new Location();
    	$form = $this->buildLocationForm($location);
    	$form->handleRequest($request);
    	 
    	if ($form->isValid()) {
    
			// Save the user
    		$em = $this->getDoctrine()->getManager();
    		$em->persist($location);
    		$em->flush();
    
    		$this->addFlash('notice', 'The location has been created.');
    		return $this->redirectToRoute('admin');
    	}
    	 
    	return $this->render('admin/addlocation.html.twig', array(
    			'form' => $form->createView(),
    	));
    }
    
    /**
     * @Route("/admin/location/{id}", name="editlocation")
     */
    public function editLocation(Request $request, $id)
    {
    	
    	$location = $this->getDoctrine()->getRepository('AppBundle:Location')->find($id);
    	$form = $this->buildLocationForm($location);
    	
    	$form->handleRequest($request);
    	if($form->isValid())
    	{
    	
    		$em = $this->getDoctrine()->getManager();
    		$em->flush();
    		 
    		// Notify and redirect.
    		$this->addFlash('notice', 'The changs to the location have been saved.');
    		return $this->redirectToRoute('admin');
    	}
    	 
    	return $this->render('admin/editlocation.html.twig', array(
    			'form' => $form->createView(),
    			'location' => $location,
    	));
    }

    /**
     * @Route("/admin/test/email", name="testemail")
     * @param Request $request
     */
    public function emailTest(Request $request)
    {
    	
    	$recipient = $request->query->get('recipient');
    	
    	// Render the message body.
    	$body = "<html><body>This is a test message and can be ignored.</body></html>";
    	
    	// Compose and send the message.
    	$message = \Swift_Message::newInstance()
    	->setSubject("RoDa boats booking applicatio test message")
    	->setFrom("info@rodaboats.com")
    	->setTo($recipient)
    	->setBody($body, 'text/html')
    	;
    	$this->get('mailer')->send($message);
    	
    	// Notify and redirect.
    	$this->addFlash('notice', 'Test message sent.');
    	return $this->redirectToRoute('admin');
    }
    
    /**
     * @Route("/admin/confirmation/edit", name="editconfirmation")
     * @param Request $request
     */
    public function editConfirmation(Request $request)
    {

    	$em = $this->getDoctrine()->getManager();
    	
    	// Lookup template, create it if it does not exist.
    	$template = $this->getDoctrine()->getRepository('AppBundle:EmailTemplate')->findOneBy(array('name' => 'TEMPLATE_CONFIRMATION'));
    	if($template == null)
    	{
    		$template = new EmailTemplate();
    		$template->setName('TEMPLATE_CONFIRMATION');
    		$template->setContent('');
    		$em->persist($template);
    	}
    	
    	$form = $this->createFormBuilder($template)
    	->add('content', 'hidden')
    	->add('save', 'submit', array('label' => 'Save changes'))
    	->getForm();
    	
    	$form->handleRequest($request);
    	if($form->isValid())
    	{
    		
    		$template->setDate(new \DateTime());
    		$em->flush();
    		 
    		// Notify and redirect.
    		$this->addFlash('notice', 'The changes to the booking confirmation template have been stored.');
    		return $this->redirectToRoute('admin');
    	}
    	$em = $this->getDoctrine()->getManager();
    	
    	dump($template);
    	return $this->render('admin/editconfirmation.html.twig', array(
    			'form' => $form->createView(),
    			'template' => $template
    	));
    }
}
