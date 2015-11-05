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
class Maestrano_Sso_Model_User extends ModuleFrontController
{
	public $id_profile = 1;  //Administrator role id is 1 ,Here you can assign other roles ids
	
	 /**
	 * Used by findOrCreate to create a local user
	 * based on the sso user.
	 * If the method returns null then access is denied
	 *
	 * @param Maestrano_Sso_User $mnoUser
	 * @return the the user created, null otherwise
	 * @throws Exception
	 */
	 	
	public function findOrCreate($mnoUser)
	{
		$result = array();
		
		$sql = "SELECT COUNT(*) FROM "._DB_PREFIX_."employee WHERE email = '".pSQL($mnoUser->email)."'";
		$totalShop = Db::getInstance()->getValue($sql);		
		if($totalShop == 0)
		{			
			$passwd = md5(pSQL(_COOKIE_KEY_.$this->generatePassword()));
			Db::getInstance()->insert('employee', array(
				'id_profile' => (int)$this->id_profile,
				'id_lang'    => (int)Configuration::get('PS_LANG_DEFAULT'),				
				'lastname'   => pSQL($mnoUser->getLastName()),
				'firstname'  => pSQL($mnoUser->getFirstName()),
				'email'      => pSQL($mnoUser->email),
				'passwd'     => pSQL($passwd),
				'default_tab'=> 1,
				'active'     => 1
			));
			
			$result['id_employee'] = Db::getInstance()->Insert_ID();
			$result['passwd']      = $passwd;
			$result['id_profile']  = $this->id_profile;
			return $result; 
		}		
		else
		{
			$sql = "SELECT id_employee,passwd,id_profile FROM "._DB_PREFIX_."employee WHERE email = '".pSQL($mnoUser->email)."'";
			if ($row = Db::getInstance()->getRow($sql))
			{
				$result['id_employee'] = $row['id_employee'];
				$result['passwd']      = $row['passwd'];
				$result['id_profile']  = $row['id_profile'];
				return $result;
			}			
			return false;	
		}
		return false;
	}
	
	/**
	 * Get the Current Language.	 
	 *
	 * @return integer as language id
	 */
    public function getCurrentLanguage() 
    {
        $sql = "SELECT id_lang FROM "._DB_PREFIX_."lang WHERE active = '1'";
        if ($row = Db::getInstance()->getRow($sql))
        {
			return $row['id_lang'];
		}
		return false;
    }
    
    
	/**
	 * Generate a random password.
	 * Convenient to set dummy passwords on users
	 *
	 * @return string a random password
	 */
    public function generatePassword() 
    {
        $length = 66;
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }
}
