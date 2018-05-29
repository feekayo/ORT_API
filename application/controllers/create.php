<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Create extends CI_Controller {

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
    public function index(){
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
    
    //1 // assumes that collection wallpaper will be added after creation
    public function new_music_collection($mykey){
        
        
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

            $this->form_validation->set_rules("title","Title","required|xss_clean|trim"); //validate parameters
            $this->form_validation->set_rules("genre","Genre","required|xss_clean|trim"); //validate parameters
            $this->form_validation->set_rules("user_id","User_id","required|xss_clean|trim"); 
            
            if($this->form_validation->run()){//if all required fields are filled
            
                    $this->load->model('model_accounts');
                    
                    $user_id = $this->input->post('user_id'); 
                    
                    if($this->model_accounts->url_key_is_valid($mykey,$user_id)){//main authentication
                    
                        //load music model
                        $this->load->model("model_music");
                        if($this->model_music->check_collection_title()){
                            
                            $user_id = $this->input->post('user_id');
                            
                            //continue <---- FILE USAGE HERE ---->
                            if(!file_exists("uploads/".$user_id."/collections/".preg_replace('/[^a-z0-9]+/i', '_', $this->input->post('title')))) mkdir("uploads/".$user_id."/collections/".preg_replace('/[^a-z0-9]+/i', '_', $this->input->post('title')));//create collection file 
                            
                            
                            if($this->model_music->new_collection()){//try to create new music collection
                                $response['log'] = "Collection Created";
                                $response['success'] = 1;
                                $response['collection_id'] = $this->model_music->get_collection_id();
                            }else{
                                $response['log'] = "Collection not Created";
                                $response['success'] = 0;                            
                            }
                        
                        }else{// break
                        
                            $response['log'] = "Collection Exists!";
                            $response['success'] = 0;
                        }
                    
                    }else{
                        $response['log'] = "Invalid Url Key";
                        $response['success'] = 0;
                    }
                
            }else{
                $response['log'] = "Incomplete data";
                $response['success'] = 0;
            }
            
            
        } echo json_encode($response);
    
    }
    
    //2
    public function new_pod_collection($mykey){
        
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
        
            $this->form_validation->set_rules("title","Title","required|xss_clean|trim"); //validate parameters
            $this->form_validation->set_rules("genre","Genre","required|xss_clean|trim"); //validate parameters
            $this->form_validation->set_rules("description","Description","required|xss_clean|trim"); //validate parameters
            $this->form_validation->set_rules("user_id","User_id","required|xss_clean|trim"); 
            
            if($this->form_validation->run()){//if all required fields are filled
            
                    $this->load->model('model_accounts');
                    
                    $user_id = $this->input->post('user_id'); 
                    
                    if($this->model_accounts->url_key_is_valid($mykey,$user_id)){//main authentication
                    
                        //load music model
                        $this->load->model("model_pods");
                        if($this->model_pods->check_collection_title()){
                            
                            $user_id = $this->input->post('user_id');
                            
                            //continue <---- FILE USAGE HERE ---->
                            if(!file_exists("uploads/".$user_id."/collections/".preg_replace('/[^a-z0-9]+/i', '_', $this->input->post('title')))) mkdir("uploads/".$user_id."/collections/".preg_replace('/[^a-z0-9]+/i', '_', $this->input->post('title')));//create collection file 
                            
                            
                            if($this->model_pods->new_collection()){//try to create new music collection
                                $response['log'] = "Collection Created";
                                $response['success'] = 1;
                                
                                $response['collection_id'] = $this->model_pods->get_collection_id();
                                
                            }else{
                                $response['log'] = "Collection not Created";
                                $response['success'] = 0;                            
                            }
                        
                        }else{// break
                        
                            $response['log'] = "Collection Exists!";
                            $response['success'] = 0;
                        }
                    
                    }else{
                        $response['log'] = "Invalid Url Key";
                        $response['success'] = 0;
                    }
                
            }else{
                $response['log'] = "Incomplete data";
                $response['success'] = 0;
            }
            
            
        } echo json_encode($response);  
    }
    
    //<!---Uses FILE UPLOAD --!>
    public function upload_track($mykey){
        
        $this->webHeaders();
        
        $response = array();
        if(!isset($mykey) || $mykey==""){
        
            $response['log'] = "bad url";
            $response['success'] = 0;
            
        }else{
        
                $this->load->library('form_validation');
                
                //set validation rules
                if(empty($_FILES['file']['name'])){
                    $this->form_validation->set_rules("file","Audio File", "required|xss_clean|trim");
                }else{
                    $this->form_validation->set_rules("file","Audio File", "xss_clean|trim");
                }
            
                $this->form_validation->set_rules("user_id","User_id","required|xss_clean|trim");
                $this->form_validation->set_rules("collection_id","Collection_id","required|xss_clean|trim");
            
                $collection_id = $this->input->post('collection_id');
                $user_id = $this->input->post('user_id');
                    
                    
                $this->load->model('model_accounts');
                if($this->form_validation->run()== TRUE){
                    
                    //check if user has authentication privilege
                    if($this->model_accounts->url_key_is_valid($mykey,$user_id)){//main validation
                            $this->load->model('model_collection');
                            if(!$this->model_collection->auth_collection_change($collection_id,$user_id)){//
                                        
                                $response['log'] = "User Unauthorized";
                                $response['success'] = 0;
                                    
                            }else{
                                            
                                //get upload path
                                            
                                $path = $this->model_collection->get_collection_path($collection_id);
                                            
                                foreach($path as $row){
                                    $path = $row->collection_directory;
                                }
                                
                                $spath = str_replace(base_url(),"./",$path);
                                $config['upload_path'] = $spath;
                                $config['allowed_types'] = 'mp3';
                                $config['overwrite'] = TRUE;
                                $config['max_size'] = '20480';//change if need be
                                        
                                //loading upload class with audio config preferences
                                $this->load->library('upload');
                                $this->upload->initialize($config);
                                chmod($spath, 0777);
                                if(!$this->upload->do_upload('file')){//if upload is carried out
                                    //$response['writable'] = is_writable($path);
                                    $response['log'] = array('error' => $this->upload->display_errors());
                                    $response['success'] = 0;
                                }else{
                                    $response['log'] = "Track Uploaded";
                                    $response['path'] = $path."/".$_FILES['file']['name'];
                                    $response['success'] = 1;
                                }        
                            }
                    }else{
                        $response['log'] = "Invalid url key";
                        $response['success'] = 0;                    
                    }
            }else{
                $response['log'] = "Incomplete Data";
                $response['success'] = 0;
            }
        } echo json_encode($response);
        
    }
    
    //3
    public function add_track($mykey){
        
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
            $this->form_validation->set_rules("user_id","User_id","required|xss_clean|trim");
            $this->form_validation->set_rules("collection_id","Collection_id","required|xss_clean|trim");
            $this->form_validation->set_rules("title","TrackName","required|xss_clean|trim");
            $this->form_validation->set_rules("file_path","File Path","required|xss_clean|trim");
    
            if($this->form_validation->run()){
                
                
                $this->load->model("model_accounts");
                
                $user_id = $this->input->post('user_id');
                $collection_id = $this->input->post('collection_id');
                
                if($this->model_accounts->url_key_is_valid($mykey,$user_id)){//main validation
                    if($this->model_collection->auth_collection_change($collection_id,$user_id)){//
                    
                        //add track
                        if(!$this->model_collection->check_track_exists($this->input->post('title'),$this->input->post('file_path'),$this->input->post('user_id'))){
                        
                            if($this->model_collection->add_track($collection_id)){
                                $response['log'] = "Track added";
                                $response['success'] = 1;                       
                            }else{
                                $response['log'] = "Operation Failed";
                                $response['success'] = 0;
                            }
                        }else{
                            $response['log'] = "Track Exists";
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
            
                $response['log'] = "Incomplete data";
                $response['success'] = 0;
            }
            
        } echo json_encode($response);
    }
    
    //3
    public function add_following($mykey){
        
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
            $this->form_validation->set_rules("user_id","User_id","required|xss_clean|trim"); 
            $this->form_validation->set_rules("followed_id","Followed_id","required|xss_clean|trim");
            
            if($this->form_validation->run()){
                
                $this->load->model('model_accounts');
                    
                $user_id = $this->input->post('user_id'); 
                    
                if($this->model_accounts->url_key_is_valid($mykey,$user_id)){//main authentication
                    
                    $followed_id = $this->input->post('followed_id');
                    
                    //check for prior following
                    if($this->model_accounts->check_follow_status($followed_id) == FALSE){
                    
                            if($this->model_accounts->model_follow($followed_id)){
                                $response['log'] = "Following";
                                $response['success'] = 1;
                            }else{
                                $response['log'] = "Follow action failed";
                                $response['success'] = 0;                    
                            }
                    }else{
                        $response['log'] = "Priorly followed";
                        $response['success'] = 0;
                    }
                }else{
                    $response['log'] = "Invalid Url key";
                    $response['success'] = 0;
                }                
                
            
            }else{
                $response['log'] = "Incomplete data";
                $response['success'] = 0;
            }
            
        } echo json_encode($response);    
    }


    
    //4
    public function add_live_comment($mykey){
        
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
            $this->form_validation->set_rules("session_user_id","Session_User_id","required|xss_clean|trim");
            
            $this->form_validation->set_rules("profile_user_id","User_id","required|xss_clean|trim");
            
            $this->form_validation->set_rules("comment","Comment","required|xss_clean|trim");
            
            if($this->form_validation->run()){
                $session_user_id = $this->input->post('session_user_id');
                
                $this->load->model("model_accounts");
                if($this->model_accounts->url_key_is_valid($mykey,$session_user_id)){//main authentication
                    
                    $profile_user_id = $this->input->post('profile_user_id');
                    
                    if($this->model_accounts->add_comment_live($profile_user_id)){
                    
                        $response['log'] = "Comment Success";
                        $response['success'] = 1;
                        
                    }else{
                        
                        $response['log'] = "Comment Failed";
                        $response['success'] = 0;
                    
                    }

                }else{
                    $response['log'] = "Invalid user key";
                    $response['success'] = 0;
                }
            }else{
                $response['log'] = "Incomplete data";
                $response['success'] = 0;
            }
            
            
            
            
            
        } echo json_encode($response);    
    }
    

    
    //6
    public function add_track_comment($mykey){
        
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
            $this->form_validation->set_rules("user_id","User_id","required|xss_clean|trim");
            
            $this->form_validation->set_rules("track_id","track_id","required|xss_clean|trim");
            
            $this->form_validation->set_rules("comment","Comment","required|xss_clean|trim");
            
            if($this->form_validation->run()){
                $user_id = $this->input->post('user_id');
                
                $this->load->model("model_accounts");
                if($this->model_accounts->url_key_is_valid($mykey,$user_id)){//main authentication
                    
                    $track_id = $this->input->post('track_id');
                    
                    if($this->model_accounts->add_comment_track($track_id)){
                    
                        $response['log'] = "Comment Success";
                        $response['success'] = 1;
                        
                    }else{
                        
                        $response['log'] = "Comment Success";
                        $response['success'] = 0;
                    
                    }

                }else{
                    $response['log'] = "Invalid url key";
                    $response['success'] = 0;
                }
            }else{
                $response['log'] = "Incomplete data";
                $response['success'] = 0;
            }
            
            
            
            
            
        } echo json_encode($response);    
    }
    

    
    //8
    public function add_track_like($mykey){
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
            $this->form_validation->set_rules("user_id","User_id","required|xss_clean|trim"); 
            $this->form_validation->set_rules("track_id","Track_id","required|xss_clean|trim");
            
            if($this->form_validation->run()){
                    $this->load->model('model_accounts');
                    
                    $user_id = $this->input->post('user_id'); 
                    
                    if($this->model_accounts->url_key_is_valid($mykey,$user_id)){//main authentication            
    
                        //load collection model
                        $this->load->model('model_collection');
                        
                        $track_id = $this->input->post('track_id');
                        
                        if($this->model_collection->check_like_status($track_id) == TRUE){
                            $response['log'] = "Priorly Liked";
                            $response['success'] = 0;
                        }else{
                            if($this->model_collection->like_track_action($track_id)){
                                $response['log'] = "Liked";
                                $response['success'] = 1; 
                            }else{
                                $response['log'] = "Like action failed";
                                $response['success'] = 0;
                            }
                        }
                    }else{
                        $response['log'] = "Invalid user key";
                        $response['success'] = 0;
                    }
                
            }else{
            
                $response['log'] = "Incomplete data";
                $response['success'] = 0;
                
            }
            
        } echo json_encode($response);    
    }
    
    //9
    public function add_podcast_subscribe($mykey){
        
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
            $this->form_validation->set_rules("user_id","User_id","required|xss_clean|trim"); 
            $this->form_validation->set_rules("collection_id","Collection_id","required|xss_clean|trim");
            
            if($this->form_validation->run()){//if all required fields are filled
            
                    $this->load->model('model_accounts');
                    
                    $user_id = $this->input->post('user_id'); 
                    
                    if($this->model_accounts->url_key_is_valid($mykey,$user_id)){//main authentication            
    
                        //load collection model
                        $this->load->model('model_collection');
                        
                        $collection_id = $this->input->post('collection_id');
                        if($this->model_collection->check_subscribe_status($collection_id) == TRUE){//check if user aint priorly subscribed
                            $response['log'] = "Priorly Subscribed";
                            $response['success'] = 0;
                        }else{
                            
                            if($this->model_collection->subscribe_collection_action($collection_id)){
                                $response['log'] = "Subscribed";
                                $response['success'] = 1;
                            }else{
                                $response['log'] = "Subscription Failed";
                                $response['success'] = 0;
                            
                            }
                        
                        }
                        
                    }else{
                        $response['log'] = "Invalid url Key";
                        $response['success'] = 0;
                    
                    }        
            }else{
            
                $response['log'] = "Incomplete data";
                $response["success"] = 0;
            }
            
            
        } echo json_encode($response);    
    }
    

    public function add_playlist($mykey){
        
        $this->webHeaders();
        
        $response = array();
        if(!isset($mykey) || $mykey==""){//primary valiadation
            $response['log'] = "bad url";
            $response['success'] = 0;
        }else{
            //load form validation library
            $this->load->library("form_validation");
            //validation rules
            $this->form_validation->set_rules("user_id","User_id","required|xss_clean|trim"); 
            $this->form_validation->set_rules("playlist_name","Playlist_name","required|xss_clean|trim");              
            $this->form_validation->set_rules("description","description","required|xss_clean|trim");

            if($this->form_validation->run()){
                
                $this->load->model('model_accounts');
                if($this->model_accounts->url_key_is_valid($mykey,$this->input->post('user_id'))){
                    $this->load->model('model_collection');

                    $user_id = $this->input->post('user_id');
                    $name = $this->input->post('playlist_name');
                    $description = $this->input->post('description');

                    if(!($this->model_collection->check_playlist_name($name,$user_id))){

                        if($this->model_collection->add_playlist($user_id,$name,$description)){
                            $response['log'] = "Playlist Created";
                            $response['success'] = 1;
                            $response['playlist_id'] = $this->model_collection->get_playlist_id($name,$user_id);
                        }else{
                            $response['log'] = "Couldn't create playlist";
                            $response['success'] = 0;
                        }

                    }else{
                        $response['log'] = "Playlist with name exists";
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
            
            
        }echo json_encode($response);        
        
    }
    
    
    public function add_to_playlist($mykey){
      
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
                        if($this->model_collection->check_playlist($this->input->post('track_id'),$this->input->post('play_id'))){

                            if($this->model_collection->add_to_playlist()){

                                $response['log'] = "Playlist Updated";
                                $response['success'] = 1;

                            }else{

                                $response['log'] = "Addition Failed";
                                $response['success'] = 0;

                            }

                        }else{

                            $response['log'] = "Proirly added";
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
            
            
        } echo json_encode($response);        
    }
    
    public function add_voice_note($mykey){
        
        $this->webHeaders();
        $response = array();
        
        if(!isset($mykey) || $mykey==""){//primary valiadation
            $response['log'] = "bad url";
            $response['success'] = 0;
        }else{
            
            $this->load->library("form_validation");
            
            //set validation rules;
            $this->form_validation->set_rules("user_id","User_id","required|xss_clean|trim");
            $this->form_validation->set_rules("caption","Caption","required|xss_clean|trim");
            $this->form_validation->set_rules("reply_to","Reply_to","xss_clean|trim");    
            $this->form_validation->set_rules("audio","Audio","required|xss_clean");
            $this->form_validation->set_rules("filename","Filename","required|xss_clean|trim");
            if($this->form_validation->run()){
                     
                $this->load->model("model_accounts");
                if($this->model_accounts->url_key_is_valid($mykey,$this->input->post('user_id'))){//main authentication 
                      
                      //$path = base_url().'uploads/'.$this->input->post('user_id')."/voices";
                      
                    $caption = $this->input->post('caption');
                    $user_id = $this->input->post('user_id');
                    $reply_to = $this->input->post('reply_to');
                    $audio = $this->input->post('audio');
                    $filename = $this->input->post('filename');
                    
                    $data = substr($audio,strpos($audio,",")+1);
                    
                    $decodedData = base64_decode($data);
                    
                    $track = base_url().'uploads/'.$user_id."/voices/".$filename;
                    
                    $fp = fopen($track, 'wb');
                    fwrite($fp, $decodedData);
                    fclose($fp);
                    
                    if($this->model_collection->add_voice_note()){

                        $response['log'] = "Success";
                        $response['success'] = 1;
                        
                    }else{
                        
                        $response['log'] = "Operation Failed";
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
    
    public function add_voice_note_like($mykey){
        
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
                    if(!($this->model_collection->get_voice_like_exists())){
                        if($this->model_collection->like_voice_note()){
                            $response['log'] = "Liked";
                            $response['success'] = 1;
                        }else{
                            $response['log'] = "Operation Failed";
                            $response['success'] = 0;
                        }
                    }else{
                        $response['log'] = "Priorly Liked";
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
    
    public function add_voice_note_listen($mykey){
        
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
                    if($this->model_collection->listen_voice_note()){
                        $response['log'] = "Listened";
                        $response['success'] = 1;
                    }else{
                        $response['log'] = "Operation Failed";
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
    
} 