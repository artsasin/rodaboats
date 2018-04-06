<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Cocur\Slugify\Slugify;

/**
 * @ORM\Table(name="emailTemplate")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\EmailTemplateRepository")
 */
class EmailTemplate
{
	
	/**
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;
	
	/**
	 * Template name
	 * @ORM\Column(type="string", length=255)
	 */
	private $name;
	
	/**
	 * Template language
	 * @ORM\Column(type="string", length=255)
	 */
	private $language;
	
	/**
	 * @ORM\Column(type="date")
	 * @Assert\NotBlank()
	 */
	private $date;
	
	/**
	 * @ORM\Column(type="text", nullable=false)
	 * @var unknown
	 */
	private $content;
	

	public function __construct()
	{
		
		$this->date = new \DateTime();
		$this->language = "EN";
	}
	

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return EmailTemplate
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return EmailTemplate
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return EmailTemplate
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set language
     *
     * @param string $language
     *
     * @return EmailTemplate
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Get language
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }
}
