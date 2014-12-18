<?php

	namespace Kho\Entity;
	
	use Doctrine\ORM\Mapping as ORM;
	use ZfcUser\Entity\UserInterface;
	use BjyAuthorize\Provider\Role\ProviderInterface;
	use Doctrine\Common\Collections\ArrayCollection;


	/**
	* @ORM\Entity
	* @ORM\Table(name="chiet_khau")
	*/
	class ChietKhau {
		

		/**
		* @ORM\Column(name="id_chiet_khau",type="integer")
		* @ORM\Id
		* @ORM\GeneratedValue
		*/
		private $idChietKhau;

		/**
		* @ORM\ManyToOne(targetEntity="Kho\Entity\Kho", cascade={"persist"})
		* @ORM\JoinColumn(name="id_kho", referencedColumnName="id_kho")
		*/
		private $idKho;

		/**
		* @ORM\Column(name="chiet_khau", type="float")
		*/
		private $chietKhau;


		/**
		* @ORM\Column(type="integer")
		*/
		private $status;


		// cái này là lấy trong termtaxonomy phân phối: id từ 39, 40, 41, 42, 43 tương ứng: điểm bán, trường học, cơ quan, của hàng bán lẽ
		/**
		* @ORM\ManyToOne(targetEntity="S3UTaxonomy\Entity\ZfTermTaxonomy")
		* @ORM\JoinColumn(name="id_kenh_phan_phoi", referencedColumnName="term_taxonomy_id")
		*/
		private $idKenhPhanPhoi;

		

		public function setIdChietKhau($idChietKhau)
		{
			$this->idChietKhau=$idChietKhau;
		}

		public function getIdChietKhau()
		{
			return $this->idChietKhau;
		}

		public function setIdKho($idKho)
		{
			$this->idKho=$idKho;
		}

		public function getIdKho()
		{
			return $this->idKho;
		}

		public function setChietKhau($chietKhau)
		{
			$this->chietKhau=$chietKhau;
		}
		public function getChietKhau()
		{
			return $this->chietKhau;
		}

		public function setStatus($status)
		{
			$this->status=$status;
		}

		public function getStatus()
		{
			return $this->status;
		}

		public function setIdKenhPhanPhoi($idKenhPhanPhoi)
		{
			$this->idKenhPhanPhoi=$idKenhPhanPhoi;
		}
		public function getIdKenhPhanPhoi()
		{
			return $this->idKenhPhanPhoi;
		}

	}
	

?>