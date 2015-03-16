<?php
// Saman Portal
// Copyright (C) 2008 by the Saman Information Structure Development Team.
// http://www.sis-eg.com/
// ----------------------------------------------------------------------


function cdk_content_advanced_searchblockblock_init(){
	pnSecAddSchema('cdk:content_advanced_searchblock:', 'Block title::');
}

function cdk_content_advanced_searchblockblock_info(){
    return array('text_type' => 'content_advanced_searchblock',
                 'module' => 'cdk',
                 'text_type_long' => _CDK_SEARCH_ADVANCED_BLOCK,
                 'allow_multiple' => true,
                 'form_content' => false,
                 'form_refresh' => false,
                 'show_preview' => false,
				 'default_title' => _CDK_SEARCH_ADVANCED_BLOCK_TITLE,
				 'allow_user_add'  => 0,
				 'group' => 2,
				 'block_description' => _CDK_SEARCH_ADVANCED_BLOCK_DESCRIPTION,
				 'is_object' => true
                 );
}

$GLOBALS['object_settings']['content_advanced_searchblock'] = array('contentType', 'contentTypeTemplate', 'doNotSavePostVars');

function cdk_content_advanced_searchblockblock_display($blockinfo){
    if (!pnSecAuthAction(0, 'cdk::content_advanced_searchblock', "$blockinfo[title]::", ACCESS_READ)) 
    	return;
    	
    $vars = pnBlockVarsFromContent($blockinfo['content']);       
    $originalGetParams = $_GET;
    $_GET['ctp_id'] = $vars['contentType'];
    $_GET['template_id'] = $vars['contentTypeTemplate'];
    $_GET['result_block_id'] = $vars['resultBlockId'];
    $_GET['result_wd_id'] = $vars['resultWdId'];
    if ($vars['showResultFirst'] || !empty($_POST['SearchBtn']) || !empty($_POST['sisSearchWhereClause'][$_GET['result_block_id']]))
    	$GLOBALS['_sisShowSearchResultBlocks_'][$_GET['result_block_id']] = true;
    else
    	$GLOBALS['_sisShowSearchResultBlocks_'][$_GET['result_block_id']] = false;
    $_GET['show_result_first'] = $vars['showResultFirst'];
    $_GET['hide_items_group'] = $vars['hideItemsGroup'];    
    $_GET['donot_save_post_vars'] = $vars['doNotSavePostVars'];    
    $GLOBALS['_block_id_'] = $blockinfo['bid'];

	$blockinfo['content'] = pnModFunc('cdk', 'user', 'loadmodule', array('system'=>'cdk',
													 							  	 'sismodule' => 'block/content_advanced_search_block.php'));     
    $_GET = $originalGetParams;
	return themesideblock($blockinfo);	
}

function cdk_content_advanced_searchblockblock_modify($blockinfo){
    $vars = pnBlockVarsFromContent($blockinfo['content']);    
    	
    $types = pnModAPIFunc('cdk', 'user', 'getAccessibleTypes');    
	foreach ($types as $key => $value) {
		$selected = '';
		if ($value['ctp_id'] == $vars['contentType'])
			$selected = 'selected';
		$typesOptions .= "<option value='$value[ctp_id]' $selected >$value[title]</option>";
	}
	$allTemplates = pnModAPIFunc('cdk', 'user', 'getTypeTemplates', array('templateType'=>4, 'includeGeneral'=>true));
	$content = "<script>
				var contentTypeTemplates = new Array();
				var selectedTemplate = '$vars[contentTypeTemplate]';
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
		}				
	}
	$content .= "</script>";	
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'content_advanced_searchblock', 'contentType'))	
		$content .= "<tr>
						<td colspan='2'><br></td>
					</tr>
					<tr>
						<td class='caption' nowrap='nowrap'>"
							. _CNT_BLK_CONENT_TYPE . " : 
						</td>
						<td>
							<select name='contentType' id='cmbContentType' onchange='initContentTypeTemplateCombo();'>"
								. $typesOptions . "
							</select>
				   		</td>
					</tr>";
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'content_advanced_searchblock', 'contentTypeTemplate'))								
		$content .= "<tr>
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
	$content .= "<tr>
					<td class='caption'  nowrap='nowrap'>"
						. _CNT_BLK_CONENT_RESULT_BLOCK_ID . ":
					</td>
					<td>
						<input type='text' name='resultBlockId' style='width:50px' value='$vars[resultBlockId]'/>
					</td>
				</tr>
				<tr>
					<td class='caption'  nowrap='nowrap'>"
						. _CNT_BLK_CONENT_RESULT_WD_ID . ":
					</td>
					<td>
						<input type='text' name='resultWdId' style='width:50px' value='$vars[resultWdId]'/>
					</td>
				</tr>
				<tr>
					<td class='caption'  nowrap='nowrap'>
					</td>
					<td>
						<input type='checkbox' name='showResultFirst' ".($vars['showResultFirst']?'checked':'')." value='1' /> "._CDK_BLK_SHOW_FIRST_DATA."
					</td>
				</tr>
				<tr>
					<td class='caption'  nowrap='nowrap'>
					</td>
					<td>
						<input type='checkbox' name='hideItemsGroup' ".($vars['hideItemsGroup']?'checked':'')." value='1' /> "._CDK_BLK_NOT_SHOW_ITEMS_GROUP."
					</td>
				</tr>";
	if(0 && pnBlockIsObjectSetting($blockinfo['block_id'], 'content_advanced_searchblock', 'doNotSavePostVars')) {						
		$content .= "<tr>
						<td class='caption'  nowrap='nowrap'>
						</td>
						<td>
							<input type='checkbox' name='doNotSavePostVars' ".($vars['doNotSavePostVars']?'checked':'')." value='1' /> "._CNT_BLK_DONOT_SAVE_POST_VARS."
						</td>
					</tr>";
	}
	$content .= "<tr>
					<td colspan=\"2\"><br></td>
				</tr>";
    $output = new pnHTML();
	$output->SetInputMode(_PNH_VERBATIMINPUT);
	$output->Text($content);
    return $output->GetOutput();
}

function cdk_content_advanced_searchblockblock_update($blockinfo){
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'content_advanced_searchblock', 'contentType'))
		$vars['contentType'] = pnVarCleanFromInput('contentType');
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'content_advanced_searchblock', 'contentTypeTemplate'))								
		$vars['contentTypeTemplate'] = pnVarCleanFromInput('contentTypeTemplate');
	$vars['resultBlockId'] = pnVarCleanFromInput('resultBlockId');    
	$vars['resultWdId'] = pnVarCleanFromInput('resultWdId');
	$vars['showResultFirst'] = pnVarCleanFromInput('showResultFirst');    
	$vars['hideItemsGroup'] = pnVarCleanFromInput('hideItemsGroup');  
	$vars['doNotSavePostVars'] = pnVarCleanFromInput('doNotSavePostVars');  
		
	$blockinfo['content'] = pnBlockVarsToContent($vars);
	return $blockinfo;
}
?>