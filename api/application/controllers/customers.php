<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Customers extends REST_Controller {
    
    /**
     * Projects list
     * @route GET projects all
     * @param type $token
     */
    public function all_get($token) 
    {
        $token_entry = new Token();
        $token_entry->get_by_valid_token($token)->get();
        if($token_entry->exists())
        {
            $customers = new Customer();
            $customers->order_by('customer_name', 'ASC');
            $customers->get();
            $response = [];
            foreach($customers as $customer) 
            {
               $p = new stdClass();
               $p->id = $customer->id;
               $p->customer_name = $customer->customer_name;
               array_push($response, $p);
            }
            $this->response($response);
        }
        else 
        {
            $response = new stdClass();
            $response->status=false;
            $response->error='Token not found or session expired';
            $this->response($response);
        }
    }
    
    
}