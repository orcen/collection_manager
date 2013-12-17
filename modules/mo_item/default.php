<?
	require ($_sC->_get('path_modules') . 'mo_item/item.class.php');
  
  if ( false == isset($_GET['action']) && false == isset($_GET['search'])) {
		if (false == isset($_GET['showUid'])) {
			$content .= createItemList($catId, true);
		}
		else {
			$content .= itemFullView($_GET['showUid']);
		}
	}

	function createItemList($pid, $allSubCats = false, $list = false) {
		global $_sC, $db, $templateFile;

		if (false == $list) {
			if ($allSubCats == true) {
				if ($childList = getAllChildList($pid)) {
					$whereCond = 'cid IN (' . $childList . ')';
				}
				else {
					$whereCond = 'cid = ' . $pid;
				}
			}
			else {
				$whereCond = 'cid = ' . $pid;
			}
		}
		else {
			$whereCond = 'items.uid IN (' . implode(',', $list) . ')';
		}

		$source = $db->select('uid, cid, name, description', 'items', $whereCond . ' AND hidden=0 AND public=1','crstamp DESC', null, 0, 6);

		if ($source) {
			$content = '<div id="itemList">';

			$itemTemp = getSubpart($templateFile, '###ITEM###');

			$itemObj = new item();

			foreach ($source as $item) {
				$actItem = clone $itemObj;

				$actItem->_set('name', $item['name']);
				$actItem->_set('description', $item['description']);
				$actItem->_set('cid', $item['cid']);

				$markers = array(
					'###CATEGORY###' => $actItem->cname,
					'###NAME###' => $actItem->name,
					'###DESCRIPTION###' => substr($actItem->description, 0, 250));

				if (false != isset($_SESSION['userid']) || $_sC->_get('singleuser_mode') !== false) {
					$markers['###EDITLINK###'] .= '<a href="?action[item][' . $item['uid'] . ']=edit">Upravit</a>';
				}

				if (false != $details = $db->select('*', 'details', 'pid=' . $item['uid'], 'uid ASC')) {
					foreach ($details as $detail) {
						if (in_array($detail['param'], array('images', 'video'))) {
							$markers['###ITEMIMAGE###'] = imgTag(reset(explode("\n", $detail['value'])));
						}
						else {
							$actItem->_setDetail($detail['param'], $detail['value']);
							$markers['###' . strtoupper($detail['param']) . '###'] = (!empty($detail['value']) ? $detail['value'] : "");
						}
					}
				}

				$markers['###MORE###'] = 'Více info';
				$markers['###LINK###'] = '?action=detail&amp;=' . $item['uid'];
				$content .= substituteMarkerArray($itemTemp, $markers);
				unset($actItem);
			}
      $content .= '</div>';
		}

		return $content;
	}

	function getAllChildList($uid) {
		global $_sC, $db;

		$list = $db->select('uid', 'category', 'pid=' . $uid);

		if ($list) {

			$inList = $uid;

			foreach ($list as $litem) {
				$inList .= ',' . $litem['uid'];

				if ($subList = getAllChildList($litem['uid'])) {
					$inList .= ',' . $subList;
				}
			}

			return $inList;
		}
		else {
			return false;
		}
	}

	function form_new($par) {
		global $_sC, $db;

		//require($_sC->_get('path_helpers').'form.helper.php');

		$catRes = $db->select('uid,name', 'category', null, 'uid ASC,pid ASC');

		$categories = array('' => 'zvolte');

		foreach ($catRes as $catRow) {
			$categories[$catRow['uid']] = $catRow['name'];
		}

		$form = new form();
		$form->form_method = 'post';
		$form->form_enctype = 'multipart/form-data';
		$form->form_id = 'newItem';

		$form->form_fields = array(
			'mainData' => array(
				'fieldset' => 'mainData',
				'legend' => 'Základní údaje',
				'fields' => array(
					array(
						'type' => 'text',
						'name' => 'name',
						'value' => '',
						'label' => 'Název',
						'value' => $_POST['f_name']),
					array(
						'type' => 'textarea',
						'name' => 'description',
						'id' => 'description',
						'label' => 'Popis',
						'rows' => 10,
						'cols' => 50,
						'value' => $_POST['f_description']),
					array(
						'type' => 'select',
						'name' => 'cid',
						'label' => 'Kategorie',
						'value' => $categories,
						'selected' => $_POST['f_cid']),
					array(
						'type' => 'radio',
						'name' => 'hidden',
						'label' => 'Skrýt',
						'value' => array(0 => 'Ne', 1 => 'Ano'),
						'checked' => 0),
					array(
						'type' => 'radio',
						'name' => 'public',
						'label' => 'Soukromý',
						'value' => array(0 => 'Ne', 1 => 'Ano'),
						'checked' => 0))),
			'details' => array(
				'fieldset' => 'details',
				'legend' => 'Detaily',
				'fields' => array()),
			array('fieldset' => 'controlls', 'fields' => array(array(
						'type' => 'submit',
						'name' => 'save',
						'value' => 'uložit'))));
		$details = array(
			array(
				'type' => 'text',
				'name' => 'manufactory',
				'id' => 'manufactory',
				'label' => 'Výrobce',
				'value' => $_POST['f_manufactory'] /*,'onchange'=>'getManufactory(this.id);'*/ ),
			array(
				'type' => 'text',
				'name' => 'overall-length',
				'label' => 'celková délka',
				'value' => $_POST['f_overall-length'],
				'description' => 'v mm'),
			array(
				'type' => 'text',
				'name' => 'blade-length',
				'label' => 'délka čepele',
				'value' => $_POST['f_blade-length'],
				'description' => 'v mm'),
			array(
				'type' => 'text',
				'name' => 'thickness',
				'label' => 'síla čepele',
				'value' => $_POST['f_thickness'],
				'description' => 'v mm'),
			array(
				'type' => 'text',
				'name' => 'weight',
				'label' => 'váha',
				'value' => $_POST['f_weight'],
				'description' => 'v g'),
			array(
				'type' => 'text',
				'name' => 'hardness',
				'label' => 'tvrdost',
				'value' => $_POST['f_hardness'],
				'description' => 'v HRC'),
			array(
				'type' => 'text',
				'name' => 'steel',
				'id' => 'steel',
				'label' => 'ocel',
				'value' => $_POST['f_steel'] /*,'onchange'=>'getMaterial("blade",this.id);'*/ ),
			array(
				'type' => 'text',
				'name' => 'handle-length',
				'label' => 'délka rukojeti',
				'value' => $_POST['f_handle-length'],
				'description' => 'v mm'),
			array(
				'type' => 'text',
				'name' => 'handle-material',
				'id' => 'handleMaterial',
				'label' => 'materiál rukojeti',
				'value' => $_POST['f_handle-material'] /*,'onchange'=>'getMaterial("handle",this.id);'*/ ),
			array(
				'type' => 'file',
				'name' => 'image1',
				'label' => 'obrázek'),
			array(
				'type' => 'file',
				'name' => 'image2',
				'label' => 'obrázek'),
			array(
				'type' => 'file',
				'name' => 'image3',
				'label' => 'obrázek'),
			array(
				'type' => 'file',
				'name' => 'image4',
				'label' => 'obrázek'),
			array(
				'type' => 'file',
				'name' => 'image5',
				'label' => 'obrázek'),
			array(
				'type' => 'file',
				'name' => 'image6',
				'label' => 'obrázek'),
			//array('type'=>'hidden','name'=>'MAX_FILE_SIZE','value'=>($_sC->_get('max_file_upload_size')*1024*1024))
			);

		$form->form_fields['details']['fields'] = $details;

		return $form->create_output(true);
	}

	function form($action, $par) {
		global $_sC, $db;

		//require($_sC->_get('path_helpers').'form.helper.php');

		if ($action == 'edit') {
			$mainData = $db->select_row('*', 'items', 'uid=' . $par);
			$detailsData = $db->select('param,value', 'details', 'pid=' . $par);
			$newDetailsData = array();

			if (false != $detailsData) {
				foreach ($detailsData as $dat) {
					$newDetailsData[$dat['param']] = $dat['value'];
				}
				$detailsData = $newDetailsData;
			}
		}

		$catRes = $db->select('uid,name', 'category', null, 'uid ASC,pid ASC');

		$categories = array('' => 'zvolte');

		foreach ($catRes as $catRow) {
			$categories[$catRow['uid']] = $catRow['name'];
		}

		$form = new form();
		$form->form_method = 'post';
		$form->form_enctype = 'multipart/form-data';
		$form->form_id = 'newItem';

		$form->form_fields = array(
			'mainData' => array(
				'fieldset' => 'mainData',
				'legend' => 'Základní údaje',
				'fields' => array(
					array(
						'type' => 'text',
						'name' => 'name',
						'value' => '',
						'label' => 'Název',
						'value' => ($action == 'edit' ? $mainData['name'] : $_POST['f_name'])),
					array(
						'type' => 'textarea',
						'name' => 'description',
						'id' => 'description',
						'label' => 'Popis',
						'rows' => 10,
						'cols' => 50,
						'value' => ($action == 'edit' ? $mainData['description'] : $_POST['f_description'])),
					array(
						'type' => 'select',
						'name' => 'cid',
						'label' => 'Kategorie',
						'value' => $categories,
						'selected' => ($action == 'edit' ? $mainData['cid'] : $_POST['f_cid'])),
					array(
						'type' => 'radio',
						'name' => 'hidden',
						'label' => 'Skrýt',
						'value' => array(0 => 'Ne', 1 => 'Ano'),
						'checked' => ($action == 'edit' ? $mainData['hidden'] : 0)),
					array(
						'type' => 'radio',
						'name' => 'public',
						'label' => 'Veřejný',
						'value' => array(0 => 'Ne', 1 => 'Ano'),
						'checked' => ($action == 'edit' ? $mainData['public'] : 1)))),
			'details' => array(
				'fieldset' => 'details',
				'legend' => 'Detaily',
				'fields' => array()),
			array('fieldset' => 'controlls', 'fields' => array(array(
						'type' => 'submit',
						'name' => 'save',
						'value' => 'uložit'))));

		$details = array(
			array(
				'type' => 'text',
				'name' => 'manufactory',
				'id' => 'manufactory',
				'label' => 'Výrobce',
				'value' => ($action == 'edit' ? $detailsData['manufactory'] : $_POST['f_manufactory']) /*,'onchange'=>'getManufactory(this.id);'*/ ),
			array(
				'type' => 'text',
				'name' => 'overall-length',
				'label' => 'celková délka',
				'value' => ($action == 'edit' ? $detailsData['overall-length'] : $_POST['f_overall-length']),
				'description' => 'v mm'),
			array(
				'type' => 'text',
				'name' => 'blade-length',
				'label' => 'délka čepele',
				'value' => ($action == 'edit' ? $detailsData['blade-length'] : $_POST['f_blade-length']),
				'description' => 'v mm'),
			array(
				'type' => 'text',
				'name' => 'thickness',
				'label' => 'síla čepele',
				'value' => ($action == 'edit' ? $detailsData['thickness'] : $_POST['f_thickness']),
				'description' => 'v mm'),
			array(
				'type' => 'text',
				'name' => 'weight',
				'label' => 'váha',
				'value' => ($action == 'edit' ? $detailsData['weight'] : $_POST['f_weight']),
				'description' => 'v g'),
			array(
				'type' => 'text',
				'name' => 'hardness',
				'label' => 'tvrdost',
				'value' => ($action == 'edit' ? $detailsData['hardness'] : $_POST['f_hardness']),
				'description' => 'v HRC'),
			array(
				'type' => 'text',
				'name' => 'steel',
				'id' => 'steel',
				'label' => 'ocel',
				'value' => ($action == 'edit' ? $detailsData['steel'] : $_POST['f_steel']) /*,'onchange'=>'getMaterial("blade",this.id);'*/ ),
			array(
				'type' => 'text',
				'name' => 'handle-length',
				'label' => 'délka rukojeti',
				'value' => ($action == 'edit' ? $detailsData['handle-lengt'] : $_POST['f_handle-length']),
				'description' => 'v mm'),
			array(
				'type' => 'text',
				'name' => 'handle-material',
				'id' => 'handleMaterial',
				'label' => 'materiál rukojeti',
				'value' => ($action == 'edit' ? $detailsData['handle-material'] : $_POST['f_handle-material']) /*,'onchange'=>'getMaterial("handle",this.id);'*/ ),
			array(
				'type' => 'file',
				'name' => 'image1',
				'label' => 'obrázek'),
			array(
				'type' => 'file',
				'name' => 'image2',
				'label' => 'obrázek'),
			array(
				'type' => 'file',
				'name' => 'image3',
				'label' => 'obrázek'),
			array(
				'type' => 'file',
				'name' => 'image4',
				'label' => 'obrázek'),
			array(
				'type' => 'file',
				'name' => 'image5',
				'label' => 'obrázek'),
			array(
				'type' => 'file',
				'name' => 'image6',
				'label' => 'obrázek'),
			//array('type'=>'hidden','name'=>'MAX_FILE_SIZE','value'=>($_sC->_get('max_file_upload_size')*1024*1024))
			);

		if ($action == 'edit') {
			$form->form_fields['mainData']['fields'][] = array(
				'type' => 'hidden',
				'name' => 'uid',
				'value' => $par);
		}
		$form->form_fields['details']['fields'] = $details;

		return $form->create_output(true);
	}

	function itemFullView($uid) {
		global $_sC, $db, $templateFile;

		$template = getSubpart($templateFile, '###FULLVIEW###');

		$dataMain = $db->select_row('*', 'items', 'uid=' . $uid);
		$dataDetails = $db->select('*', 'details', 'pid=' . $uid);

		$markers = array();

		$description = $dataMain['description'];
		$description = "<p>" . preg_replace("/\r?\n/us", "</p><p>", $description) . "</p>";
		$description = str_replace("<p></p>", "", $description);
		$dataMain['description'] = $description;

		foreach ($dataMain as $col => $val) {
			$markers['###' . strtoupper($col) . '###'] = $val;
		}

		foreach ($dataDetails as $detail) {
			if ($detail['param'] == 'images') {
				$images = explode("\n", trim($detail['value']));
				for ($i = 0; $i < count($images); $i++) {
					if (!empty($images[0])) {
						$imageGallery .= imgTag($images[$i], array('width' => '200px'));
					}
				}

				$markers['###IMAGES###'] = $imageGallery;
			}
			else {
				$markers['###' . strtoupper($detail['param']) . '###'] = $detail['value'];
			}
		}

		$result = substituteMarkerArray($template, $markers);

		$commentsData = $db->select('*', 'comments', 'pid=' . $uid, 'crstamp DESC');


		$comments = '<div id="comments">' . "\n" . '<h4>Komentáře</h4>';

		if ($db->row_count > 0) {
			for ($i = 0; $i < count($commentsData); $i++) {
				$comm = $commentsData[$i];
				$comments .= '<div class="commItem ' . ($i % 2 ? 'odd' : 'even') . '">' . '<h5>' . $comm['title'] . '</h5>' . '<p>' . $comm['text'] . '</p>' . '</div>';
			}
		}

		$comments .= '</div>';
		$commForm = new form();
		$commForm->form_fields = array(array(
				'fieldset' => 'comment',
				'legend' => 'Komentář',
				'fields' => array(
					array(
						'type' => 'text',
						'name' => 'title',
						'size' => 75,
						'label' => 'Titulek'),
					array(
						'type' => 'textarea',
						'id' => 'text',
						'name' => 'text',
						'rows' => 5,
						'cols' => 70,
						'label' => 'Text'),
					array(
						'type' => 'text',
						'name' => 'creator',
						'size' => 45,
						'label' => 'Autor',
						'value' => '(anonym)'),
					array(
						'type' => 'submit',
						'name' => 'save',
						'value' => 'Odeslat'))));
		$comments .= $commForm->create_output(true);

		$result .= $comments;

		return $result;
	}

	function searchForm($data) {
		global $_sC, $db;


		$catData = $db->select('uid, name', 'category');

		$catList = array('null' => 'Všechny');

		foreach ($catData as $cat) {
			$catList[$cat['uid']] = $cat['name'];
		}

		$sF = new form(); // create object for (s)earch(F)orm

		$sF->form_method = 'post';
		$sF->form_id = 'searchForm';
		$sF->form_fields = array(
			'mainSearch' => array(
				'fieldset' => 'mainSearch',
				'legend' => 'Vyhedávání',
				'fields' => array(array(
						'type' => 'text',
						'name' => 'keyword',
						'label' => 'Klíčové slovo',
						'size' => 45,
						'value' => $data['f_keyword'],
						'help' => array('value' => 'Vyhledává v názvu a popisu', 'icon' => 'help.gif')), array(
						'type' => 'select',
						'name' => 'category',
						'label' => 'Kategorie',
						'value' => $catList,
						'selected' => $data['f_category']))),
			'detailSearch' => array(
				'fieldset' => 'detailSearch',
				'legend' => 'Podrobný hledání',
				'fields' => array(
					array(
						'type' => 'text',
						'name' => 'overall-length',
						'label' => array('value' => 'celková délka', 'class' => 'part1'),
						'value' => $_POST['f_overall-length'],
						'description' => 'v mm',
						'help' => array('value' => 'Včetně hodnoty', 'icon' => 'help.gif')),
					array(
						'type' => 'select',
						'name' => 'handle_overall-length',
						'class' => 'part2',
						'value' => array(
							'>=' => '>',
							'<=' => '<',
							'=' => '='),
						'selected' => $_POST['f_handle_overall-length']),
					array(
						'type' => 'text',
						'name' => 'blade-length',
						'label' => array('value' => 'délka čepele', 'class' => 'part1'),
						'value' => $_POST['f_blade-length'],
						'description' => 'v mm',
						'help' => array('value' => 'Včetně hodnoty', 'icon' => 'help.gif')),
					array(
						'type' => 'select',
						'name' => 'handle_blade-length',
						'value' => array(
							'>=' => '>',
							'<=' => '<',
							'=' => '='),
						'selected' => $_POST['f_handle_blade-length']),
					array(
						'type' => 'text',
						'name' => 'thickness',
						'label' => array('value' => 'síla čepele', 'class' => 'part1'),
						'value' => $_POST['f_thickness'],
						'description' => 'v mm',
						'help' => array('value' => 'Včetně hodnoty', 'icon' => 'help.gif')),
					array(
						'type' => 'select',
						'name' => 'handle_thickness',
						'value' => array(
							'>=' => '>',
							'<=' => '<',
							'=' => '='),
						'selected' => $_POST['f_handle_thickness']),
					array(
						'type' => 'text',
						'name' => 'weight',
						'label' => array('value' => 'váha', 'class' => 'part1'),
						'value' => $_POST['f_weight'],
						'description' => 'v g',
						'help' => array('value' => 'Včetně hodnoty', 'icon' => 'help.gif')),
					array(
						'type' => 'select',
						'name' => 'handle_weight',
						'value' => array(
							'>=' => '>',
							'<=' => '<',
							'=' => '='),
						'selected' => $_POST['f_handle_weight']),
					)),
			'controls' => array('fieldset' => 'control', 'fields' => array(array(
						'type' => 'submit',
						'name' => 'search',
						'value' => 'Hledat'))));

		return $sF->create_output(true);
	}

	function searchResult($data) {
		global $_sC, $db;

		$args = array(
			'f_keyword' => FILTER_SANITIZE_STRING,
			'f_category' => FILTER_VALIDATE_INT,
			'f_handle_blade-length' => FILTER_SANITIZE_STRING,
			'f_blade-length' => FILTER_VALIDATE_INT,
			'f_handle_overall-length' => FILTER_SANITIZE_STRING,
			'f_overall-length' => FILTER_VALIDATE_INT,
			'f_handle_thickness' => FILTER_SANITIZE_STRING,
			'f_thickness' => FILTER_VALIDATE_FLOAT,
			'f_handle_weight' => FILTER_SANITIZE_STRING,
			'f_weight' => FILTER_VALIDATE_INT,
			);

		$data = filter_var_array($data, $args);
		$keyword = $_POST['f_keyword'];
		$category = $_POST['f_category'];

		$extData = array();
		$extData['blade-length'] = $_POST['f_blade-length'] ? $_POST['f_handle_blade-length'] . ' ' . $_POST['f_blade-length'] : null;
		$extData['overall-length'] = $_POST['f_overall-length'] ? $_POST['f_handle_overall-length'] . ' ' . $_POST['f_overall-length'] : null;
		$extData['thickness'] = $_POST['f_thickness'] ? $_POST['f_handle_thickness'] . ' ' . $_POST['f_thickness'] : null;
		$extData['weight'] = $_POST['f_weight'] ? $_POST['f_handle_weight'] . ' ' . $_POST['f_weight'] : null;

		$list = array();

		if (strlen($data['f_keyword']) >= 3) {
			$searchArray = $db->select('items.*', 'items left join category on items.cid=category.uid', '(items.description LIKE "%' . $data['f_keyword'] .
				'%" OR items.name  LIKE "%' . $keyword . '%")' . (false == empty($category) ? ' AND items.cid = ' . $category : ' AND 1'));

			if ($db->row_count > 0) {
				foreach ($searchArray as $item) {
					$list[] = $item['uid'];
				}
			}
		}

		$whereCond = "";

		foreach ($extData as $param => $arg) {
			if (false == empty($arg)) {
				$whereCond .= '(param = "' . $param . '" AND value ' . $arg . ') AND ';
			}
		}

		if ($category != 'null') {
			$whereCond .= 'cid=' . $category;
		}
		else {
			$whereCond .= 1;
		}

		$searchArray = $db->select('items.*', 'items left join details on items.uid=details.pid', $whereCond, null, 'items.uid');

		if ($db->row_count > 0) {
			foreach ($searchArray as $item) {
				$list[] = $item['uid'];
			}
		}

		$result = createItemList(null, false, $list);
		return $result;
	}

	function parVal($a) {
		return array($a['param'] => $a['value']);
	}

?>
