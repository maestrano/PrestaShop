<?php

/**
* Map Connec Customer Person representation to/from PrestaShop Contact
*/

class CustomerMapper extends BaseMapper {

  public function __construct() {
    parent::__construct();

    $this->connec_entity_name      = 'Customer';
    $this->local_entity_name       = 'Customer';
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
    return new Customer($local_id);
  }
  
  protected function mapConnecResourceToModel($person_hash, $person)
  {
    // Map hash attributes to Person
    if(array_key_exists('title', $person_hash)) {
      if($person_hash['title'] == 'MRS') { $person->id_gender = 2; } else { $person->id_gender = 1; }
    }
    if(array_key_exists('first_name', $person_hash)) { $person->firstname = $person_hash['first_name']; }
    if(array_key_exists('last_name', $person_hash)) { $person->lastname = $person_hash['last_name']; }
    if(array_key_exists('email', $person_hash) && array_key_exists('address', $person_hash['email'])) { $person->email = $person_hash['email']['address']; }
    if(array_key_exists('birth_date', $person_hash)) { $person->birthday = $this->format_date_to_php($person_hash['birth_date']); }
    else { $person->birthday = '1970-01-01'; }

    if(!$this->is_set($person->passwd)) { $person->setWsPasswd(uniqid()); }
  }
  
  // Map the Prestashop Customer to a Connec resource hash
  protected function mapModelToConnecResource($person)
  {
    $person_hash = array();

    // Save as Customer
    $person_hash['is_customer'] = true;

    // Map attributes    
    if($person->id_gender=='1') {$gender = "MR";} else {$gender = "MRS";}    
    $person_hash['title'] = $gender;
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
