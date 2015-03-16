<?php
//######################### Admin Functions #######################
//newsletter functions
pnModLoad('cdk','user');
pnModLoad('cdk','admin');

function cdk_admin_newsletter_config_show($args) {
	extract($args);
	$settings=unserialize($settings);

	$types=pnModAPIFunc('cdk','user','getSubportalTypes');
	
	$templates=pnModAPIFunc('cdk','user','getTypeTemplates');
	
	$output="<table border=0 >";
	$output.="<tr><td colspan=3><span class='itemdescription'>("._CDK_STYLE_COMMENT.")</spna></td></tr>";
	foreach ($types as $key=>$value)
	{
		if ($value['main_ctp_id']!="") {
			continue;
		}
		$checked="";
		if ($settings['cdk_types'][$value['ctp_id']])$checked="checked";
		
		$output.="<tr>";
		$output.="<td class='caption'><input type='checkbox' name='cdk_types[".$value['ctp_id']."]' value='".$value['ctp_id']."' $checked>".$value['title'];
		$template_id=$settings['cdk_templates'][$value['ctp_id']];
		
		$type_templates=$templates[$value['ctp_id']];
		$output.="</td><td class='caption'>"._CDK_TEMPLATE.":&nbsp;<select name='cdk_templates[".$value['ctp_id']."]'>";
		foreach ($type_templates as $keyT=>$valueT)
		{
			$selected="";
			if ($template_id==$valueT) {
				$selected="selected";
			}
			$output.="<option value='$valueT' $selected>$valueT</option>";
		}
		$output.="</select></td>";
		$output.="<td class='caption'>"._CDK_NUM_ITEMS.":&nbsp;<input type='input' name='cdk_nums[".$value['ctp_id']."]' size='4' value='".$settings['cdk_nums'][$value['ctp_id']]."'/></td>";
		$output.="<td class='caption'>"._CDK_NUM_COLS.":&nbsp;<input type='input' name='cdk_num_cols[".$value['ctp_id']."]' size='4' value='".$settings['cdk_num_cols'][$value['ctp_id']]."'/></td>";
		$output.="</tr>";
		
	}
	
	
	$output.="</table>";
	$arr=array('content'=>$output,'title'=>_CDK,'name'=>'cdk');
	
	return $arr;
	
}
function cdk_admin_newsletter_save_config()
{
	$cdk_types=pnVarCleanFromInput('cdk_types');
	$cdk_templates=pnVarCleanFromInput('cdk_templates');
	$cdk_nums=pnVarCleanFromInput('cdk_nums');
	$cdk_num_cols=pnVarCleanFromInput('cdk_num_cols');
	$output=serialize(array('cdk_types'=>$cdk_types,'cdk_templates'=>$cdk_templates,'cdk_nums'=>$cdk_nums,'cdk_num_cols'=>$cdk_num_cols));

	return $output;
	
}
function cdk_admin_newsletter_content($args)
{
	extract($args);

	static $cdk_results;
	static $duration;
	$max_news=30;
	$admin_setting=unserialize($admin_setting);
	
	$num_items=$user_setting['num_items'];
	$admin_types=$admin_setting['cdk_types'];
	$admin_templates=$admin_setting['cdk_templates'];
	$admin_nums=$admin_setting['cdk_nums'];
	$admin_num_cols=$admin_setting['cdk_num_cols'];
	$originalGetParams = $_GET;
	if (count($admin_types)==0) 
	{
		pnSessionSetVar('errormsg',_CDK_SELECT_SECTION);
		pnRedirect('index.php?module=newsletter&type=admin&func=new_newsletters');
	}

	foreach ($admin_types as $key=>$value)
	{
		
	    $_GET['ctp_id'] = $value;
	    $_GET['template_id'] = $admin_templates[$value];
	    $_GET['item_count'] = $admin_nums[$value];
	    $_GET['use_pager'] = 0;
	    $_GET['col_count'] = $admin_num_cols[$value];
	    $_GET['field'] ='display_start_date';    
	    $_GET['order'] = 'deascending'; 
	    $_GET['empty_result'] = true;
	    $GLOBALS['newsletter_empty']=0;
		$type=pnModAPIFunc('cdk','user','getType',array('ctp_id'=>$value));
		
		$content=cdk_format_output(pnModFunc('cdk', 'user', 'loadmodule', array('system'=>'cdk',
																		  	 'sismodule' => 'block/newsletter_content.php')),$type);     
																		  	 
		if ($GLOBALS['newsletter_empty']==0) 
		{
			
			$content=preg_replace("/<script [^>]*>(.*)<[\/]script>/s","", $content);
			$content=preg_replace("/<input [^>]*>/s","", $content);
			$cdk_results.= $content;
	    	$temp[$value]=1;														 							  	 
		}
	}
	
	$_GET = $originalGetParams;
	
	
	return $cdk_results;
}


//############################## User Functions#############################
//$args={'settings','user_setting'}

function cdk_user_newsletter_show($args)
{
	extract($args);
	$setting=unserialize($setting);
	$user_setting=unserialize($user_setting);
	$admin_types=$setting['cdk_types'];
	$types=pnModAPIFunc('cdk','user','getTypes');
	$output="<table border=0 width='100%'>";
	$num_items=$user_setting['num_items'];
	if (!$num_items)$num_items=10;		
	$output.="<tr><td colspan=4 class='caption'>"._CDK_NUM_ITEM.":<input type='text' name='newsletter_cdk_numitems' size='5' value='$num_items'></td></tr>";
	foreach ($types as $key=>$value)
	{
		
		if ($admin_types[$value['ctp_id']])
		{
			if ($i==0)
				$output.="<tr>";
			$checked="";
			if ($user_setting['cdk_types'][$value['ctp_id']])$checked="checked";
			$output.="<td class='caption'><input type='checkbox' name='cdk_types[".$value['ctp_id']."]' value='".$value['ctp_id']."' $checked>".$value['title']."</td>";
			$i++;
			if ($i==4)
			{
				$output.="</tr>";
				$i=0;
			}
		}
	}
	if ($i!=0)
		$output.="</tr>";
	
	$output.="</table>";
	$arr=array('content'=>$output,'title'=>_CDK,'has_subsection'=>1,'module_name'=>'cdk');
	
	return $arr;
}
function cdk_user_newsletter_save()
{
	
	list($cdk_types,$num_items)=pnVarCleanFromInput('cdk_types','newsletter_cdk_numitems');
	$arr=array('cdk_types'=>$cdk_types,'num_items'=>$num_items);
	$output=serialize($arr);
	
	return $output;
}
function cdk_user_newsletter_content($args)
{

	extract($args);
	static $cdk_results;
	static $duration2;
	$max_news=30;
	$admin_setting=unserialize($admin_setting);
	$user_setting=unserialize($user_setting);
		
	$admin_types=$admin_setting['cdk_types'];
	$admin_templates=$admin_setting['cdk_templates'];
	$admin_nums=$admin_setting['cdk_nums'];
	$admin_num_cols=$admin_setting['cdk_num_cols'];
	
	$num_items=$user_setting['num_items'];
	$user_types=$user_setting['cdk_types'];

	$duration3=$new_duration;
	
	if ($duration3==1)
	{
		$start_date=pnMinusFromGDate(date("Y-m-d"),0,100);
	}
	elseif ($duration3==2)
	{
		$start_date=pnMinusFromGDate(date("Y-m-d"),7);
	}
	elseif ($duration3==3) 
	{
		$start_date=pnMinusFromGDate(date("Y-m-d"),1);
	}
	elseif ($duration3==4)
	{
		$start_date=pnMinusFromGDate(date("Y-m-d"),$duration_days);
	}
	$start_time="12:00:00";
	
	
	$originalGetParams = $_GET;
	
	foreach ($admin_types as $key=>$value)
	{
		if (in_array($value,$user_types)) 
		{
					

		    $_GET['ctp_id'] = $value;
		    $_GET['template_id'] = $admin_templates[$value];
		    $_GET['item_count'] = $admin_nums[$value];
		    $_GET['use_pager'] = 0;
		    $_GET['col_count'] = $admin_num_cols[$value];
		    $_GET['field'] ='display_start_date';    
		    $_GET['order'] = 'ascending'; 
		    $_GET['empty_result'] = true;
		    $_GET['where_clause'] = " ( create_date > '$start_date' OR ( create_date='$start_date' AND create_time>'$start_time') )";
		    $GLOBALS['newsletter_empty']=0;
			$type=pnModAPIFunc('cdk','user','getType',array('ctp_id'=>$value));
			
			$content=cdk_format_output(pnModFunc('cdk', 'user', 'loadmodule', array('system'=>'cdk',
																			  	 'sismodule' => 'block/newsletter_content.php')),$type);     
																					  	 
			
			
			if ($GLOBALS['newsletter_empty']==0) 
			{
				
				$content=eregi_replace("<[^>]*script.*\"?[^>]*>","", $content);
				$cdk_results.= $content;
		    	$temp[$value]=1;														 							  	 
			}
		}
	}

	$_GET = $originalGetParams;
	
	
	return $cdk_results;
}
function cdk_format_output($content,$type)
{
	
	if ($content=="") 
	{
		return ;	
	}
	extract($args);
	$dir="ltr";
	if (pnUserGetLang()=="far") 
	{
		$dir="rtl";	
	}
	$title=unserialize($title);
	$title=$title[pnUserGetLang()];
	$output="<br/>".$content;
	
	return $output;
}


?>