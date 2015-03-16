<?php
// Saman Portal
// Copyright (C) 2008 by the Saman Information Structure Development Team.
// http://www.sis-eg.com/
// ----------------------------------------------------------------------


function cdk_content_formblockblock_init(){
	pnSecAddSchema('cdk:content_formblock:', 'Block title::');
}

function cdk_content_formblockblock_info(){
    return array('text_type' => 'content_formblock',
                 'module' => 'cdk',
                 'text_type_long' => _CDK_FORM_BLOCK_,
                 'allow_multiple' => true,
                 'form_content' => false,
                 'form_refresh' => false,
                 'show_preview' => false,
				 'default_title' => _CDK_FORM_BLOCK_TITLE,
				 'allow_user_add'  => 0,
				 'group' => 2,
    			 'block_description' => _CDK_FORM_BLOCK_DESCRIPTION,
    			 'is_object' => true,
    			 'allow_subportal_add'  => 1
                 );
}

$GLOBALS['object_settings']['content_formblock'] = array('contentType', 'operation', 'contentTypeNewTemplate', 'contentTypeEditTemplate', 'contentTypeViewTemplate');

$GLOBALS['portlet_settings']['content_formblock'] = array('contentType'				=> _CNT_BLK_CONTENT_TYPE,
														  'operation'				=> _CDK_BLK_FORM_OPERATION,
														  'recordId'				=> _CDK_BLK_FORM_ID,
														  'contentTypeNewTemplate'	=> _CDK_BLK_FORM_NEW_TEMPLATE,
														  'contentTypeEditTemplate'	=> _CDK_BLK_FORM_EDIT_TEMPLATE,
														  'contentTypeViewTemplate'	=> _CDK_BLK_FORM_VIEW_TEMPLATE,
														  'hideItemsGroup'			=> _CDK_BLK_HIDE_ITEMS_GROUP,
														  'ajaxBased'				=> _CDK_BLK_AJAX_FORM,
														  'showBackButton'			=> _CDK_BLK_SHOW_BACK_BUTTON);


function cdk_content_formblockblock_display($blockinfo){
    if (!pnSecAuthAction(0, 'cdk::content_formblock', "$blockinfo[title]::", ACCESS_READ)) 
    	return;

    $vars = pnBlockVarsFromContent($blockinfo['content']);

    $_GET['id'] = intval($_GET['id']);

    $orginalGetParams = $_GET;
    $GLOBALS['__GET'] = $_GET;

    if(!$_GET['ctp_id'] || $_GET['ctp_id'] != $vars['contentType']){
    	$_GET['ctp_id'] = $vars['contentType'];
   	 	if ($vars['recordId'])
    		$_GET['id'] = $vars['recordId'];
    }

    $_GET['show_back_button'] = $vars['showBackButton'];
    $_GET['ajax_based'] = $vars['ajaxBased'];
    $_GET['hide_items_group'] = $vars['hideItemsGroup'];
    $_GET['in_block'] = true;
    
    if (!$_GET['sisOp']) 
    	$_GET['sisOp'] = $vars['operation'];
	$_GET['operation'] = $vars['operation'];
	
    switch ($_GET['sisOp']) {
    	case 'new':
    		$_GET['template'] = $vars['contentTypeNewTemplate'];
    		$GLOBALS['sisReadOnly'] = false;
    	break;
    	case 'edit':
    		$_GET['template'] = $vars['contentTypeEditTemplate'];
    		$GLOBALS['sisReadOnly'] = false;
    	break;
    	case 'view':
    		$_GET['template'] = $vars['contentTypeViewTemplate'];
    		$GLOBALS['sisReadOnly'] = true;
    		if(!$vars['showHooks'])
    			$_GET['noBlockHooks']=1;
    	break;
    	default:
		    $_GET['templates'] = array('new' => $vars['contentTypeNewTemplate'],
		    						   'edit' => $vars['contentTypeEditTemplate'],
		    						   'view' => $vars['contentTypeViewTemplate']);
		break;    	
    }
	$GLOBALS['_block_id_'] = $blockinfo['bid'];

	$GLOBALS['_load_in_block_mode_'] = true;
	$blockinfo['content'] = pnModFunc('cdk', 'user', 'loadmodule', array('system'=>'cdk',
													 					  'sismodule' => 'block/content_form_block.php'
																		));
	$GLOBALS['_load_in_block_mode_'] = false;

	unset($GLOBALS['_block_id_']);
	unset($GLOBALS['__GET']);
	$_GET = $orginalGetParams;		
    unset($GLOBALS['_ContentTypeTemplateType_']);
	return themesideblock($blockinfo);	
}

function cdk_content_formblockblock_modify($blockinfo){
    $vars = pnBlockVarsFromContent($blockinfo['content']);
    if ($vars['contentTypeField'] == '' )
    	$vars['contentTypeField'] = 'last_modified_date';
    if ($vars['contentTypeFieldOrder'] == '' )
    	$vars['contentTypeFieldOrder'] = 'descending';
    	
    $types = pnModAPIFunc('cdk', 'user', 'getAccessibleTypes');
	foreach ($types as $key => $value) {
		$selected = '';
		if ($value['ctp_id'] == $vars['contentType'])
			$selected = 'selected';
		$typesOptions .= "<option value='$value[ctp_id]' $selected >$value[title]</option>";
	}
	$allTemplates = pnModAPIFunc('cdk', 'user', 'getTypeTemplates', array('templateType'=>2, 'includeGeneral'=>true));
	$content = "<script>
				var contentTypeTemplates = new Array();
				var selectedViewTemplate = '$vars[contentTypeViewTemplate]';
				var selectedEditTemplate = '$vars[contentTypeEditTemplate]';
				var selectedNewTemplate = '$vars[contentTypeNewTemplate]';
				
				function initContentTypeViewTemplateCombo() {
					cmbContentType = document.getElementById('cmbContentType');
					cmbContentTypeViewTemplate = document.getElementById('cmbContentTypeViewTemplate');
					cmbContentTypeViewTemplate.innerHTML = '';
					cmbContentTypeViewTemplate.appendChild(document.createElement('option'));
					if (contentTypeTemplates[cmbContentType.value] != null) 
						for(var idx = 0; idx < contentTypeTemplates[cmbContentType.value].length; idx++) {
							var opt = document.createElement('option');
							opt.value = contentTypeTemplates[cmbContentType.value][idx].name;
							opt.setAttribute('typeId', contentTypeTemplates[cmbContentType.value][idx].typeId);
							if (selectedViewTemplate == opt.value)
								opt.selected = true;
							opt.innerHTML = contentTypeTemplates[cmbContentType.value][idx].name;
							cmbContentTypeViewTemplate.appendChild(opt);
						}
				   selectedViewTemplate	= '';
				}
				function initContentTypeEditTemplateCombo() {
					cmbContentType = document.getElementById('cmbContentType');
					cmbContentTypeEditTemplate = document.getElementById('cmbContentTypeEditTemplate');
					cmbContentTypeEditTemplate.innerHTML = '';
					cmbContentTypeEditTemplate.appendChild(document.createElement('option'));
					if (contentTypeTemplates[cmbContentType.value] != null)
						for(var idx = 0; idx < contentTypeTemplates[cmbContentType.value].length; idx++) {
							var opt = document.createElement('option');
							opt.value = contentTypeTemplates[cmbContentType.value][idx].name;
							opt.setAttribute('typeId', contentTypeTemplates[cmbContentType.value][idx].typeId);
							if (selectedEditTemplate == opt.value)
								opt.selected = true;
							opt.innerHTML = contentTypeTemplates[cmbContentType.value][idx].name;
							cmbContentTypeEditTemplate.appendChild(opt);
						}
				   selectedEditTemplate	= '';
				}
				function initContentTypeNewTemplateCombo() {
					cmbContentType = document.getElementById('cmbContentType');
					cmbContentTypeNewTemplate = document.getElementById('cmbContentTypeNewTemplate');
					cmbContentTypeNewTemplate.innerHTML = '';
					cmbContentTypeNewTemplate.appendChild(document.createElement('option'));
					if (contentTypeTemplates[cmbContentType.value] != null)
						for(var idx = 0; idx < contentTypeTemplates[cmbContentType.value].length; idx++) {
							var opt = document.createElement('option');
							opt.value = contentTypeTemplates[cmbContentType.value][idx].name;
							opt.setAttribute('typeId', contentTypeTemplates[cmbContentType.value][idx].typeId);
							if (selectedNewTemplate == opt.value)
								opt.selected = true;
							opt.innerHTML = contentTypeTemplates[cmbContentType.value][idx].name;
							cmbContentTypeNewTemplate.appendChild(opt);
						}
				   selectedNewTemplate	= '';
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
	$content .= "</script>";
	$content .= "<tr>
					<td width='15%'><br></td>
					<td ><br></td>
				</tr>";
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'content_formblock', 'contentType'))
		$content .= "<tr>
						<td class='caption' nowrap='nowrap'>"
							. _CNT_BLK_CONTENT_TYPE . " : 
						</td>
						<td>
							<select name='contentType' id='cmbContentType' onchange='initContentTypeViewTemplateCombo(); initContentTypeEditTemplateCombo(); initContentTypeNewTemplateCombo();'>"
								. $typesOptions . "
							</select>
				   		</td>
					</tr>";
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'content_formblock', 'operation'))
		$content .= "<tr>
						<td class='caption' nowrap='nowrap'>"
							. _CDK_BLK_FORM_OPERATION . " : 
						</td>
						<td>
							<select name='operation' id='cmbOperation'>
								<option value='new' ".($vars['operation']=='new'?'selected':'').">" . _CDK_BLK_FORM_OPERATION_NEW . "</option>
								<option value='edit' ".($vars['operation']=='edit'?'selected':'').">" . _CDK_BLK_FORM_OPERATION_EDIT . "</option>
								<option value='view' ".($vars['operation']=='view'?'selected':'').">" . _CDK_BLK_FORM_OPERATION_VIEW . "</option>
								<option value='portal_one_content' ".($vars['operation']=='portal_one_content'?'selected':'').">" . _CDK_BLK_FORM_OPERATION_PORTAL_ONE_CONTENT . "</option>
								<option value='user_one_content' ".($vars['operation']=='user_one_content'?'selected':'').">" . _CDK_BLK_FORM_OPERATION_USER_ONE_CONTENT . "</option>
							</select>
				   		</td>
					</tr>";
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'content_formblock', 'recordId'))
		$content .="<tr>
						<td class='caption'  nowrap='nowrap'>"
							. _CDK_BLK_FORM_ID . ":
						</td>
						<td>
							<input type='text' size=10 name='recordId' id='txtRecordId' value='$vars[recordId]'/>
						</td>
					</tr>";
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'content_formblock', 'contentTypeNewTemplate'))
		$content .= "<tr>
						<td class='caption'  nowrap='nowrap'>"
							. _CDK_BLK_FORM_NEW_TEMPLATE . ":
						</td>
						<td>
							<select name='contentTypeNewTemplate' id='cmbContentTypeNewTemplate' style='width:120px'>
							</select>
							<script>
								initContentTypeNewTemplateCombo();
							</script>
						</td>
					</tr>";
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'content_formblock', 'contentTypeEditTemplate'))
		$content .= "<tr>
						<td class='caption'  nowrap='nowrap'>"
							. _CDK_BLK_FORM_EDIT_TEMPLATE . ":
						</td>
						<td>
							<select name='contentTypeEditTemplate' id='cmbContentTypeEditTemplate' style='width:120px'>
							</select>
							<script>
								initContentTypeEditTemplateCombo();
							</script>
						</td>
					</tr>";
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'content_formblock', 'contentTypeViewTemplate'))
		$content .= "<tr>
						<td class='caption'  nowrap='nowrap'>"
							. _CDK_BLK_FORM_VIEW_TEMPLATE . ":
						</td>
						<td>
							<select name='contentTypeViewTemplate' id='cmbContentTypeViewTemplate' style='width:120px'>
							</select>
							<script>
								initContentTypeViewTemplateCombo();
							</script>
						</td>
					</tr>";
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'content_formblock', 'hideItemsGroup'))
		$content .= "<tr>
						<td class='caption'  nowrap='nowrap'></td>
						<td>
							<input type='checkbox' name='hideItemsGroup' id='chkhideItemsGroup' value=1 ".($vars['hideItemsGroup']?'checked':'')."/> "._CDK_BLK_HIDE_ITEMS_GROUP."						
						</td>
					</tr>";
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'content_formblock', 'ajaxBased'))
		$content .= "<tr>
						<td class='caption'  nowrap='nowrap'></td>
						<td>
							<input type='checkbox' name='ajaxBased' id='chkAjaxBased' value=1 ".($vars['ajaxBased']?'checked':'')."/> "._CDK_BLK_AJAX_FORM."						
						</td>
					</tr>";
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'content_formblock', 'showBackButton'))
		$content .= "<tr>
						<td class='caption'  nowrap='nowrap'></td>
						<td>
							<input type='checkbox' name='showBackButton' id='chkShowBackButton' value=1 ".($vars['showBackButton']?'checked':'')."/> "._CDK_BLK_SHOW_BACK_BUTTON."
						</td>
					</tr>";
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'content_formblock', 'showHooks'))
		$content .= "<tr>
						<td class='caption'  nowrap='nowrap'></td>
						<td>
							<input type='checkbox' name='showHooks' id='showHooks' value=1 ".($vars['showHooks']?'checked':'')."/> "._CDK_BLK_SHOW_HOOKS."
							<span class='description'>("._CDK_BLK_SHOW_HOOKS_VIEW_MODE_ONLY.")</span>
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

function cdk_content_formblockblock_update($blockinfo){
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'content_formblock', 'contentType'))
		$vars['contentType'] = pnVarCleanFromInput('contentType');
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'content_formblock', 'operation'))
		$vars['operation'] = pnVarCleanFromInput('operation');
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'content_formblock', 'recordId'))
		$vars['recordId'] = pnVarCleanFromInput('recordId');
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'content_formblock', 'contentTypeNewTemplate'))
		$vars['contentTypeNewTemplate'] = pnVarCleanFromInput('contentTypeNewTemplate');
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'content_formblock', 'contentTypeEditTemplate'))
		$vars['contentTypeEditTemplate'] = pnVarCleanFromInput('contentTypeEditTemplate');
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'content_formblock', 'contentTypeViewTemplate'))
		$vars['contentTypeViewTemplate'] = pnVarCleanFromInput('contentTypeViewTemplate');
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'content_formblock', 'hideItemsGroup'))
		$vars['hideItemsGroup'] = pnVarCleanFromInput('hideItemsGroup');
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'content_formblock', 'ajaxBased'))
		$vars['ajaxBased'] = pnVarCleanFromInput('ajaxBased');
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'content_formblock', 'showBackButton'))
		$vars['showBackButton'] = pnVarCleanFromInput('showBackButton');
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'content_formblock', 'showHooks'))
		$vars['showHooks'] = pnVarCleanFromInput('showHooks');
	$blockinfo['content'] = pnBlockVarsToContent($vars);
	return $blockinfo;
}
?>