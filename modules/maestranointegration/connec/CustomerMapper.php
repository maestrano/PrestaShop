<?php

/**
* Map Connec Customer Person representation to/from PrestaShop Contact
*/

class CustomerMapper extends BaseMapper {

	public function __construct() {
		parent::__construct();

		$this->connec_entity_name      = 'Customer';
		$this->local_entity_name       = 'Customers';
		$this->connec_resource_name    = 'people';
		$this->connec_resource_endpoint = 'people';
	}
	
	// Return the Customer local id
	protected function getId($person) 
	{
		return $person->id;
	}

	// Return a local Customer by id
	protected function loadModelById($local_id) 
	{			
		$person			   =  new StdClass;
		$person->firstname =  new StdClass;
		$person->lastname  =  new StdClass;
		$person->email     =  new StdClass;
		$person->id        =  new StdClass;
		$person->mode      =  new StdClass;
		
		$sql = "SELECT * FROM "._DB_PREFIX_."customer WHERE id_customer = '".pSQL($local_id)."'";
		if ($row = Db::getInstance()->getRow($sql))
		{			
			$person->firstname =  $row['firstname'];
			$person->lastname  =  $row['lastname'];
			$person->email     =  $row['email'];
			$person->id        =  $local_id;
			$person->mode      =  'edit';
			return $person;
		}
	}	
	
	protected function mapConnecResourceToModel($person_hash, $person) 
	{		
		// Map hash attributes to Person
		if($this->is_set($person_hash['code'])) { $person->column_fields['contact_no'] = $person_hash['code']; }
		if($this->is_set($person_hash['title'])) { $person->column_fields['salutation'] = $person_hash['title']; }
		if($this->is_set($person_hash['first_name'])) { $person->column_fields['firstname'] = $person_hash['first_name']; }
		if($this->is_set($person_hash['last_name'])) { $person->column_fields['lastname'] = $person_hash['last_name']; }
		if($this->is_set($person_hash['description'])) { $person->column_fields['description'] = $person_hash['description']; }
		if($this->is_set($person_hash['job_title'])) { $person->column_fields['title'] = $person_hash['job_title']; }
		if($this->is_set($person_hash['birth_date'])) { $person->column_fields['birthday'] = $this->format_date_to_php($person_hash['birth_date']); }
		
				
	}
	
	// Map the Prestashop Customer to a Connec resource hash
	protected function mapModelToConnecResource($person) 
	{
		 $person_hash = array();
		 		 
		// Save as Customer
		$person_hash['is_customer'] = true;
		
		// Map attributes				
		$person_hash['title'] = $person->id_gender;
		$person_hash['first_name'] = $person->firstname;
		$person_hash['last_name']  = $person->lastname;
		
		$email_hash = array();
		$email_hash['address'] = $person->email;		
		if(!empty($email_hash)) { $person_hash['email'] = $email_hash; }
		
		if($this->is_set($person->birthday)) {
			$person_hash['birth_date'] = $this->format_date_to_connec($person->birthday);
		}
		
		return $person_hash;
		
	}
	
	
	


}
