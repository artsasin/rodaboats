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

class TemplateController extends Controller
{
    
    
    /**
     * @Route("/admin/template/{id}/edit", name="edittemplate")
     * @param Request $request
     */
    public function editConfirmation(Request $request, $id)
    {

    	// Lookup template, create it if it does not exist.
    	$template = $this->getDoctrine()->getRepository('AppBundle:EmailTemplate')->find($id);
    	$em = $this->getDoctrine()->getManager();
    	
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
    		$this->addFlash('notice', 'The changes to the template have been stored.');
    		return $this->redirectToRoute('admin');
    	}
    	
    	
    	return $this->render('template/edit.html.twig', array(
    			'form' => $form->createView(),
    			'template' => $template
    	));
    }
}
