<?

	session_start();

	header('Content-Type: text/html; charset=utf-8');

	if (file_exists("config")) // Does a Config File exist
		{
		require ("./system/systemConfig.class.php");
		$_sC = new systemConfig("config");

		switch ($_sC->_get('db_type')) {
			case 'mysql':
				require_once ($_sC->_get('path_system') . "mysql.class.php");
				break;
			case 'postgresql':
				require_once ($_sC->_get('path_system') . "pgsql.class.php");
				break;
			default:
				die("No Database Type was set!");
		}

		# Local database connection
		$db = db::getInstance();

		$db->_set("server", $_sC->_get("db_server"));
		$db->_set("port", $_sC->_get("db_port"));
		$db->_set("user", $_sC->_get("db_user"));
		$db->_set("password", $_sC->_get("db_password"));
		$db->_set("database", $_sC->_get("db_database"));
		$db->_set("coding", $_sC->_get("db_encoding"));

		$db->connect();

		require ($_sC->_get('path_system') . 'functions.php');

		(string )$html_output = "";

		if ($_sC->_get('debug') == true) {
			$html_output .= "<div style='position:absolute;right:0px; border:1px solid #DDD; background-color: #EFEFEF; font-size:10pt;'>\n" .
				"<strong>DEBUG INFO</strong><br />\n" . "<i>Configuration</i><br />\n" . nl2br($_sC->showVariables(true)) . "<i>_POST Vars</i><br />\n" . nl2br(print_r($_POST, true)) .
				"<i>_GET Vars</i><br />\n" . nl2br(print_r($_GET, true)) . "<i>_SESSION Vars</i><br />\n" . nl2br(print_r($_SESSION, true)) . "</div>";
		}

		require_once ($_sC->_get('path_helpers') . 'form.helper.php');
		require_once ($_sC->_get('path_helpers') . 'table.helper.php');
	}
	else {
		die("<p>The config file was not found! Please create one!");
	}

	$templateFile = file_get_contents($_sC->_get('path_templates') . 'main_template.html');

	include ($_sC->_get('path_modules') . 'mo_category/default.php');


	$catId = ((isset($_GET['category']) && !empty($_GET['category'])) ? $_GET['category'] : 0);

	$navigation = createCategoryList();

	$content = "";

	include ($_sC->_get('path_modules') . 'mo_item/default.php');

	/*if (false != isset($_GET['search'])) {

		$content .= searchForm($_POST);

		if (false != isset($_POST['f_search'])) {
			$content .= searchResult($_POST);
		}
	}
	else {
		if (false != isset($_POST['f_save'])) {
			$item = new item();

			$mainData = array();
			$details = array();
			unset($_POST['f_save']);

			foreach ($_POST as $key => $value) {
				$key = substr($key, 2);
				if (in_array($key, array('cid','name','description','hidden','public','uid'))) {
					$mainData[$key] = $value;
					$item->_set($key, $value);
				}
				else {
					$details[$key] = $value;
				}
			}

			if (false == isset($_POST['f_uid'])) {
				$db->insert($mainData, 'items');
			}
			else {

				$db->update($mainData, 'items', 'uid=' . $_POST['f_uid']);
			}
			$uid = ($db->last_ID);


			foreach ($details as $param => $value) {
				if (false == isset($_POST['f_uid'])) {
					$db->insert(array(
						'pid' => $uid,
						'param' => $param,
						'value' => $value), 'details');
				}
				else {
					//$db->update(array('value'=>$value),'details','uid='.$uid.' AND param="'.$param.'"');
				}
			}

			$files = array_keys($_FILES);

			for ($f = 0; $f < count($files); $f++) {
				if (!empty($_FILES[$files[$f]]['name'])) {
					$target_path = $_sC->_get('path_images') . $_FILES[$files[$f]]['name'];
					$images = "";
					if (move_uploaded_file($_FILES[$files[$f]]['tmp_name'], $target_path)) {
						$images .= $_FILES[$files[$f]]['name'];
					}
					else {
						echo $_FILES[$files[$f]]['error'];
					}

					if (!empty($images)) {
						$db->insert(array(
							'pid' => $uid,
							'param' => 'images',
							'value' => trim($images)), 'details');
					}
				}
				else {
					continue;
				}
			}
		}

		$module = reset(array_keys($_GET['action']));

		if (is_array($_GET['action'][$module])) {
			$modKey = reset(array_keys($_GET['action'][$module]));
			$action = $_GET['action'][$module][$modKey];
			$function = 'form';
			$functionPar = $modKey;
		}
		else {
			$function = 'form';
			$action = $_GET['action'][$module];
			$functionPar = null;
		}
		$content .= $function($action, $functionPar);
	}*/

	$border_content = '<h2> News </h2>';

	if (false == isset($_SESSION['userid']) and $_sC->_get('singleuser_mode') == false) {
		$loginForm = new form();
		$loginForm->form_method = 'post';
		$loginForm->form_target = 'login.php';
		$loginForm->form_fields = array(
			array(
				'type' => 'text',
				'name' => 'username',
				'label' => 'Uživatelské jméno'),
			array(
				'type' => 'password',
				'name' => 'password',
				'label' => 'Heslo'),
			array(
				'type' => 'submit',
				'name' => 'login',
				'value' => 'Přihlásit'));
		$loginFormResult = $loginForm->create_output(true);
	}
	else {
		$loginFormResult = null;
	}

	$styleLinks = '<link href="' . $_sC->_get('domain') . '/templates/css/default.css" rel="stylesheet" type="text/css" media="screen" />';

	$markers = array(
		"###TITLE###" => $_sC->_get('system_title'),
		"###HEADTITLE###" => $_sC->_get('system_title'),
		"###LOGINFORM###" => $loginFormResult,
		"###TOPNAVIGATION###" => '<a href="?search" class="topnav-link">Hledat</a><a href="" class="topnav-link">Impressum</a><a href="" class="topnav-link">Nápověda</a>',
		"###NAVIGATION###" => $navigation,
		"###CONTENT###" => $content,
		"###BORDER_CONTENT###" => $border_content,
    '###METADESCRIPTION###' => '',
    '###METAKEYWORDS###' => '',
    '###FOOTER###' => '',
		"###CSSSTYLES###" => $styleLinks,
		'###JSFILE1###' => $_sC->_get('domain') . '/javascript/jquery.js',
		'###JSFILE2###' => $_sC->_get('domain') . '/javascript/jquery-handler.js');

	$mainTemp = getSubpart($templateFile, '###MAINTEMPLATE###');

	$html_output = substituteMarkerArray($mainTemp, $markers);

	print ($html_output);

	$db->close();

?>
