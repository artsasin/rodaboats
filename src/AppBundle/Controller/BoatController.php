<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use AppBundle\Entity\Boat;
use AppBundle\Entity\User;
use AppBundle\Entity\Document;

class BoatController extends Controller
{
	
	/**
	 * @Route("/boat", name="boat")
	 */
	public function indexAction()
	{
	
		$em = $this->getDoctrine()->getManager();
	
		$boats = $this->getDoctrine()->getRepository('AppBundle:Boat')->findAll();
		
		return $this->render('boat/index.html.twig', array(
				'boats' => $boats,
		));
	}
	
	protected function buildForm(Boat $boat)
	{
		

		$form = $this->createFormBuilder($boat)
		->add('name', 'text')
		->add('hin', 'text')
		->add('status', 'choice', array(
				'choices' => array(
						'Active' => Boat::STATUS_ACTIVE,
						'Inactive' => Boat::STATUS_INACTIVE,
						'Deleted' => Boat::STATUS_DELETED,
				),
				'choices_as_values' => true
		))
		->add('location', 'entity', array(
				'class' => 'AppBundle:Location',
				'choice_label' => 'name',
				'required' => false,
		))
		->add('registration', 'text', array('required' => false))
		->add('trackerImei', 'text', array('required' => false))
		->add('trackerName', 'text', array('required' => false))
		->add('insurancePolicy', 'text', array('required' => false))
		->add('harbor', 'text')
		->add('shaft', 'text', array('required' => false))
		->add('steeringCable', 'text', array('required' => false))
		->add('steeringCableDate', 'date', array('required' => false))
		->add('batteryType', 'text', array('required' => false))
		->add('batteryDate', 'date', array('required' => false))
		->add('engineSerial', 'text', array('required' => false))
		->add('propeller', 'text', array('required' => false))
		->add('propellerDate', 'date', array('required' => false))
		->add('anchor', 'text', array('required' => false))
		->add('tank1', 'text', array('required' => false))
		->add('tank2', 'text', array('required' => false))
		->add('save', 'submit', array('label' => 'Save'))
		->getForm();
		
		return $form;
	}
	
	protected function buildUploadForm(Document $document)
	{
		
		$form = $this->createFormBuilder($document)
		->setAction($this->generateUrl('addboatdocument', array('id' => $document->getBoat()->getId())))
		->add('file')
		->add('save', 'submit', array('label' => 'Upload'))
		->getForm();
		
		return $form;
	}

	
	/**
	 * @Route("/admin/boat/add", name="addboat")
	 */
	public function addBoat(Request $request)
	{
		 
		// Build the form around a fresh boat object.
		$boat = new Boat();
		$form = $this->buildForm($boat);
		$form->handleRequest($request);
		 
		if ($form->isValid()) {
	
			// Save the new boat.
			$em = $this->getDoctrine()->getManager();
			$em->persist($boat);
			$em->flush();
	
			// Notify and redirect.
			$this->addFlash('notice', 'The boat has been added.');
			return $this->redirectToRoute('admin');
		}
		 
		return $this->render('boat/add.html.twig', array(
				'form' => $form->createView(),
		));
	}
	
	/**
	 * @Route("/admin/boat/{id}/edit", name="editboat")
	 */
	public function editBoat(Request $request, $id)
	{
		
		$boat = $this->getDoctrine()->getRepository('AppBundle:Boat')->find($id);
		$form = $this->buildForm($boat);
		$form->handleRequest($request);
		
		if($form->isValid())
		{
			
			$em = $this->getDoctrine()->getManager();
			$em->flush();
			
			// Notify and redirect.
			$this->addFlash('notice', 'The changs to the boat have been saved.');
			return $this->redirectToRoute('admin');
		}
		
		return $this->render('boat/edit.html.twig', array(
    		'form' => $form->createView(),
    	));
	}

	/**
	 * @Route("/boat/{id}", name="viewboat")
	 */
	public function viewBoat(Request $request, $id)
	{
	
		$boat = $this->getDoctrine()->getRepository('AppBundle:Boat')->find($id);
		$document = new Document();
		$document->setBoat($boat);
		$form = $this->buildUploadForm($document);
	
		return $this->render('boat/view.html.twig', array(
				'upload' => $form->createView(),
				'boat' => $boat,
		));
	}
	
	/**
	 * @Route("/boat/{id}/document/add", name="addboatdocument")
	 */
	public function addDocument(Request $request, $id)
	{
	
		$boat = $this->getDoctrine()->getRepository('AppBundle:Boat')->find($id);
		if($boat == null)
			throw new Exception("Unknown boat");
	
		$document = new Document();
		$document->setBoat($boat);
	
		// Create the upload form.
		$form = $this->buildUploadForm($document);
		$form->handleRequest($request);
	
		if (!$form->isValid())
		{
			$this->addFlash('danger', 'An error ocurred while uploading the document.');
			return $this->redirectToRoute('viewboat', array('id' => $boat->getId()));
		}
		
		// Store the uploaded document. 
		$document->upload();
		
		$em = $this->getDoctrine()->getManager();
		$em->persist($document);
		$em->flush();
		
	
		$this->addFlash('notice', 'The document has been saved.');
		return $this->redirectToRoute('viewboat', array('id' => $boat->getId()));
	}
	
	/**
	 * @Route("/boat/{boat}/document/{id}", name="viewboatdocument")
	 */
	public function viewDocument(Request $request, $id, $boat)
	{
	
		$boat = $this->getDoctrine()->getRepository('AppBundle:Boat')->find($boat);
		$document = $this->getDoctrine()->getRepository('AppBundle:Document')->find($id);
	
		$file = $document->getAbsolutePath();
		$response = new BinaryFileResponse($file);
		$response->setContentDisposition(ResponseHeaderBag::DISPOSITION_INLINE, $document->getName());
		return $response;
	}
	
	/**
	 * @Route("/boat/{boat}/document/{id}/delete", name="deleteboatdocument")
	 * @Method({"POST"})
	 */
	public function deleteDocument(Request $request, $id, $boat)
	{
	
		$boat = $this->getDoctrine()->getRepository('AppBundle:Boat')->find($boat);
		$document = $this->getDoctrine()->getRepository('AppBundle:Document')->find($id);
		
		// Delete the file.
		$document->removeUpload();
		
		$em = $this->getDoctrine()->getManager();
		$em->remove($document);
		$em->flush();
	
		$this->addFlash('notice', 'The document has been deleted.');
		return $this->redirectToRoute('viewboat', array('id' => $boat->getId()));
	}
	
	/**
	 * @Route("/boat/{id}/maintenance", name="viewboatmaintenance")
	 */
	public function editMaintenance(Request $request, $id)
	{
	
		$boat = $this->getDoctrine()->getRepository('AppBundle:Boat')->find($id);
		
		$form = $this->createFormBuilder($boat)
		->add('engineOilCheck', 'date', array('required' => false))
		->add('shaftOilCheck', 'date', array('required' => false))
		->add('petrolFilterChange', 'date', array('required' => false))
		->add('batteryCheck', 'date', array('required' => false))
		->add('sparkChange', 'date', array('required' => false))
		->add('impellorChange', 'date', array('required' => false))
		->add('oilFilterChange', 'date', array('required' => false))
		->add('steeringWheelGrease', 'date', array('required' => false))
		->add('throttleShiftingGrease', 'date', array('required' => false))
		->add('engineCleaning', 'date', array('required' => false))
		->add('propellerChange', 'date', array('required' => false))
		->add('engineHours', 'integer', array('required' => false))
		->add('engineHoursCheck', 'date', array('required' => false))
		->add('engineHoursCheck', 'date', array('required' => false))
		->add('comments', 'textarea', array('required' => false))
		->add('save', 'submit', array('label' => 'Save'))
		->getForm();
		
		$form->handleRequest($request);
		if($form->isValid())
		{
				
			$em = $this->getDoctrine()->getManager();
			$em->flush();
				
			// Notify and redirect.
			$this->addFlash('notice', 'The changs to the boat maintenance have been saved.');
			return $this->redirectToRoute('viewboat', array('id' => $boat->getId()));
		}
	
		return $this->render('boat/maintenance.html.twig', array(
				'form' => $form->createView(),
				'boat' => $boat,
		));
	}
}
