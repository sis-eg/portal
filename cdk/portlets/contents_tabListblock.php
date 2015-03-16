<?php
// Saman Portal
// Copyright (C) 2008 by the Saman Information Structure Development Team.
// http://www.sis-eg.com/
// ----------------------------------------------------------------------

function cdk_contents_tabListListblockblock_init(){
	pnSecAddSchema('cdk:contents_tabListblock:', 'Block title::');
}

function cdk_contents_tabListblockblock_info(){
    return array('text_type' => 'contents_tabListblock',
                 'module' => 'cdk',
                 'text_type_long' => _CDK_TABLIST_BLOCK,
                 'allow_multiple' => true,
                 'form_content' => false,
                 'form_refresh' => false,
                 'show_preview' => false,
				 'default_title' => _CDK_TABLIST_BLOCK_TITLE,
				 'allow_user_add'  => 0,
				 'group' => 2,
				 'block_description' => _CDK_TABLIST_BLOCK_DESCRIPTION,
				 'allow_subportal_add'  => 1,
				 'is_object' => true
                 );
}

$GLOBALS['object_settings']['contents_tabListblock'] = '*';

$GLOBALS['portlet_settings']['contents_tabListblock'] = array('subportal' 				=> _CNT_BLK_SELECT_SUPORTAL,
															  'tabsettings'				=> _CNT_BLK_TABS_SETTING,
															  'contentTypeFieldOrder' 	=> _CNT_BLK_CONENT_TYPE_ORDERBY_FIELD,
															  'templateSize'		=> _CNT_BLK_TEMPLATE_SIZE
															  );
$GLOBALS['portlet_settings_related']['contents_tabListblock'] = array('contentTypeFieldOrder' 	=> array('contentTypeField'),
																   	  'tabsettings'		  	  	=> array('tab_title','typeCombo','templateCombo','templateCombo2','count_items','selected_items','filter_items','wd_id'),
																   	  'templateSize'			=> array('bigTemplateWidth', 'bigTemplateHeight', 'smallTemplateWidth', 'smallTemplateHeight'));

function cdk_contents_tabListblockblock_display($blockinfo){
    if (!pnSecAuthAction(0, 'cdk::contents_tabListblock', "$blockinfo[title]::", ACCESS_READ)) 
    	return;
    $vars = pnBlockVarsFromContent($blockinfo['content']);
    $GLOBALS['originalGetParams'] = $_GET;
    $GLOBALS['originalRequestParams'] = $_REQUEST;
    $oldOp = $GLOBALS['sisOp'];
    $j=0;
    $randNum=rand(1,1000);
    
   	$output = '<div style="display:none"><link rel="stylesheet" type="text/css" href="'.WHERE_IS_PERSO.'themes/saman/style/style_contentlist.css" /></div>';
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
   	foreach ($vars['tab_title'] as $key=>$tabValue) {
   		if($tabValue!="" && $vars['typeCombo'][$key])
   			$tabsCount++;
   	}
   	foreach ($vars['wd_id'] as $key => $value){
   		if (substr($value,0,1)=="#") {
    		$vars['wd_id'][$key] = replaceTemplateParams($value);
   		}
   	}
    $output .= "<table width='100%' cellpadding='0' cellspacing='0' style='border:1px solid #DCE1E7;border-collapse:collapse' id='tabsheet_list'>";
    if ($vars['tab_title'][1]) {
        $output .= "<tr>";
    }
  	$idx = 0;
  	$randId =  $blockinfo["bid"];
  	$selectedTab = 1;
  	if ($_GET["fragment$blockinfo[bid]"])
  		$selectedTab = $_GET["fragment$blockinfo[bid]"];
   	foreach ($vars['tab_title'] as $key=>$tabValue) {
   		if($tabValue!="" && $vars['typeCombo'][$key]) {
			$idx++;
			$query = "module=cdk&type=user&func=loadmodule&system=cdk&sismodule=block/contents_template_list_block.php&ctp_id=".$vars['typeCombo'][$key]."&bigTemplate_id=".$vars['templateCombo'][$key]."&thumTemplate_id=".$vars['templateCombo2'][$key]."&item_count=".$vars['count_items'][$key]."&selected_id=".$vars['selected_items'][$key]."&where_clause=".base64_encode($vars['filter_items'][$key])."&web_directory_id=".$vars['wd_id'][$key]."&field=".$vars['contentTypeField']."&order=".$vars['contentTypeFieldOrder']."&standalone=1&codedWhereClause=1&randId=$randId&die=1&subportal=all&stw=$vars[smallTemplateWidth]&sth=$vars[smallTemplateHeight]&btw=$vars[bigTemplateWidth]&bth=$vars[bigTemplateHeight]";
	   		if ($selectedTab == $idx) {
		   		$orgGet = $_GET;
		   		parse_str($query, $_GET);
		   		$_REQUEST=$_GET;
		   		unset($_GET['die']);
		   		$content = pnModFunc('cdk', 'user', 'loadmodule');
		   		if (strpos($content, "ui-tabs-panel") === false) {
		   			return;
		   		}
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
            if ($vars['tab_title'][1]) {
               $output.="<td class='$className' id='td_{$randId}_$idx' nowrap style='cursor:pointer;padding:5px;width:".(100/$tabsCount)."%' onclick='this.getElementsByTagName(\"a\")[0].click(); return false;'>
                        <a href='$aLink' name='fragment$blockinfo[bid]_$idx' onclick='showContentItemC$randId(document.getElementById(\"td_{$randId}_$idx\"), \"index.php\" + \"?$query\"); return false;'>$tabValue</a>
                      </td>";
            }
   		}
   	}
	$align = 'right';
	$notAlign = 'left';
	if (pnUserGetLang() == 'eng') {
		$align = 'left';
		$notAlign = 'right';
	}
	$output .= "
			</tr>
			<tr>
				<td colspan='$idx'>
					<div style='background-repeat:no-repeat;background-position: center center;' id='div_{$randId}_contanier' class='b7'>
						$content
					</div>
				</td>
			</tr>
			<tr>
				<td colspan='$idx' class='tab_footer'>
				  <div class='tab_footer_div'>
					<div class='next_start_pre' style='float:$align'>
						<img src='".$GLOBALS['imgPath']."tabsheet/next.png' onclick='tabsheetAction_$randId(\"previous\", this)'/>
						<img src='".$GLOBALS['imgPath']."tabsheet/pause.png' onclick='tabsheetAction_$randId(\"pause-start\", this)'/>
						<img src='".$GLOBALS['imgPath']."tabsheet/previous.png' onclick='tabsheetAction_$randId(\"next\", this)'/>
					</div>
					<div class='counter_display' style='float:$notAlign;direction:ltr;padding:0px 5px 0px 5px' id='tabsheet_list_footer_$randId'></div>
				  </div>
				</td>
			</tr>
		</table><script>
			var oldIdC$randId = 'td_{$randId}_1';
			var animating_$randId = false;
			var action_$randId = 'start';
			function showContentItemC$randId(tabObj, tabHref){
				if (oldIdC$randId == tabObj.id || animating_$randId)
					return;
				animating_$randId = true;
				document.getElementById(oldIdC$randId).className = 'b4';
				document.getElementById(oldIdC$randId).setAttribute('old_content', document.getElementById('div_{$randId}_contanier').innerHTML);
				document.getElementById(oldIdC$randId).setAttribute('fragment_id', currentFragmentid_$randId);
				tabObj.className='b3';
				oldIdC$randId = tabObj.id;
				if (tabObj.getAttribute('old_content')) {
					document.getElementById('div_{$randId}_contanier').innerHTML = tabObj.getAttribute('old_content');
					currentFragmentid_$randId = tabObj.getAttribute('fragment_id');
					animating_$randId = false;
					setTabsheetSliderCounter_$randId();
				}
				else {
					document.getElementById('div_{$randId}_contanier').innerHTML = '';
					document.getElementById('div_{$randId}_contanier').style.backgroundImage = 'url($GLOBALS[themePath]/images/loading.gif)';
					$.ajax({
							 type:'GET',
							 url: tabHref,
							 success: function(data){ document.getElementById('div_{$randId}_contanier').innerHTML = data; evalAJAXJavaScripts(data); document.getElementById('div_{$randId}_contanier').style.backgroundImage = '';  currentFragmentid_$randId = 1; animating_$randId = false; setTabsheetSliderCounter_$randId();}
						});
				}
			}
			var currentFragmentid_$randId = 1;
			function showFragmentNav_$randId(fragmentId) {
				if (fragmentId == currentFragmentid_$randId || animating_$randId)
					return;
				animating_$randId = true;
				$('#fragment_' + currentFragmentid_$randId + '_$randId').parent().height($('#fragment_' + currentFragmentid_$randId + '_$randId').height());
				$('#fragment_' + currentFragmentid_$randId + '_$randId').fadeOut('fast', function() {
					$('#fragment_' + fragmentId + '_$randId').fadeIn('fast', 
						function() {
							currentFragmentid_$randId = fragmentId;
							animating_$randId = false;
							setTabsheetSliderCounter_$randId();
						});
				  });
				document.getElementById('nav_fragment_' + fragmentId + '_$randId').className = 'ui-tabs-nav-item ui-tabs-selected';
				document.getElementById('nav_fragment_' + currentFragmentid_$randId + '_$randId').className = 'ui-tabs-nav-item';
				if(document.getElementById(oldIdC$randId))
					document.getElementById(oldIdC$randId).setAttribute('last_selected', fragmentId);
			}
			function setTabsheetSliderCounter_$randId() {
				$('#tabsheet_list_footer_$randId').html(currentFragmentid_$randId + ' - ' + document.getElementById('div_{$randId}_contanier').getElementsByTagName('li').length);
			}
			function tabsheetAction_$randId(action, imgObj) {
				var nextItem = currentFragmentid_$randId;
				var lastItem = document.getElementById('div_{$randId}_contanier').getElementsByTagName('li').length;
				if (action == 'next') {
					nextItem++;
					if (nextItem > lastItem)
						nextItem = 1;
					showFragmentNav_$randId(nextItem);
				}
				else if (action == 'previous') {
					nextItem--;
					if (nextItem < 1)
						nextItem = lastItem;
					showFragmentNav_$randId(nextItem);
				}
				else if (action == 'pause-start') {
					if (action_$randId == 'start') {
						imgObj.src = '$GLOBALS[imgPath]tabsheet/start.png';
						action_$randId = 'pause';
					}
					else {
						imgObj.src = '$GLOBALS[imgPath]tabsheet/pause.png';
						action_$randId = 'start';
					}
				}
			}
			window.setInterval(function() {if (action_$randId == 'start') tabsheetAction_$randId('next')}, 7000);
		</script>
		<style>
			.ui-tabs-nav-item img {
				float: none!important;
			}
			.ui-tabs-a {
				padding-bottom: 10px;
			}
		</style>";
    $blockinfo['content']=$output;
    $GLOBALS['sisOp'] = $oldOp;
	$_GET = $GLOBALS['originalGetParams'] ;
	$_REQUEST=$GLOBALS['originalRequestParams'];
	unset($GLOBALS['originalGetParams'] );
	unset($GLOBALS['originalRequestParams']);
	
	return themesideblock($blockinfo);
}

function cdk_contents_tabListblockblock_modify($blockinfo){
    $vars = pnBlockVarsFromContent($blockinfo['content']);
    $allTemplates = pnModAPIFunc('cdk', 'user', 'getTypeTemplates', array('templateType'=>3, 'includeGeneral'=>true));
    $types = pnModAPIFunc('cdk', 'user', 'getTypes');
    if ($GLOBALS['portal_id'] == 0 && pnModAvailable('subportal') && pnBlockIsObjectSetting($blockinfo['block_id'], 'contents_tabListblock', 'subportal')) {
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
    if (pnBlockIsObjectSetting($blockinfo['block_id'], 'contents_tabListblock', 'tabsettings'))	{
	    $output.="
	    <tr>
	    	<td colspan='2'>
	    	<table name='tb_list' id='tb_list' style='text-align:center;width:100%' class='list'>
	    		<tr>
	    			<td class='listheader'>"._TAB_TITLE."</td>
	    			<td class='listheader'>"._TAB_CONTENT_TYPE."</td>
	    			<td class='listheader' style='width:15%'>"._TAB_TEMPLATE."</td>
	    			<td class='listheader' style='width:15%'>"._TAB_TEMPLATE2."</td>
	    			<td class='listheader' style='width:5%'>"._TAB_COUNT_ITEMS."</td>
	    			<td class='listheader' style='width:15%'>"._TAB_SELECTED_ITEMS."</td>
	    			<td class='listheader' style='width:15%'>"._TAB_FILTER_ITEMS."</td>
	    			<td class='listheader' width='10%'>"._TAB_WD_ID."</td>
	    		</tr>";
	    $selected_tabLists = $vars['tab_title'];
	    $rowNum=0;
	    foreach ($selected_tabLists as $key=>$tabValue){
	    	if ($tabValue=="") 
	    		break;
	    	$pagingChecked=$vars['paging'][$key]?'checked':'';
	    	if (($rowNum%2)==0) 
	    		$rowClass="sp-even";
	    	else 
	    		$rowClass="sp-odd";
	    	$output.="
	    		<tr class='$rowClass'>
	    			<td class=".sisSetCellStyle($rowNum)."><input type='text' name='tab_title[]' value='$tabValue' style='width:95%' /></td>
	    			<td class=".sisSetCellStyle($rowNum).">".getTypeCombo2($vars['typeCombo'][$key],$rowNum)."</td>
	    			<td class=".sisSetCellStyle($rowNum)."><div id='divTemplateCombo$rowNum'>".getTemplateComboOne($vars['typeCombo'][$key],$vars['templateCombo'][$key])."</div></td>
	    			<td class=".sisSetCellStyle($rowNum)."><div id='divTemplateCombo2$rowNum'>".getTemplateComboTwo($vars['typeCombo'][$key],$vars['templateCombo2'][$key])."</div></td>
	    			<td class=".sisSetCellStyle($rowNum)."><input type='text' name='count_items[]' value='".$vars['count_items'][$key]."' style='width:75%' /></td>
	    			<td class=".sisSetCellStyle($rowNum)."><textarea name='selected_items[]' cols='8' style='width:90%'>".$vars['selected_items'][$key]."</textarea></td>
	    			<td class=".sisSetCellStyle($rowNum)."><textarea name='filter_items[]' cols='8' style='width:90%'>".$vars['filter_items'][$key]."</textarea></td>
	    			<td class=".sisSetCellStyle($rowNum)."><input type='text' name='wd_id[]' value='".$vars['wd_id'][$key]."' size='2' style='width:75%'/></td>
	    		</tr>";
	    	$rowNum++;
	    }
	    for (;$rowNum<10;$rowNum++){
	    	if (($rowNum%2)==0) 
	    		$rowClass="sp-even";
	    	else 
	    		$rowClass="sp-odd";
	    	$output.="
	    		<tr class='$rowClass'>
	    			<td class=".sisSetCellStyle($rowNum)."><input type='text' name='tab_title[]' style='width:95%'/></td>
	    			<td class=".sisSetCellStyle($rowNum).">".getTypeCombo2('',$rowNum)."</td>
	    			<td class=".sisSetCellStyle($rowNum)."><div id='divTemplateCombo$rowNum'></div></td>
	    			<td class=".sisSetCellStyle($rowNum)."><div id='divTemplateCombo2$rowNum'></div></td>
	    			<td class=".sisSetCellStyle($rowNum)."><input type='text' name='count_items[]' value='' size='1' style='width:75%'/></td>
	    			<td class=".sisSetCellStyle($rowNum)."><textarea name='selected_items[]' cols='5' style='width:90%'></textarea></td>
	    			<td class=".sisSetCellStyle($rowNum)."><textarea name='filter_items[]' cols='5' style='width:90%'></textarea></td>
	    			<td class=".sisSetCellStyle($rowNum)."><input type='text' name='wd_id[]'  size='2' style='width:75%'/></td>
	    		</tr>";
	    }
	   $output.="</table>
	    </td>
	   </tr>";
    }
	if (pnBlockIsObjectSetting($blockinfo['block_id'], 'contents_tabListblock', 'templateSize'))
		$output.="<tr>
	   	<td class='caption'>
	   		" . _CNT_BLK_SMALL_TEMPLATE_SIZE . ":
	   	</td>
	   	<td class='data'>
	   		<input type='text' size=5 name='smallTemplateWidth' value='$vars[smallTemplateWidth]'/> x <input type='text' size=5 name='smallTemplateHeight' value='$vars[smallTemplateHeight]'/>
	   		" . _CNT_BLK_TEMPLATE_SIZE_PIXEL . "
	   	</td>
	   	</tr>
	   	<tr>
	   	<td class='caption'>
	   		". _CNT_BLK_BIG_TEMPLATE_SIZE . ":
	   	</td>
	   	<td class='data'>
	   		<input type='text' size=5 name='bigTemplateWidth' value='$vars[bigTemplateWidth]'/> x <input type='text' size=5 name='bigTemplateHeight' value='$vars[bigTemplateHeight]'/>
	   		" . _CNT_BLK_TEMPLATE_SIZE_PIXEL . "	   		
	   	</td>
	   	</tr>";
	if (pnBlockIsObjectSetting($blockinfo['block_id'], 'contents_tabListblock', 'contentTypeFieldOrder'))
		$output.="<tr>
	   	<td class='caption'>
	   		". _CNT_BLK_CONENT_TYPE_ORDERBY_FIELD . ":
	   	</td>
	   	<td class='data'>
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
	   	</td>
	   	</tr>";
    $output.="<script>
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
					tmpCombo.innerHTML='<select name=\"templateCombo[]\" style=\"width:95%\">'+document.xmlRequest.responseText+'</select>';
					var tmpCombo=document.getElementById('divTemplateCombo2'+currentTemplateCombo);
					tmpCombo.innerHTML='<select name=\"templateCombo2[]\" style=\"width:95%\">'+document.xmlRequest.responseText+'</select>';
					currentTemplateCombo=-1;
				}
			}
		</script>";
    return $output;
}

function cdk_contents_tabListblockblock_update($blockinfo){
	if (pnBlockIsObjectSetting($blockinfo['block_id'], 'contents_tabListblock', 'tabsettings')){
		$vars['tab_title'] = pnVarCleanFromInput('tab_title');
		$vars['typeCombo'] = pnVarCleanFromInput('typeCombo');
		$vars['templateCombo'] = pnVarCleanFromInput('templateCombo');
		$vars['templateCombo2'] = pnVarCleanFromInput('templateCombo2');
		$vars['count_items'] = pnVarCleanFromInput('count_items');
		$vars['selected_items'] = pnVarCleanFromInput('selected_items');
		$vars['filter_items'] = pnVarCleanFromInput('filter_items');
		$vars['wd_id'] = pnVarCleanFromInput('wd_id');
	}
	if (pnBlockIsObjectSetting($blockinfo['block_id'], 'contents_tabListblock', 'contentTypeFieldOrder')){
		$vars['contentTypeField'] = pnVarCleanFromInput('contentTypeField');
		$vars['contentTypeFieldOrder'] = pnVarCleanFromInput('contentTypeFieldOrder');
	}
	if (pnBlockIsObjectSetting($blockinfo['block_id'], 'contents_tabListblock', 'templateSize')){
		$vars['smallTemplateWidth'] = intval(pnVarCleanFromInput('smallTemplateWidth'));
		$vars['smallTemplateHeight'] = intval(pnVarCleanFromInput('smallTemplateHeight'));
		$vars['bigTemplateWidth'] = intval(pnVarCleanFromInput('bigTemplateWidth'));
		$vars['bigTemplateHeight'] = intval(pnVarCleanFromInput('bigTemplateHeight'));
	}
	if ($GLOBALS['portal_id'] == 0)
		$vars['subportal'] = pnVarCleanFromInput('subportal');

	$blockinfo['content'] = pnBlockVarsToContent($vars);
	return $blockinfo;
}
function getTypeCombo2($ctp_id,$rowNum){
	$types = pnModAPIFunc('cdk', 'user', 'getTypes');
	$output="<select name='typeCombo[]' id='typeCombo$rowNum' onchange='fillComboTemplate($rowNum)' style='width:95%;'>
				<option value='-1'></option>";
	foreach ($types as $key => $tabValue) {
		$selected = '';
		if ($tabValue['ctp_id'] == $ctp_id)
			$selected = 'selected';
		$output .= "<option value='$tabValue[ctp_id]' $selected >$tabValue[title]</option>";
	}
	$output .= "</optgroup>";
	$output .= "</select>";
	return $output;
}
function getTemplateComboOne($ctp_id,$ctt_id){
	$output="<select name='templateCombo[]' style='width:95%'>";
	if (!$ctp_id) {
		$output.="<option>-1</option></select>";
		return $output;
	}
	$typeTemplates=pnModAPIFunc('cdk','user','getTypeTemplates',array('ctpId'=>$ctp_id, 'templateType'=>3, 'includeGeneral'=>true));
	foreach ($typeTemplates as $key => $tabValue) {
		$selected = '';
		if ($tabValue == $ctt_id)
			$selected = 'selected';
		$output .= "<option value='$tabValue' $selected >$tabValue</option>";
	}
	$output.="</select>";
	return $output;
}
function getTemplateComboTwo($ctp_id,$ctt_id){
	$output="<select name='templateCombo2[]' style='width:95%'>";
	if (!$ctp_id) {
		$output.="<option>-1</option></select>";
		return $output;
	}
	$typeTemplates=pnModAPIFunc('cdk','user','getTypeTemplates',array('ctpId'=>$ctp_id, 'templateType'=>3, 'includeGeneral'=>true));
	foreach ($typeTemplates as $key => $tabValue) {
		$selected = '';
		if ($tabValue == $ctt_id)
			$selected = 'selected';
		$output .= "<option value='$tabValue' $selected >$tabValue</option>";
	}
	$output.="</select>";
	return $output;
}
?>