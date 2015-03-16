<?php
// Saman Portal
// Copyright (C) 2009 by the Saman Information Structure Development Team.
// http://www.sis-eg.com/
// ----------------------------------------------------------------------

function cdk_menu_contentsblockblock_init(){
	pnSecAddSchema('cdk::menu_contentssblock:', 'Block title::');
}

function cdk_menu_contentsblockblock_info(){
    return array('text_type' => 'menu_contentsblockblock',
                 'module' => 'cdk',
                 'text_type_long' => _CDK_MENUCONTENT_BLOCK,
                 'allow_multiple' => true,
                 'form_content' => false,
                 'form_refresh' => false,
                 'show_preview' => false,
				 'default_title' => _CDK_MENUCONTENT_BLOCK,
				 'allow_user_add'  => 0,
				 'group' => 2,
				 'block_description' => _CDK_MENUCONTENT_BLOCK_DESCRIPTION,
			     'allow_subportal_add'  => 1				 
                 );
}

function cdk_menu_contentsblockblock_display($blockinfo){
    if (!pnSecAuthAction(0, 'cdk::menu_subjectsblock', "$blockinfo[title]::", ACCESS_READ)) 
    	return;
    	
    $vars = pnBlockVarsFromContent($blockinfo['content']);        

	switch ($vars['menutype']) {
		case 1:
		case 2:	
			$ctp_id = $vars['contentType'];
			if ($ctp_id == '-1')
				$ctp_id = pnVarCleanFromInput('ctp_id');	
		    $content = pnModFunc('cdk','user','treeMenu', array('menutype'		=> $vars['menutype'],
		    														'contentType'			=> $ctp_id,
		    														'contentTypeTemplate'	=> $vars['contentTypeTemplate'],
		    														'catid'					=> $vars['catid'],
																	'maxlength'				=> $vars['maxlength'],
																	'defaultexpand' 		=> $vars['defaultexpand'],
																	'showcontents'  		=> $vars['showcontents'],
																	'showsubpages'  		=> $vars['showsubpages'],
																	'linkdirectory'  		=> $vars['linkdirectory'],
																	'showitemscount'  		=> $vars['showitemscount'],
																	'showhint'  			=> $vars['showhint'],
																	'hintwidth'  			=> $vars['hintwidth'],
																	'hintheight'  			=> $vars['hintheight'],
																	'selectingQuery'		=> $vars['selectingQuery']));
			$blockinfo['content'] = $content;
		    $blockinfo = cdk_treeMenu($blockinfo,$vars);
			break;
		default:
			break;
	}
    return themesideblock($blockinfo);	
}

function cdk_menu_contentsblockblock_modify($blockinfo){
    $vars = pnBlockVarsFromContent($blockinfo['content']);
    if (empty($vars['hintheight'])) 
    	$vars['hintheight'] = 200;
    if (empty($vars['hintwidth'])) 
    	$vars['hintwidth'] = 200;
	if ($vars['menutype'])
		$selectedMenuType[$vars['menutype']] = 'selected';

    $types = pnModAPIFunc('cdk', 'user', 'getAccessibleTypes');
    $typesOptions .= "<option value='-1'>"._CNT_BLK_CONENT_TYPE_AUTO_SELECT."</option>
    				  <optgroup label='"._CNT_BLK_CONENT_TYPE_EXISTING_TYPES."'>";    
	foreach ($types as $key => $value) {
		$selected = '';
		if ($value['ctp_id'] == $vars['contentType'])
			$selected = 'selected';
		$typesOptions .= "<option value='$value[ctp_id]' $selected >$value[title]</option>";
	}
	$typesOptions .= "</optgroup>";	
	$allTemplates = pnModAPIFunc('cdk', 'user', 'getTypeTemplates');
	$content = "<script>
				var contentTypeTemplates = new Array();
				var contentTypeFields = new Array();				
				var selectedTemplate = '$vars[contentTypeTemplate]';
				var selectedField = '$vars[contentTypeField]';				
				function initContentTypeTemplateCombo() {
					cmbContentType = document.getElementById('cmbContentType');
					cmbContentTypeTemplate = document.getElementById('cmbContentTypeTemplate');
					cmbContentTypeTemplate.innerHTML = '';
					cmbContentTypeTemplate.appendChild(document.createElement('option'));
					if (contentTypeTemplates[cmbContentType.value] != null) 
						for(var idx = 0; idx < contentTypeTemplates[cmbContentType.value].length; idx++) {
							var opt = document.createElement('option');
							opt.value = contentTypeTemplates[cmbContentType.value][idx].name;
							opt.setAttribute('typeId', contentTypeTemplates[cmbContentType.value][idx].typeId);
							if (selectedTemplate == opt.value)
								opt.selected = true;
							opt.innerText = contentTypeTemplates[cmbContentType.value][idx].name;
							cmbContentTypeTemplate.appendChild(opt);
						}										
				   selectedTemplate	= '';
				}
				
				function initContentTypeFieldCombo() {
					cmbContentType = document.getElementById('cmbContentType');
					cmbContentTypeField2 = document.getElementById('cmbContentTypeField2');
					cmbContentTypeField2.innerHTML = '';
															
					if (contentTypeFields[cmbContentType.value] != null) 
						for(var idx = 0; idx < contentTypeFields[cmbContentType.value].length; idx++) {
							opt2 = document.createElement('option');
							opt2.value = contentTypeFields[cmbContentType.value][idx].name;
							opt2.innerText = contentTypeFields[cmbContentType.value][idx].caption;
							cmbContentTypeField2.appendChild(opt2);
						}
				}
				
												
				function changeTemplateImage(obj) {
					var item = obj.options(obj.selectedIndex);
					var imgTemplate = document.getElementById('imgTemplate');
					if (item.value == '')
						imgTemplate.src = 'services/cdk/images/noimage.png';
					else
						imgTemplate.src = 'index.php?module=cdk&func=loadmodule&system=cdk&sismodule=user/get_template_image.php&ctp_id=' + item.getAttribute('typeId') + '&template=' + item.value;
				}";
	foreach ($allTemplates as $key => $templates) {
		$strTemplates = '';
		$lastTemplate = '';
		foreach ($templates as $id => $template) {
			if ($lastTemplate != $template) {
				$strTemplates .= "{'id':$id, 'name':'$template', 'typeId':$key},";
			}
			$lastTemplate = $template;
		}
		if ($strTemplates > '') {
			$strTemplates = substr($strTemplates, 0, strlen($strTemplates) - 1);		
			$content .= "contentTypeTemplates[$key] = [$strTemplates];";
		}
	}
	foreach ($types as $contentType) {
		$strTemplates = '';
		foreach ($contentType['type_fields'] as $field) {
			if ($field['fieldType'] != 'image' && $field['fieldType'] != 'file' && $field['fieldType'] != 'text') {
				$caption = $field['title_'.pnUserGetLang()];
				$strTemplates .= "{'name':'$field[fieldName]', 'caption':'$caption'},";						
			}
		}
		if ($strTemplates > '') {
			$strTemplates = substr($strTemplates, 0, strlen($strTemplates) - 1);		
			$content .= "contentTypeFields[$contentType[ctp_id]] = [$strTemplates];";
		}				
	}
	$content .= "contentTypeFields[-1] = [{'name':'counter', 'caption':'"._CNT_BLK_CONENT_TYPE_FIELD_COUNTER."'},".
										 "{'name':'display_start_date', 'caption':'"._CNT_BLK_CONENT_TYPE_FIELD_DISPLAY_START_DATE."'},".
										 "{'name':'last_modified_date', 'caption':'"._CNT_BLK_CONENT_TYPE_FIELD_LAST_MODIFIED_DATE."'},".
										 "{'name':'page_title', 'caption':'"._CNT_BLK_CONENT_TYPE_FIELD_PAGE_TITLE."'},".
										 "{'name':'rate', 'caption':'"._CNT_BLK_CONENT_TYPE_FIELD_RATE."'}];";
	$content .= "</script>";	
		
	pnModLoad('content','admin');
	if(empty($vars['contentType']))
		$vars['contentType']='';
	$selectedContentType[$vars['contentType']] = 'selected';
	$types = pnModAPIFunc('cdk', 'user', 'getAccessibleTypes');
	if ($vars['defaultexpand'])
		$default_expand_selected = 'checked';
	if ($vars['showcontents'])
		$showpages_selected = 'checked';
	if ($vars['showsubpages'])
		$showsubpages_selected = 'checked';
	if ($vars['linkdirectory'])
		$linkdirectory_selected = 'checked';
	if ($vars['showitemscount'])
		$showitemscount_selected = 'checked';
	if ($vars['showhint'])
		$showhint_selected = 'checked';
	$content .= "
			<tr><td colspan=\"2\"><br></td></tr>
			<tr>
				<td class='caption'>"
				. _CNT_BLK_MENU_TYPE ." :
				</td>
				<td>
					<select name='contentMenuType' >";
		if(strtolower(pnVarCleanFromInput('module'))!='my' && strtolower(pnVarCleanFromInput('name'))!='my')
			$content .= "<option value=1 ".$selectedMenuType[1].">"._CNT_BLK_DYNAMIC_TREE."</option>";
		$content .= "<option value=2 ".$selectedMenuType[2].">"._CNT_BLK_STATIC_TREE."</option>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan=1 class='caption'>"
				. _CNT_BLK_CONENT_TYPE ." :
				</td>
				<td colspan=1>
				<select name='contentType' id='cmbContentType' onchange='initContentTypeTemplateCombo(); initContentTypeFieldCombo();'>"
					. $typesOptions . "
				</select>
			   </td>
		   </tr>
		   <tr>
				<td class='caption'  nowrap='nowrap'>"
					. _CNT_BLK_CONENT_TYPE_TEMPLATE . ":
				</td>
				<td>
					<select name='contentTypeTemplate' id='cmbContentTypeTemplate' style='width:120px' onchange='return changeTemplateImage(this);'>
					</select>
					<script>
						initContentTypeTemplateCombo();
					</script>
				</td>
			</tr>
			<tr>
				<td>
				</td>
				<td>
					<img id='imgTemplate' src='services/cdk/images/noimage.png' style='padding:3px;background-color:#fff;border:1px solid #aaa'/>
				</td>				
			</tr>		
		    <tr>
		    	<td class='caption'>"
				. _CNT_BLK_CATEGORY_ID ." :
				</td>
				<td>
					<input type=\"text\" name=\"contentCatId\" size=\"3\" 
						value=\"".$vars['catid']."\" class=\"sp-normal\"> 
				</td>
			</tr>
			<tr>
				<td class='caption'>"
				. _CNT_BLK_MAX_LENGTH . " : 
				</td>
				<td>
					<input type=\"text\" name=\"maxLength\" size=\"3\" maxlength=\"255\" 
						value=\"".$vars['maxlength']."\"class=\"sp-normal\"> "._CNT_BLK_CHARACTER." 
				</td>
			</tr>
		    <tr>
				<td>
				</td>
				<td>
					<input type=\"checkbox\" name=\"defaultExpand\" 
						value=\"1\" $default_expand_selected> "._CNT_BLK_DEFAULT_TREE_EXPAND."
					<span class='itemdescription'> ("._CNT_BLK_DEFAULT_EXPAND_DESC.")</span>
				</td>
			</tr>
			<tr>
				<td>
				</td>
				<td>
					<input type=\"checkbox\" name=\"showContents\" 
						value=\"1\" $showpages_selected> "._CNT_BLK_SHOWPAGES."
				</td>
			</tr>
			<tr>
				<td>
				</td>
				<td>
					<input type=\"checkbox\" name=\"linkDirectory\" 
						value=\"1\" $linkdirectory_selected> "._CNT_BLK_LINK_WEB_DIRECTORY."
				</td>
			</tr>
			<tr>
				<td>
				</td>
				<td>
					<input type=\"checkbox\" name=\"showItemsCount\" 
						value=\"1\" $showitemscount_selected> "._CNT_BLK_SHOW_ITEMS_COUNT."
				</td>
			</tr>
			<tr>
				<td>
				</td>
				<td>
					<input type=\"checkbox\" name=\"showHint\" 
						value=\"1\" $showhint_selected> "._CNT_BLK_SHOWTHUMBNAIL."
			 	</td>
			</tr>
			<tr>
				<td class='caption'>"
				. _CNT_BLK_HINTWIDTH . " : 
				</td>
				<td>
					<input type=\"text\" name=\"hintWidth\" size=\"3\" maxlength=\"4\" 
						value=\"".$vars['hintwidth']."\" class=\"sp-normal\"> "._CNT_BLK_PIXEL."
				</td>
			</tr>		
			<tr>
				<td class='caption'>"
				. _CNT_BLK_HINTHEIGHT . " : 
				</td>
				<td>
					<input type=\"text\" name=\"hintHeight\" size=\"3\" maxlength=\"4\" 
						value=\"".$vars['hintheight']."\" class=\"sp-normal\"> "._CNT_BLK_PIXEL."
				</td>
			</tr>	
			<tr>
				<td class='caption'  nowrap='nowrap' style='vertical-align:top' valign='top'>"
					. _CNT_BLK_CONENT_TYPE_WHERE_CLAUSE . ":
				</td>
				<td>
					<textarea name='selectingQuery' rows='5' cols='50' style='direction:ltr'>$vars[selectingQuery]</textarea>
				</td>
			</tr>				
			<tr>
				<td class='caption'  nowrap='nowrap' style='vertical-align:top' valign='top'>&nbsp;
				</td>
				<td>
					<select id='cmbContentTypeField2' style='width:120px'>
					</select>
					<select id='cmbContentTypeOperation'>
						<option value='='>=</option>
						<option value='>'>&gt;</option>
						<option value='<'>&lt;</option>
						<option value='>='>=&gt;</option>
						<option value='<='>=&lt;</option>
						<option value='<>'>&lt;&gt;</option>
						<option value='LIKE'>Like</option>
						<option value='NOT LIKE'>Not Like</option>
					</select>
					<input type='text' size=10 id='txtContentTypeFilterValue' />
					<input type='button' value='"._CNT_BLK_CONENT_TYPE_COPY_TO_CLIPBOARD."' onclick='contentTypeCopyClipboard()'/>
					<script>
						initContentTypeFieldCombo();
						
						function contentTypeCopyClipboard() {
							var str = '';
							str = document.getElementById('cmbContentTypeField2').value + ' ';
							str += document.getElementById('cmbContentTypeOperation').value + ' ';
							if (document.getElementById('cmbContentTypeOperation').value != 'LIKE' && 
								document.getElementById('cmbContentTypeOperation').value != 'NOT LIKE') 
								str += '\'' + document.getElementById('txtContentTypeFilterValue').value + '\'';
							else
								str +=  '\'%' + document.getElementById('txtContentTypeFilterValue').value + '%\'';
															
							window.clipboardData.setData('Text', str);	
						}
					</script>												
				</td>
			</tr>			
			<tr><td colspan=\"2\"><br></td></tr>";
    $output = new pnHTML();
	$output->SetInputMode(_PNH_VERBATIMINPUT);
	$output->Text($content);
    return $output->GetOutput();
}

function cdk_menu_contentsblockblock_update($blockinfo){
    $vars['menutype'] = pnVarCleanFromInput('contentMenuType');    
    $vars['contentType'] = pnVarCleanFromInput('contentType');
    $vars['contentTypeTemplate'] = pnVarCleanFromInput('contentTypeTemplate');        
    $vars['catid'] = pnVarCleanFromInput('contentCatId');    
    $vars['maxlength'] = pnVarCleanFromInput('maxLength');		
	$vars['defaultexpand'] = pnVarCleanFromInput('defaultExpand');
	$vars['showcontents'] = pnVarCleanFromInput('showContents');
	$vars['showsubpages'] = pnVarCleanFromInput('showsubpages');
	$vars['linkdirectory'] = pnVarCleanFromInput('linkDirectory');
	$vars['showitemscount'] = pnVarCleanFromInput('showItemsCount');
	$vars['showhint'] = pnVarCleanFromInput('showHint');
	$vars['hintwidth'] = pnVarCleanFromInput('hintWidth');
	$vars['hintheight'] = pnVarCleanFromInput('hintHeight');
	$vars['selectingQuery'] = pnVarCleanFromInput('selectingQuery');
	$blockinfo['content'] = pnBlockVarsToContent($vars);
	return $blockinfo;
}

function cdk_treeMenu($row,$vars=array()){
	$GLOBALS['dm_not_render_index'] = array('r','c'); 
	$GLOBALS['dm_render_active_only'] = 1;
	$GLOBALS['dm_level_colors'] = array('#FFFF33',    // Commented out menu item
	                                    '#aaccee','#EBF3FB','#F3F7FB','#CCCCCC','#EEEEEE',   // Levels 1-5
	                                    '#660000','#880000','#AA0000','#CC0000','#FF0000',   // Levels 6-10
																			'#006600','#008800','#00AA00','#00CC00','#00FF00');  // Levels 11-15
	$GLOBALS['dm_image_dir'] = 'api/portlets/dynamenu/images/'.pnUserGetLang().'/';
	$GLOBALS['dm_icon_dir'] = 'api/portlets/dynamenu/icons/';
	$GLOBALS['dm_layermenu_offset'] = array (0,0,0);
	$GLOBALS['dm_tree_titleclick'] = 0;
	$GLOBALS['dm_include_seethroughjs'] = 0;
	$GLOBALS['dm_highlight_treelink'] = 1;

	$GLOBALS['dm_tree_folder_icons'] = 0;				

	$content = explode('~~', $row['content']);
	$row['content'] = '';
	$bid = $row['bid'];
	if (empty($content[3])) {
        $row['content'] = _DM_NOMENUDEFINED;
        return $row;	
    }
        echo '<script language="JavaScript" type="text/javascript">';
        include ("api/portlets/dynamenu/libjs/layersmenu-browser_detection.js");
        echo '</script>';
        echo '<script language="JavaScript" type="text/javascript" src="api/portlets/dynamenu/libjs/layerstreemenu-cookies.js"></script>';

        if (!$GLOBALS['menuflag']) {
			$GLOBALS['menuflag'] = 1;	
			echo '<script language="JavaScript" type="text/javascript" src="api/portlets/dynamenu/libjs/layersmenu.js"></script>';
	        echo '<script language="JavaScript" type="text/javascript" src="api/portlets/dynamenu/libjs/layersmenu-library.js"></script>';
			include ("api/portlets/dynamenu/lib/PHPLIB.php");
	        include ("api/portlets/dynamenu/lib/layersmenu-common.inc.php");
	        include ("api/portlets/dynamenu/lib/layersmenu.inc.php");
	        include ("api/portlets/dynamenu/lib/treemenu.inc.php");
	        include ("api/portlets/dynamenu/lib/plainmenu.inc.php");		
        }	
		$GLOBALS[mid.$bid] = new LayersMenu($GLOBALS['dm_layermenu_offset'][0],$GLOBALS['dm_layermenu_offset'][1],$GLOBALS['dm_layermenu_offset'][2],1);
	    $GLOBALS[mid.$bid]->setLibjsdir("api/portlets/dynamenu/libjs/");
        $GLOBALS[mid.$bid]->setTpldir("api/portlets/dynamenu/templates/");	
        $GLOBALS[mid.$bid]->setImgdir($GLOBALS['dm_image_dir']);
        $GLOBALS[mid.$bid]->setImgwww($GLOBALS['dm_image_dir']);
        $GLOBALS[mid.$bid]->setIcondir($GLOBALS['dm_icon_dir']);
        $GLOBALS[mid.$bid]->setIconwww($GLOBALS['dm_icon_dir']);				
        $GLOBALS[mid.$bid]->setDownArrowImg("down-arrow.png");
        $GLOBALS[mid.$bid]->setForwardArrowImg("forward-arrow.png");	
        $GLOBALS[mid.$bid]->setVerticalMenuTpl("layersmenu-vertical_menu.ihtml");				
        $GLOBALS[mid.$bid]->setHorizontalMenuTpl("layersmenu-horizontal_menu.ihtml");		
	 			
        $GLOBALS[treemid.$bid] = new TreeMenu();
	    $GLOBALS[treemid.$bid]->options = $vars;	            
	    $GLOBALS[treemid.$bid]->setLibjsdir("api/portlets/dynamenu/libjs/");
        $GLOBALS[treemid.$bid]->setImgdir($GLOBALS['dm_image_dir']);
        $GLOBALS[treemid.$bid]->setImgwww($GLOBALS['dm_image_dir']);
        $GLOBALS[treemid.$bid]->setIcondir($GLOBALS['dm_icon_dir']);
        $GLOBALS[treemid.$bid]->setIconwww($GLOBALS['dm_icon_dir']);	

        $GLOBALS[plmid.$bid] = new PlainMenu();	
        $GLOBALS[plmid.$bid]->setTpldir("api/portlets/dynamenu/templates/");
				
		list($dbconn) = pnDBGetConn();
        $pntable = pnDBGetTables();
        $blockcol = &$pntable['blocks_column'];
		$current_lang = pnUserGetLang();
	
	    if ($GLOBALS['dm_render_active_only'] == '1')
		    $render_active = " AND $blockcol[active] = '1'";
						
		if (!$GLOBALS['index'] && isset($GLOBALS['dm_not_render_index'])) {
		    while (list($key,$zone) = each($GLOBALS['dm_not_render_index'])) {
					      $zones[] = "$blockcol[position] != '$zone'";					
			  }
		  $render_index = ' AND ('.implode(' AND ', $zones).') ';
		}
	    $query = "SELECT $blockcol[bid], $blockcol[title], $blockcol[content] FROM $pntable[blocks]
		          WHERE $blockcol[bkey] = 'menu_subjectsblock' AND ($blockcol[content] LIKE '0%' OR $blockcol[content] LIKE '3%')
  				  AND ($blockcol[language] = '' OR $blockcol[language] = '$current_lang') $render_active $render_index";
        $result = $dbconn->Execute($query);	
	
		while (list($block_bid,$block_title,$block_content) = $result->fields) {
            $result->MoveNext();
		    $count = 1;
			$this_content = explode('~~', $block_content);
			$this_content[3] = blocks_menu_contentsblockblock_preprocess($this_content[3], $block_title);
			if (!$this_content[3])
			    continue;								
            $GLOBALS[mid.$bid]->setMenuStructureString($this_content[3]);	
		    if ($this_content[0] == '0') {
                $GLOBALS[mid.$bid]->setSubMenuTpl("layersmenu-vert_sub_menu.ihtml");						
		        $GLOBALS[mid.$bid]->parseStructureForMenu("vertmenu$block_bid");
                $GLOBALS[mid.$bid]->newVerticalMenu("vertmenu$block_bid");
		    } else {
                $GLOBALS[mid.$bid]->setSubMenuTpl("layersmenu-horiz_sub_menu.ihtml");						
		        $GLOBALS[mid.$bid]->parseStructureForMenu("hormenu$block_bid");
                $GLOBALS[mid.$bid]->newHorizontalMenu("hormenu$block_bid");	
            }
		 }
		if ($count)
	        $GLOBALS[mid.$bid]->printHeader();
    
    if ($content[0] == '0') { // Vertical Menu
        $boxcontent .= $GLOBALS[mid.$bid]->getMenu("vertmenu$row[bid]");
	}elseif ($content[0] == '1' || $content[0] == '5') { // Vertical Tree
		$content[3] = blocks_menu_contentsblockblock_preprocess($content[3], $row['title']);
        $GLOBALS[treemid.$bid]->setMenuStructureString($content[3]);
        $GLOBALS[treemid.$bid]->parseStructureForMenu("treemenu$row[bid]");
		if ($GLOBALS['dm_highlight_treelink']) {
            if ($_REQUEST['name'])
			    $this_url = $_REQUEST['name'];
			elseif ($_REQUEST['module'])
			    $this_url = $_REQUEST['module'];
			else 	
			    $this_url = basename(str_replace($_SERVER['REQUEST_URI'],'&','&amp;'));
			    $GLOBALS[treemid.$bid]->setSelectedItemByUrl("treemenu$row[bid]", $this_url);	
		}
        $boxcontent = $GLOBALS[treemid.$bid]->newTreeMenu("treemenu$row[bid]");
	} elseif ($content[0] == '2') { // Vertical Plain	
		$content[3] = blocks_menu_contentsblockblock_preprocess($content[3], $row['title']);		
        $GLOBALS[plmid.$bid]->setPlainMenuTpl("layersmenu-plain_menu.ihtml");			
        $GLOBALS[plmid.$bid]->setMenuStructureString($content[3]);	
        $GLOBALS[plmid.$bid]->parseStructureForMenu("treemenu$row[bid]");
        $boxcontent = $GLOBALS[plmid.$bid]->newPlainMenu("treemenu$row[bid]");				
	} elseif ($content[0] == '3') { // Horizontal Menu		
		$boxcontent = $GLOBALS[mid.$bid]->getMenu("hormenu$row[bid]");
	} else { // Horizontal Plain		
        $content[3] = blocks_menu_contentsblockblock_preprocess($content[3], $row['title']);				
        $GLOBALS[plmid.$bid]->setMenuStructureString($content[3]);	
        $GLOBALS[plmid.$bid]->parseStructureForMenu("treemenu$row[bid]");
        $boxcontent = $GLOBALS[plmid.$bid]->newHorizontalPlainMenu("treemenu$row[bid]");		
	}
	$expandMenu = '';
	if ($vars['menutype']==1){
		if ($vars['defaultexpand']){
			if(empty($_SESSION['expandMenu'.$row['bid']])){
				$tmp = split('onmousedown="toggletreemenu'.$row['bid'],$boxcontent);
				for ($i=1;$i<count($tmp);$i++){
					$tmp2 = split('">',$tmp[$i]);
					if (!empty($tmp2))
						$expandMenu .= "<script>toggletreemenu".$row['bid'].$tmp2[0]."</script>";
				}
				$_SESSION['expandMenu'.$row['bid']] = 'expandMenu';
			}
		}
	}
	$row['content'] = $boxcontent.$expandMenu;

	if ($count) {
		$GLOBALS['dmfooter'] = $GLOBALS[mid.$bid]->getFooter();
		if ($GLOBALS['dm_include_seethroughjs'])	
            $GLOBALS['dmfooter'] .= '<script language="JavaScript" type="text/javascript" src="api/portlets/dynamenu/libjs/layersmenu-see-through.js"></script>';
		}
	return $row;
}
function blocks_menu_contentsblockblock_preprocess($content, $block_title) {

    $processed_line = array();
    $content = str_replace("\'","'",$content);	
    $content = str_replace("\\\"","&quot;",$content);		
    $content = str_replace("&","&amp;",$content);								
	$this_line = explode("\n",$content);
	while (list($key,$value) = each($this_line)) {
	    $this_value = explode('|',$value);
		$this_value[2] = trim($this_value[2]);
		$url = '';
		if (substr($this_value[2],0,1) == '[') {
            $url = explode(':', substr($this_value[2], 1,  - 1));
			$this_value[2] = 'index.php?op=modload&amp;name='.$url[0].'&amp;file='.((isset($url[1])) ? $url[1]:'index');
        } elseif (substr($this_value[2],0,1) == '{') {
            $url = explode(':', substr($this_value[2], 1,  - 1));
            $this_value[2] = 'index.php?module='.$url[0].'&amp;func='.((isset($url[1])) ? $url[1]:'main');
        }
		if (!pnSecAuthAction(0, "Dynamenublock::", "$block_title:$this_value[1]:", ACCESS_READ)) {
		    continue;
		} 
		$processed_line[] = implode('|',$this_value);
	}
	$content = implode("\n",$processed_line);
    return $content;				
}

?>