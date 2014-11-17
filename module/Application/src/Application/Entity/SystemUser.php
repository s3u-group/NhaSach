<?php

	namespace Application\Entity;
	
	use Doctrine\ORM\Mapping as ORM;
	use ZfcUser\Entity\UserInterface;
	use BjyAuthorize\Provider\Role\ProviderInterface;
	use Doctrine\Common\Collections\ArrayCollection;


	/**
	* @ORM\Entity
	* @ORM\Table(name="user")
	*/
	class SystemUser implements UserInterface, ProviderInterface {
		

		/**
		* @ORM\Column(name="user_id",type="integer")
		* @ORM\Id
		* @ORM\GeneratedValue
		*/
		private $id;

		/**
		* @ORM\Column(name="display_name",length=50)
		*/
		private $displayName;

		/**
		* @ORM\Column
		*/
		private $city;


		/**
		* @ORM\Column(type="date")
		*/
		private $birthday;

		/**
		* @ORM\Column
		*/
		private $username;
		/**
		* @ORM\Column(name="password",length=128)
		*/
		private $password;

		/**
		* @ORM\Column
		*/
		private $email;


		/**
		* @ORM\Column(type="smallint",length=6)
		*/
		private $state;


	/**
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\ManyToMany(targetEntity="DanhMuc\Entity\Role")
     * @ORM\JoinTable(name="user_role_linker",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="user_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
     * )
     */
	private $roles;
		
		 public function __construct()
	    {
	        $this->roles = new ArrayCollection();
	    }
		
		public function setId($id)
		{
			$this->id=$id;

		}
		public function getId()
		{
			return $this->id;			
		}


		public function setDisplayName($displayName)
		{
			$this->displayName=$displayName;
		}

		public function getDisplayName()
		{
			return $this->displayName;
		}


		public function setUsername($username)
		{
			$this->username=$username;
		}

		public function getUsername()
		{
			return $this->username;
		}


		public function setPassword($password)
		{
			return $this->password=$password;
		}


		public function getPassword()
		{
			return $this->password;
		}

		public function setCity($city)
		{
			$this->city=$city;
		}

		public function getCity()
		{
			return $this->city;
		}


		public function setState($state)
		{
			$this->state=$state;
		}
		public function getState()
		{
			return $this->state;
		}


		public function setEmail($email)
		{
			$this->email=$email;
		}
		public function getEmail()
		{
			return $this->email;
		}


		public function getRoles()
	    {
	        return $this->roles->getValues();
	    }


		public function addRole($role)
	    {
	        $this->roles[] = $role;
	    }

		
	}
	

?>