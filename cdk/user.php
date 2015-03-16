<?php
// Saman Portal
// Copyright (C) 2008 by the Saman Information Structure Development Team.
// http://www.sis-eg.com/
// ----------------------------------------------------------------------

function cdk_user_main() {
	return;
}

function cdk_user_loadmodule($args) {	
	return pnModFunc('sisRapid', 'user', 'loadmodule', $args);	
}

function cdk_user_loadlibmodule($args) {
	return pnModFunc('sisRapid', 'user', 'loadlibmodule', $args);	
}

function cdk_user_loadobject($args) {	
	return pnModFunc('sisRapid', 'user', 'loadobject', $args);	
}

function cdk_user_search($args) {
	return pnModFunc('sisRapid', 'user', 'search', $args);		
}

function cdk_user_report_error() {
	return pnModFunc('sisRapid', 'user', 'report_error');	
}

function cdk_user_form_report_error() {
	return pnModFunc('sisRapid', 'user', 'form_report_error');	
}

function cdk_user_treeMenu($args) {
	extract($args);
	$contentType = pnModAPIFunc('cdk', 'user', 'getType', array('ctp_id' => $contentType));
	$extraQuery = '';
	if (trim($selectingQuery) > '') {
		list($dbconn) = pnDBGetConn();	
		$pntable = pnDBGetTables();
		$contentColumns = &$pntable['content_column'];
		
		$selectingQuery = split('#', $selectingQuery);
		for ($i=0; $i<count($selectingQuery); $i++) {
			if ($i%2==1)
				$selectingQuery[$i] = sisParam($selectingQuery[$i]);
		}
		$selectingQuery = implode('', $selectingQuery);
	    $extraQuery = "SELECT 
	    			id
	    		FROM 
	    			saman_content_$contentType[type_name]_items
	    		WHERE
	    			$selectingQuery";		
	}
	$tableName = '';
	if ($contentType['type_name'] > '')
		$tableName  = 'content_' . $contentType['type_name'] . '_items';

	$tree = pnModAPIFunc('web_directory', 'user', 'getTree', array('wd_id' => $catid, 'max_length'=>$maxlength, 'table_name'=>$tableName, 'extraQuery'=>$extraQuery));

	$content = $menutype.'~~~~0~~';
	$content .= cdk_create_menu($tree, 0, $contentTypeTemplate, $showcontents, $linkdirectory, $showitemscount, $contentType['type_name']);
	$content .= '#||||||';

	return $content;
}

function cdk_create_menu($tree, $indent, $template, $showItems, $linkDirectory, $showItemsCount, $contentType) {
	$retValue = str_repeat('.', $indent + 1);
	if ($showItemsCount)
		$tree['title'] .= ' (' . $tree['items_count'] . ')';
	if ($linkDirectory)
		$tree['url'] = "index.php?name=web_directory&wd_id=$tree[id]&content_type=$contentType";
	$retValue .= "|$tree[title]|$tree[url]|$tree[title]|||\n";	
	foreach ($tree['directories'] as $dir) {
		$retValue .= cdk_create_menu($dir, $indent + 1, $template, $showItems, $linkDirectory, $showItemsCount, $contentType);
	}
	if ($showItems)
		foreach ($tree['items'] as $item) {		
			$retValue .= str_repeat('.', $indent + 2);
			$retValue .= "|$item[title]|$item[url]&hintEnable=true&template=$template|$item[title]|||\n";
		}
	
	return $retValue;
}

function cdk_user_getTypeTemplateCombo(){
	
	$ctp_id=pnVarCleanFromInput('ctp_id');
	
	$ctp_id=abs(intval($ctp_id));
	if ($ctp_id<=0) {
		die();
	}
	$typeTemplates=pnModAPIFunc('cdk','user','getTypeTemplates',array('ctpId'=>$ctp_id));
	$tablistblock	=	pnVarCleanFromInput('tablistblock');
	if( $tablistblock == 1 )
		foreach ($typeTemplates as $key => $value) {
				$output .= "<option value='$value' >$value</option>";
		}
	else 
		foreach ($typeTemplates as $key => $value) {
				$output .= "<option value='$key' >$value</option>";
		}
	
	echo $output;
	die();
}

function cdk_user_checkTemplateSyntax($return = false) {
	require_once('portlets/sisRapid/dream/packs/services/dynamic_content/objects/form/designer_utils.php');
	list($code, $extraReplace, $includePHPTags) = pnVarCleanFromInput('code', 'extraReplace', 'includePHPTags');
	$code = pnModAPIFunc('cdk', 'user', 'compileTemplate', array('template'=>$code, 'extraReplace'=>$extraReplace, 'includePHPTags'=>$includePHPTags));
	$designerUtils = new designer_utils();
	$value = $designerUtils->phpSyntaxError($code);
	if ($return) {
		return $value[0];
	}
	die($value[0]);
}

function cdk_user_saveTemplate() {
	$syntax = cdk_user_checkTemplateSyntax(true);
	
	list($code, $savaParam, $includePHPTags) = pnVarCleanFromInput('code', 'saveParam', 'includePHPTags');
	$savaParam = decrypt($savaParam);
	$savaParam = split(':', $savaParam);
	if (count($savaParam) != 4)
		return;
	$savaParam[0] = pnVarPrepForStore($savaParam[0]);
	$savaParam[1] = pnVarPrepForStore($savaParam[1]);
	$savaParam[2] = pnVarPrepForStore($savaParam[2]);
	$savaParam[3] = pnVarPrepForStore($savaParam[3]);
	
	$result = pnModAPIFunc('cdk', 'user', 'saveTemplate', array('table'=>$savaParam[0], 'primaryKey'=>$savaParam[1], 'primaryKeyValue'=>$savaParam[2], 'includePHPTags'=>$includePHPTags, 'field'=>$savaParam[3], 'code'=>$code));
	
	if ($syntax) {
		die('syntax error:'.$syntax);
	}
	die($result);
}

function cdk_user_editTemplate() {
	$contentType = pnModAPIFunc('cdk', 'user', 'getType', array('ctp_id'=>intval(pnVarCleanFromInput('ctp_id'))));
	$fields = unserialize(base64_decode($contentType['type_fields']));	
	$fieldsCombo = '';
	$lang = pnUserGetLang();
	foreach ($fields as $field) {
		$field = json_decode($field, true);
		$tmpField = array();
		foreach ($field as $fieldValue)	{
			$tmpField[$fieldValue['name']] = $fieldValue['value'];
		}
		$fieldTitle = urldecode($tmpField['title_'.$lang]);
		$fieldsCombo .= "<option value='$tmpField[fieldName]'>$fieldTitle</option>";
	}	
	echo '
	<table style="width:100%;height:500px" border=0 cellspacing=0 cellpadding=0>
		<tr>
			<td>
				<input type="button" value="Check Syntax" onclick="codeEditorCheckSyntax()"/>
				<input type="button" value="Save" onclick="codeEditorSave(false)"/>
				<input type="button" value="Save & Close" onclick="codeEditorSave(true)"/>
				<input type="button" value="Close" onclick="window.close();"/>
				<select id="cmbFields">'.$fieldsCombo.'</select>
				<select id="cmbInsertType"><option value=1>Title</option><option value=2>Content</option></select>
				<input type="button" value="Insert Code" onclick="codeEditorInsertCode()"/>
			</td>
		<tr/>
		<tr>
			<td>
            	<textarea id="myCpWindows2" style="width: 100%;height: 450px"></textarea>
			
			</td>
		</tr>
	</table>
    <script language="javascript" type="text/javascript" src="'.pnGetBaseURL().'addons/sisSoftwareFactory/addons/editor/edit_area/edit_area_full.js"></script>	
	<script>
        editAreaLoader.init({
            id : "myCpWindows2"		
            ,syntax: "PHP"			
            ,start_highlight: true		
            ,allow_toggle:false
            ,allow_resize:"no"
            ,toolbar:"search, go_to_line"
            ,language:"fa"
            ,EA_load_callback:"codeEditorLoad"/*
            ,change_callback:"editorOnChange"*/
        });
        editAreaLoader.setValue("myCpWindows2", window.code);
		document.body.onresize = adjustScreenSize;			
		
		function codeEditorCheckSyntax() {
			var code = editAreaLoader.getValue("myCpWindows2");
			$.ajax({
				url: "index.php?module=cdk&func=checkTemplateSyntax",  
				type: "POST",  
				data: {code : code, extraReplace : window.extraReplace},  
				success: function(html){codeEditorCheckSyntaxEnd(html)},
				dataType: "html"
			});
		}
        
		function codeEditorCheckSyntaxEnd(html) {
			if (html == "") {
				alert("It\'s OK");
				return;
			}
			html = html * 1;
			var lines = editAreaLoader.getValue("myCpWindows2").split("\n");
			var startIndex = 0;
			if (html > 1) {
				startIndex += html - 1;
				for(var idx=0; idx < html - 1; idx++) {
					startIndex += lines[idx].length;
				}
				endIndex = startIndex + lines[idx].length;
			}
			else
				endIndex = startIndex + lines[0].length;
            editAreaLoader.setSelectionRange("myCpWindows2", startIndex, endIndex);
		}
		
		function codeEditorSave(closeForm) {
			var code = editAreaLoader.getValue("myCpWindows2");

			$.ajax({
				url: "index.php?module=cdk&func=saveTemplate",  
				type: "POST",  
				data: {code : code, saveParam: window.saveParameters},  
				success: function(html){alert(html); window.opener.document.getElementById(window.codeControlName).value = code; if (closeForm) window.close();},
				dataType: "html"
			});				
		}
		
		function codeEditorInsertCode() {
			insertType = document.getElementById("cmbInsertType").value;
			fieldName = document.getElementById("cmbFields").value;
			if (insertType == 1)
				editAreaLoader.insertTags("myCpWindows2", "##fieldCaption[\'" + fieldName + "\']##", "");
			else
				editAreaLoader.insertTags("myCpWindows2", "##fieldContent[\'" + fieldName + "\']##", "");
		}
		
        function adjustScreenSize() { 
            var tmpHeight = document.childNodes[1].clientHeight - 19;
            var tmpWidth = document.childNodes[1].clientWidth;
            if (tmpHeight < 400)
                tmpHeight = 400;
            if (tmpWidth < 500 )
                tmpWidth = 500;
            try {
                if (document.getElementById("frame_myCpWindows2") != null) {
                    document.getElementById("frame_myCpWindows2").style.height = (tmpHeight)  + "px";
                    document.getElementById("frame_myCpWindows2").style.width = (tmpWidth) + "px";                    
                }    
            }
            catch (ex) {}                   
        }		
        
        function codeEditorLoad() {
        	adjustScreenSize();
        }
        
	</script>
	';
}

function cdk_user_createThumbnail() {
	ob_end_clean();
	list($image, $width, $height) = pnVarCleanFromInput('image', 'width', 'height');	
	
	if (!$image || !$width || !$height) {
		die();
	}
	$decodedImage = base64_decode($image);	
	
	if ($image == base64_encode($decodedImage)) {
		$image = $decodedImage;
	}	

	$path = sisGetSetting('sisUPLOADPATH').'tmp/';
	@mkdir($path);
	
	if (strpos($image, get_htdocs_path()) !== false) {
		$image = $image;
	}
	else if ($image[0] == '/') {
		$image = get_htdocs_path().$image;
	}
	else {	
		$image = sisGetSetting('sisPORTALPATH').'/'.WHERE_IS_PERSO.'modules/cdk/upload/'.$image;
	}
	if (!is_file($image)) {		
		die();
	}
	
	$key = md5($image.$width.$height);
		
	if (is_file($path.$key.'.jpg')) {
    	if (time() - filectime($path.$key.'.jpg') <= 300) {
			die(file_get_contents(sisGetSetting('sisUPLOADPATH').'tmp/'.$key.'.jpg'));;
		}
	}
	
	$imageContent = sisCacheGet($image.$width.$height);
	$imageContent = sisCacheGet($key);
	if ($imageContent) {
		file_put_contents($path.$key.'.jpg', $imageContent);
		die($imageContent);
	}
		
	$resultImage = tempnam("/tmp", "sis");
		
	require_once('api/classes/ajaxcrop/func.php');             

	createThumbnail($image, $width, $height, $resultImage, null, null, null, null, null, null, function_exists('mime_content_type')?mime_content_type($image):null, 'image/jpeg');
	
	$imageContent = file_get_contents($resultImage);

	sisCacheSet($key, $imageContent, 60);

	@unlink($resultImage);	
	
	file_put_contents($path.$key.'.jpg', $imageContent);	
	
	die($imageContent);
}
?>