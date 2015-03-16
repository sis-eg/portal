<?php
// Saman Portal
// Copyright (C) 2008 by the Saman Information Structure Development Team.
// http://www.sis-eg.com/
// ----------------------------------------------------------------------


function cdk_contents_marqueeblock_init(){
	pnSecAddSchema('cdk:contents_marquee:', 'Block title::');
}

function cdk_contents_marqueeblock_info(){
    return array('text_type' => 'contents_marquee',
                 'module' => 'cdk',
                 'text_type_long' => _CDK_CONTENT_MARQUEE_BLOCK,
                 'allow_multiple' => true,
                 'form_content' => false,
                 'form_refresh' => false,
                 'show_preview' => false,
				 'default_title' => _CDK_CONTENT_MARQUEE_BLOCK_TITLE,
				 'allow_user_add'  => 0,
				 'group' => 2,
				 'block_description' => _CDK_CONTNETS_MARQUEE_BLOCK_DESCRIPTION,
			     'allow_subportal_add'  => 1,
			     'is_object' => true
                 );
}

$GLOBALS['object_settings']['contents_marquee'] = '*';

$GLOBALS['portlet_settings']['contents_marquee'] = array('contentType' 						=> _CNT_BLK_CONENT_TYPE,
														  'contentTypeField'				=> _CNT_BLK_CONENT_TYPE_ORDERBY_FIELD,
														  'bulletImage' 					=> _CNT_BLK_BULLET_IMAGE,
														  'contentTypeItemsCount' 			=> _CNT_BLK_CONENT_TYPE_ITEMS_COUNT,
														  'contentTypeSelectedID' 			=> _CNT_BLK_CONENT_TYPE_SELECTED_ID,
														  'contentTypeSelectingQuery' 		=> _CNT_BLK_CONENT_TYPE_WHERE_CLAUSE,
														  'donot_display_doplicate_items'	=> _CNT_BLK_CONENT_TYPE_DONOT_DISPLAY_DUPLICATE_ITEMS,
														  'contentTypeWebDirectory' 		=> _CNT_BLK_CONENT_TYPE_WEB_DIRECTORY,
														  'titleLength' 					=> _CNT_BLK_TITLE_LENGTH,
														  'subportal' 						=> _CNT_BLK_SELECT_SUPORTAL
														  );

$GLOBALS['portlet_settings_related']['contents_marquee'] = array('contentTypeField' 		=> array('contentTypeFieldOrder'),
																 'contentTypeWebDirectory' 	=> array('currentDirectory'));

function cdk_contents_marqueeblock_display($blockinfo){
    if (!pnSecAuthAction(0, 'cdk::contents_marquee', "$blockinfo[title]::", ACCESS_READ)) 
    	return;
    	
    $vars = pnBlockVarsFromContent($blockinfo['content']);
    $GLOBALS['originalGetParams'] = $_GET;
    if ($vars['titleLength'] == '' )
    	$vars['titleLength'] = 100;
    if ($vars['contentType'] == '-1')
    	$_GET['ctp_id'] = pnVarCleanFromInput('ctp_id');
    else
    	$_GET['ctp_id'] = $vars['contentType'];
	
    $_GET['bullet_image'] = $vars['bulletImage'];
    $_GET['item_count'] = $vars['contentTypeItemsCount'];
    $_GET['titleLength'] = $vars['titleLength'];
    $_GET['selected_id'] = $vars['contentTypeSelectedID'];
    $_GET['where_clause'] = $vars['contentTypeSelectingQuery'];
    $_GET['web_directory_id'] = $vars['contentTypeWebDirectory'];
    if ($vars['currentDirectory'])
    	$_GET['web_directory_id'] = $GLOBALS['wd_id'];
    $_GET['field'] = $vars['contentTypeField'];
    $_GET['order'] = $vars['contentTypeFieldOrder'];
    $_GET['titleLength'] = $vars['titleLength'];
    $_GET['empty_result'] = true;
    if ($GLOBALS['portal_id'] == 0 || $vars['subportal']) {
    	$_GET['subportal'] = $vars['subportal'];
    	$GLOBALS['_filter_subportal_contents_'] = $_GET['subportal'];
    }
    $GLOBALS['cdk_doplicate_items'][$_GET['ctp_id']] = $vars['donot_display_doplicate_items'];
    $oldOp = $GLOBALS['sisOp'];
	$blockinfo['content'] = pnModFunc('cdk', 'user', 'loadmodule', array('system'=>'cdk',
													 					 'sismodule' => 'block/contents_marquee_block.php'));

	$GLOBALS['sisOp'] = $oldOp;
	$_GET = $GLOBALS['originalGetParams'] ;
	unset($GLOBALS['originalGetParams'] );
	if ($blockinfo['content']=="")
		return ;

	return themesideblock($blockinfo);
}

function cdk_contents_marqueeblock_modify($blockinfo){
    $vars = pnBlockVarsFromContent($blockinfo['content']);
    if ($vars['contentTypeField'] == '' )
    	$vars['contentTypeField'] = 'last_modified_date';
    if ($vars['contentTypeFieldOrder'] == '' )
    	$vars['contentTypeFieldOrder'] = 'descending';
     if ($vars['titleLength'] == '' )
    	$vars['titleLength'] = 100;

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
	$content = "<script>
				var contentTypeFields = new Array();
				var selectedField = '$vars[contentTypeField]';
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
				}";
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
	$content .= "<tr>
					<td colspan='2'><br></td>
				</tr>";
	if ($GLOBALS['portal_id'] == 0 && pnModAvailable('subportal') && pnBlockIsObjectSetting($blockinfo['block_id'],'contents_marquee','subportal')) {
		$subportals = pnModAPIFunc('subportal', 'admin', 'getall', array('portalType'=>2));
		$subportalOptions = '<option value="0" '.($vars['subportal'] == 0?'selected':'').' >'._CNT_BLK_SELECT_SUPORTAL_MAIN_PORTAL.'</option>';
		foreach ($subportals as $subportal) {
			$title = $subportal['etitle'];
			if (pnUserGetLang() == 'far')
				$title = $subportal['ptitle'];
			$subportalOptions .= '<option value="'.$subportal['id'].'" '.($vars['subportal'] == $subportal['id']?'selected':'').' >'.$title.'</option>';
		}
		$subportalOptions .= '<option value="all" '.($vars['subportal'] == 'all'?'selected':'').' >'._CNT_BLK_SELECT_SUPORTAL_ALL.'</option>';

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
	if(pnBlockIsObjectSetting($blockinfo['block_id'],'contents_marquee','contentType'))	
		$content .= "<tr>
						<td class='caption' nowrap='nowrap'>"
							. _CNT_BLK_CONENT_TYPE . " : 
						</td>
						<td>
							<select name='contentType' id='cmbContentType' onchange='initContentTypeFieldCombo();'>"
								. $typesOptions . "
							</select>
				   		</td>
					</tr>";
	if(pnBlockIsObjectSetting($blockinfo['block_id'],'contents_marquee','titleLength'))	
		$content .= "<tr>
						<td class='caption'  nowrap='nowrap'>"
							. _CNT_BLK_TITLE_LENGTH . ":
						</td>
						<td>
							<input type='text' name='titleLength' id='titleLength' style='width:20px' value='$vars[titleLength]'/>
						</td>
					</tr>";
	if(pnBlockIsObjectSetting($blockinfo['block_id'],'contents_marquee','bulletImage'))	
		$content .= "<tr>
						<td class='caption'  nowrap='nowrap'>"
							. _CNT_BLK_BULLET_IMAGE . ":
						</td>
						<td>
							<input type='text' name='bulletImage' id='txtbulletImage' style='width:500px' value='$vars[bulletImage]'/>
						</td>
					</tr>";
	if(pnBlockIsObjectSetting($blockinfo['block_id'],'contents_marquee','contentTypeItemsCount'))
		$content .= "<tr>
						<td class='caption'  nowrap='nowrap'>"
							. _CNT_BLK_CONENT_TYPE_ITEMS_COUNT . ":
						</td>
						<td>
							<input type='text' name='contentTypeItemsCount' id='txtContentTypeItemsCount' style='width:50px' value='$vars[contentTypeItemsCount]'/>
						</td>
					</tr>";
	if(pnBlockIsObjectSetting($blockinfo['block_id'],'contents_marquee','contentTypeField'))
		$content .= "<tr>
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
	if(pnBlockIsObjectSetting($blockinfo['block_id'],'contents_marquee','contentTypeSelectedID'))
		$content .= "<tr>
						<td class='caption'  nowrap='nowrap' style='vertical-align:top' valign='top'>"
							. _CNT_BLK_CONENT_TYPE_SELECTED_ID . ":
						</td>
						<td>
							<textarea name='contentTypeSelectedID' id='memcontentTypeSelectedID' rows='5' cols='50'>$vars[contentTypeSelectedID]</textarea>
							<br/>
							<span class='itemdescription'>" . _CNT_BLK_CONENT_TYPE_CUSTOM_ID_DESC . "</span>
						</td>
					</tr>";
	if(pnBlockIsObjectSetting($blockinfo['block_id'],'contents_marquee','contentTypeSelectingQuery'))
		$content .= "<tr>
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
	if(pnBlockIsObjectSetting($blockinfo['block_id'],'contents_marquee','contentTypeWebDirectory'))	
		$content .= "<tr>
					<td class='caption'  nowrap='nowrap' style='vertical-align:top' valign='top'>"
						. _CNT_BLK_CONENT_TYPE_WEB_DIRECTORY . ":
					</td>
					<td>
						<input name='contentTypeWebDirectory' value='$vars[contentTypeWebDirectory]' size='4' />
						&nbsp;
						<INPUT value=1 " . ($vars['currentDirectory'] == 1?'CHECKED':'') . " type='checkbox' name='currentDirectory'>&nbsp;"._CNT_BLK_DISPLAY_BASE_CURRENT_DIRECTORY."
					</td>
				</tr>";
	if(pnBlockIsObjectSetting($blockinfo['block_id'],'contents_marquee','donot_display_doplicate_items'))
		$content .= "<tr>
						<td class='caption'  nowrap='nowrap' style='vertical-align:top' valign='top'>
						</td>
						<td>
							<INPUT value=1 " . ($vars['donot_display_doplicate_items'] == 1?'CHECKED':'') . " type='checkbox' name='donot_display_doplicate_items'>
							" . _CNT_BLK_CONENT_TYPE_DONOT_DISPLAY_DUPLICATE_ITEMS  . "
							<br/>
							<span class='itemdescription'>" . _CNT_BLK_CONENT_TYPE_DONOT_DISPLAY_DUPLICATE_ITEMS_DESC . "</span>
						</td>
					</tr>";
	$content .= "<tr>
					<td colspan=\"2\"><br></td>
				</tr>";
    $output = new pnHTML();
	$output->SetInputMode(_PNH_VERBATIMINPUT);
	$output->Text($content);
    return $output->GetOutput();
}

function cdk_contents_marqueeblock_update($blockinfo){
	if(pnBlockIsObjectSetting($blockinfo['block_id'],'contents_marquee','contentType'))	
		$vars['contentType'] = pnVarCleanFromInput('contentType');
	if(pnBlockIsObjectSetting($blockinfo['block_id'],'contents_marquee','contentTypeField')){
		$vars['contentTypeField'] = pnVarCleanFromInput('contentTypeField');
		$vars['contentTypeFieldOrder'] = pnVarCleanFromInput('contentTypeFieldOredr');
	}
	if(pnBlockIsObjectSetting($blockinfo['block_id'],'contents_marquee','bulletImage'))	
		$vars['bulletImage'] = pnVarCleanFromInput('bulletImage');
	if(pnBlockIsObjectSetting($blockinfo['block_id'],'contents_marquee','contentTypeItemsCount'))
		$vars['contentTypeItemsCount'] = pnVarCleanFromInput('contentTypeItemsCount');
	if(pnBlockIsObjectSetting($blockinfo['block_id'],'contents_marquee','contentTypeSelectedID'))
		$vars['contentTypeSelectedID'] = pnVarCleanFromInput('contentTypeSelectedID');
	if(pnBlockIsObjectSetting($blockinfo['block_id'],'contents_marquee','contentTypeSelectingQuery'))
		$vars['contentTypeSelectingQuery'] = pnVarCleanFromInput('contentTypeSelectingQuery');
	if(pnBlockIsObjectSetting($blockinfo['block_id'],'contents_marquee','donot_display_doplicate_items'))
		$vars['donot_display_doplicate_items'] = pnVarCleanFromInput('donot_display_doplicate_items');
	if(pnBlockIsObjectSetting($blockinfo['block_id'],'contents_marquee','contentTypeWebDirectory'))	{
		$vars['contentTypeWebDirectory'] = pnVarCleanFromInput('contentTypeWebDirectory');
		$vars['currentDirectory'] = pnVarCleanFromInput('currentDirectory');
		if ($vars['currentDirectory'])
			$vars['contentTypeWebDirectory'] = '';
	}
	if(pnBlockIsObjectSetting($blockinfo['block_id'],'contents_marquee','titleLength'))	
		$vars['titleLength'] = pnVarCleanFromInput('titleLength');
	if ($GLOBALS['portal_id'] == 0 && pnBlockIsObjectSetting($blockinfo['block_id'],'contents_marquee','subportal'))
		$vars['subportal'] = pnVarCleanFromInput('subportal');

	$blockinfo['content'] = pnBlockVarsToContent($vars);
	return $blockinfo;
}
?>