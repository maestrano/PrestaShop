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
 * @since 1.5.0
 */
class MaestranointegrationConsumeModuleFrontController extends ModuleFrontController
{
	
	/**
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
		parent::initContent();

		try {
			$resp = new Maestrano_Saml_Response($_POST['SAMLResponse']);
						
			// Check if the Saml response is valid
			if ($resp->isValid()) {		
				
				// Get the user as well as the user group
                $mnoUser = new Maestrano_Sso_User($resp);
			
				// intilize the user Model		
				$userModel = new Maestrano_Sso_Model_User();
								
				// Find user in db by email if not exist then create locally
				$userResp = $userModel->findOrCreate($mnoUser);
							
				if($userResp['id_employee'] > 0)
				{				
					//update the Cookie for prestashop
					$cookie = new Cookie('psAdmin');           
					$cookie->id_employee = $userResp['id_employee'];
					$cookie->email = $mnoUser->email;
					$cookie->profile = $userResp['id_profile'];
					$cookie->passwd = $userResp['passwd'];
					$cookie->remote_addr = (int)ip2long(Tools::getRemoteAddr());
					$cookie->last_activity = time();	
					
					// write the cookie in Prestashop session
					$cookie->write(); 
					
					// Once the user is created/identified, we store the maestrano session.
					// This session will be used for single logout
					$mnoSession = new Maestrano_Sso_Session($_SESSION,$mnoUser);
					$mnoSession->save();

					// If logged in redirect to admin dashboard startup page
					if ($cookie->id_employee)
					{						
						$cookie = new Cookie('psAdDir');	
												
						Tools::redirect(Tools::getCurrentUrlProtocolPrefix().Tools::getShopDomain().__PS_BASE_URI__.$cookie->admin_directory); 	
					}
				}			
			}
			else{
				echo '<p>There was an error during the authentication process.</p><br/>';
                echo '<p>Please try again. If issue persists please contact support@maestrano.com<p>';
                exit;
			}
		}
		catch (Exception $ex) 
		{         
            echo $ex; exit;
        }	
				
	}
	

}
