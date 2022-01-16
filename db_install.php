<?php
/**********************************************************************************
* db_install.php                                                           	      *
***********************************************************************************
*                                                                                 *
* SMFPacks Shoutbox Lite v1.1	                                                  *
* Copyright (c) 2015-2017 by NIBOGO. All rights reserved.              	  		  *
* Powered by www.smfpacks.com                                                     *
* Developed by NIBOGO for SMFPacks.com                                            *
*                                                                                 *
**********************************************************************************/

	global $context, $smcFunc, $db_type;
	
	// If SSI.php is in the same place as this file, and SMF isn't defined, this is being run standalone.
	if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
		require_once(dirname(__FILE__) . '/SSI.php');
	// Hmm... no SSI.php and no SMF?
	elseif (!defined('SMF'))
		die('<b>Error:</b> Cannot install - please verify you put this in the same place as SMF\'s index.php.');
	
	if (empty($context['uninstalling']))
	{
		db_extend('packages');
		
		// Messages!
	    $columns = array(
		    array(
		      'name' => 'ID_SHOUT',
		      'type' => 'mediumint',
		      'size' => 8,
			  'auto' => true,
		      'null' => false
		    ),
		    array(			
		      'name' => 'ID_MEMBER',
		      'type' => 'mediumint',
			  'size' => 8,
			  'default' => 0,
		      'null' => false
		    ),
			array(
		      'name' => 'realName',
		      'type' => 'tinytext',
		      'null' => false
		    ),
		    array(
		      'name' => 'colorName',
		      'type' => 'varchar',
		      'size' => '20',
		      'null' => false,
		      'default' => '""'
		    ),	
			array(
		      'name' => 'style',
		      'type' => 'text',
		      'null' => false
		    ),	
			array(
		      'name' => 'message',
		      'type' => 'text',
		      'null' => false
		    ),
		    array(
		      'name' => 'timestamp',
		      'type' => 'int',
		      'size' => 10,
		      'default' => 0,
		      'null' => false
		    ),
	    );
	
	    $indexes = array(
	      array(
	        'type' => 'primary',
	        'columns' => array('ID_SHOUT')
	      ),
	    );
		  
		$smcFunc['db_create_table']('{db_prefix}shoutbox', $columns, $indexes);
		
		// Shoutbox Bans
		$columns = array(
		    array(
		      'name' => 'ID_MEMBER',
		      'type' => 'mediumint',
		      'size' => 8,
		      'default' => 0,
		      'null' => false
		    ),    
		    array(
		      'name' => 'banStart',
		      'type' => 'int',
		      'size' => 10,
		      'default' => 0,
		      'null' => false
		    ),
		    array(
		      'name' => 'banEnd',
		      'type' => 'int',
		      'size' => 10,
		      'default' => 0,
		      'null' => false
		    ),
		    array(
		      'name' => 'reason',
		      'type' => 'text',
		      'null' => false
		    ),
		    array(
		      'name' => 'banBy',
		      'type' => 'tinytext',
		      'null' => false
		    ),
		    array(
		      'name' => 'banByID',
		      'type' => 'mediumint',
		      'size' => 8,
		      'default' => 0,
		      'null' => false
		    ),
	    );
	
	    $indexes = array(
	      array(
	        'type' => 'primary',
	        'columns' => array('ID_MEMBER')
	      ),
	    );	
		  
		$smcFunc['db_create_table']('{db_prefix}shoutbox_ban', $columns, $indexes);
		
		// Shoutbox settings
		$columns = array(
		    array(
		      'name' => 'variable',
		      'type' => 'tinytext',
		      'null' => false
		    ),    
		    array(
		      'name' => 'value',
		      'type' => 'text',
		      'null' => false
		    )
	    );
	
	    $indexes = array(
	      array(
	        'type' => 'primary',
	        'columns' => array('variable(30)')
	      ),
	    );	
		
		$smcFunc['db_create_table']('{db_prefix}shoutbox_settings', $columns, $indexes);
		
		// Insert some settings
		$smcFunc['db_query']('', "INSERT IGNORE INTO {db_prefix}shoutbox_settings (variable, value)
			VALUES
				('timeColor','#b7b7b7'),
				('minMsgLenght','1'),
				('maxMsgLenght','1024'),
				('maxLinkLenght','38'),
				('fixLongWords','60'),
				('timeFormat','%d|%b %I:%M %p'),
				('boxTitle','SMFPacks Shoutbox'),
				('showmsg_down','1'),
				('faces','Arial,Arial Black,Comic Sans MS,Courier New,Fixedsys,Lucida Console,Lucida Sans Unicode,Microsoft Sans Serif,System,Trebuchet MS,Times New Roman,Verdana'),
				('backgroundColor','#FFFFFF'),
				('keepShouts','100'),
				('height','180'),
				('showActions','boardindex,collapsecategory,collapse'),
				('printClass','smalltext'),
				('refreshShouts','8000'),
				('startShouts','20'),
				('disableTags','bgcolor'),
				('banUpadte','0'),
				('lastPrune', '0'),
				('textColor','#000000'),
				('showform_down','1'),
				('convertMiniLinks','1')");
				
		$call = 'add_integration_function';
	}
	else
		$call = 'remove_integration_function';

	// Integration hooks cause is cooler!
	$hooks = array(
		'integrate_actions' => 'shoutbox_add_actions',
		'integrate_load_theme' => 'shoutbox_load_theme',
		'integrate_pre_include' => '$sourcedir/Subs-Shoutbox.php',
		'integrate_admin_areas' => 'shoutbox_admin_areas',
		'integrate_load_permissions' => 'shoutbox_load_permissions',
	);
	
	foreach ($hooks as $hook => $function)
		$call($hook, $function);

	if (SMF == 'SSI')
		echo 'Database changes are complete!';