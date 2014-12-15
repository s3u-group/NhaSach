<?php

namespace DoctrineORMModule\Proxy\__CG__\HangHoa\Entity;

/**
 * DO NOT EDIT THIS FILE - IT WAS CREATED BY DOCTRINE'S PROXY GENERATOR
 */
class SanPham extends \HangHoa\Entity\SanPham implements \Doctrine\ORM\Proxy\Proxy
{
    /**
     * @var \Closure the callback responsible for loading properties in the proxy object. This callback is called with
     *      three parameters, being respectively the proxy object to be initialized, the method that triggered the
     *      initialization process and an array of ordered parameters that were passed to that method.
     *
     * @see \Doctrine\Common\Persistence\Proxy::__setInitializer
     */
    public $__initializer__;

    /**
     * @var \Closure the callback responsible of loading properties that need to be copied in the cloned object
     *
     * @see \Doctrine\Common\Persistence\Proxy::__setCloner
     */
    public $__cloner__;

    /**
     * @var boolean flag indicating if this object was already initialized
     *
     * @see \Doctrine\Common\Persistence\Proxy::__isInitialized
     */
    public $__isInitialized__ = false;

    /**
     * @var array properties to be lazy loaded, with keys being the property
     *            names and values being their default values
     *
     * @see \Doctrine\Common\Persistence\Proxy::__getLazyProperties
     */
    public static $lazyPropertiesDefaults = array();



    /**
     * @param \Closure $initializer
     * @param \Closure $cloner
     */
    public function __construct($initializer = null, $cloner = null)
    {

        $this->__initializer__ = $initializer;
        $this->__cloner__      = $cloner;
    }







    /**
     * 
     * @return array
     */
    public function __sleep()
    {
        if ($this->__isInitialized__) {
            return array('__isInitialized__', '' . "\0" . 'HangHoa\\Entity\\SanPham' . "\0" . 'idSanPham', '' . "\0" . 'HangHoa\\Entity\\SanPham' . "\0" . 'maSanPham', '' . "\0" . 'HangHoa\\Entity\\SanPham' . "\0" . 'tenSanPham', '' . "\0" . 'HangHoa\\Entity\\SanPham' . "\0" . 'moTa', '' . "\0" . 'HangHoa\\Entity\\SanPham' . "\0" . 'hinhAnh', '' . "\0" . 'HangHoa\\Entity\\SanPham' . "\0" . 'nhan', '' . "\0" . 'HangHoa\\Entity\\SanPham' . "\0" . 'idDonViTinh', '' . "\0" . 'HangHoa\\Entity\\SanPham' . "\0" . 'idLoai', '' . "\0" . 'HangHoa\\Entity\\SanPham' . "\0" . 'tonKho', '' . "\0" . 'HangHoa\\Entity\\SanPham' . "\0" . 'giaNhap', '' . "\0" . 'HangHoa\\Entity\\SanPham' . "\0" . 'loaiGia', '' . "\0" . 'HangHoa\\Entity\\SanPham' . "\0" . 'giaBia', '' . "\0" . 'HangHoa\\Entity\\SanPham' . "\0" . 'chiecKhau', '' . "\0" . 'HangHoa\\Entity\\SanPham' . "\0" . 'ctHoaDons', '' . "\0" . 'HangHoa\\Entity\\SanPham' . "\0" . 'ctPhieuNhaps', '' . "\0" . 'HangHoa\\Entity\\SanPham' . "\0" . 'kho');
        }

        return array('__isInitialized__', '' . "\0" . 'HangHoa\\Entity\\SanPham' . "\0" . 'idSanPham', '' . "\0" . 'HangHoa\\Entity\\SanPham' . "\0" . 'maSanPham', '' . "\0" . 'HangHoa\\Entity\\SanPham' . "\0" . 'tenSanPham', '' . "\0" . 'HangHoa\\Entity\\SanPham' . "\0" . 'moTa', '' . "\0" . 'HangHoa\\Entity\\SanPham' . "\0" . 'hinhAnh', '' . "\0" . 'HangHoa\\Entity\\SanPham' . "\0" . 'nhan', '' . "\0" . 'HangHoa\\Entity\\SanPham' . "\0" . 'idDonViTinh', '' . "\0" . 'HangHoa\\Entity\\SanPham' . "\0" . 'idLoai', '' . "\0" . 'HangHoa\\Entity\\SanPham' . "\0" . 'tonKho', '' . "\0" . 'HangHoa\\Entity\\SanPham' . "\0" . 'giaNhap', '' . "\0" . 'HangHoa\\Entity\\SanPham' . "\0" . 'loaiGia', '' . "\0" . 'HangHoa\\Entity\\SanPham' . "\0" . 'giaBia', '' . "\0" . 'HangHoa\\Entity\\SanPham' . "\0" . 'chiecKhau', '' . "\0" . 'HangHoa\\Entity\\SanPham' . "\0" . 'ctHoaDons', '' . "\0" . 'HangHoa\\Entity\\SanPham' . "\0" . 'ctPhieuNhaps', '' . "\0" . 'HangHoa\\Entity\\SanPham' . "\0" . 'kho');
    }

    /**
     * 
     */
    public function __wakeup()
    {
        if ( ! $this->__isInitialized__) {
            $this->__initializer__ = function (SanPham $proxy) {
                $proxy->__setInitializer(null);
                $proxy->__setCloner(null);

                $existingProperties = get_object_vars($proxy);

                foreach ($proxy->__getLazyProperties() as $property => $defaultValue) {
                    if ( ! array_key_exists($property, $existingProperties)) {
                        $proxy->$property = $defaultValue;
                    }
                }
            };

        }
    }

    /**
     * 
     */
    public function __clone()
    {
        $this->__cloner__ && $this->__cloner__->__invoke($this, '__clone', array());
    }

    /**
     * Forces initialization of the proxy
     */
    public function __load()
    {
        $this->__initializer__ && $this->__initializer__->__invoke($this, '__load', array());
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __isInitialized()
    {
        return $this->__isInitialized__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setInitialized($initialized)
    {
        $this->__isInitialized__ = $initialized;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setInitializer(\Closure $initializer = null)
    {
        $this->__initializer__ = $initializer;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __getInitializer()
    {
        return $this->__initializer__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setCloner(\Closure $cloner = null)
    {
        $this->__cloner__ = $cloner;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific cloning logic
     */
    public function __getCloner()
    {
        return $this->__cloner__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     * @static
     */
    public function __getLazyProperties()
    {
        return self::$lazyPropertiesDefaults;
    }

    
    /**
     * {@inheritDoc}
     */
    public function setLoaiGia($loaiGia)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setLoaiGia', array($loaiGia));

        return parent::setLoaiGia($loaiGia);
    }

    /**
     * {@inheritDoc}
     */
    public function getLoaiGia()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getLoaiGia', array());

        return parent::getLoaiGia();
    }

    /**
     * {@inheritDoc}
     */
    public function setKho($kho)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setKho', array($kho));

        return parent::setKho($kho);
    }

    /**
     * {@inheritDoc}
     */
    public function getKho()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getKho', array());

        return parent::getKho();
    }

    /**
     * {@inheritDoc}
     */
    public function setIdSanPham($idSanPham)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setIdSanPham', array($idSanPham));

        return parent::setIdSanPham($idSanPham);
    }

    /**
     * {@inheritDoc}
     */
    public function getIdSanPham()
    {
        if ($this->__isInitialized__ === false) {
            return (int)  parent::getIdSanPham();
        }


        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getIdSanPham', array());

        return parent::getIdSanPham();
    }

    /**
     * {@inheritDoc}
     */
    public function setMaSanPham($maSanPham)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setMaSanPham', array($maSanPham));

        return parent::setMaSanPham($maSanPham);
    }

    /**
     * {@inheritDoc}
     */
    public function getMaSanPham()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getMaSanPham', array());

        return parent::getMaSanPham();
    }

    /**
     * {@inheritDoc}
     */
    public function setTenSanPham($tenSanPham)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setTenSanPham', array($tenSanPham));

        return parent::setTenSanPham($tenSanPham);
    }

    /**
     * {@inheritDoc}
     */
    public function getTenSanPham()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getTenSanPham', array());

        return parent::getTenSanPham();
    }

    /**
     * {@inheritDoc}
     */
    public function setMoTa($moTa)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setMoTa', array($moTa));

        return parent::setMoTa($moTa);
    }

    /**
     * {@inheritDoc}
     */
    public function getMoTa()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getMoTa', array());

        return parent::getMoTa();
    }

    /**
     * {@inheritDoc}
     */
    public function setHinhAnh($hinhAnh)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setHinhAnh', array($hinhAnh));

        return parent::setHinhAnh($hinhAnh);
    }

    /**
     * {@inheritDoc}
     */
    public function getHinhAnh()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getHinhAnh', array());

        return parent::getHinhAnh();
    }

    /**
     * {@inheritDoc}
     */
    public function setNhan($nhan)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setNhan', array($nhan));

        return parent::setNhan($nhan);
    }

    /**
     * {@inheritDoc}
     */
    public function getNhan()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getNhan', array());

        return parent::getNhan();
    }

    /**
     * {@inheritDoc}
     */
    public function setIdDonViTinh($idDonViTinh)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setIdDonViTinh', array($idDonViTinh));

        return parent::setIdDonViTinh($idDonViTinh);
    }

    /**
     * {@inheritDoc}
     */
    public function getIdDonViTinh()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getIdDonViTinh', array());

        return parent::getIdDonViTinh();
    }

    /**
     * {@inheritDoc}
     */
    public function getDonViTinh()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getDonViTinh', array());

        return parent::getDonViTinh();
    }

    /**
     * {@inheritDoc}
     */
    public function setIdLoai($idLoai)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setIdLoai', array($idLoai));

        return parent::setIdLoai($idLoai);
    }

    /**
     * {@inheritDoc}
     */
    public function getIdLoai()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getIdLoai', array());

        return parent::getIdLoai();
    }

    /**
     * {@inheritDoc}
     */
    public function setTonKho($tonKho)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setTonKho', array($tonKho));

        return parent::setTonKho($tonKho);
    }

    /**
     * {@inheritDoc}
     */
    public function getTonKho()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getTonKho', array());

        return parent::getTonKho();
    }

    /**
     * {@inheritDoc}
     */
    public function setGiaNhap($giaNhap)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setGiaNhap', array($giaNhap));

        return parent::setGiaNhap($giaNhap);
    }

    /**
     * {@inheritDoc}
     */
    public function getGiaNhap()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getGiaNhap', array());

        return parent::getGiaNhap();
    }

    /**
     * {@inheritDoc}
     */
    public function setGiaBia($giaBia)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setGiaBia', array($giaBia));

        return parent::setGiaBia($giaBia);
    }

    /**
     * {@inheritDoc}
     */
    public function getGiaBia()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getGiaBia', array());

        return parent::getGiaBia();
    }

    /**
     * {@inheritDoc}
     */
    public function setChiecKhau($chiecKhau)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setChiecKhau', array($chiecKhau));

        return parent::setChiecKhau($chiecKhau);
    }

    /**
     * {@inheritDoc}
     */
    public function getChiecKhau()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getChiecKhau', array());

        return parent::getChiecKhau();
    }

}
