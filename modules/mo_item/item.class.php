<?
require($_sC->_get('path_helpers').'/xml.helper.php');

class item
{
	var $details = array();
	var $name;
	var $cname; // Dočasně bude bráno z objectu kategorie
	var $cid;
	var $description;
	
	var $hidden = false;
	var $public = false;
	
	private $__defFile;
	private $_definition;
	
	function item()
	{
		global $_sC;
		
		$this->__defFile = $_sC->_get('path_modules').'/mo_item/item_definition.xml';
		
		if( file_exists($this->__defFile) )
		{
			if( $defData = file($this->__defFile) )
			{
				foreach( $defData as $line)
				{
					$line = trim($line);
					
					if( false == empty($line) )
					{
						if( substr($line,0,1) != '#' )
						{
							$this->_processData($line);
						}
					}
				}
			}
		}
	}
	
	function _set($var,$value)
	{
		$vars = get_class_vars( get_class($this) );
		if( array_key_exists($var,$vars) )
		{
			$this->$var = $value;
		}
	}
	function _setDetail($detail,$value)
	{
		if( false == array_key_exists($detail,$this->details) )
		{
			$this->details[$detail] = $value;
		}
	}
	
	private function _processData($data)
	{
		$data = str_replace(" ","",$data);
		if( strpos($data,".",0) )
		{
			list($variable,$value) = explode("=",$data);
			$varPath = explode(".",$variable);
		}
		else
		{
			list($variable,$value) = explode("=",$data);

			if( $value == "GROUP" )
			{
				$this->definition[$variable] = array();
			}
		}
	}
}
?>