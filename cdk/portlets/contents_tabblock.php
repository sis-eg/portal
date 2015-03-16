<?php
// Saman Portal
// Copyright (C) 2008 by the Saman Information Structure Development Team.
// http://www.sis-eg.com/
// ----------------------------------------------------------------------


function cdk_contents_tabblockblock_init(){
	pnSecAddSchema('cdk:contents_tabblock:', 'Block title::');
}

function cdk_contents_tabblockblock_info(){
    return array('text_type' => 'contents_tabblock',
                 'module' => 'cdk',
                 'text_type_long' => _CDK_TAB_BLOCK,
                 'allow_multiple' => true,
                 'form_content' => false,
                 'form_refresh' => false,
                 'show_preview' => false,
				 'default_title' => _CDK_TAB_BLOCK_TITLE,
				 'allow_user_add'  => 0,
				 'group' => 2,
				 'block_description' => _CDK_TAB_BLOCK_DESCRIPTION,
				 'allow_subportal_add'  => 1,
				 'is_object' => true
                 );
}

$GLOBALS['object_settings']['contents_tabblock'] = '*';
/*
tab_title
typeCombo
templateCombo
count_items
paging
cols_num
selected_items
filter_items
wd_id
contentTypeField
contentTypeFieldOrder
subportal
*/
$GLOBALS['portlet_settings']['contents_tabblock'] = array('subportal' 			  => _CNT_BLK_SELECT_SUPORTAL,
														  'tabsettings' 		  => _CNT_BLK_TABS_SETTING,
														  'contentTypeFieldOrder' => _CNT_BLK_CONENT_TYPE_ORDERBY_FIELD);

$GLOBALS['portlet_settings_related']['contents_tabblock'] = array('contentTypeFieldOrder' => array('contentTypeField'),
																  'tabsettings'		  	  => array('tab_title','typeCombo','templateCombo','count_items','paging','cols_num','selected_items','filter_items','wd_id'));

function cdk_contents_tabblockblock_display($blockinfo){
    if (!pnSecAuthAction(0, 'cdk::contents_tabblock', "$blockinfo[title]::", ACCESS_READ)) 
    	return;

    $vars = pnBlockVarsFromContent($blockinfo['content']);
    $j=0;
    $randNum=rand(1,1000);
   	$output="";
   	$tabsBlockCount=$GLOBALS['tabsBlockCount'];
	 if ($GLOBALS['portal_id'] == 0 || $vars['subportal']) {
    	$_GET['subportal'] = $vars['subportal'];
    	$GLOBALS['_filter_subportal_contents_'] = $_GET['subportal'];
    }
   	if(!$tabsBlockCount){
   		$GLOBALS['tabsBlockCount']=1;
   		$tabsBlockCount=1;
   	}
   	else {
   		$GLOBALS['tabsBlockCount']++;
   		$tabsBlockCount++;
   	}
	$tabsCount = 0;
   	foreach ($vars['tab_title'] as $key=>$value) {
   		if($value!="" && $vars['typeCombo'][$key])
   			$tabsCount++;
   	}
   	$output .= "
			<table width='100%' cellpadding='0' cellspacing='0' style='border:1px solid #DCE1E7;border-collapse:collapse' id='tabsheet_list'>
				<tr>";
  	$idx = 0;
  	$randId =  rand();
  	$selectedTab = 1;
  	if ($_GET["fragment$blockinfo[bid]"])
  		$selectedTab = $_GET["fragment$blockinfo[bid]"];
   	foreach ($vars['tab_title'] as $key=>$value) {
   		if($value!="" && $vars['typeCombo'][$key]) {
			$idx++;
	   		$query = "module=cdk&type=user&func=loadmodule&system=cdk&sismodule=block/contents_template_block.php&ctp_id=".$vars['typeCombo'][$key]."&template_id=".$vars['templateCombo'][$key]."&item_count=".$vars['count_items'][$key]."&selected_id=".$vars['selected_items'][$key]."&where_clause=".base64_encode($vars['filter_items'][$key])."&web_directory_id=".$vars['wd_id'][$key]."&field=".$vars['contentTypeField']."&order=".$vars['contentTypeFieldOrder']."&standalone=1&codedWhereClause=1&randId=$randId&die=1&subportal=all";
	   		if ($selectedTab == $idx) {
		   		$orgGet = $_GET;
		   		parse_str($query, $_GET);
		   		unset($_GET['die']);
		   		$content = pnModFunc('cdk', 'user', 'loadmodule');
		   		$_GET = $orgGet;
				$className = "b3";
	   		}   				   		
			else 
				$className = "b4";

			if (!$_GET["fragment$blockinfo[bid]"]) {
				$aLink = $_SERVER['REQUEST_URI'];
				if (ereg("[?]", $aLink))
					$aLink .= "&fragment$blockinfo[bid]=$idx";
				else 
					$aLink .= "?fragment$blockinfo[bid]=$idx";
			}
			else 
				$aLink = '#a';
   			$output.="<td class='$className' id='td_{$randId}_$idx' nowrap style='cursor:pointer;padding:5px;width:".(100/$tabsCount)."%' onclick='this.getElementsByTagName(\"a\")[0].click(); return false;'>
   						<a href='$aLink' name='fragment$blockinfo[bid]_$idx' onclick='showContentItemC$randId(document.getElementById(\"td_{$randId}_$idx\"), \"index.php\" + \"?$query\"); return false;'>$value</a>
   					  </td>";
   		}
   	}
	$output .= "</tr>
			<tr>
				<td colspan='$idx'>
					<div style='background-repeat:no-repeat;background-position: center center;' id='div_{$randId}_contanier' class='b7'>
						$content
					</div>
				</td>
			</tr>
		</table><script>
			var oldIdC$randId = 'td_{$randId}_1';
			var animating_$randId = false;

			function showContentItemC$randId(tabObj, tabHref){
				if (oldIdC$randId == tabObj.id || animating_$randId)
					return;
				animating_$randId = true;						
				document.getElementById(oldIdC$randId).className = 'b4';
				document.getElementById(oldIdC$randId).setAttribute('old_content', document.getElementById('div_{$randId}_contanier').innerHTML);
				tabObj.className='b3';
				oldIdC$randId = tabObj.id;
				if (tabObj.getAttribute('old_content')) {
					document.getElementById('div_{$randId}_contanier').innerHTML = tabObj.getAttribute('old_content');
					animating_$randId = false;
				}
				else {
					document.getElementById('div_{$randId}_contanier').innerHTML = '';
					document.getElementById('div_{$randId}_contanier').style.backgroundImage = 'url($GLOBALS[themePath]/images/loading.gif)';
					$.ajax({
							 type:'GET',
							 url: tabHref,
							 success: function(data){ document.getElementById('div_{$randId}_contanier').innerHTML = data; evalAJAXJavaScripts(data); document.getElementById('div_{$randId}_contanier').style.backgroundImage = ''; animating_$randId = false;}
						});
				}
			}			
		</script>";  
    $blockinfo['content']=$output;
	return themesideblock($blockinfo);	
}

function cdk_contents_tabblockblock_modify($blockinfo){
    $vars = pnBlockVarsFromContent($blockinfo['content']);    
    
    $allTemplates = pnModAPIFunc('cdk', 'user', 'getTypeTemplates', array('templateType'=>3, 'includeGeneral'=>true));
    $types = pnModAPIFunc('cdk', 'user', 'getTypes');
    
    if ($GLOBALS['portal_id'] == 0 && pnModAvailable('subportal') && pnBlockIsObjectSetting($blockinfo['block_id'], 'contents_tabblock', 'subportal')) {
		$subportals = pnModAPIFunc('subportal', 'admin', 'getall', array('portalType'=>2));
		$subportalOptions = '<option value="0" '.($vars['subportal'] == 0?'selected':'').' >'._CNT_BLK_SELECT_SUPORTAL_MAIN_PORTAL.'</option>';		
		foreach ($subportals as $subportal) {
			$title = $subportal['etitle'];
			if (pnUserGetLang() == 'far')
				$title = $subportal['ptitle'];
			$subportalOptions .= '<option value="'.$subportal['id'].'" '.($vars['subportal'] == $subportal['id']?'selected':'').' >'.$title.'</option>';
		}
		$subportalOptions .= '<option value="all" '.($vars['subportal'] == 'all'?'selected':'').' >'._CNT_BLK_SELECT_SUPORTAL_ALL.'</option>';
				
		$output .= "<tr>
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
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'contents_tabblock', 'tabsettings')){	
	    $output.="<tr>
	    	<td colspan='2'>
	    	<table name='tb_list' id='tb_list' style='text-align:center' class='list'>
	    		<tr>
	    			<td class='listheader' style='width:10px;padding:0px;'>#</td>
	    			<td class='listheader'>"._TAB_TITLE."</td>
	    			<td class='listheader'>"._TAB_CONTENT_TYPE."</td>
	    			<td class='listheader'>"._TAB_TEMPLATE."</td>
	    			<td class='listheader'>"._TAB_COUNT_ITEMS."</td>
	    			<td class='listheader' width='1%'>"._TAB_PAGING."</td>
	    			<td class='listheader'>"._TAB_COLS_NUMBER."</td>
	    			<td class='listheader'>"._TAB_SELECTED_ITEMS."</td>
	    			<td class='listheader'>"._TAB_FILTER_ITEMS."</td>
	    			<td class='listheader' width='1%'>"._TAB_WD_ID."</td>
	    		</tr>";
	    $selected_tabs=$vars['tab_title'];
	    $rowNum=0;
	    foreach ($selected_tabs as $key=>$value){
	    	if ($value=="") 
	    		break;
	    	$pagingChecked=$vars['paging'][$key]?'checked':'';
	    	if (($rowNum%2)==0) 
	    		$rowClass="sp-even";
	    	else 
	    		$rowClass="sp-odd";
	    	$output.="<tr class='$rowClass'>
		    			<td class=".sisSetCellStyle($rowNum).">".($rowNum+1)."</td>
		    			<td class=".sisSetCellStyle($rowNum)."><input type='text' name='tab_title[]' value='$value' size='15' /></td>
		    			<td class=".sisSetCellStyle($rowNum).">".getTypeCombo($vars['typeCombo'][$key],$rowNum)."</td>
		    			<td class=".sisSetCellStyle($rowNum)."><div id='divTemplateCombo$rowNum'>".getTemplateCombo($vars['typeCombo'][$key],$vars['templateCombo'][$key])."</div></td>
		    			<td class=".sisSetCellStyle($rowNum)."><input type='text' name='count_items[]' value='".$vars['count_items'][$key]."' size='1' /></td>
		    			<td class=".sisSetCellStyle($rowNum)."><input type='checkbox' name='paging[]' $pagingChecked /></td>
		    			<td class=".sisSetCellStyle($rowNum)."><input type='text' name='cols_num[]' value='".$vars['cols_num'][$key]."' size='1' /></td>
		    			<td class=".sisSetCellStyle($rowNum)."><textarea name='selected_items[]' cols='8'>".$vars['selected_items'][$key]."</textarea></td>
		    			<td class=".sisSetCellStyle($rowNum)."><textarea name='filter_items[]' cols='8'>".$vars['filter_items'][$key]."</textarea></td>
		    			<td class=".sisSetCellStyle($rowNum)."><input type='text' name='wd_id[]' value='".$vars['wd_id'][$key]."' size='2' /></td>
		    		</tr>";
	    	$rowNum++;
	    }
	    for (;$rowNum<20;$rowNum++){
	    	if (($rowNum%2)==0) 
	    		$rowClass="sp-even";
	    	else 
	    		$rowClass="sp-odd";
	    	$output.="<tr class='$rowClass'>
		    			<td class=".sisSetCellStyle($rowNum).">".($rowNum+1)."</td>
		    			<td class=".sisSetCellStyle($rowNum)."><input type='text' name='tab_title[]' size='15' /></td>
		    			<td class=".sisSetCellStyle($rowNum).">".getTypeCombo('',$rowNum)."</td>
		    			<td class=".sisSetCellStyle($rowNum)."><div id='divTemplateCombo$rowNum'></div></td>
		    			<td class=".sisSetCellStyle($rowNum)."><input type='text' name='count_items[]' value='' size='1' /></td>
		    			<td class=".sisSetCellStyle($rowNum)."><input type='checkbox' name='paging[]'  /></td>
		    			<td class=".sisSetCellStyle($rowNum)."><input type='text' name='cols_num[]' size='1' /></td>
		    			<td class=".sisSetCellStyle($rowNum)."><textarea name='selected_items[]' cols='5'></textarea></td>
		    			<td class=".sisSetCellStyle($rowNum)."><textarea name='filter_items[]' cols='5' ></textarea></td>
		    			<td class=".sisSetCellStyle($rowNum)."><input type='text' name='wd_id[]'  size='2' /></td>
		    		</tr>";
	    }
	    $output.="</table>
	    		</td>
			</tr>
			<script>
	    		var currentTemplateCombo=-1;
	    		function fillComboTemplate(i) {
					var ctp_id=document.getElementById('typeCombo'+i).value;
					if(ctp_id && currentTemplateCombo==-1){
						SimpleAjax(host+'/index.php'+'?'+'module=cdk&type=user&func=getTypeTemplateCombo&standalone=1&ctp_id='+ctp_id,'GET', '', showComboTemplate, 'xmlRequest');
						currentTemplateCombo=i;
					}
				}
				function showComboTemplate(){
					if (document.xmlRequest.readyState == 4) {
						var tmpCombo=document.getElementById('divTemplateCombo'+currentTemplateCombo);
						tmpCombo.innerHTML='<select name=\"templateCombo[]\" style=\"width:80px\">'+document.xmlRequest.responseText+'</select>';
						currentTemplateCombo=-1;
					}
				}
			</script>";
	}
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'contents_tabblock', 'contentTypeFieldOrder'))
	   $output.= "<tr>
	   	<td>
	   		". _CNT_BLK_CONENT_TYPE_ORDERBY_FIELD . "
	   	</td>
	   	<td>
	   		<select name='contentTypeField' id='cmbContentTypeField' style='width:120px'>
		   		<option value='counter'  " . ($vars['contentTypeField'] == 'counter'?'selected':'') . ">"._CNT_BLK_CONENT_TYPE_FIELD_COUNTER."</option>
		   		<option value='display_start_date' " . ($vars['contentTypeField'] == 'display_start_date'?'selected':'') . ">"._CNT_BLK_CONENT_TYPE_FIELD_DISPLAY_START_DATE."</option>
		   		<option value='last_modified_date' " . ($vars['contentTypeField'] == 'last_modified_date'?'selected':'') . ">"._CNT_BLK_CONENT_TYPE_FIELD_LAST_MODIFIED_DATE."</option>
		   		<option value='page_title' " . ($vars['contentTypeField'] == 'page_title'?'selected':'') . ">"._CNT_BLK_CONENT_TYPE_FIELD_PAGE_TITLE."</option>
			</select>
			<select name='contentTypeFieldOrder' id='cmbcontentTypeFieldOrder'>
				<option value='ascending' " . ($vars['contentTypeFieldOrder'] == 'ascending'?'selected':'') . ">" . _CNT_BLK_CONENT_TYPE_ASCENDING . "</option>
				<option value='descending' " . ($vars['contentTypeFieldOrder'] == 'descending'?'selected':'') . ">" . _CNT_BLK_CONENT_TYPE_DESCENDING . "</option>
			</select>
	   	</td>";

    return $output;
}

function cdk_contents_tabblockblock_update($blockinfo){
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'contents_tabblock', 'tabsettings')){
		$vars['tab_title'] = pnVarCleanFromInput('tab_title');
		$vars['typeCombo'] = pnVarCleanFromInput('typeCombo');
		$vars['templateCombo'] = pnVarCleanFromInput('templateCombo');
		$vars['count_items'] = pnVarCleanFromInput('count_items');
		$vars['paging'] = pnVarCleanFromInput('paging');
		$vars['cols_num'] = pnVarCleanFromInput('cols_num');
		$vars['selected_items'] = pnVarCleanFromInput('selected_items');
		$vars['filter_items'] = pnVarCleanFromInput('filter_items');
		$vars['wd_id'] = pnVarCleanFromInput('wd_id');
	}
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'contents_tabblock', 'contentTypeFieldOrder')){
		$vars['contentTypeField'] = pnVarCleanFromInput('contentTypeField');
		$vars['contentTypeFieldOrder'] = pnVarCleanFromInput('contentTypeFieldOrder');
	}
	if($GLOBALS['portal_id'] == 0 && pnBlockIsObjectSetting($blockinfo['block_id'], 'contents_tabblock', 'subportal'))	
		$vars['subportal'] = pnVarCleanFromInput('subportal');

	$blockinfo['content'] = pnBlockVarsToContent($vars);
	return $blockinfo;
}
function getTypeCombo($ctp_id,$rowNum){
	 
	$types = pnModAPIFunc('cdk', 'user', 'getTypes');
	$output="
		<select name='typeCombo[]' id='typeCombo$rowNum' onchange='fillComboTemplate($rowNum)' style='width:80px;'>
			<option value='-1'></option>
    		
	";
	foreach ($types as $key => $value) {
		$selected = '';
		if ($value['ctp_id'] == $ctp_id)
			$selected = 'selected';
		$output .= "<option value='$value[ctp_id]' $selected >$value[title]</option>";
	}
	$output .= "</optgroup>";
	$output .= "</select>";

	return $output;
}
function getTemplateCombo($ctp_id, $templateName){
	$output="<select name='templateCombo[]' style='width:80px'>";
	if (!$ctp_id) {
		$output.="<option>-1</option></select>";
		return $output;
	}
	$typeTemplates=pnModAPIFunc('cdk','user','getTypeTemplates',array('ctpId'=>$ctp_id, 'templateType'=>3, 'includeGeneral'=>true));
	
	foreach ($typeTemplates as $key => $value) {
		$selected = '';
		if ($value == $templateName)
			$selected = 'selected';
		$output .= "<option value='$value' $selected >$value</option>";
	}
	
	$output.="</select>";
	
	return $output;
}
?>