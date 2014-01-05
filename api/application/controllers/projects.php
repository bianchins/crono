<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Projects extends REST_Controller {
    
    /**
     * Project information
     * @route GET project/$id/$token (rewritten by codeigniter route)
     * @param type $id
     * @param type $token
     */
    public function project_get($id, $token) 
    {
        $token_entry = new Token();
        $token_entry->get_by_valid_token($token)->get();
        $response = new stdClass();
        if($token_entry->exists())
        {
            $projects = new Project();
            $projects->get_by_id($id);
            //TODO
            $p = new stdClass();
            $p->id = $project->id;
            $p->name = $project->name;
            $p->customer_name = $project->Customer->get()->customer_name;
            if(!$p->customer_name) $p->customer_name='-';
            $p->closed = ($project->closed) ? TRUE : FALSE;
            $p->gitlab_project_id = $project->gitlab_project_id;
            $response->status = true;
            $response->project = $p;
            $this->response($response);
        }
        else 
        {
            $response->status=false;
            $response->error='Token not found or session expired';
            $this->response($response);
        }   
    }
    
    /**
     * Delete a project
     * @route DELETE project/$id/$token (rewritten by codeigniter route)
     * @param type $id
     * @param type $token
     */
    public function project_delete($id, $token)
    {
        $token_entry = new Token();
        $token_entry->get_by_valid_token($token)->get();
        $response = new stdClass();
        if($token_entry->exists())
        {
            $projects = new Project();
            $projects->get_by_id($id);
            $projects->delete();
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
            $projects = new Project();
            $projects->order_by('name', 'ASC');
            $projects->get();
            $response = [];
            foreach($projects as $project) 
            {
               $p = new stdClass();
               $p->id = $project->id;
               $p->name = $project->name;
               $p->customer_name = $project->Customer->get()->customer_name;
               if(!$p->customer_name) $p->customer_name='-';
               $p->closed = ($project->closed) ? TRUE : FALSE;
               $p->gitlab_project_id = $project->gitlab_project_id;
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
     * Create a new project
     * @route POST projects/
     */
    public function index_post()
    {
        $token_entry = new Token();
        $token_entry->get_by_valid_token($this->post('token'))->get();
        $response = new stdClass();
        if($token_entry->exists())
        {
            $project = new Project();
            $project->name=$this->post('name');
            $project->customer_id=$this->post('customer_id');
            if($project->save())
            {
                $response->status=true;
            }
            else 
            {
                $response->status=false;
                $response->error='Project not saved!';
            }
        }
        else 
        {
            $response->status=false;
            $response->error='Token not found or session expired';
        }
        $this->response($response);
    }
}

