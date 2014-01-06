<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Customers extends REST_Controller {
    
    /**
     * Customers list
     * @route GET customers all
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
    
    /**
     * Create a new customer
     * @route POST customers/
     */
    public function index_post()
    {
        $token_entry = new Token();
        $token_entry->get_by_valid_token($this->post('token'))->get();
        $response = new stdClass();
        if($token_entry->exists())
        {
            $customer = new Customer();
            $customer->customer_name=$this->post('customer_name');
            if($customer->save())
            {
                $response->status=true;
                $response->last_inserted_id = $customer->id;
            }
            else 
            {
                $response->status=false;
                $response->error='Customer not saved!';
            }
        }
        else 
        {
            $response->status=false;
            $response->error='Token not found or session expired';
        }
        $this->response($response);
    }
    
    /**
     * Delete a customr
     * @route DELETE customer/$id/$token (rewritten by codeigniter route)
     * @param type $id
     * @param type $token
     */
    public function customer_delete($id, $token)
    {
        $token_entry = new Token();
        $token_entry->get_by_valid_token($token)->get();
        $response = new stdClass();
        if($token_entry->exists())
        {
            $customer = new Customer();
            $customer->get_by_id($id);
            $customer->delete();
            $response->status=TRUE;
            $this->response($response);
        }
        else 
        {
            $response->status=FALSE;
            $response->error='Token not found or session expired';
            $this->response($response);
        } 
    }

    
}