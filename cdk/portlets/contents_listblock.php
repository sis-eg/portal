<?php
// Saman Portal
// Copyright (C) 2008 by the Saman Information Structure Development Team.
// http://www.sis-eg.com/
// ----------------------------------------------------------------------


function cdk_contents_listblockblock_init(){
	pnSecAddSchema('cdk:contents_listblock:', 'Block title::');
}

function cdk_contents_listblockblock_info(){
    return array('text_type' => 'contents_listblock',
                 'module' => 'cdk',
                 'text_type_long' => _CDK_LIST_BLOCK_,
                 'allow_multiple' => true,
                 'form_content' => false,
                 'form_refresh' => false,
                 'show_preview' => false,
				 'default_title' => _CDK_LIST_BLOCK_TITLE,
				 'allow_user_add'  => 0,
				 'group' => 2,
				 'block_description' => _CDK_LIST_BLOCK_DESCRIPTION,
			     'allow_subportal_add'  => 1,
			     'is_object' => true				 
                 );
}

$GLOBALS['object_settings']['contents_listblock'] = array(
	'beforeDisplayScript',
	'contentType',
	'contentTypeTemplate',
	'contentTypeField',
	'contentTypeField4',
	'contentTypeField5',
	'contentTypeDisplayPermission',
	'use_pager',
	'donot_display_doplicate_items',
	'show_alphabetic_nav',
	'contentTypeField3',
	'contentTypeColsCount',
	'dataArrangement',
	'subportal',
	'alphabeticSortField',
	'admin_list',
	'apply_web_directory_view_limitation',
	'history',
	'contentTypeExtraSelect',
	'contentTypeExtraFrom',
	'contentTypeFieldOrder',
	'contentTypeField4Order',
	'contentTypeField5Order',
	'contentTypeFieldCustomeOrder'
);
$GLOBALS['portlet_settings']['contents_listblock'] = array('beforeDisplayScript'					=> _CNT_BLK_BEFORE_DISPLAY_SCRIPT,
															'subportal'								=> _CNT_BLK_SELECT_SUPORTAL,
															'contentType'							=> _CNT_BLK_CONENT_TYPE,
															'contentTypeTemplate'					=> _CNT_BLK_CONENT_TYPE_TEMPLATE,
															'contentTypeDisplayPermission'			=> _CNT_BLK_DISPLAY_BASE_PERMISSION,
															'contentTypeItemsCount'					=> _CNT_BLK_CONENT_TYPE_ITEMS_COUNT,
															'use_pager'								=> _CNT_BLK_CONENT_TYPE_USE_PAGER,
															'show_alphabetic_nav'					=> _CNT_BLK_CONENT_TYPE_SHOW_ALPHABETIC_NAV,
															'search_sensitive'						=> _CNT_BLK_SEARCH_SENSITIVE,
															'admin_list'							=> _CNT_BLK_USE_AS_ADMIN_LIST,
															'apply_web_directory_view_limitation'	=> _CNT_BLK_USE_WEB_DIRECTORY_VIEW_LIMITATION,
															'history'								=> _CNT_BLK_HISTORY_TYPE,
															'contentTypeColsCount'					=> _CNT_BLK_CONENT_TYPE_COLS_COUNT,
															'dataArrangement'						=> _CNT_BLK_DATA_ARRANGEMENT,
															'contentTypeFieldOrder'					=> _CNT_BLK_CONENT_TYPE_ORDERBY_FIELD,
															'contentTypeSelectedID'					=> _CNT_BLK_CONENT_TYPE_SELECTED_ID,
															'contentTypeExtraSelect'				=> _CNT_BLK_EXTRA_SELECT_CLAUSE,
															'contentTypeExtraFrom'					=> _CNT_BLK_EXTRA_FROM_CLAUSE,
															'contentTypeSelectingQuery'				=> _CNT_BLK_CONENT_TYPE_WHERE_CLAUSE,
															'contentTypeWebDirectory'				=> _CNT_BLK_CONENT_TYPE_WEB_DIRECTORY,
															'donot_display_doplicate_items'			=> _CNT_BLK_CONENT_TYPE_DONOT_DISPLAY_DUPLICATE_ITEMS,
/*																'contentTypeField'						=> _CDK_LIST_BLOCK_1,
																'contentTypeField4'						=> _CDK_LIST_BLOCK_1,
																'contentTypeField4Order'				=> _CDK_LIST_BLOCK_1,
																'contentTypeField5'						=> _CDK_LIST_BLOCK_1,
																'contentTypeField5Order'				=> _CDK_LIST_BLOCK_1,
															'contentWebDirectoryDeep'				=> _CNT_BLK_DISPLAY_ALL_SUB_WD_DATE,
															'currentDirectory'						=> _CNT_BLK_DISPLAY_BASE_CURRENT_DIRECTORY,
																'alphabeticSortField'					=> _CDK_LIST_BLOCK_1*/
															);

$GLOBALS['portlet_settings_related']['contents_listblock'] = array('contentTypeFieldOrder' 	=> array('contentTypeField','contentTypeField4','contentTypeField4Order','contentTypeField5','contentTypeField5Order', 'contentTypeFieldCustomeOrder'),
																   'contentTypeWebDirectory'=> array('contentWebDirectoryDeep','currentDirectory'),
																   'show_alphabetic_nav' 	=> array('alphabeticSortField'));


function cdk_contents_listblockblock_display($blockinfo){
    if (!pnSecAuthAction(0, 'cdk::contents_listblock', "$blockinfo[title]::", ACCESS_READ)) 
    	return;
    if ($GLOBALS['_sisShowSearchResultBlocks_'][$blockinfo['bid']] === false)
    	return;
    $vars = pnBlockVarsFromContent($blockinfo['content']);  
    $GLOBALS['originalGetParams'] = $_GET;
    if ($vars['contentType'] == '-1')
    	$_GET['ctp_id'] = pnVarCleanFromInput('ctp_id');
    else
    	$_GET['ctp_id'] = $vars['contentType'];
    if (!$vars['contentWebDirectoryDeep'])
    	$vars['contentWebDirectoryDeep'] = 1;
    if (!$vars['history'])
    	$vars['history'] = 'ignore';
    		
    $GLOBALS['before_display_script'] = $vars['beforeDisplayScript'];	
    if ($vars['contentTypeTemplate'] || $vars['contentType'] != '-1') {
    	$_GET['template_id'] = $vars['contentTypeTemplate'];
    }
    if ($vars['contentTypeItemsCount'] || $vars['contentType'] != '-1') {
    	$_GET['item_count'] = $vars['contentTypeItemsCount'];
    }
    if ($vars['contentTypeDisplayPermission'] || $vars['contentType'] != '-1') {
    	$_GET['list_display_permission'] = $vars['contentTypeDisplayPermission'];
    }
    if ($vars['use_pager'] || $vars['contentType'] != '-1') {
    	$_GET['use_pager'] = $vars['use_pager'];
    }
    if ($vars['contentTypeColsCount'] || $vars['contentType'] != '-1') {
    	$_GET['col_count'] = $vars['contentTypeColsCount'];
    }
    if ($vars['dataArrangement'] || $vars['contentType'] != '-1') {
    	$_GET['data_arrangement'] = $vars['dataArrangement'];
    }
    if ($vars['contentTypeSelectedID'] || $vars['contentType'] != '-1') {
    	$_GET['selected_id'] = $vars['contentTypeSelectedID'];
    }
    if ($vars['contentTypeSelectingQuery'] || $vars['contentType'] != '-1') {
    	$_GET['where_clause'] = $vars['contentTypeSelectingQuery'];
    }
    if ($vars['contentTypeExtraSelect'] || $vars['contentType'] != '-1') {
    	$GLOBALS['extra_select_clause'] = $vars['contentTypeExtraSelect'];
    }
    if ($vars['contentTypeExtraFrom'] || $vars['contentType'] != '-1') {
    	$GLOBALS['extra_from_clause'] = $vars['contentTypeExtraFrom'];
    }
    if ($vars['contentTypeWebDirectory'] || $vars['contentType'] != '-1') {
    	$_GET['web_directory_id'] = $vars['contentTypeWebDirectory'];
    }
    if ($vars['contentWebDirectoryDeep'] || $vars['contentType'] != '-1') {
    	$_GET['web_directory_deep'] = $vars['contentWebDirectoryDeep'];
    }
    if ($vars['currentDirectory'] || $vars['contentType'] != '-1') {
	    if ($vars['currentDirectory'])
	    	$_GET['web_directory_id'] = $GLOBALS['wd_id'];
    }
    if ($vars['show_alphabetic_nav'] || $vars['contentType'] != '-1') {
    	$_GET['show_alphabetic_nav'] = $vars['show_alphabetic_nav'];
    }
    if ($vars['alphabeticSortField'] || $vars['contentType'] != '-1') {
    	$_GET['alphabeticSortField'] = $vars['alphabeticSortField'];
    }
    if ($vars['search_sensitive'] || $vars['contentType'] != '-1') {
    	$_GET['search_sensitive'] = $vars['search_sensitive'];
    }
    if ($vars['admin_list'] || $vars['contentType'] != '-1') {
    	$GLOBALS['_admin_list_'] = $vars['admin_list'];
    }
    if ($vars['admin_list'] || $vars['apply_wd_limit'] != '-1') {
    	$_GET['apply_wd_limit'] = $vars['apply_web_directory_view_limitation'];
    }
    if ($vars['history'] || $vars['apply_wd_limit'] != '-1') {
    	$_GET['history'] = $vars['history'];
    }
    if ($vars['contentTypeField'] || $vars['apply_wd_limit'] != '-1') {
    	$_GET['field'] = $vars['contentTypeField'];    
    }
    if ($vars['contentTypeFieldOrder'] || $vars['apply_wd_limit'] != '-1') {
    	$_GET['order'] = $vars['contentTypeFieldOrder'];    
    }
    if ($vars['contentTypeField4'] || $vars['apply_wd_limit'] != '-1') {
    	$_GET['field2'] = $vars['contentTypeField4'];    
    }
    if ($vars['contentTypeField4Order'] || $vars['apply_wd_limit'] != '-1') {
    	$_GET['order2'] = $vars['contentTypeField4Order'];    
    }
    if ($vars['contentTypeField5'] || $vars['apply_wd_limit'] != '-1') {
    	$_GET['field3'] = $vars['contentTypeField5'];    
    }
    if ($vars['contentTypeField5Order'] || $vars['apply_wd_limit'] != '-1') {
    	$_GET['order3'] = $vars['contentTypeField5Order'];    
    }
    if ($vars['contentTypeFieldCustomeOrder'] || $vars['apply_wd_limit'] != '-1') {
    	$_GET['custome_order'] = $vars['contentTypeFieldCustomeOrder'];    
    }
    $_GET['empty_result'] = true;
    if ($GLOBALS['portal_id'] == 0 || $vars['subportal']) {   	
    	$_GET['subportal'] = $vars['subportal'];
    	$GLOBALS['_filter_subportal_contents_'] = $_GET['subportal'];
    }
    $GLOBALS['cdk_doplicate_items'][$_GET['ctp_id']] = $vars['donot_display_doplicate_items'];
    $oldOp = $GLOBALS['sisOp'];
    $GLOBALS['_block_id_'] = $blockinfo['bid'];    
    
	$blockinfo['content'] = pnModFunc('cdk', 'user', 'loadmodule', array('system'=>'cdk',
													 							  	 'sismodule' => 'block/contents_template_block.php'));
	unset($_GET['subportal']);
	unset($GLOBALS['_filter_subportal_contents_']);

	$GLOBALS['sisOp'] = $oldOp;
	$_GET = $GLOBALS['originalGetParams'] ;
	unset($GLOBALS['originalGetParams'] );	
    unset($GLOBALS['extra_select_clause']);
    unset($GLOBALS['extra_from_clause']);
	
	if ($blockinfo['content']==""|| $blockinfo['content']=='<input type="hidden" name="allRowsCount" value="0" >')  
		return ;	

	return themesideblock($blockinfo);	
}

function cdk_contents_listblockblock_modify($blockinfo){
	pnModFunc('cdk', 'user', 'loadmodule', array('system'=>'cdk', 'sismodule'=>'header.php'));
	require_once(sisGetSetting('definitions'));
	
    $vars = pnBlockVarsFromContent($blockinfo['content']);          
    /*if ($vars['contentTypeField'] == '' )
    	$vars['contentTypeField'] = 'last_modified_date';
    if ($vars['contentTypeFieldOrder'] == '' )
    	$vars['contentTypeFieldOrder'] = 'descending';*/
    if (!$vars['contentWebDirectoryDeep'])
    	$vars['contentWebDirectoryDeep'] = 1;
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
	$allTemplates = pnModAPIFunc('cdk', 'user', 'getTypeTemplates', array('templateType'=>3, 'includeGeneral'=>true));
	$content = "<script>
				var contentTypeTemplates = new Array();
				var contentTypeFields = new Array();				
				var selectedTemplate = '$vars[contentTypeTemplate]';
				var selectedField = '$vars[contentTypeField]';				
				var selectedField3 = '$vars[alphabeticSortField]';
				var selectedField4 = '$vars[contentTypeField4]';
				var selectedField5 = '$vars[contentTypeField5]';
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
							opt.innerHTML = contentTypeTemplates[cmbContentType.value][idx].name;
							cmbContentTypeTemplate.appendChild(opt);
						}										
				   selectedTemplate	= '';
				}
				function initContentTypeFieldCombo() {
					cmbContentType = document.getElementById('cmbContentType');
					cmbContentTypeField = document.getElementById('cmbContentTypeField');
					cmbContentTypeField2 = document.getElementById('cmbContentTypeField2');
					cmbContentTypeField3 = document.getElementById('cmbContentTypeField3');
					cmbContentTypeField4 = document.getElementById('cmbContentTypeField4');
					cmbContentTypeField5 = document.getElementById('cmbContentTypeField5');
					if (cmbContentTypeField)
						cmbContentTypeField.innerHTML = '';
					if (cmbContentTypeField2)
						cmbContentTypeField2.innerHTML = '';
					if (cmbContentTypeField3)
						cmbContentTypeField3.innerHTML = '';
					if (cmbContentTypeField4)
						cmbContentTypeField4.innerHTML = '';
					if (cmbContentTypeField5)
						cmbContentTypeField5.innerHTML = '';
						
					var opt = document.createElement('option');
					if (cmbContentTypeField)
						cmbContentTypeField.appendChild(opt);
					opt = document.createElement('option');						
					opt.value = 'rand()';
					if (selectedField == opt.value)
						opt.selected = true;
					opt.innerHTML = '"._CNT_BLK_CONENT_TYPE_ORDERBY_RANDOM."';										
					if (cmbContentTypeField)
						cmbContentTypeField.appendChild(opt);
						
					opt = document.createElement('option');
					if (cmbContentTypeField4)
						cmbContentTypeField4.appendChild(opt);
					opt = document.createElement('option');
					if (cmbContentTypeField5)
						cmbContentTypeField5.appendChild(opt);

					if (contentTypeFields[cmbContentType.value] != null) 
						for(var idx = 0; idx < contentTypeFields[cmbContentType.value].length; idx++) {
							opt = document.createElement('option');
							opt.value = contentTypeFields[cmbContentType.value][idx].name;
							if (selectedField == opt.value)
								opt.selected = true;
							opt.innerHTML = contentTypeFields[cmbContentType.value][idx].caption;
							if (cmbContentTypeField)
								cmbContentTypeField.appendChild(opt);

							opt2 = document.createElement('option');
							opt2.value = contentTypeFields[cmbContentType.value][idx].name;
							opt2.innerHTML = contentTypeFields[cmbContentType.value][idx].caption;
							if (cmbContentTypeField2)
								cmbContentTypeField2.appendChild(opt2);
								
							opt3 = document.createElement('option');
							opt3.value = contentTypeFields[cmbContentType.value][idx].name;
							opt3.innerHTML = contentTypeFields[cmbContentType.value][idx].caption;
							if (selectedField3 == opt3.value)
								opt3.selected = true;
							if (cmbContentTypeField3)
								cmbContentTypeField3.appendChild(opt3);
								
							opt4 = document.createElement('option');
							opt4.value = contentTypeFields[cmbContentType.value][idx].name;
							opt4.innerHTML = contentTypeFields[cmbContentType.value][idx].caption;
							if (selectedField4 == opt4.value)
								opt4.selected = true;
							if (cmbContentTypeField4)
								cmbContentTypeField4.appendChild(opt4);
								
							opt5 = document.createElement('option');
							opt5.value = contentTypeFields[cmbContentType.value][idx].name;
							opt5.innerHTML = contentTypeFields[cmbContentType.value][idx].caption;
							if (selectedField5 == opt5.value)
								opt5.selected = true;
							if (cmbContentTypeField5)
								cmbContentTypeField5.appendChild(opt5);
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
			$template = str_replace("'", "", $template);			
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
		$strTemplates .= "{'name':'_wd_rank_', 'caption':'"._CNT_BLK_WEB_DIRECTORY_RANK."'},";
		if ($strTemplates > '') {
			$strTemplates = substr($strTemplates, 0, strlen($strTemplates) - 1);		
			$content .= "contentTypeFields[$contentType[ctp_id]] = [$strTemplates];";
		}
	}
	/*$content .= "contentTypeFields[-1] = [{'name':'counter', 'caption':'"._CNT_BLK_CONENT_TYPE_FIELD_COUNTER."'},".
										 "{'name':'display_start_date', 'caption':'"._CNT_BLK_CONENT_TYPE_FIELD_DISPLAY_START_DATE."'},".
										 "{'name':'last_modified_date', 'caption':'"._CNT_BLK_CONENT_TYPE_FIELD_LAST_MODIFIED_DATE."'},".
										 "{'name':'page_title', 'caption':'"._CNT_BLK_CONENT_TYPE_FIELD_PAGE_TITLE."'},".
										 "{'name':'rate', 'caption':'"._CNT_BLK_CONENT_TYPE_FIELD_RATE."'},".
										 "{'name':'wd_rank', 'caption':'"._CNT_BLK_WEB_DIRECTORY_RANK."'}];";*/
	$content .= "</script>";	
	$content .= "<tr>
					<td width='15%'><br></td>
					<td></td>
				</tr>";
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'contents_listblock', 'beforeDisplayScript'))	
		$content .= "<tr>
						<td class='caption' nowrap='nowrap' style='vertical-align:top'>"
							. _CNT_BLK_BEFORE_DISPLAY_SCRIPT . " : 
						</td>
						<td>
							<textarea name='beforeDisplayScript' id='beforeDisplayScript' rows='5' cols='80' style='direction:ltr;font-family:Courier New;width:85%'>$vars[beforeDisplayScript]</textarea>
				   		</td>
					</tr>";	
	if ($GLOBALS['portal_id'] == 0 && pnModAvailable('subportal')) {
		$subportals = pnModAPIFunc('subportal', 'admin', 'getall', array('portalType'=>2));
		$subportalOptions = '<option value="0" '.($vars['subportal'] == 0?'selected':'').' >'._CNT_BLK_SELECT_SUPORTAL_CURRENT_PORTAL.'</option>';		
		$subportalOptions .= '<option value="-1" '.($vars['subportal'] == '-1'?'selected':'').' >'._CNT_BLK_SELECT_SUPORTAL_MAIN_PORTAL.'</option>';				
		
		foreach ($subportals as $subportal) {
			$title = $subportal['etitle'];
			if (pnUserGetLang() == 'far')
				$title = $subportal['ptitle'];
			$subportalOptions .= '<option value="'.$subportal['id'].'" '.($vars['subportal'] == $subportal['id']?'selected':'').' >'.$title.'</option>';
		}
		$subportalOptions .= '<option value="all" '.($vars['subportal'] == 'all'?'selected':'').' >'._CNT_BLK_SELECT_SUPORTAL_ALL.'</option>';
		if(pnBlockIsObjectSetting($blockinfo['block_id'], 'contents_listblock', 'contentType'))								
			$content .= "<tr>
							<td class='caption' nowrap='nowrap'>"
								. _CNT_BLK_SELECT_SUPORTAL . " : 
							</td>
							<td>
								<select name='subportal' id='cmbSubportal' >"
									. $subportalOptions . "
								</select>
					   		</td>
						</tr>";		
	}
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'contents_listblock', 'contentType'))	
		$content .= "<tr>
						<td class='caption' nowrap='nowrap'>"
							. _CNT_BLK_CONENT_TYPE . " : 
						</td>
						<td>
							<select name='contentType' id='cmbContentType' onchange='initContentTypeTemplateCombo(); initContentTypeFieldCombo();'>"
								. $typesOptions . "
							</select>
				   		</td>
					</tr>";
	else 
		$content .= "<input type='hidden' name='contentType' id='cmbContentType' value='$vars[contentType]' />";
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'contents_listblock', 'contentTypeTemplate'))								
			$content .= "						
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
				</tr>";

	$accessLevels = $GLOBALS['permissionsLevel'];
	unset($accessLevels[0]);
	$accessLevelsOptions = '<option></option>';
	foreach ($accessLevels as $key=>$value) {
		$accessLevelsOptions .= "<option value='$value[value]' ".($vars['contentTypeDisplayPermission']==$value['value']?'selected':'')." >$value[caption]</option>";
	}	
	
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'contents_listblock', 'contentTypeDisplayPermission'))
			$content .= "									
				<tr>
					<td class='caption'  nowrap='nowrap'>"
						. _CNT_BLK_DISPLAY_BASE_PERMISSION . ":
					</td>
					<td>
						<select name='contentTypeDisplayPermission' id='txtContentTypeDisplayPermission'>$accessLevelsOptions</select>
					</td>
				</tr>";
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'contents_listblock', 'contentTypeItemsCount'))
			$content .= "									
				<tr>
					<td class='caption'  nowrap='nowrap'>"
						. _CNT_BLK_CONENT_TYPE_ITEMS_COUNT . ":
					</td>
					<td>
						<input type='text' name='contentTypeItemsCount' id='txtContentTypeItemsCount' style='width:50px' value='$vars[contentTypeItemsCount]'/>
					</td>
				</tr>";
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'contents_listblock', 'use_pager'))
			$content .= "						
				<tr>
					<td class='caption'  nowrap='nowrap'>
					</td>
					<td>
						<INPUT value=1 " . ($vars['use_pager'] == 1?'CHECKED':'') . " type='checkbox' name='use_pager'>"
						. _CNT_BLK_CONENT_TYPE_USE_PAGER ."						
					</td>
				</tr>";
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'contents_listblock', 'show_alphabetic_nav'))
			$content .= "												
				<tr>
					<td class='caption'  nowrap='nowrap'>
					</td>
					<td>
						<INPUT value=1 " . ($vars['show_alphabetic_nav'] == 1?'CHECKED':'') . " type='checkbox' name='show_alphabetic_nav'>"
						. _CNT_BLK_CONENT_TYPE_SHOW_ALPHABETIC_NAV ."&nbsp;
						<select id='cmbContentTypeField3' name='alphabeticSortField' style='width:120px'>
						</select>				
					</td>
				</tr>";
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'contents_listblock', 'search_sensitive'))
			$content .= "												
				<tr>
					<td class='caption'  nowrap='nowrap'>
					</td>
					<td>
						<INPUT value=1 " . ($vars['search_sensitive'] == 1?'CHECKED':'') . " type='checkbox' name='search_sensitive'>"
						. _CNT_BLK_SEARCH_SENSITIVE ."						
					</td>
				</tr>";
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'contents_listblock', 'admin_list'))
			$content .= "												
				<tr>
					<td class='caption'  nowrap='nowrap'>
					</td>
					<td>
						<INPUT value=1 " . ($vars['admin_list'] == 1?'CHECKED':'') . " type='checkbox' name='admin_list'>"
						. _CNT_BLK_USE_AS_ADMIN_LIST ."						
					</td>
				</tr>";						
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'contents_listblock', 'apply_web_directory_view_limitation'))						
			$content .= "										
				<tr>
					<td class='caption'  nowrap='nowrap' style='vertical-align:top' valign='top'>											
					</td>
					<td>
						<INPUT value=1 " . ($vars['apply_web_directory_view_limitation'] == 1?'CHECKED':'') . " type='checkbox' name='apply_web_directory_view_limitation'>
						" . _CNT_BLK_USE_WEB_DIRECTORY_VIEW_LIMITATION  . "
					</td>
				</tr>";	
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'contents_listblock', 'history'))														
		$content .= "										
			<tr>
				<td class='caption'  nowrap='nowrap' style='vertical-align:top' valign='top'>"._CNT_BLK_HISTORY_TYPE.":
				</td>
				<td>
					<select name='history'>
						<option value='ignore' ". (($vars['history'] == 'ignore' || !$vars['history'])?'selected':'').">"._CNT_BLK_HISTORY_IGNORE."</option>
						<option value='add' ". ($vars['history'] == 'add' ?'selected':'').">"._CNT_BLK_HISTORY_KEEP."</option>
						<option value='reset' ". ($vars['history'] == 'reset' ?'selected':'').">"._CNT_BLK_HISTORY_RESET."</option>
					</select>
				</td>
			</tr>";						
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'contents_listblock', 'contentTypeColsCount')) 
			$content .="									
				<tr>
					<td class='caption'  nowrap='nowrap'>"
						. _CNT_BLK_CONENT_TYPE_COLS_COUNT . ":
					</td>
					<td>
						<input type='text' name='contentTypeColsCount' id='txtContentTypeColsCount' style='width:50px' value='$vars[contentTypeColsCount]'/>
					</td>
				</tr>";
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'contents_listblock', 'dataArrangement')) 
			$content .="									
				<tr>
					<td class='caption'  nowrap='nowrap'>"
						. _CNT_BLK_DATA_ARRANGEMENT . ":
					</td>
					<td>
						<select name='dataArrangement' id='cmbDataArrangement'>
							<option value='1' ".($vars['dataArrangement']!=2?'selected':'').">"._CNT_BLK_DATA_ARRANGEMENT_ROW."</option>
							<option value='2' ".($vars['dataArrangement']==2?'selected':'').">"._CNT_BLK_DATA_ARRANGEMENT_COLUMN."</option>
						</select>
						<br/>
						<span class='itemdescription'>" . _CNT_BLK_DATA_ARRANGEMENT_DESC . "</span>
					</td>
				</tr>";
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'contents_listblock', 'contentTypeFieldOrder'))
			$content .= "												
				<tr>
					<td class='caption'  nowrap='nowrap' style='vertical-align:top'>"
						. _CNT_BLK_CONENT_TYPE_ORDERBY_FIELD . ":
					</td>
					<td>
						<select name='contentTypeField' id='cmbContentTypeField' style='width:120px'>
						</select>
						<select name='contentTypeFieldOredr' id='cmbContentTypeFieldOredr'>
							<option></option>
							<option value='ascending' " . ($vars['contentTypeFieldOrder'] == 'ascending'?'selected':'') . ">" . _CNT_BLK_CONENT_TYPE_ASCENDING . "</option>
							<option value='descending' " . ($vars['contentTypeFieldOrder'] == 'descending'?'selected':'') . ">" . _CNT_BLK_CONENT_TYPE_DESCENDING . "</option>							
						</select>		
						<br/>				
						<select name='contentTypeField4' id='cmbContentTypeField4' style='width:120px'>
						</select>
						<select name='contentTypeField4Oredr' id='cmbContentTypeField4Oredr'>
							<option></option>
							<option value='ascending' " . ($vars['contentTypeField4Order'] == 'ascending'?'selected':'') . ">" . _CNT_BLK_CONENT_TYPE_ASCENDING . "</option>
							<option value='descending' " . ($vars['contentTypeField4Order'] == 'descending'?'selected':'') . ">" . _CNT_BLK_CONENT_TYPE_DESCENDING . "</option>							
						</select>		
						<br/>				
						<select name='contentTypeField5' id='cmbContentTypeField5' style='width:120px'>
						</select>
						<select name='contentTypeField5Oredr' id='cmbContentTypeField5Oredr'>
							<option></option>
							<option value='ascending' " . ($vars['contentTypeField5Order'] == 'ascending'?'selected':'') . ">" . _CNT_BLK_CONENT_TYPE_ASCENDING . "</option>
							<option value='descending' " . ($vars['contentTypeField5Order'] == 'descending'?'selected':'') . ">" . _CNT_BLK_CONENT_TYPE_DESCENDING . "</option>
						</select>
						<br/>
						<input type='text' name='contentTypeFieldCustomeOrder' style='width:180px;direction:ltr' value='$vars[contentTypeFieldCustomeOrder]'/>
					</td>
				</tr>";
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'contents_listblock', 'contentTypeSelectedID'))
			$content .= "									
				<tr>
					<td class='caption'  nowrap='nowrap' style='vertical-align:top' valign='top'>"
						. _CNT_BLK_CONENT_TYPE_SELECTED_ID . ":
					</td>
					<td>
						<textarea name='contentTypeSelectedID' id='memcontentTypeSelectedID' rows='5' cols='50'>$vars[contentTypeSelectedID]</textarea>
						<br/>
						<span class='itemdescription'>" . _CNT_BLK_CONENT_TYPE_CUSTOM_ID_DESC . "</span>
					</td>
				</tr>";
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'contents_listblock', 'contentTypeExtraSelect'))						
			$content .= "						
				<tr>
					<td class='caption'  nowrap='nowrap' style='vertical-align:top' valign='top'>"
						. _CNT_BLK_EXTRA_SELECT_CLAUSE . ":
					</td>
					<td>
						<textarea name='contentTypeExtraSelect' rows='5' cols='50' style='direction:ltr'>$vars[contentTypeExtraSelect]</textarea>
					</td>
				</tr>";
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'contents_listblock', 'contentTypeExtraFrom'))						
			$content .= "						
				<tr>
					<td class='caption'  nowrap='nowrap' style='vertical-align:top' valign='top'>"
						. _CNT_BLK_EXTRA_FROM_CLAUSE . ":
					</td>
					<td>
						<textarea name='contentTypeExtraFrom' rows='5' cols='50' style='direction:ltr'>$vars[contentTypeExtraFrom]</textarea>
					</td>
				</tr>";
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'contents_listblock', 'contentTypeSelectingQuery'))						
			$content .= "						
				<tr>
					<td class='caption'  nowrap='nowrap' style='vertical-align:top' valign='top'>"
						. _CNT_BLK_CONENT_TYPE_WHERE_CLAUSE . ":
					</td>
					<td>
						<textarea name='contentTypeSelectingQuery' rows='5' cols='50' style='direction:ltr'>$vars[contentTypeSelectingQuery]</textarea>
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
					</td>
				</tr>";
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'contents_listblock', 'contentTypeWebDirectory'))						
			$content .= "						
				<tr>
					<td class='caption'  nowrap='nowrap' style='vertical-align:top' valign='top'>"
						. _CNT_BLK_CONENT_TYPE_WEB_DIRECTORY . ":
					</td>
					<td>
						<input name='contentTypeWebDirectory' value='$vars[contentTypeWebDirectory]' size='4' />
						&nbsp;
						<INPUT value=1 " . ($vars['currentDirectory'] == 1?'CHECKED':'') . " type='checkbox' name='currentDirectory'>&nbsp;"._CNT_BLK_DISPLAY_BASE_CURRENT_DIRECTORY."
					</td>
				</tr>
				<tr>
					<td></td>
					<td>
						<input name='contentWebDirectoryDeep' type='radio' ".($vars['contentWebDirectoryDeep'] == 1?'checked':'')." value='1' /> "._CNT_BLK_DISPLAY_ALL_SUB_WD_DATE."
					</td>
				</tr>
				<tr>
					<td></td>
					<td>
						<input name='contentWebDirectoryDeep' type='radio' ".($vars['contentWebDirectoryDeep'] == 2?'checked':'')." value='2' /> "._CNT_BLK_DISPLAY_JUST_SUB_WD_DATE."
					</td>
				</tr>
				";
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'contents_listblock', 'donot_display_doplicate_items'))						
			$content .= "										
				<tr>
					<td class='caption'  nowrap='nowrap' style='vertical-align:top' valign='top'>											
					</td>
					<td>
						<INPUT value=1 " . ($vars['donot_display_doplicate_items'] == 1?'CHECKED':'') . " type='checkbox' name='donot_display_doplicate_items'>
						" . _CNT_BLK_CONENT_TYPE_DONOT_DISPLAY_DUPLICATE_ITEMS  . "
						<br/>
						<span class='itemdescription'>" . _CNT_BLK_CONENT_TYPE_DONOT_DISPLAY_DUPLICATE_ITEMS_DESC . "</span>
					</td>
				</tr>";
		$content .="			
				<tr>
					<td colspan=\"2\"><br></td>
				</tr>
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
						if (window.clipboardData)
							window.clipboardData.setData('Text', str);	
						else
							window.prompt('Control + C to copy:', str);
					}
				</script>
				";
    $output = new pnHTML();
	$output->SetInputMode(_PNH_VERBATIMINPUT);
	$output->Text($content);
    return $output->GetOutput();
}

/*
	'beforeDisplayScript',
	'contentType',
	'contentTypeTemplate',
	'contentTypeField',
	'contentTypeFieldOrder',
	'contentTypeField4',
	'contentTypeField4Order',
	'contentTypeField5',
	'contentTypeField5Order',
	'contentTypeFieldCustomeOrder',
	'use_pager',
	'contentTypeSelectedID',
	'donot_display_doplicate_items',
	'show_alphabetic_nav',
	'subportal',
	'search_sensitive',
	'admin_list',
	'apply_web_directory_view_limitation',
	'history'

*/

function cdk_contents_listblockblock_update($blockinfo){
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'contents_listblock', 'beforeDisplayScript'))
		$vars['beforeDisplayScript'] = pnVarCleanFromInput('beforeDisplayScript');
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'contents_listblock', 'contentType'))
		$vars['contentType'] = pnVarCleanFromInput('contentType');
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'contents_listblock', 'contentTypeTemplate'))
		$vars['contentTypeTemplate'] = pnVarCleanFromInput('contentTypeTemplate');
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'contents_listblock', 'contentTypeFieldOrder')){
		$vars['contentTypeField'] = pnVarCleanFromInput('contentTypeField');
		$vars['contentTypeFieldOrder'] = pnVarCleanFromInput('contentTypeFieldOredr');
		$vars['contentTypeField4'] = pnVarCleanFromInput('contentTypeField4');
		$vars['contentTypeField4Order'] = pnVarCleanFromInput('contentTypeField4Oredr');
		$vars['contentTypeField5'] = pnVarCleanFromInput('contentTypeField5');
		$vars['contentTypeField5Order'] = pnVarCleanFromInput('contentTypeField5Oredr');
		$vars['contentTypeFieldCustomeOrder'] = pnVarCleanFromInput('contentTypeFieldCustomeOrder');
	}
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'contents_listblock', 'contentTypeItemsCount'))
		$vars['contentTypeItemsCount'] = pnVarCleanFromInput('contentTypeItemsCount');
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'contents_listblock', 'contentTypeDisplayPermission'))
		$vars['contentTypeDisplayPermission'] = pnVarCleanFromInput('contentTypeDisplayPermission');
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'contents_listblock', 'use_pager'))
		$vars['use_pager'] = pnVarCleanFromInput('use_pager');
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'contents_listblock', 'contentTypeColsCount'))
		$vars['contentTypeColsCount'] = pnVarCleanFromInput('contentTypeColsCount');
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'contents_listblock', 'dataArrangement'))
		$vars['dataArrangement'] = pnVarCleanFromInput('dataArrangement');
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'contents_listblock', 'contentTypeSelectedID'))
		$vars['contentTypeSelectedID'] = pnVarCleanFromInput('contentTypeSelectedID');
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'contents_listblock', 'contentTypeSelectingQuery'))
		$vars['contentTypeSelectingQuery'] = pnVarCleanFromInput('contentTypeSelectingQuery');
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'contents_listblock', 'contentTypeExtraSelect'))
		$vars['contentTypeExtraSelect'] = pnVarCleanFromInput('contentTypeExtraSelect');
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'contents_listblock', 'contentTypeExtraFrom'))
		$vars['contentTypeExtraFrom'] = pnVarCleanFromInput('contentTypeExtraFrom');
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'contents_listblock', 'donot_display_doplicate_items'))
		$vars['donot_display_doplicate_items'] = pnVarCleanFromInput('donot_display_doplicate_items');
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'contents_listblock', 'contentTypeWebDirectory')){
		$vars['contentTypeWebDirectory'] = pnVarCleanFromInput('contentTypeWebDirectory');
		$vars['contentWebDirectoryDeep'] = pnVarCleanFromInput('contentWebDirectoryDeep');
		$vars['currentDirectory'] = pnVarCleanFromInput('currentDirectory');
		if ($vars['currentDirectory'])
			$vars['contentTypeWebDirectory'] = '';
	}
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'contents_listblock', 'show_alphabetic_nav')){
		$vars['show_alphabetic_nav'] = pnVarCleanFromInput('show_alphabetic_nav');
		$vars['alphabeticSortField'] = pnVarCleanFromInput('alphabeticSortField');
	}
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'contents_listblock', 'search_sensitive'))
		$vars['search_sensitive'] = pnVarCleanFromInput('search_sensitive');
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'contents_listblock', 'admin_list'))
		$vars['admin_list'] = pnVarCleanFromInput('admin_list');
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'contents_listblock', 'apply_web_directory_view_limitation'))
		$vars['apply_web_directory_view_limitation'] = pnVarCleanFromInput('apply_web_directory_view_limitation');
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'contents_listblock', 'history'))
		$vars['history'] = pnVarCleanFromInput('history');
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'contents_listblock', 'subportal'))
		if ($GLOBALS['portal_id'] == 0)
			$vars['subportal'] = pnVarCleanFromInput('subportal');
	$blockinfo['content'] = pnBlockVarsToContent($vars);
	return $blockinfo;
}
?>