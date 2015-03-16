<?php
// Saman Portal
// Copyright (C) 2008 by the Saman Information Structure Development Team.
// http://www.sis-eg.com/
// ----------------------------------------------------------------------

function cdk_slider_contents_listblock_init(){
	pnSecAddSchema('cdk:slider_contents_list:', 'Block title::');
}

function cdk_slider_contents_listblock_info(){
    return array('text_type' => 'slider_contents_list',
                 'module' => 'cdk',
                 'text_type_long' => _CDK_SLIDER_LIST_BLOCK_,
                 'allow_multiple' => true,
                 'form_content' => false,
                 'form_refresh' => false,
                 'show_preview' => false,
				 'default_title' => _CDK_SLIDER_LIST_BLOCK_TITLE,
				 'allow_user_add'  => 0,
				 'group' => 2,
				 'block_description' => _CDK_SLIDER_LIST_BLOCK_DESCRIPTION,
			     'allow_subportal_add'  => 1,
				 'is_object' => true
                 );
}

$GLOBALS['object_settings']['slider_contents_list'] =array('contentType',
															'contentTypeTemplate',
															'contentTypeExtraSelect',
															'contentTypeExtraFrom',
															'subportal');
$GLOBALS['portlet_settings']['slider_contents_list'] = array('subportal' 						=> _CNT_BLK_SELECT_SUPORTAL_MAIN_PORTAL,
															  'contentType' 					=> _CNT_BLK_CONENT_TYPE,
															  'contentTypeTemplate' 			=> _CNT_BLK_CONENT_TYPE_TEMPLATE,
															  'contentTypeItemsCount' 			=> _CNT_BLK_CONENT_TYPE_ITEMS_COUNT,
															  'contentTypeFieldOrder' 			=> _CNT_BLK_CONENT_TYPE_ORDERBY_FIELD,
															  'contentTypeSelectedID' 			=> _CNT_BLK_CONENT_TYPE_SELECTED_ID,
															  'contentTypeExtraSelect' 			=> _CNT_BLK_EXTRA_SELECT_CLAUSE,
															  'contentTypeExtraFrom' 			=> _CNT_BLK_EXTRA_FROM_CLAUSE,
															  'contentTypeSelectingQuery' 		=> _CNT_BLK_CONENT_TYPE_WHERE_CLAUSE,
															  'contentTypeWebDirectory' 		=> _CNT_BLK_CONENT_TYPE_WEB_DIRECTORY,
															  'donot_display_doplicate_items' 	=> _CNT_BLK_CONENT_TYPE_DONOT_DISPLAY_DUPLICATE_ITEMS,
															  'sliderHeight' 					=> _CNT_BLK_WIDTH_AND_HEIGHT
																  );
$GLOBALS['portlet_settings_related']['slider_contents_list'] = array('contentTypeFieldOrder' => array('contentTypeField'),
																   	  'sliderHeight'		  => array('sliderWidth'));

function cdk_slider_contents_listblock_display($blockinfo){
    if (!pnSecAuthAction(0, 'cdk::slider_contents_list', "$blockinfo[title]::", ACCESS_READ))
    	return;
    $vars = pnBlockVarsFromContent($blockinfo['content']);
    $GLOBALS['originalGetParams'] = $_GET;
    if ($vars['contentType'] == '-1')
    	$_GET['ctp_id'] = pnVarCleanFromInput('ctp_id');
    else
    	$_GET['ctp_id'] = $vars['contentType'];

    $_GET['template_id'] = $vars['contentTypeTemplate'];
    $_GET['item_count'] = $vars['contentTypeItemsCount'];
    $_GET['selected_id'] = $vars['contentTypeSelectedID'];
    $_GET['where_clause'] = $vars['contentTypeSelectingQuery'];
    $_GET['web_directory_id'] = $vars['contentTypeWebDirectory'];
    $_GET['field'] = $vars['contentTypeField'];
    $_GET['order'] = $vars['contentTypeFieldOrder'];
    $_GET['empty_result'] = true;
    $_GET['sliderHeight'] = $vars['sliderHeight'];
    $_GET['sliderWidth'] = $vars['sliderWidth'];
    $_GET['sliderItemHeight'] = $vars['sliderItemHeight'];
    $_GET['sliderItemWidth'] = $vars['sliderItemWidth'];
    
    $_GET['_blockId'] = $blockinfo['bid'];
    $GLOBALS['extra_select_clause'] = $vars['contentTypeExtraSelect'];
    $GLOBALS['extra_from_clause'] = $vars['contentTypeExtraFrom'];
    if ($GLOBALS['portal_id'] == 0 || $vars['subportal']) {
    	$_GET['subportal'] = $vars['subportal'];
    	$GLOBALS['_filter_subportal_contents_'] = $_GET['subportal'];
    }
    $GLOBALS['cdk_doplicate_items'][$_GET['ctp_id']] = $vars['donot_display_doplicate_items'];
    $oldOp = $GLOBALS['sisOp'];
	$blockinfo['content'] = pnModFunc('cdk', 'user', 'loadmodule', array('system'=>'cdk',
													 					 'sismodule' => 'block/slider_contents_template_block.php'));
	$GLOBALS['sisOp'] = $oldOp;
	$_GET = $GLOBALS['originalGetParams'];
	unset($GLOBALS['originalGetParams']);
    unset($GLOBALS['extra_select_clause']);
    unset($GLOBALS['extra_from_clause']);

    if (!$blockinfo['content'])
		return;		
	return themesideblock($blockinfo);	
}

function cdk_slider_contents_listblock_modify($blockinfo){
    $vars = pnBlockVarsFromContent($blockinfo['content']);
    if ($vars['contentTypeField'] == '' )
    	$vars['contentTypeField'] = 'last_modified_date';
    if ($vars['contentTypeFieldOrder'] == '' )
    	$vars['contentTypeFieldOrder'] = 'descending';
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
							opt.innerHTML = contentTypeTemplates[cmbContentType.value][idx].name;
							cmbContentTypeTemplate.appendChild(opt);
						}
				   selectedTemplate	= '';
				}
				function initContentTypeFieldCombo() {
					cmbContentType = document.getElementById('cmbContentType');
					cmbContentTypeField = document.getElementById('cmbContentTypeField');
					cmbContentTypeField2 = document.getElementById('cmbContentTypeField2');
					cmbContentTypeField.innerHTML = '';
					cmbContentTypeField2.innerHTML = '';
					var opt = document.createElement('option');
					opt.value = 'rand()';
					if (selectedField == opt.value)
						opt.selected = true;
					opt.innerHTML = '"._CNT_BLK_CONENT_TYPE_ORDERBY_RANDOM."';
					cmbContentTypeField.appendChild(opt);
					if (contentTypeFields[cmbContentType.value] != null) 
						for(var idx = 0; idx < contentTypeFields[cmbContentType.value].length; idx++) {
							opt = document.createElement('option');
							opt.value = contentTypeFields[cmbContentType.value][idx].name;
							if (selectedField == opt.value)
								opt.selected = true;
							opt.innerHTML = contentTypeFields[cmbContentType.value][idx].caption;
							cmbContentTypeField.appendChild(opt);

							opt2 = document.createElement('option');
							opt2.value = contentTypeFields[cmbContentType.value][idx].name;
							opt2.innerHTML = contentTypeFields[cmbContentType.value][idx].caption;
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
	if ($GLOBALS['portal_id'] == 0 && pnModAvailable('subportal')) {
		$subportals = pnModAPIFunc('subportal', 'admin', 'getall', array('portalType'=>2));
		$subportalOptions = '<option value="0" '.($vars['subportal'] == 0?'selected':'').' >'._CNT_BLK_SELECT_SUPORTAL_MAIN_PORTAL.'</option>';
		foreach ($subportals as $subportal) {
			$title = $subportal['etitle'];
			if (pnUserGetLang() == 'far')
				$title = $subportal['ptitle'];
			$subportalOptions .= '<option value="'.$subportal['id'].'" '.($vars['subportal'] == $subportal['id']?'selected':'').' >'.$title.'</option>';
		}
		$subportalOptions .= '<option value="all" '.($vars['subportal'] == 'all'?'selected':'').' >'._CNT_BLK_SELECT_SUPORTAL_ALL.'</option>';
		if(pnBlockIsObjectSetting($blockinfo['block_id'], 'slider_contents_list', 'subportal'))
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
	$content .= "<tr>
					<td colspan='2'><br></td>
				</tr>";
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'slider_contents_list', 'contentType'))
		$content .= "
				<tr>
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

	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'slider_contents_list', 'contentTypeTemplate'))
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
						<span class='itemdescription'>"._CDK_BLK_FREE_LYOUT."</span>
					</td>
				</tr>
				<tr>
					<td>
					</td>
					<td>
						<img id='imgTemplate' src='services/cdk/images/noimage.png' style='padding:3px;background-color:#fff;border:1px solid #aaa'/>
					</td>
				</tr>";
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'slider_contents_list', 'contentTypeItemsCount'))
		$content .= "
			<tr>
					<td class='caption'  nowrap='nowrap'>"
						. _CNT_BLK_CONENT_TYPE_ITEMS_COUNT . ":
					</td>
					<td>
						<input type='text' name='contentTypeItemsCount' id='txtContentTypeItemsCount' style='width:50px' value='$vars[contentTypeItemsCount]'/>
					</td>
				</tr>";
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'slider_contents_list', 'contentTypeFieldOrder'))
		$content .= "
				<tr>
					<td class='caption'  nowrap='nowrap'>"
						. _CNT_BLK_CONENT_TYPE_ORDERBY_FIELD . ":
					</td>
					<td>
						<select name='contentTypeField' id='cmbContentTypeField' style='width:120px'>
						</select>
						<select name='contentTypeFieldOredr' id='cmbContentTypeFieldOredr'>
							<option value='ascending' " . ($vars['contentTypeFieldOrder'] == 'ascending'?'selected':'') . ">" . _CNT_BLK_CONENT_TYPE_ASCENDING . "</option>
							<option value='descending' " . ($vars['contentTypeFieldOrder'] == 'descending'?'selected':'') . ">" . _CNT_BLK_CONENT_TYPE_DESCENDING . "</option>
						</select>
					</td>
				</tr>";
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'slider_contents_list', 'contentTypeSelectedID'))
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
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'slider_contents_list', 'contentTypeExtraSelect'))
			$content .= "
				<tr>
					<td class='caption'  nowrap='nowrap' style='vertical-align:top' valign='top'>"
						. _CNT_BLK_EXTRA_SELECT_CLAUSE . ":
					</td>
					<td>
						<textarea name='contentTypeExtraSelect' rows='5' cols='50' style='direction:ltr'>$vars[contentTypeExtraSelect]</textarea>
					</td>
				</tr>";
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'slider_contents_list', 'contentTypeExtraFrom'))
			$content .= "
				<tr>
					<td class='caption'  nowrap='nowrap' style='vertical-align:top' valign='top'>"
						. _CNT_BLK_EXTRA_FROM_CLAUSE . ":
					</td>
					<td>
						<textarea name='contentTypeExtraFrom' rows='5' cols='50' style='direction:ltr'>$vars[contentTypeExtraFrom]</textarea>
					</td>
				</tr>";
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'slider_contents_list', 'contentTypeSelectingQuery'))
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
					</td>
				</tr>";
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'slider_contents_list', 'contentTypeWebDirectory'))
		$content .= "
				<tr>
					<td class='caption'  nowrap='nowrap' style='vertical-align:top' valign='top'>"
						. _CNT_BLK_CONENT_TYPE_WEB_DIRECTORY . ":
					</td>
					<td>
						<input name='contentTypeWebDirectory' value='$vars[contentTypeWebDirectory]' size='4' />
					</td>
				</tr>";
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'slider_contents_list', 'donot_display_doplicate_items'))
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
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'slider_contents_list', 'sliderHeight'))
		$content .= "
				<tr>
					<td class='caption'  nowrap='nowrap' style='vertical-align:top' valign='top'>
					"._CNT_BLK_WIDTH_AND_HEIGHT.":
					</td>
					<td>
						<span class='itemdescription'>px</span><INPUT value='".$vars['sliderHeight']."' type='input' name='sliderHeight' size='3' >*<INPUT value='".$vars['sliderWidth']."' type='input' name='sliderWidth' size='3' >
					</td>
				</tr>
				<tr>
					<td class='caption'  nowrap='nowrap' style='vertical-align:top' valign='top'>
					"._CNT_BLK_ITEM_WIDTH_AND_HEIGHT.":
					</td>
					<td>
						<span class='itemdescription'>px</span><INPUT value='".$vars['sliderItemHeight']."' type='input' name='sliderItemHeight' size='3' >*<INPUT value='".$vars['sliderItemWidth']."' type='input' name='sliderItemWidth' size='3' >
					</td>
				</tr>";
	
	$content .="<tr>
					<td colspan=\"2\"><br></td>
				</tr>";
    $output = new pnHTML();
	$output->SetInputMode(_PNH_VERBATIMINPUT);
	$output->Text($content);
    return $output->GetOutput();
}

function cdk_slider_contents_listblock_update($blockinfo){
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'slider_contents_list', 'contentType'))
		$vars['contentType'] = pnVarCleanFromInput('contentType');
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'slider_contents_list', 'contentTypeTemplate'))
		$vars['contentTypeTemplate'] = pnVarCleanFromInput('contentTypeTemplate');
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'slider_contents_list', 'contentTypeFieldOrder')){
		$vars['contentTypeField'] = pnVarCleanFromInput('contentTypeField');
		$vars['contentTypeFieldOrder'] = pnVarCleanFromInput('contentTypeFieldOredr');
	}
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'slider_contents_list', 'contentTypeItemsCount'))
		$vars['contentTypeItemsCount'] = pnVarCleanFromInput('contentTypeItemsCount');
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'slider_contents_list', 'contentTypeSelectedID'))
		$vars['contentTypeSelectedID'] = pnVarCleanFromInput('contentTypeSelectedID');
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'slider_contents_list', 'contentTypeExtraSelect'))
		$vars['contentTypeExtraSelect'] = pnVarCleanFromInput('contentTypeExtraSelect');
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'slider_contents_list', 'contentTypeExtraFrom'))
		$vars['contentTypeExtraFrom'] = pnVarCleanFromInput('contentTypeExtraFrom');
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'slider_contents_list', 'contentTypeSelectingQuery'))
		$vars['contentTypeSelectingQuery'] = pnVarCleanFromInput('contentTypeSelectingQuery');
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'slider_contents_list', 'donot_display_doplicate_items'))
		$vars['donot_display_doplicate_items'] = pnVarCleanFromInput('donot_display_doplicate_items');
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'slider_contents_list', 'contentTypeWebDirectory'))
		$vars['contentTypeWebDirectory'] = pnVarCleanFromInput('contentTypeWebDirectory');
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'slider_contents_list', 'sliderHeight')){
		$vars['sliderWidth']  = pnVarCleanFromInput('sliderWidth');
		$vars['sliderHeight'] = pnVarCleanFromInput('sliderHeight');
		$vars['sliderItemWidth']  = pnVarCleanFromInput('sliderItemWidth');
		$vars['sliderItemHeight'] = pnVarCleanFromInput('sliderItemHeight');
	}
	
	
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'slider_contents_list', 'subportal'))
		if ($GLOBALS['portal_id'] == 0)
			$vars['subportal'] = pnVarCleanFromInput('subportal');
	$blockinfo['content'] = pnBlockVarsToContent($vars);
	return $blockinfo;
}
?>