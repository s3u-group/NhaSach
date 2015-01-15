<?php

	namespace Barcode\Entity;
	
	use Doctrine\ORM\Mapping as ORM;
	use ZfcUser\Entity\UserInterface;
	use BjyAuthorize\Provider\Role\ProviderInterface;
	use Doctrine\Common\Collections\ArrayCollection;


	/**
	* @ORM\Entity
	* @ORM\Table(name="barcode")
	*/
	class Barcode {

		/**
		* @ORM\Column(name="id_barcode",type="integer")
		* @ORM\Id
		* @ORM\GeneratedValue
		*/
		private $idBarcode;

		/**
		* @ORM\Column(name="ten_barcode",type="text", length=255)
		*/
		private $tenBarcode;

		/**
		* @ORM\Column(name="length",type="integer")
		*/
		private $length;

		/**
		* @ORM\Column(name="state",type="integer")
		*/
		private $state;

		public function setIdBarcode($idBarcode)
		{
			$this->idBarcode=$idBarcode;
		}
		public function getIdBarcode()
		{
			return $this->idBarcode;
		}

		public function setTenBarcode($tenBarcode)
		{
			$this->tenBarcode=$tenBarcode;
		}
		public function getTenBarcode()
		{
			return $this->tenBarcode;
		}

		public function setLength($length)
		{
			$this->length=$length;
		}
		public function getLength()
		{
			return $this->length;
		}

		public function setState($state)
		{
			$this->state=$state;
		}
		public function getState()
		{
			return $this->state;
		}
	}
?>