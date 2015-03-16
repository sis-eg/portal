<?php
// Saman Portal
// Copyright (C) 2008 by the Saman Information Structure Development Team.
// http://www.sis-eg.com/
// ----------------------------------------------------------------------
require_once 'services/cdk/lang/'.pnUserGetLang().'/global.php';
$GLOBALS['contentTypeCommonFields'] = array(
											array('title_'.pnUserGetLang() => _CNT_PAGE_TITLE_FIELD,
											      'fieldName' => 'page_title',
												  'fieldType' => 'string'),
											array('title_'.pnUserGetLang() => _CNT_CREATOR_FIELD,
											      'fieldName' => 'creator_user_id',
												  'fieldType' => 'numeric'),
											array('title_'.pnUserGetLang() => _CNT_EDITOR_FIELD,
											      'fieldName' => 'last_modified_user_id',
												  'fieldType' => 'numeric'),
											array('title_'.pnUserGetLang() => _CNT_CREATE_DATE_FIELD,
											      'fieldName' => 'create_date',
												  'fieldType' => 'date'),
											array('title_'.pnUserGetLang() => _CNT_CREATE_TIME_FIELD,
											      'fieldName' => 'create_time',
												  'fieldType' => 'time'),
											array('title_'.pnUserGetLang() => _CNT_DISPLAY_START_DATE_FIELD,
											      'fieldName' => 'display_start_date',
												  'fieldType' => 'date'),
											array('title_'.pnUserGetLang() => _CNT_DISPLAY_START_TIME_FIELD,
											      'fieldName' => 'display_start_time',
												  'fieldType' => 'time'),
											array('title_'.pnUserGetLang() => _CNT_DISPLAY_END_DATE_FIELD,
											      'fieldName' => 'display_end_date',
												  'fieldType' => 'date'),
											array('title_'.pnUserGetLang() => _CNT_DISPLAY_END_TIME_FIELD,
											      'fieldName' => 'display_end_time',
												  'fieldType' => 'time'),
											array('title_'.pnUserGetLang() => _CNT_MODIFIED_DATE_FIELD,
											      'fieldName' => 'last_modified_date',
												  'fieldType' => 'date'),
											array('title_'.pnUserGetLang() => _CNT_MODIFIED_TIME_FIELD,
											      'fieldName' => 'last_modified_time',
												  'fieldType' => 'time'),
											array('title_'.pnUserGetLang() => _CNT_RECORD_NUMBER_FIELD,
											      'fieldName' => 'cnt_id',
												  'fieldType' => 'numeric'),
											array('title_'.pnUserGetLang() => _CNT_COUNTER_FIELD,
											      'fieldName' => 'counter',
												  'fieldType' => 'numeric'),
											array('title_'.pnUserGetLang() => _CNT_RECORD_STATE_FIELD,
											      'fieldName' => 'record_state',
												  'fieldType' => 'numeric'),
											array('title_'.pnUserGetLang() => _CNT_RATE_FIELD,
											      'fieldName' => 'rate',
												  'fieldType' => 'numeric'));


function cdk_userapi_getTypes($args) {
	extract($args);
	list($dbconn) = pnDBGetConn();	
	$pntable = pnDBGetTables();
	$contentTypeColumns = &$pntable['content_types_column'];
	$contentRelatedTypesColumns = &$pntable['content_related_types_column'];
	
	$type_fields = $contentTypeColumns['type_fields'];
	if ($simpleList)
		$type_fields = "''";;
	$extraWhere = '1=1';
	if ($justTopLevel) {
		$extraWhere .= " AND $contentTypeColumns[main_ctp_id] IS NULL";
	}
    $sql = "SELECT 
    			$contentTypeColumns[ctp_id],
    			$contentTypeColumns[type_name],
    			$contentTypeColumns[title],   			
    			$type_fields,
    			$contentTypeColumns[description],
				$contentTypeColumns[type],    			    			
				$contentTypeColumns[main_ctp_id],
				(select count(*) from $pntable[content_related_types] WHERE $contentRelatedTypesColumns[child_ctp_id] = ctp_id) as is_child
    		FROM 
    			$pntable[content_types]
    		WHERE
    			$contentTypeColumns[type] = 1 AND $contentTypeColumns[state] = 2 AND $extraWhere
    		ORDER BY ".prepareForOrderBy($contentTypeColumns['title']);

    $result = $dbconn->Execute($sql);
    $contentTypes = array();
    if ($dbconn->ErrorNo() == 0) {
		while (!$result->EOF) {
			list($ctp_id, $type_name, $title, $type_fiedls, $description, $type, $main_ctp_id, $is_child) = $result->fields;
			if (!$hideChilds || !$main_ctp_id) {
				$fields = unserialize(base64_decode($type_fiedls));
				if (is_array($fields)) {
					for($idx=0; $idx<count($fields); $idx++) {
						$fields[$idx] = json_decode($fields[$idx], true); 					
						$fieldArray = array();					
						foreach($fields[$idx] as $field)
							if ($field['name'] != 'dummy')
								$fieldArray[$field['name']] = urldecode($field['value']);
						$fields[$idx] = $fieldArray;					
					}
					$fields = array_merge($fields,$GLOBALS['contentTypeCommonFields']);
				}
				$title = unserialize($title);
				$title = $title[pnUserGetLang()];			
				$description = unserialize($description);
				$description = $description[pnUserGetLang()];			
				$contentTypes[$type_name] = array('ctp_id'=>$ctp_id, 'type_name'=>$type_name, 'title'=> $title, 'description' => $description, 'type_fields'=>$fields,'type'=>$type,'main_ctp_id'=>$main_ctp_id);
			}
			$result->moveNext();
		}
        $result->Close();
    }
	return $contentTypes;
}

/*
$ctp_id
$type_name
*/
function cdk_userapi_getType($args) {
	extract($args);
	
	list($dbconn) = pnDBGetConn();	
	$pntable = pnDBGetTables();
	$contentTypeColumns = &$pntable['content_types_column'];
	
	if ($ctp_id)
	    $sql = "SELECT 
	    			$contentTypeColumns[ctp_id],
	    			$contentTypeColumns[type_name],
	    			$contentTypeColumns[title],   			
	    			$contentTypeColumns[type_fields],
	    			$contentTypeColumns[description],  			
	    			$contentTypeColumns[wkf_id],
	    			$contentTypeColumns[settings],
	    			$contentTypeColumns[type],
	    			$contentTypeColumns[perm_type],
	    			$contentTypeColumns[is_system]
	    		FROM 
	    			$pntable[content_types] 
	    		WHERE $contentTypeColumns[ctp_id] = '$ctp_id' AND $contentTypeColumns[state] = 2";
	else if ($type_name)
	    $sql = "SELECT 
	    			$contentTypeColumns[ctp_id],
	    			$contentTypeColumns[type_name],
	    			$contentTypeColumns[title],   			
	    			$contentTypeColumns[type_fields],
	    			$contentTypeColumns[description],  			
	    			$contentTypeColumns[wkf_id],
	    			$contentTypeColumns[settings],
	    			$contentTypeColumns[type],
	    			$contentTypeColumns[perm_type],
	    			$contentTypeColumns[is_system]
	    		FROM 
	    			$pntable[content_types] 
	    		WHERE $contentTypeColumns[type_name] = '$type_name' AND $contentTypeColumns[state] = 2";
	else 
		return;	
    $result = $dbconn->Execute($sql);
    if (is_array($result->fields)) {
   		list($ctp_id, $type_name, $title, $type_fields, $description, $wkf_id, $settings,$type,$perm_type,$is_system) = $result->fields;
   						    				    				 
    	return array('ctp_id' 		=> $ctp_id, 
    				 'type_name' 	=> $type_name, 
    				 'title'		=> $title, 
    				 'type_fields'	=> $type_fields, 
    				 'description'	=> $description,
    				 'wkf_id'		=> $wkf_id,
    				 'settings'		=> unserialize($settings),
    				 'type'			=> $type,
    				 'perm_type'	=> $perm_type,
    				 'is_system'	=> $is_system
    				    				 );
    }
    else
		return null;
}

function cdk_userapi_getContentTypes() {
	list($dbconn) = pnDBGetConn();	
	$pntable = pnDBGetTables();
	$contentTypeColumns = &$pntable['content_types_column'];

    $sql = "SELECT 
    			*
    		FROM 
    			$pntable[content_types]
    		WHERE
    			$contentTypeColumns[type] = 1 AND $contentTypeColumns[state] = 2    			
    		ORDER BY ".prepareForOrderBy($contentTypeColumns['title']);

    $result = $dbconn->Execute($sql);
    $contentTypes = array();
    if ($dbconn->ErrorNo() == 0) {
		while (!$result->EOF) {
			$result->fields['settings'] = unserialize($result->fields['settings']);		
			$contentTypes[$result->fields['type_name']] = $result->fields;
			$result->moveNext();
		}
        $result->Close();
    }
	return $contentTypes;    
}

/*
$templateType
$includeGeneral
$ctpId
*/
function cdk_userapi_getTypeTemplates($args) {
	list($dbconn) = pnDBGetConn();	
	$pntable = pnDBGetTables();
	$templateColumns = &$pntable['content_type_templates_column'];
    $sql = "SELECT DISTINCT
    			$templateColumns[ctp_id],
    			$templateColumns[ctt_id],
    			$templateColumns[template_name],
    			$templateColumns[title]
    		FROM 
    			$pntable[content_type_templates]
    		WHERE ";
    if ($args['ctpId'] > '')    	
    	$sql .= " $templateColumns[ctp_id] = '$args[ctpId]' AND ";
    if (!sisCheckUserLicense(PORTAL_CORE_DEVELOPER)){
		$sql .= " $templateColumns[is_system] != 1 AND ";
    }
    if ($args['templateType']) {
    	if ($args['includeGeneral']) {
    		$sql .= " ($templateColumns[template_type] = '$args[templateType]' OR $templateColumns[template_type] = 1) ";
    	}
    	else {
			$sql .= " $templateColumns[template_type] = '$args[templateType]' ";    		
    	}
	   	$sql .= " ORDER BY ".prepareForOrderBy($templateColumns['template_name']).", $templateColumns[template_type]";
    }
    else {
	    $sql .= " 1=1 ";
	   	$sql .= " ORDER BY $templateColumns[template_name]";
    }
	
	
    $result = $dbconn->Execute($sql);
    $templates = array();
    if ($dbconn->ErrorNo() == 0) {
		while (!$result->EOF) {
			$templates[$result->fields[0]][$result->fields[1]] = localizedStr($result->fields[2]);
			$result->moveNext();
		}
        $result->Close();
    }
    
    if ($args['ctpId'] > '')
    	return $templates[$args['ctpId']];
	return $templates;		
}

function cdk_userapi_getTypeTemplates2($args) {
	list($dbconn) = pnDBGetConn();	
	$pntable = pnDBGetTables();
	$templateColumns = &$pntable['content_type_templates_column'];
    $sql = "SELECT DISTINCT
    			$templateColumns[ctp_id],
    			$templateColumns[ctt_id],
    			$templateColumns[template_name],
    			$templateColumns[title]
    		FROM 
    			$pntable[content_type_templates]
    		WHERE ";
    if ($args['ctpId'] > '')    	
    	$sql .= " $templateColumns[ctp_id] = '$args[ctpId]' AND ";
    if ($args['templateType']) {
    	if ($args['includeGeneral']) {
    		$sql .= " ($templateColumns[template_type] = '$args[templateType]' OR $templateColumns[template_type] = 1) ";
    	}
    	else {
			$sql .= " $templateColumns[template_type] = '$args[templateType]' ";    		
    	}
	   	$sql .= " ORDER BY ".prepareForOrderBy($templateColumns['template_name']).", $templateColumns[template_type]";
    }
    else {
	    $sql .= " 1=1 ";
	   	$sql .= " ORDER BY $templateColumns[template_name]";
    }

    $result = $dbconn->Execute($sql);
    $templates = array();
    if ($dbconn->ErrorNo() == 0) {
		while (!$result->EOF) {
			$templates[$result->fields[0]][$result->fields[1]] = array('name'=>$result->fields[2], 'caption'=>localizedStr($result->fields[3]));
			$result->moveNext();
		}
        $result->Close();
    }
    if ($args['ctpId'] > '')
    	return $templates[$args['ctpId']];
	return $templates;		
}

function cdk_userapi_getContent($args) {
	extract($args);
	if (!is_numeric($itemsCount) || $itemsCount <= 0)
		$itemsCount = 20;
	list($dbconn) = pnDBGetConn();	
	$pntable = pnDBGetTables();
	$contentTypesColumns = &$pntable['content_types_column'];
    $sql = "SELECT 
    			$contentTypesColumns[type_name],
    			$contentTypesColumns[settings]
    		FROM 
    			$pntable[content_types]
    		WHERE
    			$contentTypesColumns[ctp_id] = '$ctpId' ";
    $result = $dbconn->Execute($sql);
    if ($result->fields[0] == '')
    	return array();
    $viewName = "viw_content_{$result->fields[0]}_items";	

    $sql = "SELECT 
    			*
    		FROM 
    			$viewName
    		WHERE record_state = 1000 AND (lang IS NULL || lang = 'all' || lang = '".pnUserGetLang()."') AND ". cdk_userapi_getSpecialPermissionsWhereCaluse('cdk', $result->fields[0], 100)."
    		ORDER BY last_modified_date DESC, last_modified_time DESC
    		LIMIT 0, $itemsCount ";
    $dbconn->SetFetchMode(ADODB_FETCH_ASSOC);
    $result = $dbconn->Execute($sql);
    if (is_array($result->fields))
    	return $result->getArray();
    return array();
}

function cdk_userapi_getSpecialPermissionsWhereCaluse($component, $typeName, $permission, $tablePrefix='') {
	if ($tablePrefix > '')
		$tablePrefix .= '.';
	list($dbconn) = pnDBGetConn();	
	$pntable = pnDBGetTables();
    $result = $dbconn->Execute("SELECT DISTINCT sp_instance FROM portal.group_perms WHERE sp_component = '$component::' AND sp_instance LIKE ':$typeName:%:%'");		
	$vars = array();
	while (!$result->EOF) {
		$parts = split(':',$result->fields['sp_instance']);
		$vars[] = $parts[2];
		$result->moveNext();
	}
	$whereClause = '(1=1';
	foreach ($vars as $var) {
		$whereClause .= " AND hasSpecialPermission(".sisSession('__sisUserID').", '$component::', ':$typeName:', '$var', $tablePrefix{$var}, $permission)";
	}
	$whereClause .= ')';
	return $whereClause;
}

function cdk_userapi_getRSSTemplate($args) {
	extract($args);
	if (!is_numeric($itemsCount) || $itemsCount <= 0)
		$itemsCount = 20;
	list($dbconn) = pnDBGetConn();	
	$pntable = pnDBGetTables();
	$contentTypesColumns = &$pntable['content_types_column'];
    $sql = "SELECT 
    			$contentTypesColumns[type_name],
    			$contentTypesColumns[settings]
    		FROM 
    			$pntable[content_types]
    		WHERE
    			$contentTypesColumns[ctp_id] = '$ctpId' ";
    $result = $dbconn->Execute($sql);
    if ($result->fields[0] == '')
    	return '';
    $settings = unserialize($result->fields[1]);
    if ($settings['rssTemplate'] == '')
    	return '';
    	
	$templateColumns = &$pntable['content_type_templates_column'];
    $sql = "SELECT DISTINCT
    			$templateColumns[compiled_template]
    		FROM 
    			$pntable[content_type_templates]
    		WHERE 
    			$templateColumns[ctp_id] = '$args[ctpId]' and  $templateColumns[template_name] = '$settings[rssTemplate]'";
    $result = $dbconn->Execute($sql);
    return $result->fields[0];
}

/*
includeDeactives
portalId
*/
function cdk_userapi_getDynamicModules($args) {
	extract($args);
	if (!$portalId) {
		$portalId = $GLOBALS['portal_id'];
	}
	#$mods = sisCacheGet('cdk_userapi_getDynamicModules');
	if ($mods) {
		return $mods;
	}
	list($dbconn) = pnDBGetConn();	
	$pntable = pnDBGetTables();
	$contentTypeColumns = &$pntable['content_types_column'];

	$userGroups = getUserGroups(pnUserGetVar('uid'));	
	$userGroups = '-'.implode('-', $userGroups).'-';
	$uid = pnSessionGetVar('uid');
	$extraServices = array(-1);
	if ($includeRoles) {
		$atsCtpId = getImportId("ctp_id:1e420994-4037-11e4-b062-b499bab34c80");
		getContentType($atsCtpId);
		if (function_exists('atsGetServiceAccess')) {
			$services = atsGetServiceAccess(sisGlobal('portal_id'));
		}
		if (count($services) > 0) {
			$extraServices = array_keys($services);
		}
		
	}
	if (!$uid)
		$uid = -1;
	if ($portalId != 0)
	    $sql = "SELECT 
	    			$contentTypeColumns[type_name],
	    			$contentTypeColumns[ctp_id],    			
					$contentTypeColumns[title],    			
					$contentTypeColumns[description],				
					$contentTypeColumns[image],			
					$contentTypeColumns[ui_type],			
					$contentTypeColumns[settings],
					$contentTypeColumns[state],
					$contentTypeColumns[version],					
					hasPermission($uid, 'cdk::', concat(':' , $contentTypeColumns[type_name] , ':'), 800, '$userGroups')
	    		FROM 
	    			$pntable[content_types]
	    			INNER JOIN saman_subportal_modules
	    				ON (sp_module = CONCAT('cdk_', ctp_id) AND sp_subportal_id = '$portalId')
	    		WHERE
	    			$contentTypeColumns[type] = 1 AND ".(!$includeDeactives?"$contentTypeColumns[state] = 2 AND":"")." $contentTypeColumns[main_ctp_id] IS NULL AND ($contentTypeColumns[ctp_id] IN (" . implode($extraServices, ',') . ") OR  hasPermission($uid, 'cdk::', concat(':' , $contentTypeColumns[type_name] , ':'), 500, '$userGroups'))
	    		ORDER BY ".prepareForOrderBy($contentTypeColumns['title']);    	
	else
	    $sql = "SELECT 
	    			$contentTypeColumns[type_name],
	    			$contentTypeColumns[ctp_id],    			
					$contentTypeColumns[title],    			
					$contentTypeColumns[description],				
					$contentTypeColumns[image],			
					$contentTypeColumns[ui_type],			
					$contentTypeColumns[settings],
					$contentTypeColumns[state],		
					$contentTypeColumns[version],
					hasPermission($uid, 'cdk::', concat(':' , $contentTypeColumns[type_name] , ':'), 800, '$userGroups')
	    		FROM 
	    			$pntable[content_types]
	    		WHERE
	    			$contentTypeColumns[type] = 1 AND ".(!$includeDeactives?"$contentTypeColumns[state] = 2 AND":"")." $contentTypeColumns[main_ctp_id] IS NULL AND ($contentTypeColumns[ctp_id] IN (" . implode($extraServices, ',') . ") OR  hasPermission($uid, 'cdk::', concat(':' , $contentTypeColumns[type_name] , ':'), 500, '$userGroups'))
	    		ORDER BY ".prepareForOrderBy($contentTypeColumns['title']);    
    $result = $dbconn->Execute($sql);
    $contentTypes = array();
    if ($dbconn->ErrorNo() == 0) {
		while (!$result->EOF) {
			list($typeName, $ctpId, $title, $description, $image, $uiType, $settings, $status, $version, $adminPermission) = $result->fields;
			if (function_exists('hasSpecialPermission')) {
				if (!hasSpecialPermission($typeName, '#ANY#', 800)) {
					$result->moveNext();
					continue;
				}
			}
			$adminUrl = '';
			if ($services[$ctpId]) {
				$adminPermission = 1;
				$adminUrl = "/index.php?module=web_directory&wd_id=" . getImportId("wd_id:$services[$ctpId]");
			}
			$image =  strToArray($image);
			foreach ($image as $key=>$value){
				$image=$value;
			}			
			if ($image['name'] > '')
				$image = WHERE_IS_PERSO.'/modules/cdk/upload/'.$result->fields[1].'/'.$image['name'];
			else
				$image = 'services/control_panel/images/default.gif';			
			if (!is_file($image))
				$image = 'services/control_panel/images/default.gif';
			if (function_exists('registerContentTypeLangConsts')) {
				registerContentTypeLangConsts($ctpId);
			}
			$contentTypes[] = array('name' => $typeName,
									'type_id' => $ctpId,
									'id' => '100000'.$ctpId,
									'type' => 1,
									'state' => $status==2?_PNMODULE_STATE_ACTIVE:_PNMODULE_STATE_INACTIVE,
									'displayname' => localizedStr($title),
									'description' => localizedStr($description),
									'image' => $image,
									'admin_capable' => true,
									'user_capable' => true,
									'dynamic_module' => true,
									'ui_type' => $uiType,
									'admin_permission' => $adminPermission,
									'version' => $version,
									'settings' => unserialize($settings));
			$result->moveNext();
		}
        $result->Close();
    }
	#sisCacheSet('cdk_userapi_getDynamicModules', $contentTypes, 3600);    
	return $contentTypes;	
}

function cdk_userapi_getDynamicModulesForSearch($args) {
	list($dbconn) = pnDBGetConn();	
	$pntable = pnDBGetTables();
	$contentTypeColumns = &$pntable['content_types_column'];

	$userGroups = getUserGroups(pnUserGetVar('uid'));
	$userGroups = '-'.implode('-', $userGroups).'-';
	$uid=pnSessionGetVar('uid');
	if (!$uid) 
		$uid=-1;
	
	if ($GLOBALS['portal_id'] != 0)	{	
		$sql = "SELECT sp_module FROM saman_subportal_modules WHERE sp_subportal_id = '$GLOBALS[portal_id]'";
    	$result = $dbconn->Execute($sql);
    	$subportalModules = array();
  		while (!$result->EOF) {
  			$subportalModules[str_replace('cdk_', '', $result->fields[0])] = true;
  			$result->moveNext();
  		}				
	}

    $sql = "SELECT 
    			$contentTypeColumns[type_name],
    			$contentTypeColumns[ctp_id],    			
				$contentTypeColumns[title],    			
				$contentTypeColumns[description],				
				$contentTypeColumns[image],			
				$contentTypeColumns[settings]				
    		FROM 
    			$pntable[content_types]
    		WHERE
    			$contentTypeColumns[type] = 1 AND $contentTypeColumns[state] = 2 AND hasPermission(".$uid.", 'cdk::', concat(':' , $contentTypeColumns[type_name] , ':'), 100, '$userGroups')
    		ORDER BY ".prepareForOrderBy($contentTypeColumns['title']);    

    $result = $dbconn->Execute($sql);
    $contentTypes = array();

    if ($dbconn->ErrorNo() == 0) {
		while (!$result->EOF) {
			list($typeName, $ctpId, $title, $description, $image,$settings) = $result->fields;
			if ($GLOBALS['portal_id'] != 0 && !$subportalModules[$ctpId]) {
				$result->moveNext();				
				continue;
			}
			$image =  strToArray($image);
			foreach ($image as $key=>$value){
				$image=$value;
			}
			if ($image['name'] > '')
				$image = 'index.php?module=cdk&func=loadlibmodule&system=cdk&sismodule='.pnGetBaseURI().'/portlets/sisRapid/dream/libs/V2.56/core/sisFile.php&fileName='.$image['name'].'&relativePath='.$image['path'].'&nodownload=1&width=48&height=48';
			else
				$image = 'services/control_panel/images/default.gif';			
			$contentTypes[] = array('name' => $typeName,
									'type_id' => $ctpId,
									'id' => '100000'.$ctpId,
									'type' => 1,
									'displayname' => localizedStr($title),
									'description' => localizedStr($description),
									'image' => $image,
									'admin_capable' => true,
									'user_capable' => true,
									'dynamic_module' => true,
									'version' => '1.0.0.0',
									'settings' => unserialize($settings));
			$result->moveNext();
		}
        $result->Close();
    }
	return $contentTypes;	
}

function cdk_userapi_getDynamicModuleAdminUrl($args) {
	extract($args);
	$url = pnModURL2('cdk', 'user', 'loadmodule', array('system' => 'cdk',
															'sismodule' => 'management/contents_list.php',															
															'_sub_menu_' => '0',
															'_menu_' => '1',
															'ctp_id' => $id,
															'control_panel'=>1));
	return $url;
}

function cdk_userapi_encode_all_url($args) {
	$matches = array();
	preg_match_all($args['prefix'] . '(?:index.php\?|modules.php\?)(?:name|module)=(?:cdk)[^\'"]*[\'"]]', 
    			   $args['source'], $matches);
    for($idx=0; $idx < count($matches[0]); $idx++) {
		$url = str_replace($matches[1][$idx],'',$matches[0][$idx]);
		$url = str_replace('"','',$url);
		$url = str_replace("'",'',$url);
		$url2 = cdk_userapi_encodeurl(array('url'=>$url));
		$matches[2][$idx] = "'$url'";
		$matches[3][$idx] = "'".$url2."'";		
		$matches[4][$idx] = '"'.$url.'"';
		$matches[5][$idx] = '"'.$url2.'"';		
    }

	$matches['2_copy'] = $matches[2];
	$matches['3_copy'] = $matches[3];
	$matches['4_copy'] = $matches[4];
	$matches['5_copy'] = $matches[5];
	foreach ($matches[2] as $key=>$value) {
		if (ereg(pnGetBaseURL(), $value)) {
			unset($matches['2_copy'][$key]);
			unset($matches['3_copy'][$key]);
		}
		else {
			unset($matches[2][$key]);
			unset($matches[3][$key]);			
		}
	}
	foreach ($matches[4] as $key=>$value) {
		if (ereg(pnGetBaseURL(), $value)) {
			unset($matches['4_copy'][$key]);
			unset($matches['5_copy'][$key]);
		}
		else {
			unset($matches[4][$key]);
			unset($matches[5][$key]);			
		}
	}
    $args['source'] = str_replace($matches[2], $matches[3], $args['source']);	    
    $args['source'] = str_replace($matches['2_copy'], $matches['3_copy'], $args['source']);	
    $args['source'] = str_replace($matches[4], $matches[5], $args['source']);	    
    $args['source'] = str_replace($matches['4_copy'], $matches['5_copy'], $args['source']);	
}

function cdk_userapi_encodeurl($args) {
	extract($args);
		
	if (!isset($url)) {
		if (!isset($args['sismodule']))
			return;
 		$url = "index.php?module=cdk&func=loadmodule&system=cdk&sismodule=$sismodule";
 		unset($args['system']);
 		unset($args['sismodule']);
 		foreach ($args as $key=>$value)
 			$url .= "&$key=$value";
	}
	else {
		if (/*ereg('loadlibmodule', $url) ||*/ ereg('call_function.php', $url))
			return $url;
	}

	$url = str_replace('&amp;', '&', $url);		
	$url = str_replace(pnGetBaseURL(), '', $url);
	if (pnGetBaseURI())
		$url = str_replace(pnGetBaseURI().'/', '', $url);		
	$url = str_replace('/index.php', 'index.php', $url);
	$url = str_replace('index.php?module=cdk&func=loadmodule&system=cdk&', '', $url);

	$formName = '';
	$parts = split('#', $url);	

	if ($parts[1] > '')
		$formName .= "#$parts[1]";
	$parts = split('&', $parts[0]);

	$parsedParts = array();
	$parameters = array('sismodule' => 1,
						'sisop' => 1,
						'sisOp' => 1,
						'cnt_id' => 1,
						'ctp_id' => 1,
						'id' => 1,
						'view' => 1,
						'ceo_title' => 1,
						'ceo_category' => 1
						);		
	foreach ($parts as $part) {
		$part = split('=', $part);
		$part1 = $part[0];
		unset($part[0]);
		$part2 = implode('=', $part);
		$parsedParts[$part1] = $part2;
	}
	$parsedParts['sismodule'] = str_replace('/', '___', $parsedParts['sismodule']);

	if (!$parsedParts['sisop'] && $parsedParts['sisOp'])
		$parsedParts['sisop'] = $parsedParts['sisOp'];
	
	// فهرست کل انواع محتواها
	if ($parsedParts['sismodule'] == 'configuration___content_types_list.php')
		return pnGetBaseURL().'services/';
	
	if (!isset($parsedParts['ctp_id']) && $parsedParts['sismodule'] != 'user___refer_form.php') {		
		return $args['url'];
	}

	if (SHORT_URL && $parsedParts['sismodule'] == 'management___content_edit.php') {		
		$parsedParts['sismodule'] = 'user___content_edit.php';
	}
	if (SHORT_URL && $parsedParts['sismodule'] == 'management___content_view.php') {
		$parsedParts['sismodule'] = 'user___content_view.php';	
	}
	
	if ($GLOBALS['__sisCdkShortUrlCache__']['conentTypeNames'][$parsedParts['ctp_id']]) {
		$contentType = $GLOBALS['__sisCdkShortUrlCache__']['conentTypeNames'][$parsedParts['ctp_id']]['name'];
		$parsedParts['ceo_category'] = $GLOBALS['__sisCdkShortUrlCache__']['conentTypeNames'][$parsedParts['ctp_id']]['seo_title'];
	}
	else {
		list($dbconn) = pnDBGetConn();	
		$pntable = pnDBGetTables();
		$contentTypeColumns = &$pntable['content_types_column'];
	    $sql = "SELECT 
	    			$contentTypeColumns[type_name],
	    			$contentTypeColumns[title],
	    			$contentTypeColumns[seo_title]
	    		FROM 
	    			$pntable[content_types]
	    		WHERE
	    			$contentTypeColumns[ctp_id] = '$parsedParts[ctp_id]'";    
	    $result = $dbconn->Execute($sql);	
		$contentType = $result->fields[0];	
	    $seoCategory = localizedStr($result->fields[2]);		
		$parsedParts['ceo_category'] = $seoCategory;
		$GLOBALS['__sisCdkShortUrlCache__']['conentTypeNames'][$parsedParts['ctp_id']] = array('name'=>$result->fields[0], 'title'=>localizedStr($result->fields[1]), 'seo_title'=>$seoCategory);		
	}

	$action = "";
	if ($parsedParts['sismodule'] == "user___content_home_page.php")
		$action = "";	
	if ($parsedParts['sismodule'] == "user___content_edit.php") {
		if ($parsedParts['sisop'] == "new")
			$action = "add/";
		if ($parsedParts['sisop'] == "edit")
			$action = "edit/";
		if ($parsedParts['sisop'] == "del")
			$action = "delete/";			
	}
	if ($parsedParts['sismodule'] == "user___content_advanced_search.php")
		$action = "advanced_search/";			
	if ($parsedParts['sismodule'] == "user___content_search.php")
		$action = "search/";			
	if ($parsedParts['sismodule'] == "user___content_archives.php")
		$action = "archive/";			
	if ($parsedParts['sismodule'] == "user___content_view.php" && $parsedParts['view'] == 'thumbnail')
		$action = "thumbnail/";	
	if ($parsedParts['sismodule'] != "user___content_home_page.php" && 
		$parsedParts['sismodule'] != "user___content_edit.php" &&
		$parsedParts['sismodule'] != "user___content_view.php" &&
		$parsedParts['sismodule'] != "user___content_search.php" &&
		$parsedParts['sismodule'] != "user___content_archives.php" &&
		$parsedParts['sismodule'] != "user___content_advanced_search.php") { 
		unset($parameters['sismodule']);
		unset($parameters['sisop']);		
		unset($parameters['sisOp']);		
		unset($parameters['view']);		
	}
	$content = "";
	if ($parsedParts['cnt_id'])
		$content = $parsedParts['cnt_id'].'/';
	else if ($parsedParts['id'])
		$content = $parsedParts['id'].'/';
	
	$ceoTitle2 = '';
	if (!$action) {
		$newUrl = "$contentType/$action$content";	
	}
	else {
		$newUrl = "services/$contentType/$action$content";
	}	
	if ($action.$content == '')
		$ceoTitle2 = sisPrepareTitleForUrl($GLOBALS['__sisCdkShortUrlCache__']['conentTypeNames'][$parsedParts['ctp_id']]['title']).'.html';
	$hasExtraParams = false;
	
	foreach ($parsedParts as $key=>$value) {
		if (!isset($parameters[$key]) && trim($value)>'') {					
			if ($key == 'sisop')
				$key = 'sisOp';
			$newUrl .= "$key/$value/";
			$hasExtraParams = true;
		}
	}
	
	if ($parsedParts['cnt_id']) {
		if (!$parsedParts['ceo_title'] && SHORT_URL) {
			list($dbconn) = pnDBGetConn();
			$pntable = pnDBGetTables();
			$contentColumns = &$pntable['content_column'];
			$contentTypeColumns = &$pntable['content_types_column'];		
			$webDirectoryColumns = &$pntable['web_directories_column'];
			$webDirectoryExtraColumns = &$pntable['web_directory_extra_column'];
/*			$sql = "SELECT
						$contentColumns[page_title],
						$webDirectoryColumns[title]
					FROM
						$pntable[content]
						INNER JOIN $pntable[content_types]
							ON ($contentColumns[ctp_id] = $contentTypeColumns[ctp_id])
						LEFT JOIN $pntable[web_directory_extra]
							ON ($webDirectoryExtraColumns[pk_value] = $contentColumns[foreign_key_value] AND $webDirectoryExtraColumns[content_type] = $contentTypeColumns[type_name])
						LEFT JOIN $pntable[web_directories]
							ON ($webDirectoryColumns[id] = $webDirectoryExtraColumns[wd_id])
					WHERE
						$contentColumns[cnt_id] = '$parsedParts[cnt_id]'";*/
			$sql = "SELECT
						$contentColumns[page_title],
						$contentColumns[seo_title]						
					FROM
						$pntable[content]
					WHERE
						$contentColumns[cnt_id] = '$parsedParts[cnt_id]'";

			$result = $dbconn->Execute($sql);
			$parsedParts['ceo_title'] = $result->fields[1]?$result->fields[1]:$result->fields[0];
			/*$parsedParts['ceo_category'] = localizedStr($result->fields[1]);*/

/*			$settings = unserialize($result->fields[1]);
			if ($settings['ceoWebDirectory'] && !$GLOBALS['_cdk_ceo_directory'][$settings['ceoWebDirectory_']]) {
				$webDirectoriesColumn = &$pntable['web_directories_column'];		
				$sql = "SELECT 
							$webDirectoriesColumn[title]
						FROM 
							$pntable[web_directories]
						WHERE
							$webDirectoriesColumn[id] = '$settings[ceoWebDirectory]'";		
				$result = $dbconn->Execute($sql);
				$GLOBALS['_cdk_ceo_directory_'][$settings['ceoWebDirectory']] = localizedStr($result->fields[0]);
			}
			$parsedParts['ceo_category'] = $GLOBALS['_cdk_ceo_directory_'][$settings['ceoWebDirectory']];*/
		}
		/*if (!$parsedParts['ceo_category']) {
			$wdPath = cdk_userapi_getNavigationPath(array('ctp_id'=>$parsedParts['ctp_id'], 'id'=>$parsedParts['id']));
			if (count($wdPath) > 0){
				$wdPath = $wdPath[count($wdPath) - 1];
				$GLOBALS['_cdk_ceo_directory_'][$wdPath['wd_id']] = $wdPath['wd_title'];
				$parsedParts['ceo_category'] = $GLOBALS['_cdk_ceo_directory_'][$wdPath['wd_id']];
			}
		}*/
		if ($parsedParts['ceo_title']) {
			if (!$hasExtraParams)
				$newUrl = substr($newUrl, 0, strlen($newUrl) - 1).'-';		
			$newUrl .= sisPrepareTitleForUrl($parsedParts['ceo_title']).'.html';		
			if ($parsedParts['ceo_category'])
				$newUrl .= '?t='.sisPrepareTitleForUrl($parsedParts['ceo_category']);
		}
	}

	if ($parsedParts['sismodule'] == 'user___content_view.php' && !strpos($newUrl, '.htm') && $newUrl[mb_strlen($newUrl) - 1] == '/') {
		$newUrl .= $parsedParts['cnt_id'].'.htm';
	}
	
	if ($ceoTitle2)
		$newUrl .= $ceoTitle2;
	return pnGetBaseURL().$newUrl;
}

function cdk_userapi_decodeurl($args) {
	array_shift($args['vars']);
	array_shift($args['vars']);
	$parts = $args['vars'];	
	
	if ($args['return'])
		$returnParams = array();
	// در صورتيکه قالب آدرس به درستي تبديل نشده باشد و از تبديل پيش فرض استفاده کرده باشد
	if ($parts[0] == 'func' || $parts[0] == 'type') {
		return false;
	}
	
	// درصورتیکه آدرس به شیوه عادی بود دیگر تغییر نکند
	if ($_GET['sismodule'])
		return false;
	// فهرست کل انواع محتواها	
	if (count($parts) == 0)  {
		$queryStringParams = pnQueryStringSetVar('func', 'loadmodule', $args['return']);
		if ($args['return'])
			$returnParams = array_merge($returnParams, $queryStringParams);						
		$queryStringParams = pnQueryStringSetVar('system', 'cdk', $args['return']);
		if ($args['return'])
			$returnParams = array_merge($returnParams, $queryStringParams);						
		$queryStringParams = pnQueryStringSetVar('sismodule', 'configuration/content_types_list.php', $args['return']);
		if ($args['return'])
			$returnParams = array_merge($returnParams, $queryStringParams);						
		if ($args['return'])
			return $returnParams;
		else
			return true;
	}

	$module = '';
	list($dbconn) = pnDBGetConn();	
	$pntable = pnDBGetTables();
	$contentTypeColumns = &$pntable['content_types_column'];
    $sql = "SELECT 
    			$contentTypeColumns[ctp_id]
    		FROM 
    			$pntable[content_types]
    		WHERE
    			$contentTypeColumns[type_name] = '$parts[0]'";    
    $result = $dbconn->Execute($sql);
	$ctpId = $result->fields[0];
	$index = 0;
	if ($parts[1] == 'add' ) {		
		if(is_numeric($parts[2]) /*$parts[2] != "parent_ctp_id" && $parts[2] != "swd_id"*/){
			$cntId = $parts[2];			
			$module = $parts[1];
			$index = 3;
		}
		else{
			$cntId = 0;	
			$module = $parts[1];
			$index = 2;
		}				
	}
	else if ($parts[1] == 'edit' || $parts[1] == "search" || $parts[1] == "advanced_search" || $parts[1] == "thumbnail" || $parts[1] == "archive") {
		$cntId = $parts[2];			
		$module = $parts[1];
		$index = 2;
	}
	else if ($parts[1] == 'delete') {
		$cntId = $parts[2];			
		$module = $parts[1];
		$index = 3;		
	}
	//else if (intval($parts[1]) || ereg('.html', $parts[1])) {						
	else if (intval($parts[1])) {						
		$cntId = intval($parts[1]);
		$module = 'view';			
		$index = 2;
	}		
	else {
		$index = 1;
	}	
	$op = '';
	$view = '';
	if ($module == 'add' || $module == 'edit' || $module == 'delete') {
		if ($module == 'add')
			$op = 'new';
		else if ($module == 'edit')
			$op = 'edit';
		else if ($module == 'delete')
			$op = 'del';
		$module = 'user/content_edit.php';
	}
	else if ($module == 'search')
		$module = 'user/content_search.php';
	else if ($module == 'advanced_search')
		$module = 'user/content_advanced_search.php';
	else if ($module == 'thumbnail') {
		$module = 'user/content_view.php';	
		$op = 'view';							
		$view = 'thumbnail';
		$index = 3;		
	}
	else if ($module == 'view') {
		$module = 'user/content_view.php';
		$op = 'view';
	}
	else if ($module == 'archive') {
		$module = 'user/content_archives.php';
		$op = 'view';
	}
	else
		$module = 'user/content_home_page.php';
		
	$queryStringParams = pnQueryStringSetVar('func', 'loadmodule', $args['return']);
	if ($args['return'])
		$returnParams = array_merge($returnParams, $queryStringParams);							
	$queryStringParams = pnQueryStringSetVar('system', 'cdk', $args['return']);
	if ($args['return'])
		$returnParams = array_merge($returnParams, $queryStringParams);							
	$queryStringParams = pnQueryStringSetVar('sismodule', $module, $args['return']);
	if ($args['return'])
		$returnParams = array_merge($returnParams, $queryStringParams);							
	if ($op > '') {
		$queryStringParams = pnQueryStringSetVar('sisOp', $op, $args['return']);		
		if ($args['return'])
			$returnParams = array_merge($returnParams, $queryStringParams);								
	}
	$queryStringParams = pnQueryStringSetVar('ctp_id', $ctpId, $args['return']);
	if ($args['return'])
		$returnParams = array_merge($returnParams, $queryStringParams);								
	if ($cntId) {
		pnQueryStringSetVar('cnt_id', $cntId);	
		$contentColumns = &$pntable['content_column'];
	    $sql = "SELECT 
	    			$contentColumns[foreign_key_value],
	    			CASE
	    				WHEN $contentColumns[cnt_id] = '$cntId' THEN 2
	    				ELSE 1
	    			END as content_order
	    		FROM 
	    			$pntable[content]
	    		WHERE
	    			$contentColumns[cnt_id] = '$cntId' OR $contentColumns[foreign_key_value] = '$cntId'
	    		ORDER BY content_order DESC
	    		LIMIT 0,1";    
    	$result = $dbconn->Execute($sql);
		$queryStringParams = pnQueryStringSetVar('id', $result->fields[0], $args['return']);
		if ($args['return'])
			$returnParams = array_merge($returnParams, $queryStringParams);
	}
	if ($view) {
		$queryStringParams = pnQueryStringSetVar('view', $view, $args['return']);	
		if ($args['return'])
			$returnParams = array_merge($returnParams, $queryStringParams);
	}
	for ($idx = $index; $idx < count($parts); $idx+=2) {
		if ($parts[$idx] > '') {
			if ($parts[$idx] == 'sismodule')
				$parts[$idx + 1] = str_replace('___', '/', $parts[$idx + 1]);
			$queryStringParams = pnQueryStringSetVar($parts[$idx], $parts[$idx + 1], $args['return']);
			if ($args['return'])
				$returnParams = array_merge($returnParams, $queryStringParams);
		}
	}
	$otherParams = $_SERVER['REQUEST_URI'];
	$otherParams = split("[?]", $otherParams);
	if (isset($otherParams[1])) {			
		parse_str($otherParams[1], $otherParams);
		foreach ($otherParams as $key=>$param) {
			if ($key != 't') {
				$queryStringParams = pnQueryStringSetVar($key, $param, $args['return']);
				if ($args['return'])
					$returnParams = array_merge($returnParams, $queryStringParams);				
			}
		}
	}
	
	if ($args['return'])
		return $returnParams;
	else
		return true;
}


//wd_id
function cdk_userapi_getAccessibleTypes($args) {
	extract($args);
	$groups = sisUserGetGroups();
	$groups[-2] = '';		
	$groups = array_keys($groups);
    $dbconn =& pnDBGetConn(true);
	$pntable =& pnDBGetTables();
	
	$content_types = $pntable['content_types'];
	$content_types_column = &$pntable['content_types_column'];	
	$group_perms = $pntable['group_perms'];
	$group_perms_column = &$pntable['group_perms_column'];
	$web_directories = $pntable['web_directories'];
	$web_directories_column = $pntable['web_directories_column'];
	
	$sql = "SELECT 
				CONCAT($web_directories_column[id],'/',$web_directories_column[path]) 
			FROM 
				$web_directories
			WHERE
				$web_directories_column[id] = '$wd_id'";
	$result = $dbconn->Execute($sql);
	$wd_path = $result->fields[0];
	
	$sql = "SELECT DISTINCT
				$content_types_column[title] as title, 
				$content_types_column[ctp_id], 
				$content_types_column[type_name], 
				$group_perms_column[instance],
				$group_perms_column[level],
    			$content_types_column[type_fields],
    			$content_types_column[description],
				$content_types_column[type],    			    			
				$content_types_column[main_ctp_id],				
				$content_types_column[image],				
				$content_types_column[settings]				
			FROM 
				$content_types	
				INNER JOIN $group_perms 
					ON ($group_perms_column[component] = 'cdk::' AND $group_perms_column[level] >= ".ACCESS_EDIT." AND $group_perms_column[instance] LIKE CONCAT('%:', $content_types_column[type_name], ':%'))
				WHERE  $content_types_column[type] = 1 AND $content_types_column[state] = 2 AND $group_perms_column[gid] IN (".implode(',', $groups).")
				ORDER BY ".prepareForOrderBy('title');
	$result = $dbconn->Execute($sql);

	$contentTypes = array();
	while (!$result->EOF) {
		if ($wd_id) {
			if (strpos($result->fields[3], ':$web_directory$:') > 0) {
				$permision = split(":", $result->fields[3]);
				if (!ereg($permision[3], $wd_path)) {
					$result->moveNext();
					continue;
				}
			}
			else if ($result->fields[4] < ACCESS_ADMIN) {
				$result->moveNext();
				continue;
			}
		}
		else if ($result->fields[4] < ACCESS_EDIT) {
				$result->moveNext();
				continue;
		}

		if (!$contentTypes[$result->fields[1]]) {
			$fields = unserialize(base64_decode($result->fields[5]));
			if (is_array($fields)) {
				for($idx=0; $idx<count($fields); $idx++) {
					$fields[$idx] = json_decode($fields[$idx], true); 					
					$fieldArray = array();					
					foreach($fields[$idx] as $field)
						if ($field['name'] != 'dummy')
							$fieldArray[$field['name']] = urldecode($field['value']);
					$fields[$idx] = $fieldArray;					
				}
				$fields = array_merge($fields,$GLOBALS['contentTypeCommonFields']);
			}
			
			$image =  strToArray($result->fields[9]);
			foreach ($image as $key=>$value){
				$image=$value;
			}			
			if ($image['name'] > '')
				$image = WHERE_IS_PERSO.'/modules/cdk/upload/'.$result->fields[1].'/'.$image['name'];
			else
				$image = 'services/control_panel/images/default.gif';			
			if (!is_file($image))
				$image = 'services/control_panel/images/default.gif';
			$contentTypes[$result->fields[1]] = array('ctp_id' => $result->fields[1],
													  'type_name' => $result->fields[2],
													  'title' => localizedStr($result->fields[0]),
													  'type_fields' => $fields,
													  'image' => $image);
			if ($includeSettings) {
				$contentTypes[$result->fields[1]]['settings'] = unserialize($result->fields[10]);
			}													  
		}
		$result->moveNext();
	}	

	return $contentTypes;
}

function cdk_userapi_getNavigationPath($args) {
	extract($args);
	if (!$ctp_id)
		list($ctp_id, $id, $sisOp, $sisModule) = pnVarCleanFromInput('ctp_id', 'id', 'sisOp', 'sismodule');
	 
	if ($ctp_id && ($sisModule == 'user/call_function.php' || $sisModule == 'user/get_template_image.php'))
		return null;
		
	if ($ctp_id)
		$GLOBALS['__sis_current_service__'] = $ctp_id;
	$contentType = $GLOBALS['loadedContents'][$ctp_id];
	if (is_numeric($ctp_id) && !$contentType)
		$contentType = cdk_userapi_getType(array('ctp_id'=>$ctp_id));		
	if ($GLOBALS['wd_id'] && !$force) {
		$path = pnModAPIFunc('web_directory', 'user', 'getNavigationPath', array('wd_id'=>$GLOBALS['wd_id']));		
		return $path;
	}
	if (is_numeric($ctp_id) && is_numeric($id)) {	
		$pathCount = pnModAPIFunc('web_directory', 'user', 'getPathCount', array('item_id'=>$id, 'content_type'=>$contentType['type_name']));
		$mainPath = null;		
		if ($pathCount == 0 || $_GET['history'] == 'keep') {
			//$path =  pnModAPIFunc('pmk', 'user', 'getNavigationPath'); 
			$path = cdk_userapi_getDefaultPage(array('ctp_id'=>$ctp_id, 'contentType'=>&$contentType));
		}
		else {
			/*if ($pathCount > 1) {		
				$typePageId = pnModAPIFunc('pmk', 'user', 'getServicePage', array('service'=>'cdk_'.$ctp_id));
				$parentPath = pnModAPIFunc('pmk', 'user', 'getNavigationPath', array('page_id'=>$typePageId));
				$mainPath = $parentPath[count($parentPath) - 1]['wd_id'];
			}*/
			$path = pnModAPIFunc('web_directory', 'user', 'getPath', array('item_id'=>$id, 'content_type'=>$contentType['type_name'], 'main_path'=>$mainPath, 'get_full_path'=>$get_full));
		}
		return $path;
	}
	//else if (is_numeric($ctp_id) && (strpos($sisModule, 'user/') === 'user/content_view.php' || $sisModule == 'user/content_edit.php' || ($sisModule == 'user/content_search.php' && $_REQUEST['query']))) {
	else if (is_numeric($ctp_id) && strpos($sisModule, 'user/') === 0 ) {
		$path = cdk_userapi_getDefaultPage(array('ctp_id'=>$ctp_id, 'contentType'=>&$contentType, 'sisModule'=>$sisModule));			
		return $path;
	}
	else if (is_numeric($ctp_id)) {
		if ($GLOBALS['wd_id'])
			$path = pnModAPIFunc('web_directory', 'user', 'getNavigationPath', array('wd_id'=>$GLOBALS['wd_id']));		
		else {
			$path[] = array('title'=>localizedStr($contentType['title']), 'url'=>pnModURL('cdk', 'user', 'loadmodule' , array('system'=>'cdk', 'sismodule'=>'user/content_home_page.php', 'ctp_id'=>$ctp_id)));
			/*if (!$getTypeWebDirectory) {
			    $dbconn =& pnDBGetConn(true);
			
				$sql = "SELECT sp_id FROM saman_web_directories WHERE sp_type_name = 'cdk_$ctp_id'";
				$result = $dbconn->Execute($sql);
				if ($result->fields[0]) {
					$path = pnModAPIFunc('web_directory', 'user', 'getNavigationPath', array('wd_id'=>$result->fields[0]));		
					return $path;
				}							
			}*/				
		}
		return $path;
		//return pnModAPIFunc('pmk', 'user', 'getNavigationPath');
	}
	
	return array();
}

/*
$ctp_id
$contentType
$sisModule
*/
function cdk_userapi_getDefaultPage($args) {	
	extract($args);
    $dbconn =& pnDBGetConn(true);

	$sql = "SELECT sp_id FROM saman_web_directories WHERE sp_type_name = 'cdk_$ctp_id' AND sp_portal_id='$GLOBALS[portal_id]'";
	$result = $dbconn->Execute($sql);
	$main_ctp_id = $ctp_id;
	while (!$result->fields[0] && $main_ctp_id ) {
		$sql = "SELECT main_ctp_id FROM saman_content_types WHERE ctp_id = '$main_ctp_id'";		
		$result = $dbconn->Execute($sql);
		$main_ctp_id = $result->fields[0];		
		$sql = "SELECT sp_id FROM saman_web_directories WHERE sp_type_name = 'cdk_$main_ctp_id' AND sp_portal_id='$GLOBALS[portal_id]'";
		$result = $dbconn->Execute($sql);		
	}	
	$homePageUrl = array('title'=>localizedStr($contentType['title']), 'url'=>pnModURL('cdk', 'user', 'loadmodule' , array('system'=>'cdk', 'sismodule'=>'user/content_home_page.php', 'ctp_id'=>$ctp_id)));
	$path = array();
	if ($result->fields[0]) {
		if ($sisModule && $sisModule != 'user/content_view.php' && $sisModule != 'user/content_edit.php') {
			$wdInfo = pnModAPIFunc('web_directory', 'user', 'getWebDirectoryInfo', array('wd_id'=>$result->fields[0]));				
			if (!$wdInfo['page_id'])
				return array($homePageUrl);			
		}
		$path = pnModAPIFunc('web_directory', 'user', 'getNavigationPath', array('wd_id'=>$result->fields[0], 'get_full_path'=>true));
		if ($path[count($path) - 1]['wd_id'])
			$homePageUrl['wd_id'] =  $path[count($path) - 1]['wd_id'];
		$path[count($path) - 1] = $homePageUrl;
	}
	else
		$path[] = $homePageUrl;						
	return $path;	
}

/*
wd_id
gid
type_name
ctp_id
*/
function cdk_userapi_installService($args) {
	extract($args);
	$orgGet = $_GET;
	$_GET['ctp_id'] = $ctp_id;
	$_GET['subportal_id'] = $subportal_id;
	$_GET['clone_subportal_id'] = $clone_subportal_id;
	pnModFunc('cdk', 'user', 'loadmodule', array('system'=>'cdk', 'sismodule' => 'configuration/content_type_install_in_subportal.php'));     	
}

/*
wd_id
gid
type_name
ctp_id
*/
function cdk_userapi_assignSubportalAdmin($args) {
	extract($args);
	if (!isset($wd_id) || !isset($gid) || !isset($type_name) || !isset($ctp_id))
		return false;

	$content_type = cdk_userapi_getType(array('ctp_id'=>$ctp_id));
	
	if (!$content_type)
		return false;

	if (!pnModAPIFunc('permissions', 'admin', 'create', array('type'=>'group', 'realm'=>0, 'id'=>$gid, 'component'=>'cdk::', 'instance'=>":$type_name:", 'level'=>ACCESS_ADMIN, 'insseq'=>-1)))
		return false;	
		
/*	if (!pnModAPIFunc('permissions', 'admin', 'create', array('type'=>'group', 'realm'=>0, 'id'=>$gid, 'component'=>'cdk::', 'instance'=>":$type_name:\$web_directory\$:$wd_id", 'level'=>ACCESS_ADMIN, 'insseq'=>-1)))
		return false;	*/

	if ($content_type['wkf_id']) {
	    $dbconn =& pnDBGetConn(true);
		$pntable =& pnDBGetTables();
		
		$workflow_steps_table = $pntable['workflow_steps'];
		$workflow_steps_column = &$pntable['workflow_steps_column'];	
		
		$sql = "SELECT $workflow_steps_column[wsp_id] FROM $workflow_steps_table WHERE $workflow_steps_column[wkf_id] = '$content_type[wkf_id]' AND $workflow_steps_column[step_order] IN (1, 1000, 1001)";
		$result = $dbconn->Execute($sql);
		if (!$result)
			return false;
		$wsp_ids = array();
		while (!$result->EOF) {
			$wsp_ids[] = $result->fields[0];
			$result->moveNext();
		}
		
		$workflow_step_roles_table = $pntable['workflow_step_roles'];
		$workflow_step_roles_column = &$pntable['workflow_step_roles_column'];	

		foreach ($wsp_ids as $wsp_id) {
			$sql = "INSERT INTO 
						$workflow_step_roles_table 
						($workflow_step_roles_column[wsp_id],$workflow_step_roles_column[rol_id])
					VALUES
						('$wsp_id', '$gid')";
			if (!$dbconn->Execute($sql))
				return false;
			
		}
	}
	
	return true;
}


/*
wd_id
gid
type_name
ctp_id
portal_id
*/
function cdk_userapi_deassignSubportalAdmin($args) {
	extract($args);
	if (!isset($wd_id) || !isset($gid) || !isset($type_name) || !isset($ctp_id))
		return false;

	$content_type = cdk_userapi_getType(array('ctp_id'=>$ctp_id));
	if($contentType['perm_type']==CNT_CONTENT_TYPE_PERM_TYPE_ROLE_BASE){
		$sisRoleController=new sisRoleController($contentType);
		@eval('$roles='.$contentType['settings']['roleSet']);
		if(is_array($roles)){
			$sisRoleController->roles=$roles;
			$sisRoleController->deleteAllSubportalGeneratedPermissions($portal_id);
		}
	}
	if (!$content_type)
		return false;

    $dbconn =& pnDBGetConn(true);
	$pntable =& pnDBGetTables();
		
	$groups_table = $pntable['groups'];
	$groups_column = $pntable['groups_column'];
	$users_table = $pntable['users'];
	$users_column = $pntable['users_column'];

	if (!pnModAPIFunc('permissions', 'admin', 'delete', array('type'=>'group', 'id'=>$gid, 'component'=>'cdk::', 'instance'=>":$type_name:")))
		return false;	

	$sql = "SELECT $groups_column[gid] FROM $groups_table WHERE $groups_column[portal_id] = '$portal_id'";
	$result = $dbconn->Execute($sql);	
	$gids = array($gid);
	while (!$result->EOF) {
		if (!pnModAPIFunc('permissions', 'admin', 'delete', array('type'=>'group', 'id'=>$result->fields[0], 'component'=>'cdk::', 'instance'=>":$type_name:")))
			return false;			
		$gids[] = $result->fields[0];
		$result->moveNext();
	}
	
	$sql = "SELECT $users_column[uid] FROM $users_table WHERE $users_column[portal_id] = '$portal_id'";
	$result = $dbconn->Execute($sql);	
	while (!$result->EOF) {
		if (!pnModAPIFunc('permissions', 'admin', 'delete', array('type'=>'user', 'id'=>$result->fields[0], 'component'=>'cdk::', 'instance'=>":$type_name:")))
			return false;			
		$result->moveNext();
	}
		
	if ($content_type['wkf_id']) {				
		$workflow_steps_table = $pntable['workflow_steps'];
		$workflow_steps_column = &$pntable['workflow_steps_column'];	
		
		$sql = "SELECT $workflow_steps_column[wsp_id] FROM $workflow_steps_table WHERE $workflow_steps_column[wkf_id] = '$content_type[wkf_id]' AND $workflow_steps_column[step_order] IN (1, 1000, 1001)";
		$result = $dbconn->Execute($sql);
		if (!$result)
			return false;
		$wsp_ids = array();
		while (!$result->EOF) {
			$wsp_ids[] = $result->fields[0];
			$result->moveNext();
		}
		$workflow_step_roles_table = $pntable['workflow_step_roles'];
		$workflow_step_roles_column = &$pntable['workflow_step_roles_column'];	
		
		$sql = "DELETE FROM $workflow_step_roles_table 
				WHERE
					$workflow_step_roles_column[wsp_id] IN (".implode(',', $wsp_ids).") AND $workflow_step_roles_column[rol_id] IN (".implode(',', $gids).")";
		if (!$dbconn->Execute($sql))
			return false;		
	}
	
	return true;
}

function cdk_userapi_deleteSubportalData($args){
	extract($args);
	if (!$portal_id || !pnSecAuthAction(0, '.*', ".*", ACCESS_ADMIN)) 
		return false;	

	$_GET['portal_id'] = $portal_id;
	
	pnModFunc('cdk', 'user', 'loadmodule', array('system'=>'cdk', 'sismodule'=>'management/delete_subportal_contents.php'));
	
	return ;
}

/*
	ctp_id
	id
	page_id
*/
function cdk_userapi_assginPage($args) {
	extract($args);
	if (!$page_id || !$id || !$ctp_id)
		return;

    $dbconn =& pnDBGetConn(true);
	$pntable =& pnDBGetTables();
	
	$content_table = $pntable['content'];
	$content_column = $pntable['content_column'];
		
	$sql = "UPDATE $content_table SET $content_column[page_id] = '$page_id' WHERE $content_column[ctp_id] = '$ctp_id' AND $content_column[foreign_key_value] = '$id'";
	if (!$dbconn->Execute($sql))
		return false;
	return true;
}

/*
	ctp_id
	id
	page_id
*/
function cdk_userapi_deassginPage($args) {
	extract($args);
	if (!$page_id || !$id || !$ctp_id)
		return;

    $dbconn =& pnDBGetConn(true);
	$pntable =& pnDBGetTables();
	
	$content_table = $pntable['content'];
	$content_column = $pntable['content_column'];
		
	$sql = "UPDATE $content_table SET $content_column[page_id] = NULL WHERE $content_column[ctp_id] = '$ctp_id' AND $content_column[foreign_key_value] = '$id'";
	if (!$dbconn->Execute($sql))
		return false;
	return true;
}

/*
$ctp_id
$id
$rate
*/
function cdk_userapi_rateContent($args) {
	extract($args);
	if (!$ctp_id || !$id || !$rate)
		return;

    $dbconn =& pnDBGetConn(true);
	$pntable =& pnDBGetTables();
	
	$content_table = $pntable['content'];
	$content_column = $pntable['content_column'];
	$sql = "UPDATE $content_table SET $content_column[rate] = '$rate' WHERE $content_column[ctp_id] = '$ctp_id' AND $content_column[foreign_key_value] = '$id'";

	if (!$dbconn->Execute($sql))
		return false;
	return true;
}

/*
 $template
 $extraReplace
*/
function cdk_userapi_compileTemplate($args) {
	if (!pnUserLoggedIn())
		return;
	
	extract($args);
	$template = str_replace(array("!q!", "!dq!"), array("'", '"'), $template);    	
	if ($includePHPTags) {
		$templateParts = split('##', $template);
		$code = '';
		$templatePartsCount = count($templateParts);
		for ($idx=0; $idx<$templatePartsCount; $idx++) {
			if ($idx%2 == 0)
				$code .= "echo '".str_replace("'", "\\'", $templateParts[$idx])."'; ";
			else {
				if (!$extraReplace)
					$code .= $templateParts[$idx]."; ";			
				else {
					$tmpCode = preg_replace("/(fieldCaption|fieldContent|childContent|newContentLink|deleteContentLink|editContentLink|newChildLink|editChildLink|deleteChildLink)(\s*)\[(.*)\]/i", "\${1}\${2}(\${3})", $templateParts[$idx]);
					$tmpCode = preg_replace("/(options)(\s*)\[(.*)\]/i", "\${1}\${2}(\${3})", $tmpCode);
					$tmpCode = preg_replace("/(options)(\s*)\((.*)\)/i", "array\${2}(\${3})", $tmpCode);
	    			$code .= $tmpCode . ";\r\n";
	    			/*$code .= str_replace(array("align", "notAlign"), array("align()", "notAlign()"), $tmpCode) . ";\r\n";    			*/
					/*$code .= str_replace(array("[", "]", "options", "align", "notAlign"), array("(", ")", "array", "align()", "notAlign()"), $templateParts[$idx]) . "; ";*/
				}
			}
		}
	}
	else 
		$code = $template;
	return  $code;	
}

/*
 $table
 $field
 $code
 $primaryKey
 $primaryKeyValue
*/
function cdk_userapi_saveTemplate($args) {
	if (!pnUserLoggedIn())
		return;
	if (!trim($_SESSION['_codeEditorSecKey_']) || pnVarCleanFromInput('secKey') != $_SESSION['_codeEditorSecKey_']) {
		return;
	}
	extract($args);
	list($dbconn) = pnDBGetConn();	
	if ($table == 'saman_content_types' && strpos($field, "settings$") !== false) {
		$sql = "SELECT settings, type_name  FROM saman_content_types WHERE ctp_id = '$primaryKeyValue'";		
		$result = $dbconn->Execute($sql);
		$settingsValue = $result->fields[0];
		$typeName = $result->fields[1];
		$settingsValue = unserialize($settingsValue);
		$field = split("[$]", $field);
		$settingsValue[$field[1]] = $code;
		$settingsValue = serialize($settingsValue);
		$sql = "UPDATE saman_content_types SET settings = '".pnVarPrepForStore($settingsValue)."' WHERE ctp_id = '$primaryKeyValue'";
	    if (!$dbconn->Execute($sql))
	    	return 'error';
	    sisCacheDel("ctp_$primaryKeyValue");
	    sisCacheDel("ctp_$typeName");
		return;
	}
	
	$sql = "UPDATE $table SET $field = '".pnVarPrepForStore($code)."' WHERE $primaryKey = '$primaryKeyValue'";
    if (!$dbconn->Execute($sql))
    	return 'error';
	
	if ($table == 'saman_content_type_templates') {
		$code = cdk_userapi_compileTemplate(array('template'=>$code, 'extraReplace'=>true, 'includePHPTags'=>$includePHPTags));
		$sql = "UPDATE $table SET compiled_$field = '".pnVarPrepForStore($code)."' WHERE $primaryKey = '$primaryKeyValue'";
		list($dbconn) = pnDBGetConn();	
	    if (!$dbconn->Execute($sql))
	    	return 'error';
	}
	return;
}



function cdk_userapi_registerTypeFunctions($args) {	
	extract($args);
	
	$contentType=cdk_userapi_getType(array('ctp_id'=>$ctp_id));	
	if(!function_exists("sisLoadContentTypeRoles")){
		pnModFunc('cdk', 'user', 'loadmodule', array('system'=>'cdk', 'sismodule'=>'header.php'));
		require_once(sisGetSetting('utils'));
	}
	sisLoadContentTypeRoles($ctp_id);	
	if ($GLOBALS['loadedFormFunctions'][$type_name.'_'.$ctp_id])
		return $GLOBALS['loadedFormFunctions'][$type_name.'_'.$ctp_id];
	
	if ($GLOBALS['loadedFormFunctions'][$ctp_id]) {
		$typeName =	$GLOBALS['loadedContents'][$ctp_id]['type_name'];
		if (!$typeName) {
			$typeName = $GLOBALS['loadedFormFunctions'][$ctp_id];
		}
		return $typeName;
	}
	list($dbconn) = pnDBGetConn();	
	$pntable = pnDBGetTables();
	$contentTypeColumns = &$pntable['content_types_column'];

	if ($ctp_id)
	    $sql = "SELECT 
	    			$contentTypeColumns[ctp_id],
	    			$contentTypeColumns[main_ctp_id],
	    			$contentTypeColumns[settings],
	    			$contentTypeColumns[type_name],
	    			$contentTypeColumns[lang_consts],
	    			$contentTypeColumns[type],
	    			$contentTypeColumns[perm_type]
	    		FROM 
	    			$pntable[content_types] 
	    		WHERE $contentTypeColumns[ctp_id] = '$ctp_id' AND $contentTypeColumns[state] = 2";
	else if ($type_name)
	    $sql = "SELECT 
	    			$contentTypeColumns[ctp_id],
	    			$contentTypeColumns[main_ctp_id],
	    			$contentTypeColumns[settings],
	    			$contentTypeColumns[type_name],
	    			$contentTypeColumns[lang_consts],
	    			$contentTypeColumns[type],
	    			$contentTypeColumns[perm_type]	    			
	    		FROM 
	    			$pntable[content_types] 
	    		WHERE $contentTypeColumns[type_name] = '$type_name' AND $contentTypeColumns[state] = 2";
	else 
		return;
	
    $result = $dbconn->Execute($sql);
	$ctpId = $result->fields[0];
	$typeName = $result->fields[3];
	if (!$ctpId)
		return;	
		
	$styleFile = $GLOBALS['themePath'].'style/'.$typeName.'.css';
	if (is_file($styleFile)) {
		$GLOBALS['__sisRapid_global_settings__'] .= '<link  rel="stylesheet" type="text/css" href="'. $GLOBALS['themePath'].'style/'.$typeName.'.css" />';
	}
	$styleFile = $GLOBALS['themePath'].'style/'.$typeName.'_'.pnUserGetLang().'.css';
	if (is_file($styleFile)) {
		$GLOBALS['__sisRapid_global_settings__'] .= '<link  rel="stylesheet" type="text/css" href="'. $GLOBALS['themePath'].'style/'.$typeName.'_'.pnUserGetLang().'.css" />';
	}
		
	if ($_GET['disable_functions'])	{
		$GLOBALS['loadedFormFunctions'][$type_name.'_'.$ctp_id] = $result->fields[3];		
		return $result->fields[3];
	}
		
	if ($GLOBALS['loadedFormFunctions'][$ctpId]) {
		$GLOBALS['loadedFormFunctions'][$type_name.'_'.$ctp_id] = $result->fields[3];
		return $result->fields[3];
	}

    if ($result->fields[1])
    	cdk_userapi_registerTypeFunctions(array('ctp_id' => $result->fields[1]));
	$GLOBALS['loadedFormFunctions'][$ctpId] = true;
	$GLOBALS['loadedFormFunctions'][$type_name.'_'.$ctp_id] = $result->fields[3];
    $settings = unserialize($result->fields[2]);
    $consts = unserialize($result->fields[4]);
    @eval($settings['formFunctions']);
	foreach ($consts[pnUserGetLang()] as $key=>$value) {
		@define($key, $value);			
	}
	
	return $result->fields[3];    
}

function cdk_userapi_getSubportalTypes($args) {
	extract($args);
	if (!isset($portal_id)) 
		$portal_id=$GLOBALS['portal_id'];
		
	pnModDBInfoLoad('subportal');
	list($dbconn) = pnDBGetConn();	
	$pntable = pnDBGetTables();
	$contentTypeColumns = &$pntable['content_types_column'];
	$subportalModulesColumn = &$pntable['subportal_modules_column'];
	
	$type_fields = $contentTypeColumns['type_fields'];
	if ($simpleList)
		$type_fields = "''";;
	if ($portal_id!=0) {
	    $sql = "SELECT 
	    			$contentTypeColumns[ctp_id],
	    			$contentTypeColumns[type_name],
	    			$contentTypeColumns[title],   			
	    			$type_fields,
	    			$contentTypeColumns[description],
					$contentTypeColumns[type],    			    			
					$contentTypeColumns[main_ctp_id]
	    		FROM 
	    			$pntable[content_types]
	    		LEFT JOIN $pntable[subportal_modules]
	    		ON replace($subportalModulesColumn[module],'cdk_','')=$contentTypeColumns[ctp_id]
	    		WHERE
	    			$contentTypeColumns[type] = 1 AND 
	    			$contentTypeColumns[state] = 2 AND 
	    			$contentTypeColumns[main_ctp_id] IS NULL AND
	    			$subportalModulesColumn[subportal_id]='$portal_id'
	    			
	    		ORDER BY ".prepareForOrderBy($contentTypeColumns['title']);
	}
	else {
		 $sql = "SELECT 
	    			$contentTypeColumns[ctp_id],
	    			$contentTypeColumns[type_name],
	    			$contentTypeColumns[title],   			
	    			$type_fields,
	    			$contentTypeColumns[description],
					$contentTypeColumns[type],    			    			
					$contentTypeColumns[main_ctp_id]
	    		FROM 
	    			$pntable[content_types]
	    		
	    		WHERE
	    			$contentTypeColumns[type] = 1 AND 
	    			$contentTypeColumns[state] = 2 AND
	    			$contentTypeColumns[main_ctp_id] IS NULL 
	    			 
	    		ORDER BY ".prepareForOrderBy($contentTypeColumns['title']);
	}
	
    $result = $dbconn->Execute($sql);
    $contentTypes = array();
    if ($dbconn->ErrorNo() == 0) {
		while (!$result->EOF) {
			list($ctp_id, $type_name, $title, $type_fiedls, $description,$type,$main_ctp_id) = $result->fields;
			$fields = unserialize(base64_decode($type_fiedls));
			if (is_array($fields)) {
				for($idx=0; $idx<count($fields); $idx++) {
					$fields[$idx] = json_decode($fields[$idx], true); 					
					$fieldArray = array();					
					foreach($fields[$idx] as $field)
						if ($field['name'] != 'dummy')
							$fieldArray[$field['name']] = urldecode($field['value']);
					$fields[$idx] = $fieldArray;					
				}
				$fields = array_merge($fields,$GLOBALS['contentTypeCommonFields']);
			}
			$title = unserialize($title);
			$title = $title[pnUserGetLang()];			
			$description = unserialize($description);
			$description = $description[pnUserGetLang()];			
			$contentTypes[$type_name] = array('ctp_id'=>$ctp_id, 'type_name'=>$type_name, 'title'=> $title, 'description' => $description, 'type_fields'=>$fields,'type'=>$type,'main_ctp_id'=>$main_ctp_id);
			$result->moveNext();
		}
        $result->Close();
    }
	return $contentTypes;
}


/*
wd_id
gid
type_name
ctp_id
*/
function cdk_userapi_upgradeService($args) {
	if (!pnUserLoggedIn())
		return;	
	extract($args);
	$orgGet = $_GET;
	$_GET['ctp_id'] = $ctp_id;
	$_GET['subportal_id'] = $subportal_id;
	pnModFunc('cdk', 'user', 'loadmodule', array('system'=>'cdk', 'sismodule' => 'configuration/content_type_upgrade_in_subportal.php'));     	
}

/*
ctp_id
cnt_id
*/
function cdk_userapi_updateServerCounter($args) {
	extract($args);
	if (!$ctp_id || !$cnt_id) {
		return;
	}
	
	if ($_SESSION['__lastContentID'] != $cnt_id && pnVarCleanFromInput('sisOp') != 'edit') {		
		list($dbconn) = pnDBGetConn();	
		$pntable = pnDBGetTables();
		
		$content_table = $pntable['content'];
		$content_column = $pntable['content_column'];
			
		$sql = "UPDATE $content_table SET $content_column[counter] = $content_column[counter] + 1 WHERE $content_column[ctp_id] = '".intval($ctp_id)."' AND $content_column[cnt_id] = '".intval($cnt_id)."'";
		$_SESSION['__lastContentID'] = $cnt_id;		
		if (!$dbconn->Execute($sql))
			return;
	}	
}

/*
$query
*/
function cdk_userapi_templateSamrtSearch($args) {
    extract($args);
    $query = str_replace("'", "", $query);
    if (!$query) {
    	return array();
    }
    
    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

	$template_table = $pntable['content_type_templates'];
	$template_column = $pntable['content_type_templates_column'];
	$content_type_table = $pntable['content_types'];
	$content_type_column = $pntable['content_types_column'];
	
	$sql = "SELECT 
				$template_column[ctt_id],
				$template_column[ctp_id],
				$template_column[template_name],
				$content_type_column[type_name],
				$content_type_column[type],
				$template_table.type as template_type
			FROM
				$template_table
				INNER JOIN $content_type_table
					ON ($template_column[ctp_id] = $content_type_column[ctp_id])
			WHERE
				$template_column[template_name] LIKE '%$query%' AND
				$content_type_column[type] IN (1, 2, 5)";
	
	$result = $dbconn->Execute($sql);
	$templates = array();
    while (!$result->EOF) {
    	list($ctt_id, $ctp_id, $name, $type_name, $type, $template_type) = $result->fields;
    	if ($template_type == 1) {
    		$templates[] = array('name'=>"$type_name - $name", 'url'=>pnModURL($type==1?'cdk':'fdk', 'user', 'loadmodule', array('system'=>$type==1?'cdk':'fdk', 'sismodule'=>'configuration/template_designer.php', 'ctp_id'=>$ctp_id, 'ctt_id'=>$ctt_id)));
    	}
    	else {
    		$templates[] = array('name'=>"$type_name - $name", 'url'=>pnModURL($type==1?'cdk':'fdk', 'user', 'loadmodule', array('system'=>$type==1?'cdk':'fdk', 'sismodule'=>'configuration/content_template_edit.php', 'sisOp'=>'edit','ctp_id'=>$ctp_id, 'ctt_id'=>$ctt_id)));
    	}
    	$result->moveNext();
    }
    
    return $templates;
}

/*
ctp_id
*/
function cdk_userapi_deactivateService($args) {
	extract($args);
	$ctp_id = intval($ctp_id);
	if (!$ctp_id) {
		return;
	}
    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

	$content_type_table = $pntable['content_types'];
	$content_type_column = $pntable['content_types_column'];
	
	$sql = "UPDATE $content_type_table
			SET
				$content_type_column[state] = 1
			WHERE
				$content_type_column[ctp_id] = $ctp_id";
	$dbconn->Execute($sql);	
	return true;	
}

/*
ctp_id
*/
function cdk_userapi_activateService($args) {
	extract($args);
	$ctp_id = intval($ctp_id);
	if (!$ctp_id) {
		return;
	}
    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

	$content_type_table = $pntable['content_types'];
	$content_type_column = $pntable['content_types_column'];
	
	$sql = "UPDATE $content_type_table
			SET
				$content_type_column[state] = 2
			WHERE
				$content_type_column[ctp_id] = $ctp_id";
	$dbconn->Execute($sql);	
	return true;	
}


/*
ctp_id
*/
function cdk_userapi_getReports($args) {
	extract($args);
	$ctp_id = intval($ctp_id);
	if (!$ctp_id) {
		return;
	}
    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

	$contentReportsTable = $pntable['content_type_reports'];
	$contentReportsColumn = $pntable['content_type_reports_column'];
	
	$sql = "SELECT
				$contentReportsColumn[crp_id],
				$contentReportsColumn[report_name],
				$contentReportsColumn[title],
				$contentReportsColumn[type],
				$contentReportsColumn[portal_id]
			FROM
				$contentReportsTable
			WHERE
				$contentReportsColumn[ctp_id] = $ctp_id
			ORDER BY 
				$contentReportsColumn[type]";
	$result = $dbconn->Execute($sql);	
	
	$reports = array();
	while (!$result->EOF) {
		list($crpId, $reportName, $title, $type) = $result->fields;
		$reports[$type][] = array('id' => $crpId, 'name' => $reportName, 'title' => localizedStr($title), 'type' => $type);
		$result->moveNext();
	}
	return $reports;		
}

/*
ctp_id,
report_name
*/
function cdk_userapi_getReport($args) {
	extract($args);
	$ctp_id = intval($ctp_id);
	if (!$ctp_id || !trim($report_name)) {
		return;
	}
    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

	$contentReportsTable = $pntable['content_type_reports'];
	$contentReportsColumn = $pntable['content_type_reports_column'];
	
	$sql = "SELECT
				$contentReportsColumn[content]
			FROM
				$contentReportsTable
			WHERE
				$contentReportsColumn[ctp_id] = $ctp_id AND
				$contentReportsColumn[report_name] = '$report_name'";
	$result = $dbconn->Execute($sql);	
	$report = unserialize($result->fields[0]);
	$reports = array();
	while (!$result->EOF) {
		list($crpId, $reportName, $title, $type) = $result->fields;
		$reports[$type][] = array('id' => $crpId, 'name' => $reportName, 'title' => localizedStr($title), 'type' => $type);
		$result->moveNext();
	}
	return $report;
}

/*
typeName
*/
function cdk_userapi_getAccessibleDirectories($args) {
	extract($args);
	if (!$typeName) {
		return;
	}
	$uid = pnUserGetVar('uid');
	if (!$uid)
		$uid = -1;
	$userGroups = getUserGroups($uid);		
	list($dbconn) = pnDBGetConn();	
	$pntable = pnDBGetTables();
	
    $result = $dbconn->Execute("SELECT 
    								DISTINCT sp_level,
									sp_instance
								FROM
									saman_user_perms
								WHERE
						 			sp_component = 'cdk::' AND sp_instance LIKE ':$typeName:\$web_directory$:%' AND sp_uid = '$uid'
						 		UNION
						 		SELECT 
    								DISTINCT sp_level,
									sp_instance
								FROM
									saman_group_perms
								WHERE
						 			sp_component = 'cdk::' AND sp_instance LIKE ':$typeName:\$web_directory$:%' AND sp_gid IN (".implode(',', $userGroups).")
						 		");
	$directories = array('allow'=>array(), 'deny'=>array());
	while (!$result->EOF) {
		list($level, $instance) = $result->fields;
		$instance = str_replace(":$typeName:\$web_directory$:", "", $instance);
		if ($level) {
			$directories['allow'][] = $instance;
		}
		else {
			$directories['deny'][] = $instance;
		}
		$result->moveNext();		
	}	
	return $directories;
}

function cdk_userapi_raiseContentUpdateEvent($args) {
	extract($args);
	if (!$contentType || !$id) {
		return;
	}
	$oldContent = $GLOBALS['contentType'];	
	$GLOBALS['contentType'] = getContentType($contentType);
	if ($GLOBALS['contentType']) {
		require_once(sisGetSetting('sisDatabase'));
		$table = sisTable::newTable("dynamic_content::".sprintf(CONTENT_TYPE_TABLE_NAME, $contentType));
		$table->select($record, "*", "id=$id");		
		if (is_array($record) && count($record)) {
			$table->update($record, "id=$id");
		}
	}
	$GLOBALS['contentType'] = $oldContent;
	return;
}
?>