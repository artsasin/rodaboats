<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Criteria;

/**
 */
class Report
{
	
	public $start;
	
	public $end;
	
	public $location;
	
	public $boat;
	
	public $output;
	
	public $status;
	
	public $cancellationReason;
}
