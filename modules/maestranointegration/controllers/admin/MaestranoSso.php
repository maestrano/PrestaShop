<?php
/*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @since 1.6.0
 */
 
class MaestranoSso extends ModuleAdminController
{
	public function __construct() 
	{
		
		if (Maestrano::sso()->isSsoEnabled()) 
		{			
			if(!$this->isAdminLogged())
			{
				 //$samlController = array('init','consume')
				 //Tools::getValue('controller');
				 //if (!in_array(Tools::getValue('controller'), $samlController)) 
				 
				 
				 $adminDir = $this->cookieForAdminDirectory();
				 
				 if($adminDir != "" AND strpos($adminDir, "admin") !== false)
				 {
					 				 
					// Write cookie for the Admin directory
					$cookie = new Cookie('psAdDir');
					$cookie->admin_directory = $adminDir;
					$cookie->write(); 
					
					// Redirect to Masterano
					Tools::redirect(Tools::getCurrentUrlProtocolPrefix().Tools::getShopDomain().__PS_BASE_URI__.Maestrano::sso()->getInitPath());
				 }
						
			}
		}
	}		
	
	/**
	 * check Admin Logged or Not
	 *
	 * @return flag as true,false
	 */
	public function isAdminLogged()
	{	
		$cookie = new Cookie('psAdmin');
		
		if ($cookie->id_employee)
		{
			return true;
		}
		return false;
		
	}
	
	/**
	 * Generate cookie for admin directory becuase prestashop have dynamic names of admin directory
	 *
	 * @return the directory name
	 */
	public function cookieForAdminDirectory()
	{
		$pageURL = 'http';
		if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on"){
			$pageURL .= "s";
		}
		$pageURL .= "://";
		if ($_SERVER["SERVER_PORT"] != "80"){
			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		}
		else{
			$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}	
		
		$pageURL = str_replace(Tools::getCurrentUrlProtocolPrefix().Tools::getShopDomain().__PS_BASE_URI__,'',$pageURL);
		$pageURL = str_replace(basename($pageURL),'',$pageURL);
		
		return $pageURL;
	}
	
}
