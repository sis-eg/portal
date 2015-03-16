<?php
// Saman Portal
// Copyright (C) 2008 by the Saman Information Structure Development Team.
// http://www.sis-eg.com/
// ----------------------------------------------------------------------


function cdk_content_editblockblock_init(){
	pnSecAddSchema('cdk:content_editblock:', 'Block title::');
}

function cdk_content_editblockblock_info(){
    return array('text_type' => 'content_editblock',
                 'module' => 'cdk',
                 'text_type_long' => _CDK_CONTNET_EDIT_BLOCK,
                 'allow_multiple' => true,
                 'form_content' => false,
                 'form_refresh' => false,
                 'show_preview' => false,
				 'default_title' => _CDK_CONTNET_EDIT_BLOCK_TITLE,
				 'allow_user_add'  => 0,
				 'group' => 2,
  				  'block_description' => _CDK_CONTNET_EDIT_BLOCK_DESCRIPTION
                 );
}

function cdk_content_editblockblock_display($blockinfo){
    if (!pnSecAuthAction(0, 'cdk::content_editblock', "$blockinfo[title]::", ACCESS_READ)) 
    	return;
    
    $vars = pnBlockVarsFromContent($blockinfo['content']);       
    $originalGetParams = $_GET;
    $_GET['viewPageId']	= $vars['viewPageId'];
    $lastOp = $GLOBALS['sisOp'];
    $_GET['sisOp'] = $GLOBALS['sisOp'] = 'new';
	$blockinfo['content'] = pnModFunc('cdk', 'user', 'loadmodule', array('system'=>'cdk',
													 							  	 'sismodule' => 'block/content_edit_block.php'));													 							  	 
													 							  	 
	$GLOBALS['sisOp'] = $lastOp;
	$_GET = $originalGetParams;
    return themesideblock($blockinfo);	
}

function cdk_content_editblockblock_modify($blockinfo){
    $vars = pnBlockVarsFromContent($blockinfo['content']);    
	$content .= "<tr>
					<td colspan='2'><br></td>
				</tr>
				<tr>
					<td class='caption' nowrap='nowrap'>"
						. _CNT_BLK_VIEW_PAGE_ID . " : 
					</td>
					<td>
						<input type='text' size='4' name='viewPageId' value='$vars[viewPageId]' />
			   		</td>
				</tr>
				<tr>
					<td colspan=\"2\"><br></td>
				</tr>";
    $output = new pnHTML();
	$output->SetInputMode(_PNH_VERBATIMINPUT);
	$output->Text($content);
    return $output->GetOutput();
}

function cdk_content_editblockblock_update($blockinfo){
	$vars = array();
	$vars['viewPageId'] = pnVarCleanFromInput('viewPageId');
	$blockinfo['content'] = pnBlockVarsToContent($vars);
	return $blockinfo;
}
?>