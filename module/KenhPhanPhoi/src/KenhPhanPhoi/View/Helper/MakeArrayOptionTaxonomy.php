<?php
namespace KenhPhanPhoi\View\Helper;

use Zend\View\Helper\AbstractHelper;

class MakeArrayOptionTaxonomy extends AbstractHelper{
	public function __invoke($mangs){
		$dm=array();
		if(!$mangs)
		{
			return $dm;
		}
		foreach ($mangs as $mang) 
		{
			if($mang['cap']>0)
			{
				$dm[$mang['termTaxonomyId']]=$mang['termId']['name'];
			}
			
		}	
		return $dm;
	}
}
?>