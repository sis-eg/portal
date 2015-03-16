<?php
/*
** Title:  مشخصات عمومی بلاک رجیستری
** Author: میلاد میری
** Date:   1392/03/09
*/


function cdk_content_registery_formblock_init(){
	pnSecAddSchema('cdk:content_registery_form:', 'Block title::');
}


function cdk_content_registery_formblock_info(){
    return array('text_type' => 'content_registery_form',
                 'module' => 'cdk',
                 'text_type_long' => _CDK_REGISTERY_FORM_BLOCK,
                 'allow_multiple' => true,
                 'form_content' => false,
                 'form_refresh' => false,
                 'show_preview' => false,
				 'default_title' => _CDK_REGISTERY_FORM_BLOCK_TITLE,
				 'allow_user_add'  => 0,
				 'group' => 2,
				 'block_description' => _CDK_SEARCH_REGISTERY_FORM_DESCRIPTION,
				 'is_object' => true,
				 'allow_subportal_add' =>1
                 );
}


$GLOBALS['object_settings']['content_registery_form'] = array('block_properties', 
															  'contentType',
															  'selectedVars');

$GLOBALS['portlet_settings']['content_registery_form'] = array('selectedVars' => _CNT_BLK_CONENT_REGIS_PARAM_ID);

function cdk_content_registery_formblock_display($blockinfo){
    $vars  = pnBlockVarsFromContent($blockinfo['content']);    
    if (!$vars['contentType']) {
    	$vars['contentType'] = $vars['_ctp_id_'];
    }
    @eval("\$propertiesTmp=array($vars[block_properties]);");    
    $properties = array();    
    $lang= pnUserGetLang();    
    $mod_name = pnModAPIFunc('cdk','user','getType',array('ctp_id'=>$vars['contentType']));
    
            			      
 	foreach ($propertiesTmp as $key=>$value) {

    	if (isset($value['title_'.$lang]))
    		$properties[$key]['title'] = $value['title_'.$lang]; 
    	else 
    		$properties[$key]['title'] = $value['title']; 	

    		
    	$properties[$key]['access_domain'] = ($value['access_domain'] ? true : false);
    	

    	  
    	if($properties[$key]['access_domain'])
			$portal_id = $GLOBALS['portal_id'];
		else
			$portal_id = 0;			
			
    	$properties[$key]['type'] = $value['type'];        
    	$properties[$key]['mandatory'] = $value['mandatory'];			
    	$data = registryModuleGetVar($key,$mod_name['type_name'],$portal_id);    		    	    	    		    
    	    
		if(!empty($data) || $data===0) {
   			$properties[$key]['value'] = $data;
		}else{
			registryModuleSetVar($key,$value['default_value'],$mod_name['type_name'],$portal_id);
			$properties[$key]['value'] = $value['default_value'];
		}    			
		$properties[$key]['width'] = $value['width'];  
    	$properties[$key]['height'] = $value['height'];    	  
    	if($value['type'] == 'memo')   	
    		$properties[$key]['editor'] = $value['editor'];
		if ($value['type'] == 'radio' || $value['type'] == 'combobox' ){    		
    		$properties[$key]['items'] = array();    		
    		if ( $value['type'] == 'combobox' && $value['blank_item'])
    			$properties[$key]['items'][] = array('value' => '', 'caption' => '');
    			
	    	if ($value['items_type'] == 'static'){
				$items = split(",", $value['items']);						
				foreach ($items as $item) {
					$value_item = substr($item, 0, strpos($item, ':'));
					$caption = str_replace($value_item.':', '', $item);
					if (ereg(pnUserGetLang().':', $item)) {
						$caption =  substr($caption, strpos($caption, pnUserGetLang().':') + 4);
						if (strpos($caption, ';'))
							$caption = substr($caption, 0, strpos($caption, ';'));					
					}
					else				
						$caption =  substr($item, strpos($item, ':') + 1);
					$tag = '';
					if (ereg(':', $caption)) {	
						$tag = substr($caption, strrpos($caption, ':') + 1);	
						$caption = str_replace(":$tag", "", $caption);
					}
					$properties[$key]['items'][] = array('value' => $value_item, 'caption' => $caption, 'tag'=>$tag);
				}//end of foreach
				
	    	}else if ($value['items_type'] == 'query'){
			    $dbconn =& pnDBGetConn(true);
			    $pntable =& pnDBGetTables();
			    $sql = $value['items'];
				if (trim($sql)) {
					$result =& $dbconn->Execute($sql);
				    if (!($dbconn->ErrorNo() != 0)) {
					    while (!$result->EOF) {
					    	$tmp = $result->GetRowAssoc(false);
					    	$caption = localizedStr($tmp['caption']);
							if (isset($tmp['caption_'.pnUserGetLang()]))
								$caption = $tmp['caption_'.pnUserGetLang()];
							$properties[$key]['items'][] = array('value'=>$tmp['value'], 'caption'=>$caption);
    						$result->MoveNext(); 
					    }
				    }
				}
	    	}//end of else if
    	}//end of main if           	
 	}//end of foreach
	
 	$_GET['prop'] = $properties;
 	$_GET['registry_type'] = $vars['contentType'];
 	$_GET['selectedVars'] = $vars['selectedVars'];
 	$blockinfo['content'] = pnModFunc('cdk', 'user', 'loadmodule', array('system'=>'cdk',
													 							  	 'sismodule' => 'block/content_registery_form.php'
																					 ));    
	return themesideblock($blockinfo);	
}


function cdk_content_registery_formblock_modify($blockinfo){
    $vars = pnBlockVarsFromContent($blockinfo['content']);    
    $types = pnModAPIFunc('cdk', 'user', 'getAccessibleTypes');        		    
	$content .= "<tr><td colspan='2'><br></td></tr>";
	if(!$blockinfo['ctp_id'] && pnBlockIsObjectSetting($blockinfo['block_id'], 'content_registery_form', 'contentType')){
		$content .= "<tr>
						<td class='caption'  nowrap='nowrap' style='vertical-align: top;'>"
							. _CNT_BLK_CONENT_REGIS_CONTENT_TYPE . ":
						</td>
						<td>
							<select name='contentType'>";
								foreach ($types as $key => $value) {
									$selected = '';
									if ($value['ctp_id'] == $vars['contentType'])
										$selected = 'selected';
									$content .= "<option value='$value[ctp_id]' $selected >$value[title]</option>";
								}												
		$content .="</select>
						</td>
					</tr>";	
	}
	if(($vars['block_properties'] || !$blockinfo['ctp_id']) && pnBlockIsObjectSetting($blockinfo['block_id'], 'content_registery_form', 'block_properties')){					
		$content .="<tr>
					<td class='caption'  nowrap='nowrap' style='vertical-align: top;'>"
						. _CNT_BLK_CONENT_REGIS_PARAM_ID . ":
					</td>
					<td>
						<textarea name='block_properties' id='block_properties' rows='15' cols1='80' style='width:98%' dir='ltr'>$vars[block_properties]</textarea>
					</td>
				</tr>
				<tr>
					<td></td>
					<td class='itemdescription' valign='top' colspan='2'>هر خصوصيت اختصاصي بلاک، را در يک خط بصورت زير وارد نماييد</td>
				</tr>					
				<tr>
					<td></td>
					<td class='itemdescription' style='text-align:left;direction:ltr'>
					<div style='line-height:21px'>
					'propertyName'=> array(
						'title_far'=>'farsi title',
						'title_eng'=>'english title',
						'title'=>'other lang title', 
						'type'=>'text|memo|radio|checkbox|combobox|date|time',
						'default_value'=>'value',
						'width'=>'30',
						'height'=>'10',
						'access_domain'=>true|false,
						'mandatory'=>true|false,
						'blank_item'=>true|false(use only combobox),
						'editor'=>true|false(use only memo)						
					  ),		
					</div>			
					
					</td>				
				</tr>";
	}
	if (intval($blockinfo['ctp_id']) && !$vars['block_properties'] && pnBlockIsObjectSetting($blockinfo['block_id'], 'content_registery_form', 'selectedVars')) {
		$registryVarsInfo = getContentTypeRegistryVarsInfo(intval($blockinfo['ctp_id']));		
		if ($registryVarsInfo) {
			$content .="<tr>
						<td class='caption'  nowrap='nowrap' style='vertical-align: top;'>"
							. _CNT_BLK_CONENT_REGIS_PARAM_ID . ":
						</td>
						<td>";
			
			foreach ($registryVarsInfo as $key=>$value) {
				$checked = !$vars['selectedVars'] || $vars['selectedVars'][$key];
				$content .= "<input type='checkbox' name='registry[$key]' value='1' style='vertical-align:middle' " . ($checked?'checked':'')  . "/> $value[title]<br/>";
			}
			$content .="</td>
					</tr>";
		}
	}
    $output = new pnHTML();
	$output->SetInputMode(_PNH_VERBATIMINPUT);
	$output->Text($content);
    return $output->GetOutput();
}


function cdk_content_registery_formblock_update($blockinfo){
	$vars['selectedVars'] = $_POST['registry'];   		
	$vars['block_properties'] = pnVarCleanFromInput('block_properties');
	$vars['contentType'] = pnVarCleanFromInput('contentType'); 
	  	
	$blockinfo['content'] = pnBlockVarsToContent($vars);
	return $blockinfo;
}