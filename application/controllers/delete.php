<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Delete extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		//echo "Hi Internet ";
	   
    }
     public function webHeaders(){
        
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    
        if(empty($_POST)){
            $_POST = json_decode(file_get_contents("php://input"),true);
        }
        
    }     
   
    //remove from playlist
    
    public function delete_from_playlist($mykey){
        
        $this->webHeaders();
        $response = array();
        if(!isset($mykey) || $mykey==""){//primary valiadation
            $response['log'] = "bad url";
            $response['success'] = 0;
        }else{
        
            //load collection model
            $this->load->model("model_collection");
            
            //load form validation library
            $this->load->library("form_validation");
            
                        
            //validation rules
            $this->form_validation->set_rules("play_id","Play_id","required|xss_clean|trim"); 
            $this->form_validation->set_rules("user_id","User_id","required|xss_clean|trim"); 
            $this->form_validation->set_rules("track_id","Track_id","required|xss_clean|trim");            
            
            if($this->form_validation->run()){
            
                $this->load->model("model_accounts");
                
                if($this->model_accounts->url_key_is_valid($mykey,$this->input->post('user_id'))){//main authentication 
                
                    if($this->model_collection->check_playlist_owner($this->input->post('play_id'),$this->input->post('user_id'))){
                    
                        if($this->model_collection->remove_from_playlist($this->input->post('track_id'),$this->input->post('play_id'))){
                        
                            $response['log'] = "Track Removed";
                            $response['success'] = 1;
                        
                        }else{
                        
                            $response['log'] = "Operation Failed";
                            $response['success'] = 0;
                        
                        }
                        
                    }else{
                    
                        $response['log'] = "User unauthorized";
                        $response['success'] = 0;
                    
                    }
                    
                    
                }else{
                    $response['log'] = "Invalid Url Key";
                    $response['success'] = 0;                    
                }
            
            }else{
            
                $response['log'] = "Incomplete Data";
                $response['success'] = 0;
            }
            
            
        } echo json_encode($response);  
    
    }
    
    public function delete_playlist($mykey){
        
        $this->webHeaders();
        $response = array();
        if(!isset($mykey) || $mykey==""){//primary valiadation
           $response['log'] = "bad url";
           $response['success'] = 0;
        }else{   
            
            $this->load->library("form_validation");
            
            $this->form_validation->set_rules("user_id","User_id","required|xss_clean|trim");
            $this->form_validation->set_rules("play_id","Play_id","required|xss_clean|trim");
            
            if($this->form_validation->run()){
                
                $this->load->model("model_accounts");
                
                if($this->model_accounts->url_key_is_valid($mykey,$this->input->post('user_id'))){//main authentication 
                    
                    $this->load->model("model_collection");
                    if($this->model_collection->check_playlist_owner($this->input->post('play_id'),$this->input->post('user_id'))){
                        
                        if($this->model_collection->delete_playlist($this->input->post('play_id'))){
                            
                            $response['log'] = "Playlist Deleted";
                            $response['success'] = 1;
                            
                        }else{
                            $response['log'] = "Operation Failed";
                            $response['success'] = 0;
                        }
                        
                    }else{
                        $response['log'] = "User Unauthorized";
                        $response['success'] = 0;
                    }
                    
                }else{
                    $response['log'] = "Invalid url key";
                    $response['success'] = 0;
                }                
                
            }else{
                $response['log'] = "Incomplete Data";
                $response['success'] = 0;
            }
        }
        echo json_encode($response);
        
    }

    public function delete_following($mykey){
                 
        $this->webHeaders();
        $response = array();
        if(!isset($mykey) || $mykey==""){//primary valiadation
           $response['log'] = "bad url";
           $response['success'] = 0;
        }else{   
            
            $this->load->library("form_validation");
            
            $this->form_validation->set_rules("user_id","User_id","required|xss_clean|trim");
            $this->form_validation->set_rules("followed_id","Followed_id","required|xss_clean|trim");
            
            if($this->form_validation->run()){
                
                $this->load->model("model_accounts");
                
                if($this->model_accounts->url_key_is_valid($mykey,$this->input->post('user_id'))){//main authentication 
                    
                    if($this->model_accounts->check_follow_status($this->input->post('followed_id'))){
                        
                        if($this->model_accounts->model_unfollow($this->input->post('followed_id'))){
                            $response['log'] = "User Unfollowed";
                            $response['success'] = 1;
                        }else{
                            $response['log'] =  "Operation Failed";
                            $response['success'] = 0;
                        }
                    }else{
                            $response['log'] = "Not priorly followed";
                            $response['success'] = 0;
                    }
                    
                }else{
                    $response['log'] = "Invalid url key";
                    $response['success'] = 0;
                }                
                
            }else{
                $response['log'] = "Incomplete Data";
                $response['success'] = 0;
            }
        }
        echo json_encode($response);
        
    }
    
    public function delete_like($mykey){

        $this->webHeaders();
        $response = array();
        if(!isset($mykey) || $mykey==""){//primary valiadation
           $response['log'] = "bad url";
           $response['success'] = 0;
        }else{   
            
            $this->load->library("form_validation");
            
            $this->form_validation->set_rules("user_id","User_id","required|xss_clean|trim");
            $this->form_validation->set_rules("track_id","Track_id","required|xss_clean|trim");
            
            if($this->form_validation->run()){
                
                $this->load->model("model_accounts");
                
                if($this->model_accounts->url_key_is_valid($mykey,$this->input->post('user_id'))){//main authentication 
                    
                    $this->load->model("model_collection");
                    if($this->model_collection->check_like_status($this->input->post('track_id'))){
                        
                        if($this->model_collection->unlike_track_action($this->input->post('track_id'))){
                            $response['log'] = "Track Unliked";
                            $response['success'] = 1;
                        }else{
                            $response['log'] = "Operation Failed";
                            $response['success'] = 0;
                        }
                        
                    }else{
                        $response['log'] = "Not Priorly Liked";
                        $response['success'] = 0;
                    }
                    
                }else{
                    $response['log'] = "Invalid url key";
                    $response['success'] = 0;
                }                
                
            }else{
                $response['log'] = "Incomplete Data";
                $response['success'] = 0;
            }
        }
        echo json_encode($response);
        
    }    
    
    public function delete_subscription($mykey){
       
        $this->webHeaders();
        $response = array();
        if(!isset($mykey) || $mykey==""){//primary valiadation
           $response['log'] = "bad url";
           $response['success'] = 0;
        }else{   
            
            $this->load->library("form_validation");
            
            $this->form_validation->set_rules("user_id","User_id","required|xss_clean|trim");
            $this->form_validation->set_rules("collection_id","Collection_id","required|xss_clean|trim");
            if($this->form_validation->run()){
                
                $this->load->model("model_accounts");
                
                if($this->model_accounts->url_key_is_valid($mykey,$this->input->post('user_id'))){//main authentication 
                    $this->load->model("model_collection");
                    if($this->model_collection->check_subscribe_status($this->input->post('collection_id'))){
                        
                        if($this->model_collection->unsubscribe_collection_action($this->input->post('collection_id'))){
                            $response['log'] = "Subscription Deleted";
                            $response['success'] = 1;
                        }else{
                            $response['log'] = "Operation Failed";
                            $response['success'] = 0;
                        }
                        
                    }else{
                        $response['log'] = "Not Priorly Subscribed";
                        $response['success'] = 0;
                    }                    
                    
                }else{
                    $response['log'] = "Invalid url key";
                    $response['success'] = 0;
                }                
                
            }else{
                $response['log'] = "Incomplete Data";
                $response['success'] = 0;
            }
        }
        echo json_encode($response);
        
    }   
    
    public function delete_voice_note($mykey){
        
        $this->webHeaders();
        $response = array();
        
        if(!isset($mykey) || $mykey==""){//primary valiadation
            $response['log'] = "bad url";
            $response['success'] = 0;
        }else{
            $this->load->library("form_validation");
            
            $this->form_validation->set_rules("user_id","","required|xss_clean|trim");
            $this->form_validation->set_rules("post_id","","required|xss_clean|trim");
            
            if($this->form_validation->run()){
                
                $this->load->model("model_accounts");
                if($this->model_accounts->url_key_is_valid($mykey,$this->input->post('user_id'))){//main authentication 
                    
                    $this->load->model("model_collection");
                    if($this->model_collection->user_owns_voice()){
                        if($this->model_collection->delete_voice()){

                            $response['log'] = "Deleted";
                            $response['success'] = 1;

                        }else{
                            $response['log'] = "Operation Failed";
                            $response['successs'] = 0;
                        }
                    }else{
                        $response['log'] = "User Unauthorized";
                        $response['success'] = 0;
                    }
                }else{
                    $response['log'] = "Invalid url key";
                    $response['success'] = 0;
                }
                
            }else{
                   $response['log'] = "Incomplete Data";
                   $response['success'] = 0;
            }
            
        }
        echo json_encode($response); 
    }
    
    public function delete_track($mykey){
        
        $this->webHeaders();
        $response = array();
        
        if(!isset($mykey) || $mykey==""){//primary valiadation
            $response['log'] = "bad url";
            $response['success'] = 0;
        }else{
            $this->load->library("form_validation");
            $this->form_validation->set_rules("user_id","","required|xss_clean|trim");
            $this->form_validation->set_rules("track_id","","required|xss_clean|trim");
            
            if($this->form_validation->run()){
                
                $user_id = $this->input->post('user_id');
                
                $this->load->model("model_accounts");
                //url verification
                if($this->model_accounts->url_key_is_valid($mykey,$user_id)){
                    
                    $this->load->model("model_collection");
                    //user authorization
                    if($this->model_collection->user_owns_tracks()){
                        
                        if($this->model_collection->delete_track()){
                            $response['log'] = "Track Deleted";
                            $response['success'] = 1;
                            
                        }else{
                            $response['log'] = "Operation Failed";
                            $response['success'] = 0;
                        }
                        
                    }else{
                        $response['log'] = "User Unauthorized";
                        $response['success'] = 0;
                        
                    }   
                    
                    
                }else{
                    $response['log'] = "Invalid url key";
                    $response['success'] = 0;
                    
                }
            }else{
                $response['log'] = "Incomplete Data";
                $response['success'] = 0;
                
            }
        }       
        echo json_encode($response); 
    }
    
    public function unlike_voice_note($mykey){
        
        $this->webHeaders();
        $response = array();
        
        if(!isset($mykey) || $mykey==""){//primary valiadation
            $response['log'] = "bad url";
            $response['success'] = 0;
        }else{
            $this->load->library("form_validation");
            
            $this->form_validation->set_rules("user_id","","required|xss_clean|trim");
            $this->form_validation->set_rules("post_id","","required|xss_clean|trim");
            
            if($this->form_validation->run()){
                
                $this->load->model("model_accounts");
                if($this->model_accounts->url_key_is_valid($mykey,$this->input->post('user_id'))){//main authentication 
                    $this->load->model("model_collection");
                    if($this->model_collection->get_voice_like_exists()){
                        if($this->model_collection->unlike_voice_note()){
                            $response['log'] = "Un Liked";
                            $response['success'] = 1;
                        }else{
                            $response['log'] = "Operation Failed";
                            $response['success'] = 0;
                        }
                    }else{
                        $response['log'] = "Not Priorly Liked";
                        $response['success'] = 0;
                    }
                }else{
                    $response['log'] = "Invalid Url Key";
                    $response['success'] = 0;
                }
            }else{
                $response['log'] = "Incomplete Data";
                $response['success'] = 0;
            }
        }        
        echo json_encode($response);        
        
    }
    
    public function delete_comment($mykey){
        
        $this->webHeaders();
        $response = array();
        
        if(!isset($mykey) || $mykey==""){//primary valiadation
            $response['log'] = "bad url";
            $response['success'] = 0;
        }else{
            $this->load->library("form_validation");
            
            $this->form_validation->set_rules("user_id","","required|xss_clean|trim");
            $this->form_validation->set_rules("comment_id","","required|xss_clean|trim");
            $this->form_validation->set_rules("track_id","","required|xss_clean|trim");
            
            if($this->form_validation->run()){
                
                $this->load->model("model_accounts");
                if($this->model_accounts->url_key_is_valid($mykey,$this->input->post('user_id'))){//main authentication 
                    
                    if($this->model_accounts->user_owns_comment($this->input->post('user_id'),$this->input->post('comment_id'))){
                        if($this->model_accounts->delete_comment($this->input->post('user_id'),$this->input->post('comment_id'),$this->input->post('track_id'))){
                            $response['log'] = "Deleted";
                            $response['success'] = 1;
                        }else{
                            $response['log'] = "Operation Failed";
                            $response['success'] = 0;
                        }
                        
                    }else{
                        $response['log'] = "User Unauthorized";
                        $response['success'] = 0;
                       
                    }
                }else{
                    $response['log'] = "Invalid Url Key";
                    $response['success'] = 0;
                }   
                
            }else{
                $response['log'] = "Incomplete Data";
                $response['success'] = 0;
                
            }   
        }
        echo json_encode($response);
    }
    
    public function delete_collection($mykey){
         
        $this->webHeaders();
        $response = array();
        
        if(!isset($mykey) || $mykey==""){//primary valiadation
            $response['log'] = "bad url";
            $response['success'] = 0;
        }else{
            $this->load->library("form_validation");
            
            $this->form_validation->set_rules("collection_id","","required|xss_clean|trim");
            $this->form_validation->set_rules("user_id","","required|xss_clean|trim");
            
            if($this->form_validation->run()){
            
                $this->load->model("model_accounts");
                if($this->model_accounts->url_key_is_valid($mykey,$this->input->post('user_id'))){//main authentication 
                     $this->load->model('model_collection');
                    $collection_id = $this->input->post('collection_id');
                    $user_id = $this->input->post('user_id');                    
                    if($this->model_collection->auth_collection_change($collection_id,$user_id)){//
                        if($this->model_collection->delete_collection($collection_id)){
                            $response['log'] = "Collection Deleted";
                            $response['success'] = 1;
                        }else{
                            $response['log'] = "Operation Failed";
                            $response['success'] = 0;
                        }
                    }else{
                        $response['log'] = "User Unauthorized";
                        $response['success'] = 0;
                    }
                }else{
                    $response['log'] = "Invalid url key";
                    $response['success'] = 0;
                }
            }else{
                $response['log'] = "Incomplete Data";
                $response['success'] = 0;
            }
                
        }
        echo json_encode($response);
    }
}