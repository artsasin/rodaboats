<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 */
class User implements UserInterface, \Serializable
{
	
	const TYPE_ADMIN = 1;
	const TYPE_SALES = 2;
	const TYPE_MAINTENANCE = 3;
	const TYPE_BOOKKEEPING = 4;
	const TYPE_REPRESENTATIVE = 5;
	
	/**
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @ORM\Column(type="string", length=25, unique=true)
	 */
	private $username;

	/**
	 * @ORM\Column(type="string", length=64)
	 */
	private $password;

	/**
	 * @ORM\Column(type="string", length=60, unique=true)
	 */
	private $email;

	/**
	 * @ORM\Column(name="is_active", type="boolean")
	 */
	private $isActive;
	
	/**
	 * @ORM\Column(name="type", type="integer")
	 */
	private $type;
	
	/**
	 * Used during registration procedure only.
	 * @Assert\NotBlank(groups={"registration"})
	 * @Assert\Length(max = 4096, groups={"registration"})
	 */
	private $plainPassword;
	
	/**
	 * @ORM\ManyToOne(targetEntity="Location", inversedBy="boats")
	 * @ORM\JoinColumn(name="location_id", referencedColumnName="id")
	 */
	protected $location;
	
	/**
	 * @ORM\Column(type="string", length=64, unique=true)
	 */
	private $apiKey;

	public function __construct()
	{
		$this->isActive = true;
		$this->type = User::TYPE_SALES;
	}

	public function getUsername()
	{
		return $this->username;
	}

	public function getSalt()
	{
		// you *may* need a real salt depending on your encoder
		// see section on salt below
		return null;
	}

	public function getPassword()
	{
		return $this->password;
	}
	
	public function getPlainPassword()
	{
		return $this->plainPassword;
	}

	public function getRoles()
	{
		
		$roles = array('ROLE_USER');
		
		switch($this->type)
		{
			case User::TYPE_ADMIN:
				$roles[] = "ROLE_ADMIN";
				$roles[] = "ROLE_SALES";
				break;
			case User::TYPE_SALES:
				$roles[] = "ROLE_SALES";
				break;
			case User::TYPE_BOOKKEEPING:
				$roles[] = "ROLE_BOOKKEEPING";
				break;
			case User::TYPE_REPRESENTATIVE:
				$roles[] = "ROLE_REPRESENTATIVE";
				break;
			case User::TYPE_MAINTENANCE:
				$roles[] = "ROLE_MAINTENANCE";
				break;
		}
		
		return $roles;
	}
	
	/**
	 * Returns a human readable name for the type.
	 */
	public function getTypeName()
	{
		switch($this->type)
		{
			case User::TYPE_ADMIN:
				return "Admin";
			case User::TYPE_BOOKKEEPING:
				return "Bookkeeping";
			case User::TYPE_MAINTENANCE:
				return "Maintenance";
			case User::TYPE_REPRESENTATIVE:
				return "Representative";
			case User::TYPE_SALES:
				return "Sales";
			default:
				return "UNKNOWN";
		}
	}

	public function eraseCredentials()
	{
	}

	/** @see \Serializable::serialize() */
	public function serialize()
	{
		return serialize(array(
				$this->id,
				$this->username,
				$this->password,
		));
	}

	/** @see \Serializable::unserialize() */
	public function unserialize($serialized)
	{
		list (
				$this->id,
				$this->username,
				$this->password,
		) = unserialize($serialized);
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
     * Set username
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }
    
    public function setPlainPassword($password)
    {
    	$this->plainPassword = $password;
    
    	return $this;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return User
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set type
     *
     * @param integer $type
     *
     * @return User
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set location
     *
     * @param \AppBundle\Entity\Location $location
     *
     * @return User
     */
    public function setLocation(\AppBundle\Entity\Location $location = null)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return \AppBundle\Entity\Location
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set apiKey
     *
     * @param string $apiKey
     *
     * @return User
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    /**
     * Get apiKey
     *
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }
}
