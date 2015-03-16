<?php
// Saman Portal
// Copyright (C) 2008 by the Saman Information Structure Development Team.
// http://www.sis-eg.com/
// ----------------------------------------------------------------------


function cdk_content_refercodeblockblock_init(){
	pnSecAddSchema('cdk:content_refercodeblock:', 'Block title::');
}

function cdk_content_refercodeblockblock_info(){
    return array('text_type' => 'content_refercodeblock',
                 'module' => 'cdk',
                 'text_type_long' => _CDK_BLK_REFER_BLOCK,
                 'allow_multiple' => true,
                 'form_content' => false,
                 'form_refresh' => false,
                 'show_preview' => false,
				 'default_title' => _CDK_BLK_REFER_BLOCK,
				 'allow_user_add'  => 1,
				 'group' => 2,
				 'block_description' => _CDK_BLOCK_DESCRIPTION,
			     'allow_subportal_add'  => 1,
			     'is_object' => true			     
                 );
}

function cdk_content_refercodeblockblock_display($blockinfo){
    if (!pnSecAuthAction(0, 'cdk::content_editblock', "$blockinfo[title]::", ACCESS_READ)) 
    	return;
    
    $vars = pnBlockVarsFromContent($blockinfo['content']);       
    $errorMsg = '';
    if (pnSessionGetVar('cdkReferCodeError')) {
    	$errorMsg = '<div style="color:red;text-align:center">'.pnSessionGetVar('cdkReferCodeError').'</div>';
    	pnSessionDelVar('cdkReferCodeError');
    }
    
    pnModAPILoad('cdk');
    sisDisableCache();
     if (SHORT_URL)
    	$sisModule='user___content_refer_form.php';
    else 
    	$sisModule='user/content_refer_form.php';
    	
	$blockinfo['content'] = 
	"<form method='post' action='".pnModURL('cdk', 'user', 'loadmodule', array('system'=>'cdk', 'sismodule'=>$sisModule))."'>
		 $errorMsg
		 <table style='margin-top:10px;width:100%' cellpadding='3' cellspacing='2' >
			<tr>
				<td style='width:10%' nowrap>"._CDK_BLK_REFER_CODE ."</td>
				<td><input type='text' style='width:85%' id='txtReferCode' name='refer_code'/>&nbsp;<span style='color:red'>*</span></td>
			</tr>
			<tr>
				<td></td>
				<td>
					<img src='images/securitycode.jpg' id='imgReferCaptchaCode'/>
				</td>
			</tr>
			<tr>
				<td style='width:10%' nowrap>"._CDK_BLK_REFER_SECURITY_CODE ."</td>
				<td><input type='text' style='width:71px' id='txtReferCodeCaptcha' name='captcha'/>&nbsp;<span style='color:red'>*</span></td>
			</tr>
			<tr>
				<td></td>
				<td><input type='submit' value='"._CDK_BLK_DO_REFER."' style='width:60px' onclick='if (trim(document.getElementById(\"txtReferCode\").value)==\"\") {alert(\""._CDK_BLK_REFER_VALIDATION."\"); document.getElementById(\"txtReferCode\").focus(); return false;} if (trim(document.getElementById(\"txtReferCodeCaptcha\").value)==\"\") {alert(\""._CDK_BLK_REFER_VALIDATION."\"); document.getElementById(\"txtReferCodeCaptcha\").focus(); return false;} ' /></td>
			</tr>
		 </table>
		 <script>
				document.getElementById('imgReferCaptchaCode').src =basePath+'images/securitycode.jpg';
				document.getElementById('imgReferCaptchaCode').src =basePath+'".pnGetBaseURI()."/user.php?op=securityImg&module=user_registeration';
				
				function _validateReferForm() {
					return false;
				}
		 </script>		 
	 </form>";
    return themesideblock($blockinfo);	
}

function cdk_content_refercodeblockblock_modify($blockinfo){
    $vars = pnBlockVarsFromContent($blockinfo['content']);    
    $output = new pnHTML();
	$output->SetInputMode(_PNH_VERBATIMINPUT);
	$output->Text("");
    return $output->GetOutput();
}

function cdk_content_refercodeblockblock_update($blockinfo){
	$vars = array();
	$blockinfo['content'] = pnBlockVarsToContent($vars);
	return $blockinfo;
}
?>