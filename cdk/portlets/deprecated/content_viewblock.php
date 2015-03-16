<?php
// Saman Portal
// Copyright (C) 2008 by the Saman Information Structure Development Team.
// http://www.sis-eg.com/
// ----------------------------------------------------------------------


function cdk_content_viewblockblock_init(){
	pnSecAddSchema('cdk:content_viewblock:', 'Block title::');
}

function cdk_content_viewblockblock_info(){
    return array('text_type' => 'content_viewblock',
                 'module' => 'cdk',
                 'text_type_long' => _CDK_CONTNET_VIEW_BLOCK,
                 'allow_multiple' => true,
                 'form_content' => false,
                 'form_refresh' => false,
                 'show_preview' => false,
				 'default_title' => _CDK_CONTNET_VIEW_BLOCK_TITLE,
				 'allow_user_add'  => 0,
				 'group' => 2,
				 'block_description' => _CDK_CONTNET_VIEW_BLOCK_DESCRIPTION,
			     'allow_subportal_add'  => 1
                 );
}

function cdk_content_viewblockblock_display($blockinfo){
    if (!pnSecAuthAction(0, 'cdk::content_viewblock', "$blockinfo[title]::", ACCESS_READ)) 
    	return;
    
    $vars = pnBlockVarsFromContent($blockinfo['content']);  
    if (!$vars['contentId'] || !$vars['contentType'])
    	return;
    	
	list($dbconn) = pnDBGetConn();	
	pnModDBInfoLoad('cdk');
	$pntable = pnDBGetTables();
	$contentTable = $pntable['content'];
	$contentColumns = $pntable['content_column'];
	
	
    $sql = "SELECT 
    			$contentColumns[ctp_id],
    			$contentColumns[cnt_id],    			
    			$contentColumns[foreign_key_value]
    		FROM 
    			$contentTable
    		WHERE 
    			$contentColumns[foreign_key_value] = '$vars[contentId]' AND $contentColumns[ctp_id] = '$vars[contentType]'";
    
    $result = $dbconn->Execute($sql);
    if (!$result || !$result->fields[0])
    	return;
    $originalGetParams = $_GET;
    if (!$_GET['sisOp'])
		$_GET['sisOp'] = 'view';
	$_GET['ctp_id'] = $result->fields[0];
	$_GET['cnt_id'] = $result->fields[1];
	$_GET['id'] = $result->fields[2];
	$_GET['from_page'] = true;
	$_GET['module'] = 'cdk';
	$_GET['name'] = 'cdk';
	if ($vars['contentTypeTemplate'])
		$_GET['template'] = $vars['contentTypeTemplate'];
	$GLOBALS['_in_block_']=true;
	if ($_GET['sisOp'] == 'view')
		$blockinfo['content'] = pnModFunc('cdk', 'user', 'loadmodule', array('system'		=> 'cdk',
												 						 'sismodule'	=> 'user/content_view.php'));
	else
		$blockinfo['content'] = pnModFunc('cdk', 'user', 'loadmodule', array('system'		=> 'cdk',
												 						 'sismodule'	=> 'user/content_edit.php'));
	$GLOBALS['_in_block_']=false;
    $_GET = $originalGetParams;
	return themesideblock($blockinfo);	
}

function cdk_content_viewblockblock_modify($blockinfo){
    $vars = pnBlockVarsFromContent($blockinfo['content']);    
    
    $types = pnModAPIFunc('cdk', 'user', 'getAccessibleTypes');
	foreach ($types as $key => $value) {
		$selected = '';
		if ($value['ctp_id'] == $vars['contentType'])
			$selected = 'selected';
		$typesOptions .= "<option value='$value[ctp_id]' $selected >$value[title]</option>";
	}
	$allTemplates = pnModAPIFunc('cdk', 'user', 'getTypeTemplates');	
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
							opt.innerText = contentTypeTemplates[cmbContentType.value][idx].name;
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
	$content .= "</script>";	    
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
					<td class='caption' nowrap='nowrap'>"
						. _CNT_BLK_CONENT_ID . " : 
					</td>
					<td>
						<input type='text' size='4' name='contentId' value='$vars[contentId]' />
			   		</td>
				</tr>
				<tr>
					<td colspan=\"2\"><br></td>
				</tr>";
    $output = new pnHTML();
	$output->SetInputMode(_PNH_VERBATIMINPUT);
	$output->Text($content);
    return $output->GetOutput();
}

function cdk_content_viewblockblock_update($blockinfo){
	$vars = array();
	$vars['contentId'] = pnVarCleanFromInput('contentId');
	$vars['contentType'] = pnVarCleanFromInput('contentType');
	$vars['contentTypeTemplate'] = pnVarCleanFromInput('contentTypeTemplate');
	$blockinfo['content'] = pnBlockVarsToContent($vars);
	
    $dbconn =& pnDBGetConn(true);
    $result = $dbconn->Execute("SELECT 
    					det.sp_page_id
    				  FROM
    				  	saman_page_items pitm
    				  	INNER JOIN saman_pages_detail det
    				  		ON (pitm.sp_page_id = det.sp_page_detail_id)
    				  WHERE
    				  	pitm.sp_bid = '".pnVarCleanFromInput('page_id')."'");	
   	
	pnModAPIFunc('cdk', 'user', 'assginPage', array('ctp_id'=>$vars['contentType'], 'id'=>$vars['contentId'], 'page_id'=>$result->fields[0]));
	
	return $blockinfo;
}

function cdk_content_viewblockblock_delete($args){
	extract($args);
 	pnModAPIFunc('cdk', 'user', 'deassginPage', array('ctp_id'=>$vars['contentType'], 'id'=>$vars['contentId'], 'page_id'=>$main_page_id));
 	return true;
}
?>