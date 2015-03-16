<?php
// Saman Portal
// Copyright (C) 2008 by the Saman Information Structure Development Team.
// http://www.sis-eg.com/
// ----------------------------------------------------------------------


function cdk_content_quick_searchblockblock_init(){
	pnSecAddSchema('cdk:content_quick_searchblock:', 'Block title::');
}

function cdk_content_quick_searchblockblock_info(){
    return array('text_type' => 'content_quick_searchblock',
                 'module' => 'cdk',
                 'text_type_long' => _CDK_SEARCH_QUICK_BLOCK,
                 'allow_multiple' => true,
                 'form_content' => false,
                 'form_refresh' => false,
                 'show_preview' => false,
				 'default_title' => _CDK_SEARCH_QUICK_BLOCK_TITLE,
				 'allow_user_add'  => 0,
				 'group' => 2,
    			 'block_description' => _CDK_SEARCH_QUICK_BLOCK_DESCRIPTION,
    			 'is_object' => true
                 );
}

$GLOBALS['object_settings']['content_quick_searchblock'] = array('contentType');

$GLOBALS['portlet_settings']['content_quick_searchblock'] = array('contentType' 					=> _CNT_BLK_CONTENT_TYPE,
																  'searchBlockPosition'				=> _CNT_BLK_SEARCH_POSITION,
																  'resultBlockId' 					=> _CNT_BLK_SEARCH_RESULT_BLOCK_ID,
																  'searchPage' 						=> _CNT_BLK_SEARCH_PAGE,
																  'advancedSearchPage' 				=> _CNT_BLK_ADVANCED_SEARCH_PAGE,
																  'advancedSearchBtnValue' 			=> _CNT_BLK_SEARCH_BTN_VALUE,
																  'showRSSLink' 					=> _CNT_BLK_SEARCH_SHOW_RSS_LINK
																  );
/*
$GLOBALS['portlet_settings_related']['content_quick_searchblock'] = array('contentTypeFieldOrder' => array('contentTypeField'),
																   	  'sliderHeight'		  => array('sliderWidth'));
*/
function cdk_content_quick_searchblockblock_display($blockinfo){
    if (!pnSecAuthAction(0, 'cdk::content_quick_searchblock', "$blockinfo[title]::", ACCESS_READ)) 
    	return;
    	
    $vars = pnBlockVarsFromContent($blockinfo['content']);       
    
    $originalGetParams = $_GET;
    $GLOBALS['originalGetParams'] = $_GET;
    $GLOBALS['sisReadOnly'] = false;
    $_GET['ctp_id'] = $vars['contentType'];
    if ($_GET['ctp_id'] == '-1')
    	$_GET['ctp_id'] = pnVarCleanFromInput('ctp_id');
    $_GET['block_position'] = $vars['searchBlockPosition'];
    $_GET['show_rss'] = $vars['showRSSLink'];
    $_GET['result_block_id'] = $vars['resultBlockId'];
    $_GET['advanced_page'] = $vars['advancedSearchPage'];
    $_GET['searchPage'] = $vars['searchPage'];
    $_GET['advancedSearchBtnValue'] = $vars['advancedSearchBtnValue'];
    $GLOBALS['_block_id_'] = $blockinfo['bid'];
    
	$blockinfo['content'] = pnModFunc('cdk', 'user', 'loadmodule', array('system'=>'cdk',
													 							  	 'sismodule' => 'block/content_quick_search_block.php'));     
    $_GET = $GLOBALS['originalGetParams'] ;
	unset($GLOBALS['originalGetParams'] );
    $GLOBALS['sisReadOnly'] = $originalReadOnly;
	return themesideblock($blockinfo);	
}

function cdk_content_quick_searchblockblock_modify($blockinfo){
    $vars = pnBlockVarsFromContent($blockinfo['content']);    
    if (!$vars['searchBlockPosition'])
    	$vars['searchBlockPosition'] = 'horizontal';
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
	$content = "<tr>
					<td colspan='2'><br></td>
				</tr>";
	if(pnBlockIsObjectSetting($blockinfo['block_id'],'content_quick_searchblock','contentType'))
		$content .= "	
				<tr>
					<td class='caption' nowrap='nowrap'>"
						. _CNT_BLK_CONTENT_TYPE . " : 
					</td>
					<td>
						<select name='contentType'>"
							. $typesOptions . "
						</select>
			   		</td>
				</tr>";
	if(pnBlockIsObjectSetting($blockinfo['block_id'],'content_quick_searchblock','searchBlockPosition'))
		$content .= "
				<tr>
					<td class='caption'  nowrap='nowrap'>"
						. _CNT_BLK_SEARCH_POSITION . ":
					</td>
					<td>
						<input type='radio' name='searchBlockPosition' value='horizontal' ".($vars['searchBlockPosition'] == 'horizontal'?'checked':'')."> " . _CNT_BLK_SEARCH_POSITION_HORIZONTAL . " 
						<input type='radio' name='searchBlockPosition' value='vertical' ".($vars['searchBlockPosition'] == 'vertical'?'checked':'')."> " . _CNT_BLK_SEARCH_POSITION_VERTICAL . "
					</td>
				</tr>";
	if(pnBlockIsObjectSetting($blockinfo['block_id'],'content_quick_searchblock','resultBlockId'))
		$content .= "<tr>
					<td class='caption' nowrap='nowrap'>"
						. _CNT_BLK_SEARCH_RESULT_BLOCK_ID . " : 
					</td>
					<td>
						<input type='text' size='5' name='resultBlockId' value='$vars[resultBlockId]' />
			   		</td>
				</tr>";
	if(pnBlockIsObjectSetting($blockinfo['block_id'],'content_quick_searchblock','searchPage'))
		$content .= "
				<tr>
					<td class='caption' nowrap='nowrap'>"
						. _CNT_BLK_SEARCH_PAGE . " : 
					</td>
					<td>
						<input type='text' size='80' name='searchPage' value='$vars[searchPage]' style='direction:ltr'/>
			   		</td>
				</tr>";
	if(pnBlockIsObjectSetting($blockinfo['block_id'],'content_quick_searchblock','advancedSearchPage'))
		$content .= "
				<tr>
					<td class='caption' nowrap='nowrap'>"
						. _CNT_BLK_ADVANCED_SEARCH_PAGE . " : 
					</td>
					<td>
						<input type='text' size='80' name='advancedSearchPage' value='$vars[advancedSearchPage]' style='direction:ltr'/>
			   		</td>
				</tr>";
	if(pnBlockIsObjectSetting($blockinfo['block_id'],'content_quick_searchblock','advancedSearchBtnValue'))
		$content .= "
				<tr>
					<td class='caption' nowrap='nowrap'>"
						. _CNT_BLK_SEARCH_BTN_VALUE . " : 
					</td>
					<td>
						<input type='text' size='20' name='advancedSearchBtnValue' value='$vars[advancedSearchBtnValue]' /><span class=''>("._CNT_BLK_SEARCH_BTN_VALUE_DESC.")</span>
			   		</td>
				</tr>";
	if(pnBlockIsObjectSetting($blockinfo['block_id'],'content_quick_searchblock','showRSSLink'))
		$content .= "
				<tr>
					<td class='caption'  nowrap='nowrap' style='vertical-align:top' valign='top'>
					</td>
					<td>
						<INPUT value=1 " . ($vars['showRSSLink'] == 1?'CHECKED':'') . " type='checkbox' name='showRSSLink'>
						" . _CNT_BLK_SEARCH_SHOW_RSS_LINK  . "
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

function cdk_content_quick_searchblockblock_update($blockinfo){
	if(pnBlockIsObjectSetting($blockinfo['block_id'],'content_quick_searchblock','contentType'))
		$vars['contentType'] = pnVarCleanFromInput('contentType');
	if(pnBlockIsObjectSetting($blockinfo['block_id'],'content_quick_searchblock','searchBlockPosition'))
		$vars['searchBlockPosition'] = pnVarCleanFromInput('searchBlockPosition');
	if(pnBlockIsObjectSetting($blockinfo['block_id'],'content_quick_searchblock','showRSSLink'))
		$vars['showRSSLink'] = pnVarCleanFromInput('showRSSLink');
	if(pnBlockIsObjectSetting($blockinfo['block_id'],'content_quick_searchblock','resultBlockId'))
		$vars['resultBlockId'] = pnVarCleanFromInput('resultBlockId');
	if(pnBlockIsObjectSetting($blockinfo['block_id'],'content_quick_searchblock','searchPage'))
		$vars['searchPage'] = pnVarCleanFromInput('searchPage');
	if(pnBlockIsObjectSetting($blockinfo['block_id'],'content_quick_searchblock','advancedSearchPage'))
		$vars['advancedSearchPage'] = pnVarCleanFromInput('advancedSearchPage');
	if(pnBlockIsObjectSetting($blockinfo['block_id'],'content_quick_searchblock','advancedSearchBtnValue'))
		$vars['advancedSearchBtnValue'] = pnVarCleanFromInput('advancedSearchBtnValue');
	$blockinfo['content'] = pnBlockVarsToContent($vars);
	return $blockinfo;
}
?>