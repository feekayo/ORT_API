<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Read extends CI_Controller {

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

    
    public function fetch_username_data(){

        $this->webHeaders();
        $response = array();
        //load form validation library
        $this->load->library("form_validation");
         
        //validation rules
        $this->form_validation->set_rules("session_user_id","User_id","xss_clean|trim"); 
        $this->form_validation->set_rules("username","Username","required|xss_clean|trim");         
        
        if($this->form_validation->run()){
            
            $this->load->model("model_accounts");
            
            //check user's existence
            if($this->model_accounts->account_exists($this->input->post('username'))){
        
                $result = $this->model_accounts->get_user_data($this->input->post('username'));
                
                
                foreach($result as $row){
        
                    $response['log'] = "success";
                    $response['success'] = 1;
                    $response['user_id'] = $row->user_id;
                    $response['username'] = $row->name;
                    $response['email'] = $row->email;
                    $response['fullname'] = $row->fullname;
                    $response['type'] = $row->type;
                    $response['bio'] = $row->bio;
                    $response['wallpaper'] = $row->wallpaper;
                    $response['date'] = $row->date;
                    
                    $user_id = $row->user_id;
                    $response['following'] = $this->model_accounts->check_follow_exists($user_id);
                    
                    $response['is_following'] = $this->model_accounts->get_is_following_num($user_id);
                    $response['is_followed_by'] = $this->model_accounts->get_is_followed_num($user_id);
                    $response['tracks_uploaded'] = $this->model_accounts->get_tracks_num($user_id);
                    $response['voices_uploaded'] = $this->model_accounts->get_voice_num($user_id);
                    $response['tracks_listened'] = $this->model_accounts->get_user_listens_num($user_id);
                    $response['tracks_liked'] = $this->model_accounts->get_user_likes_num($user_id);                    

                }
                
            }else{

                $response['log'] = "User doesn't exist";
                $response['success'] = 0;
            
            }
                    
        }else{
            $response['log'] = "Incomplete data";
            $response['success'] = 0;
        }

        
             
        echo json_encode($response);
    
    }
    
    public function fetch_user_id_data(){
        
        $this->webHeaders();
        $response = array();

        //load form validation library
        $this->load->library("form_validation");
         
        //validation rules
        $this->form_validation->set_rules("session_user_id","User_id","xss_clean|trim"); 
        $this->form_validation->set_rules("user_id","User_id","required|xss_clean|trim");         
        
        if($this->form_validation->run()){
            
            $this->load->model("model_accounts");
            
            //check user's existence
            if($this->model_accounts->account_exists($this->input->post('user_id'))){
        
                $result = $this->model_accounts->get_user_data($this->input->post('user_id'));
                

                
                foreach($result as $row){
        
                    $response['log'] = "success";
                    $response['success'] = 1;
                    $response['user_id'] = $row->user_id;
                    $response['username'] = $row->name;
                    $response['email'] = $row->email;
                    $response['fullname'] = $row->fullname;
                    $response['type'] = $row->type;
                    $response['bio'] = $row->bio;
                    $response['wallpaper'] = $row->wallpaper;
                    $response['date'] = $row->date;
                    
                    $user_id = $row->user_id;
                    $response['following'] = $this->model_accounts->check_follow_exists($user_id);
                    
                    $response['is_following'] = $this->model_accounts->get_is_following_num($user_id);
                    $response['is_followed_by'] = $this->model_accounts->get_is_followed_num($user_id);
                    $response['tracks_uploaded'] = $this->model_accounts->get_tracks_num($user_id);
                    $response['voices_uploaded'] = $this->model_accounts->get_voice_num($user_id);
                    $response['tracks_listened'] = $this->model_accounts->get_user_listens_num($user_id);
                    $response['tracks_liked'] = $this->model_accounts->get_user_likes_num($user_id);                             
                }
                
            }else{

                $response['log'] = "User doesnt exist";
                $response['success'] = 0;
            
            }
                    
        }else{
            $response['log'] = "Incomplete data";
            $response['success'] = 0;
        }

        
             
        echo json_encode($response);
    }    
    
    
    public function fetch_user_stats(){
        
        
        $this->webHeaders();
        $response = array();

        //load form validation library
        $this->load->library("form_validation");
        
        //validation rules
        
        $this->form_validation->set_rules("user_id","User_id","required|xss_clean|trim");         
        
        if($this->form_validation->run()){
            
            $this->load->model("model_accounts");
            
            //check user's existence
            if($this->model_accounts->account_exists($this->input->post('user_id'))){
        
                
                $user_id = $this->input->post('user_id');
                $response['log'] = "success";
                $response['success'] = 1;
                $response['is_following'] = $this->model_accounts->get_is_following_num($user_id);
                $response['is_followed_by'] = $this->model_accounts->get_is_followed_num($user_id);
                $response['tracks_uploaded'] = $this->model_accounts->get_tracks_num($user_id);
                $response['voices_uploaded'] = $this->model_accounts->get_voice_num($user_id);
                $response['tracks_listened'] = $this->model_accounts->get_user_listens_num($user_id);
                $response['tracks_liked'] = $this->model_accounts->get_user_likes_num($user_id);
                
            }else{
                $response['log'] = "User doesn't exist";
                $response['success'] = 0;            
            }    
        }else{
            $response['log'] = "Incomplete data";
            $response['success'] = 0;
        }

        
             
        echo json_encode($response);        
    
    }
    
    
    public function fetch_collection_data(){
        
        $this->webHeaders();
        $response = array();

        //load form validation library
        $this->load->library("form_validation");
        
        //validation rules
        $this->form_validation->set_rules("session_user_id","User_id","xss_clean|trim"); 
        $this->form_validation->set_rules("collection_id","User_id","required|xss_clean|trim");         
        
        if($this->form_validation->run()){
            
            $this->load->model("model_collection");
            
            //check user's existence
            if($this->model_collection->collection_exists($this->input->post('collection_id'))){
            
                $result = $this->model_collection->get_collection($this->input->post('collection_id'));
                
                foreach($result as $row){
                    $response['success'] = 1;
                    $response['log'] = "Collection data fetched";
                    $response['collection_id'] = $row->collection_id;
                    $response['user_id'] = $row->user_id;
                    $response['title'] = $row->collection_title;
                    $response['desc'] = $row->collection_bio;
                    $response['type'] = $row->collection_type;
                    $response['genre'] = $row->collection_category;
                    $response['album_art'] = $row->album_art;
                    $response['directory'] = $row->collection_directory;
                    $response['date_created'] = $row->date;
                    
                    $collection_id = $row->collection_id;
                    $response['subscribed'] = $this->model_collection->check_subscribe_exists($collection_id);
                }
                
            }else{
                $response['log'] = "collection doesn't exist";
                $response['success'] = 0;
            }
    
        }else{
            
            $response['log'] = "Incomplete Data";
            $response['success'] = 0;
        
        }
        
        echo json_encode($response);
    }
    
    
    public function fetch_user_playlist(){
    
        $this->webHeaders();
        $response = array();

        //load form validation library
        $this->load->library("form_validation");
        
        //validation rules
        $this->form_validation->set_rules("user_id","User_id","required|xss_clean|trim");     
        
        if($this->form_validation->run()){
            
            $this->load->model("model_accounts");
            
            $user_id = $this->input->post('user_id');
            //check if user exists
            if($this->model_accounts->check_user($user_id)){
                $this->load->model("model_collection");
                
                $playlists = $this->model_collection->fetch_user_playlists($user_id);
                
                if($playlists!=null){
                    
                    $count = 1;
                    $response['success'] = 1;
                    $response['log'] = "success";
                    
                    foreach($playlists as $row){
                        
                        $response['playlists'][$count]['play_id'] = $row->play_id;
                        $response['playlists'][$count]['name'] = $row->playlist_name;
                        $response['playlists'][$count]['description'] = $row->description;
                        $response['playlists'][$count]['timestamp'] = $row->timestamp;
                        
                        $count++;
                    }
                    
                }else{
                    $response['log'] = "No User Playlist";
                    $response['success'] = 0;
                }
            }else{
                $response['log'] = "User Doesn't Exist";
            }
            
        }else{
            $response['log'] = "Incomplete Data";
            $response['success'] = 0;
            
        }
        echo json_encode($response);
    }
 
    
    public function ns_fetch_user_playlist(){
        
        $this->webHeaders();
        $response = array();

        //load form validation library
        $this->load->library("form_validation");
        
        //validation rules
        $this->form_validation->set_rules("user_id","User_id","required|xss_clean|trim");     
        
        if($this->form_validation->run()){
            
            $this->load->model("model_accounts");
            
            $user_id = $this->input->post('user_id');
            //check if user exists
            if($this->model_accounts->check_user($user_id)){
                $this->load->model("model_collection");
                
                $playlists = $this->model_collection->fetch_user_playlists($user_id);
                
                if($playlists!=null){
                    
                    $count = 1;
                    $response['success'] = 1;
                    $response['log'] = "success";
                    
                    $response['playlists'] = array();
                    
                    foreach($playlists as $row){
                        
                        $data = array(
                            "play_id" => $row->play_id,
                            "name" => $row->playlist_name,
                            "description" => $row->description,
                            "timestamp" => $row->timestamp
                        );
                        
                        $response['playlists'] = array_merge($response['playlists'],array($data));
                        
                    }
                    
                }else{
                    $response['log'] = "No User Playlist";
                    $response['success'] = 0;
                }
            }else{
                $response['log'] = "User Doesn't Exist";
            }
            
        }else{
            $response['log'] = "Incomplete Data";
            $response['success'] = 0;
            
        }
        echo json_encode($response);
    }    
    
    
    public function fetch_playlist_tracks(){
    
        $this->webHeaders();
        $response = array();

        //load form validation library
        $this->load->library("form_validation");
        
        
        //validation rules
        $this->form_validation->set_rules("session_user_id","User_id","xss_clean|trim"); 
        $this->form_validation->set_rules("play_id","Play_id","required|xss_clean|trim");  
        $this->form_validation->set_rules("last_id","Last_id","required|xss_clean|trim");//to be zero for first installment
        
        if($this->form_validation->run()){
            
            $this->load->model("model_accounts");
            
           $play_id = $this->input->post('play_id');
           $last_id = $this->input->post('last_id');
                
           $this->load->model("model_collection");
               
                
           $user_playlist = $this->model_collection->fetch_playlist_tracks($play_id,$last_id);
            
                //print_r($user_playlist);
           if($user_playlist!=null){
                    
               $response['success'] = 1;
               $response['log'] = "Playlist Fetched";                    
               $count = 1;
                    
               foreach($user_playlist as $row){
                   
                   $response['tracks'][$count]['id'] = $row->id;
                   $response['tracks'][$count]['track_id'] = $row->track_id;
                   $response['tracks'][$count]['collection_id'] =  $row->collection_id;
                   $response['tracks'][$count]['collection_name'] =  $row->collection_title;
                      
                   $response['tracks'][$count]['listens'] = $row->listens;
                   $response['tracks'][$count]['likes'] = $row->likes;
                   $response['tracks'][$count]['title'] = $row->trackname;
                   $response['tracks'][$count]['username'] = $row->name;
                   $response['tracks'][$count]['genre'] = $row->track_genre;
                   $response['tracks'][$count]['audio_file'] = $row->audio_file;
                   $response['tracks'][$count]['downloadable']= $row->downloadable;
                   $response['tracks'][$count]['featured'] = $row->people;
                   $response['tracks'][$count]['comments_num'] = $row->comments_num;
                        
                   $track_id = $row->track_id;
                   $response['tracks'][$count]['liked'] = $this->model_collection->check_like_exists($track_id);
                   $count++;
               }
                
           }else{
              if($last_id==1){
                $response['log'] = "Empty Playlist";
              }else $response['log'] = "No More Tracks";
              $response['success'] = 0;
           }
            
    
        }else{
            
            $response['log'] = "Incomplete Data";
            $response['success'] = 0;
        
        }
        
        echo json_encode($response);    
        
    
    }
    
    public function ns_fetch_playlist_tracks(){
    
        $this->webHeaders();
        $response = array();
        

        //load form validation library
        $this->load->library("form_validation");
        
        //validation rules
        $this->form_validation->set_rules("session_user_id","User_id","xss_clean|trim"); 
        $this->form_validation->set_rules("play_id","Play_id","required|xss_clean|trim");  
        $this->form_validation->set_rules("last_id","Last_id","required|xss_clean|trim");//to be zero for first installment
        
        if($this->form_validation->run()){
            
            $this->load->model("model_accounts");
            
           $play_id = $this->input->post('play_id');
           $last_id = $this->input->post('last_id');
                
           $this->load->model("model_collection");
               
                
           $user_playlist = $this->model_collection->fetch_playlist_tracks($play_id,$last_id);
            
                //print_r($user_playlist);
           if($user_playlist!=null){
                    
               $response['success'] = 1;
               $response['log'] = "Playlist Fetched";                    
               $count = 1;
               
               $response['tracks'] = array();
                    
               foreach($user_playlist as $row){
                   $track_id = $row->track_id;
                   
                   $data = array(
                     'id' => $row->id,
                     'track_id' => $row->track_id,
                     'collection_id' => $row->collection_id,
                     'collection_name' => $row->collection_title,
                     'listens' => $row->listens,
                     'likes' => $row->likes,
                     'title' => $row->trackname,
                     'username' => $row->name,
                     'genre' => $row->track_genre,
                     'audio_file' => $row->audio_file,
                     'downloadable' => $row->downloadable,
                     'featured' => $row->people,
                     'album_art' => $row->album_art, 
                     'comments_num' => $row->comments_num,
                     'liked' => $this->model_collection->check_like_exists($track_id)
                   );
                        
                   $response['tracks'] = array_merge($response['tracks'],array($data));                   
               }
                
           }else{
              if($last_id==1){
                $response['log'] = "Empty Playlist";
              }else $response['log'] = "No More Tracks";
              $response['success'] = 0;
           }
            
    
        }else{
            
            $response['log'] = "Incomplete Data";
            $response['success'] = 0;
        
        }
        
        echo json_encode($response);    
        
    
    }    
    
    public function fetch_live_comments(){
    
        $this->webHeaders();
        $response = array();
        //load form validation library
        $this->load->library("form_validation");
        
           //validation rules
        $this->form_validation->set_rules("user_id","User_id","required|xss_clean|trim");  
        $this->form_validation->set_rules("last_id","Last_id","required|xss_clean|trim");//to be zero for first installment
        
        if($this->form_validation->run()){
            
            $this->load->model("model_accounts");
            
            $user_id = $this->input->post('user_id');
            $last_id = $this->input->post('last_id');
            
            //check if user exists
            if($this->model_accounts->check_user($user_id)){
                
                $result_set = $this->model_accounts->fetch_live_comments($user_id,$last_id);
                
                //print_r($result_set);
                
                if($result_set!=null){
               
                    $count = 1;
                    $response['log'] = "Success";
                    $response['success'] = 1;
                    
                    foreach($result_set as $row){
                    
                        
                        $response['comments'][$count]['username'] = $row->name;
                        $response['comments'][$count]['user_id'] = $row->user_id;
                        $response['comments'][$count]['comment'] = $row->comment;
                        $response['comments'][$count]['timestamp'] = $row->timestamp;
                        $count++;
                    }
                
                }else{
                
              if($last_id==1){
                $response['log'] = "No Comments";
              }else $response['log'] = "No More Comments";
                    $response['success'] = 0;
                
                }
            
            }else{
                
                $response['log'] = "User doesnt exist";
            
            }
        }else{
            $response['log'] = "Incomplete data";
            $response['success'] = 0;
        }
        
        echo json_encode($response);
    }
    
    public function fetch_track_comments(){
    
        $this->webHeaders();    
        $response = array();
        //load form validation library
        $this->load->library("form_validation");
        
           //validation rules
        $this->form_validation->set_rules("track_id","User_id","required|xss_clean|trim");  
        $this->form_validation->set_rules("last_id","Last_id","required|xss_clean|trim");//to be zero for first installment
        
        if($this->form_validation->run()){
            
            $this->load->model("model_accounts");
            
            $track_id = $this->input->post('track_id');
            $last_id = $this->input->post('last_id');
            
            $this->load->model("model_collection");
            
            $result_set = $this->model_collection->fetch_track_comments($track_id,$last_id);
                
            //print_r($result_set);
            if($result_set!=null){
               
                $count = 1;
                $response['log'] = "Success";
                $response['success'] = 1;
                    
                foreach($result_set as $row){
                            
                    $response['comments'][$count]['dp'] = $row->wallpaper;
                    $response['comments'][$count]['comment_id'] = $row->comment_id;
                    $response['comments'][$count]['username'] = $row->name;
                    $response['comments'][$count]['user_id'] = $row->user_id;
                    $response['comments'][$count]['comment'] = $row->comment;
                    $response['comments'][$count]['timestamp'] = $row->timestamp;
                    $count++;
                    
                }
                
            }else{
                
              if($last_id==1){
                $response['log'] = "No Comments";
              }else $response['log'] = "No More Comments";
                $response['success'] = 0;
                
            }
        }else{
            $response['log'] = "Incomplete data";
            $response['success'] = 0;
        }
        
        echo json_encode($response);
    
    }

    public function ns_fetch_track_comments(){
    
        $this->webHeaders();    
        $response = array();
        //load form validation library
        $this->load->library("form_validation");
        
           //validation rules
        $this->form_validation->set_rules("track_id","User_id","required|xss_clean|trim");  
        $this->form_validation->set_rules("last_id","Last_id","required|xss_clean|trim");//to be zero for first installment
        
        if($this->form_validation->run()){
            
            $this->load->model("model_accounts");
            
            $track_id = $this->input->post('track_id');
            $last_id = $this->input->post('last_id');
            
            $this->load->model("model_collection");
            
            $result_set = $this->model_collection->fetch_track_comments($track_id,$last_id);
                
            //print_r($result_set);
            if($result_set!=null){
               
                //$count = 1;
                $response['log'] = "Success";
                $response['success'] = 1;
                  
                $response['comments'] = array();
                
                foreach($result_set as $row){
                            
                    $data = array(
                       'dp' => $row->wallpaper,
                       'comment_id' => $row->comment_id,
                       'username' => $row->name,
                       'user_id' => $row->user_id,
                       'comment' => $row->comment,
                       'timestamp' => $row->timestamp
                    );
                    
                    
                    $response['comments'] = array_merge($response['comments'],array($data));
                }
                
            }else{
                
              if($last_id==1){
                $response['log'] = "No Comments";
              }else $response['log'] = "No More Comments";
                $response['success'] = 0;
                
            }
        }else{
            $response['log'] = "Incomplete data";
            $response['success'] = 0;
        }
        
        echo json_encode($response);
    
    }    
    
    public function fetch_tracks_by_latest(){
    
        $this->webHeaders();
        $response = array();
        
        $this->load->model("model_collection");
        
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules("session_user_id","User_id","xss_clean|trim"); 
        $this->form_validation->set_rules("last_id","Last_id","required|xss_clean|trim");
        
        if($this->form_validation->run()){
            
            $last_id = $this->input->post('last_id');
            
            $result_set = $this->model_collection->get_tracks_by_latest($last_id);
            
            //print_r($result_set);
            $count = 1;
            
            if($result_set!=null){
            
                $response['log'] = "Success";
                $response['success'] = 1;
                
                foreach($result_set as $row){    
                   $response['tracks'][$count]['track_id'] = $row->track_id;
                   $response['tracks'][$count]['collection_id'] =  $row->collection_id;
                   $response['tracks'][$count]['collection_name'] =  $row->collection_title;
                      
                   $response['tracks'][$count]['listens'] = $row->listens;
                   $response['tracks'][$count]['likes'] = $row->likes;
                   $response['tracks'][$count]['title'] = $row->trackname;
                   $response['tracks'][$count]['username'] = $row->name;
                   $response['tracks'][$count]['genre'] = $row->track_genre;
                   $response['tracks'][$count]['audio_file'] = $row->audio_file;
                   $response['tracks'][$count]['downloadable']= $row->downloadable;
                   $response['tracks'][$count]['featured'] = $row->people;
                   $response['tracks'][$count]['album_art'] = $row->album_art;     
                   $response['tracks'][$count]['comments_num'] = $row->comments_num;
                   $track_id = $row->track_id;
                   $response['tracks'][$count]['liked'] = $this->model_collection->check_like_exists($track_id);
                    $count++;
                    
                }
            }else{
              if($last_id==1){
                $response['log'] = "No Tracks";
              }else $response['log'] = "No More Tracks";
                $response['success'] = 0;
            }
        
        }else{
        
            $response['log'] = "Incomplete Data";
            $response['success'] = 0;
        }
        echo json_encode($response);
    }
    
    public function ns_fetch_tracks_by_latest(){
    
        $this->webHeaders();
        $response = array();
        
        $this->load->model("model_collection");
        
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules("session_user_id","User_id","xss_clean|trim"); 
        $this->form_validation->set_rules("last_id","Last_id","required|xss_clean|trim");
        
        if($this->form_validation->run()){
            
            $last_id = $this->input->post('last_id');
            
            $result_set = $this->model_collection->get_tracks_by_latest($last_id);
            
            //print_r($result_set);
            
            if($result_set!=null){
            
                $response['log'] = "Success";
                $response['success'] = 1;
                
                $response['tracks'] = array();
                
                foreach($result_set as $row){
                   $track_id = $row->track_id;
                   
                   $data = array(
                     'track_id' => $row->track_id,
                     'collection_id' => $row->collection_id,
                     'collection_name' => $row->collection_title,
                     'listens' => $row->listens,
                     'likes' => $row->likes,
                     'title' => $row->trackname,
                     'username' => $row->name,
                     'genre' => $row->track_genre,
                     'audio_file' => $row->audio_file,
                     'downloadable' => $row->downloadable,
                     'featured' => $row->people,
                     'album_art' => $row->album_art, 
                     'comments_num' => $row->comments_num,
                     'liked' => $this->model_collection->check_like_exists($track_id)
                   );
                        
                   $response['tracks'] = array_merge($response['tracks'],array($data)); 
                }
            }else{
              if($last_id==1){
                $response['log'] = "No Tracks";
              }else $response['log'] = "No More Tracks";
                $response['success'] = 0;
            }
        
        }else{
        
            $response['log'] = "Incomplete Data";
            $response['success'] = 0;
        }
        echo json_encode($response);
    }
        
    
    public function fetch_tracks_by_listens(){
    
        $this->webHeaders();
        $response = array();
        
        $this->load->model("model_collection");
        
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules("session_user_id","User_id","xss_clean|trim"); 
        $this->form_validation->set_rules("last_id","Last_id","required|xss_clean|trim");
        $this->form_validation->set_rules("last_listens","Last_listens","required|xss_clean|trim");
        
        if($this->form_validation->run()){
        
            $last_id = $this->input->post('last_id');
            $last_listens = $this->input->post('last_listens');
            
            
            $result_set = $this->model_collection->get_tracks_by_listens($last_id,$last_listens);
            
            
            //print_r($result_set);
            
            if($result_set != null){
                
                $response['log'] = "Success";
                $response['success'] = 1;
                
                $count = 1;
                
                foreach($result_set as $row){
            
                   $response['tracks'][$count]['track_id'] = $row->track_id;
                   $response['tracks'][$count]['collection_id'] =  $row->collection_id;
                   $response['tracks'][$count]['collection_name'] =  $row->collection_title;
                      
                   $response['tracks'][$count]['listens'] = $row->listens;
                   $response['tracks'][$count]['likes'] = $row->likes;
                   $response['tracks'][$count]['title'] = $row->trackname;
                   $response['tracks'][$count]['username'] = $row->name;
                   $response['tracks'][$count]['genre'] = $row->track_genre;
                   $response['tracks'][$count]['audio_file'] = $row->audio_file;
                   $response['tracks'][$count]['downloadable']= $row->downloadable;
                   $response['tracks'][$count]['featured'] = $row->people;
                   $response['tracks'][$count]['album_art'] = $row->album_art;     
                   $response['tracks'][$count]['comments_num'] = $row->comments_num;
                   $track_id = $row->track_id;
                   $response['tracks'][$count]['liked'] = $this->model_collection->check_like_exists($track_id);             
                    $count++;
                }
                    
            
            }else{
              if($last_id==1){
                $response['log'] = "No Tracks";
              }else $response['log'] = "No More Tracks";
                $response['success'] = 0;
            }
        }else{
        
            $response['log'] = "Incomplete Data";
            $response['success'] = 0;
        }
        echo json_encode($response);    
    }
    
    public function ns_fetch_tracks_by_listens(){
    
        $this->webHeaders();
        $response = array();

        $this->load->model("model_collection");
        
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules("session_user_id","User_id","xss_clean|trim"); 
        $this->form_validation->set_rules("last_id","Last_id","required|xss_clean|trim");
        $this->form_validation->set_rules("last_listens","Last_listens","required|xss_clean|trim");
        
        if($this->form_validation->run()){
        
            $last_id = $this->input->post('last_id');
            $last_listens = $this->input->post('last_listens');
            
            
            $result_set = $this->model_collection->get_tracks_by_listens($last_id,$last_listens);
            
            
            //print_r($result_set);
            
            if($result_set != null){
                
                $response['log'] = "Success";
                $response['success'] = 1;
                
                $response['tracks'] = array();
                
                foreach($result_set as $row){
                   $track_id = $row->track_id;
                   
                   $data = array(
                     'track_id' => $row->track_id,
                     'collection_id' => $row->collection_id,
                     'collection_name' => $row->collection_title,
                     'listens' => $row->listens,
                     'likes' => $row->likes,
                     'title' => $row->trackname,
                     'username' => $row->name,
                     'genre' => $row->track_genre,
                     'audio_file' => $row->audio_file,
                     'downloadable' => $row->downloadable,
                     'featured' => $row->people,
                     'album_art' => $row->album_art, 
                     'comments_num' => $row->comments_num,
                     'liked' => $this->model_collection->check_like_exists($track_id)
                   );
                        
                   $response['tracks'] = array_merge($response['tracks'],array($data)); 
                }
                    
            
            }else{
              if($last_id==1){
                $response['log'] = "No Tracks";
              }else $response['log'] = "No More Tracks";
                $response['success'] = 0;
            }
        }else{
        
            $response['log'] = "Incomplete Data";
            $response['success'] = 0;
        }
        echo json_encode($response);    
    }    
    
    public function fetch_tracks_by_likes(){
    
        $this->webHeaders();
        $response = array();
        
        $this->load->model("model_collection");
        
        $this->load->library('form_validation');

        $this->form_validation->set_rules("session_user_id","User_id","xss_clean|trim"); 
        $this->form_validation->set_rules("last_id","Last_id","required|xss_clean|trim");
        $this->form_validation->set_rules("last_likes","Last_listens","required|xss_clean|trim");
        
        if($this->form_validation->run()){
        
            $last_id = $this->input->post('last_id');
            $last_likes = $this->input->post('last_likes');
            
            
            $result_set = $this->model_collection->get_tracks_by_likes($last_id,$last_likes);
            
            
            //print_r($result_set);
            
            if($result_set != null){
                
                $response['log'] = "Success";
                $response['success'] = 1;
                
                $count = 1;
                
                foreach($result_set as $row){
            
                   $response['tracks'][$count]['track_id'] = $row->track_id;
                   $response['tracks'][$count]['collection_id'] =  $row->collection_id;
                   $response['tracks'][$count]['collection_name'] =  $row->collection_title;
                      
                   $response['tracks'][$count]['listens'] = $row->listens;
                   $response['tracks'][$count]['likes'] = $row->likes;
                   $response['tracks'][$count]['title'] = $row->trackname;
                   $response['tracks'][$count]['username'] = $row->name;
                   $response['tracks'][$count]['genre'] = $row->track_genre;
                   $response['tracks'][$count]['audio_file'] = $row->audio_file;
                   $response['tracks'][$count]['downloadable']= $row->downloadable;
                   $response['tracks'][$count]['featured'] = $row->people;
                   $response['tracks'][$count]['album_art'] = $row->album_art;   
                   $response['tracks'][$count]['comments_num'] = $row->comments_num;                   
                   $track_id = $row->track_id;
                   $response['tracks'][$count]['liked'] = $this->model_collection->check_like_exists($track_id);               
                    $count++;
                }
                    
            
            }else{
              if($last_id==1){
                $response['log'] = "No Tracks";
              }else $response['log'] = "No More Tracks";
                $response['success'] = 0;
            }
        }else{
        
            $response['log'] = "Incomplete Data";
            $response['success'] = 0;
        }
        echo json_encode($response); 
    }

    public function ns_fetch_tracks_by_likes(){
    
        $this->webHeaders();
        $response = array();             
        
        $this->load->model("model_collection");
        
        $this->load->library('form_validation');

        $this->form_validation->set_rules("session_user_id","User_id","xss_clean|trim"); 
        $this->form_validation->set_rules("last_id","Last_id","required|xss_clean|trim");
        $this->form_validation->set_rules("last_likes","Last_listens","required|xss_clean|trim");
        
        if($this->form_validation->run()){
        
            $last_id = $this->input->post('last_id');
            $last_likes = $this->input->post('last_likes');
            
            
            $result_set = $this->model_collection->get_tracks_by_likes($last_id,$last_likes);
            
            
            //print_r($result_set);
            
            if($result_set != null){
                
                $response['log'] = "Success";
                $response['success'] = 1;
                
                $count = 1;
                
                $response['tracks'] = array();
                
                foreach($result_set as $row){
                   $track_id = $row->track_id;
                   
                   $data = array(
                     'track_id' => $row->track_id,
                     'collection_id' => $row->collection_id,
                     'collection_name' => $row->collection_title,
                     'listens' => $row->listens,
                     'likes' => $row->likes,
                     'title' => $row->trackname,
                     'username' => $row->name,
                     'genre' => $row->track_genre,
                     'audio_file' => $row->audio_file,
                     'downloadable' => $row->downloadable,
                     'featured' => $row->people,
                     'album_art' => $row->album_art, 
                     'comments_num' => $row->comments_num,
                     'liked' => $this->model_collection->check_like_exists($track_id)
                   );
                        
                   $response['tracks'] = array_merge($response['tracks'],array($data)); 
                }
                    
            
            }else{
              if($last_id==1){
                $response['log'] = "No Tracks";
              }else $response['log'] = "No More Tracks";
                $response['success'] = 0;
            }
        }else{
        
            $response['log'] = "Incomplete Data";
            $response['success'] = 0;
        }
        echo json_encode($response); 
    }
    
    public function fetch_tracks_by_genre(){
    
        $this->webHeaders();
        $response = array();
        
        $this->load->model("model_collection");
        
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules("session_user_id","User_id","xss_clean|trim"); 
        $this->form_validation->set_rules("last_id","Last_id","required|xss_clean|trim");
        $this->form_validation->set_rules("genre","genre","required|xss_clean|trim");
        
        if($this->form_validation->run()){
        
            $last_id = $this->input->post('last_id');
            $genre = $this->input->post('genre');
            
            
            $result_set = $this->model_collection->get_tracks_by_genre($last_id,$genre);
            
            
            //print_r($result_set);
            
            if($result_set != null){
                
                $response['log'] = "Success";
                $response['success'] = 1;
                
                $count = 1;
                
                foreach($result_set as $row){
            
                   $response['tracks'][$count]['track_id'] = $row->track_id;
                   $response['tracks'][$count]['collection_id'] =  $row->collection_id;
                   $response['tracks'][$count]['collection_name'] =  $row->collection_title;
                      
                   $response['tracks'][$count]['listens'] = $row->listens;
                   $response['tracks'][$count]['likes'] = $row->likes;
                   $response['tracks'][$count]['title'] = $row->trackname;
                   $response['tracks'][$count]['username'] = $row->name;
                   $response['tracks'][$count]['genre'] = $row->track_genre;
                   $response['tracks'][$count]['audio_file'] = $row->audio_file;
                   $response['tracks'][$count]['downloadable']= $row->downloadable;
                   $response['tracks'][$count]['featured'] = $row->people;
                   $response['tracks'][$count]['album_art'] = $row->album_art;    
                   $response['tracks'][$count]['comments_num'] = $row->comments_num;                   
                   $track_id = $row->track_id;
                   $response['tracks'][$count]['liked'] = $this->model_collection->check_like_exists($track_id);           
                    $count++;
                }
                    
            
            }else{
              if($last_id==1){
                $response['log'] = "No Tracks";
              }else $response['log'] = "No More Tracks";
                $response['success'] = 0;
            }
        }else{
        
            $response['log'] = "Incomplete Data";
            $response['success'] = 0;
        }
        echo json_encode($response); 
    }

    public function ns_fetch_tracks_by_genre(){
    
        $this->webHeaders();
        $response = array();
        
        $this->load->model("model_collection");
        
        $this->load->library('form_validation');

        $this->form_validation->set_rules("session_user_id","User_id","xss_clean|trim"); 
        $this->form_validation->set_rules("last_id","Last_id","required|xss_clean|trim");
        $this->form_validation->set_rules("genre","genre","required|xss_clean|trim");
        
        if($this->form_validation->run()){
        
            $last_id = $this->input->post('last_id');
            $genre = $this->input->post('genre');
            
            
            $result_set = $this->model_collection->get_tracks_by_genre($last_id,$genre);
            
            
            //print_r($result_set);
            
            if($result_set != null){
                
                $response['log'] = "Success";
                $response['success'] = 1;
                
                $response['tracks'] = array();
                
                foreach($result_set as $row){
                   $track_id = $row->track_id;
                   
                   $data = array(
                     'track_id' => $row->track_id,
                     'collection_id' => $row->collection_id,
                     'collection_name' => $row->collection_title,
                     'listens' => $row->listens,
                     'likes' => $row->likes,
                     'title' => $row->trackname,
                     'username' => $row->name,
                     'genre' => $row->track_genre,
                     'audio_file' => $row->audio_file,
                     'downloadable' => $row->downloadable,
                     'featured' => $row->people,
                     'album_art' => $row->album_art, 
                     'comments_num' => $row->comments_num,
                     'liked' => $this->model_collection->check_like_exists($track_id)
                   );
                        
                   $response['tracks'] = array_merge($response['tracks'],array($data)); 
                }
                    
            
            }else{
              if($last_id==1){
                $response['log'] = "No Tracks";
              }else $response['log'] = "No More Tracks";
                $response['success'] = 0;
            }
        }else{
        
            $response['log'] = "Incomplete Data";
            $response['success'] = 0;
        }
        echo json_encode($response); 
    }
    
    public function fetch_user_tracks(){
    
        $this->webHeaders();
        $response = array();
        
        $this->load->model("model_collection");
        
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules("session_user_id","User_id","xss_clean|trim"); 
        $this->form_validation->set_rules("last_id","Last_id","required|xss_clean|trim");
        $this->form_validation->set_rules("user_id","User_id","required|xss_clean|trim");
        
        if($this->form_validation->run()){
        
            $last_id = $this->input->post('last_id');
            $user_id = $this->input->post('user_id');
            
            $result_set = $this->model_collection->get_user_tracks($user_id,$last_id);
            
            if($result_set!=null){
            
                $count = 1;
                $response['log'] = "Success";
                $response['success'] = 1;
                
                foreach($result_set as $row){
                            
                   $response['tracks'][$count]['track_id'] = $row->track_id;
                   $response['tracks'][$count]['collection_id'] =  $row->collection_id;
                   $response['tracks'][$count]['collection_name'] =  $row->collection_title;
                      
                   $response['tracks'][$count]['listens'] = $row->listens;
                   $response['tracks'][$count]['likes'] = $row->likes;
                   $response['tracks'][$count]['title'] = $row->trackname;
                   $response['tracks'][$count]['username'] = $row->name;
                   $response['tracks'][$count]['genre'] = $row->track_genre;
                   $response['tracks'][$count]['audio_file'] = $row->audio_file;
                   $response['tracks'][$count]['downloadable']= $row->downloadable;
                   $response['tracks'][$count]['featured'] = $row->people;
                   $response['tracks'][$count]['album_art'] = $row->album_art;     
                   $response['tracks'][$count]['comments_num'] = $row->comments_num;
                   
                   $track_id = $row->track_id;
                   $response['tracks'][$count]['liked'] = $this->model_collection->check_like_exists($track_id);                
                    $count++;
                }
                
            }else{
              if($last_id==1){
                $response['log'] = "No Tracks";
              }else $response['log'] = "No More Tracks";
                $response['success'] = 0;
            }
            
            
        }else{
        
            $response['log'] = "Incomplete Data";
            $response['success'] = 0;
        }
        echo json_encode($response);    
    }
    
    public function ns_fetch_user_tracks(){
    
        $this->webHeaders();
        $response = array();
        
        $this->load->model("model_collection");
        
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules("session_user_id","User_id","xss_clean|trim"); 
        $this->form_validation->set_rules("last_id","Last_id","required|xss_clean|trim");
        $this->form_validation->set_rules("user_id","User_id","required|xss_clean|trim");
        
        if($this->form_validation->run()){
        
            $last_id = $this->input->post('last_id');
            $user_id = $this->input->post('user_id');
            
            $result_set = $this->model_collection->get_user_tracks($user_id,$last_id);
            
            if($result_set!=null){
            
                $response['log'] = "Success";
                $response['success'] = 1;
                
                $response['tracks'] = array();
                
                foreach($result_set as $row){
                   $track_id = $row->track_id;
                   
                   $data = array(
                     'track_id' => $row->track_id,
                     'collection_id' => $row->collection_id,
                     'collection_name' => $row->collection_title,
                     'listens' => $row->listens,
                     'likes' => $row->likes,
                     'title' => $row->trackname,
                     'username' => $row->name,
                     'genre' => $row->track_genre,
                     'audio_file' => $row->audio_file,
                     'downloadable' => $row->downloadable,
                     'featured' => $row->people,
                     'album_art' => $row->album_art, 
                     'comments_num' => $row->comments_num,
                     'liked' => $this->model_collection->check_like_exists($track_id)
                   );
                        
                   $response['tracks'] = array_merge($response['tracks'],array($data)); 
                }
                
            }else{
              if($last_id==1){
                $response['log'] = "No Tracks";
              }else $response['log'] = "No More Tracks";
                $response['success'] = 0;
            }
            
            
        }else{
        
            $response['log'] = "Incomplete Data";
            $response['success'] = 0;
        }
        echo json_encode($response);    
    }
    
    public function fetch_user_tracks_by_listens(){
    
        $this->webHeaders();
        $response = array();
        
        $this->load->model("model_collection");
        
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules("session_user_id","User_id","xss_clean|trim"); 
        $this->form_validation->set_rules("last_id","Last_id","required|xss_clean|trim");
        $this->form_validation->set_rules("user_id","User_id","required|xss_clean|trim");
        $this->form_validation->set_rules("last_listens","Last_listens ","required|xss_clean|trim");
        if($this->form_validation->run()){
        
            $last_id = $this->input->post('last_id');
            $user_id = $this->input->post('user_id');
            $last_listens = $this->input->post('last_listens');
            
            $result_set = $this->model_collection->get_user_tracks_by_listens($user_id,$last_id,$last_listens);
            
            if($result_set!=null){
            
                $count = 1;
                $response['log'] = "Success";
                $response['success'] = 1;
                
                foreach($result_set as $row){
                            
                   $response['tracks'][$count]['track_id'] = $row->track_id;
                   $response['tracks'][$count]['collection_id'] =  $row->collection_id;
                   $response['tracks'][$count]['collection_name'] =  $row->collection_title;
                      
                   $response['tracks'][$count]['listens'] = $row->listens;
                   $response['tracks'][$count]['likes'] = $row->likes;
                   $response['tracks'][$count]['title'] = $row->trackname;
                   $response['tracks'][$count]['username'] = $row->name;
                   $response['tracks'][$count]['genre'] = $row->track_genre;
                   $response['tracks'][$count]['audio_file'] = $row->audio_file;
                   $response['tracks'][$count]['downloadable']= $row->downloadable;
                   $response['tracks'][$count]['featured'] = $row->people;
                   $response['tracks'][$count]['album_art'] = $row->album_art;     
                   $response['tracks'][$count]['comments_num'] = $row->comments_num;
                   
                   $track_id = $row->track_id;
                   $response['tracks'][$count]['liked'] = $this->model_collection->check_like_exists($track_id);                       
                    $count++;
                }
                
            }else{
                
              if($last_id==1){
                $response['log'] = "No Tracks";
              }else $response['log'] = "No More Tracks";
                $response['success'] = 0;
            
            }
            
        }else{        
            $response['log'] = "Incomplete Data";
            $response['success'] = 0;
        }
        echo json_encode($response);    
    }
    
    public function ns_fetch_user_tracks_by_listens(){

        $this->webHeaders();
        $response = array();
        
        $this->load->model("model_collection");
        
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules("session_user_id","User_id","xss_clean|trim"); 
        $this->form_validation->set_rules("last_id","Last_id","required|xss_clean|trim");
        $this->form_validation->set_rules("user_id","User_id","required|xss_clean|trim");
        $this->form_validation->set_rules("last_listens","Last_listens ","required|xss_clean|trim");
        if($this->form_validation->run()){
        
            $last_id = $this->input->post('last_id');
            $user_id = $this->input->post('user_id');
            $last_listens = $this->input->post('last_listens');
            
            $result_set = $this->model_collection->get_user_tracks_by_listens($user_id,$last_id,$last_listens);
            
            if($result_set!=null){
            
                $count = 1;
                $response['log'] = "Success";
                $response['success'] = 1;
                
                $response['tracks'] = array();
                
                foreach($result_set as $row){
                   $track_id = $row->track_id;
                   
                   $data = array(
                     'track_id' => $row->track_id,
                     'collection_id' => $row->collection_id,
                     'collection_name' => $row->collection_title,
                     'listens' => $row->listens,
                     'likes' => $row->likes,
                     'title' => $row->trackname,
                     'username' => $row->name,
                     'genre' => $row->track_genre,
                     'audio_file' => $row->audio_file,
                     'downloadable' => $row->downloadable,
                     'featured' => $row->people,
                     'album_art' => $row->album_art, 
                     'comments_num' => $row->comments_num,
                     'liked' => $this->model_collection->check_like_exists($track_id)
                   );
                        
                   $response['tracks'] = array_merge($response['tracks'],array($data)); 
                }
                
            }else{
                
              if($last_id==1){
                $response['log'] = "No Tracks";
              }else $response['log'] = "No More Tracks";
                $response['success'] = 0;
            
            }
            
        }else{        
            $response['log'] = "Incomplete Data";
            $response['success'] = 0;
        }
        echo json_encode($response);    
    }    
    
    public function fetch_user_tracks_by_genre(){

        $this->webHeaders();
        $response = array();
        
        $this->load->model("model_collection");
        
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules("session_user_id","User_id","xss_clean|trim"); 
        $this->form_validation->set_rules("last_id","Last_id","required|xss_clean|trim");
        $this->form_validation->set_rules("user_id","User_id","required|xss_clean|trim");
        $this->form_validation->set_rules("genre","Genre ","required|xss_clean|trim");
        if($this->form_validation->run()){
        
            $last_id = $this->input->post('last_id');
            $user_id = $this->input->post('user_id');
            $genre = $this->input->post('genre');
            
            $result_set = $this->model_collection->get_user_tracks_by_genre($user_id,$last_id,$genre);
            
            if($result_set!=null){
            
                $count = 1;
                $response['log'] = "Success";
                $response['success'] = 1;
                
                foreach($result_set as $row){
                            
                   $response['tracks'][$count]['track_id'] = $row->track_id;
                   $response['tracks'][$count]['collection_id'] =  $row->collection_id;
                   $response['tracks'][$count]['collection_name'] =  $row->collection_title;
                      
                   $response['tracks'][$count]['listens'] = $row->listens;
                   $response['tracks'][$count]['likes'] = $row->likes;
                   $response['tracks'][$count]['title'] = $row->trackname;
                   $response['tracks'][$count]['username'] = $row->name;
                   $response['tracks'][$count]['genre'] = $row->track_genre;
                   $response['tracks'][$count]['audio_file'] = $row->audio_file;
                   $response['tracks'][$count]['downloadable']= $row->downloadable;
                   $response['tracks'][$count]['featured'] = $row->people;
                   $response['tracks'][$count]['album_art'] = $row->album_art;     
                   $response['tracks'][$count]['comments_num'] = $row->comments_num;
                   
                   $track_id = $row->track_id;
                   $response['tracks'][$count]['liked'] = $this->model_collection->check_like_exists($track_id);                         
                    $count++;
                }
                
            }else{
                
              if($last_id==1){
                $response['log'] = "No Tracks";
              }else $response['log'] = "No More Tracks";
                $response['success'] = 0;
            
            }
            
        }else{        
            $response['log'] = "Incomplete Data";
            $response['success'] = 0;
        }
        echo json_encode($response);         
    }    
    
    public function ns_fetch_user_tracks_by_genre(){      
        $this->webHeaders();
        $response = array();
        
        $this->load->model("model_collection");
        
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules("session_user_id","User_id","xss_clean|trim"); 
        $this->form_validation->set_rules("last_id","Last_id","required|xss_clean|trim");
        $this->form_validation->set_rules("user_id","User_id","required|xss_clean|trim");
        $this->form_validation->set_rules("genre","Genre ","required|xss_clean|trim");
        if($this->form_validation->run()){
        
            $last_id = $this->input->post('last_id');
            $user_id = $this->input->post('user_id');
            $genre = $this->input->post('genre');
            
            $result_set = $this->model_collection->get_user_tracks_by_genre($user_id,$last_id,$genre);
            
            if($result_set!=null){
                
                $response['log'] = "Success";
                $response['success'] = 1;
                
                $response['tracks'] = array();
                
                foreach($result_set as $row){
                   $track_id = $row->track_id;
                   
                   $data = array(
                     'track_id' => $row->track_id,
                     'collection_id' => $row->collection_id,
                     'collection_name' => $row->collection_title,
                     'listens' => $row->listens,
                     'likes' => $row->likes,
                     'title' => $row->trackname,
                     'username' => $row->name,
                     'genre' => $row->track_genre,
                     'audio_file' => $row->audio_file,
                     'downloadable' => $row->downloadable,
                     'featured' => $row->people,
                     'album_art' => $row->album_art, 
                     'comments_num' => $row->comments_num,
                     'liked' => $this->model_collection->check_like_exists($track_id)
                   );
                        
                   $response['tracks'] = array_merge($response['tracks'],array($data)); 
                }
                
            }else{
                
              if($last_id==1){
                $response['log'] = "No Tracks";
              }else $response['log'] = "No More Tracks";
                $response['success'] = 0;
            
            }
            
        }else{        
            $response['log'] = "Incomplete Data";
            $response['success'] = 0;
        }
        echo json_encode($response);         
    }     
    
    public function fetch_user_tracks_by_likes(){
        $this->webHeaders();
        $response = array();
        
        $this->load->model("model_collection");
        
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules("session_user_id","User_id","xss_clean|trim"); 
        $this->form_validation->set_rules("last_id","Last_id","required|xss_clean|trim");
        $this->form_validation->set_rules("user_id","User_id","required|xss_clean|trim");
        $this->form_validation->set_rules("last_likes","Last_likes ","required|xss_clean|trim");
        if($this->form_validation->run()){
        
            $last_id = $this->input->post('last_id');
            $user_id = $this->input->post('user_id');
            $last_likes = $this->input->post('last_likes');
            
            $result_set = $this->model_collection->get_user_tracks_by_likes($user_id,$last_id,$last_likes);
            
            if($result_set!=null){
            
                $count = 1;
                $response['log'] = "Success";
                $response['success'] = 1;
                
                foreach($result_set as $row){
                            
                   $response['tracks'][$count]['track_id'] = $row->track_id;
                   $response['tracks'][$count]['collection_id'] =  $row->collection_id;
                   $response['tracks'][$count]['collection_name'] =  $row->collection_title;
                      
                   $response['tracks'][$count]['listens'] = $row->listens;
                   $response['tracks'][$count]['likes'] = $row->likes;
                   $response['tracks'][$count]['title'] = $row->trackname;
                   $response['tracks'][$count]['username'] = $row->name;
                   $response['tracks'][$count]['genre'] = $row->track_genre;
                   $response['tracks'][$count]['audio_file'] = $row->audio_file;
                   $response['tracks'][$count]['downloadable']= $row->downloadable;
                   $response['tracks'][$count]['featured'] = $row->people;
                   $response['tracks'][$count]['album_art'] = $row->album_art;     
                   $response['tracks'][$count]['comments_num'] = $row->comments_num;
                   
                   $track_id = $row->track_id;
                   $response['tracks'][$count]['liked'] = $this->model_collection->check_like_exists($track_id);                        
                    $count++;
                }
                
            }else{
                
              if($last_id==1){
                $response['log'] = "No Tracks";
              }else $response['log'] = "No More Tracks";
                $response['success'] = 0;
            
            }
            
        }else{        
            $response['log'] = "Incomplete Data";
            $response['success'] = 0;
        }
        echo json_encode($response);    
    }
    
    public function ns_fetch_user_tracks_by_likes(){     
        $this->webHeaders();
        $response = array();
        
        $this->load->model("model_collection");
        
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules("session_user_id","User_id","xss_clean|trim"); 
        $this->form_validation->set_rules("last_id","Last_id","required|xss_clean|trim");
        $this->form_validation->set_rules("user_id","User_id","required|xss_clean|trim");
        $this->form_validation->set_rules("last_likes","Last_likes ","required|xss_clean|trim");
        if($this->form_validation->run()){
        
            $last_id = $this->input->post('last_id');
            $user_id = $this->input->post('user_id');
            $last_likes = $this->input->post('last_likes');
            
            $result_set = $this->model_collection->get_user_tracks_by_likes($user_id,$last_id,$last_likes);
            
            if($result_set!=null){
            
                //$count = 1;
                $response['log'] = "Success";
                $response['success'] = 1;
                
                $response['tracks'] = array();
                
                foreach($result_set as $row){
                   $track_id = $row->track_id;
                   
                   $data = array(
                     'track_id' => $row->track_id,
                     'collection_id' => $row->collection_id,
                     'collection_name' => $row->collection_title,
                     'listens' => $row->listens,
                     'likes' => $row->likes,
                     'title' => $row->trackname,
                     'username' => $row->name,
                     'genre' => $row->track_genre,
                     'audio_file' => $row->audio_file,
                     'downloadable' => $row->downloadable,
                     'featured' => $row->people,
                     'album_art' => $row->album_art, 
                     'comments_num' => $row->comments_num,
                     'liked' => $this->model_collection->check_like_exists($track_id)
                   );
                        
                   $response['tracks'] = array_merge($response['tracks'],array($data)); 
                }
                
            }else{
                
              if($last_id==1){
                $response['log'] = "No Tracks";
              }else $response['log'] = "No More Tracks";
                $response['success'] = 0;
            
            }
            
        }else{        
            $response['log'] = "Incomplete Data";
            $response['success'] = 0;
        }
        echo json_encode($response);    
    }    
    
    public function fetch_collection_tracks_by_latest(){
         
        $this->webHeaders();
        $response = array();
        
        $this->load->model("model_collection");
        
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules("session_user_id","User_id","xss_clean|trim"); 
        $this->form_validation->set_rules("last_id","Last_id","required|xss_clean|trim");
        $this->form_validation->set_rules("collection_id","Collection_id","required|xss_clean|trim");
        
        if($this->form_validation->run()){
            $last_id = $this->input->post('last_id');
            $collection_id = $this->input->post('collection_id');
            
            $result_set = $this->model_collection->get_collection_tracks($collection_id,$last_id);
            
            if($result_set!=null){
            
                $count = 1;
                $response['log'] = "Success";
                $response['success'] = 1;
                
                foreach($result_set as $row){
                            
                   $response['tracks'][$count]['track_id'] = $row->track_id;
                   $response['tracks'][$count]['collection_id'] =  $row->collection_id;
                   $response['tracks'][$count]['collection_name'] =  $row->collection_title;
                      
                   $response['tracks'][$count]['listens'] = $row->listens;
                   $response['tracks'][$count]['likes'] = $row->likes;
                   $response['tracks'][$count]['title'] = $row->trackname;
                   $response['tracks'][$count]['username'] = $row->name;
                   $response['tracks'][$count]['genre'] = $row->track_genre;
                   $response['tracks'][$count]['audio_file'] = $row->audio_file;
                   $response['tracks'][$count]['downloadable']= $row->downloadable;
                   $response['tracks'][$count]['featured'] = $row->people;
                   $response['tracks'][$count]['album_art'] = $row->album_art;     
                   $response['tracks'][$count]['comments_num'] = $row->comments_num;
                   
                   $track_id = $row->track_id;
                   $response['tracks'][$count]['liked'] = $this->model_collection->check_like_exists($track_id);                        
                    $count++;
                }                
                
            }else{
            
              if($last_id==1){
                $response['log'] = "No Tracks";
              }else $response['log'] = "No More Tracks";
                $response['success'] = 0;
                
            }
                    
        }else{
        
            $response['log'] = "Incomplete Data";
            $response['success'] = 0;
        }
        echo json_encode($response);  
        
    } 

    public function ns_fetch_collection_tracks_by_latest(){
        
        $this->webHeaders();
        $response = array();
        
        $this->load->model("model_collection");
        
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules("session_user_id","User_id","xss_clean|trim"); 
        $this->form_validation->set_rules("last_id","Last_id","required|xss_clean|trim");
        $this->form_validation->set_rules("collection_id","Collection_id","required|xss_clean|trim");
        
        if($this->form_validation->run()){
            $last_id = $this->input->post('last_id');
            $collection_id = $this->input->post('collection_id');
            
            $result_set = $this->model_collection->get_collection_tracks($collection_id,$last_id);
            
            if($result_set!=null){
            
                $response['log'] = "Success";
                $response['success'] = 1;
                
                $response['tracks'] = array();
                
                foreach($result_set as $row){
                   $track_id = $row->track_id;
                   
                   $data = array(
                     'track_id' => $row->track_id,
                     'collection_id' => $row->collection_id,
                     'collection_name' => $row->collection_title,
                     'listens' => $row->listens,
                     'likes' => $row->likes,
                     'title' => $row->trackname,
                     'username' => $row->name,
                     'genre' => $row->track_genre,
                     'audio_file' => $row->audio_file,
                     'downloadable' => $row->downloadable,
                     'featured' => $row->people,
                     'album_art' => $row->album_art, 
                     'comments_num' => $row->comments_num,
                     'liked' => $this->model_collection->check_like_exists($track_id)
                   );
                        
                   $response['tracks'] = array_merge($response['tracks'],array($data)); 
                }               
                
            }else{
            
              if($last_id==1){
                $response['log'] = "No Tracks";
              }else $response['log'] = "No More Tracks";
                $response['success'] = 0;
                
            }
                    
        }else{
        
            $response['log'] = "Incomplete Data";
            $response['success'] = 0;
        }
        echo json_encode($response);  
        
    } 
    
    public function fetch_collection_tracks_by_listens(){
   
        $this->webHeaders();
        $response = array();
        
        $this->load->model("model_collection");
        
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules("session_user_id","User_id","xss_clean|trim"); 
        $this->form_validation->set_rules("last_id","Last_id","required|xss_clean|trim");
        $this->form_validation->set_rules("last_listens","Last_listens","required|xss_clean|trim");
        $this->form_validation->set_rules("collection_id","Collection_id","required|xss_clean|trim");
        
        if($this->form_validation->run()){
            $last_id = $this->input->post('last_id');
            $collection_id = $this->input->post('collection_id');
            $last_listens = $this->input->post('last_listens');
            
            $result_set = $this->model_collection->get_collection_tracks_by_listens($collection_id,$last_id,$last_listens);
            
            if($result_set!=null){
            
                $count = 1;
                $response['log'] = "Success";
                $response['success'] = 1;
                
                foreach($result_set as $row){
                            
                   $response['tracks'][$count]['track_id'] = $row->track_id;
                   $response['tracks'][$count]['collection_id'] =  $row->collection_id;
                   $response['tracks'][$count]['collection_name'] =  $row->collection_title;
                      
                   $response['tracks'][$count]['listens'] = $row->listens;
                   $response['tracks'][$count]['likes'] = $row->likes;
                   $response['tracks'][$count]['title'] = $row->trackname;
                   $response['tracks'][$count]['username'] = $row->name;
                   $response['tracks'][$count]['genre'] = $row->track_genre;
                   $response['tracks'][$count]['audio_file'] = $row->audio_file;
                   $response['tracks'][$count]['downloadable']= $row->downloadable;
                   $response['tracks'][$count]['featured'] = $row->people;
                   $response['tracks'][$count]['album_art'] = $row->album_art;     
                   $response['tracks'][$count]['comments_num'] = $row->comments_num;
                   
                   $track_id = $row->track_id;
                   $response['tracks'][$count]['liked'] = $this->model_collection->check_like_exists($track_id);                         
                    $count++;
                }                
                
            }else{
            
              if($last_id==1){
                $response['log'] = "No Tracks";
              }else $response['log'] = "No More Tracks";
                $response['success'] = 0;
                
            }
                    
        }else{
        
            $response['log'] = "Incomplete Data";
            $response['success'] = 0;
        }
        echo json_encode($response);  
        
    }
 
    public function ns_fetch_collection_tracks_by_listens(){

        $this->webHeaders();
        $response = array();
        
        $this->load->model("model_collection");
        
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules("session_user_id","User_id","xss_clean|trim"); 
        $this->form_validation->set_rules("last_id","Last_id","required|xss_clean|trim");
        $this->form_validation->set_rules("last_listens","Last_listens","required|xss_clean|trim");
        $this->form_validation->set_rules("collection_id","Collection_id","required|xss_clean|trim");
        
        if($this->form_validation->run()){
            $last_id = $this->input->post('last_id');
            $collection_id = $this->input->post('collection_id');
            $last_listens = $this->input->post('last_listens');
            
            $result_set = $this->model_collection->get_collection_tracks_by_listens($collection_id,$last_id,$last_listens);
            
            if($result_set!=null){
                $response['log'] = "Success";
                $response['success'] = 1;
                
                $response['tracks'] = array();
                
                foreach($result_set as $row){
                   $track_id = $row->track_id;
                   
                   $data = array(
                     'track_id' => $row->track_id,
                     'collection_id' => $row->collection_id,
                     'collection_name' => $row->collection_title,
                     'listens' => $row->listens,
                     'likes' => $row->likes,
                     'title' => $row->trackname,
                     'username' => $row->name,
                     'genre' => $row->track_genre,
                     'audio_file' => $row->audio_file,
                     'downloadable' => $row->downloadable,
                     'featured' => $row->people,
                     'album_art' => $row->album_art, 
                     'comments_num' => $row->comments_num,
                     'liked' => $this->model_collection->check_like_exists($track_id)
                   );
                        
                   $response['tracks'] = array_merge($response['tracks'],array($data)); 
                }               
                
            }else{
            
              if($last_id==1){
                $response['log'] = "No Tracks";
              }else $response['log'] = "No More Tracks";
                $response['success'] = 0;
                
            }
                    
        }else{
        
            $response['log'] = "Incomplete Data";
            $response['success'] = 0;
        }
        echo json_encode($response);  
        
    }
    
    public function fetch_collection_tracks_by_likes(){
   
        $this->webHeaders();
        $response = array();
        
        $this->load->model("model_collection");
        
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules("session_user_id","User_id","xss_clean|trim"); 
        $this->form_validation->set_rules("last_id","Last_id","required|xss_clean|trim");
        $this->form_validation->set_rules("last_likes","Last_likes","required|xss_clean|trim");
        $this->form_validation->set_rules("collection_id","Collection_id","required|xss_clean|trim");
        
        if($this->form_validation->run()){
            $last_id = $this->input->post('last_id');
            $collection_id = $this->input->post('collection_id');
            $last_likes = $this->input->post('last_likes');
            
            $result_set = $this->model_collection->get_collection_tracks_by_likes($collection_id,$last_id,$last_likes);
            
            if($result_set!=null){
            
                $count = 1;
                $response['log'] = "Success";
                $response['success'] = 1;
                
                foreach($result_set as $row){
                            
                   $response['tracks'][$count]['track_id'] = $row->track_id;
                   $response['tracks'][$count]['collection_id'] =  $row->collection_id;
                   $response['tracks'][$count]['collection_name'] =  $row->collection_title;
                      
                   $response['tracks'][$count]['listens'] = $row->listens;
                   $response['tracks'][$count]['likes'] = $row->likes;
                   $response['tracks'][$count]['title'] = $row->trackname;
                   $response['tracks'][$count]['username'] = $row->name;
                   $response['tracks'][$count]['genre'] = $row->track_genre;
                   $response['tracks'][$count]['audio_file'] = $row->audio_file;
                   $response['tracks'][$count]['downloadable']= $row->downloadable;
                   $response['tracks'][$count]['featured'] = $row->people;
                   $response['tracks'][$count]['album_art'] = $row->album_art;     
                   $response['tracks'][$count]['comments_num'] = $row->comments_num;
                   
                   $track_id = $row->track_id;
                   $response['tracks'][$count]['liked'] = $this->model_collection->check_like_exists($track_id);                       
                    $count++;
                }                
                
            }else{
            
              if($last_id==1){
                $response['log'] = "No Tracks";
              }else $response['log'] = "No More Tracks";
                $response['success'] = 0;
                
            }
                    
        }else{
        
            $response['log'] = "Incomplete Data";
            $response['success'] = 0;
        }
        echo json_encode($response);  
        
    }
    
  public function ns_fetch_collection_tracks_by_likes(){
   
        $this->webHeaders();
        $response = array();          
        
        $this->load->model("model_collection");
        
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules("session_user_id","User_id","xss_clean|trim"); 
        $this->form_validation->set_rules("last_id","Last_id","required|xss_clean|trim");
        $this->form_validation->set_rules("last_likes","Last_likes","required|xss_clean|trim");
        $this->form_validation->set_rules("collection_id","Collection_id","required|xss_clean|trim");
        
        if($this->form_validation->run()){
            $last_id = $this->input->post('last_id');
            $collection_id = $this->input->post('collection_id');
            $last_likes = $this->input->post('last_likes');
            
            $result_set = $this->model_collection->get_collection_tracks_by_likes($collection_id,$last_id,$last_likes);
            
            if($result_set!=null){
            
                $count = 1;
                $response['log'] = "Success";
                $response['success'] = 1;
                
                $response['tracks'] = array();
                
                foreach($result_set as $row){
                   $track_id = $row->track_id;
                   
                   $data = array(
                     'track_id' => $row->track_id,
                     'collection_id' => $row->collection_id,
                     'collection_name' => $row->collection_title,
                     'listens' => $row->listens,
                     'likes' => $row->likes,
                     'title' => $row->trackname,
                     'username' => $row->name,
                     'genre' => $row->track_genre,
                     'audio_file' => $row->audio_file,
                     'downloadable' => $row->downloadable,
                     'featured' => $row->people,
                     'album_art' => $row->album_art, 
                     'comments_num' => $row->comments_num,
                     'liked' => $this->model_collection->check_like_exists($track_id)
                   );
                        
                   $response['tracks'] = array_merge($response['tracks'],array($data)); 
                }               
                
            }else{
            
              if($last_id==1){
                $response['log'] = "No Tracks";
              }else $response['log'] = "No More Tracks";
                $response['success'] = 0;
                
            }
                    
        }else{
        
            $response['log'] = "Incomplete Data";
            $response['success'] = 0;
        }
        echo json_encode($response);  
        
    }    
    
    public function fetch_collection_tracks_by_genre(){
        
        $this->webHeaders();
        $response = array();
        
        $this->load->model("model_collection");
        
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules("session_user_id","User_id","xss_clean|trim"); 
        $this->form_validation->set_rules("last_id","Last_id","required|xss_clean|trim");
        $this->form_validation->set_rules("collection_id","Collection_id","required|xss_clean|trim");
        $this->form_validation->set_rules("genre","Genre","required|xss_clean|trim");
        
        if($this->form_validation->run()){
            
            $last_id = $this->input->post('last_id');
            $collection_id = $this->input->post('collection_id');
            $genre = $this->input->post('genre');
            
            $result_set = $this->model_collection->get_collection_tracks_by_genre($collection_id,$last_id,$genre);
            
            if($result_set!=null){
            
                $count = 1;
                $response['log'] = "Success";
                $response['success'] = 1;
                
                foreach($result_set as $row){
                            
                   $response['tracks'][$count]['track_id'] = $row->track_id;
                   $response['tracks'][$count]['collection_id'] =  $row->collection_id;
                   $response['tracks'][$count]['collection_name'] =  $row->collection_title;
                      
                   $response['tracks'][$count]['listens'] = $row->listens;
                   $response['tracks'][$count]['likes'] = $row->likes;
                   $response['tracks'][$count]['title'] = $row->trackname;
                   $response['tracks'][$count]['username'] = $row->name;
                   $response['tracks'][$count]['genre'] = $row->track_genre;
                   $response['tracks'][$count]['audio_file'] = $row->audio_file;
                   $response['tracks'][$count]['downloadable']= $row->downloadable;
                   $response['tracks'][$count]['featured'] = $row->people;
                   $response['tracks'][$count]['album_art'] = $row->album_art;     
                   $response['tracks'][$count]['comments_num'] = $row->comments_num;
                   
                   $track_id = $row->track_id;
                   $response['tracks'][$count]['liked'] = $this->model_collection->check_like_exists($track_id);                       
                    $count++;
                }
                
            }else{
                
              if($last_id==1){
                $response['log'] = "No Tracks";
              }else $response['log'] = "No More Tracks";
                $response['success'] = 0;
            
            }
        }else{
        
            $response['log'] = "Incomplete Data";
            $response['success'] = 0;
        }
        echo json_encode($response);     
    }
    
    public function ns_fetch_collection_tracks_by_genre(){
        
        $this->webHeaders();
        $response = array(); 
        
        $this->load->model("model_collection");
        
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules("session_user_id","User_id","xss_clean|trim"); 
        $this->form_validation->set_rules("last_id","Last_id","required|xss_clean|trim");
        $this->form_validation->set_rules("collection_id","Collection_id","required|xss_clean|trim");
        $this->form_validation->set_rules("genre","Genre","required|xss_clean|trim");
        
        if($this->form_validation->run()){
            
            $last_id = $this->input->post('last_id');
            $collection_id = $this->input->post('collection_id');
            $genre = $this->input->post('genre');
            
            $result_set = $this->model_collection->get_collection_tracks_by_genre($collection_id,$last_id,$genre);
            
            if($result_set!=null){
            
                //$count = 1;
                $response['log'] = "Success";
                $response['success'] = 1;
                
                $response['tracks'] = array();
                
                foreach($result_set as $row){
                   $track_id = $row->track_id;
                   
                   $data = array(
                     'track_id' => $row->track_id,
                     'collection_id' => $row->collection_id,
                     'collection_name' => $row->collection_title,
                     'listens' => $row->listens,
                     'likes' => $row->likes,
                     'title' => $row->trackname,
                     'username' => $row->name,
                     'genre' => $row->track_genre,
                     'audio_file' => $row->audio_file,
                     'downloadable' => $row->downloadable,
                     'featured' => $row->people,
                     'album_art' => $row->album_art, 
                     'comments_num' => $row->comments_num,
                     'liked' => $this->model_collection->check_like_exists($track_id)
                   );
                        
                   $response['tracks'] = array_merge($response['tracks'],array($data)); 
                }
                
            }else{
                
              if($last_id==1){
                $response['log'] = "No Tracks";
              }else $response['log'] = "No More Tracks";
                $response['success'] = 0;
            
            }
        }else{
        
            $response['log'] = "Incomplete Data";
            $response['success'] = 0;
        }
        echo json_encode($response);     
    }    
    
    public function fetch_genre_tracks_by_latest(){
        
        $this->webHeaders();
        $response = array();
        
        $this->load->model("model_collection");
        
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules("session_user_id","User_id","xss_clean|trim"); 
        $this->form_validation->set_rules("last_id","Last_id","required|xss_clean|trim");
        $this->form_validation->set_rules("genre","Genre","required|xss_clean|trim");
        

        
        if($this->form_validation->run()){
        
            $last_id = $this->input->post('last_id');
            $genre = $this->input->post('genre');
            
            $result_set = $this->model_collection->get_genre_tracks_by_latest($last_id,$genre);
            
            if($result_set!=null){
            
                $count = 1;
                $response['log'] = "Success";
                $response['success'] = 1;
                
                foreach($result_set as $row){
                            
                   $response['tracks'][$count]['track_id'] = $row->track_id;
                   $response['tracks'][$count]['collection_id'] =  $row->collection_id;
                   $response['tracks'][$count]['collection_name'] =  $row->collection_title;
                      
                   $response['tracks'][$count]['listens'] = $row->listens;
                   $response['tracks'][$count]['likes'] = $row->likes;
                   $response['tracks'][$count]['title'] = $row->trackname;
                   $response['tracks'][$count]['username'] = $row->name;
                   $response['tracks'][$count]['genre'] = $row->track_genre;
                   $response['tracks'][$count]['audio_file'] = $row->audio_file;
                   $response['tracks'][$count]['downloadable']= $row->downloadable;
                   $response['tracks'][$count]['featured'] = $row->people;
                   $response['tracks'][$count]['album_art'] = $row->album_art;     
                   $response['tracks'][$count]['comments_num'] = $row->comments_num;
                   
                   $track_id = $row->track_id;
                   $response['tracks'][$count]['liked'] = $this->model_collection->check_like_exists($track_id);                        
                    $count++;
                }                
                
            }else{
            
              if($last_id==1){
                $response['log'] = "No Tracks";
              }else $response['log'] = "No More Tracks";
                $response['success'] = 0;
                
            }    
            
        }else{
        
            $response['log'] = "Incomplete Data";
            $response['success'] = 0;
        }
        echo json_encode($response); 
    }
    
   public function ns_fetch_genre_tracks_by_latest(){
        
        $this->webHeaders();
        $response = array();
        
        $this->load->model("model_collection");
        
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules("session_user_id","User_id","xss_clean|trim"); 
        $this->form_validation->set_rules("last_id","Last_id","required|xss_clean|trim");
        $this->form_validation->set_rules("genre","Genre","required|xss_clean|trim");
        

        
        if($this->form_validation->run()){
        
            $last_id = $this->input->post('last_id');
            $genre = $this->input->post('genre');
            
            $result_set = $this->model_collection->get_genre_tracks_by_latest($last_id,$genre);
            
            if($result_set!=null){
            
                $count = 1;
                $response['log'] = "Success";
                $response['success'] = 1;
                
                $response['tracks'] = array();
                
                foreach($result_set as $row){
                   $track_id = $row->track_id;
                   
                   $data = array(
                     'track_id' => $row->track_id,
                     'collection_id' => $row->collection_id,
                     'collection_name' => $row->collection_title,
                     'listens' => $row->listens,
                     'likes' => $row->likes,
                     'title' => $row->trackname,
                     'username' => $row->name,
                     'genre' => $row->track_genre,
                     'audio_file' => $row->audio_file,
                     'downloadable' => $row->downloadable,
                     'featured' => $row->people,
                     'album_art' => $row->album_art, 
                     'comments_num' => $row->comments_num,
                     'liked' => $this->model_collection->check_like_exists($track_id)
                   );
                        
                   $response['tracks'] = array_merge($response['tracks'],array($data)); 
                }             
                
            }else{
            
              if($last_id==1){
                $response['log'] = "No Tracks";
              }else $response['log'] = "No More Tracks";
                $response['success'] = 0;
                
            }    
            
        }else{
        
            $response['log'] = "Incomplete Data";
            $response['success'] = 0;
        }
        echo json_encode($response); 
    }
    
    public function fetch_genre_tracks_by_listens(){
        
        $this->webHeaders();
        $response = array();
        
        $this->load->model("model_collection");
        
        $this->load->library('form_validation');        
        
        $this->form_validation->set_rules("session_user_id","User_id","xss_clean|trim"); 
        $this->form_validation->set_rules("last_id","Last_id","required|xss_clean|trim");
        $this->form_validation->set_rules("last_listens","Last_listens","required|xss_clean|trim");
        $this->form_validation->set_rules("genre","Genre","required|xss_clean|trim");
        
        if($this->form_validation->run()){
            $last_id = $this->input->post('last_id');
            $last_listens = $this->input->post('last_listens');
            $genre = $this->input->post('genre');
            
            $result_set = $this->model_collection->get_genre_tracks_by_listens($last_id,$last_listens,$genre);
            
            if($result_set!=null){
            
                $count = 1;
                $response['log'] = "Success";
                $response['success'] = 1;
                
                foreach($result_set as $row){
                            
                   $response['tracks'][$count]['track_id'] = $row->track_id;
                   $response['tracks'][$count]['collection_id'] =  $row->collection_id;
                   $response['tracks'][$count]['collection_name'] =  $row->collection_title;
                      
                   $response['tracks'][$count]['listens'] = $row->listens;
                   $response['tracks'][$count]['likes'] = $row->likes;
                   $response['tracks'][$count]['title'] = $row->trackname;
                   $response['tracks'][$count]['username'] = $row->name;
                   $response['tracks'][$count]['genre'] = $row->track_genre;
                   $response['tracks'][$count]['audio_file'] = $row->audio_file;
                   $response['tracks'][$count]['downloadable']= $row->downloadable;
                   $response['tracks'][$count]['featured'] = $row->people;
                   $response['tracks'][$count]['album_art'] = $row->album_art;     
                   $response['tracks'][$count]['comments_num'] = $row->comments_num;
                   
                   $track_id = $row->track_id;
                   $response['tracks'][$count]['liked'] = $this->model_collection->check_like_exists($track_id);                        
                    $count++;
                }                
                
            }else{
            
              if($last_id==1){
                $response['log'] = "No Tracks";
              }else $response['log'] = "No More Tracks";
                $response['success'] = 0;
                
            }    
        }else{
        
            $response['log'] = "Incomplete Data";
            $response['success'] = 0;
        }
        echo json_encode($response);    
    }
    
    public function ns_fetch_genre_tracks_by_listens(){
        
        $this->webHeaders();
        $response = array();
        
        $this->load->model("model_collection");
        
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules("session_user_id","User_id","xss_clean|trim"); 
        $this->form_validation->set_rules("last_id","Last_id","required|xss_clean|trim");
        $this->form_validation->set_rules("last_listens","Last_listens","required|xss_clean|trim");
        $this->form_validation->set_rules("genre","Genre","required|xss_clean|trim");
        
        if($this->form_validation->run()){
            $last_id = $this->input->post('last_id');
            $last_listens = $this->input->post('last_listens');
            $genre = $this->input->post('genre');
            
            $result_set = $this->model_collection->get_genre_tracks_by_listens($last_id,$last_listens,$genre);
            
            if($result_set!=null){
            
                $count = 1;
                $response['log'] = "Success";
                $response['success'] = 1;
                
                $response['tracks'] = array();
                
                foreach($result_set as $row){
                   $track_id = $row->track_id;
                   
                   $data = array(
                     'track_id' => $row->track_id,
                     'collection_id' => $row->collection_id,
                     'collection_name' => $row->collection_title,
                     'listens' => $row->listens,
                     'likes' => $row->likes,
                     'title' => $row->trackname,
                     'username' => $row->name,
                     'genre' => $row->track_genre,
                     'audio_file' => $row->audio_file,
                     'downloadable' => $row->downloadable,
                     'featured' => $row->people,
                     'album_art' => $row->album_art, 
                     'comments_num' => $row->comments_num,
                     'liked' => $this->model_collection->check_like_exists($track_id)
                   );
                        
                   $response['tracks'] = array_merge($response['tracks'],array($data)); 
                }                
                
            }else{
            
              if($last_id==1){
                $response['log'] = "No Tracks";
              }else $response['log'] = "No More Tracks";
                $response['success'] = 0;
                
            }    
        }else{
        
            $response['log'] = "Incomplete Data";
            $response['success'] = 0;
        }
        echo json_encode($response);    
    }    
    
    public function fetch_genre_tracks_by_likes(){
        
        $this->webHeaders();
        $response = array();       
        
        $this->load->model("model_collection");
        
        $this->load->library('form_validation');
 
        $this->form_validation->set_rules("session_user_id","User_id","xss_clean|trim"); 
        $this->form_validation->set_rules("last_id","Last_id","required|xss_clean|trim");
        $this->form_validation->set_rules("last_likes","Last_likes","required|xss_clean|trim");
        $this->form_validation->set_rules("genre","Genre","required|xss_clean|trim");
        
        if($this->form_validation->run()){
            $last_id = $this->input->post('last_id');
            $last_likes = $this->input->post('last_likes');
            $genre = $this->input->post('genre');
            
            $result_set = $this->model_collection->get_genre_tracks_by_likes($last_id,$last_likes,$genre);
            
            if($result_set!=null){
            
                $count = 1;
                $response['log'] = "Success";
                $response['success'] = 1;
                
                foreach($result_set as $row){
                            
                   $response['tracks'][$count]['track_id'] = $row->track_id;
                   $response['tracks'][$count]['collection_id'] =  $row->collection_id;
                   $response['tracks'][$count]['collection_name'] =  $row->collection_title;
                      
                   $response['tracks'][$count]['listens'] = $row->listens;
                   $response['tracks'][$count]['likes'] = $row->likes;
                   $response['tracks'][$count]['title'] = $row->trackname;
                   $response['tracks'][$count]['username'] = $row->name;
                   $response['tracks'][$count]['genre'] = $row->track_genre;
                   $response['tracks'][$count]['audio_file'] = $row->audio_file;
                   $response['tracks'][$count]['downloadable']= $row->downloadable;
                   $response['tracks'][$count]['featured'] = $row->people;
                   $response['tracks'][$count]['album_art'] = $row->album_art;     
                   $response['tracks'][$count]['comments_num'] = $row->comments_num;
                   
                   $track_id = $row->track_id;
                   $response['tracks'][$count]['liked'] = $this->model_collection->check_like_exists($track_id);                         
                    $count++;
                }                
                
            }else{
            
              if($last_id==1){
                $response['log'] = "No Tracks";
              }else $response['log'] = "No More Tracks";
                $response['success'] = 0;
                
            }    
        }else{
        
            $response['log'] = "Incomplete Data";
            $response['success'] = 0;
        }
        echo json_encode($response); 
    }
    
    public function ns_fetch_genre_tracks_by_likes(){
        
        $this->webHeaders();
        $response = array();
        
        $this->load->model("model_collection");
        
        $this->load->library('form_validation');
 
        $this->form_validation->set_rules("session_user_id","User_id","xss_clean|trim"); 
        $this->form_validation->set_rules("last_id","Last_id","required|xss_clean|trim");
        $this->form_validation->set_rules("last_likes","Last_likes","required|xss_clean|trim");
        $this->form_validation->set_rules("genre","Genre","required|xss_clean|trim");
        
        if($this->form_validation->run()){
            $last_id = $this->input->post('last_id');
            $last_likes = $this->input->post('last_likes');
            $genre = $this->input->post('genre');
            
            $result_set = $this->model_collection->get_genre_tracks_by_likes($last_id,$last_likes,$genre);
            
            if($result_set!=null){
            
                $response['log'] = "Success";
                $response['success'] = 1;
                
                $response['tracks'] = array();
                
                foreach($result_set as $row){
                   $track_id = $row->track_id;
                   
                   $data = array(
                     'track_id' => $row->track_id,
                     'collection_id' => $row->collection_id,
                     'collection_name' => $row->collection_title,
                     'listens' => $row->listens,
                     'likes' => $row->likes,
                     'title' => $row->trackname,
                     'username' => $row->name,
                     'genre' => $row->track_genre,
                     'audio_file' => $row->audio_file,
                     'downloadable' => $row->downloadable,
                     'featured' => $row->people,
                     'album_art' => $row->album_art, 
                     'comments_num' => $row->comments_num,
                     'liked' => $this->model_collection->check_like_exists($track_id)
                   );
                        
                   $response['tracks'] = array_merge($response['tracks'],array($data)); 
                }                
                
            }else{
            
              if($last_id==1){
                $response['log'] = "No Tracks";
              }else $response['log'] = "No More Tracks";
                $response['success'] = 0;
                
            }    
        }else{
        
            $response['log'] = "Incomplete Data";
            $response['success'] = 0;
        }
        echo json_encode($response); 
    }    
    
    public function fetch_user_followers(){
        
        $this->webHeaders();
        $response = array();
        
        $this->load->model("model_accounts");
        
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules("session_user_id","User_id","xss_clean|trim"); 
        $this->form_validation->set_rules("last_id","Last_id","required|xss_clean|trim");
        $this->form_validation->set_rules("user_id","User_id","required|xss_clean|trim");
        
        if($this->form_validation->run()){
        
            $user_id = $this->input->post('user_id');
            $last_id = $this->input->post('last_id');
            
            $result_set = $this->model_accounts->get_user_followers($user_id,$last_id);
            
            
            if($result_set!=null){
            
                $response['log'] = "Success";
                $response['success'] = 1;
                
                $count = 1;
                
                foreach($result_set as $row){
        
                    $response['followers'][$count]['follow_id'] = $row->follow_id;
                    $response['followers'][$count]['username'] = $row->name;
                    $response['followers'][$count]['user_id'] = $row->user_id;
                    $response['followers'][$count]['fullname'] = $row->fullname;
                    $response['followers'][$count]['user_type'] = $row->type;
                    $response['followers'][$count]['bio'] = $row->bio;
                    $response['followers'][$count]['wallpaper'] = $row->wallpaper;
                    $user_id = $row->user_id;

                    $response['followers'][$count]['following'] = $this->model_accounts->check_follow_exists($user_id);                        
                    $count++;
                }
                
                
            }else{
                
              if($last_id==1){
                $response['log'] = "No Followers";
              }else $response['log'] = "No More Followers";
                $response['success'] = 0;  
                    
            }
            
            
        }else{
        
            $response['log'] = "Incomplete Data";
            $response['success'] = 0;
        }
        echo json_encode($response);
    }
    
     public function ns_fetch_user_followers(){
        
        $this->webHeaders();
        $response = array(); 
        
        $this->load->model("model_accounts");
        
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules("session_user_id","User_id","xss_clean|trim"); 
        $this->form_validation->set_rules("last_id","Last_id","required|xss_clean|trim");
        $this->form_validation->set_rules("user_id","User_id","required|xss_clean|trim");
        
        if($this->form_validation->run()){
        
            $user_id = $this->input->post('user_id');
            $last_id = $this->input->post('last_id');
            
            $result_set = $this->model_accounts->get_user_followers($user_id,$last_id);
            
            
            if($result_set!=null){
            
                $response['log'] = "Success";
                $response['success'] = 1;
                
                $response['followers'] = array();
                foreach($result_set as $row){
                       
                    $user_id = $row->user_id;
                    $data = array(
                        'follow_id' => $row->follow_id,
                        'username' => $row->name,
                        'user_id' => $row->user_id,
                        'fullname' => $row->fullname,
                        'user_type' => $row->type,
                        'bio' => $row->bio,
                        'wallpaper' => $row->wallpaper,
                        'following' => $this->model_accounts->check_follow_exists($user_id)
                    );
                    
                    $response['followers'] = array_merge($response['followers'],array($data));
                }
                
                
            }else{
                
              if($last_id==1){
                $response['log'] = "No Followers";
              }else $response['log'] = "No More Followers";
                $response['success'] = 0;  
                    
            }
            
            
        }else{
        
            $response['log'] = "Incomplete Data";
            $response['success'] = 0;
        }
        echo json_encode($response);
    }   
    
    public function fetch_user_following(){
        
        $this->webHeaders();
        $response = array();
        
        $this->load->model("model_accounts");
        
        $this->load->library('form_validation');
 
        $this->form_validation->set_rules("session_user_id","User_id","xss_clean|trim"); 
        $this->form_validation->set_rules("last_id","Last_id","required|xss_clean|trim");
        $this->form_validation->set_rules("user_id","User_id","required|xss_clean|trim");
        
        if($this->form_validation->run()){
                
            $user_id = $this->input->post('user_id');
            $last_id = $this->input->post('last_id');
            
            $result_set = $this->model_accounts->get_user_following($user_id,$last_id);
            
            
            if($result_set!=null){
            
                $response['log'] = "Success";
                $response['success'] = 1;
                
                $count = 1;
                
                foreach($result_set as $row){
        
                    $response['followings'][$count]['follow_id'] = $row->follow_id;  
                    $response['followings'][$count]['username'] = $row->name;
                    $response['followings'][$count]['user_id'] = $row->user_id;
                    $response['followings'][$count]['fullname'] = $row->fullname;
                    $response['followings'][$count]['user_type'] = $row->type;
                    $response['followings'][$count]['bio'] = $row->bio;
                    $response['followings'][$count]['wallpaper'] = $row->wallpaper;
                    $user_id = $row->user_id;
                    
                    $response['followings'][$count]['following'] = $this->model_accounts->check_follow_exists($user_id);                        
                    $count++;
                }
                
                
            }else{
                
              if($last_id==1){
                $response['log'] = "Following Nobody";
              }else $response['log'] = "No More Followings";
                $response['success'] = 0;  
                    
            }
        }else{
        
            $response['log'] = "Incomplete Data";
            $response['success'] = 0;
        }
        echo json_encode($response);
    }
    
    public function ns_fetch_user_following(){
        
        $this->webHeaders();
        $response = array(); 
        
        $this->load->model("model_accounts");
        
        $this->load->library('form_validation');
 
        $this->form_validation->set_rules("session_user_id","User_id","xss_clean|trim"); 
        $this->form_validation->set_rules("last_id","Last_id","required|xss_clean|trim");
        $this->form_validation->set_rules("user_id","User_id","required|xss_clean|trim");
        
        if($this->form_validation->run()){
                
            $user_id = $this->input->post('user_id');
            $last_id = $this->input->post('last_id');
            
            $result_set = $this->model_accounts->get_user_following($user_id,$last_id);
            
            
            if($result_set!=null){
            
                $response['log'] = "Success";
                $response['success'] = 1;
                
                $response['followings'] = array();
                foreach($result_set as $row){
                       
                    $user_id = $row->user_id;
                    $data = array(
                        'follow_id' => $row->follow_id,
                        'username' => $row->name,
                        'user_id' => $row->user_id,
                        'fullname' => $row->fullname,
                        'user_type' => $row->type,
                        'bio' => $row->bio,
                        'wallpaper' => $row->wallpaper,
                        'following' => $this->model_accounts->check_follow_exists($user_id)
                    );
                    
                    $response['followings'] = array_merge($response['followings'],array($data));
                }
                
                
            }else{
                
              if($last_id==1){
                $response['log'] = "Following Nobody";
              }else $response['log'] = "No More Followings";
                $response['success'] = 0;  
                    
            }
        }else{
        
            $response['log'] = "Incomplete Data";
            $response['success'] = 0;
        }
        echo json_encode($response);
    }    
    
    public function fetch_pod_subscribers(){
        
        $this->webHeaders();
        $response = array();
        
        $this->load->model("model_collection");        
        
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules("session_user_id","User_id","xss_clean|trim"); 
        $this->form_validation->set_rules("last_id","Last_id","required|xss_clean|trim");
        $this->form_validation->set_rules("collection_id","Collection_id","required|xss_clean|trim");
        
        if($this->form_validation->run()){
        
            $collection_id = $this->input->post('collection_id');
            $last_id = $this->input->post('last_id');
            
            $result_set = $this->model_collection->get_pod_subscribers($collection_id,$last_id);            
            
            if($result_set!=null){
            
                $response['log'] = "Success";
                $response['success'] = 1;
                
                $count = 1;
                $this->load->model("model_accounts");
                
                foreach($result_set as $row){
        
                    $response['subscribers'][$count]['subscribe_id'] = $row->subs_id;
                    $response['subscribers'][$count]['username'] = $row->name;
                    $response['subscribers'][$count]['user_id'] = $row->user_id;
                    $response['subscribers'][$count]['fullname'] = $row->fullname;
                    $response['subscribers'][$count]['user_type'] = $row->type;
                    $response['subscribers'][$count]['bio'] = $row->bio;
                    $response['subscribers'][$count]['wallpaper'] = $row->wallpaper;
                        
                    $user_id = $row->user_id;
                    $this->load->model("model_accounts");
                    $response['subscribers'][$count]['following'] = $this->model_accounts->check_follow_exists($user_id);
                    
                    $count++;
                }
                
            }else{
              if($last_id==1){
                $response['log'] = "No Subscribers";
              }else $response['log'] = "No More Subscribers";
                $response['success'] = 0;
            }            
        }else{
        
            $response['log'] = "Incomplete Data";
            $response['success'] = 0;
        }
        echo json_encode($response);
    }
    
    public function ns_fetch_pod_subscribers(){
        
        $this->webHeaders();
        $response = array();  
        
        $this->load->model("model_collection");
           
        
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules("session_user_id","User_id","xss_clean|trim"); 
        $this->form_validation->set_rules("last_id","Last_id","required|xss_clean|trim");
        $this->form_validation->set_rules("collection_id","Collection_id","required|xss_clean|trim");
        
        if($this->form_validation->run()){
        
            $collection_id = $this->input->post('collection_id');
            $last_id = $this->input->post('last_id');
            
            $result_set = $this->model_collection->get_pod_subscribers($collection_id,$last_id);            
            
            if($result_set!=null){
            
                $response['log'] = "Success";
                $response['success'] = 1;
                
                $response['subscribers'] = array();
                $this->load->model("model_accounts");
                foreach($result_set as $row){
                       
                    $user_id = $row->user_id;
                    $data = array(
                        'subscribe_id' => $row->subs_id,
                        'username' => $row->name,
                        'user_id' => $row->user_id,
                        'fullname' => $row->fullname,
                        'user_type' => $row->type,
                        'bio' => $row->bio,
                        'wallpaper' => $row->wallpaper,
                        'following' => $this->model_accounts->check_follow_exists($user_id)
                    );
                    
                    $response['subscribers'] = array_merge($response['subscribers'],array($data));
                }
                
            }else{
              if($last_id==1){
                $response['log'] = "No Subscribers";
              }else $response['log'] = "No More Subscribers";
                $response['success'] = 0;
            }            
        }else{
        
            $response['log'] = "Incomplete Data";
            $response['success'] = 0;
        }
        echo json_encode($response);
    }    
    
    
    public function fetch_track_likers(){
        
        $this->webHeaders();
        $response = array();
        
        $this->load->model("model_collection");
        
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules("session_user_id","User_id","xss_clean|trim"); 
        $this->form_validation->set_rules("last_id","Last_id","required|xss_clean|trim");
        $this->form_validation->set_rules("track_id","Track_id","required|xss_clean|trim");
        
        if($this->form_validation->run()){
        
            $last_id = $this->input->post("last_id");
            $track_id = $this->input->post("track_id");
            
            $result_set = $this->model_collection->get_track_likers($track_id,$last_id);
            
            if($result_set!=null){
            
                $response['log'] = "Success";
                $response['success'] = 1;
                
                $count = 1;
                
                foreach($result_set as $row){
        
                    $response['likers'][$count]['like_id'] = $row->like_id;
                    $response['likers'][$count]['username'] = $row->name;
                    $response['likers'][$count]['user_id'] = $row->user_id;
                    $response['likers'][$count]['fullname'] = $row->fullname;
                    $response['likers'][$count]['user_type'] = $row->type;
                    $response['likers'][$count]['bio'] = $row->bio;
                    $response['likers'][$count]['wallpaper'] = $row->wallpaper;

                    $user_id = $row->user_id;
                    $this->load->model("model_accounts");
                    $response['likers'][$count]['following'] = $this->model_accounts->check_follow_exists($user_id);
                    $count++;
                }
                            
                
                
            }else{
              if($last_id==1){
                $response['log'] = "No Likers";
              }else $response['log'] = "No More Likers";
                $response['success'] = 0;
            }
            
        }else{
        
            $response['log'] = "Incomplete Data";
            $response['success'] = 0;
        }
        echo json_encode($response);    
    }
    
    public function ns_fetch_track_likers(){
        
        $this->webHeaders();
        $response = array();
        
        $this->load->model("model_collection");
        
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules("session_user_id","User_id","xss_clean|trim"); 
        $this->form_validation->set_rules("last_id","Last_id","required|xss_clean|trim");
        $this->form_validation->set_rules("track_id","Track_id","required|xss_clean|trim");
        
        if($this->form_validation->run()){
        
            $last_id = $this->input->post("last_id");
            $track_id = $this->input->post("track_id");
            
            $result_set = $this->model_collection->get_track_likers($track_id,$last_id);
            
            if($result_set!=null){
            
                $response['log'] = "Success";
                $response['success'] = 1;
                
                $response['likers'] = array();
                $this->load->model("model_accounts");
                foreach($result_set as $row){
                       
                    $user_id = $row->user_id;
                    $data = array(
                        'like_id' => $row->like_id,
                        'username' => $row->name,
                        'user_id' => $row->user_id,
                        'fullname' => $row->fullname,
                        'user_type' => $row->type,
                        'bio' => $row->bio,
                        'wallpaper' => $row->wallpaper,
                        'following' => $this->model_accounts->check_follow_exists($user_id)
                    );
                    
                    $response['likers'] = array_merge($response['likers'],array($data));
                }
                            
                
                
            }else{
              if($last_id==1){
                $response['log'] = "No Likers";
              }else $response['log'] = "No More Likers";
                $response['success'] = 0;
            }
            
        }else{
        
            $response['log'] = "Incomplete Data";
            $response['success'] = 0;
        }
        echo json_encode($response);    
    }    
    
    
    public function fetch_user_collections(){
        
        $this->webHeaders();

        $response = array();
        
        $this->load->library("form_validation");
        $this->form_validation->set_rules("session_user_id","User_id","xss_clean|trim");
        $this->form_validation->set_rules("last_id","Last_id","required|xss_clean|trim");
        $this->form_validation->set_rules("user_id","User_id","required|xss_clean|trim");
        $this->form_validation->set_rules("order_by","order_by","required|xss_clean|trim");
        $this->form_validation->set_rules("type","Type","xss_clean|trim");
        
        
        if($this->form_validation->run()){
            
            $this->load->model("model_collection");
            $collections = $this->model_collection->get_user_collections();
    
            $count = 1;
            if($collections!=null){
               $response['success'] = 1;
               $response['log'] = "Collection data fetched";                
                
               foreach($collections as $row){
                    $response['collections'][$count]['collection_id'] = $row->collection_id;
                    $response['collections'][$count]['user_id'] = $row->user_id;
                    $response['collections'][$count]['username'] = $row->name;
                    $response['collections'][$count]['title'] = $row->collection_title;
                    $response['collections'][$count]['desc'] = $row->collection_bio;
                    $response['collections'][$count]['type'] = $row->collection_type;
                    $response['collections'][$count]['genre'] = $row->collection_category;
                    $response['collections'][$count]['album_art'] = $row->album_art;
                    $response['collections'][$count]['directory'] = $row->collection_directory;
                    $response['collections'][$count]['date_created'] = $row->date;

                    
                    
                    $response['collections'][$count]['subscribed'] = $this->model_collection->check_subscribe_exists($row->collection_id);                    
                    $count++;
                }
                
            }else{
                $last_id = $this->input->post("last_id");
                if($last_id!=1){
                    $response['log'] = "No More Collections";                    
                }else $response['log'] = "No Collections";
                $response['success'] = 0; 
            }
            
        }else{
            $response['log'] = "Incomplete Data ";
            $response['success'] = 0;
            
        }
        
        
        
        echo json_encode($response);
        
    }
    
    public function ns_fetch_user_collections(){
        
        $this->webHeaders();

        $response = array();        
        
        $this->load->library("form_validation");
        $this->form_validation->set_rules("session_user_id","User_id","xss_clean|trim");
        $this->form_validation->set_rules("last_id","Last_id","required|xss_clean|trim");
        $this->form_validation->set_rules("user_id","User_id","required|xss_clean|trim");
        $this->form_validation->set_rules("order_by","order_by","required|xss_clean|trim");
        $this->form_validation->set_rules("type","Type","xss_clean|trim");
        
        
        if($this->form_validation->run()){
            
            $this->load->model("model_collection");
            $collections = $this->model_collection->get_user_collections();
    
            if($collections!=null){
               $response['success'] = 1;
               $response['log'] = "Collection data fetched";                
                
               
               $response['collections'] = array();
               
               foreach($collections as $row){
                  // $collection_id = $row->collection_id;
                   
                   $data = array(
                       'collection_id' => $row->collection_id,
                       'user_id' => $row->user_id,
                       'username' => $row->name,
                       'title' => $row->collection_title,
                       'desc' => $row->collection_bio,
                       'type' => $row->collection_type,
                       'genre' => $row->collection_category,
                       'album_art' => $row->album_art,
                       'directory' => $row->collection_directory,
                       'date_created' => $row->date,
                       'subscribed' => $this->model_collection->check_subscribe_exists($row->collection_id)
                   );
                   $response['collections'] = array_merge($response['collections'],array($data));
               }
                
            }else{
                $last_id = $this->input->post("last_id");
                if($last_id!=1){
                    $response['log'] = "No More Collections";                    
                }else $response['log'] = "No Collections";
                $response['success'] = 0; 
            }
            
        }else{
            $response['log'] = "Incomplete Data ";
            $response['success'] = 0;
            
        }
        
        
        
        echo json_encode($response);
        
    }    
    
    public function ns_small_fetch_user_collections(){
        
        $this->webHeaders();

        $response = array();
        
        $this->load->library("form_validation");
        $this->form_validation->set_rules("session_user_id","User_id","xss_clean|trim");
        $this->form_validation->set_rules("last_id","Last_id","required|xss_clean|trim");
        $this->form_validation->set_rules("user_id","User_id","required|xss_clean|trim");
        $this->form_validation->set_rules("order_by","order_by","required|xss_clean|trim");
        $this->form_validation->set_rules("type","Type","xss_clean|trim");
        
        
        if($this->form_validation->run()){
            
            $this->load->model("model_collection");
            $collections = $this->model_collection->get_user_collections();
    
            if($collections!=null){
               $response['success'] = 1;
               $response['log'] = "Collection data fetched";                
                
               
               $response['collections'] = array();
               
               foreach($collections as $row){
                  // $collection_id = $row->collection_id;
                   
                   $data = array(
                       'collection_id' => $row->collection_id,
                       'title' => $row->collection_title
                   );
                   $response['collections'] = array_merge($response['collections'],array($data));
               }
                
            }else{
                $last_id = $this->input->post("last_id");
                if($last_id!=1){
                    $response['log'] = "No More Collections";                    
                }else $response['log'] = "No Collections";
                $response['success'] = 0; 
            }
            
        }else{
            $response['log'] = "Incomplete Data ";
            $response['success'] = 0;
            
        }
        
        
        
        echo json_encode($response);
        
    }        
    
    public function fetch_collections(){
       
        $this->webHeaders();

        $response = array();
        
        $this->load->library("form_validation");
        $this->form_validation->set_rules("session_user_id","User_id","xss_clean|trim");
        $this->form_validation->set_rules("last_id","Last_id","required|xss_clean|trim");
        $this->form_validation->set_rules("order_by","order_by","required|xss_clean|trim");
        $this->form_validation->set_rules("type","Type","xss_clean|trim");
        
        
        if($this->form_validation->run()){
            
            $this->load->model("model_collection");
            $collections = $this->model_collection->get_collections();
    
            $count = 1;
            if($collections!=null){
               $response['success'] = 1;
               $response['log'] = "Collection data fetched";                
                
               foreach($collections as $row){
                    $response['collections'][$count]['collection_id'] = $row->collection_id;
                    $response['collections'][$count]['user_id'] = $row->user_id;
                    $response['collections'][$count]['username'] = $row->name;
                    $response['collections'][$count]['title'] = $row->collection_title;
                    $response['collections'][$count]['desc'] = $row->collection_bio;
                    $response['collections'][$count]['type'] = $row->collection_type;
                    $response['collections'][$count]['genre'] = $row->collection_category;
                    $response['collections'][$count]['album_art'] = $row->album_art;
                    $response['collections'][$count]['directory'] = $row->collection_directory;
                    $response['collections'][$count]['date_created'] = $row->date;
                    
                    $response['collections'][$count]['subscribed'] = $this->model_collection->check_subscribe_exists($row->collection_id);                    
                    $count++;
                }
                
            }else{
                $last_id = $this->input->post("last_id");
                if($last_id!=1){
                    $response['log'] = "No More Collections";                    
                }else $response['log'] = "No Collections";
                $response['success'] = 0; 
            }
            
        }else{
            $response['log'] = "Incomplete Data";
            $response['success'] = 0;
            
        }
        
        
        
        echo json_encode($response);
    }
    
    public function ns_fetch_collections(){
       
        $this->webHeaders();
        $response = array();
        
        $this->load->library("form_validation");
        $this->form_validation->set_rules("session_user_id","User_id","xss_clean|trim");
        $this->form_validation->set_rules("last_id","Last_id","required|xss_clean|trim");
        $this->form_validation->set_rules("order_by","order_by","required|xss_clean|trim");
        $this->form_validation->set_rules("type","Type","xss_clean|trim");
        
        
        if($this->form_validation->run()){
            
            $this->load->model("model_collection");
            $collections = $this->model_collection->get_collections();
    
            $count = 1;
            if($collections!=null){
               $response['success'] = 1;
               $response['log'] = "Collection data fetched";                
               $response['collections'] = array();
               
               foreach($collections as $row){
                  // $collection_id = $row->collection_id;
                   
                   $data = array(
                       'collection_id' => $row->collection_id,
                       'user_id' => $row->user_id,
                       'username' => $row->name,
                       'title' => $row->collection_title,
                       'desc' => $row->collection_bio,
                       'type' => $row->collection_type,
                       'genre' => $row->collection_category,
                       'album_art' => $row->album_art,
                       'directory' => $row->collection_directory,
                       'date_created' => $row->date,
                       'subscribed' => $this->model_collection->check_subscribe_exists($row->collection_id)
                   );
                   $response['collections'] = array_merge($response['collections'],array($data));
               }
                
            }else{
                $last_id = $this->input->post("last_id");
                if($last_id!=1){
                    $response['log'] = "No More Collections";                    
                }else $response['log'] = "No Collections";
                $response['success'] = 0; 
            }
            
        }else{
            $response['log'] = "Incomplete Data";
            $response['success'] = 0;
            
        }
        
        
        
        echo json_encode($response);
    }    

    public function fetch_user_likes(){
        $this->webHeaders();
        
        $response = array();

        $this->load->model("model_collection");
        $this->load->library('form_validation');       
 
        $this->form_validation->set_rules("session_user_id","User_id","xss_clean|trim"); 
        $this->form_validation->set_rules("last_id","Last_id","required|xss_clean|trim");
        $this->form_validation->set_rules("user_id","user_id","required|xss_clean|trim");
        
        if($this->form_validation->run()){
            $last_id = $this->input->post('last_id');
            $user_id = $this->input->post('user_id');
            
            $result_set = $this->model_collection->get_user_likes($last_id,$user_id);
            
            if($result_set!=null){
            
                $count = 1;
                $response['log'] = "Success";
                $response['success'] = 1;
                
                foreach($result_set as $row){
                            
                   $response['tracks'][$count]['track_id'] = $row->track_id;
                   $response['tracks'][$count]['collection_id'] =  $row->collection_id;
                   $response['tracks'][$count]['collection_name'] =  $row->collection_title;
                      
                   $response['tracks'][$count]['listens'] = $row->listens;
                   $response['tracks'][$count]['likes'] = $row->likes;
                   $response['tracks'][$count]['title'] = $row->trackname;
                   $response['tracks'][$count]['username'] = $row->name;
                   $response['tracks'][$count]['genre'] = $row->track_genre;
                   $response['tracks'][$count]['audio_file'] = $row->audio_file;
                   $response['tracks'][$count]['downloadable']= $row->downloadable;
                   $response['tracks'][$count]['featured'] = $row->people;
                   $response['tracks'][$count]['album_art'] = $row->album_art;     
                   $response['tracks'][$count]['comments_num'] = $row->comments_num;
                   
                   $track_id = $row->track_id;
                   $response['tracks'][$count]['liked'] = $this->model_collection->check_like_exists($track_id);                         
                    $count++;
                }                
                
            }else{
            
              if($last_id==1){
                $response['log'] = "No Tracks";
              }else $response['log'] = "No More Tracks";
                $response['success'] = 0;
                
            }    
        }else{
        
            $response['log'] = "Incomplete Data";
            $response['success'] = 0;
        }
        
        echo json_encode($response);
        
    }
    
    public function ns_fetch_user_likes(){
        $this->webHeaders();
        
        $response = array();
        
        $this->load->model("model_collection");
        $this->load->library('form_validation');
 
        $this->form_validation->set_rules("session_user_id","User_id","xss_clean|trim"); 
        $this->form_validation->set_rules("last_id","Last_id","required|xss_clean|trim");
        $this->form_validation->set_rules("user_id","user_id","required|xss_clean|trim");
        
        if($this->form_validation->run()){
            $last_id = $this->input->post('last_id');
            $user_id = $this->input->post('user_id');
            
            $result_set = $this->model_collection->get_user_likes($last_id,$user_id);
            
            if($result_set!=null){

                $response['log'] = "Success";
                $response['success'] = 1;
                
                $response['tracks'] = array();
                
                foreach($result_set as $row){
                   $track_id = $row->track_id;
                   
                   $data = array(
                     'track_id' => $row->track_id,
                     'collection_id' => $row->collection_id,
                     'collection_name' => $row->collection_title,
                     'listens' => $row->listens,
                     'likes' => $row->likes,
                     'title' => $row->trackname,
                     'username' => $row->name,
                     'genre' => $row->track_genre,
                     'audio_file' => $row->audio_file,
                     'downloadable' => $row->downloadable,
                     'featured' => $row->people,
                     'album_art' => $row->album_art, 
                     'comments_num' => $row->comments_num,
                     'liked' => $this->model_collection->check_like_exists($track_id)
                   );
                        
                   $response['tracks'] = array_merge($response['tracks'],array($data)); 
                }                 
                
            }else{
            
              if($last_id==1){
                $response['log'] = "No Tracks";
              }else $response['log'] = "No More Tracks";
                $response['success'] = 0;
                
            }    
        }else{
        
            $response['log'] = "Incomplete Data";
            $response['success'] = 0;
        }
        
        echo json_encode($response);
        
    }    

    public function fetch_track_by_id(){
        
        $response = array();
        $this->webHeaders();
        
        
        $this->load->library("form_validation");
        $this->form_validation->set_rules("track_id","","required|xss_clean|trim");
        
        if($this->form_validation->run()){
            $this->load->model("model_collection");
            $result_set = $this->model_collection->fetch_track($this->input->post('track_id'));
            if($result_set!=null){
                $response['log'] = "Success";
                $response['success'] = 1;
                foreach($result_set as $row){
                   $response['track_id'] = $row->track_id;
                      
                   $response['listens'] = $row->listens;
                   $response['likes'] = $row->likes;
                   $response['title'] = $row->trackname;
                   $response['genre'] = $row->track_genre;
                   $response['audio_file'] = $row->audio_file;
                   $response['downloadable']= $row->downloadable;
                   $response['featured'] = $row->people;      
                   $response['comments_num'] = $row->comments_num;
                }              
                
                
            }else{
                $response['log'] = $result_set;
                $response['success'] = 0;
            }
        }else{
            $response['log'] = "Incomplete Data";
            $response['success'] = 0;
        }
        
        echo json_encode($response);
    }
    
    public function ns_fetch_home_tracks(){
        
        $response = array();
        $this->webHeaders();
        //$_POST['session_user_id'] = 1;
        //$_POST['last_id'] = 7;           
        $this->load->library("form_validation");
        
        $this->form_validation->set_rules("session_user_id","","xss_clean|trim");
        $this->form_validation->set_rules("last_id","","required|xss_clean|trim");
        
        if($this->form_validation->run()){
            
            $this->load->model("model_collection");
            $tracks_set = $this->model_collection->home_tracks();
            
            if($tracks_set!=null){
                
 
                $response['success'] = 1;
                $response['log'] = "Success";
                $response['tracks'] = array();
                
                foreach($tracks_set as $row){
                   $track_id = $row->track_id;
                   
                   $data = array(
                     'track_id' => $row->track_id,
                     'collection_id' => $row->collection_id,
                     'collection_name' => $row->collection_title,
                     'listens' => $row->listens,
                     'likes' => $row->likes,
                     'title' => $row->trackname,
                     'username' => $row->name,
                     'genre' => $row->track_genre,
                     'audio_file' => $row->audio_file,
                     'downloadable' => $row->downloadable,
                     'featured' => $row->people,
                     'album_art' => $row->album_art, 
                     'comments_num' => $row->comments_num,
                     'liked' => $this->model_collection->check_like_exists($track_id)
                   );
                        
                   $response['tracks'] = array_merge($response['tracks'],array($data)); 
                }  
                $response['success'] = 1;
                $response['log'] = "Success";
            }else{
                $response['success'] = 0;
                $response['log'] = "No Data";
            }
        }else{
            $response['log'] = "Incomplete Data";
            $response['success'] = 0;
        }
        
        echo json_encode($response);
    }
    
    public function fetch_home_voices(){
        
        $response = array();
        $this->webHeaders();
            
        $this->load->library("form_validation");
        $_POST['session_user_id'] = 1;
        $_POST['last_id'] = 10;        
        $this->form_validation->set_rules("session_user_id","","xss_clean|trim");
        $this->form_validation->set_rules("last_id","","required|xss_clean|trim");
        
        if($this->form_validation->run()){
            
            $this->load->model("model_collection");
            $voices_set = $this->model_collection->home_voices();
            
            if($voices_set!=null){
                $count = 1;
                
                $response['success'] = 1;
                $response['log'] = "Success";
                foreach($voices_set as $row){
                    
                    $response['voices'][$count]['post_id'] = $row->post_id;
                    $response['voices'][$count]['caption'] = $row->post_caption; 
                    $response['voices'][$count]['reply_to'] = $row->reply_to;
                    $response['voices'][$count]['likes'] = $row->likes;
                    $response['voices'][$count]['listens'] =  $row->listens;
                    $response['voices'][$count]['timestamp'] = $row->timestamp;
                    $response['voices'][$count]['username'] = $row->name;
                    $response['voices'][$count]['user_id'] = $row->user_id;
                    $response['voices'][$count]['profile_pix'] = $row->wallpaper;
                    $response['voices'][$count]['audio_file'] = $row->post_track;
                    
                    $post_id = $row->post_id;
                    $response['voices'][$count]['liked'] = $this->model_collection->get_voice_like_status($post_id);
                    $response['voices'][$count]['replies'] = $this->model_collection->get_voice_replies_num($post_id);
                    
                    $count++;
                }
                $response['success'] = 1;
                $response['log'] = "Success";
            }else{
                $response['success'] = 0;
                $response['log'] = "No Data";
            }
        }else{
            $response['log'] = "Incomplete Data";
            $response['success'] = 0;
        }
        
        echo json_encode($response);
    }    
    
      public function ns_fetch_home_voices(){
        
        $response = array();
        $this->webHeaders();
                
        $this->load->library("form_validation");
        $_POST['session_user_id'] = 1;
        $_POST['last_id'] = 10;        
        $this->form_validation->set_rules("session_user_id","","xss_clean|trim");
        $this->form_validation->set_rules("last_id","","required|xss_clean|trim");
        
        if($this->form_validation->run()){
            
            $user_id = $this->input->post('session_user_id');
            
            $this->load->model("model_collection");
            $voices_set = $this->model_collection->home_voices();
            
            if($voices_set!=null){
                
                $response['success'] = 1;
                $response['log'] = "Success";
                
                $response['voices'] = array();
                
                foreach($voices_set as $row){
                    
                    $post_id = $row->post_id;
                    $data = array(
                        'post_id' => $row->post_id,
                        'caption' => $row->post_caption,
                        'reply_to' => $row->reply_to,
                        'likes' => $row->likes,
                        'listens' => $row->listens,
                        'timestamp' => $row->timestamp,
                        'username' => $row->name,
                        'user_id' => $row->user_id,
                        'profile_pix' => $row->wallpaper,
                        'audio_file' => $row->post_track,
                        'liked' => $this->model_collection->get_voice_like_status($post_id),  
                        'replies' => $this->model_collection->get_voice_replies_num($post_id)  
                        
                    );
                    $response['voices'] = array_merge($response['voices'], array($data));

                }
                $response['success'] = 1;
                $response['log'] = "Success";
            }else{
                $response['success'] = 0;
                $response['log'] = "No Data";
            }
        }else{
            $response['log'] = "Incomplete Data";
            $response['success'] = 0;
        }
        
        echo json_encode($response);
    }     
    
    public function fetch_pod_subscriptions(){
        $response = array();
        $this->webHeaders();       
        $this->load->library("form_validation");

        
        $this->form_validation->set_rules("session_user_id","","xss_clean|trim");
        $this->form_validation->set_rules("user_id","required|xss_clean|trim");
        
        if($this->form_validation->run()){
            
            $this->load->model("model_collection");
            $result_set = $this->model_collection->fetch_pod_subscriptions();
            $count = 1; 
            if($result_set!=null){
                  
                $response['success'] = 1;
                $response['log'] = "Success";
                foreach($result_set as $row){
                    $response['subscriptions'][$count]['subscription_id'] = $row->subs_id;
                    $response['subscriptions'][$count]['collection_id'] = $row->collection_id;
                    $response['subscriptions'][$count]['user_id'] = $row->user_id;
                    $response['subscriptions'][$count]['username'] = $row->name;
                    $response['subscriptions'][$count]['title'] = $row->collection_title;
                    $response['subscriptions'][$count]['desc'] = $row->collection_bio;
                    $response['subscriptions'][$count]['type'] = $row->collection_type;
                    $response['subscriptions'][$count]['genre'] = $row->collection_category;
                    $response['subscriptions'][$count]['album_art'] = $row->album_art;
                    $response['subscriptions'][$count]['directory'] = $row->collection_directory;
                    $response['subscriptions'][$count]['date_created'] = $row->date;

                    
                    
                    $response['subscriptions'][$count]['subscribed'] = $this->model_collection->check_subscribe_exists($row->collection_id);                    
                    $count++;               
                }
                
            }else{
                $response['success'] = 0;
                $response['log'] = "No Subscriptions";
            }   
            
        }else{
           $response['log'] = "Incomplete Data";
           $response['success'] = 0;
        }
        
        echo json_encode($response);
        
        
    }
    
    public function ns_fetch_pod_subscriptions(){
        $response = array();
        $this->webHeaders();       
        $this->load->library("form_validation"); 
        
        $this->form_validation->set_rules("session_user_id","","xss_clean|trim");
        $this->form_validation->set_rules("user_id","required|xss_clean|trim");
        
        if($this->form_validation->run()){
            
            $this->load->model("model_collection");
            $result_set = $this->model_collection->fetch_pod_subscriptions();
            $count = 1; 
            if($result_set!=null){
                  
                $response['success'] = 1;
                $response['log'] = "Success";
                $response['subscriptions'] = array();
                foreach($result_set as $row){
                    
                    $data = array(
                        'subscription_id' => $row->subs_id,
                        'collection_id' => $row->collection_id,
                        'user_id' => $row->user_id,
                        'username' => $row->name,
                        'title' => $row->collection_title,
                        'desc' => $row->collection_bio,
                        'type' => $row->collection_type,
                        'genre' => $row->collection_category,
                        'album_art' => $row->album_art,
                        'directory' => $row->collection_directory,
                        'date_created' => $row->date,
                        'subscribed' => $this->model_collection->check_subscribe_exists($row->collection_id)
                    );
                    
                    $response['subscriptions'] = array_merge($response['subscriptions'],array($data));
                                       
                    $count++;               
                }
                
            }else{
                $response['success'] = 0;
                $response['log'] = "No Subscriptions";
            }   
            
        }else{
           $response['log'] = "Incomplete Data";
           $response['success'] = 0;
        }
        
        echo json_encode($response);
        
        
    }    
    
    public function fetch_subscription_tracks(){
        
        $this->webHeaders();
        $response = array();
        
        $this->load->model("model_collection"); 
        
        $this->load->library('form_validation');         
        
        $this->form_validation->set_rules("session_user_id","User_id","xss_clean|trim"); 
        $this->form_validation->set_rules("last_id","Last_id","required|xss_clean|trim");
        $this->form_validation->set_rules("collection_id","Collection_id","required|xss_clean|trim");
        
        if($this->form_validation->run()){
            $last_id = $this->input->post('last_id');
            $collection_id = $this->input->post('collection_id');
            
            $result_set = $this->model_collection->get_collection_tracks($collection_id,$last_id);
            
            if($result_set!=null){
            
                $count = 1;
                $response['log'] = "Success";
                $response['success'] = 1;
                
                foreach($result_set as $row){
                            
                   $response['tracks'][$count]['track_id'] = $row->track_id;
                   $response['tracks'][$count]['collection_id'] =  $row->collection_id;
                   $response['tracks'][$count]['collection_name'] =  $row->collection_title;
                      
                   $response['tracks'][$count]['listens'] = $row->listens;
                   $response['tracks'][$count]['likes'] = $row->likes;
                   $response['tracks'][$count]['title'] = $row->trackname;
                   $response['tracks'][$count]['username'] = $row->name;
                   $response['tracks'][$count]['genre'] = $row->track_genre;
                   $response['tracks'][$count]['audio_file'] = $row->audio_file;
                   $response['tracks'][$count]['downloadable']= $row->downloadable;
                   $response['tracks'][$count]['featured'] = $row->people;
                   $response['tracks'][$count]['album_art'] = $row->album_art;     
                   $response['tracks'][$count]['comments_num'] = $row->comments_num;
                   
                   $track_id = $row->track_id;                   
                   $response['tracks'][$count]['listened'] = $this->model_collection->check_listened_status($track_id);
                   $response['tracks'][$count]['liked'] = $this->model_collection->check_like_exists($track_id);                        
                    $count++;
                }                
                
            }else{
            
              if($last_id==1){
                $response['log'] = "No Tracks";
              }else $response['log'] = "No More Tracks";
                $response['success'] = 0;
                
            }
                    
        }else{
        
            $response['log'] = "Incomplete Data";
            $response['success'] = 0;
        }
        echo json_encode($response);  
                
        
    }
    
    public function fetch_user_voices(){
        $this->webHeaders();
        $response = array();
        
        $this->load->library("form_validation");
        
        $this->form_validation->set_rules("session_user_id","","xss_clean|trim");
        $this->form_validation->set_rules("last_id","","required|xss_clean|trim");
        $this->form_validation->set_rules("user_id","","required|xss_clean|trim");
        
        if($this->form_validation->run()){
            $this->load->model('model_collection');
            $result_set = $this->model_collection->fetch_user_voices();
            if($result_set!=null){
                $response['success'] = 1;
                $response['log'] = "Success";
                $count = 1;
                foreach($result_set as $row){

                        $response['voices'][$count]['post_id'] = $row->post_id;
                        $response['voices'][$count]['caption'] = $row->post_caption; 
                        $response['voices'][$count]['reply_to'] = $row->reply_to;
                        $response['voices'][$count]['likes'] = $row->likes;
                        $response['voices'][$count]['listens'] =  $row->listens;
                        $response['voices'][$count]['timestamp'] = $row->timestamp;
                        $response['voices'][$count]['username'] = $row->name;
                        $response['voices'][$count]['user_id'] = $row->user_id;
                        $response['voices'][$count]['profile_pix'] = $row->wallpaper;
                        $response['voices'][$count]['audio_file'] = $row->post_track;
                        
                        $post_id = $row->post_id;
                        $response['voices'][$count]['liked'] = $this->model_collection->get_voice_like_status($post_id);
                        $response['voices'][$count]['replies'] = $this->model_collection->get_voice_replies_num($post_id);
                        $count++;
                }
            }else{
                $response['log'] = "No Voice Notes";
                $response['success'] = 0;
            }
        }else{
            $response['log'] = "Incomplete Data";
            $response['success'] = 0;
        }
        echo json_encode($response);        
    }
    
    public function ns_fetch_user_voices(){
        $this->webHeaders();
        $response = array();
        
        $this->load->library("form_validation");
        
        $this->form_validation->set_rules("session_user_id","","xss_clean|trim");
        $this->form_validation->set_rules("last_id","","required|xss_clean|trim");
        $this->form_validation->set_rules("user_id","","required|xss_clean|trim");
        
        if($this->form_validation->run()){
            $this->load->model('model_collection');
            $result_set = $this->model_collection->fetch_user_voices();
            if($result_set!=null){
                $response['success'] = 1;
                $response['log'] = "Success";
                $response['voices'] = array();
                
                foreach($result_set as $row){
                    
                    $post_id = $row->post_id;
                    $data = array(
                        'post_id' => $row->post_id,
                        'caption' => $row->post_caption,
                        'reply_to' => $row->reply_to,
                        'likes' => $row->likes,
                        'listens' => $row->listens,
                        'timestamp' => $row->timestamp,
                        'username' => $row->name,
                        'user_id' => $row->user_id,
                        'profile_pix' => $row->wallpaper,
                        'audio_file' => $row->post_track,
                        'liked' => $this->model_collection->get_voice_like_status($post_id),  
                        'replies' => $this->model_collection->get_voice_replies_num($post_id)  
                        
                    );
                    $response['voices'] = array_merge($response['voices'], array($data));

                }
            }else{
                $response['log'] = "No Voice Notes";
                $response['success'] = 0;
            }
        }else{
            $response['log'] = "Incomplete Data";
            $response['success'] = 0;
        }
        echo json_encode($response);        
    }    
    
    public function ns_fetch_subscription_tracks(){
        
        $this->webHeaders();
        $response = array();
        
        $this->load->model("model_collection"); 
        
        $this->load->library('form_validation');
        $this->form_validation->set_rules("session_user_id","User_id","xss_clean|trim"); 
        $this->form_validation->set_rules("last_id","Last_id","required|xss_clean|trim");
        $this->form_validation->set_rules("collection_id","Collection_id","required|xss_clean|trim");
        
        if($this->form_validation->run()){
            $last_id = $this->input->post('last_id');
            $collection_id = $this->input->post('collection_id');
            
            $result_set = $this->model_collection->get_collection_tracks($collection_id,$last_id);
            
            if($result_set!=null){
            
                //$count = 1;
                $response['log'] = "Success";
                $response['success'] = 1;
                
                $response['tracks'] = array();
                
                foreach($result_set as $row){
                   $track_id = $row->track_id;
                   
                   $data = array(
                     'track_id' => $row->track_id,
                     'collection_id' => $row->collection_id,
                     'collection_name' => $row->collection_title,
                     'listens' => $row->listens,
                     'likes' => $row->likes,
                     'title' => $row->trackname,
                     'username' => $row->name,
                     'genre' => $row->track_genre,
                     'audio_file' => $row->audio_file,
                     'downloadable' => $row->downloadable,
                     'featured' => $row->people,
                     'album_art' => $row->album_art, 
                     'comments_num' => $row->comments_num,
                     'liked' => $this->model_collection->check_like_exists($track_id)
                   );
                        
                   $response['tracks'] = array_merge($response['tracks'],array($data)); 
                }               
                
            }else{
            
              if($last_id==1){
                $response['log'] = "No Tracks";
              }else $response['log'] = "No More Tracks";
                $response['success'] = 0;
                
            }
                    
        }else{
        
            $response['log'] = "Incomplete Data";
            $response['success'] = 0;
        }
        echo json_encode($response);  
                
        
    }    
    
    public function fetch_voice_reply(){
        $this->webHeaders();
        $response = array();
        
        $this->load->library("form_validation");
        
        
        $this->form_validation->set_rules("session_user_id","","xss_clean|trim");
        $this->form_validation->set_rules("last_id","","required|xss_clean|trim");
        $this->form_validation->set_rules("post_id","","required|xss_clean|trim");
        
        if($this->form_validation->run()){
            $this->load->model('model_collection');
            $result_set = $this->model_collection->fetch_voices_reply();
            if($result_set!=null){
                $response['success'] = 1;
                $response['log'] = "Success";
                $count = 1;
                foreach($result_set as $row){

                    $response['voices'][$count]['post_id'] = $row->post_id;
                    $response['voices'][$count]['caption'] = $row->post_caption; 
                    $response['voices'][$count]['reply_to'] = $row->reply_to;
                    $response['voices'][$count]['likes'] = $row->likes;
                    $response['voices'][$count]['listens'] =  $row->listens;
                    $response['voices'][$count]['timestamp'] = $row->timestamp;
                    $response['voices'][$count]['username'] = $row->name;
                    $response['voices'][$count]['user_id'] = $row->user_id;
                    $response['voices'][$count]['profile_pix'] = $row->wallpaper;
                    $response['voices'][$count]['audio_file'] = $row->post_track;
                    
                    $post_id = $row->post_id;
                    $response['voices'][$count]['liked']  = $this->model_collection->get_voice_like_status($post_id);
                    $response['voices'][$count]['replies'] = $this->model_collection->get_voice_replies_num($post_id);
                    $count++;
                }
            }else{
                $response['log'] = "No Voice Notes";
                $response['success'] = 0;
            }
        }else{
            $response['log'] = "Incomplete Data";
            $response['success'] = 0;
        }
        echo json_encode($response);         
        
    }
    
    public function ns_fetch_voice_reply(){
        $this->webHeaders();
        $response = array();
        
        $this->load->library("form_validation");      
        $this->form_validation->set_rules("session_user_id","","xss_clean|trim");
        $this->form_validation->set_rules("last_id","","required|xss_clean|trim");
        $this->form_validation->set_rules("post_id","","required|xss_clean|trim");
        
        if($this->form_validation->run()){
            $this->load->model('model_collection');
            $result_set = $this->model_collection->fetch_voices_reply();
            if($result_set!=null){
                $response['success'] = 1;
                $response['log'] = "Success";
                $response['voices'] = array();
                
                foreach($result_set as $row){
                    
                    $post_id = $row->post_id;
                    $data = array(
                        'post_id' => $row->post_id,
                        'caption' => $row->post_caption,
                        'reply_to' => $row->reply_to,
                        'likes' => $row->likes,
                        'listens' => $row->listens,
                        'timestamp' => $row->timestamp,
                        'username' => $row->name,
                        'user_id' => $row->user_id,
                        'profile_pix' => $row->wallpaper,
                        'audio_file' => $row->post_track,
                        'liked' => $this->model_collection->get_voice_like_status($post_id),  
                        'replies' => $this->model_collection->get_voice_replies_num($post_id)  
                        
                    );
                    $response['voices'] = array_merge($response['voices'], array($data));

                }
            }else{
                $response['log'] = "No Voice Notes";
                $response['success'] = 0;
            }
        }else{
            $response['log'] = "Incomplete Data";
            $response['success'] = 0;
        }
        echo json_encode($response);         
        
    }    

    
    public function fetch_voice_note_likers(){
        $response = array();
      
        $this->webHeaders();       
        $this->load->library("form_validation");
        $this->form_validation->set_rules("session_user_id","","xss_clean|trim");
        $this->form_validation->set_rules("post_id","","required|xss_clean|trim");
        $this->form_validation->set_rules("last_id","","required|xss_clean|trim");
        
        if($this->form_validation->run()){
            
            $this->load->model("model_collection");
            $result_set = $this->model_collection->get_voice_note_likers($this->input->post('post_id'),$this->input->post('last_id'));//not yet implemented
            
            if($result_set!=null){
            
                $response['log'] = "Success";
                $response['success'] = 1;
                
                $count = 1;
                
                foreach($result_set as $row){
        
                    $response['likers'][$count]['like_id'] = $row->like_id;
                    $response['likers'][$count]['username'] = $row->name;
                    $response['likers'][$count]['user_id'] = $row->user_id;
                    $response['likers'][$count]['fullname'] = $row->fullname;
                    $response['likers'][$count]['user_type'] = $row->type;
                    $response['likers'][$count]['bio'] = $row->bio;
                    $response['likers'][$count]['wallpaper'] = $row->wallpaper;

                    $user_id = $row->user_id;
                    $this->load->model("model_accounts");
                    $response['likers'][$count]['following'] = $this->model_accounts->check_follow_exists($user_id);
                    $count++;
                }
                            
                
                
            }else{
              if($this->input->post('last_id')){
                $response['log'] = "No Likers";
              }else $response['log'] = "No More Likers";
                $response['success'] = 0;
            }            
        }else{
            $response['log'] = "Incomplete Data";
            $response['success'] = 0;
        }
        
        echo json_encode($response);        
        
    }
    public function ns_fetch_voice_note_likers(){
        $response = array();
      
        $this->webHeaders();       
        $this->load->library("form_validation");
        $this->form_validation->set_rules("session_user_id","","xss_clean|trim");
        $this->form_validation->set_rules("post_id","","required|xss_clean|trim");
        $this->form_validation->set_rules("last_id","","required|xss_clean|trim");
        
        if($this->form_validation->run()){
            
            $this->load->model("model_collection");
            $result_set = $this->model_collection->get_voice_note_likers($this->input->post('post_id'),$this->input->post('last_id'));//not yet implemented
            
            if($result_set!=null){
            
                $response['log'] = "Success";
                $response['success'] = 1;

                $response['likers'] = array();
                
                $this->load->model("model_accounts");
                foreach($result_set as $row){
        
                   $data = array(
                        'like_id' => $row->like_id,
                        'username' => $row->name,
                        'user_id' => $row->user_id,
                        'fullname' => $row->fullname,
                        'user_type' => $row->type,
                        'bio' => $row->bio,
                        'wallpaper' => $row->wallpaper,
                        'following' => $this->model_accounts->check_follow_exists($user_id)
                    );
                
                   $response['likers'] = array_merge($response['likers'],array($data));
                }
                            
                
                
            }else{
              if($this->input->post('last_id')){
                $response['log'] = "No Likers";
              }else $response['log'] = "No More Likers";
                $response['success'] = 0;
            }            
        }else{
            $response['log'] = "Incomplete Data";
            $response['success'] = 0;
        }
        
        echo json_encode($response);        
        
    }    
    
    public function fetch_voice_note_by_id(){
       $this->webHeaders();
        $response = array();
        
        $this->load->library("form_validation");
        
        
        $this->form_validation->set_rules("session_user_id","","xss_clean|trim");
        $this->form_validation->set_rules("post_id","","required|xss_clean|trim");
        
        if($this->form_validation->run()){
            $this->load->model('model_collection');
            $result_set = $this->model_collection->fetch_voices_by_id($this->input->post('post_id'));
            if($result_set!=null){
                $response['success'] = 1;
                $response['log'] = "Success";
                $count = 1;
                foreach($result_set as $row){

                    $response['post_id'] = $row->post_id;
                    $response['caption'] = $row->post_caption; 
                    $response['reply_to'] = $row->reply_to;
                    $response['likes'] = $row->likes;
                    $response['listens'] =  $row->listens;
                    $response['timestamp'] = $row->timestamp;
                    $response['username'] = $row->name;
                    $response['user_id'] = $row->user_id;
                    $response['profile_pix'] = $row->wallpaper;
                    $response['audio_file'] = $row->post_track;
                    
                    $post_id = $row->post_id;
                    $response['liked']  = $this->model_collection->get_voice_like_status($post_id);
                    $response['replies'] = $this->model_collection->get_voice_replies_num($post_id);
                    $count++;
                }
            }else{
                $response['log'] = "Voice Note doesn't Exist";
                $response['success'] = 0;
            }
        }else{
            $response['log'] = "Incomplete Data";
            $response['success'] = 0;
        }
        echo json_encode($response);                
        
    }
    
}