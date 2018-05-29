<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Update extends CI_Controller {

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
    
    public function update_status($mykey){

        $this->webHeaders();
        $response = array();//json response
        if(!isset($mykey) || $mykey==""){//primary url validation
            $response['log'] = "bad url";
            $response['success'] = 0;
        }else{
            $this->load->model('model_accounts');
            
            $this->load->library("form_validation");

            $this->form_validation->set_rules("update","Status","required|xss_clean|trim"); //validate parameters
            $this->form_validation->set_rules("user_id","User_id","required|xss_clean|trim");         
            
            if($this->form_validation->run()){
                $user_id = $this->input->post('user_id');
                    
                if($this->model_accounts->url_key_is_valid($mykey,$user_id)){//main validation
                    
                    if($this->model_accounts->model_update_status($user_id)){//update status
                        $response['log'] = "Status Updated";
                        $response['success'] = 1;
                    }else{
                        $response['log'] = "Status Update failed";
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
        
        }
        echo json_encode($response);
    }
    
    
    public function update_profile_wallpaper($mykey){
        
        $this->webHeaders();
        $response = array(); //json response
        
        if(!isset($mykey) || $mykey==""){
            $response['log'] = "bad url";
            $response['success'] = 0;        
        }else{
            
            $this->load->library("form_validation");

            $this->form_validation->set_rules("user_id","User_id","required|xss_clean|trim");//validate url         
            
            if($this->form_validation->run()){
            
                    if(empty($_FILES['wallpaper']['name'])){//check if file is null
                        $response['log'] = "Null file";
                        $response['success'] = 0;
                    }else{
                        
                        //load user model
                        $this->load->model('model_accounts');
                        $user_id = $this->input->post('user_id');
                        if($this->model_accounts->url_key_is_valid($mykey,$user_id)){//main validation
                            //setting picture configurations
                            $config['upload_path'] = './uploads/'.$user_id."/wallpapers";
                            $config['allowed_types'] = 'gif|jpg|png|jpeg';
                            $config['overwrite'] = TRUE;
                            $config['max_size'] = '4096';

                            //loading upload class with picture config preferences
                            $this->load->library('upload', $config);
                            if(! $this->upload->do_upload('wallpaper')){//if upload is carried out
                                  
                                $response['log'] = "Invalid Type or Size";
                                $response['success'] = 0;
                                //echo $this->upload->display_errors();
                                    //echo $config['upload_path'];
                            }else{
                                    
                                if($this->model_accounts->change_wallpaper($user_id)){// check if news is added to db 
                                    $response['log'] = "Wallpaper Changed";
                                    $response['success'] = 1;    
                                }else{
                                
                                    $response['log'] = "Could not Update";
                                    $response['success'] = 0;    
                                }
                                                        
                            }
                        }else{
                            $response['log'] = "Invalid url key";
                            $response['success'] = 0;
                        }
                    }
            }else{
                        $response['log'] = "Incomplete data";
                        $response['success'] = 0;            
            }
            
        }
        
        echo json_encode($response);
    
    }
    
    public function update_collection_wallpaper($mykey){
        
        $this->webHeaders();
        $response = array(); //json response
        
        if(!isset($mykey) || $mykey==""){
            $response['log'] = "bad url";
            $response['success'] = 0;        
        }else{
            
            $this->load->library("form_validation");    
            $this->form_validation->set_rules("user_id","User_id","required|xss_clean|trim");//validate url      
            $this->form_validation->set_rules("collection_id","Collection_id","required|xss_clean|trim");
            
            if($this->form_validation->run() == FALSE){
                $response['log'] = "Incomplete data";
                $response['success'] = 0;                
            }else{
                if(empty($_FILES['wallpaper']['name'])){//check if file is null
                    $response['log'] = "Null file";
                    $response['success'] = 0;
                }else{
                    
                    $this->load->model('model_accounts');
                    
                    $user_id = $this->input->post('user_id'); 
                    
                    if($this->model_accounts->url_key_is_valid($mykey,$user_id)){//main authentication
                        $this->load->model('model_collection');
                        $collection_id = $this->input->post('collection_id');
                        //fetch collection title
                        $result = $this->model_collection->get_collection_path($collection_id);
                        
                        foreach($result as $row){
                            $path = $row->collection_directory;
                        }
                        
                        $path = str_replace(base_url(),"./",$path);
                        $config['upload_path'] = $path;
                        $config['allowed_types'] = 'gif|jpg|png|jpeg';
                        $config['overwrite'] = TRUE;
                        $config['max_size'] = '2000';

                        
                        //loading upload class with picture config preferences
                        $this->load->library('upload', $config);
                        if(! $this->upload->do_upload('wallpaper')){//if upload is carried out
                                  
                            $response['log'] = "Invalid Type or Size";
                            $response['success'] = 0;
                            //echo $this->upload->display_errors();
                            //echo $config['upload_path'];
                        }else{
                            
                            if($this->model_collection->auth_collection_change($collection_id,$user_id)){//check if user has change wallpaper privileges
                                if($this->model_collection->update_wallpaper($collection_id)){// check if news is added to db 
                                    $response['log'] = "Wallpaper Changed";
                                    $response['success'] = 1;    
                                }else{
                                    $response['log'] = "Could not Update";
                                    $response['success'] = 0;    
                                }
                            }else{
                                $response['log'] = "User unauthorized";
                                $response['success'] = 0;                             
                            }
                                                        
                        }
                    }else{
                        $response['log'] = "Invalid url key";
                        $response['success'] = 0;                    
                    }
                
                }            
            }
        }
        echo json_encode($response);
    }
    
    
    public function update_collection_title($mykey){
  
        $this->webHeaders();
        $response = array();
        if(!isset($mykey) || $mykey==""){//primary validation
            $response['log'] = "bad url";
            $response['success'] = 0;        
        }else{
        
            //load form validation l
            $this->load->library("form_validation");    
            $this->form_validation->set_rules("user_id","User_id","required|xss_clean|trim");//validate url      
            $this->form_validation->set_rules("collection_id","Collection_id","required|xss_clean|trim");            
            $this->form_validation->set_rules("title","Title","required|xss_clean|trim");            
            
            if($this->form_validation->run()){
                
                    $this->load->model('model_accounts');
                    
                    $user_id = $this->input->post('user_id'); 
                    
                    if($this->model_accounts->url_key_is_valid($mykey,$user_id)){//main authentication
                    
                        //load collection model
                        $this->load->model('model_collection');

                        $collection_id = $this->input->post('collection_id');
                        
                        //check if user has authentication privilege
                        if($this->model_collection->auth_collection_change($collection_id,$user_id)){
                        
                            $this->model_collection->update_title($collection_id);
                            
                            $response['log'] = "Title Updated";
                            $response['success'] = 1;
                            
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
    
    public function update_collection_description($mykey){
      
        $this->webHeaders();
        $response = array();
        if(!isset($mykey) || $mykey==""){//primary validation
            $response['log'] = "bad url";
            $response['success'] = 0;        
        }else{
                    
            //load form validation l
            $this->load->library("form_validation");    
            $this->form_validation->set_rules("user_id","User_id","required|xss_clean|trim");//validate url      
            $this->form_validation->set_rules("collection_id","Collection_id","required|xss_clean|trim");            
            $this->form_validation->set_rules("update","Update","required|xss_clean|trim");            
            
            if($this->form_validation->run()){
                
                    $this->load->model('model_accounts');
                    
                    $user_id = $this->input->post('user_id'); 
                    
                    if($this->model_accounts->url_key_is_valid($mykey,$user_id)){//main authentication
                    
                        //load collection model
                        $this->load->model('model_collection');
                        
                        $collection_id = $this->input->post('collection_id');
                        
                        //check if user has authentication privilege
                        if($this->model_collection->auth_collection_change($collection_id,$user_id)){
                        
                            $this->model_collection->update_status($collection_id);
                            
                            $response['log'] = "Updated";
                            $response['success'] = 1;
                            
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

    //update number of track listens listens
    public function update_listens_num($mykey){
        
        $this->webHeaders();
        $response = array();
        if(!isset($mykey) || $mykey==""){//primary validation
            $response['log'] = "bad url";
            $response['success'] = 0;        
        }else{
            //load form validation library
            
            $this->load->library("form_validation");    
            $this->form_validation->set_rules("user_id","User_id","required|xss_clean|trim");//validate url          
            $this->form_validation->set_rules("track_id","Track_id","required|xss_clean|trim");//validate track_id
            
            if($this->form_validation->run()){

                    $this->load->model('model_accounts');
                    
                    $user_id = $this->input->post('user_id'); 
                    
                    if($this->model_accounts->url_key_is_valid($mykey,$user_id)){//main authentication
                    
                        //load collection model
                        $this->load->model('model_collection');
                        
                        $track_id = $this->input->post('track_id');
                        
                        $this->model_collection->incrementListens($track_id,$user_id);
                        
                        $response['log'] = "Success";
                        $response['success'] = 1;
                        
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

    public function update_track_name($mykey){
        $this->webHeaders();

        $response = array();
        if(!isset($mykey) || $mykey==""){//primary validation
            $response['log'] = "bad url";
            $response['success'] = 0;        
        }else{
            //form authentication
            $this->load->library('form_validation');
            
            $this->form_validation->set_rules('user_id','User_id','required|xss_clean|trim');
            $this->form_validation->set_rules('track_id','Track_id','required|xss_clean|trim');
            $this->form_validation->set_rules('update','Update','required|xss_clean|trim');
            
            if($this->form_validation->run()){
                
                $user_id = $this->input->post('user_id');
                
                $this->load->model("model_accounts");
                //url verification
                if($this->model_accounts->url_key_is_valid($mykey,$user_id)){
                    
                    $this->load->model("model_collection");
                    //user authorization
                    if($this->model_collection->user_owns_tracks()){
                        
                        //action 
                        if($this->model_collection->update_trackname()){
                            $response['log'] = "Success";
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
                    $response['log'] = "Invalid url Key";
                    $response['success'] = 0;
                }                      
                
            }else{
                $response['log'] = "Incomplete Data";
                $response['success'] = 0;
                
            }
            
            

            
        }
        echo json_encode($response);
    }
    public function update_track_genre($mykey){
        
        $this->webHeaders();
        $response = array();
        if(!isset($mykey) || $mykey==""){//primary validation
            $response['log'] = "bad url";
            $response['success'] = 0;        
        }else{
            //form authentication
            $this->load->library('form_validation');
            
            $this->form_validation->set_rules('user_id','User_id','required|xss_clean|trim');
            $this->form_validation->set_rules('track_id','Track_id','required|xss_clean|trim');
            $this->form_validation->set_rules('update','Update','required|xss_clean|trim');
            
            if($this->form_validation->run()){
                
                $user_id = $this->input->post('user_id');
                
                $this->load->model("model_accounts");
                //url verification
                if($this->model_accounts->url_key_is_valid($mykey,$user_id)){
                    
                    $this->load->model("model_collection");
                    //user authorization
                    if($this->model_collection->user_owns_tracks()){
                        
                        //action 
                        if($this->model_collection->update_track_genre()){
                            $response['log'] = "Success";
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
                    $response['log'] = "Invalid url Key";
                    $response['success'] = 0;
                }                      
                
            }else{
                $response['log'] = "Incomplete Data";
                $response['success'] = 0;
                
            }
            
            

            
        }
        echo json_encode($response);        
    }
    public function update_featured_artistes($mykey){
        $this->webHeaders();
        $response = array();
        if(!isset($mykey) || $mykey==""){//primary validation
            $response['log'] = "bad url";
            $response['success'] = 0;        
        }else{
            //form authentication
            $this->load->library('form_validation');
            
            $this->form_validation->set_rules('user_id','User_id','required|xss_clean|trim');
            $this->form_validation->set_rules('track_id','Track_id','required|xss_clean|trim');
            $this->form_validation->set_rules('update','Update','required|xss_clean|trim');
            
            if($this->form_validation->run()){
                
                $user_id = $this->input->post('user_id');
                
                $this->load->model("model_accounts");
                //url verification
                if($this->model_accounts->url_key_is_valid($mykey,$user_id)){
                    
                    $this->load->model("model_collection");
                    //user authorization
                    if($this->model_collection->user_owns_tracks()){
                        
                        //action 
                        if($this->model_collection->update_track_people()){
                            $response['log'] = "Success";
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
                    $response['log'] = "Invalid url Key";
                    $response['success'] = 0;
                }                      
                
            }else{
                $response['log'] = "Incomplete Data";
                $response['success'] = 0;
                
            }
            
            

            
        }
        echo json_encode($response);        
    }
    public function update_downloadable($mykey){
         $this->webHeaders();
        $response = array();
        if(!isset($mykey) || $mykey==""){//primary validation
            $response['log'] = "bad url";
            $response['success'] = 0;        
        }else{
            //form authentication
            $this->load->library('form_validation');
            
            $this->form_validation->set_rules('user_id','User_id','required|xss_clean|trim');
            $this->form_validation->set_rules('track_id','Track_id','required|xss_clean|trim');
            
            if($this->form_validation->run()){
                
                $user_id = $this->input->post('user_id');
                
                $this->load->model("model_accounts");
                //url verification
                if($this->model_accounts->url_key_is_valid($mykey,$user_id)){
                    
                    $this->load->model("model_collection");
                    //user authorization
                    if($this->model_collection->user_owns_tracks()){
                        
                        //action 
                        
                        if(isset($_POST['downloadable'])){
                            $downloadable = "Yes";
                        }else $downloadable = "No";
                        
                        
                        if($this->model_collection->update_downloadable($downloadable)){
                            $response['log'] = "Success";
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
                    $response['log'] = "Invalid url Key";
                    $response['success'] = 0;
                }                      
                
            }else{
                $response['log'] = "Incomplete Data";
                $response['success'] = 0;
                
            }
            
            

            
        }
        echo json_encode($response);
    }
    public function update_username($mykey){
        $this->webHeaders();
        $response = array();
        if(!isset($mykey) || $mykey==""){//primary validation
            $response['log'] = "bad url";
            $response['success'] = 0;        
        }else{
            //form authentication
            $this->load->library('form_validation');
            
            $this->form_validation->set_rules('user_id','User_id','required|xss_clean|trim');
            $this->form_validation->set_rules('update','Update','required|xss_clean|trim');
            
            if($this->form_validation->run()){
                
                $user_id = $this->input->post('user_id');
                
                $this->load->model("model_accounts");
                //url verification
                if($this->model_accounts->url_key_is_valid($mykey,$user_id)){
                   
                    if(!$this->model_accounts->account_exists($this->input->post('update'))){
                        
                        //action 
                        if($this->model_accounts->update_username()){
                            $response['log'] = "Updated";
                            $response['success'] = 1;
                        }else{

                            $response['log'] = "Operation Failed";
                            $response['success'] = 0;
                        }
          
                    }else{
                        $response['log'] = "Username in Use";
                        $response['success'] = 0;
                    }
                }else{
                    $response['log'] = "Invalid url Key";
                    $response['success'] = 0;
                }                      
                
            }else{
                $response['log'] = "Incomplete Data";
                $response['success'] = 0;
                
            }
            
            

            
        }
        echo json_encode($response);        
    }
    public function update_fullname($mykey){
        $this->webHeaders();
        $response = array();
        if(!isset($mykey) || $mykey==""){//primary validation
            $response['log'] = "bad url";
            $response['success'] = 0;        
        }else{
            //form authentication
            $this->load->library('form_validation');
            
            $this->form_validation->set_rules('user_id','User_id','required|xss_clean|trim');
            $this->form_validation->set_rules('update','Update','required|xss_clean|trim');
            
            if($this->form_validation->run()){
                
                $user_id = $this->input->post('user_id');
                
                $this->load->model("model_accounts");
                //url verification
                if($this->model_accounts->url_key_is_valid($mykey,$user_id)){
                   
                        
                   //action 
                   if($this->model_accounts->update_fullname()){
                      $response['log'] = "Updated";
                      $response['success'] = 1;
                   }else{
                           
                      $response['log'] = "Operation Failed";
                      $response['success'] = 0;
                   }
          
                    
                }else{
                    $response['log'] = "Invalid url Key";
                    $response['success'] = 0;
                }                      
                
            }else{
                $response['log'] = "Incomplete Data";
                $response['success'] = 0;
                
            }
            
            

            
            
        }
        echo json_encode($response);        
    }
    public function update_user_type($mykey){
        $this->webHeaders();
        $response = array();
        if(!isset($mykey) || $mykey==""){//primary validation
            $response['log'] = "bad url";
            $response['success'] = 0;        
        }else{
            //form authentication
            $this->load->library('form_validation');
            
            $this->form_validation->set_rules('user_id','User_id','required|xss_clean|trim');
            $this->form_validation->set_rules('update','Update','required|xss_clean|trim');
            
            if($this->form_validation->run()){
                
                $user_id = $this->input->post('user_id');
                
                $this->load->model("model_accounts");
                //url verification
                if($this->model_accounts->url_key_is_valid($mykey,$user_id)){
                   
                        
                   //action 
                   if($this->model_accounts->update_type()){
                      $response['log'] = "Updated";
                      $response['success'] = 1;
                   }else{
                           
                      $response['log'] = "Operation Failed";
                      $response['success'] = 0;
                   }
          
                    
                }else{
                    $response['log'] = "Invalid url Key";
                    $response['success'] = 0;
                }                      
                
            }else{
                $response['log'] = "Incomplete Data";
                $response['success'] = 0;
                
            }
            
            

            
        }
        echo json_encode($response);        
    }
    
    public function update_voice_caption($mykey){
         
        $this->webHeaders();
        $response = array();
        
        if(!isset($mykey) || $mykey==""){//primary valiadation
            $response['log'] = "bad url";
            $response['success'] = 0;
        }else{
            $this->load->library("form_validation");
            
            $this->form_validation->set_rules("user_id","","required|xss_clean|trim");
            $this->form_validation->set_rules("post_id","","required|xss_clean|trim");
            $this->form_validation->set_rules("caption","","required|xss_clean|trim");
                    
            if($this->form_validation->run()){
                
                $this->load->model("model_accounts");
                if($this->model_accounts->url_key_is_valid($mykey,$this->input->post('user_id'))){//main authentication 
                    
                    $this->load->model("model_collection");
                    if($this->model_collection->user_owns_voice()){
                        if($this->model_collection->update_caption()){

                            $response['log'] = "Caption Updated";
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
        
    }
    
    public function update_track($mykey){
        $this->webHeaders();
        $response = array();
        if(!isset($mykey) || $mykey==""){//primary validation
            $response['log'] = "bad url";
            $response['success'] = 0;        
        }else{
            $this->load->library("form_validation");
            
            $this->form_validation->set_rules("user_id","","required|xss_clean|trim");
            $this->form_validation->set_rules("track_id","","required|xss_clean|trim");
            $this->form_validation->set_rules("title","","required|xss_clean|trim");
            $this->form_validation->set_rules("genre","","required|xss_clean|trim");
            $this->form_validation->set_rules("featured","","xss_clean|trim");
            $this->form_validation->set_rules("downloadable","","xss_clean|trim");
            
            if($this->form_validation->run()){
                
                $this->load->model("model_accounts");
                //url verification
                $user_id = $this->input->post('user_id');
                if($this->model_accounts->url_key_is_valid($mykey,$user_id)){
                    
                    $this->load->model("model_collection");
                    //user authorization
                    if($this->model_collection->user_owns_tracks()){
                        if($this->model_collection->update_track()){
                            $response['log'] = "Success";
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
                    $response['log'] = "Invalid user key";
                    $response['success'] = 0;
                }
                
            }else{
                $response['log'] = "Incomplete Data";
                $response['success'] = 0; 
            }
        }        
        echo json_encode($response);
    }
    
    
    public function update_collection($mykey){
     
        $this->webHeaders();
        $response = array();
        if(!isset($mykey) || $mykey==""){//primary validation
            $response['log'] = "bad url";
            $response['success'] = 0;        
        }else{
                    
            //load form validation l
            $this->load->library("form_validation");    
            $this->form_validation->set_rules("user_id","User_id","required|xss_clean|trim");//validate url      
            $this->form_validation->set_rules("collection_id","Collection_id","required|xss_clean|trim");            
            $this->form_validation->set_rules("title","Title","required|xss_clean|trim");            
            $this->form_validation->set_rules("description","Desc","required|xss_clean|trim");            
            $this->form_validation->set_rules("genre","genre","required|xss_clean|trim");            
            
            if($this->form_validation->run()){
                
                    $this->load->model('model_accounts');
                    
                    $user_id = $this->input->post('user_id'); 
                    
                    if($this->model_accounts->url_key_is_valid($mykey,$user_id)){//main authentication
                    
                        //load collection model
                        $this->load->model('model_collection');
                        
                        $collection_id = $this->input->post('collection_id');
                        
                        //check if user has authentication privilege
                        if($this->model_collection->auth_collection_change($collection_id,$user_id)){
                        
                            $this->model_collection->update_collection($collection_id);
                            
                            $response['log'] = "Updated";
                            $response['success'] = 1;
                            
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