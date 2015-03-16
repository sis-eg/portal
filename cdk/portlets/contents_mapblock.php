<?php
// Saman Portal
// Copyright (C) 2008 by the Saman Information Structure Development Team.
// http://www.sis-eg.com/
// ----------------------------------------------------------------------

function cdk_contents_mapblockblock_init(){
	pnSecAddSchema('cdk:contents_mapblock:', 'Block title::');
}

function cdk_contents_mapblockblock_info(){
    return array('text_type' => 'contents_mapblock',
                 'module' => 'cdk',
                 'text_type_long' => _CDK_MAP_BLOCK,
                 'allow_multiple' => true,
                 'form_content' => false,
                 'form_refresh' => false,
                 'show_preview' => false,
				 'default_title' => _CDK_MAP_BLOCK_TITLE,
				 'allow_user_add'  => 0,
				 'group' => 2,
				 'block_description' => _CDK_MAP_BLOCK_DESCRIPTION,
				 'is_object' => true
                 );
}

$GLOBALS['object_settings']['contents_mapblock'] = '*';

$GLOBALS['portlet_settings']['contents_mapblock'] = array('map_image' 				=> _CDK_MAP_BLOCK_IMAGE,
														  'content_arange' 			=> _CDK_MAP_CONTENT_ARANGE,
														  'navigation_type' 		=> _CDK_MAP_NAVIGATION_LIST,
														  'default_play' 			=> _CDK_MAP_PLAY,
														  'map_settings' 			=> _CDK_MAP_BLOCK_SETTINGS,
														  'contentTypeFieldOrder' 	=> _CNT_BLK_CONENT_TYPE_ORDERBY_FIELD
														  );

$GLOBALS['portlet_settings_related']['contents_mapblock'] = array('contentTypeFieldOrder' => array('contentTypeField'),
																  'map_settings'		  => array('area_tag','area_title','typeCombo','templateCombo','templateCombo2','count_items','filter_items','wd_id'));

function cdk_contents_mapblockblock_display($blockinfo){
    if (!pnSecAuthAction(0, 'cdk::contents_tabListblock', "$blockinfo[title]::", ACCESS_READ))
    	return;
    $portalId=$GLOBALS['portal_id'];

    $vars = pnBlockVarsFromContent($blockinfo['content']);

    $bid=$blockinfo['bid'];
    $selected_tagLists = $vars['area_tag'];
    $navigation_type=$vars['navigation_type'];
    $default_play=$vars['default_play'];
    if($default_play=='')
    	$default_play='start';
    $content_arange=$vars['content_arange'];
    if($content_arange==""&&$navigation_type==1)
    	$content_arange='<table width="100%">
							<tr>
								<td rowspan="4">
									##map##
								</td>
								<td >
									##pageTitle##
								</td>
							</tr>
							<tr>
								<td>
									##pageDetail##
								</td>
							</tr>
							<tr>
								<td valign="bottom">
									##pageList##
								</td>
							</tr>
							<tr>
								<td >
									##pageNaveBar##
								</td>
							</tr>
						</table>';
    else if($content_arange=="")
    	$content_arange='<table width="100%">
							<tr>
								<td rowspan="3">
									##map##
								</td>
								<td colspan="2">
									##pageTitle##
								</td>
							</tr>
							<tr>
								<td valign="top">
									##pageList##
								</td>
								<td>
									##pageDetail##
								</td>
							</tr>
							<tr>
								<td colspan="2">
									##pageNaveBar##
								</td>
							</tr>
						</table>';
    $idx = 0;
  	$randId =  rand();
  	$selectedArea = 1;

    $mapImage=WHERE_IS_PERSO."themes/".pnUserGetTheme()."/images/$portalId/".$vars['map_image'];
    list($mapWidth,$mapHeight,$type,$attr)=getimagesize($mapImage);

    $map.="
    <link rel='stylesheet' type='text/css' href='".WHERE_IS_PERSO."/themes/saman/style/style_map.css' />
    <script type='text/javascript' src='javascript/jquery/plugins/jquery.maphilight.min.js'></script>
	<script type='text/javascript'>
	$(function() {
		$('.map').maphilight();
	});
	</script>
	<script type='text/javascript'>
	function loadMapPage(url,title){
		$('#cdk_map_title_page').text(title);
		$.ajax({type:'GET',
				url: url,
				success: function(data){
				 	var dataTmp=data.split('~');
				 	document.getElementById('div_{$randId}_contanier').innerHTML = dataTmp[0];
				 	document.getElementById('div_{$randId}_list').innerHTML = dataTmp[1];
				 	document.getElementById('div_{$randId}_contanier').style.backgroundImage = '';
				 	currentFragmentid_$randId = 1;
				 	animating_$randId = false;
				 	setTabsheetSliderCounter_$randId();$('#tabsheet_list_footer_$randId').html(currentFragmentid_$randId + ' - ' + dataTmp[2]);
				 	panelCounter=dataTmp[2];
				 }
			});
		return false;
	}
	</script>
	<img class='map' usemap='#cdk_map_$bid' width='".$mapWidth."px' height='".$mapHeight."px'  src='$mapImage' alt='' class='cdk_map_image' />
				<map name='cdk_map_$bid' class='cdk_map_map'>";
    $randNum=rand(1,1000);

   	foreach ($vars['area_tag'] as $key=>$tagValue) {
   		if($tagValue!="" && $vars['typeCombo'][$key]) {
			$idx++;  
			$query = "module=cdk&type=user&func=loadmodule&system=cdk&sismodule=block/contents_template_map_block.php&ctp_id=".$vars['typeCombo'][$key]."&bigTemplate_id=".$vars['templateCombo'][$key]."&thumTemplate_id=".$vars['templateCombo2'][$key]."&item_count=".$vars['count_items'][$key]."&where_clause=".base64_encode($vars['filter_items'][$key])."&web_directory_id=".$vars['wd_id'][$key]."&field=".$vars['contentTypeField']."&order=".$vars['contentTypeFieldOrder']."&standalone=1&codedWhereClause=1&randId=$randId&navigation_type=$navigation_type&die=1";
			if ($idx==1) {
		   		$orgGet = $_GET;
		   		parse_str($query, $_GET);
		   		unset($_GET['die']);
		   		$content = pnModFunc('cdk', 'user', 'loadmodule');
		   		$pageFirstTitle=$vars['area_title'][$key];
		   		$_GET = $orgGet;
				$className = "b3";
	   		}
//			$query ="index.php?".$query;
			$map.=str_replace('/>',' onClick="return loadMapPage(basePath+ \'index\'+\'.php?\'+ \''.$query.'\',\''.$vars['area_title'][$key].'\')" />',$tagValue);
   		}
   	}
   	$map.="</map>";
   	$pageTitle="<div id='cdk_map_title_page' class='cdk_map_title_page'>$pageFirstTitle</div>";
  	$content=split("~",$content);
	$pageDetail="<div id='div_{$randId}_contanier' class='cdk_map_detail'>
					".$content[0]."
				</div>";
	$pageList="<div id='div_{$randId}_list' class='cdk_map_list'>
					".$content[1]."
				</div>";
	if($default_play=='start')
		$submit_play='pause';
	else 
		$submit_play='start';
	$pageNavBar="<div class='cdk_map_nav'>
					<div style='float:$align' class='cdk_map_nav_button'>
						<img src='".$GLOBALS['imgPath']."tabsheet/next.png' onclick='tabsheetAction_$randId(\"next\", this)'/>
						<img src='".$GLOBALS['imgPath']."tabsheet/$submit_play.png' onclick='tabsheetAction_$randId(\"pause-start\", this)'/>
						<img src='".$GLOBALS['imgPath']."tabsheet/previous.png' onclick='tabsheetAction_$randId(\"previous\", this)'/>
					</div>
					<div style='float:$notAlign;' id='tabsheet_list_footer_$randId' class='cdk_map_nav_counter'>1 - ".$content[2]."</div>
				</div>";
	$scripts="
		<script>
			var oldIdC$randId = 'td_{$randId}_1';
			var animating_$randId = false;
			var action_$randId = '$default_play';
			var panelCounter=".$content[2].";
			function showContentItemC$randId(tabObj, tabHref){
				if (oldIdC$randId == tabObj.id || animating_$randId)
					return;
				animating_$randId = true;
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
			}
			function setTabsheetSliderCounter_$randId() {
				$('#tabsheet_list_footer_$randId').html(currentFragmentid_$randId + ' - ' + panelCounter);
			}
			function tabsheetAction_$randId(action, imgObj) {
				var nextItem = currentFragmentid_$randId;
				var lastItem = panelCounter;
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
		window.setInterval(function() { if (action_$randId == 'start') tabsheetAction_$randId('next')}, 7000);
		</script>";
	$content_arange=str_replace(array('##map##','##pageTitle##','##pageDetail##','##pageList##','##pageNaveBar##'),array($map,$pageTitle,$pageDetail,$pageList,$pageNavBar),$content_arange);
	$content_arange.=$scripts;
    $blockinfo['content']=$content_arange;
	return themesideblock($blockinfo);
}

function cdk_contents_mapblockblock_modify($blockinfo){
    $vars = pnBlockVarsFromContent($blockinfo['content']);
    $allTemplates = pnModAPIFunc('cdk', 'user', 'getTypeTemplates');
    $types = pnModAPIFunc('cdk', 'user', 'getTypes');
    $portalId=$GLOBALS['portal_id'];
	$checked1=$checked2="";
	if($vars['navigation_type']==1)
		$checked1="checked";
	else 
		$checked2="checked";
	$checked_play_yes="checked";
	$checked_play_no="";
	if($vars['default_play']=='start')
		$checked_play_yes="checked";
	else 
		$checked_play_no="checked";
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'contents_mapblock', 'map_image')){
	    $output="
		    <tr>
		    	<td class='caption'>
		    		"._CDK_MAP_BLOCK_IMAGE.":</td>
		    	<td>
		    		<input type='file' name='map_image' />
		    		<input type='hidden' name='map_image_sent' value='1' />
		    	</td>
		    </tr>
		    <tr>
		    	<td></td>
		    	<td>";
	    if($vars['map_image']!="" && file_exists(WHERE_IS_PERSO."themes/".pnUserGetTheme()."/images/$portalId/".$vars['map_image'])){
	    	$output.="<img src='".WHERE_IS_PERSO."themes/".pnUserGetTheme()."/images/$portalId/".$vars['map_image']."' width='140px' class='cdk_map_block_image' />";
	    	$output.="<input type='hidden' name='map_image' value='".$vars['map_image']."' />";
	    }
	    else
	    	$output.="<img src='".WHERE_IS_PERSO."themes/".pnUserGetTheme()."/images/no_map.gif' width='140px'  class='cdk_map_block_image'/>";
	    $output.="
	    	</td>
	    </tr>";
	}
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'contents_mapblock', 'content_arange'))
	    $output.="<tr>
	    	<td class='caption' valign='top'>
	    		"._CDK_MAP_CONTENT_ARANGE.":
	    	</td>
	    	<td>
	    		<table>
	    			<tr>
	    				<td>
				    		<textarea name='content_arange' cols='75' rows='10'>".$vars['content_arange']."</textarea>
				    	</td>
				    	<td>
				    		<span class='itemdescription'>"._CDK_MAP_CONTENT_ARANGE_COMMENT."</span>
				    	</td>
				    </tr>
				 </table>
	    	</td>
	    </tr>";
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'contents_mapblock', 'navigation_type'))
	     $output.="<tr>
	    	<td class='caption'>"._CDK_MAP_NAVIGATION_LIST."</td>
	    	<td>
	    		<input type='radio' name='navigation_type' value='1' $checked1 />"._CDK_MAP_HORIZONTAL."
	    		<input type='radio' name='navigation_type' value='2' $checked2/>"._CDK_MAP_VERTICAL."
	    	</td>
	    </tr>";
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'contents_mapblock', 'default_play'))
	     $output.="<tr>
	    	<td class='caption'>"._CDK_MAP_PLAY."</td>
	    	<td>
	    		<input type='radio' name='default_play' value='start' $checked_play_yes />"._YES."
	    		<input type='radio' name='default_play' value='pause' $checked_play_no/>"._NO."
	    	</td>
	    </tr>";
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'contents_mapblock', 'map_settings')){
		$output.="<tr>
	    	<td colspan='2'>
	    	<table name='tb_list' id='tb_list' style='text-align:center;width:100%' class='list'>
	    		<tr>
	    			<td class='listheader'>"._CDK_MAP_AREA_TAG."</td>
	    			<td class='listheader'>"._CDK_MAP_TITLE."</td>
	    			<td class='listheader'>"._CDK_MAP_CONTENT_TYPE."</td>
	    			<td class='listheader' style='width:15%'>"._CDK_MAP_TEMPLATE."</td>
	    			<td class='listheader' style='width:15%'>"._CDK_MAP_TEMPLATE2."</td>
	    			<td class='listheader' style='width:5%'>"._CDK_MAP_COUNT_ITEMS."</td>
	    			<td class='listheader' style='width:15%'>"._CDK_MAP_FILTER_ITEMS."</td>
	    			<td class='listheader' width='10%'>"._CDK_MAP_WD_ID."</td>
	    		</tr>";
    	$selected_tagLists = $vars['area_tag'];
	    $rowNum=0;
	    foreach ($selected_tagLists as $key=>$tagValue){
	    	if ($tagValue=="") 
	    		continue;
	    	$pagingChecked=$vars['paging'][$key]?'checked':'';
	    	if (($rowNum%2)==0) 
	    		$rowClass="sp-even";
	    	else 
	    		$rowClass="sp-odd";
	    	$output.="
	    		<tr class='$rowClass'>
	    			<td class=".sisSetCellStyle($rowNum)."><input type='text' name='area_tag[]' value='$tagValue' style='width:95%' /></td>
	    			<td class=".sisSetCellStyle($rowNum)."><input type='text' name='area_title[]' value='".$vars['area_title'][$key]."' style='width:95%' /></td>
	    			<td class=".sisSetCellStyle($rowNum).">".mapblockGetTypeCombo2($vars['typeCombo'][$key],$rowNum)."</td>
	    			<td class=".sisSetCellStyle($rowNum)."><div id='divTemplateCombo$rowNum'>".mapblockGetTemplateComboOne($vars['typeCombo'][$key],$vars['templateCombo'][$key])."</div></td>
	    			<td class=".sisSetCellStyle($rowNum)."><div id='divTemplateCombo2$rowNum'>".mapblockGetTemplateComboTwo($vars['typeCombo'][$key],$vars['templateCombo2'][$key])."</div></td>
	    			<td class=".sisSetCellStyle($rowNum)."><input type='text' name='count_items[]' value='".$vars['count_items'][$key]."' style='width:75%' /></td>
	    			<td class=".sisSetCellStyle($rowNum)."><textarea name='filter_items[]' cols='8' style='width:90%'>".$vars['filter_items'][$key]."</textarea></td>
	    			<td class=".sisSetCellStyle($rowNum)."><input type='text' name='wd_id[]' value='".$vars['wd_id'][$key]."' size='2' style='width:75%'/></td>
	    		</tr>";
	    	$rowNum++;
	    }
	    $extra=$rowNum+10;
	    for (;$rowNum<$extra;$rowNum++){
	    	if (($rowNum%2)==0) 
	    		$rowClass="sp-even";
	    	else 
	    		$rowClass="sp-odd";
	    	$output.="
	    		<tr class='$rowClass'>
	    			<td class=".sisSetCellStyle($rowNum)."><input type='text' name='area_tag[]' style='width:95%'/></td>
	    			<td class=".sisSetCellStyle($rowNum)."><input type='text' name='area_title[]' style='width:95%'/></td>
	    			<td class=".sisSetCellStyle($rowNum).">".mapblockGetTypeCombo2('',$rowNum)."</td>
	    			<td class=".sisSetCellStyle($rowNum)."><div id='divTemplateCombo$rowNum'></div></td>
	    			<td class=".sisSetCellStyle($rowNum)."><div id='divTemplateCombo2$rowNum'></div></td>
	    			<td class=".sisSetCellStyle($rowNum)."><input type='text' name='count_items[]' value='' size='1' style='width:75%'/></td>
	    			<td class=".sisSetCellStyle($rowNum)."><textarea name='filter_items[]' cols='5' style='width:90%'></textarea></td>
	    			<td class=".sisSetCellStyle($rowNum)."><input type='text' name='wd_id[]'  size='2' style='width:75%'/></td>
	    		</tr>";
	    }
	    $output.="</table>
	    </td>
	   </tr>";
	}
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'contents_mapblock', 'contentTypeFieldOrder'))
		$output.="<tr>
		   	<td class='caption'>
		   		". _CNT_BLK_CONENT_TYPE_ORDERBY_FIELD . "
		   	</td>
		   	<td>
		   		<select name='contentTypeField' id='cmbContentTypeField' style='width:120px'>
			   		<option value='counter'>"._CNT_BLK_CONENT_TYPE_FIELD_COUNTER."</option>
			   		<option value='display_start_date'>"._CNT_BLK_CONENT_TYPE_FIELD_DISPLAY_START_DATE."</option>
			   		<option value='last_modified_date'>"._CNT_BLK_CONENT_TYPE_FIELD_LAST_MODIFIED_DATE."</option>
			   		<option value='page_title'>"._CNT_BLK_CONENT_TYPE_FIELD_PAGE_TITLE."</option>
				</select>
				<select name='contentTypeFieldOredr' id='cmbContentTypeFieldOredr'>
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

function cdk_contents_mapblockblock_update($blockinfo){
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'contents_mapblock', 'map_settings'))	{
		$vars['area_tag'] = pnVarCleanFromInput('area_tag');
		$vars['area_title'] = pnVarCleanFromInput('area_title');
		$vars['typeCombo'] = pnVarCleanFromInput('typeCombo');
		$vars['templateCombo'] = pnVarCleanFromInput('templateCombo');
		$vars['templateCombo2']	= pnVarCleanFromInput('templateCombo2');
		$vars['count_items'] = pnVarCleanFromInput('count_items');
		$vars['filter_items'] = pnVarCleanFromInput('filter_items');
		$vars['wd_id'] = pnVarCleanFromInput('wd_id');
	}
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'contents_mapblock', 'contentTypeFieldOrder')){
		$vars['contentTypeField'] = pnVarCleanFromInput('contentTypeField');
		$vars['contentTypeFieldOrder'] = pnVarCleanFromInput('contentTypeFieldOredr');
	}
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'contents_mapblock', 'content_arange'))
		$vars['content_arange'] = pnVarCleanFromInput('content_arange');
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'contents_mapblock', 'default_play'))
		$vars['default_play'] = pnVarCleanFromInput('default_play');
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'contents_mapblock', 'navigation_type'))
		$vars['navigation_type'] = pnVarCleanFromInput('navigation_type');

	$portalId=$GLOBALS['portal_id'];
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'contents_mapblock', 'map_image'))
		if($_FILES['map_image']['size']){
			$map_image=pnUploadFileHandle('map_image',WHERE_IS_PERSO."themes/".pnUserGetTheme()."/images/$portalId/");
			if($map_image)
				$vars['map_image']=$map_image;
		}
		else 
			$vars['map_image'] = pnVarCleanFromInput('map_image');

	$blockinfo['content'] = pnBlockVarsToContent($vars);
	return $blockinfo;
}

function mapblockGetTypeCombo2($ctp_id,$rowNum){
	$types = pnModAPIFunc('cdk', 'user', 'getTypes');
	$output="
		<select name='typeCombo[]' id='typeCombo$rowNum' onchange='fillComboTemplate($rowNum)' style='width:100px' >
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

function mapblockGetTemplateComboOne($ctp_id,$ctt_id){
	$output="<select name='templateCombo[]' >";
	if (!$ctp_id) {
		$output.="<option>-1</option></select>";
		return $output;
	}
	$typeTemplates=pnModAPIFunc('cdk','user','getTypeTemplates',array('ctpId'=>$ctp_id));

	foreach ($typeTemplates as $key => $tabValue) {
		$selected = '';
		if ($tabValue == $ctt_id)
			$selected = 'selected';
		$output .= "<option value='$tabValue' $selected >$tabValue</option>";
	}
	$output.="</select>";
	return $output;
}

function mapblockGetTemplateComboTwo($ctp_id,$ctt_id){
	$output="<select name='templateCombo2[]'>";
	if (!$ctp_id) {
		$output.="<option>-1</option></select>";
		return $output;
	}
	$typeTemplates=pnModAPIFunc('cdk','user','getTypeTemplates',array('ctpId'=>$ctp_id));
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