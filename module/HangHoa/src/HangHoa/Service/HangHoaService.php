<?php
namespace HangHoa\Service;

use Zend\ServiceManager\ServiceManagerAwareInterface;
use ZfcBase\EventManager\EventProvider;
use Zend\ServiceManager\ServiceManager;


class HangHoaService extends EventProvider implements ServiceManagerAwareInterface
{
	public function setServiceManager(ServiceManager $serviceManager)
	{
		$this->serviceManager = $serviceManager;
        return $this;
	}

	public function importHangHoaService($target)
	{
		$this->getEventManager()->trigger(__FUNCTION__,$target);
	}

	public function exportHangHoaService($target)
	{
		$this->getEventManager()->trigger(__FUNCTION__,$target);
	}

	public function themHangHoaService($target)
	{
		$this->getEventManager()->trigger(__FUNCTION__,$target);
	}
}
?>
