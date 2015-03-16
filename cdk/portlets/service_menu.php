<?php
// Saman Portal
// Copyright (C) 2008 by the Saman Information Structure Development Team.
// http://www.sis-eg.com/
// ----------------------------------------------------------------------

function cdk_service_menublock_init(){
	pnSecAddSchema('cdk:service_menu:', 'Block title::');
}

function cdk_service_menublock_info(){
    return array('text_type' => 'service_menu',
                 'module' => 'cdk',
                 'text_type_long' => _CDK_SERVICE_MENU_BLOCK,
                 'allow_multiple' => true,
                 'form_content' => false,
                 'form_refresh' => false,
                 'show_preview' => false,
				 'default_title' => _CDK_SERVICE_MENU_BLOCK_TITLE,
				 'allow_user_add'  => 0,
				 'group' => 2,
				 'block_description' => _CDK_SERVICE_MENU_BLOCK_DESCRIPTION,
			     'allow_subportal_add'  => 1,
				 'is_object' 			=> true
                 );
}

$GLOBALS['object_settings']['service_menu'] = array('contentType', 'menuItems', 'loadType', 'menuHeader', 'menuFooter');

$GLOBALS['portlet_settings']['service_menu'] = array('loadType'		=> _SERVICE_MENU_ITEMS_LOAD_TYPE,
												     'menuItems' 	=> _SERVICE_MENU_ITEMS,
												     'menuHeader'	=> _SERVICE_MENU_HEADER_FOOTER
												     );

//$GLOBALS['portlet_settings_related']['service_menu'] = array();

function cdk_service_menublock_display($blockinfo){	
	
    if (!pnSecAuthAction(0, 'cdk::service_menu', "$blockinfo[title]::", ACCESS_READ)) 
    	return;
    $vars = pnBlockVarsFromContent($blockinfo['content']);

	if ($vars['contentType'] || $vars['_type_name_']) {
		pnModFunc('cdk', 'user', 'loadmodule', array('system'=>'cdk', 'sismodule'=>'header.php'));
		pnModAPIFunc('cdk', 'user', 'registerTypeFunctions', array('type_name'=>$vars['contentType']?$vars['contentType']:$vars['_type_name_']));
	}

    $jsonClass = new Solar_Json();
	$menuItems = $jsonClass->decode($vars['menuItems'],true);
	$menu = array();
	$i = 0;
		
	$contentType = getContentType($vars['ctp_id']?$vars['ctp_id']:$vars['_ctp_id_']);
	
	if($contentType && $contentType['type']==DYNAMIC_CONTENT_TYPE && $contentType['perm_type']==CNT_CONTENT_TYPE_PERM_TYPE_ROLE_BASE && isset($contentType['settings']['roleSet']) ){
		$parentType = $contentType;
		if($parentType['main_ctp_id']){
			$mainCtpId = $parentType['main_ctp_id'];
			do{
				$parentType = getContentType($mainCtpId);
				$mainCtpId = $parentType['main_ctp_id'];
			}while ($mainCtpId);
		}
		$role = sisLoadContentTypeRoles($parentType['ctp_id']);
	}

	foreach ($menuItems['items'] as $menuItem) {
	
		if($menuItem->objectId == "" || ($menuItem->objectId != "" && $role && $role->isMenuAccesible((sisSession('__sisUserID')), $menuItem->objectId))){
			$menuTmp = array();
			$title = 'title_'.pnUserGetLang();
			if ($menuItem->$title) {
				$title = $menuItem->$title;
			}
			else {
				$title = $menuItem->title;
			}
			$perm = false;
			if($contentType['perm_type']==CNT_CONTENT_TYPE_PERM_TYPE_ROLE_BASE){
				$perm=1;
			}
			else{
				if ($menuItem->perm == "-1") {
					$accessCode = serviceMenuContentDecode($menuItem->accessCode);
					if ($accessCode) {
						eval("\$perm = $accessCode;");
					}
				}
				else {
					$perm = pnSecAuthAction(0, 'cdk::', ":$contentType[type_name]:", $menuItem->perm, false);
				}
			}
			
			$menuTmp = array('menu_id' => $i,
							'title'   => serviceMenuContentDecode($title),
							'link'    => serviceMenuContentDecode($menuItem->url),
							'access'  => $perm,
							'icon'	  => '',
							'submenu' =>array()
							);
			$i++;
			$j=0;
			foreach ($menuItem->subItems as $subMenuItem) {
				if($subMenuItem->objectId == "" || ($subMenuItem->objectId!="" && $role->isMenuAccesible((sisSession('__sisUserID')),$subMenuItem->objectId))){
					$title = 'title_'.pnUserGetLang();
					if ($subMenuItem->$title) {
						$title = $subMenuItem->$title;
					}
					else {
						$title = $subMenuItem->title;
					}
					$perm = false;
					if($contentType['perm_type']==CNT_CONTENT_TYPE_PERM_TYPE_ROLE_BASE){
						$perm=$role->isMenuAccesible((sisSession('__sisUserID')),$menuItem->objectId);
						
					}
					else{
							if ($subMenuItem->perm == "-1") {
								$accessCode = serviceMenuContentDecode($subMenuItem->accessCode);
								if ($accessCode) {
									eval("\$perm = $accessCode;");
								}
							}
							else {
								$perm = pnSecAuthAction(0, 'cdk::', ":$contentType[type_name]:", $subMenuItem->perm, false);
							}
					}
					$menuTmp['submenu'][] = array('submenu_id' => $j,
												'title'   => serviceMenuContentDecode($title),
												'link'    => serviceMenuContentDecode($subMenuItem->url),
												'access'  => $perm,
												'icon'	  => ''
												);
					$j++;
			}
		}
			$menu[] = $menuTmp;
	
		}
	}

	require_once('services/theme_engine/plugins/function.createservicemenu.php');
	ob_start();
	if (trim($vars['menuHeader'])) {
		eval($vars['menuHeader']);
	}
	$header = ob_get_clean();
	ob_start();
	if (trim($vars['menuFooter'])) {
		eval($vars['menuFooter']);
	}
	$footer = ob_get_clean();
    $blockinfo['content'] = $header . createServiceMenu($menu, false) . $footer;

	if($vars['loadType'])
		$GLOBALS['__sisMenu__'] = $blockinfo['content'];
	else 
	    return themesideblock($blockinfo);
	return ;
}
function cdk_service_menublock_modify($blockinfo){

	$dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

    $vars = pnBlockVarsFromContent($blockinfo['content']);
    $lang = pnUserGetLang();
	$aling = 'left';
	$notaling = 'right';
	if ($lang == 'far') {
		$aling = 'right';
		$notaling = 'left';
	}
	if(empty($blockinfo['block_id'])){
		if(!empty($blockinfo['ctp_id'])){
			$output .= "<input type='hidden' name='ctp_id' value='$blockinfo[ctp_id]' />";
		}
		elseif (pnBlockIsObjectSetting($blockinfo['block_id'],'service_menu','contentType')){
		    $types = pnModAPIFunc('cdk', 'user', 'getAccessibleTypes');
		    $typesOptions = "";
			foreach ($types as $key => $value) {
				$selected = '';
				if ($value['type_name'] == $vars['contentType'])
					$selected = 'selected';
				$typesOptions .= "<option value='$value[type_name]' $selected >$value[title]</option>";
			}
		    $output .= '<tr>
							<td class="caption" nowrap="nowrap">'
								. _SERVICE_MENU_ITEMS_CONENT_TYPE . ' : 
							</td>
							<td>
								<select name="contentType" id="cmbContentType" >'
									. $typesOptions . '
								</select>
					   		</td>
						</tr>';
		}
	}
	if(pnBlockIsObjectSetting($blockinfo['block_id'],'service_menu','menuHeader')){
	    $output .= '<tr>
						<td class="caption"  valign="top">
						' . _SERVICE_MENU_HEADER . ' :
						</td>
						<td>
							<textarea name="menuHeader" wrap="off" style="width:80%;height:100px;direction:ltr;FONT-SIZE: 13px; FONT-FAMILY: Courier New; resize: none">' . pnVarPrepForDisplay($vars['menuHeader']) . '</textarea>
				   		</td>
					</tr>
					<tr>
						<td class="caption"  valign="top">
						' . _SERVICE_MENU_FOOTER . ' :
						</td>
						<td>
							<textarea name="menuFooter" wrap="off" style="width:80%;height:100px;direction:ltr;FONT-SIZE: 13px; FONT-FAMILY: Courier New; resize: none">' . pnVarPrepForDisplay($vars['menuFooter']) . '</textarea>
				   		</td>
					</tr>';
	}
	if(pnBlockIsObjectSetting($blockinfo['block_id'],'service_menu','loadType')){
		if($vars['loadType'])
			$loadTypeChecked = 'checked';
	    $output .= '<tr>
						<td class="caption" nowrap="nowrap">
						</td>
						<td>
							<input type="checkbox" name="loadType" value=1 '.$loadTypeChecked.'>'._SERVICE_MENU_ITEMS_LOAD_TYPE.'
				   		</td>
					</tr>';
	}
	if(pnBlockIsObjectSetting($blockinfo['block_id'],'service_menu','menuItems')){
		if (empty($vars['menuItems']))
	    	$vars['menuItems'] = '{"items":[]}';
	    $output .= '<tr><td valign="top" class="caption">' . _SERVICE_MENU_ITEMS . ' :</td><td>
				        	<table style="width:100%;table-layout:fixed" border="0" cellpadding="0">
				        		<tr>
				        			<td style="width:200px;padding-'.$notaling.':2px;">
				        				<div style="border:1px solid #a2c6ec">
				        				<div style="border-bottom:1px solid #a2c6ec;padding:3px;background-color:#dae6f0;height:19px">
				        					<div class="toolbar-item" onmouseover="this.className = \'toolbar-selected-item\';" onmouseout="this.className = \'toolbar-item\';">
				        						<img id="imgAdd" src="images/custom_menu/'.$lang.'/add.png" onclick="addNode();"/>
				        					</div>
				        					<img src="images/custom_menu/'.$lang.'/delimiter.png" style="float:'.$aling.';margin-'.$notaling.':3px"/>
				        					<div class="toolbar-item" onmouseover="this.className = \'toolbar-selected-item\';" onmouseout="this.className = \'toolbar-item\';">
				        						<img id="imgDelete" src="images/custom_menu/'.$lang.'/disable_delete.gif" onclick="deleteNode(this);" disabled="true"/>
											</div>
				        					<img src="images/custom_menu/'.$lang.'/delimiter.png" style="float:'.$aling.';margin-'.$notaling.':3px"/>
				        					<div class="toolbar-item" onmouseover="this.className = \'toolbar-selected-item\';" onmouseout="this.className = \'toolbar-item\';">
				        						<img id="imgUp" src="images/custom_menu/'.$lang.'/disable_moveup.gif" onclick="moveUpNode(this);" disabled="true"/>
											</div>
				        					<img src="images/custom_menu/'.$lang.'/delimiter.png" style="float:'.$aling.';margin-'.$notaling.':3px"/>
				        					<div class="toolbar-item" onmouseover="this.className = \'toolbar-selected-item\';" onmouseout="this.className = \'toolbar-item\';">
				        						<img id="imgDown" src="images/custom_menu/'.$lang.'/disable_movedn.gif" disabled="true" onclick="moveDownNode(this);"/>
											</div>
				        					<img src="images/custom_menu/'.$lang.'/delimiter.png" style="float:'.$aling.';margin-'.$notaling.':3px"/>
				        					<div class="toolbar-item" onmouseover="this.className = \'toolbar-selected-item\';" onmouseout="this.className = \'toolbar-item\';">
				        						<img id="imgRight" src="images/custom_menu/'.$lang.'/disable_moveright.gif" disabled="true" onclick="moveRightNode(this);"/>
											</div>
				        					<img src="images/custom_menu/'.$lang.'/delimiter.png" style="float:'.$aling.';margin-'.$notaling.':3px"/>
				        					<div class="toolbar-item" onmouseover="this.className = \'toolbar-selected-item\';" onmouseout="this.className = \'toolbar-item\';">
				        						<img id="imgLeft" src="images/custom_menu/'.$lang.'/disable_moveleft.gif" disabled="true" onclick="moveLeftNode(this);"/>
											</div>
				        				</div>
				        				<div id="__divMenuTree__" style="padding:0px 10px 0px 10px;height:300px;overflow:auto;width:178px;"></div>
										</div>
				        			</td>
									<td style="border:1px solid #a2c6ec;padding:10px 5px 10px 5px;width:100%" valign="top">
										<table style="width:100%;display:none;table-layout:fixed" id="tblContent">';
	    $langs = languagelist();
	    foreach ($langs as $key=>$value) {
	    	if ($key != 'x_all') {
	    		$output .= '<tr>
								<td style="width:25%">'._SERVICE_MENU_TITLE.' ('.$value.') </td>
								<td><input type="text" size="40" id="txtTitle_'.$key.'" onblur="serviceMenuSaveNode(sisMenuTree.selectedNode)"/></td>
						   </tr>';
	    	}
	    }
	   	$output .= '<tr>
						<td>'._SERVICE_MENU_ADDRESS.'</td>
						<td><input type="text" id="txtAddress" onblur="serviceMenuSaveNode(sisMenuTree.selectedNode)" style="width:95%;direction:ltr"/></td>
					</tr>';
		if(isset($blockinfo['ctp_id'])){
			$contentType = getContentType($blockinfo['ctp_id']);
		}
		if($contentType && $contentType['perm_type'] == 1){
			$output .= '<tr>
							<td>'._SERVICE_MENU_PERMISSION.'</td>
							<td>
								<select name="txtPermIdx" id="txtPermIdx" onblur="serviceMenuSaveNode(sisMenuTree.selectedNode)" onchange="if (this.value == \'-1\') $(\'#trAccessCode\').css(\'display\', \'\'); else $(\'#trAccessCode\').css(\'display\', \'none\');">
									<OPTION value=100 >'. _SERVICE_MENU_ACCESS_VIEW .'</OPTION>
									<OPTION value=300 >'. _SERVICE_MENU_ACCESS_COMMENT .'</OPTION>
									<OPTION value=400 >'. _SERVICE_MENU_ACCESS_ADD .'</OPTION>
									<OPTION value=500 >'. _SERVICE_MENU_ACCESS_EDIT_OWNER .'</OPTION>
									<OPTION value=600 >'. _SERVICE_MENU_ACCESS_EDIT .'</OPTION>
									<OPTION value=700 >'. _SERVICE_MENU_ACCESS_DELETE .'</OPTION>
									<OPTION value=800 >'. _SERVICE_MENU_ACCESS_ADMIN .'</OPTION>
									<OPTION value=900 >'. _SERVICE_MENU_ACCESS_CONFIG .'</OPTION>
									<OPTION value="-1" >'. _SERVICE_MENU_ACCESS_CODE_BASED .'</OPTION>
								</select>
								<input type="hidden" id="txtPerm" name="txtPerm" value="">
							</td>
						</tr>';
		}
	
		if($contentType && $contentType['perm_type'] == 2){
			$output .= '<tr>
							<td>'._SERVICE_MENU_OBJECT_ID.':</td>
							<td>
								<!--<span name="txtObjectId" id="txtObjectId"></span>-->
								<input type="text" id="txtObjectId" name="txtObjectId" onblur="serviceMenuSaveNode(sisMenuTree.selectedNode)" style="width:95%;direction:ltr"/>
							</td>
						</tr>';
		}
		if($contentType && $contentType['perm_type'] == 1){
			$output .= '<tr id="trAccessCode" style="display:none">
							<td>'._SERVICE_MENU_ACCESS_CODE.'</td>
							<td><input type="text" id="txtAccessCode" onblur="serviceMenuSaveNode(sisMenuTree.selectedNode)" style="width:95%;direction:ltr;"/></td>
						</tr>';
		}
	
		$output .='
				</table>
				<div>&nbsp;</div>
			</td>
		</tr>
	</table>
	<input type="hidden" id="txtDom" name="menuItems" value="'.$vars['menuItems'].'">
	</td></tr>
	    <script>
	    var contentTypePerm='.$contentType['perm_type'].';
		function init() {
			sisMenuTree.init("__divMenuTree__");
			sisMenuTree.align = "'.$aling.'";
			sisMenuTree.baseImagePath = "images/tree/'.$lang.'/";
			sisMenuTree.addAttribute("title", "'._SERVICE_MENU_DEFUALTTITLE.'");';

	    foreach ($langs as $key=>$value) {
	    	if ($key != 'x_all') {
	    		$output .= 'sisMenuTree.addAttribute("title_'.$key.'", "'._SERVICE_MENU_DEFUALTTITLE.'");';
	    	}
	    }
		$output .= '
			sisMenuTree.addAttribute("url", "");
			sisMenuTree.addAttribute("img", "");
			if( contentTypePerm == 1 ){
				sisMenuTree.addAttribute("perm", "");
				sisMenuTree.addAttribute("accessCode", "");
			}
			sisMenuTree.addAttribute("permIdx", "");
			if(  contentTypePerm ==2 ){
				sisMenuTree.addAttribute("objectId", "");
			}

			var tree = '.$vars['menuItems'].';
			for (idx=0; idx<tree["items"].length; idx++) {
				generateTree(tree["items"][idx], null);
			}
			sisMenuTree.onNodeSelected = selectNode;
			document.getElementById(\'txtDom\').value = sisMenuTree.getData();
		}

		function generateTree(item, parentNode) {';
	   	$titles = ' title="\' + sisMenuTree.controlContentDecode(item[\'title\']) + \'" ';
	    foreach ($langs as $key=>$value) {
	    	if ($key != 'x_all') {
	    		$titles .= ' title_'.$key.'="\' + sisMenuTree.controlContentDecode(item[\'title_'.$key.'\']) + \'" ';
	    	}
	    }
		$output .= 'var node = sisMenuTree.createNode(sisMenuTree.controlContentDecode(item[\'title_'.pnUserGetLang().'\']?item[\'title_'.pnUserGetLang().'\']:item[\'title\']), \'images/custom_menu/'.$lang.'/applicationicon.png\', \' '.$titles.' url="\' + sisMenuTree.controlContentDecode(item[\'url\']) + \'" img="\' + sisMenuTree.controlContentDecode(item[\'img\']) + \'"  objectId="\' + sisMenuTree.controlContentDecode(item[\'objectId\']) + \'"  perm="\' + sisMenuTree.controlContentDecode(item[\'perm\']) + \'" permIdx="\' + sisMenuTree.controlContentDecode(item[\'permIdx\']) + \'" accessCode="\' + sisMenuTree.controlContentDecode(item[\'accessCode\']) + \'" \');
			sisMenuTree.addNode(node, "append", parentNode);
			if (typeof(item[\'subItems\']) != \'undefined\' && item[\'subItems\'].length > 0) {
				var idx = 0;
				for(idx=0; idx<item[\'subItems\'].length; idx++)
					generateTree(item[\'subItems\'][idx], node);
			}
		}
		function addNode() {
			var node = sisMenuTree.createNode(\''._SERVICE_MENU_DEFUALTTITLE.'\', \'images/custom_menu/'.$lang.'/applicationicon.png\');
			if (sisMenuTree.selectedNode != null) {
				sisMenuTree.addNode(node, "append", document.getElementById(sisMenuTree.selectedNode.getAttribute("containeritem")));
			}
			else {
				sisMenuTree.addNode(node);
			}
			sisMenuTree.selectNode(node.rows[0].childNodes[1].childNodes[1], null);
			serviceMenuSaveNode(node.rows[0].childNodes[1].childNodes[1]);
		}

		function deleteNode(imageObj) {
			if (imageObj.getAttribute(\'disabled\') == true)
				return;
			var conatinerItem = document.getElementById(sisMenuTree.selectedNode.getAttribute("containeritem"));
			if (conatinerItem.rows.length > 1) {
				if (!confirm("Do you want delete this node and it\'s child(s)"))
					return;
			}
			else
				if (!confirm("Do you want delete this node"))
					return;
			sisMenuTree.deleteNode(conatinerItem);
			document.getElementById(\'tblContent\').style.display = \'none\';
			sisMenuTree.selectedNode = null;
			selectNode(null);
		}

		function moveUpNode(imageObj) {
			if (imageObj.getAttribute(\'disabled\') == "true")
				return;
			var conatinerItem = document.getElementById(sisMenuTree.selectedNode.getAttribute("containeritem"));
			sisMenuTree.swapNode(conatinerItem, conatinerItem.previousSibling);
			selectNode(sisMenuTree.selectedNode);
		}

		function moveDownNode(imageObj) {
			if (imageObj.getAttribute(\'disabled\') == "true" || imageObj.getAttribute(\'disabled\') == true)
				return;
			var conatinerItem = document.getElementById(sisMenuTree.selectedNode.getAttribute("containeritem"));
			sisMenuTree.swapNode(conatinerItem.nextSibling, conatinerItem);
			selectNode(sisMenuTree.selectedNode);
		}
		function moveRightNode(imageObj) {
			if (imageObj.getAttribute(\'disabled\') == "true" || imageObj.getAttribute(\'disabled\') == true)
				return;
			var conatinerItem = document.getElementById(sisMenuTree.selectedNode.getAttribute("containeritem"));
			sisMenuTree.indentNode(conatinerItem, -1);
			selectNode(sisMenuTree.selectedNode);
		}
		function moveLeftNode(imageObj) {
			if (imageObj.getAttribute(\'disabled\') == "true" || imageObj.getAttribute(\'disabled\') == true)
				return;
			var conatinerItem = document.getElementById(sisMenuTree.selectedNode.getAttribute("containeritem"));
			sisMenuTree.indentNode(conatinerItem, 1);
			selectNode(sisMenuTree.selectedNode);
		}
		var lastNode = null;
		function selectNode(node) {
			if (lastNode != null)
				serviceMenuSaveNode(lastNode);
			lastNode = node;
			if (node == null) {
				document.getElementById(\'imgAdd\').src = "images/custom_menu/'.$lang.'/add.png";
				document.getElementById(\'imgAdd\').setAttribute(\'disabled\', false);
				document.getElementById(\'imgDelete\').src = "images/custom_menu/'.$lang.'/disable_delete.gif";
				document.getElementById(\'imgDelete\').setAttribute(\'disabled\', true);
				document.getElementById(\'imgUp\').src = "images/custom_menu/'.$lang.'/disable_moveup.gif";
				document.getElementById(\'imgUp\').setAttribute(\'disabled\', true);
				document.getElementById(\'imgLeft\').src = "images/custom_menu/'.$lang.'/disable_moveleft.gif";
				document.getElementById(\'imgLeft\').setAttribute(\'disabled\', true);
				document.getElementById(\'imgDown\').src = "images/custom_menu/'.$lang.'/disable_movedn.gif";
				document.getElementById(\'imgDown\').setAttribute(\'disabled\', true);
				document.getElementById(\'imgRight\').src = "images/custom_menu/'.$lang.'/disable_moveright.gif";
				document.getElementById(\'imgRight\').setAttribute(\'disabled\', true);

				document.getElementById(\'tblContent\').style.display = \'none\';
				return;
			}
			document.getElementById(\'tblContent\').style.display = \'\';
			document.getElementById(\'imgDelete\').src = "images/custom_menu/'.$lang.'/delete.png";
			document.getElementById(\'imgDelete\').setAttribute(\'disabled\', false);

			var conatinerItem = document.getElementById(node.getAttribute("containeritem"));

			if (conatinerItem.getAttribute("parentItem") != \'\') {
				document.getElementById(\'imgAdd\').src = "images/custom_menu/'.$lang.'/disable_add.png";
				document.getElementById(\'imgAdd\').setAttribute(\'disabled\', true);
			}
			else {
				document.getElementById(\'imgAdd\').src = "images/custom_menu/'.$lang.'/add.png";
				document.getElementById(\'imgAdd\').setAttribute(\'disabled\', false);
			}
			if (conatinerItem.previousSibling == null) {
				document.getElementById(\'imgUp\').src = "images/custom_menu/'.$lang.'/disable_moveup.gif";
				document.getElementById(\'imgUp\').setAttribute(\'disabled\', true);
				document.getElementById(\'imgLeft\').src = "images/custom_menu/'.$lang.'/disable_moveleft.gif";
				document.getElementById(\'imgLeft\').setAttribute(\'disabled\', true);
			}
			else {
				document.getElementById(\'imgUp\').src = "images/custom_menu/'.$lang.'/moveup.gif";
				document.getElementById(\'imgUp\').setAttribute(\'disabled\', false);
				document.getElementById(\'imgLeft\').src = "images/custom_menu/'.$lang.'/moveleft.gif";
				document.getElementById(\'imgLeft\').setAttribute(\'disabled\', false);
			}
			if (conatinerItem.nextSibling == null) {
				document.getElementById(\'imgDown\').src = "images/custom_menu/'.$lang.'/disable_movedn.gif";
				document.getElementById(\'imgDown\').setAttribute(\'disabled\', true);
			}
			else {
				document.getElementById(\'imgDown\').src = "images/custom_menu/'.$lang.'/movedn.gif";
				document.getElementById(\'imgDown\').setAttribute(\'disabled\', false);
			}
			if (conatinerItem.getAttribute("parentItem") == \'\') {
				document.getElementById(\'imgRight\').src = "images/custom_menu/'.$lang.'/disable_moveright.gif";
				document.getElementById(\'imgRight\').setAttribute(\'disabled\', true);
			}
			else {
				document.getElementById(\'imgRight\').src = "images/custom_menu/'.$lang.'/moveright.gif";
				document.getElementById(\'imgRight\').setAttribute(\'disabled\', false);
			}';
	    foreach ($langs as $key=>$value) {
	    	if ($key != 'x_all') {
	    		$output .= 'if (node.getAttribute(\'title_'.$key.'\'))
	    						document.getElementById(\'txtTitle_'.$key.'\').value = node.getAttribute(\'title_'.$key.'\');
	    					else
	    						document.getElementById(\'txtTitle_'.$key.'\').value = node.getAttribute(\'title\');';
	    	}
	    }
	$output .= 'document.getElementById(\'txtAddress\').value = node.getAttribute(\'url\');
				if( contentTypePerm == 1 ){
					document.getElementById(\'txtPerm\').value = node.getAttribute(\'perm\');
					document.getElementById(\'txtPermIdx\').selectedIndex = node.getAttribute(\'permIdx\');
					document.getElementById(\'txtAccessCode\').value = node.getAttribute(\'accessCode\');
				}
				if( contentTypePerm == 2 ){
					document.getElementById(\'txtObjectId\').value = node.getAttribute(\'objectId\');
				}
				if( contentTypePerm == 1 )
					if (node.getAttribute(\'perm\') == \'-1\')
						$(\'#trAccessCode\').css(\'display\', \'\');
					else
						$(\'#trAccessCode\').css(\'display\', \'none\');
			}
			function serviceMenuSaveNode(node) {';

		foreach ($langs as $key=>$value) {
	    	if ($key != 'x_all') {
	    		$output .= 'node.setAttribute(\'title_'.$key.'\', document.getElementById(\'txtTitle_'.$key.'\').value.replace(\'<\', \'\').replace(\'>\', \'\'));';
	    	}
	    }
	$output .= 'node.innerHTML = document.getElementById(\'txtTitle_'.pnUserGetLang().'\').value.replace(\'<\', \'\').replace(\'>\', \'\');
				node.innerHTML = document.getElementById(\'txtTitle_'.pnUserGetLang().'\').value.replace(\'<\', \'\').replace(\'>\', \'\');
				node.setAttribute(\'url\', document.getElementById(\'txtAddress\').value.replace(\'<\', \'\').replace(\'>\', \'\'));
				if( contentTypePerm == 1 ){
					node.setAttribute(\'perm\', document.getElementById(\'txtPermIdx\').options[document.getElementById(\'txtPermIdx\').selectedIndex].value);
					node.setAttribute(\'permIdx\', document.getElementById(\'txtPermIdx\').selectedIndex);
					node.setAttribute(\'accessCode\', document.getElementById(\'txtAccessCode\').value);
				}
				if( contentTypePerm == 2 ){
					
					node.setAttribute(\'objectId\', document.getElementById(\'txtObjectId\').value);
				}
				document.getElementById(\'txtDom\').value = sisMenuTree.getData();
			}
			init();
			</script>';
	}
   	return $output;
}

function cdk_service_menublock_update($blockinfo){
	if(pnBlockIsObjectSetting($blockinfo['block_id'],'service_menu','menuHeader')) {
		$vars['menuHeader'] = pnVarCleanFromInput('menuHeader');
		$vars['menuFooter'] = pnVarCleanFromInput('menuFooter');
	}
	if(pnBlockIsObjectSetting($blockinfo['block_id'],'service_menu','loadType'))
		$vars['loadType'] = pnVarCleanFromInput('loadType');
	if(pnBlockIsObjectSetting($blockinfo['block_id'],'service_menu','contentType'))
		$vars['contentType'] = pnVarCleanFromInput('contentType');
	if(pnBlockIsObjectSetting($blockinfo['block_id'],'service_menu','menuItems')){
		$vars['menuItems'] = pnVarCleanFromInput('menuItems');
	}
	if(pnVarCleanFromInput('ctp_id')){
		$vars['ctp_id'] = pnVarCleanFromInput('ctp_id');
	}
		
	$vars['menuItems'] = pnVarCleanFromInput('menuItems');
	$blockinfo['content'] = pnBlockVarsToContent($vars);

	return $blockinfo;
}

function serviceMenuContentDecode($content) {
	$content = str_replace(array('!ad!', '!e!', '!s!', '!d!', '!sl!'), array('&', '=', '\'', '"', '\\'), $content);
	if (ereg('#', $content)) {
		$tmpStr = split('#', $content);
		$replaces = array();
		for ($i=1; $i<count($tmpStr); $i+=2) {
			$var = split(':', $tmpStr[$i]);
			if (strtolower($var[0]) == 's')
				$replaces["#$tmpStr[$i]#"] = $_SESSION($var[1]);
			else if (strtolower($var[0]) == 'p')
				$replaces["#$tmpStr[$i]#"] = $_REQUEST[$var[1]];
			else if (strtolower($var[0]) == 'g')
				$replaces["#$tmpStr[$i]#"] = $GLOBALS[$var[1]];
			else if (strtolower($var[0]) == 'i') {
				$replaces["#$tmpStr[$i]#"] = getImportId("$var[1]:$var[2]");
			}
			else
				$replaces["#$tmpStr[$i]#"] = $qoute.$qoute;
		}
		$content = str_replace(array_keys($replaces), array_values($replaces), $content);
	}
	return $content;
}
?>