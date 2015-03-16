<?php
// Saman Portal
// Copyright (C) 2008 by the Saman Information Structure Development Team.
// http://www.sis-eg.com/
// ----------------------------------------------------------------------

function cdk_admin_main() {	
	pnRedirect('index.php?module=cdk&func=loadmodule&system=cdk&sismodule=configuration/content_types_list.php&_menu_=0');	
	//pnRedirect(pnModURL('cdk', 'user', 'loadmodule', array('system'=>'cdk', 'sismodule'=>'configuration___content_types_list.php')));
	die();
}

?>