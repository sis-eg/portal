<?php
// Saman Portal
// Copyright (C) 2009 by the Saman Information Structure Development Team.
// http://www.sis-eg.com/
// ----------------------------------------------------------------------

function cdk_dataset_template_listblock_init(){
	pnSecAddSchema('cdk::dataset_template_listblock:', 'Block title::');
}

function cdk_dataset_template_listblock_info(){
    return array('text_type' => 'dataset_template_list',
                 'module' => 'cdk',
                 'text_type_long' => _CDK_DATASET_TEMPLATE_LIST_BLOCK,
                 'allow_multiple' => true,
                 'form_content' => false,
                 'form_refresh' => false,
                 'show_preview' => false,
				 'default_title' => _CDK_DATASET_TEMPLATE_LIST_BLOCK_TITLE,
				 'allow_user_add'  => 1,
				 'group' => 2,
 				 'block_description' => _CDK_DATASET_TEMPLATE_LIST_BLOCK_DESCRIPTION,
 				 'is_object' => true,
    			 'allow_subportal_add'  => 1 				 
                 );
}

$GLOBALS['object_settings']['dataset_template_list'] = array('dataset_query',
																'show_records',
																'header_template',
																'content_template',
																'footer_template',
																'empty_template',
																'contentType');
																
$GLOBALS['portlet_settings']['dataset_template_list'] = array('dataset_query' 		=> _CDK_DATASET_TEMPLATE_LIST_QUERY,
																'show_records'		=> _CDK_DATASET_TEMPLATE_LIST_SHOW_RECORDS,
																'columns' 			=> _CDK_DATASET_TEMPLATE_LIST_COLUMNS,
																'row_count' 		=> _CDK_DATASET_TEMPLATE_LIST_ITMES_COUNT,
																'page_row_count'	=> _CDK_DATASET_TEMPLATE_LIST_PAGE_ITMES_COUNT,
																'header_template' 	=> _CDK_DATASET_TEMPLATE_LIST_HEADER_TEMPLATE,
																'content_template'	=> _CDK_DATASET_TEMPLATE_LIST_CONTENT_TEMPLATE,
																'footer_template' 	=> _CDK_DATASET_TEMPLATE_LIST_FOOTER_TEMPLATE,
																'empty_template' 	=> _CDK_DATASET_TEMPLATE_LIST_EMPTY_TEMPLATE,
																'contentType' 		=> _CDK_DATASET_TEMPLATE_LIST_CONENT_TYPE);

function cdk_dataset_template_listblock_display($blockinfo){
    if (!pnSecAuthAction(0, 'cdk::dataset_template_listblock', "$blockinfo[title]::", ACCESS_READ)) 
    	return;
    	    	
    $vars = pnBlockVarsFromContent($blockinfo['content']);
    
	if ($vars['contentType'])
		pnModAPIFunc('cdk', 'user', 'registerTypeFunctions', array('type_name'=>$vars['contentType']));
    
    if(empty($vars['columns']))
    	$vars['columns'] = 1;
    if ($vars['show_records'] != 2)
    	$vars['show_records'] = 1;
    $GLOBALS['dataset_template_list_block'] = $vars;
    $GLOBALS['_block_id_'] = $blockinfo['bid'];
	$blockinfo['content'] = pnModFunc('cdk', 'user', 'loadmodule', array('system'=>'cdk',
													 					 'sismodule' => 'block/dataset_template_list_block.php'));     
	unset($GLOBALS['_block_id_']);
	if (!$blockinfo['content'])													 					 
		return false;
    return themesideblock($blockinfo);	
}

function cdk_dataset_template_listblock_modify($blockinfo){
    $vars = pnBlockVarsFromContent($blockinfo['content']);
//pnDump($blockinfo);        
    if ($vars['show_records'] != 2)
    	$vars['show_records'] = 1;
    	
	if(pnBlockIsObjectSetting($blockinfo['block_id'],'dataset_template_list','contentType')){
	    $types = pnModAPIFunc('cdk', 'user', 'getAccessibleTypes');    
	    $typesOptions = "<option></option>";
		foreach ($types as $key => $value) {
			$selected = '';
			if ($value['type_name'] == $vars['contentType'])
				$selected = 'selected';
			$typesOptions .= "<option value='$value[type_name]' $selected >$value[title]</option>";
		}
	    $content .= '<tr>
						<td class="caption" nowrap="nowrap">'
							. _CDK_DATASET_TEMPLATE_LIST_CONENT_TYPE . ' : 
						</td>
						<td>
							<select name="contentType" id="cmbContentType" >'
								. $typesOptions . '
							</select>
				   		</td>
					</tr>';
	}
	if(pnBlockIsObjectSetting($blockinfo['block_id'],'dataset_template_list','dataset_query'))
		$content .= "
			    <tr>
			    	<td class='caption' valign='top'>"
					. _CDK_DATASET_TEMPLATE_LIST_QUERY ." :
					</td>
					<td>
						<textarea name='dataset_template_list_query' rows='12' cols='80' onkeydown='return catchTab(this, event)' style='width:90%;direction:ltr;font-size: 13px; font-family: Courier New' wrap='off'>".htmlspecialchars($vars['dataset_query'])."</textarea>
					</td>
				</tr>";
	if(pnBlockIsObjectSetting($blockinfo['block_id'],'dataset_template_list','show_records'))					
		$content .= "<tr>
			    	<td class='caption'>"
					. _CDK_DATASET_TEMPLATE_LIST_SHOW_RECORDS ." :
					</td>
					<td>
						<input type='radio' name='dataset_template_list_show_records' value=1 " . ($vars['show_records'] == 1?'CHECKED':'') . ">"._CDK_DATASET_TEMPLATE_LIST_SHOW_RECORDS_TABLES."
						<input type='radio' name='dataset_template_list_show_records' value=2 " . ($vars['show_records'] == 2?'CHECKED':'') . ">"._CDK_DATASET_TEMPLATE_LIST_SHOW_RECORDS_FREE."
					</td>
				</tr>";
	if(pnBlockIsObjectSetting($blockinfo['block_id'],'dataset_template_list','row_count')){
		$content .= "<tr>
				    	<td class='caption'>"
						. _CDK_DATASET_TEMPLATE_LIST_ITMES_COUNT ." :
						</td>
						<td>
							<input type=\"text\" name=\"dataset_template_list_row_count\" size=\"10\" maxlength=\"10\" value=\"".$vars['row_count']."\" class=\"sp-normal\">			
						</td>
					</tr>";
	}
	if(pnBlockIsObjectSetting($blockinfo['block_id'],'dataset_template_list','page_row_count')){
		$content .= "<tr>
				    	<td class='caption'>"
						. _CDK_DATASET_TEMPLATE_LIST_PAGE_ITMES_COUNT ." :
						</td>
						<td>
							<input type=\"text\" name=\"dataset_template_list_page_row_count\" size=\"10\" maxlength=\"10\" value=\"".$vars['page_row_count']."\" class=\"sp-normal\">
							<br/>
							<span class='itemdescription'>"._CDK_DATASET_TEMPLATE_LIST_PAGE_ITMES_COUNT_DESC."</span>
						</td>
					</tr>";
	}
	
	if(pnBlockIsObjectSetting($blockinfo['block_id'],'dataset_template_list','columns')){
		$content .= "<tr>
				    	<td class='caption'>"
						. _CDK_DATASET_TEMPLATE_LIST_COLUMNS ." :
						</td>
						<td>
							<input type=\"text\" name=\"dataset_template_list_columns\" size=\"10\" maxlength=\"10\" value=\"".$vars['columns']."\" class=\"sp-normal\">
						</td>
					</tr>";
	}
	if(pnBlockIsObjectSetting($blockinfo['block_id'],'dataset_template_list','header_template'))
		$content .= "
		    <tr>
		    	<td class='caption' valign='top'>"
				. _CDK_DATASET_TEMPLATE_LIST_HEADER_TEMPLATE ." :
				</td>
				<td>
					<textarea name='dataset_template_list_header_template' rows='10' cols='80' onkeydown='return catchTab(this, event)' style='width:90%;direction:ltr;font-size: 13px; font-family: Courier New' wrap='off'>".htmlspecialchars($vars['header_template'])."</textarea>
				</td>
			</tr>";
	if(pnBlockIsObjectSetting($blockinfo['block_id'],'dataset_template_list','content_template'))
		$content .= "
		    <tr>
		    	<td class='caption' valign='top'>"
				. _CDK_DATASET_TEMPLATE_LIST_CONTENT_TEMPLATE ." :
				</td>
				<td>
					<textarea name='dataset_template_list_content_template' rows='20' cols='80' onkeydown='return catchTab(this, event)' style='width:90%;direction:ltr;font-size: 13px; font-family: Courier New' wrap='off'>".htmlspecialchars($vars['content_template'])."</textarea>
				</td>
			</tr>";
	if(pnBlockIsObjectSetting($blockinfo['block_id'],'dataset_template_list','footer_template'))
		$content .= "				
		    <tr>
		    	<td class='caption' valign='top'>"
				. _CDK_DATASET_TEMPLATE_LIST_FOOTER_TEMPLATE ." :
				</td>
				<td>
					<textarea name='dataset_template_list_footer_template' rows='10' cols='80' onkeydown='return catchTab(this, event)' style='width:90%;direction:ltr;font-size: 13px; font-family: Courier New' wrap='off'>".htmlspecialchars($vars['footer_template'])."</textarea>
				</td>
			</tr>";
	if(pnBlockIsObjectSetting($blockinfo['block_id'],'dataset_template_list','empty_template'))
		$content .= "				
			<tr>
		    	<td class='caption' valign='top'>"
				. _CDK_DATASET_TEMPLATE_LIST_EMPTY_TEMPLATE ." :
				</td>
				<td>
					<textarea name='dataset_template_list_empty_template' rows='10' cols='80' onkeydown='return catchTab(this, event)' style='width:90%;direction:ltr;font-size: 13px; font-family: Courier New' wrap='off'>".htmlspecialchars($vars['empty_template'])."</textarea>
				</td>
			</tr>";
	$content .= "
		<script>
				function replaceSelection (input, replaceString) {
					if (input.setSelectionRange) {
						var selectionStart = input.selectionStart;
						var selectionEnd = input.selectionEnd;
						input.value = input.value.substring(0, selectionStart)+ replaceString + input.value.substring(selectionEnd);
				    
						if (selectionStart != selectionEnd){ 
							setSelectionRange(input, selectionStart, selectionStart + 	replaceString.length);
						}else{
							setSelectionRange(input, selectionStart + replaceString.length, selectionStart + replaceString.length);
						}
				
					}else if (document.selection) {
						var range = document.selection.createRange();
				
						if (range.parentElement() == input) {
							var isCollapsed = range.text == '';
							range.text = replaceString;
				
							 if (!isCollapsed)  {
								range.moveStart('character', -replaceString.length);
								range.select();
							}
						}
					}
				}
				
				
				// We are going to catch the TAB key so that we can use it, Hooray!
				function catchTab(item, e){
					if(navigator.userAgent.match('Gecko')){
						c=e.which;
					}else{
						c=e.keyCode;
					}
					if(c==9){
						replaceSelection(item,String.fromCharCode(9));
						return false;
					}		
					return true;					    
				}				
		</script>
	";
    $output = new pnHTML();
	$output->SetInputMode(_PNH_VERBATIMINPUT);
	$output->Text($content);
    return $output->GetOutput();
}

function cdk_dataset_template_listblock_update($blockinfo){
	if(pnBlockIsObjectSetting($blockinfo['block_id'],'dataset_template_list','dataset_query'))
    	$vars['dataset_query'] = pnVarCleanFromInput('dataset_template_list_query');    
	if(pnBlockIsObjectSetting($blockinfo['block_id'],'dataset_template_list','show_records'))    	
    	$vars['show_records'] = pnVarCleanFromInput('dataset_template_list_show_records');    
	if(pnBlockIsObjectSetting($blockinfo['block_id'],'dataset_template_list','columns'))    	
	    $vars['columns'] = pnVarCleanFromInput('dataset_template_list_columns');    
	if(pnBlockIsObjectSetting($blockinfo['block_id'],'dataset_template_list','row_count'))    	
    	$vars['row_count'] = pnVarCleanFromInput('dataset_template_list_row_count');    
	if(pnBlockIsObjectSetting($blockinfo['block_id'],'dataset_template_list','page_row_count'))    	
    	$vars['page_row_count'] = pnVarCleanFromInput('dataset_template_list_page_row_count');    
	if(pnBlockIsObjectSetting($blockinfo['block_id'],'dataset_template_list','header_template'))
    	$vars['header_template'] = pnVarCleanFromInput('dataset_template_list_header_template');    
	if(pnBlockIsObjectSetting($blockinfo['block_id'],'dataset_template_list','content_template'))
    	$vars['content_template'] = pnVarCleanFromInput('dataset_template_list_content_template');    
	if(pnBlockIsObjectSetting($blockinfo['block_id'],'dataset_template_list','footer_template'))
    	$vars['footer_template'] = pnVarCleanFromInput('dataset_template_list_footer_template');    
	if(pnBlockIsObjectSetting($blockinfo['block_id'],'dataset_template_list','empty_template'))
    	$vars['empty_template'] = pnVarCleanFromInput('dataset_template_list_empty_template');    
	if(pnBlockIsObjectSetting($blockinfo['block_id'],'dataset_template_list','contentType'))
		$vars['contentType'] = pnVarCleanFromInput('contentType');

	$blockinfo['content'] = pnBlockVarsToContent($vars);
	return $blockinfo;
}

?>