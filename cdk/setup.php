<?php
// Saman Portal
// Copyright (C) 2008 by the Saman Information Structure Development Team.
// http://www.sis-eg.com/
// ----------------------------------------------------------------------

function cdk_init() {	
	if (pnModAvailable('fdk')) {		
		cdk_defaultdata();    	
		return true;
	}	
	
    list($dbconn) = pnDBGetConn();
    $pntable = pnDBGetTables();

    $content_typestable = $pntable['content_types'];
    $content_typescolumn = &$pntable['content_types_column'];
    $sql = "CREATE TABLE $content_typestable (
   				$content_typescolumn[ctp_id] int(11) not null auto_increment,
				$content_typescolumn[type_name] varchar(255) not null,
				$content_typescolumn[title] varchar(255) not null,
				$content_typescolumn[description] varchar(1000),
				$content_typescolumn[title_field] varchar(255),
				$content_typescolumn[enable_locking] bool,
				$content_typescolumn[enable_scheduling] bool,
				$content_typescolumn[image] varchar(1000),
				$content_typescolumn[type_fields] text,
				$content_typescolumn[settings] varchar(1000),
				$content_typescolumn[admin_list_template_id] int(11),
				$content_typescolumn[admin_search_template_id] int(11),
				$content_typescolumn[admin_view_template_id] int(11),
				$content_typescolumn[admin_edit_template_id] int(11),
				$content_typescolumn[user_list_template_id] int(11),
				$content_typescolumn[user_search_template_id] int(11),
				$content_typescolumn[user_view_template_id] int(11),
				$content_typescolumn[user_edit_template_id] int(11),
				$content_typescolumn[wkf_id] int(11),
				$content_typescolumn[last_modified_date] date not null,
				$content_typescolumn[last_modified_time] time not null,
				$content_typescolumn[last_modified_user_id] int(11) not null,
				$content_typescolumn[control_one_record] smallint(6) default '0',				
				$content_typescolumn[type] smallint(6),				
				$content_typescolumn[state] smallint(1),				
				$content_typescolumn[category] int(11),				
				$content_typescolumn[workflow_type] smallint(1),				
				$content_typescolumn[display_order] int(11),				
				$content_typescolumn[refer_type] smallint(1) not null DEFAULT 1,				
				$content_typescolumn[ui_type] smallint(1) null DEFAULT 0,				
   				PRIMARY KEY (ctp_id),
   				KEY type_name (type_name),
   				KEY admin_list_template_id (admin_list_template_id),
   				KEY admin_search_template_id (admin_search_template_id),
   				KEY admin_view_template_id (admin_view_template_id),
   				KEY admin_edit_template_id (admin_edit_template_id),
   				KEY user_list_template_id (user_list_template_id),
   				KEY user_search_template_id (user_search_template_id),
   				KEY user_view_template_id (user_view_template_id),
   				KEY user_edit_template_id (user_edit_template_id),
   				KEY wkf_id (wkf_id)
			)ENGINE=MyISAM DEFAULT CHARSET=utf8";
	$dbconn->Execute($sql);
    if ($dbconn->ErrorNo() != 0) {
    	logAdd("Error in query \\r\\n\\r\\n".$sql, LOG_ERROR);
    	pnSessionSetVar('errormsg', _DBSELECTERROR . ": " .$sql);
        return false;
    }
    	
    $content_type_templatestable = $pntable['content_type_templates'];
    $content_type_templatescolumn = &$pntable['content_type_templates_column'];
    $sql = "CREATE TABLE $content_type_templatestable (
				$content_type_templatescolumn[ctt_id] int(11) not null auto_increment,
				$content_type_templatescolumn[ctp_id] int(11) not null,
				$content_type_templatescolumn[template_name] varchar(255) not null,
				$content_type_templatescolumn[lang] varchar(3),
				$content_type_templatescolumn[image] varchar(1000),
				$content_type_templatescolumn[template] longtext,
				$content_type_templatescolumn[compiled_template] longtext,
				$content_type_templatescolumn[header_template] longtext,
				$content_type_templatescolumn[compiled_header_template] longtext,
				$content_type_templatescolumn[footer_template] longtext,
				$content_type_templatescolumn[compiled_footer_template] longtext,
				$content_type_templatescolumn[layout_type] tinyint(1),
				$content_type_templatescolumn[last_modified_date] date not null,
				$content_type_templatescolumn[last_modified_time] time not null,
				$content_type_templatescolumn[last_modified_user_id] int(11) not null,
				PRIMARY KEY (ctt_id),
				KEY ctp_id (ctp_id),
				KEY template_name (template_name)
			)ENGINE=MyISAM DEFAULT CHARSET=utf8";
	$dbconn->Execute($sql);
    if ($dbconn->ErrorNo() != 0) {
    	logAdd("Error in query \\r\\n\\r\\n".$sql, LOG_ERROR);
    	pnSessionSetVar('errormsg', _DBSELECTERROR . ": " .$sql);
        return false;
    }
    	
    $content_type_directoriestable = $pntable['content_type_directories'];
    $content_type_directoriescolumn = &$pntable['content_type_directories_column'];
    $sql = "CREATE TABLE $content_type_directoriestable (
				$content_type_directoriescolumn[ctc_id] int(11) not null auto_increment,
				$content_type_directoriescolumn[ctp_id] int(11) not null,
				$content_type_directoriescolumn[wdc_id] int(11) not null,
				PRIMARY KEY (ctc_id),
				UNIQUE KEY ctp_id2 (ctp_id, wdc_id),
				KEY ctp_id (ctp_id),
				KEY wdc_id (wdc_id)
			)ENGINE=MyISAM DEFAULT CHARSET=utf8";
	$dbconn->Execute($sql);
    if ($dbconn->ErrorNo() != 0) {
    	logAdd("Error in query \\r\\n\\r\\n".$sql, LOG_ERROR);
    	pnSessionSetVar('errormsg', _DBSELECTERROR . ": " .$sql);
        return false;
    }
    	
    $content_related_typestable = $pntable['content_related_types'];
    $content_related_typescolumn = &$pntable['content_related_types_column'];
    $sql = "CREATE TABLE $content_related_typestable (
				  $content_related_typescolumn[crt_id] int(11) NOT NULL auto_increment,
				  $content_related_typescolumn[parent_ctp_id] int(11) NOT NULL,
				  $content_related_typescolumn[child_ctp_id] int(11) default NULL,
				  PRIMARY KEY  (crt_id),
				  UNIQUE KEY crt_id (crt_id),
				  UNIQUE KEY child_ctp_id (child_ctp_id)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
	$dbconn->Execute($sql);
    if ($dbconn->ErrorNo() != 0) {
    	logAdd("Error in query \\r\\n\\r\\n".$sql, LOG_ERROR);
    	pnSessionSetVar('errormsg', _DBSELECTERROR . ": " .$sql);
        return false;
    }
    	
    $content_relationstable = $pntable['content_relations'];
    $content_relationscolumn = &$pntable['content_relations_column'];
    $sql = "CREATE TABLE $content_relationstable (
  						$content_relationscolumn[crl_id] int(11) NOT NULL auto_increment,
  						$content_relationscolumn[parent_ctp_id] int(11) NOT NULL,
  						$content_relationscolumn[child_ctp_id] int(11) NOT NULL,
  						$content_relationscolumn[parent_id] int(11) NOT NULL,
  						$content_relationscolumn[parent_cnt_id] int(11) NOT NULL,
  						$content_relationscolumn[child_id] int(11) NOT NULL,
  						$content_relationscolumn[child_cnt_id] int(11) NOT NULL,
  						PRIMARY KEY  (crl_id)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
	$dbconn->Execute($sql);
    if ($dbconn->ErrorNo() != 0) {
    	logAdd("Error in query \\r\\n\\r\\n".$sql, LOG_ERROR);
    	pnSessionSetVar('errormsg', _DBSELECTERROR . ": " .$sql);
        return false;
    }
    	
    $contenttable = $pntable['content'];
    $contentcolumn = &$pntable['content_column'];
    $sql = "CREATE TABLE $contenttable (
				$contentcolumn[cnt_id] int(11) not null auto_increment,
				$contentcolumn[ctp_id] int(11) not null,
				$contentcolumn[title] varchar(255),
				$contentcolumn[path] varchar(255),
				$contentcolumn[type_name] varchar(255) not null,
				$contentcolumn[foreign_key_value] int(11) not null,
				$contentcolumn[lang] varchar(3) not null,
				$contentcolumn[page_title] varchar(255),
				$contentcolumn[page_keyword] varchar(1000),
				$contentcolumn[page_description] varchar(1000),
				$contentcolumn[workflow_wsp_id] int(11),
				$contentcolumn[record_state] int(11),
				$contentcolumn[locking_date] date,
				$contentcolumn[locking_time] time,
				$contentcolumn[locker_user_id] int(11),
				$contentcolumn[create_date] date not null,
				$contentcolumn[create_time] time not null,
				$contentcolumn[creator_user_id] int(11) not null,
				$contentcolumn[last_modified_date] date,
				$contentcolumn[last_modified_time] time,
				$contentcolumn[last_modified_user_id] int(11),
				$contentcolumn[counter] int(11),
				$contentcolumn[scheduled_display] tinyint(1),
				$contentcolumn[display_start_date] date,
				$contentcolumn[display_start_time] time,
				$contentcolumn[display_end_date] date,
				$contentcolumn[display_end_time] time,
				$contentcolumn[portal_id] int(11),				
				$contentcolumn[page_id] int(11),				
				$contentcolumn[rate] int(11) DEFAULT '0',				
				PRIMARY KEY (cnt_id),
				KEY ctp_id (ctp_id),		
				KEY type_name (type_name),
				KEY foreign_key_value (foreign_key_value)				
			)ENGINE=MyISAM DEFAULT CHARSET=utf8";
	$dbconn->Execute($sql);
    if ($dbconn->ErrorNo() != 0) {
    	logAdd("Error in query \\r\\n\\r\\n".$sql, LOG_ERROR);
    	pnSessionSetVar('errormsg', _DBSELECTERROR . ": " .$sql);
        return false;
    }
    
    $content_type_domainstable = $pntable['content_type_domains'];
    $content_type_domainscolumn = &$pntable['content_type_domains_column'];
    $sql = "CREATE TABLE $content_type_domainstable (
				$content_type_domainscolumn[ctd_id] int(11) NOT NULL auto_increment,
				$content_type_domainscolumn[type] tinyint(1) NOT NULL,
				$content_type_domainscolumn[caption] varchar(1000) default NULL,
				PRIMARY KEY  (ctd_id),
				KEY `ctd_type_id` (`ctd_id`,`type`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
	$dbconn->Execute($sql);
    if ($dbconn->ErrorNo() != 0) {
    	logAdd("Error in query \\r\\n\\r\\n".$sql, LOG_ERROR);
    	pnSessionSetVar('errormsg', _DBSELECTERROR . ": " .$sql);
        return false;
    }
        
    $content_action_typestable = $pntable['content_action_types'];
    $content_action_typescolumn = &$pntable['content_action_types_column'];
    $sql = "CREATE TABLE $content_action_typestable (
				  $content_action_typescolumn[cat_id] int(11) NOT NULL auto_increment,
				  $content_action_typescolumn[parent_ctp_id] int(11) NOT NULL,
				  $content_action_typescolumn[child_ctp_id] int(11) default NULL,
				  PRIMARY KEY  (cat_id),
				  UNIQUE KEY crt_id (cat_id),
				  UNIQUE KEY child_ctp_id (child_ctp_id),
				  UNIQUE KEY FK_PCTP_ID_CCTP_ID (parent_ctp_id, child_ctp_id)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
	$dbconn->Execute($sql);
    if ($dbconn->ErrorNo() != 0) {
    	logAdd("Error in query \\r\\n\\r\\n".$sql, LOG_ERROR);
    	pnSessionSetVar('errormsg', _DBSELECTERROR . ": " .$sql);
        return false;
    }
    
    $content_action_field_mappingtable = $pntable['content_action_field_mapping'];
    $content_action_field_mappingcolumn = &$pntable['content_action_field_mapping_column'];
    $sql = "CREATE TABLE $content_action_field_mappingtable (
				$content_action_field_mappingcolumn[cfm_id] int(11) NOT NULL auto_increment,
				$content_action_field_mappingcolumn[cat_id] int(11) NOT NULL,
				$content_action_field_mappingcolumn[parent_field] varchar(100) NOT NULL,
				$content_action_field_mappingcolumn[child_field] varchar(100) NOT NULL,
				PRIMARY KEY  (cfm_id),
				UNIQUE KEY cat_id (cat_id, parent_field, child_field)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
	$dbconn->Execute($sql);
    if ($dbconn->ErrorNo() != 0) {
    	logAdd("Error in query \\r\\n\\r\\n".$sql, LOG_ERROR);
    	pnSessionSetVar('errormsg', _DBSELECTERROR . ": " .$sql);
        return false;
    }
    
    // workflows Tables
    $workflowstable = $pntable['workflows'];
    $workflowscolumn = &$pntable['workflows_column'];
    $sql = "CREATE TABLE $workflowstable (
				$workflowscolumn[wkf_id] int not null auto_increment,
				$workflowscolumn[title] varchar(255) not null,
				$workflowscolumn[wf_name] varchar(255) not null,
				$workflowscolumn[last_modified_time] time not null,
				$workflowscolumn[last_modified_date] date not null,
				$workflowscolumn[last_modified_user_id] int not null,
				PRIMARY KEY (wkf_id)
			)ENGINE=MyISAM DEFAULT CHARSET=utf8";
	$dbconn->Execute($sql);
    if ($dbconn->ErrorNo() != 0) {
    	logAdd("Error in query \\r\\n\\r\\n".$sql, LOG_ERROR);
    	pnSessionSetVar('errormsg', _DBSELECTERROR . ": " .$sql);
        return false;
    }
    
    $workflow_stepstable = $pntable['workflow_steps'];
    $workflow_stepscolumn = &$pntable['workflow_steps_column'];
    $sql = "CREATE TABLE $workflow_stepstable (
				$workflow_stepscolumn[wsp_id] int not null auto_increment,
				$workflow_stepscolumn[wkf_id] int not null,
				$workflow_stepscolumn[step_name] varchar(255) not null,
				$workflow_stepscolumn[description] varchar(1000),
				$workflow_stepscolumn[step_order] smallint not null,
				$workflow_stepscolumn[is_system] bool,
				$workflow_stepscolumn[last_modified_time] time not null,
				$workflow_stepscolumn[last_modified_date] date not null,
				$workflow_stepscolumn[last_modified_user_id] int not null,
				$workflow_stepscolumn[edit_template] varchar(255),
				$workflow_stepscolumn[view_template] varchar(255),
				PRIMARY KEY (wsp_id)
			)ENGINE=MyISAM DEFAULT CHARSET=utf8";
	$dbconn->Execute($sql);
    if ($dbconn->ErrorNo() != 0) {
    	logAdd("Error in query \\r\\n\\r\\n".$sql, LOG_ERROR);
    	pnSessionSetVar('errormsg', _DBSELECTERROR . ": " .$sql);
        return false;
    }
        
    $workflow_step_rolestable = $pntable['workflow_step_roles'];
    $workflow_step_rolescolumn = &$pntable['workflow_step_roles_column'];
    $sql = "CREATE TABLE $workflow_step_rolestable (
				$workflow_step_rolescolumn[wsr_id] int not null auto_increment,
				$workflow_step_rolescolumn[wsp_id] int not null,
				$workflow_step_rolescolumn[rol_id] int not null,
				PRIMARY KEY (wsr_id)
			)ENGINE=MyISAM DEFAULT CHARSET=utf8";
	$dbconn->Execute($sql);
    if ($dbconn->ErrorNo() != 0) {
    	logAdd("Error in query \\r\\n\\r\\n".$sql, LOG_ERROR);
    	pnSessionSetVar('errormsg', _DBSELECTERROR . ": " .$sql);
        return false;
    }
        
    $workflow_instancestable = $pntable['workflow_instances'];
    $workflow_instancescolumn = &$pntable['workflow_instances_column'];
    $sql = "CREATE TABLE $workflow_instancestable (
				$workflow_instancescolumn[win_id] int not null auto_increment,
				$workflow_instancescolumn[wsp_id] int not null,
				$workflow_instancescolumn[title] varchar(1000) not null,
				$workflow_instancescolumn[lang] varchar(3) not null,
				$workflow_instancescolumn[table_name] varchar(255) not null,
				$workflow_instancescolumn[pk_field_name] varchar(255) not null,
				$workflow_instancescolumn[pk_value] int not null,
				$workflow_instancescolumn[url] varchar(1000) not null,
				$workflow_instancescolumn[comments] text,
				$workflow_instancescolumn[create_time] time not null,
				$workflow_instancescolumn[create_date] date not null,
				$workflow_instancescolumn[creator_user_id] int not null,				
				$workflow_instancescolumn[last_modified_time] time not null,
				$workflow_instancescolumn[last_modified_date] date not null,
				$workflow_instancescolumn[last_modified_user_id] int not null,
				PRIMARY KEY (win_id),
  				KEY wsp_id (wsp_id),
  				KEY table_name (table_name, pk_value)				
			)ENGINE=MyISAM DEFAULT CHARSET=utf8";
	$dbconn->Execute($sql);
    if ($dbconn->ErrorNo() != 0) {
    	logAdd("Error in query \\r\\n\\r\\n".$sql, LOG_ERROR);
    	pnSessionSetVar('errormsg', _DBSELECTERROR . ": " .$sql);
        return false;
    }
    
	cdk_defaultdata();    	
	
    return 	true;	
}

function cdk_upgrade($oldversion) {
     return true;
}

function cdk_delete() {      
	if (pnModAvailable('fdk')) {		
		return true;
	}	
	
    list($dbconn) = pnDBGetConn();
    $pntable = pnDBGetTables();

    $sql = "DROP TABLE $pntable[content_types]";
    $dbconn->Execute($sql);
    if ($dbconn->ErrorNo() != 0) {
    	logAdd("Error in query \\r\\n\\r\\n".$sql, LOG_ERROR);
        pnSessionSetVar('errormsg', _DBSELECTERROR . ": " .$sql);
        return false;
    }
	
    $sql = "DROP TABLE $pntable[content_type_templates]";
    $dbconn->Execute($sql);
    if ($dbconn->ErrorNo() != 0) {
    	logAdd("Error in query \\r\\n\\r\\n".$sql, LOG_ERROR);
        pnSessionSetVar('errormsg', _DBSELECTERROR . ": " .$sql);
        return false;
    }
	
    $sql = "DROP TABLE $pntable[content_type_directories]";
    $dbconn->Execute($sql);
    if ($dbconn->ErrorNo() != 0) {
    	logAdd("Error in query \\r\\n\\r\\n".$sql, LOG_ERROR);
        pnSessionSetVar('errormsg', _DBSELECTERROR . ": " .$sql);
        return false;
    }
	
    $sql = "DROP TABLE $pntable[content_related_types]";
    $dbconn->Execute($sql);
    if ($dbconn->ErrorNo() != 0) {
    	logAdd("Error in query \\r\\n\\r\\n".$sql, LOG_ERROR);
        pnSessionSetVar('errormsg', _DBSELECTERROR . ": " .$sql);
        return false;
    }
	
    $sql = "DROP TABLE $pntable[content_relations]";
    $dbconn->Execute($sql);
    if ($dbconn->ErrorNo() != 0) {
    	logAdd("Error in query \\r\\n\\r\\n".$sql, LOG_ERROR);
        pnSessionSetVar('errormsg', _DBSELECTERROR . ": " .$sql);
        return false;
    }
	
    $sql = "DROP TABLE $pntable[content]";
    $dbconn->Execute($sql);
    if ($dbconn->ErrorNo() != 0) {
    	logAdd("Error in query \\r\\n\\r\\n".$sql, LOG_ERROR);
        pnSessionSetVar('errormsg', _DBSELECTERROR . ": " .$sql);
        return false;
    }
	
    $sql = "DROP TABLE $pntable[content_type_domains]";
    $dbconn->Execute($sql);
    if ($dbconn->ErrorNo() != 0) {
    	logAdd("Error in query \\r\\n\\r\\n".$sql, LOG_ERROR);
        pnSessionSetVar('errormsg', _DBSELECTERROR . ": " .$sql);
        return false;
    }
    
    //workflow
    
    $sql = "DROP TABLE $pntable[workflows]";
    $dbconn->Execute($sql);
    if ($dbconn->ErrorNo() != 0) {
    	logAdd("Error in query \\r\\n\\r\\n".$sql, LOG_ERROR);
        pnSessionSetVar('errormsg', _DBSELECTERROR . ": " .$sql);
        return false;
    }
	
    $sql = "DROP TABLE $pntable[workflow_steps]";
    $dbconn->Execute($sql);
    if ($dbconn->ErrorNo() != 0) {
    	logAdd("Error in query \\r\\n\\r\\n".$sql, LOG_ERROR);
        pnSessionSetVar('errormsg', _DBSELECTERROR . ": " .$sql);
        return false;
    }
	
    $sql = "DROP TABLE $pntable[workflow_step_roles]";
    $dbconn->Execute($sql);
    if ($dbconn->ErrorNo() != 0) {
    	logAdd("Error in query \\r\\n\\r\\n".$sql, LOG_ERROR);
        pnSessionSetVar('errormsg', _DBSELECTERROR . ": " .$sql);
        return false;
    }
	
    $sql = "DROP TABLE $pntable[workflow_instances]";
    $dbconn->Execute($sql);
    if ($dbconn->ErrorNo() != 0) {
    	logAdd("Error in query \\r\\n\\r\\n".$sql, LOG_ERROR);
        pnSessionSetVar('errormsg', _DBSELECTERROR . ": " .$sql);
        return false;
    }
    
    pnModDelVar('cdk');
    pnModDelVar('workflow');
    return true;		
}

function cdk_defaultdata() {
	if (pnModAvailable('data_locking'))
		pnModAPIFunc('data_locking', 'user', 'registerModule', array('module'=>'cdk'));
	
	return true;
}

function cdk_reset($args)
{
	extract($args);
	if (pnModAvailable('fdk')) {		
		cdk_defaultdata();    	
		return true;
	}	
	
    list($dbconn) = pnDBGetConn();
    $pntable = pnDBGetTables();

    $content_typestable = $pntable['content_types'];
    $content_typescolumn = &$pntable['content_types_column'];
    $sql="SELECT $content_typescolumn[type_name] FROM $content_typestable";
    $result=$dbconn->Execute($sql);
    while (!$result->EOF) 
    {
    	$type_name=$result->fields[0];
    	$sql="DROP VIEW viw_cotent_".$type_name."_items";	
    	$dbconn->Execute($sql);
    	$sql="DROP TABLE saman_cotent_".$type_name."_items";	
    	$dbconn->Execute($sql);
		$result->MoveNext();   	
    }
    $sql = "TRUNCATE $content_typestable";
	$dbconn->Execute($sql);
    
	$content_type_templatestable = $pntable['content_type_templates'];
    $sql = "TRUNCATE $content_type_templatestable";
	$dbconn->Execute($sql);
    	
    $content_type_directoriestable = $pntable['content_type_directories'];
    $sql = "TRUNCATE $content_type_directoriestable";
    $dbconn->Execute($sql);
		
    $content_related_typestable = $pntable['content_related_types'];
    $sql = "TRUNCATE $content_related_typestable";
	$dbconn->Execute($sql);
    	
    $content_relationstable = $pntable['content_relations'];
    $sql = "TRUNCATE $content_relationstable";
	$dbconn->Execute($sql);
	
    $contenttable = $pntable['content'];
    $sql = "TRUNCATE $contenttable";
	$dbconn->Execute($sql);
	
    $content_type_domainstable = $pntable['content_type_domains'];
    $sql = "TRUNCATE $content_type_domainstable";
	$dbconn->Execute($sql);
	
	$content_action_types = $pntable['content_action_types'];
    $sql = "TRUNCATE $content_action_types";
	$dbconn->Execute($sql);
	
	$content_action_field_mapping = $pntable['content_action_field_mapping'];
    $sql = "TRUNCATE $content_action_field_mapping";
	$dbconn->Execute($sql);
    
	$workflowstable = $pntable['workflows'];
    $sql = "TRUNCATE $workflowstable";
	$dbconn->Execute($sql);
    
    $workflow_stepstable = $pntable['workflow_steps'];
    $sql = "TRUNCATE $workflow_stepstable";
	$dbconn->Execute($sql);
        
    $workflow_step_rolestable = $pntable['workflow_step_roles'];
    $sql = "TRUNCATE $workflow_step_rolestable";
	$dbconn->Execute($sql);
        
    $workflow_instancestable = $pntable['workflow_instances'];
    $sql = "TRUNCATE $workflow_instancestable";
	$dbconn->Execute($sql);
    
	mkdir('portlets/sisRapid/dream/dbs/dynamic_content/logical/tables/'.$where_is."/",0777);
	copy('portlets/sisRapid/dream/dbs/dynamic_content/logical/tables/'.$old_where_is."/content.inc.php",'portlets/sisRapid/dream/dbs/dynamic_content/logical/tables/'.$where_is."/");
	
	extract($args);
	mkdir("/parameters/$where_is/modules/cdk/",0777);
	mkdir("/parameters/$where_is/modules/cdk/upload/",0777);
	mkdir("/parameters/$where_is/modules/cdk/upload/content/",0777);
	cdk_defaultdata();    	
	
    return 	true;	
}
?>