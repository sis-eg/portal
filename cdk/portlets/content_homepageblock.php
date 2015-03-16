<?php
// Saman Portal
// Copyright (C) 2008 by the Saman Information Structure Development Team.
// http://www.sis-eg.com/
// ----------------------------------------------------------------------

function cdk_content_homepageblockblock_init(){
	pnSecAddSchema('cdk:content_homepageblock:', 'Block title::');
}

function cdk_content_homepageblockblock_info(){
    return array('text_type' => 'content_homepageblock',
                 'module' => 'cdk',
                 'text_type_long' => _CDK_CONTNET_HOMEPAGE_BLOCK,
                 'allow_multiple' => false,
                 'form_content' => false,
                 'form_refresh' => false,
                 'show_preview' => false,
				 'default_title' => _CDK_CONTNET_HOMEPAGE_BLOCK_TITLE,
				 'allow_user_add'  => 0,
				 'group' => 2,
				 'block_description' => _CNT_BLK_CONENT_HOME_PAGE_BLOCK,
				 'allow_subportal_add'  => 1,
				 'is_object' => true
                 );
}

$GLOBALS['object_settings']['content_homepageblock'] = array('contentType');

$GLOBALS['portlet_settings']['content_homepageblock'] = array('contentType' => _CNT_BLK_CONENT_TYPE/*,
														  	  'contentTypeSettings' => _CNT_BLK_CONENT_SETTINGS*/);

function cdk_content_homepageblockblock_display($blockinfo){	
    if (!pnSecAuthAction(0, 'cdk::content_homepageblock', "$blockinfo[title]::", ACCESS_READ))
    	return;
	$originalGetParams = $_GET;
	$_GET['from_page'] = true;
	$GLOBALS['place'] = 'content_homepageblock';
  	if ($_GET['module'] == 'cdk') {
  		if ($_GET['sismodule'] == 'user/content_advanced_search.php' || $_GET['sismodule'] == 'user/content_edit.php' || $_GET['sismodule'] == 'user/content_view.php')
  			$blockinfo['template'] = -1000;
		list($module, $func, $name, $file, $type, $op) = pnVarCleanFromInput('module',
											                            	'func',
											                            	'name',
											                            	'file',
											                            	'type',
											                            	'op');
		if (empty($module))
			$module = $name;
		$vars['name'] = $module;
		$vars['type'] = $type;
		$vars['func'] = $func;
		$vars['file'] = $file;
		$vars['op'] = $op;
		$pageItem['item_content'] = pnBlockVarsToContent($vars);
		ob_start();
		pmk_user_getserviceoutput(array('pageItem'=>$pageItem, 'page_id'=>$GLOBALS['current_page_id']));
		$blockinfo['content'] = ob_get_clean();
  	}
  	else {
	    $vars = pnBlockVarsFromContent($blockinfo['content']);
	    $_GET['ctp_id']	= $vars['contentType'];
		$blockinfo['content'] = pnModFunc('cdk', 'user', 'loadmodule', array('system'=>'cdk', 'sismodule' => 'user/content_home_page.php'));
	}
    $_GET = $originalGetParams;
    $GLOBALS['place']='';
	return themesideblock($blockinfo);
}

function cdk_content_homepageblockblock_modify($blockinfo){
	$backUrl = base64_decode($_GET['backUrl']);
    $vars = pnBlockVarsFromContent($blockinfo['content']);
    $types = pnModAPIFunc('cdk', 'user', 'getAccessibleTypes');
	foreach ($types as $key => $value) {
		$selected = '';
		if ($value['ctp_id'] == $vars['contentType'])
			$selected = 'selected';
		$typesOptions .= "<option value='$value[ctp_id]' $selected >$value[title]</option>";
	}
	$allListTemplates = pnModAPIFunc('cdk', 'user', 'getTypeTemplates2', array('templateType'=>3, 'includeGeneral'=>true));
	$allSearchTemplates = pnModAPIFunc('cdk', 'user', 'getTypeTemplates2', array('templateType'=>4, 'includeGeneral'=>true));
	$allEditTemplates = pnModAPIFunc('cdk', 'user', 'getTypeTemplates2', array('templateType'=>2, 'includeGeneral'=>true));
	if ($vars['contentType']) {
		$contentType = pnModAPIFunc('cdk', 'user', 'getType', array('ctp_id'=>$vars['contentType']));
	}
    require_once('portlets/sisRapid/dream/packs/services/dynamic_content/sisLang/'.pnUserGetLang().'/global.php');
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'content_homepageblock', 'contentType')) {
		$content .= "<tr>
						<td colspan='2'><br></td>
					</tr>
					<tr>
						<td class='caption' nowrap='nowrap'>"
							. _CNT_BLK_CONENT_TYPE . " : 
						</td>
						<td>
							<select name='contentType' id='cmbContentType' onchange='initContentTypeTemplateCombo(); initContentTypeFieldCombo();'>"
								. $typesOptions . "
							</select>
				   		</td>
					</tr>";
	}
	else {
		$content .= "<input type='hidden' id='cmbContentType' name='contentType' value='$vars[contentType]' /> ";
	}/*
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'content_homepageblock', 'contentTypeSettings')) {
		$content .= "<script>
						var contentTypeFields = new Array();
						var contentTypeListTemplates = new Array();
						var contentTypeSearchTemplates = new Array();
						var contentTypeEditTemplates = new Array();
						function initContentTypeTemplateCombo() {
							cmbContentType = document.getElementById('cmbContentType');
							cmbContentTypeListTemplate1 = document.getElementById('cmbContentTypeListTemplate1');
							if (cmbContentTypeListTemplate1) {
								cmbContentTypeListTemplate1.innerHTML = '';
								cmbContentTypeListTemplate1.appendChild(document.createElement('option'));
							}
							cmbContentTypeListTemplate2 = document.getElementById('cmbContentTypeListTemplate2');
							if (cmbContentTypeListTemplate2) {
								cmbContentTypeListTemplate2.innerHTML = '';
								cmbContentTypeListTemplate2.appendChild(document.createElement('option'));
							}
							cmbContentTypeListTemplate3 = document.getElementById('cmbContentTypeListTemplate3');
							if (cmbContentTypeListTemplate3) {
								cmbContentTypeListTemplate3.innerHTML = '';
								cmbContentTypeListTemplate3.appendChild(document.createElement('option'));
							}
							cmbContentTypeListTemplate4 = document.getElementById('cmbContentTypeListTemplate4');
							if (cmbContentTypeListTemplate4) {
								cmbContentTypeListTemplate4.innerHTML = '';
								cmbContentTypeListTemplate4.appendChild(document.createElement('option'));
							}
							if (contentTypeListTemplates[cmbContentType.value] != null) {
								for(var idx = 0; idx < contentTypeListTemplates[cmbContentType.value].length; idx++) {
									if (cmbContentTypeListTemplate1) {
										var opt = document.createElement('option');
										opt.value = contentTypeListTemplates[cmbContentType.value][idx].name;
										opt.setAttribute('typeId', contentTypeListTemplates[cmbContentType.value][idx].typeId);
										opt.innerHTML = contentTypeListTemplates[cmbContentType.value][idx].caption;
										if ('{$contentType['settings']['newItemsListTemplate']}' == opt.value) {
											opt.selected = true;
										}
										cmbContentTypeListTemplate1.appendChild(opt);
									}
									if (cmbContentTypeListTemplate2) {
										var opt = document.createElement('option');
										opt.value = contentTypeListTemplates[cmbContentType.value][idx].name;
										opt.setAttribute('typeId', contentTypeListTemplates[cmbContentType.value][idx].typeId);
										opt.innerHTML = contentTypeListTemplates[cmbContentType.value][idx].caption;
										if ('{$contentType['settings']['itemsListTemplate']}' == opt.value) {
											opt.selected = true;
										}
										cmbContentTypeListTemplate2.appendChild(opt);
									}
									if (cmbContentTypeListTemplate3) {
										var opt = document.createElement('option');
										opt.value = contentTypeListTemplates[cmbContentType.value][idx].name;
										opt.setAttribute('typeId', contentTypeListTemplates[cmbContentType.value][idx].typeId);
										opt.innerHTML = contentTypeListTemplates[cmbContentType.value][idx].caption;
										if ('{$contentType['settings']['searchResultTemplate']}' == opt.value) {
											opt.selected = true;
										}
										cmbContentTypeListTemplate3.appendChild(opt);
									}
									if (cmbContentTypeListTemplate4) {
										var opt = document.createElement('option');
										opt.value = contentTypeListTemplates[cmbContentType.value][idx].name;
										opt.setAttribute('typeId', contentTypeListTemplates[cmbContentType.value][idx].typeId);
										opt.innerHTML = contentTypeListTemplates[cmbContentType.value][idx].caption;
										if ('{$contentType['settings']['rssTemplate']}' == opt.value) {
											opt.selected = true;
										}
										cmbContentTypeListTemplate4.appendChild(opt);
									}
								}
							}
							cmbContentTypeSearchTemplate1 = document.getElementById('cmbContentTypeSearchTemplate1');
							if (cmbContentTypeSearchTemplate1) {
								cmbContentTypeSearchTemplate1.innerHTML = '';
								cmbContentTypeSearchTemplate1.appendChild(document.createElement('option'));
							}
							cmbContentTypeSearchTemplate2 = document.getElementById('cmbContentTypeSearchTemplate2');
							if (cmbContentTypeSearchTemplate2) {
								cmbContentTypeSearchTemplate2.innerHTML = '';
								cmbContentTypeSearchTemplate2.appendChild(document.createElement('option'));
							}
							if (contentTypeSearchTemplates[cmbContentType.value] != null) {
								for(var idx = 0; idx < contentTypeSearchTemplates[cmbContentType.value].length; idx++) {
									if (cmbContentTypeSearchTemplate1) {
										var opt = document.createElement('option');
										opt.value = contentTypeSearchTemplates[cmbContentType.value][idx].name;
										opt.setAttribute('typeId', contentTypeSearchTemplates[cmbContentType.value][idx].typeId);
										opt.innerHTML = contentTypeSearchTemplates[cmbContentType.value][idx].caption;
										if ('{$contentType['settings']['searchTemplate']}' == opt.value) {
											opt.selected = true;
										}
										cmbContentTypeSearchTemplate1.appendChild(opt);
									}
									if (cmbContentTypeSearchTemplate2) {
										var opt = document.createElement('option');
										opt.value = contentTypeSearchTemplates[cmbContentType.value][idx].name;
										opt.setAttribute('typeId', contentTypeSearchTemplates[cmbContentType.value][idx].typeId);
										opt.innerHTML = contentTypeSearchTemplates[cmbContentType.value][idx].caption;
										if ('{$contentType['settings']['searchTemplateAdmin']}' == opt.value) {
											opt.selected = true;
										}
										cmbContentTypeSearchTemplate2.appendChild(opt);
									}
								}
							}
							cmbContentTypeEditTemplate1 = document.getElementById('cmbContentTypeEditTemplate1');
							if (cmbContentTypeEditTemplate1) {
								cmbContentTypeEditTemplate1.innerHTML = '';
								cmbContentTypeEditTemplate1.appendChild(document.createElement('option'));
							}
							cmbContentTypeEditTemplate2 = document.getElementById('cmbContentTypeEditTemplate2');
							if (cmbContentTypeEditTemplate2) {
								cmbContentTypeEditTemplate2.innerHTML = '';
								cmbContentTypeEditTemplate2.appendChild(document.createElement('option'));
							}
							cmbContentTypeEditTemplate3 = document.getElementById('cmbContentTypeEditTemplate3');
							if (cmbContentTypeEditTemplate3) {
								cmbContentTypeEditTemplate3.innerHTML = '';
								cmbContentTypeEditTemplate3.appendChild(document.createElement('option'));
							}
							cmbContentTypeEditTemplate4 = document.getElementById('cmbContentTypeEditTemplate4');
							if (cmbContentTypeEditTemplate4) {
								cmbContentTypeEditTemplate4.innerHTML = '';
								cmbContentTypeEditTemplate4.appendChild(document.createElement('option'));
							}
							cmbContentTypeEditTemplate5 = document.getElementById('cmbContentTypeEditTemplate5');
							if (cmbContentTypeEditTemplate5) {
								cmbContentTypeEditTemplate5.innerHTML = '';
								cmbContentTypeEditTemplate5.appendChild(document.createElement('option'));
							}
							cmbContentTypeEditTemplate6 = document.getElementById('cmbContentTypeEditTemplate6');
							if (cmbContentTypeEditTemplate6) {
								cmbContentTypeEditTemplate6.innerHTML = '';
								cmbContentTypeEditTemplate6.appendChild(document.createElement('option'));
							}
							cmbContentTypeEditTemplate7 = document.getElementById('cmbContentTypeEditTemplate7');
							if (cmbContentTypeEditTemplate7) {
								cmbContentTypeEditTemplate7.innerHTML = '';
								cmbContentTypeEditTemplate7.appendChild(document.createElement('option'));
							}
							cmbContentTypeEditTemplate8 = document.getElementById('cmbContentTypeEditTemplate8');
							if (cmbContentTypeEditTemplate8) {
								cmbContentTypeEditTemplate8.innerHTML = '';
								cmbContentTypeEditTemplate8.appendChild(document.createElement('option'));
							}
							if (contentTypeEditTemplates[cmbContentType.value] != null) {
								for(var idx = 0; idx < contentTypeEditTemplates[cmbContentType.value].length; idx++) {
									if (cmbContentTypeEditTemplate1) {
										var opt = document.createElement('option');
										opt.value = contentTypeEditTemplates[cmbContentType.value][idx].name;
										opt.setAttribute('typeId', contentTypeEditTemplates[cmbContentType.value][idx].typeId);
										opt.innerHTML = contentTypeEditTemplates[cmbContentType.value][idx].caption;
										if ('{$contentType['settings']['newTemplate']}' == opt.value) {
											opt.selected = true;
										}
										cmbContentTypeEditTemplate1.appendChild(opt);
									}
									if (cmbContentTypeEditTemplate2) {
										var opt = document.createElement('option');
										opt.value = contentTypeEditTemplates[cmbContentType.value][idx].name;
										opt.setAttribute('typeId', contentTypeEditTemplates[cmbContentType.value][idx].typeId);
										opt.innerHTML = contentTypeEditTemplates[cmbContentType.value][idx].caption;
										if ('{$contentType['settings']['editTemplate']}' == opt.value) {
											opt.selected = true;
										}
										cmbContentTypeEditTemplate2.appendChild(opt);
									}
									if (cmbContentTypeEditTemplate3) {
										var opt = document.createElement('option');
										opt.value = contentTypeEditTemplates[cmbContentType.value][idx].name;
										opt.setAttribute('typeId', contentTypeEditTemplates[cmbContentType.value][idx].typeId);
										opt.innerHTML = contentTypeEditTemplates[cmbContentType.value][idx].caption;
										if ('{$contentType['settings']['viewTemplate']}' == opt.value) {
											opt.selected = true;
										}
										cmbContentTypeEditTemplate3.appendChild(opt);
									}
									if (cmbContentTypeEditTemplate4) {
										var opt = document.createElement('option');
										opt.value = contentTypeEditTemplates[cmbContentType.value][idx].name;
										opt.setAttribute('typeId', contentTypeEditTemplates[cmbContentType.value][idx].typeId);
										opt.innerHTML = contentTypeEditTemplates[cmbContentType.value][idx].caption;
										if ('{$contentType['settings']['printTemplate']}' == opt.value) {
											opt.selected = true;
										}
										cmbContentTypeEditTemplate4.appendChild(opt);
									}
									if (cmbContentTypeEditTemplate5) {
										var opt = document.createElement('option');
										opt.value = contentTypeEditTemplates[cmbContentType.value][idx].name;
										opt.setAttribute('typeId', contentTypeEditTemplates[cmbContentType.value][idx].typeId);
										opt.innerHTML = contentTypeEditTemplates[cmbContentType.value][idx].caption;
										if ('{$contentType['settings']['newTemplateAdmin']}' == opt.value) {
											opt.selected = true;
										}
										cmbContentTypeEditTemplate5.appendChild(opt);
									}
									if (cmbContentTypeEditTemplate6) {
										var opt = document.createElement('option');
										opt.value = contentTypeEditTemplates[cmbContentType.value][idx].name;
										opt.setAttribute('typeId', contentTypeEditTemplates[cmbContentType.value][idx].typeId);
										opt.innerHTML = contentTypeEditTemplates[cmbContentType.value][idx].caption;
										if ('{$contentType['settings']['editTemplateAdmin']}' == opt.value) {
											opt.selected = true;
										}
										cmbContentTypeEditTemplate6.appendChild(opt);
									}
									if (cmbContentTypeEditTemplate7) {
										var opt = document.createElement('option');
										opt.value = contentTypeEditTemplates[cmbContentType.value][idx].name;
										opt.setAttribute('typeId', contentTypeEditTemplates[cmbContentType.value][idx].typeId);
										opt.innerHTML = contentTypeEditTemplates[cmbContentType.value][idx].caption;
										if ('{$contentType['settings']['viewTemplateAdmin']}' == opt.value) {
											opt.selected = true;
										}
										cmbContentTypeEditTemplate7.appendChild(opt);
									}
									if (cmbContentTypeEditTemplate8) {
										var opt = document.createElement('option');
										opt.value = contentTypeEditTemplates[cmbContentType.value][idx].name;
										opt.setAttribute('typeId', contentTypeEditTemplates[cmbContentType.value][idx].typeId);
										opt.innerHTML = contentTypeEditTemplates[cmbContentType.value][idx].caption;
										if ('{$contentType['settings']['printTemplateAdmin']}' == opt.value) {
											opt.selected = true;
										}
										cmbContentTypeEditTemplate8.appendChild(opt);
									}
								}
							}
						}
						function initContentTypeFieldCombo() {
							cmbContentType = document.getElementById('cmbContentType');
							cmbContentTypeField1 = document.getElementById('cmbContentTypeField1');
							cmbContentTypeField2 = document.getElementById('cmbContentTypeField2');
							cmbContentTypeField3 = document.getElementById('cmbContentTypeField3');
							if (cmbContentTypeField1) {
								cmbContentTypeField1.innerHTML = '';
								cmbContentTypeField1.appendChild(document.createElement('option'));
							}
							if (cmbContentTypeField2) {
								cmbContentTypeField2.innerHTML = '';
								cmbContentTypeField2.appendChild(document.createElement('option'));
							}
							if (cmbContentTypeField3) {
								cmbContentTypeField3.innerHTML = '';
								cmbContentTypeField3.appendChild(document.createElement('option'));
							}
							if (contentTypeFields[cmbContentType.value] != null) {
								for(var idx = 0; idx < contentTypeFields[cmbContentType.value].length; idx++) {
									if (cmbContentTypeField1) {
										var opt = document.createElement('option');
										opt.value = contentTypeFields[cmbContentType.value][idx].name;
										opt.innerHTML = contentTypeFields[cmbContentType.value][idx].caption;
										if ('{$contentType['settings']['orderByField1']}' == opt.value) {
											opt.selected = true;
										}
										cmbContentTypeField1.appendChild(opt);
									}
									if (cmbContentTypeField2) {
										var opt = document.createElement('option');
										opt.value = contentTypeFields[cmbContentType.value][idx].name;
										opt.innerHTML = contentTypeFields[cmbContentType.value][idx].caption;
										if ('{$contentType['settings']['orderByField2']}' == opt.value) {
											opt.selected = true;
										}
										cmbContentTypeField2.appendChild(opt);
									}
									if (cmbContentTypeField3) {
										var opt = document.createElement('option');
										opt.value = contentTypeFields[cmbContentType.value][idx].name;
										opt.innerHTML = contentTypeFields[cmbContentType.value][idx].caption;
										if ('{$contentType['settings']['alphabeticSortField']}' == opt.value) {
											opt.selected = true;
										}
										cmbContentTypeField3.appendChild(opt);
									}
								}
							}
						}";
		foreach ($allListTemplates as $key => $templates) {
			$strTemplates = '';
			$lastTemplate = '';
			foreach ($templates as $id => $template) {
				$template['name'] = str_replace("'", "", $template['name']);
				$template['caption'] = str_replace("'", "", $template['caption']);
				if ($lastTemplate != $template['name']) {
					$strTemplates .= "{'id':$id, 'name':'$template[name]', 'caption':'$template[caption]', 'typeId':$key},";
				}
				$lastTemplate = $template['name'];
			}
			if ($strTemplates > '') {
				$strTemplates = substr($strTemplates, 0, strlen($strTemplates) - 1);
				$content .= "contentTypeListTemplates[$key] = [$strTemplates];";
			}
		}
		foreach ($allSearchTemplates as $key => $templates) {
			$strTemplates = '';
			$lastTemplate = '';
			foreach ($templates as $id => $template) {
				$template['name'] = str_replace("'", "", $template['name']);
				$template['caption'] = str_replace("'", "", $template['caption']);
				if ($lastTemplate != $template['name']) {
					$strTemplates .= "{'id':$id, 'name':'$template[name]', 'caption':'$template[caption]', 'typeId':$key},";
				}
				$lastTemplate = $template['name'];
			}
			if ($strTemplates > '') {
				$strTemplates = substr($strTemplates, 0, strlen($strTemplates) - 1);
				$content .= "contentTypeSearchTemplates[$key] = [$strTemplates];";
			}
		}
		foreach ($allEditTemplates as $key => $templates) {
			$strTemplates = '';
			$lastTemplate = '';
			foreach ($templates as $id => $template) {
				$template['name'] = str_replace("'", "", $template['name']);
				$template['caption'] = str_replace("'", "", $template['caption']);
				if ($lastTemplate != $template['name']) {
					$strTemplates .= "{'id':$id, 'name':'$template[name]', 'caption':'$template[caption]', 'typeId':$key},";
				}
				$lastTemplate = $template['name'];
			}
			if ($strTemplates > '') {
				$strTemplates = substr($strTemplates, 0, strlen($strTemplates) - 1);
				$content .= "contentTypeEditTemplates[$key] = [$strTemplates];";
			}
		}
		foreach ($types as $type) {
			$strTemplates = '';
			foreach ($type['type_fields'] as $field) {
				if ($field['fieldType'] != 'image' && $field['fieldType'] != 'file' && $field['fieldType'] != 'text') {
					$caption = $field['title_'.pnUserGetLang()];
					$strTemplates .= "{'name':'$field[fieldName]', 'caption':'$caption'},";
				}
			}
			$strTemplates .= "{'name':'_wd_rank_', 'caption':'"._CNT_BLK_WEB_DIRECTORY_RANK."'},";
			if ($strTemplates > '') {
				$strTemplates = substr($strTemplates, 0, strlen($strTemplates) - 1);
				$content .= "contentTypeFields[$type[ctp_id]] = [$strTemplates];";
			}
		}
		$content .= "</script>";
		if (!$backUrl || strpos($backUrl, 'content_home_page.php')) {
			$content .= "<tr>
							<td colspan='2'><br></td>
						 </tr>
						 <tr>
							<td class='caption' nowrap='nowrap'>"
								. _SIS_CNT_SHOW_NEW_ITEMS_LIST . " : 
							</td>
							<td>
								<input type='radio' value=0 name='settings[showNewItemsList]' ".(!$contentType['settings']['showNewItemsList']?'checked':'')."/>"._SIS_CNT_BOOLEAN_NO."
								<input type='radio' value=1 name='settings[showNewItemsList]' ".($contentType['settings']['showNewItemsList']?'checked':'')."/>"._SIS_CNT_BOOLEAN_YES."
					   		</td>
						 </tr>
						 <tr>
							<td class='caption' nowrap='nowrap'>"
								. _SIS_CNT_NEW_ITEMS_LIST_TEMPLATE . " : 
							</td>
							<td>
								<select name='settings[newItemsListTemplate]' id='cmbContentTypeListTemplate1'>
								</select>
					   		</td>
						 </tr>
						 <tr>
							<td class='caption' nowrap='nowrap'>"
								. _SIS_CNT_NEW_ITEMS_LIST_COL_COUNT . " : 
							</td>
							<td>
								<input name='settings[newItemsListColsCount]' size=3 value='{$contentType['settings']['newItemsListColsCount']}'/>
					   		</td>
						 </tr>
						 <tr>
							<td class='caption' nowrap='nowrap'>"
								. _SIS_CNT_NEW_ITEMS_LIST_RECORD_COUNT . " : 
							</td>
							<td>
								<input name='settings[newItemsListRecordsCount]' size=3 value='{$contentType['settings']['newItemsListRecordsCount']}'/>
					   		</td>
						 </tr>";
		}
		if (!$backUrl || strpos($backUrl, 'content_home_page.php') || strpos($backUrl, 'content_archives.php')) {
			$content .= "<tr>
							<td colspan='2'><br></td>
						 </tr>
						 <tr>
							<td class='caption' nowrap='nowrap'>"
								. _SIS_CNT_ITEMS_LIST_TEMPLATE . " : 
							</td>
							<td>
								<select name='settings[itemsListTemplate]' id='cmbContentTypeListTemplate2'>
								</select>
					   		</td>
						 </tr>
						 <tr>
							<td class='caption' nowrap='nowrap'>"
								. _SIS_CNT_ITEMS_LIST_COL_COUNT . " : 
							</td>
							<td>
								<input name='settings[itemsListColsCount]' size=3 value='{$contentType['settings']['itemsListColsCount']}'/>
					   		</td>
						 </tr>
						 <tr>
							<td class='caption' nowrap='nowrap'>"
								. _SIS_CNT_ITEMS_LIST_RECORD_COUNT . " : 
							</td>
							<td>
								<input name='settings[itemsListRecordsCount]' size=3 value='{$contentType['settings']['itemsListRecordsCount']}'/>
					   		</td>
						 </tr>";
		}
		if (!$backUrl || strpos($backUrl, 'content_advanced_search.php')|| strpos($backUrl, 'content_search.php')) {
			$content .= "<tr>
							<td colspan='2'><br></td>
						 </tr>
						 <tr>
							<td class='caption' nowrap='nowrap'>"
								. _SIS_CNT_SEARCH_ITEMS_LIST_TEMPLATE . " : 
							</td>
							<td>
								<select name='settings[searchResultTemplate]' id='cmbContentTypeListTemplate3'>
								</select>
					   		</td>
						 </tr>
						 <tr>
							<td class='caption' nowrap='nowrap'>"
								. _SIS_CNT_SEARCH_ITEMS_LIST_COL_COUNT . " : 
							</td>
							<td>
								<input name='settings[searchResultColsCount]' size=3 value='{$contentType['settings']['searchResultColsCount']}'/>
					   		</td>
						 </tr>
						 <tr>
							<td class='caption' nowrap='nowrap'>"
								. _SIS_CNT_SEARCH_ITEMS_LIST_RECORD_COUNT . " : 
							</td>
							<td>
								<input name='settings[searchResultRecordsCount]' size=3 value='{$contentType['settings']['searchResultRecordsCount']}'/>
					   		</td>
						 </tr>
						 <tr>
							<td colspan='2'><br></td>
						 </tr>
						 <tr>
							<td class='caption' nowrap='nowrap'>"
								. _SIS_CNT_ORDER_BY_FIELD1 . " : 
							</td>
							<td>
								<select name='settings[orderByField1]' id='cmbContentTypeField1'>
								</select>
								<select name='settings[orderByField1Order]'>
									<option value=1 ".($contentType['settings']['orderByField1Order']==1?'selected':'').">"._SIS_CNT_ORDER_BY_ASCENDING."</option>
									<option value=2 ".($contentType['settings']['orderByField1Order']==2?'selected':'').">"._SIS_CNT_ORDER_BY_DESCENDING."</option>
								</select>
					   		</td>
						 </tr>
						 <tr>
							<td class='caption' nowrap='nowrap'>"
								. _SIS_CNT_ORDER_BY_FIELD2 . " : 
							</td>
							<td>
								<select name='settings[orderByField2]' id='cmbContentTypeField2'>
								</select>
								<select name='settings[orderByField2Order]'>
									<option value=1 ".($contentType['settings']['orderByField2Order']==1?'selected':'').">"._SIS_CNT_ORDER_BY_ASCENDING."</option>
									<option value=2 ".($contentType['settings']['orderByField2Order']==2?'selected':'').">"._SIS_CNT_ORDER_BY_DESCENDING."</option>
								</select>
					   		</td>
						 </tr>";
		}
		if (!$backUrl || strpos($backUrl, 'content_home_page.php')) {
			$content .= "<tr>
							<td colspan='2'><br></td>
						 </tr>
						 <tr>
							<td class='caption' nowrap='nowrap'>
							</td>
							<td>
								<input type='checkbox' name='settings[showArchiveLink]' style='vertical-align:middle' ".($contentType['settings']['showArchiveLink']?'checked':'')."/>"._SIS_CNT_SHOW_ARCHIVE_LINK."
								<input type='hidden' name='checkboxSettings[showArchiveLink]' value=1 />
					   		</td>
						 </tr>
						 <tr>
							<td class='caption' nowrap='nowrap'>
							</td>
							<td>
								<input type='checkbox' name='settings[showRssLink]' style='vertical-align:middle' ".($contentType['settings']['showRssLink']?'checked':'')."/>"._SIS_CNT_HIDE_RSS_LINK."
								<input type='hidden' name='checkboxSettings[showRssLink]' value=1 />
					   		</td>
						 </tr>
						 <tr>
							<td class='caption' nowrap='nowrap'>
							</td>
							<td>
								<input type='checkbox' name='settings[showAlphabeticNavigation]' style='vertical-align:middle' ".($contentType['settings']['showAlphabeticNavigation']?'checked':'')."/>"._SIS_CNT_SHOW_ALPHABETIC_NAVIGATION."
								<input type='hidden' name='checkboxSettings[showAlphabeticNavigation]' value=1 />
								<select name='settings[alphabeticSortField]' id='cmbContentTypeField3' style='vertical-align:middle'>
								</select>
					   		</td>
						 </tr>
						 <tr>
							<td class='caption' nowrap='nowrap'> 
							</td>
							<td>
								<input type='checkbox' name='settings[showSearchLink]' style='vertical-align:middle' ".($contentType['settings']['showSearchLink']?'checked':'')."/>"._SIS_CNT_PORTALSEARCHLINK."
								<input type='hidden' name='checkboxSettings[showSearchLink]' value=1 />
					   		</td>
						 </tr>";
		}
		if (!$backUrl || strpos($backUrl, 'content_advanced_search.php')) {
			$content .="<tr>
							<td colspan='2'><br></td>
						 </tr>
						 <tr>
							<td class='caption' nowrap='nowrap'>"
								. _SIS_CNT_SEARCH_TEMPLATE . " : 
							</td>
							<td>
								<select name='settings[searchTemplate]' id='cmbContentTypeSearchTemplate1'>
								</select>
					   		</td>
						 </tr>";
		
		}
		if (!$backUrl || strpos($backUrl, 'content_home_page.php')) {
			$content .="<tr>
							<td colspan='2'><br></td>
						 </tr>
						 <tr>
							<td class='caption' nowrap='nowrap'>"
								. _SIS_CNT_RSS_TEMPLATE . " : 
							</td>
							<td>
								<select name='settings[rssTemplate]' id='cmbContentTypeListTemplate4'>
								</select>
					   		</td>
						 </tr>
						 <tr>
							<td class='caption' nowrap='nowrap'>"
								. _SIS_CNT_RSS_ITEMS_COUNT . " : 
							</td>
							<td>
								<input name='settings[rssItemsCount]' size=3 value='{$contentType['settings']['rssItemsCount']}'/>
					   		</td>
						 </tr>
						 <tr>
							<td class='caption' nowrap='nowrap'>"
								. _SIS_CNT_RSS_BASED_DATE . " : 
							</td>
							<td>
								<select name='settings[rssDisplayBased]'>
									<option value='1' ".($contentType['settings']['rssDisplayBased'] == '1'?'selected':'').">"._SIS_CNT_RSS_SHOW_BASED_NEW."</option>
									<option value='2' ".($contentType['settings']['rssDisplayBased'] == '2'?'selected':'').">"._SIS_CNT_RSS_SHOW_BASED_TODAY."</option>
									<option value='3' ".($contentType['settings']['rssDisplayBased'] == '3'?'selected':'').">"._SIS_CNT_RSS_SHOW_BASED_YESTERDAY."</option>
									<option value='4' ".($contentType['settings']['rssDisplayBased'] == '4'?'selected':'').">"._SIS_CNT_RSS_SHOW_BASED_LAST_WEEK."</option>
									<option value='5' ".($contentType['settings']['rssDisplayBased'] == '5'?'selected':'').">"._SIS_CNT_RSS_SHOW_BASED_LAST_MONTH."</option>
									<option value='6' ".($contentType['settings']['rssDisplayBased'] == '6'?'selected':'').">"._SIS_CNT_RSS_SHOW_BASED_LAST_YEAR."</option>
									<option value='7' ".($contentType['settings']['rssDisplayBased'] == '7'?'selected':'').">"._SIS_CNT_RSS_SHOW_BASED_DATE."</option>
								</select>
					   		</td>
						 </tr>
						 <tr>
							<td class='caption' nowrap='nowrap'>"
								. _SIS_CNT_RSS_DISPLAY_DATE . " : 
							</td>
							<td>
								<input name='settings[rssDisplayDays]' size=3 value='{$contentType['settings']['rssDisplayDays']}'/>
					   		</td>
						 </tr>";
		}
		$content .= "<tr>
						<td colspan='2'><br></td>
					 </tr>
					<script>
						initContentTypeTemplateCombo();
						initContentTypeFieldCombo();
					</script>";
	}
	*/
    $output = new pnHTML();
	$output->SetInputMode(_PNH_VERBATIMINPUT);
	$output->Text($content);
    return $output->GetOutput();
}

function cdk_content_homepageblockblock_update($blockinfo){
	pnModFunc('cdk', 'user', 'loadmodule', array('system'=>'cdk', 'sismodule'=>'header.php'));
	$vars = array();
	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'content_homepageblock', 'contentType'))
		$vars['contentType'] = pnVarCleanFromInput('contentType');

	if(pnBlockIsObjectSetting($blockinfo['block_id'], 'content_homepageblock', 'contentTypeSettings')){
		$ctpId = intval(pnVarCleanFromInput('contentType'));
		if (intval($ctpId)) {
			pnModAPIFunc('cdk', 'user', 'registerTypeFunctions', array('ctp_id'=>$ctpId));
			require_once(sisGetSetting('sisPageAPI'));
			require_once(sisGetSetting('utils'));
			require_once(sisGetSetting('definitions'));
			require_once(sisGetSetting('sisDBGrid'));
			require_once(sisGetSetting('sisHTMLExp'));
			$_POST['dynamic_content_form_position'] = 'settings';
			$table = sisTable::newTable('dynamic_content::content_types');
			$record['ctp_id'] = $ctpId;
			$table->update($record, 'ctp_id="'.$ctpId.'"');	
		}
	}
	$blockinfo['content'] = pnBlockVarsToContent($vars);
	return $blockinfo;
}
?>