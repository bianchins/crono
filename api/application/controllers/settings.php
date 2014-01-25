<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Settings extends REST_Controller {
    
    public function all_get($token) {
        $token_entry = new Token();
        $token_entry->get_by_valid_token($token)->get();
        if($token_entry->exists())
        {
            $settings = new Setting();
            $settings->get();
            $response = new stdClass();
            $response->status=true;
            $response->settings = new stdClass();
            foreach($settings as $setting) 
            {
               $response->settings->{$setting->setting_key} = $setting->setting_value;
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

