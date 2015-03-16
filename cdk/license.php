<?php
// Saman Portal
// Copyright (C) 2008 by the Saman Information Structure Development Team.
// http://www.sis-eg.com/
// ----------------------------------------------------------------------
$modversion['name']           = 'cdk';
$modversion['version']        = '2.0';
$modversion['description']    = 'Content Development Kit';
$modversion['help']           = '';
$modversion['official']       = 1;
$modversion['author'] 		  = 'Saman Information Structure';
$modversion['contact'] 		  = 'http://www.sis-eg.com';
$modversion['admin']          = 1;
$modversion['securityschema'] = array('cdk::' => '::', 'cdk:content_type:' => '::type_name');
$modversion['core']			  = 0;
$modversion['rq_modules']	  = array(0=>'dynamic_content',1=>'domains',2=>'base_tables');
$modversion['supportal']	  = 1;
$modversion['allowAddInPage'] = 1;
$modversion['license'] = 1;
/* Short URL Settings

outputfilter.shorturls.php
in -->
	  $prefix . 'index.php\?module=cdk&(?:amp;)?func=loadmodule&(?:amp;)?system=cdk&(?:amp;)?sismodule=([\w\d\.%\:\_\/]+)\/([\w\d\:\_\/]+).php"|',
	  $prefix . 'index.php\?module=cdk&(?:amp;)?func=loadmodule&(?:amp;)?system=cdk&(?:amp;)?sismodule=([\w\d\.%\:\_\/]+)\/([\w\d\:\_\/]+).php[#]([\w\d]*)"|',
	  $prefix . 'index.php\?module=cdk&(?:amp;)?func=loadmodule&(?:amp;)?system=cdk&(?:amp;)?sismodule=([\w\d\.%\:\_\/]+)\/([\w\d\:\_\/]+).php&(?:amp;)?([\w\d\.\:\_\/]+)=([\w\d\.%\:\_\/]+)"|',
	  $prefix . 'index.php\?module=cdk&(?:amp;)?func=loadmodule&(?:amp;)?system=cdk&(?:amp;)?sismodule=([\w\d\.%\:\_\/]+)\/([\w\d\:\_\/]+).php&(?:amp;)?([\w\d\.\:\_\/]+)=([\w\d\.%\:\_\/]+)[#]([\w\d]*)"|',
	  $prefix . 'index.php\?module=cdk&(?:amp;)?func=loadmodule&(?:amp;)?system=cdk&(?:amp;)?sismodule=([\w\d\.%\:\_\/]+)\/([\w\d\:\_\/]+).php&(?:amp;)?([\w\d\.\:\_\/]+)=([\w\d\.%\:\_\/]+)&(?:amp;)?([\w\d\.\:\_\/]+)=([\w\d\.%\:\_\/]+)"|',
	  $prefix . 'index.php\?module=cdk&(?:amp;)?func=loadmodule&(?:amp;)?system=cdk&(?:amp;)?sismodule=([\w\d\.%\:\_\/]+)\/([\w\d\:\_\/]+).php&(?:amp;)?([\w\d\.\:\_\/]+)=([\w\d\.%\:\_\/]+)&(?:amp;)?([\w\d\.\:\_\/]+)=([\w\d\.%\:\_\/]+)[#]([\w\d]*)"|',
	  $prefix . 'index.php\?module=cdk&(?:amp;)?func=loadmodule&(?:amp;)?system=cdk&(?:amp;)?sismodule=([\w\d\.%\:\_\/]+)\/([\w\d\:\_\/]+).php&(?:amp;)?([\w\d\.\:\_\/]+)=([\w\d\.%\:\_\/]+)&(?:amp;)?([\w\d\.\:\_\/]+)=([\w\d\.%\:\_\/]+)&(?:amp;)?([\w\d\.\:\_\/]+)=([\w\d\.%\:\_\/]+)"|', 
	  $prefix . 'index.php\?module=cdk&(?:amp;)?func=loadmodule&(?:amp;)?system=cdk&(?:amp;)?sismodule=([\w\d\.%\:\_\/]+)\/([\w\d\:\_\/]+).php&(?:amp;)?([\w\d\.\:\_\/]+)=([\w\d\.%\:\_\/]+)&(?:amp;)?([\w\d\.\:\_\/]+)=([\w\d\.%\:\_\/]+)&(?:amp;)?([\w\d\.\:\_\/]+)=([\w\d\.%\:\_\/]+)[#]([\w\d]*)"|', 
	  $prefix . 'index.php\?module=cdk&(?:amp;)?func=loadmodule&(?:amp;)?system=cdk&(?:amp;)?sismodule=([\w\d\.%\:\_\/]+)\/([\w\d\:\_\/]+).php&(?:amp;)?([\w\d\.\:\_\/]+)=([\w\d\.%\:\_\/]+)&(?:amp;)?([\w\d\.\:\_\/]+)=([\w\d\.%\:\_\/]+)&(?:amp;)?([\w\d\.\:\_\/]+)=([\w\d\.%\:\_\/]+)&(?:amp;)?([\w\d\.\:\_\/]+)=([\w\d\.%\:\_\/]+)"|',
	  $prefix . 'index.php\?module=cdk&(?:amp;)?func=loadmodule&(?:amp;)?system=cdk&(?:amp;)?sismodule=([\w\d\.%\:\_\/]+)\/([\w\d\:\_\/]+).php&(?:amp;)?([\w\d\.\:\_\/]+)=([\w\d\.%\:\_\/]+)&(?:amp;)?([\w\d\.\:\_\/]+)=([\w\d\.%\:\_\/]+)&(?:amp;)?([\w\d\.\:\_\/]+)=([\w\d\.%\:\_\/]+)&(?:amp;)?([\w\d\.\:\_\/]+)=([\w\d\.%\:\_\/]+)[#]([\w\d]*)"|',
	  $prefix . 'index.php\?module=cdk&(?:amp;)?func=loadmodule&(?:amp;)?system=cdk&(?:amp;)?sismodule=([\w\d\.%\:\_\/]+)\/([\w\d\:\_\/]+).php&(?:amp;)?([\w\d\.\:\_\/]+)=([\w\d\.%\:\_\/]+)&(?:amp;)?([\w\d\.\:\_\/]+)=([\w\d\.%\:\_\/]+)&(?:amp;)?([\w\d\.\:\_\/]+)=([\w\d\.%\:\_\/]+)&(?:amp;)?([\w\d\.\:\_\/]+)=([\w\d\.%\:\_\/]+)&(?:amp;)?([\w\d\.\:\_\/]+)=([\w\d\.%\:\_\/]+)"|',
	  $prefix . 'index.php\?module=cdk&(?:amp;)?func=loadmodule&(?:amp;)?system=cdk&(?:amp;)?sismodule=([\w\d\.%\:\_\/]+)\/([\w\d\:\_\/]+).php&(?:amp;)?([\w\d\.\:\_\/]+)=([\w\d\.%\:\_\/]+)&(?:amp;)?([\w\d\.\:\_\/]+)=([\w\d\.%\:\_\/]+)&(?:amp;)?([\w\d\.\:\_\/]+)=([\w\d\.%\:\_\/]+)&(?:amp;)?([\w\d\.\:\_\/]+)=([\w\d\.%\:\_\/]+)&(?:amp;)?([\w\d\.\:\_\/]+)=([\w\d\.%\:\_\/]+)[#]([\w\d]*)"|',
	  $prefix . 'index.php\?module=cdk&(?:amp;)?func=loadmodule&(?:amp;)?system=cdk&(?:amp;)?sismodule=([\w\d\.%\:\_\/]+)\/([\w\d\:\_\/]+).php&(?:amp;)?([\w\d\.\:\_\/]+)=([\w\d\.%\:\_\/]+)&(?:amp;)?([\w\d\.\:\_\/]+)=([\w\d\.%\:\_\/]+)&(?:amp;)?([\w\d\.\:\_\/]+)=([\w\d\.%\:\_\/]+)&(?:amp;)?([\w\d\.\:\_\/]+)=([\w\d\.%\:\_\/]+)&(?:amp;)?([\w\d\.\:\_\/]+)=([\w\d\.%\:\_\/]+)&(?:amp;)?([\w\d\.\:\_\/]+)=([\w\d\.%\:\_\/]+)"|',  
	  $prefix . 'index.php\?module=cdk&(?:amp;)?func=loadmodule&(?:amp;)?system=cdk&(?:amp;)?sismodule=([\w\d\.%\:\_\/]+)\/([\w\d\:\_\/]+).php&(?:amp;)?([\w\d\.\:\_\/]+)=([\w\d\.%\:\_\/]+)&(?:amp;)?([\w\d\.\:\_\/]+)=([\w\d\.%\:\_\/]+)&(?:amp;)?([\w\d\.\:\_\/]+)=([\w\d\.%\:\_\/]+)&(?:amp;)?([\w\d\.\:\_\/]+)=([\w\d\.%\:\_\/]+)&(?:amp;)?([\w\d\.\:\_\/]+)=([\w\d\.%\:\_\/]+)&(?:amp;)?([\w\d\.\:\_\/]+)=([\w\d\.%\:\_\/]+)[#]([\w\d]*)"|'
out -->
	  '"dcontent-$1-$2.'.$extension.'"',    
	  '"dcontent-$1-$2.'.$extension.'#$3"',  
	  '"dcontent-$1-$2-$3-$4.'.$extension.'"',  
	  '"dcontent-$1-$2-$3-$4.'.$extension.'#$5"',  
	  '"dcontent-$1-$2-$3-$4-$5-$6.'.$extension.'"',
	  '"dcontent-$1-$2-$3-$4-$5-$6.'.$extension.'#$7"',
	  '"dcontent-$1-$2-$3-$4-$5-$6-$7-$8.'.$extension.'"',  
	  '"dcontent-$1-$2-$3-$4-$5-$6-$7-$8.'.$extension.'#$9"',  
	  '"dcontent-$1-$2-$3-$4-$5-$6-$7-$8-$9-$10.'.$extension.'"',  
	  '"dcontent-$1-$2-$3-$4-$5-$6-$7-$8-$9-$10.'.$extension.'#$11"',  
	  '"dcontent-$1-$2-$3-$4-$5-$6-$7-$8-$9-$10-$11-$12.'.$extension.'"',  
	  '"dcontent-$1-$2-$3-$4-$5-$6-$7-$8-$9-$10-$11-$12.'.$extension.'#$13"',  
	  '"dcontent-$1-$2-$3-$4-$5-$6-$7-$8-$9-$10-$11-$12-$13-$14.'.$extension.'"',
	  '"dcontent-$1-$2-$3-$4-$5-$6-$7-$8-$9-$10-$11-$12-$13-$14.'.$extension.'"#$15',	  
.htaccess
	# Dynamic Content
	RewriteRule ^dcontent-([^-]+)-([^-]+)\.htm$ index.php?module=cdk&func=loadmodule&system=cdk&sismodule=$1/$2.php [L,NC,NS]
	RewriteRule ^dcontent-([^-]+)-([^-]+)#([^-]+)\.htm$ index.php?module=cdk&func=loadmodule&system=cdk&sismodule=$1/$2.php#$3 [L,NC,NS]	
	RewriteRule ^dcontent-([^-]+)-([^-]+)-([^-]+)-([^-]+)\.htm$ index.php?module=cdk&func=loadmodule&system=cdk&sismodule=$1/$2.php&$3=$4 [L,NC,NS]
	RewriteRule ^dcontent-([^-]+)-([^-]+)-([^-]+)-([^-]+)#([^-]+)\.htm$ index.php?module=cdk&func=loadmodule&system=cdk&sismodule=$1/$2.php&$3=$4#$5 [L,NC,NS]
	RewriteRule ^dcontent-([^-]+)-([^-]+)-([^-]+)-([^-]+)-([^-]+)-([^-]+)\.htm$ index.php?module=cdk&func=loadmodule&system=cdk&sismodule=$1/$2.php&$3=$4&$5=$6 [L,NC,NS]
	RewriteRule ^dcontent-([^-]+)-([^-]+)-([^-]+)-([^-]+)-([^-]+)-([^-]+)#([^-]+)\.htm$ index.php?module=cdk&func=loadmodule&system=cdk&sismodule=$1/$2.php&$3=$4&$5=$6#$7 [L,NC,NS]
	RewriteRule ^dcontent-([^-]+)-([^-]+)-([^-]+)-([^-]+)-([^-]+)-([^-]+)-([^-]+)-([^-]+)\.htm$ index.php?module=cdk&func=loadmodule&system=cdk&sismodule=$1/$2.php&$3=$4&$5=$6&$7=$8 [L,NC,NS]
	RewriteRule ^dcontent-([^-]+)-([^-]+)-([^-]+)-([^-]+)-([^-]+)-([^-]+)-([^-]+)-([^-]+)#([^-]+)\.htm$ index.php?module=cdk&func=loadmodule&system=cdk&sismodule=$1/$2.php&$3=$4&$5=$6&$7=$8#$9 [L,NC,NS]	
	
	RewriteRule ^dcontent-([^-]+)-([^-]+)-([^-]+)-([^-]+)-([^-]+)-([^-]+)-([^-]+)-([^-]+)-(.*)\.htm$ dcontent2-module=cdk&func=loadmodule&system=cdk&sismodule=$1/$2.php&$3=$4&$5=$6&$7=$8-$9.htm [L,NC,NS]
	
	RewriteRule ^dcontent2-([^-]+)-([^-]+)-([^-]+)\.htm$ index.php?$1&$2=$3 [L,NC,NS]
	RewriteRule ^dcontent2-([^-]+)-([^-]+)-([^-]+)#([^-]+)\.htm$ index.php?$1&$2=$3#$4 [L,NC,NS]
	RewriteRule ^dcontent2-([^-]+)-([^-]+)-([^-]+)-([^-]+)-([^-]+)\.htm$ index.php?$1&$2=$3&$4=$5 [L,NC,NS]
	RewriteRule ^dcontent2-([^-]+)-([^-]+)-([^-]+)-([^-]+)-([^-]+)#([^-]+)\.htm$ index.php?$1&$2=$3&$4=$5#$6 [L,NC,NS]
	RewriteRule ^dcontent2-([^-]+)-([^-]+)-([^-]+)-([^-]+)-([^-]+)-([^-]+)-([^-]+)\.htm$ index.php?$1&$2=$3&$4=$5&$6=$7 [L,NC,NS]
	RewriteRule ^dcontent2-([^-]+)-([^-]+)-([^-]+)-([^-]+)-([^-]+)-([^-]+)-([^-]+)#([^-]+)\.htm$ index.php?$1&$2=$3&$4=$5&$6=$7#$8 [L,NC,NS]
	RewriteRule ^dcontent2-([^-]+)-([^-]+)-([^-]+)-([^-]+)-([^-]+)-([^-]+)-([^-]+)-([^-]+)-([^-]+)\.htm$ index.php?$1&$2=$3&$4=$5&$6=$7&$8=$9 [L,NC,NS]		  
*/
?>