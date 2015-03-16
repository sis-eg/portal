<?php
// Saman Portal
// Copyright (C) 2009 by the Saman Information Structure Development Team.
// http://www.sis-eg.com/
// ----------------------------------------------------------------------

function cdk_related_contentsblockblock_init(){
	pnSecAddSchema('cdk::menu_contentssblock:', 'Block title::');
}

function cdk_related_contentsblockblock_info(){
    return array('text_type' => 'related_contentsblockblock',
                 'module' => 'cdk',
                 'text_type_long' => _CDK_RELATEDCONTENT_BLOCK,
                 'allow_multiple' => true,
                 'form_content' => false,
                 'form_refresh' => false,
                 'show_preview' => false,
				 'default_title' => _CDK_RELATEDCONTENT_BLOCK_TITLE,
				 'allow_user_add'  => 0,
				 'group' => 2,
				'block_description' => _CDK_RELATEDCONTENT_BLOCK_DESCRIPTION
                 );
}

function cdk_related_contentsblockblock_display($blockinfo){
	 
    if (!pnSecAuthAction(0, 'cdk::menu_subjectsblock', "$blockinfo[title]::", ACCESS_READ)) 
    	return;
    
  
    $vars = pnBlockVarsFromContent($blockinfo['content']);
    $num_items=$vars['num_items'];
    if (!$num_items)$num_items=5;
    $length=$vars['length'];
    if (!$length)$length=23;
    $id=pnVarCleanFromInput('id');
    if (!$id)return ;
    pnModLoad('web_directory');
    $pntables=&pnDBGetTables();
    $dbconn=pnDBGetConn(true);
    $wdi_table=$pntables['web_directory_items'];
    $wdi_column=$pntables['web_directory_items_column'];
    $sql="SELECT $wdi_column[wd_id] FROM $wdi_table WHERE $wdi_column[url] LIKE '%id=$id'";
    $result=$dbconn->Execute($sql);

    $str="-1";
   	
    while (!$result->EOF)
    {
    	$str.=",".$result->fields[0];
    	$result->MoveNext();
    }
  	$other_lang='far';
    if (pnUserGetLang()=="far")$other_lang='eng';
    $sql2="SELECT DISTINCT($wdi_column[url]),$wdi_column[title],$wdi_column[content_type] 
    FROM $wdi_table 
    WHERE $wdi_column[wd_id] IN($str)   AND $wdi_column[lang]!='$other_lang' AND $wdi_column[url] NOT LIKE '%id=$id'
    ORDER BY $wdi_column[counter] DESC";

    $result2=$dbconn->Execute($sql2);
	if ($result2->_numOfRows==0)return ;   
    $items=array();
    while (!$result2->EOF)
    {
    	$url=$result2->fields[0];
    	$title=$result2->fields[1];
    	$content=$result2->fields[2];
    	if (count($items[$content]['items'])<$num_items)
    	{
    		$title=unserialize($title);
    		if ($content!="")
    		{
				$items[$content]['items'][]=array('title'=>$title[pnUserGetLang()],'url'=>$url);
				$items[$content]['title']=getTitleFromType($content);
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

function cdk_related_contentsblockblock_modify($blockinfo){
    $vars = pnBlockVarsFromContent($blockinfo['content']);
    $str="
    <tr>
    	<td class='caption'>"._NUM_ITEMS.":</td>
    	<td><input type='text' name='related_num_items' value='$vars[num_items]' size=10/></td>
    </tr>
    <tr>
    	<td class='caption'>"._TITLE_LENGTH.":</td>
    	<td><input type='text' name='title_length' value='$vars[title_length]' size=10/></td>
    </tr>
    ";
    return $str;
}

function cdk_related_contentsblockblock_update($blockinfo){
    $vars['num_items'] = pnVarCleanFromInput('related_num_items');    
    $vars['title_length'] = pnVarCleanFromInput('title_length');    
	$blockinfo['content'] = pnBlockVarsToContent($vars);
	return $blockinfo;
}

function getTitleFromType($content)
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