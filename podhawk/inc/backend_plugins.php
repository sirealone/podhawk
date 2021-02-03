<?php

	$actiontype = array('backend');
	include 'authority.php';

	$debug = false;
	$warning = false;

	try
	{
		if ($currentUser->isAdmin() == false)
		{
			throw new Exception('adminonly');
		}

		$message = "welcome";

		// find which plugins are not yet installed...
		$pluginsNotInstalled = array_diff($plugins->plugins_dir, $plugins->enabledPlugins, $plugins->disabledPlugins);
		//...and which are
		$pluginsInstalled = array_merge($plugins->enabledPlugins, $plugins->disabledPlugins);

		try
		{
			// installing a plugin from the directory
			if (isset($_GET['install']) && in_array($_GET['install'], $pluginsNotInstalled))
			{
			
				if (!$authenticated)
				{
					throw new Exception('no_auth');
				}
			
				$toInstall = $_GET['install'];
				include PLUGINS_DIR . $toInstall . "/" .$toInstall . ".php";

				// check that the plugin is a child of PluginPattern
				$r = new ReflectionClass($toInstall);
				$p = $r->getParentClass();
				if ($p->getName() != "PluginPattern")
				{
					throw new Exception ("You cannot install this plugin because the class $toInstall does not implement or extend the PluginPattern abstract class");
				}

				// create instance of plugin object
				$tempPlugin = new $toInstall();

				// write data about itself in the database
				$s = $tempPlugin->setup();

				if (!$s)
				{
					throw new Exception('install_fail');
				}
				
				//add the plugin to the list of installed but disabled plugins
				$plugins->disabledPlugins[] = $toInstall;

				//add data to the array of data held by the plugins object
				$data = array(	'name' => $toInstall,
								'full_name' => $tempPlugin->getFullName(),
								'enabled' => '0',
								'run_order' => '3',
								'params' => $tempPlugin->getInitialParams());
			
				$plugins->pluginsData[$toInstall] = $data;

				//add plugin to list of installed plugins
				$pluginsInstalled[] = $toInstall;

				//...and remove it from the list of non-installed plugins
				$pluginsNotInstalled = array_diff($pluginsNotInstalled, array($toInstall));

				$message =  "install_success";				
			
			}

			// uninstalling a plugin
			elseif (isset($_GET['uninstall']) && array_key_exists($_GET['uninstall'], $plugins->pluginsData))
			{

				if(!$authenticated)
				{
					throw new Exception('no_auth');
				}
			
				$toUninstall = $_GET['uninstall'];
	
				$s = $plugins->plugins->$toUninstall->remove();

				if (!$s)
				{
					throw new Exception("uninstall_fail");
				}

				$pluginsInstalled = array_diff ($pluginsInstalled, array($toUninstall));
				$pluginsNotInstalled[] = $toUninstall;

				$message = "uninstall_success";
		
			}

			// editing status and parameters of an installed plugin
			elseif (isset($_GET['edit']) && in_array($_GET['edit'], $pluginsInstalled))
			{

				$toEdit = $_GET['edit'];

				if(!$authenticated)
				{
					throw new Exception('no_auth');
				}

				// have we got posted data?
				if (isset($_POST['submit']) && $authenticated)
				{

					$plugins->plugins->$toEdit->writeData();
					$message = "saved";
					$smarty->clear_all_cache();
					$cache_manager->make_htaccess_all();

				}

				$h = $plugins->event("onBackendPluginsPage");

				$smarty->assign("thisPluginData", $plugins->plugins->$toEdit->getPluginPageData());

			}// close editing an installed plugin			

		} // close inner try block 

		catch (Exception $e)
		{
			$message = $e->getMessage();
			$warning = true;
		}

		// assigning stuff to Smarty	
		$smarty->assign(array(  "pluginsData"         => $plugins->pluginsData,
								"pluginsInstalled"    => $pluginsInstalled,
								"pluginsNotInstalled" => $pluginsNotInstalled,
								"pluginsEnabled"      => $plugins->enabledPlugins	
								));

		$smarty->assign('plugins_auth_key', $sess->createPageAuthenticator('plugins'));


		if ($debug == true)
		{
			echo "<pre>";
			echo "\nPlugins in directory\n";
			print_r($plugins->plugins_dir);
			echo "\nPlugins not installed\n";
			print_r ($pluginsNotInstalled);
			echo "\nPlugins data\n";
			print_r ($plugins->pluginsData);
			if (isset($toEdit))
			{
				echo "\nData for this plugin\n";
				print_r ($plugins->plugins->$toEdit->getPluginPageData());
			}
		}

	} //close outer try block

	catch (Exception $e)

	{
		$message = $e->getMessage();
		$warning = true;
	}

	$smarty->assign ('message', $message);
	$smarty->assign ('warning', $warning);
?>
