<?

class category
{
	var $uid;
	var $name;
	var $description;
	var $parent_category;
	var $child_categories = array();

	function fillChilds($lvl)
	{
		global $_sC,$db;

		$source = $db->select('uid,name','category','pid='.$this->parent_category);
	}

}
?>
