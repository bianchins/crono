<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Timer extends REST_Controller {
    
    public function index_post()
    {
        $token_entry = new Token();
        $token_entry->get_by_valid_token($this->post('token'))->get();
        $response = new stdClass();
        if($token_entry->exists())
        {
            $timer_entry = new Timer_entry();
            $timer_entry->task=$this->post('task');
            $timer_entry->start_time = time();
            $timer_entry->stop_time = 0;
            $timer_entry->project_id = $this->post('project_id');
            $timer_entry->user_id = $token_entry->user_id;
            $timer_entry->active=1;
            if($timer_entry->save())
            {
                $response->status=true;
                $response->last_inserted_id = $timer_entry->id;
            }
            else 
            {
                $response->status=false;
                $response->error='Timer not started!';
            }
        }
        else 
        {
            $response->status=false;
            $response->error='Token not found or session expired';
        }
        $this->response($response);
    }
    
    public function active_get($token)
    {
        $token_entry = new Token();
        $token_entry->get_by_valid_token($token)->get();
        $response = new stdClass();
        if($token_entry->exists())
        {
            //TODO
            $timer_entries = new Timer_entry();
            //Selecting the entry
            $timer_entries->where('user_id', $token_entry->user->get()->id)->where('active',1)->get();
            if($timer_entries->exists())
            {
                $response->status = true;
                $timer = new stdClass();
                $timer->id = $timer_entries->id;
                $timer->task = $timer_entries->task;
                $timer->start_time = $timer_entries->start_time;
                $timer->project_id = $timer_entries->project_id;
                $timer->project_name = $timer_entries->project->get()->name;
                $response->active_timer = $timer;
            }
            else 
            {
                $response->status = true;
                $response->active_timer = null;
            }
        }
        else 
        {
            $response->status=false;
            $response->error='Token not found or session expired';
        }
        $this->response($response);
    }
    
    public function last_get($token)
    {
        $token_entry = new Token();
        $token_entry->get_by_valid_token($token)->get();
        if($token_entry->exists())
        {
            $response = [];
            $timer_entries = new Timer_entry();
            $timer_entries->where('user_id', $token_entry->user->get()->id);
            //Only not active time entries, order by stop time
            $timer_entries->where('active',0)->order_by('stop_time','DESC')->limit(5)->get();
            foreach($timer_entries as $timer_entry)
            {
                $t = new stdClass();
                $t->id = $timer_entry->id;
                $t->project_name = $timer_entry->project->get()->name;
                $t->project_id = $timer_entry->project_id;
                $t->task = $timer_entry->task;
                $t->start_time = $timer_entry->start_time;
                $t->stop_time = $timer_entry->stop_time;
                $t->duration = from_unix_timespan_to_string($timer_entry->start_time,$timer_entry->stop_time);
                array_push($response, $t);
            }
            $this->response($response);
        }
    }
    
    public function all_get($only_current_user, $token)
    {
        $token_entry = new Token();
        $token_entry->get_by_valid_token($token)->get();
        if($token_entry->exists())
        {
            $response = [];
            $timer_entries = new Timer_entry();
            //Does it show only current user?
            if($only_current_user) 
            {
                $timer_entries->where('user_id', $token_entry->user->get()->id);
            }
            //Only not active time entries, order by stop time
            $timer_entries->where('active',0)->order_by('stop_time','DESC')->get();
            foreach($timer_entries as $timer_entry)
            {
                $t = new stdClass();
                $t->id = $timer_entry->id;
                $t->project_name = $timer_entry->project->get()->name;
                $t->project_id = $timer_entry->project_id;
                $t->task = $timer_entry->task;
                $t->start_time = $timer_entry->start_time;
                $t->stop_time = $timer_entry->stop_time;
                $t->duration = from_unix_timespan_to_string($timer_entry->start_time,$timer_entry->stop_time);
                array_push($response, $t);
            }
            $this->response($response);
        }
    }
    
    public function index_put()
    {
        $token_entry = new Token();
        $token_entry->get_by_valid_token($this->put('token'))->get();
        $response = new stdClass();
        if($token_entry->exists())
        {
            $timer_entry = new Timer_entry();
            $timer_entry->where('id',$this->put('id'))->where('user_id',$token_entry->user_id)->get();
            if($timer_entry->exists()) 
            {
                $timer_entry->stop_time = time();
                $timer_entry->active = $this->put('active');
                $timer_entry->task=$this->put('task');
                $timer_entry->project_id = $this->put('project_id');
                $timer_entry->save();
                $response->status=true;
            }
            else 
            {
                //TODO
            }
        }
        else 
        {
            $response->status=false;
            $response->error='Token not found or session expired';
        }
        $this->response($response);
    }
    
    public function delete_delete($id, $token)
    {
        
    }
    
    public function weekTotal_get($token)
    {
        //YEARWEEK(FROM_UNIXTIME(stop_time)) = YEARWEEK(CURRENT_DATE)
        $token_entry = new Token();
        $token_entry->get_by_valid_token($token)->get();
        $response = new stdClass();
        if(true)//if($token_entry->exists())
        {
            //TODO
            $timer_entries = new Timer_entry();
            //Selecting the entry
            $timer_entries->getThisWeek()->where('active',0)->select_sum('(stop_time - start_time)','totalTime')->get();
            $response->status = true;
            $response->totalThisWeek = 0;
            if($timer_entries->exists())
            {
                if(!$timer_entries->totalTime) return from_unix_timespan_to_string(0);
                $response->totalThisWeek = from_unix_timespan_to_string($timer_entries->totalTime);
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

