<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="location")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\LocationRepository")
 */
class Location
{
	
	const STATUS_ACTIVE = 1;
	const STATUS_DELETED = 2;
	
	public function __construct()
	{
		
		$this->status = self::STATUS_ACTIVE;
	}
	
	/**
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;
	
	/**
	 * @ORM\Column(type="integer")
	 * @Assert\NotBlank()
	 */
	private $status;
	
	/**
	 * @ORM\Column(type="string", length=50)
	 * @Assert\NotBlank()
	 */
	private $name;
	
	/**
	 * @ORM\OneToMany(targetEntity="Boat", mappedBy="location")
	 */
	protected $boats;
	
	/**
	 * @ORM\OneToMany(targetEntity="User", mappedBy="location")
	 */
	protected $users;
	
	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $notificationEmail;

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
     * Set status
     *
     * @param integer $status
     *
     * @return Location
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Location
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
     * Add boat
     *
     * @param \AppBundle\Entity\Boat $boat
     *
     * @return Location
     */
    public function addBoat(\AppBundle\Entity\Boat $boat)
    {
        $this->boats[] = $boat;

        return $this;
    }

    /**
     * Remove boat
     *
     * @param \AppBundle\Entity\Boat $boat
     */
    public function removeBoat(\AppBundle\Entity\Boat $boat)
    {
        $this->boats->removeElement($boat);
    }

    /**
     * Get boats
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBoats()
    {
        return $this->boats;
    }

    /**
     * Add user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Location
     */
    public function addUser(\AppBundle\Entity\User $user)
    {
        $this->users[] = $user;

        return $this;
    }

    /**
     * Remove user
     *
     * @param \AppBundle\Entity\User $user
     */
    public function removeUser(\AppBundle\Entity\User $user)
    {
        $this->users->removeElement($user);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Set notificationEmail
     *
     * @param string $notificationEmail
     *
     * @return Location
     */
    public function setNotificationEmail($notificationEmail)
    {
        $this->notificationEmail = $notificationEmail;

        return $this;
    }

    /**
     * Get notificationEmail
     *
     * @return string
     */
    public function getNotificationEmail()
    {
        return $this->notificationEmail;
    }
}
