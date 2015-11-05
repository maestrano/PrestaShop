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
class MaestranointegrationSubscribeModuleFrontController extends ModuleFrontController
{
	
	/**
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
		parent::initContent();
		
		
		try {
			if(!Maestrano::param('connec.enabled')) { return false; }
			
			$client = new Maestrano_Connec_Client();

			$notification = json_decode(file_get_contents('php://input'), false);
			$entity_name = strtoupper(trim($notification->entity));
			$entity_id = $notification->id;		
			
			switch ($entity_name) {
				
				case "PERSONS":
					$customerMapper = new CustomerMapper();
					$customerMapper->fetchConnecResource($entity_id);					
				break;
				
				case "ITEMS":
					$productMapper = new ProductMapper();
					$productMapper->fetchConnecResource($entity_id);
				break;
				
				case "TAXCODES":
					$taxMapper = new TaxMapper();
					$taxMapper->fetchConnecResource($entity_id);
				break;
		  				
			}	
		}
		catch (Exception $e) 
		{
			error_log("Caught exception in subscribe " . json_encode($e->getMessage()));
		}
				 
	}

}
