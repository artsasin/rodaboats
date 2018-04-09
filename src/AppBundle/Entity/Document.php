<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Cocur\Slugify\Slugify;

/**
 * @ORM\Table(name="document")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DocumentRepository")
 */
class Document
{
	
	/**
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;
	
	/**
	 * The original file name.
	 * @ORM\Column(type="string", length=255)
	 */
	private $name;
	
	/**
	 * The file name as stored.
	 * @ORM\Column(type="string", length=255)
	 */
	private $path;
	
	/**
	 * @ORM\Column(type="date")
	 * @Assert\NotBlank()
	 */
	private $date;
	
	/**
	 * @ORM\ManyToOne(targetEntity="Boat", inversedBy="documents")
	 * @ORM\JoinColumn(name="boat_id", referencedColumnName="id")
	 */
	protected $boat;
	
	/**
	 * @Assert\File(maxSize="32000000")
	 */
	private $file;
	

	public function __construct()
	{
		
		$this->date = new \DateTime();
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
     * @return Document
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
     * @return Document
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
     * Set boat
     *
     * @param \AppBundle\Entity\Boat $boat
     *
     * @return Document
     */
    public function setBoat(\AppBundle\Entity\Boat $boat = null)
    {
        $this->boat = $boat;

        return $this;
    }

    /**
     * Get boat
     *
     * @return \AppBundle\Entity\Boat
     */
    public function getBoat()
    {
        return $this->boat;
    }
    
    public function getAbsolutePath()
    {
    	if($this->path === null)
    		return null;
    	return $this->getUploadRootDir() . '/' . $this->path;
    }
    
    public function getWebPath()
    {
    	if($this->path === null)
    		return null;
    	return $this->getUploadDir() . '/' . $this->path;
    }
    
    protected function getUploadRootDir()
    {
    	// the absolute directory path where uploaded
    	// documents should be saved
    	return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }
    
    protected function getUploadDir()
    {
    	// get rid of the __DIR__ so it doesn't screw up
    	// when displaying uploaded doc/image in the view.
    	return 'uploads/documents';
    }
    
    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
    	$this->file = $file;
    }
    
    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
    	return $this->file;
    }
    
    public function upload()
    {
    	
    	// The file property can be empty if the field is not required
    	if (null === $this->getFile())
    		return;
    
    	// use the original file name here but you should
    	// sanitize it at least to avoid any security issues
    	$slugify = new Slugify();
    	$slug = $this->getFile()->getClientOriginalName();
    	$slug = $slugify->slugify($slug, '_');
    	$this->path = $slug;
    
    	// move takes the target directory and then the target filename to move to.
    	$this->getFile()->move(
    			$this->getUploadRootDir(),
    			$this->path);
    
    	// Clean up the file property as you won't need it anymore
    	$this->setName($this->getFile()->getClientOriginalName());
    	$this->file = null;
    }
    
    public function removeUpload()
    {
    	$file = $this->getAbsolutePath();
    	if ($file) {
    		unlink($file);
    	}
    }

    /**
     * Set path
     *
     * @param string $path
     *
     * @return Document
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }
}
