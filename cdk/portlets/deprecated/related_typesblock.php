<?php
// Saman Portal
// Copyright (C) 2009 by the Saman Information Structure Development Team.
// http://www.sis-eg.com/
// ----------------------------------------------------------------------

function cdk_related_typesblockblock_init(){
	pnSecAddSchema('cdk::menu_typesblock:', 'Block title::');
}

function cdk_related_typesblockblock_info(){
    return array('text_type' => 'related_typesblockblock',
                 'module' => 'cdk',
                 'text_type_long' => _CDK_RELATEDTYPES_BLOCK,
                 'allow_multiple' => true,
                 'form_content' => false,
                 'form_refresh' => false,
                 'show_preview' => false,
				 'default_title' => _CDK_RELATEDTYPES_BLOCK_TITLE,
				 'allow_user_add'  => 0,
				 'group' => 2,
				'block_description' => _CDK_RELATEDTYPES_BLOCK_DESCRIPTION
                 );
}

function cdk_related_typesblockblock_display($blockinfo){
	 
    if (!pnSecAuthAction(0, 'cdk::related_typesblockblock', "$blockinfo[title]::", ACCESS_READ)) 
    	return;
    
  
    $vars = pnBlockVarsFromContent($blockinfo['content']);
   
    $num_items=$vars['num_items'];
    $types=$vars['types'];
    $types_selected=unserialize($types);
    $type_ids="(-1";
    $types = pnModAPIFunc('cdk', 'user', 'getAccessibleTypes');
    
    foreach ($types as $key=>$value)
    {
    	foreach ($types_selected as $key2=>$value2)
    	if ($value2==$value['ctp_id']) 
    	{
    		$types[$key]['selected']=1;
    	}
    	else if ($types[$key]['selected']!=1) 
    	{
    		$types[$key]['selected']=0;
    	}
    	$types[$key]['type_fields']="";
    	
    }
   
    
    if (!$num_items)$num_items=5;
    $length=$vars['title_length'];
    if (!$length)$length=23;
    $ctp_id=pnVarCleanFromInput('ctp_id');
    if (isset($vars['ctp_id'])) 
    {
    	$ctp_id=$vars['ctp_id'];
    }
    if (!$ctp_id)return ;
    pnModLoad('web_directory');
    $pntables=&pnDBGetTables();
    $dbconn=pnDBGetConn(true);
    $wdi_table=$pntables['web_directory_items'];
    $wdi_column=$pntables['web_directory_items_column'];
    $wd_id=$vars['wd_id'];
    if (!$wd_id) 
    {
	    $sql="SELECT $wdi_column[wd_id] FROM $wdi_table WHERE $wdi_column[url] LIKE '%ctp_id=$ctp_id&%'";
	    $result=$dbconn->Execute($sql);
	
	    $str="-1";
	   	
	    while (!$result->EOF)
	    {
	    	$str.=",".$result->fields[0];
	    	$result->MoveNext();
	    }
    }
    else if (isset($wd_id)) 
    {
    	if (substr($wd_id,0,1)=="#") 
    	{
    		$str=substr($wd_id,1,strlen($wd_id)-2);	
    	}
    	else 
    	{
    		$str="-1";
    		find_wd_ids($wd_id,&$str);
    		
    		$sql="SELECT DISTINCT($wdi_column[wd_id]) FROM $wdi_table WHERE $wdi_column[url] LIKE '%ctp_id=$ctp_id&%' AND $wdi_column[wd_id] IN($str)";
    		
		    $result=$dbconn->Execute($sql);
		
		    $str="-1";
		   	
		    while (!$result->EOF)
		    {
		    	$str.=",".$result->fields[0];
		    	$result->MoveNext();
	  		}
    	}
    }
   
  	$other_lang='far';
  	if (pnUserGetLang()=="far")$other_lang='eng';
    $sql2="SELECT DISTINCT($wdi_column[url]),$wdi_column[title],$wdi_column[content_type] 
    FROM $wdi_table 
    WHERE $wdi_column[wd_id] IN($str)   AND $wdi_column[lang]!='$other_lang' 
    ORDER BY $wdi_column[title] DESC";

    $result2=$dbconn->Execute($sql2);
	if ($result2->_numOfRows==0)return ;   
    $items=array();
   
    while (!$result2->EOF)
    {
    	
    	$url=$result2->fields[0];
    	$title=$result2->fields[1];
    	$content=$result2->fields[2];
    	
    	if (!isset($types[$content])||(isset($types[$content])&&$types[$content]['selected'])==1) 
    	{
	    	if (count($items[$content]['items'])<$num_items)
	    	{
	    		$title=unserialize($title);
	    		if ($content!="")
	    		{
					$items[$content]['items'][]=array('title'=>$title[pnUserGetLang()],'url'=>$url);
					$items[$content]['title']=getTitleFromType2($content);
	    		}
	    	}
    	}
		$result2->MoveNext();    	
    	
    }

   	if(count($items)==0)return "";
   	
    $output="";
    $imgpath = $GLOBALS['imgPath'];
    $output.="<table width='100%'>";
    $i=1;
    foreach ($items as $key=>$value)
    {
    	$output.="<tr>";
    	$output.="<td width='50%'>
    				<table width='100%'>
    					<tr>
    						<td style='font-weight:600;'><img src='".$imgpath."btable_icon.gif' style='vertical-align:middle;margin-".$notalign.":3px'/>&nbsp;".$value['title']."</td>
    					</tr>";
    	
    	
    	
    	foreach ($value['items'] as $key2=>$value2)
    	{
    		$title=$value2['title'];
    		if (strlen($value2['title'])>$length)
    			$title=pnsafe_substr($value2['title'],0,$length)."...";
    		$output.="	<tr>
    						<td>
    							&nbsp;&nbsp;&nbsp;&nbsp;<a href='".$value2['url']."' alt='".$value2['title']."' title='".$value2['title']."'>".$title."</a>
    						</td>
    					</tr>";
    	}
    	$output.="</table>
    			
    		</td>";
    	
    	$output.="</tr>";
    	    	
    }
   
    $output.="</table>";
    $blockinfo['content']=$output;
    return themesideblock($blockinfo);	
}

function cdk_related_typesblockblock_modify($blockinfo){
    $vars = pnBlockVarsFromContent($blockinfo['content']);
    $types = pnModAPIFunc('cdk', 'user', 'getAccessibleTypes');
    $types_selected=$vars['types'];
    $types_selected=unserialize($types_selected);
    
    $i=0;
    $types_select="<table><tr>";
    foreach ($types AS $key=>$value)
    {
    	$checked="";
    	if (in_array($value['ctp_id'],$types_selected)) 
    	{
    		$checked="checked";
    	}
    	$i++;
    	if ($i%4==0) 
    	{
    		$types_select.="</tr><tr>"	;
    	}
    	$types_select.="<td><input type='checkbox' $checked value='$value[ctp_id]' name='types[]'>$value[title]</td>";
    	$selected="";
    	if ($vars['ctp_id']==$value['ctp_id']) 
    	{
    		$selected="selected";	
    	}
    	$type_selected.="<option value='$value[ctp_id]' $selected>$value[title]</option>";
    }
    $types_select.="</tr></table>";
    $str="
    <tr>
    	<td class='caption' valign='top'>"._CTPID.":</td>
    	<td><select name='ctp_id' ><option></option>$type_selected</select><br/><span class='itemdescription'>("._CTPID_COMMENT.")</span><br/><br/></td>
    </tr>
    <tr>
    	<td class='caption' valign='top'>"._SELECT_TYPES.":</td>
    	<td>$types_select<span class='itemdescription'>("._SELECT_TYPES_COMMENT.")</span><br/><br/></td>
    </tr>
     
    <tr>
    	<td class='caption' valign='top'>"._WDID.":</td>
    	<td><input type='text' name='wd_id' id='wd_id' value='$vars[wd_id]' size=10/><input type='button' name='select_wd' id='select_wd' value='...' onclick='window.open(\"index.php?name=web_directory&func=select_web_directory&standalone=1\",\"select_wd\",\"toolbar=no,directories=no,menubar=no,resizable=yes,scrollbars=yes,status=no,height=450,width=350\")'><br/><span class='itemdescription'>("._WDID_COMMENT.")</span><br/><br/>
    	<input type='hidden' name='wd_title' id='wd_title'/>
    	</td>
    </tr>
    <tr>
    	<td class='caption' valign='top'>"._NUM_ITEMS.":</td>
    	<td><input type='text' name='related_num_items' value='$vars[num_items]' size=10/><span class='itemdescription'>("._NUM_ITEMS_COMMENT.")</span></td>
    </tr>
    <tr>
    	<td class='caption' valign='top'>"._TITLE_LENGTH.":</td>
    	<td><input type='text' name='title_length' value='$vars[title_length]' size=10/><span class='itemdescription'>("._TITLE_LENGTH_COMMENT.")</span></td>
    </tr>
    
    ";
    return $str;
}

function cdk_related_typesblockblock_update($blockinfo){
	
    $vars['num_items'] = pnVarCleanFromInput('related_num_items');    
    $vars['title_length'] = pnVarCleanFromInput('title_length');    
    $vars['types'] = pnVarCleanFromInput('types');
    $vars['types'] = serialize($vars['types']);  
    $vars['ctp_id'] = pnVarCleanFromInput('ctp_id'); 
    $vars['wd_id'] = pnVarCleanFromInput('wd_id');   
	$blockinfo['content'] = pnBlockVarsToContent($vars);
	
	return $blockinfo;
}
function find_wd_ids($wd_id,&$str)
{
	$pntables=&pnDBGetTables();
    $dbconn=pnDBGetConn(true);
    $wd_table=$pntables['web_directories'];
    $wd_column=$pntables['web_directories_column'];
    $sql="SELECT $wd_column[id] FROM $wd_table WHERE $wd_column[parent_id]=$wd_id";
    $str.=",$wd_id";
    $result=$dbconn->Execute($sql);
	if ($result->_numOfRows==0)return ;   
    
    while (!$result->EOF)
    {
    	$wd_id=$result->fields[0];
    	find_wd_ids($wd_id,&$str);
    	$result->MoveNext();
    }
    
}
function getTitleFromType2($content)
{
	if($content=="dynamic_content"||$content=="cdk")
	{
		return _DYNAMIC_CONTENT;
	}
	elseif (strstr($content,'module_'))
	{
		require_once("services/control_panel/lang/".pnUserGetLang()."/menu.php");
		$content=substr($content,7);
		return constant($content);
	}
	else 
	{
		pnModDBInfoLoad('cdk', '', true);
		$pntables=pnDBGetTables();
		$dbconn=pnDBGetConn(true);
		
		$cdk_table=$pntables['content_types'];
		$cdk_column=$pntables['content_types_column'];
		$sql="SELECT $cdk_column[title] FROM $cdk_table WHERE $cdk_column[type_name]='$content'";
		
		$result=$dbconn->Execute($sql);

		$title=unserialize($result->fields[0]);
		return $title[pnUserGetLang()];
		
	}
}

?>