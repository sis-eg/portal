<?php
// Saman Portal
// Copyright (C) 2008 by the Saman Information Structure Development Team.
// http://www.sis-eg.com/
// ----------------------------------------------------------------------

function cdk_pntables() {
    $pntable = array();
    $prefix=pnConfigGetVar('prefix');

	$pntable['content_types'] = $prefix.'_content_types';
	$pntable['content_types_column'] = array ('ctp_id'       				=> "$pntable[content_types].ctp_id",
		                                      'type_name'    				=> "$pntable[content_types].type_name",
		                                      'is_system'  	 				=> "$pntable[content_types].is_system",
		                                      'title'      	 				=> "$pntable[content_types].title",
		                                      'description'  				=> "$pntable[content_types].description",
		                                      'title_field'     			=> "$pntable[content_types].title_field",
		                                      'enable_locking'    			=> "$pntable[content_types].enable_locking",
		                                      'enable_scheduling'    		=> "$pntable[content_types].enable_scheduling",
		                                      'image'     					=> "$pntable[content_types].image",
		                                      'type_fields'   				=> "$pntable[content_types].type_fields",
		                                      'settings'     				=> "$pntable[content_types].settings",
		                                      'admin_list_template_id'     	=> "$pntable[content_types].admin_list_template_id",
		                                      'admin_search_template_id'	=> "$pntable[content_types].admin_search_template_id",
		                                      'admin_view_template_id'  	=> "$pntable[content_types].admin_view_template_id",
		                                      'admin_edit_template_id'  	=> "$pntable[content_types].admin_edit_template_id",
		                                      'user_list_template_id'  		=> "$pntable[content_types].user_list_template_id",
		                                      'user_search_template_id' 	=> "$pntable[content_types].user_search_template_id",
		                                      'user_view_template_id'  		=> "$pntable[content_types].user_view_template_id",
		                                      'user_edit_template_id'  		=> "$pntable[content_types].user_edit_template_id",
		                                      'wkf_id'  					=> "$pntable[content_types].wkf_id",
		                                      'last_modified_date'  		=> "$pntable[content_types].last_modified_date",
		                                      'last_modified_time'  		=> "$pntable[content_types].last_modified_time",
		                                      'last_modified_user_id'  		=> "$pntable[content_types].last_modified_user_id",
											  'control_one_record'			=> "$pntable[content_types].control_one_record",
											  'type'						=> "$pntable[content_types].type",	
											  'state'						=> "$pntable[content_types].state",	
											  'category'					=> "$pntable[content_types].category",
											  'workflow_type'				=> "$pntable[content_types].workflow_type",	
											  'display_order'				=> "$pntable[content_types].display_order",
											  'refer_type'					=> "$pntable[content_types].refer_type"	,
											  'main_ctp_id'					=> "$pntable[content_types].main_ctp_id",	
											  'ui_type'						=> "$pntable[content_types].ui_type",	
											  'lang_consts'					=> "$pntable[content_types].lang_consts",
											  'seo_title'					=> "$pntable[content_types].seo_title",
											  'version'						=> "$pntable[content_types].version",
											  'perm_type'					=> "$pntable[content_types].perm_type"											  
		                                      );
		                                      
	$pntable['content_type_templates'] = $prefix.'_content_type_templates';
	$pntable['content_type_templates_column'] = array ('ctt_id'       		=> "$pntable[content_type_templates].ctt_id",
		                                      'ctp_id'    					=> "$pntable[content_type_templates].ctp_id",
		                                      'template_name'      	 		=> "$pntable[content_type_templates].template_name",
		                                      'title'     					=> "$pntable[content_type_templates].title",
		                                      'template_type'    			=> "$pntable[content_type_templates].template_type",
		                                      'is_system'    				=> "$pntable[content_type_templates].is_system",
		                                      'lang'     					=> "$pntable[content_type_templates].lang",
		                                      'image'     					=> "$pntable[content_type_templates].image",
		                                      'template'     				=> "$pntable[content_type_templates].template",
		                                      'compiled_template'     		=> "$pntable[content_type_templates].compiled_template",
		                                      'header_template'     		=> "$pntable[content_type_templates].header_template",
		                                      'compiled_header_template' 	=> "$pntable[content_type_templates].compiled_header_template",
		                                      'footer_template'     		=> "$pntable[content_type_templates].footer_template",
		                                      'compiled_footer_template' 	=> "$pntable[content_type_templates].compiled_footer_template",
		                                      'layout_type' 				=> "$pntable[content_type_templates].layout_type",
		                                      'last_modified_date'    		=> "$pntable[content_type_templates].last_modified_date",
		                                      'last_modified_time'     		=> "$pntable[content_type_templates].last_modified_time",
		                                      'last_modified_user_id'		=> "$pntable[content_type_templates].last_modified_user_id",                               
		                                      'empty_template'				=> "$pntable[content_type_templates].empty_template",                               
		                                      'compiled_empty_template'		=> "$pntable[content_type_templates].compiled_empty_template",                               
		                                      'type'						=> "$pntable[content_type_templates].type",                               
		                                      'designer_template'			=> "$pntable[content_type_templates].designer_template",                               
		                                      'designer_header_template'	=> "$pntable[content_type_templates].designer_header_template",                               
		                                      'designer_footer_template'	=> "$pntable[content_type_templates].designer_footer_template",                               
		                                      'designer_empty_template'		=> "$pntable[content_type_templates].designer_empty_template"
		                                      );
		                                      
	$pntable['content_type_directories'] = $prefix.'_content_type_directories';
	$pntable['content_type_directories_column'] = array ('ctc_id'       	=> "$pntable[content_type_directories].ctc_id",
		                                      'ctp_id'    					=> "$pntable[content_type_directories].ctp_id",
		                                      'wdc_id'      	 			=> "$pntable[content_type_directories].wdc_id"
		                                      );

    $pntable['content_related_types'] = $prefix.'_content_related_types';
	$pntable['content_related_types_column'] = array ('crt_id'       		=> "$pntable[content_related_types].crt_id",
		                                      'parent_ctp_id'    			=> "$pntable[content_related_types].parent_ctp_id",
		                                      'child_ctp_id'   	 			=> "$pntable[content_related_types].child_ctp_id"
		                                      );
		                                      
    $pntable['content_relations'] = $prefix.'_content_relations';
	$pntable['content_relations_column'] = array ('crl_id'       			=> "$pntable[content_relations].crl_id",
		                                      'parent_ctp_id'    			=> "$pntable[content_relations].parent_ctp_id",
		                                      'child_ctp_id'    			=> "$pntable[content_relations].child_ctp_id",
		                                      'parent_id'    				=> "$pntable[content_relations].parent_id",
		                                      'parent_cnt_id'    			=> "$pntable[content_relations].parent_cnt_id",
		                                      'child_id'    				=> "$pntable[content_relations].child_id",
		                                      'child_cnt_id'    			=> "$pntable[content_relations].child_cnt_id"
		                                      );

	$pntable['content'] = $prefix.'_content';
	$pntable['content_column'] = array ('cnt_id'       						=> "$pntable[content].cnt_id",
		                                      'ctp_id'    					=> "$pntable[content].ctp_id",
		                                      'title'      	 				=> "$pntable[content].title",
		                                      'path'  						=> "$pntable[content].path",
		                                      'type_name'    				=> "$pntable[content].type_name",		                                      
		                                      'foreign_key_value'     		=> "$pntable[content].foreign_key_value",
		                                      'lang'    					=> "$pntable[content].lang",
		                                      'page_title'     				=> "$pntable[content].page_title",
		                                      'page_keyword'   				=> "$pntable[content].page_keyword",
		                                      'page_description'     		=> "$pntable[content].page_description",
		                                      'hashtag'   					=> "$pntable[content].hashtag",
		                                      'workflow_wsp_id'				=> "$pntable[content].workflow_wsp_id",
		                                      'record_state'				=> "$pntable[content].record_state",
		                                      'locking_date'  				=> "$pntable[content].locking_date",
		                                      'locking_time'  				=> "$pntable[content].locking_time",
		                                      'locker_user_id'  			=> "$pntable[content].locker_user_id",
		                                      'create_date' 				=> "$pntable[content].create_date",
		                                      'create_time'  				=> "$pntable[content].create_time",
		                                      'creator_user_id'  			=> "$pntable[content].creator_user_id",
		                                      'last_modified_date'  		=> "$pntable[content].last_modified_date",
		                                      'last_modified_time'  		=> "$pntable[content].last_modified_time",
		                                      'last_modified_user_id'  		=> "$pntable[content].last_modified_user_id",		                                      
		                                      'counter'  					=> "$pntable[content].counter",
		                                      'scheduled_display'			=> "$pntable[content].scheduled_display",
		                                      'display_start_date'			=> "$pntable[content].display_start_date",
		                                      'display_start_time'			=> "$pntable[content].display_start_time",
		                                      'display_end_date'			=> "$pntable[content].display_end_date",
		                                      'display_end_time'			=> "$pntable[content].display_end_time",
		                                      'portal_id'					=> "$pntable[content].portal_id",
		                                      'page_id'						=> "$pntable[content].page_id",
		                                      'rate'						=> "$pntable[content].rate",
		                                      'seo_title'					=> "$pntable[content].seo_title"
		                                      );

	$pntable['content_type_domains'] = $prefix.'_content_type_domains';
	$pntable['content_type_domains_column'] = array ('ctd_id'       		=> "$pntable[content_type_domains].ctd_id",
		                                      'type'    					=> "$pntable[content_type_domains].type",
		                                      'caption'      	 			=> "$pntable[content_type_domains].caption"
		                                      );		                                      
		                                      
	$pntable['content_type_reports'] = $prefix.'_content_type_reports';
	$pntable['content_type_reports_column'] = array ('crp_id'       		=> "$pntable[content_type_reports].crp_id",
		                                      'ctp_id'    					=> "$pntable[content_type_reports].ctp_id",
		                                      'report_name'    	 			=> "$pntable[content_type_reports].report_name",
		                                      'title'    	 				=> "$pntable[content_type_reports].title",
		                                      'type'    	 				=> "$pntable[content_type_reports].type",
		                                      'content'  	 				=> "$pntable[content_type_reports].content",
		                                      'portal_id'  	 				=> "$pntable[content_type_reports].portal_id"
		                                      );		                                      
		                                      
    $pntable['content_action_types'] = $prefix.'_content_action_types';
	$pntable['content_action_types_column'] = array ('cat_id'       		=> "$pntable[content_related_types].cat_id",
		                                      'parent_ctp_id'    			=> "$pntable[content_related_types].parent_ctp_id",
		                                      'child_ctp_id'   	 			=> "$pntable[content_related_types].child_ctp_id"
		                                      );
		                                      		                                      
    $pntable['content_action_field_mapping'] = $prefix.'_content_action_field_mapping';
	$pntable['content_action_field_mapping_column'] = array ('cfm_id'       => "$pntable[content_related_types].cfm_id",
		                                      'cat_id'    					=> "$pntable[content_related_types].cat_id",
		                                      'parent_field'    			=> "$pntable[content_related_types].parent_field",
		                                      'child_field'   	 			=> "$pntable[content_related_types].child_field"
		                                      );
		                                      		                                      
	//workflow tables
	$pntable['workflows'] = 'workflow_workflows';
	$pntable['workflows_column'] = array ('wkf_id'       				=> "$pntable[workflows].wkf_id",
		                                      'title'    				=> "$pntable[workflows].title",
		                                      'wf_name'      	 		=> "$pntable[workflows].wf_name",
		                                      'last_modified_time'  	=> "$pntable[workflows].last_modified_time",
		                                      'last_modified_date'     	=> "$pntable[workflows].last_modified_date",
		                                      'last_modified_user_id'	=> "$pntable[workflows].last_modified_user_id"                                
		                                      );
		                                      
	$pntable['workflow_steps'] = 'workflow_workflow_steps';
	$pntable['workflow_steps_column'] = array ('wsp_id'       			=> "$pntable[workflow_steps].wsp_id",
		                                      'wkf_id'    				=> "$pntable[workflow_steps].wkf_id",
		                                      'step_name'      	 		=> "$pntable[workflow_steps].step_name",
		                                      'description'     		=> "$pntable[workflow_steps].description",
		                                      'step_order'				=> "$pntable[workflow_steps].step_order",                                
		                                      'is_system'				=> "$pntable[workflow_steps].is_system",                                
		                                      'last_modified_time'  	=> "$pntable[workflow_steps].last_modified_time",
		                                      'last_modified_date'     	=> "$pntable[workflow_steps].last_modified_date",
		                                      'last_modified_user_id'	=> "$pntable[workflow_steps].last_modified_user_id",
		                                      'edit_template'     		=> "$pntable[workflow_steps].edit_template",
		                                      'view_template'			=> "$pntable[workflow_steps].view_template"	
		                                      );
		                                      
	$pntable['workflow_step_roles'] = 'workflow_workflow_step_roles';
	$pntable['workflow_step_roles_column'] = array ('wsr_id'       		=> "$pntable[workflow_step_roles].wsr_id",
		                                      'wsp_id'    				=> "$pntable[workflow_step_roles].wsp_id",
		                                      'rol_id'      	 		=> "$pntable[workflow_step_roles].rol_id"
		                                      );
		                                      
	$pntable['workflow_instances'] = 'workflow_workflow_instances';
	$pntable['workflow_instances_column'] = array ('win_id'       		=> "$pntable[workflow_instances].win_id",
		                                      'wsp_id'    				=> "$pntable[workflow_instances].wsp_id",
		                                      'title'      	 			=> "$pntable[workflow_instances].title",
		                                      'lang'      	 			=> "$pntable[workflow_instances].lang",
		                                      'table_name'      	 	=> "$pntable[workflow_instances].table_name",
		                                      'pk_field_name'  	 		=> "$pntable[workflow_instances].pk_field_name",
		                                      'pk_value'  				=> "$pntable[workflow_instances].pk_value",
		                                      'url'     				=> "$pntable[workflow_instances].url",
		                                      'comments'				=> "$pntable[workflow_instances].comments",                                
		                                      'create_time'  			=> "$pntable[workflow_instances].create_time",
		                                      'create_date'     		=> "$pntable[workflow_instances].create_date",
		                                      'creator_user_id'			=> "$pntable[workflow_instances].creator_user_id",
		                                      'last_modified_time'  	=> "$pntable[workflow_instances].last_modified_time",
		                                      'last_modified_date'     	=> "$pntable[workflow_instances].last_modified_date",
		                                      'last_modified_user_id'	=> "$pntable[workflow_instances].last_modified_user_id"		                                      
		                                      );
	return $pntable;
}

?>