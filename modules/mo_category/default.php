<?

require( $_sC->_get('path_modules') . 'mo_category/category.class.php' );

function createCategoryList($pid=0) {
	global $_sC, $db;
	(string) $result = null;

  $result = '<ul class="nav">';
  if( $pid == 0 ) $result .= '<li class="nav-item"><a href="?category='.$pid.'" class="nav-link">Všechno</a></li>';

	if( $source = $db->select('uid, pid, name, description','category','pid='.$pid) )	{
		foreach( $source as $category ) {
			$result .= '<li class="nav-item"><a href="?category='.$category['uid'].'" title="'.$category['description'].'" class="nav-link">'.$category['name'].'</a>';
      $result .= createCategoryList($category['uid']);
      $result .= '</li>';
    }
	}
  if( $pid == 0 ) $result .= '<li class="nav-item"><a href="?action[item]=new" class="nav-link">Nový nůž</a></li>';
  $result .= '</ul>';

	return $result;
}

?>
