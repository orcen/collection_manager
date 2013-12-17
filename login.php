<?
session_start();
if( file_exists( "config" ) )  // Does a Config File exist
{

	require( "./system/systemConfig.class.php" );
	$_sC = new systemConfig( "config" );
	
	switch( $_sC->_get('db_type') )
	{
		case 'mysql': require_once( $_sC->_get('path_system') ."mysql.class.php" ); break;
		case 'postgresql': require_once( $_sC->_get('path_system') ."pgsql.class.php" ); break;
		default: die("No Database Type was set!");
	}
	
	//require( $_sC->_get('system_path') ."system/form.helper.php" );
	
	# Local database connection
	$db = db::getInstance();
	
	$db->_set("server",$_sC->_get("db_server"));
	$db->_set("port",$_sC->_get("db_port"));
	$db->_set("user",$_sC->_get("db_user"));
	$db->_set("password",$_sC->_get("db_password"));
	$db->_set("database",$_sC->_get("db_database"));
	$db->_set("coding",$_sC->_get("db_encoding"));
	
	$db->connect();
	
	require( $_sC->_get( 'path_system' ) . 'functions.php' );
	
	(string) $html_output = "";
	
	if( $_sC->_get('debug') == TRUE )
	{
	$html_output .= "<div style='position:absolute;right:0px; border:1px solid #DDD; background-color: #EFEFEF; font-size:10pt;'>\n"
		. "<strong>DEBUG INFO</strong><br />\n"
		. "<i>Configuration</i><br />\n"
		. nl2br( $_sC->showVariables(TRUE) )
		. "<i>_POST Vars</i><br />\n"
		. nl2br( print_r( $_POST, true ) )
		. "<i>_GET Vars</i><br />\n"
		. nl2br( print_r( $_GET, true ) )
		. "<i>_SESSION Vars</i><br />\n"
		. nl2br( print_r( $_SESSION, true ) )
		. "</div>";
	}
}
else
{
	die("<p>The config file couldn`t by found! Please create one!");
}

$user = $db->select_row('*','users','name="'.$_POST['f_username'].'"');

if( $db->row_count == 1)
{
	if($user['passwd'] == $_POST['f_password'])
	{
		$_SESSION['userid'] = $user['uid'];
		header('location: ./index.php');
	}

}
else
{
	echo "uživatel neexistuje<br />";
}

?>