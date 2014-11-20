<?php
namespace HangHoa\View\Helper;

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
				$str='';
				for($i=0; $i<$mang['cap']; $i++)
				{
					$str.='__ ';
				}
				$str.=$mang['termId']['name'];
				$dm[$mang['termTaxonomyId']]=$mang['termId']['name'];
			}
			
		}	
		return $dm;
	}
}
?>