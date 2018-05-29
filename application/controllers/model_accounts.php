<?php


class Model_accounts extends CI_Model{
    
    public function webHeaders(){
        
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    
        if(empty($_POST)){
            $_POST = json_decode(file_get_contents("php://input"),true);
        }
        
    }     

    public function logout($mykey,$user_id){
        $query = $this->db->query("DELETE FROM ort_sessions WHERE session_key='$mykey' AND user_id='$user_id'");
        
        if($query){
            return true;
        }else return false;
    }
    
    // add data to temporary database
    public function add_temp_user($key){
       
        $this->webHeaders();
        $data = array(
        
            'name' => $this->input->post('username'),
            
            'fullname' => $this->input->post('fullname'),
                
            'email' => $this->input->post('email'),
            
            'password' => md5($this->input->post('password')),
            
            'type' => $this->input->post('type'),
            
            //'bio' => $this->input->post('bio'),
            
            'mykey'=>$key
                    
        );
        
        $query = $this->db->insert('temp_users',$data);
    
        if($query){
            return true;
        }else{
            return false;
        }
        
    }
    
    //app url key checker
    public function url_key_is_valid($key,$user_id){
        
        $query = $this->db->query("SELECT * FROM ort_sessions WHERE user_id='$user_id' AND session_key='$key'");
            
        if($query->num_rows()==1){
            return true;
        }else return false;
    
    }
    
    
    //check user existence
    public function check_user($user_id){
    
        $query = $this->db->query("SELECT * FROM profiles WHERE user_id='$user_id'");
        
        if($query->num_rows()==1){
            return true;
        }else return false;
    }
    
    
    //account validation key checker
     public function is_key_valid($key){
        
        $this->db->where('mykey',$key);
        $query = $this->db->get('temp_users');
        
        if($query->num_rows() == 1){
        
            return true;
            
        }else return false;
    
        
    }
    
    
    // adding user to permanent database
    public function add_user($key){
    
        $this->db->where('mykey',$key);
        $temp_user = $this->db->get('temp_users'); // query to get temp user
    
        if($temp_user){
        
            $row = $temp_user->row();
            
            $data = array(
            
                'name' => $row->name,
                'fullname' => $row->fullname,
                'password' => $row->password,
                //'bio' => $row->bio,
                'email' => $row->email,
                'type'=> $row->type,
                'date'=> date("d-m-Y")
            
            );
            $did_add_user = $this->db->insert('profiles',$data); // query to add user to permanent database
            
        }
        
        if($did_add_user){
        
            $this->db->where('mykey',$key);
            $this->db->delete('temp_users');
            return true;
        }else{
        
            return false;
        
        }
        
    }
    
    
    
    //create user session for ort_app
    public function create_session_ort($user_id,$uniqid,$platform){

       $data = array(
            
            'user_id'=> $user_id,
            'session_key'=> $uniqid,
            'platform'=>$platform
       
       );
       $query = $this->db->insert('ort_sessions',$data);//"INSERT INTO ort_sessions(user_id,session_key)";
        
        
        
        if($query){
            return true;
        }else return false;
    }
    //fetching user data from email address
    public function get_user_data_email($email){
    
        $query = $this->db->query("SELECT * FROM profiles WHERE email='$email' OR name='$email'");
        
        if($query){ // if query is run
            
            return $query->result();
                
        }else return null;
    
    }
    
    public function password_changed($user_id){
    
        $this->webHeaders();
        $password = md5($this->input->post('password'));
        $query = $this->db->query("UPDATE profiles SET password='$password' WHERE user_id='$user_id'");
        
        if($query){
            return true;
        }else return false;
    }
    
    
    //fetching user data;
    public function get_user_data($user){
    
        $query = $this->db->query("SELECT * FROM profiles WHERE name='$user' OR user_id='$user'" );
        
        if($query){ // if query is run
            
            return $query->result();
                
        }else return null;
    
    }
    
    public function get_is_followed_num($user_id){
        
        $query = $this->db->query("SELECT follow_id FROM ort_follow WHERE followed_id='$user_id'");
        
        return $query->num_rows();
    
    }
    
    public function get_is_following_num($user_id){
        
        $query = $this->db->query("SELECT follow_id FROM ort_follow WHERE follower_id='$user_id'");
        
        return $query->num_rows();
    
    }

    public function get_tracks_num($user_id){
    
        $query = $this->db->query("SELECT track_id FROM ort_tracks WHERE user_id='$user_id'");
        
        return $query->num_rows();
    }
    
    public function get_user_listens_num($user_id){
    
        $query = $this->db->query("SELECT track_id FROM ort_listens WHERE user_id='$user_id'");
        
        return $query->num_rows();
    }
    // check user login parameters
    public function can_log_in(){
        
        $this->webHeaders();
        $email = $this->input->post('email');
        $password = $this->input->post('password');
        
        $query = $this->db->query("SELECT * FROM profiles WHERE (email='$email' OR name='$email') AND password='$password'");
        
        // if result matches
        if($query->num_rows() == 1){
        
            return true;    // allow login
            
        } else{
        
            return false; // do not allow login
        
        }
        
    }
    
            // check user login parameters
    public function valid_email(){
        
        $this->webHeaders();
        $email = $this->input->post('email');
        
        $query = $this->db->query("SELECT * FROM profiles WHERE email='$email'");
        
        // if result matches
        if($query->num_rows() == 1){
        
            return true;    // allow login
            
        } else{
        
            return false; // do not allow login
        
        }
        
    }
    
    //change password insert key into database
    public function forgot_action($key){
        
        $this->webHeaders();
        $email = $this->input->post('email');
        $query = $this->db->query("DELETE FROM password_change WHERE email='$email'");
        
        $data = array(
            
                    'email'=> $email,
                    'mykey'=> $key
                );
        
        // insert into database for respects
        $query = $this->db->insert('password_change',$data);    
        
        if($query){
            return true;
        }else return false;
        
    }
    
        //changing wallpaper
    public function change_wallpaper($user_id){
    
        $this->webHeaders();
        $dp =  base_url()."/uploads/$user_id/wallpapers/".$_FILES['wallpaper']['name'];
        
        $data = array(
                
                        "wallpaper"=>$dp
                    
                );
        
        $this->db->update("profiles",$data,"user_id = $user_id");
       // $query = $this->db->query("UPDATE users SET bio='$bio',dp='$dp' WHERE user_id='$user_id'");
        
        return true;
        
    }
    
    
    //check if inputted key is valid
    public function change_key_is_valid($key){
        
        $query = $this->db->query("SELECT * FROM password_change WHERE mykey='$key'");
        $num = $query->num_rows();
        
        if($num==0){
            return false;
        }else{
            return true;
        }
    
    }
    
    
    //fetch data from password_change table
    public function get_password_change_data($key){
        $query = $this->db->query("SELECT * FROM password_change WHERE mykey='$key' LIMIT 1");   
        return $query->result();
    }
    
    //delete password_change row with key
    public function delete_change_password_row($key){
        $query = $this->db->query("DELETE FROM password_change WHERE mykey='$key'");
    }
    
    
    //check if user exists
    
    public function account_exists($user){
    
        $query = $this->db->query("SELECT * FROM profiles WHERE name='$user' OR user_id='$user'");
        
        $num = $query->num_rows();
        
        if($num==0){
            return false;
        }else{
            return true;
        }
    }
    
    public function update_username(){
       $this->webHeaders();
       
       $username = $this->input->post('update');
       $user_id = $this->input->post('user_id');
       $query = $this->db->query("UPDATE profiles SET name='$username' WHERE user_id='$user_id'");
       
       if($query){
           return true;           
       }else return false;
    }
    
    public function update_fullname(){
       $this->webHeaders();
       
       $fullname = $this->input->post('update');
       $user_id = $this->input->post('user_id');
       $query = $this->db->query("UPDATE profiles SET fullname='$fullname' WHERE user_id='$user_id'");
       
       if($query){
           return true;           
       }else return false;
    }    
    
    public function update_type(){
       $this->webHeaders();
       
       $type = $this->input->post('update');
       $user_id = $this->input->post('user_id');
       $query = $this->db->query("UPDATE profiles SET type='$type' WHERE user_id='$user_id'");
       
       if($query){
           return true;           
       }else return false;
    }
    
    //updating status
    public function model_update_status($user_id){
    
        $this->webHeaders();
        $data = array(
                
                        "bio"=>$this->input->post('update')
                    
                );
        
        $this->db->update("profiles",$data,"user_id = $user_id");
       // $query = $this->db->query("UPDATE users SET bio='$bio',dp='$dp' WHERE user_id='$user_id'");
        
        return true;
        
    }
    
    //checking if session user is following noted profile owner b4
    public function check_follow_status($user_id){
    
        $session_id = $this->input->post('user_id');
        
        $query = $this->db->query("SELECT * FROM ort_follow WHERE follower_id='$session_id' AND followed_id='$user_id'");
        $num = $query->num_rows();
        
        if($num==0){
        
            return false;
            
        }else return true;
    
    }
    
    public function check_follow_exists($user_id){
        $this->webHeaders();
        if(isset($_POST['session_user_id'])){
            $session_id = $this->input->post('session_user_id');

            $query = $this->db->query("SELECT * FROM ort_follow WHERE follower_id='$session_id' AND followed_id='$user_id'");
            $num = $query->num_rows();

            if($num==0){

                return false;

            }else return true;
        }else return false;
    }    
    public function check_mutual_follows($user_id){
    
        $this->webHeaders();
        $session_id = $this->input->post('user_id');
        
        $query1 = $this->db->query("SELECT * FROM ort_follow WHERE follower_id='$session_id' AND followed_id='$user_id'");
        $num1 = $query1->num_rows();

        $query2 = $this->db->query("SELECT * FROM ort_follow WHERE follower_id='$user_id' AND followed_id='$session_id'");
        $num2 = $query2->num_rows();
        
        if($num1==0 || $num2==0){
            return false;
        }else return true;
        
    }
    
    public function model_follow($user_id){
    
        $this->webHeaders();
        $session_id = $this->input->post('user_id');
        
        $query = $this->db->query("SELECT * FROM ort_follow WHERE follower_id='$session_id' AND followed_id='$user_id'");
        
        $num = $query->num_rows();
            
        if($num==0){
        
             $data = array(
            
                            'follower_id'=> $session_id,
                            'followed_id'=> $user_id
                    );
            // insert into database for respects
            $query = $this->db->insert('ort_follow',$data);
        
            if($query){
                return true;
            }else return false;
        }else return true;
    }
    
    public function model_unfollow($user_id){
            $this->webHeaders();
            $session_id = $this->input->post('user_id');    
            
            $query = $this->db->query("SELECT * FROM ort_follow WHERE follower_id='$session_id' AND followed_id='$user_id'");    
        
            $num = $query->num_rows();
        
            if($num>0){
                
                $results = $query->result();
                
                foreach($results as $row){
                    $id = $row->follow_id;
                }
                
                $this->db->where('follow_id',$id);
                $this->db->delete('ort_follow');
                
                return true;
            }else return true;
    }
    
    
    
    public function get_following_num($user_id){
    
        $query = $this->db->query("SELECT * FROM ort_follow WHERE follower_id='$user_id'");
    
        return $query->num_rows();
    }
    
    public function get_followers_num($user_id){
    
        $query = $this->db->query("SELECT * FROM ort_follow WHERE followed_id='$user_id'");
    
        return $query->num_rows();
    }
    
    public function get_audio_num($user_id){
    
        $query = $this->db->query("SELECT * FROM ort_tracks WHERE user_id='$user_id'");
    
        return $query->num_rows();
    }
    
    public function get_comments_num($user_id){
    
        $query = $this->db->query("SELECT * FROM ort_comments WHERE user_id='$user_id' OR collection_creator_id='$user_id'");
        
        return $query->num_rows();
    }
    
    public function get_listens_num($user_id){
    
        $query = $this->db->query("SELECT * FROM ort_listens WHERE user_id='$user_id'");
        
        return $query->num_rows();
    }
    
    public function get_following($user_id){
    
        $query = $this->db->query("SELECT * FROM ort_follow WHERE follower_id='$user_id' ORDER BY follow_id DESC LIMIT 5");
    
        $num = $query->num_rows();
        
        if($num>0){
        
            return $query->result();
            
        }else return null;
        
    }
    
    public function get_user_music_collections($user_id){
    
        $query = $this->db->query("SELECT * FROM ort_collection WHERE user_id='$user_id' AND collection_type!='Podcast' ORDER BY collection_id DESC LIMIT 3");
        
        $num = $query->num_rows();
        
        if($num>0){
        
            return $query->result();
        
        }else return null;
    
    }

    public function get_user_pods_collections($user_id){
    
        $query = $this->db->query("SELECT * FROM ort_collection WHERE user_id='$user_id' AND collection_type='Podcast' ORDER BY collection_id DESC LIMIT 3");
        
        $num = $query->num_rows();
        
        if($num>0){
        
            return $query->result();
        
        }else return null;
    
    }
    
    public function get_num_tracks($collection_id){
    
        $query = $this->db->query("SELECT * FROM ort_tracks WHERE collection_id='$collection_id'");
        
        return  $query->num_rows();
        
    }
    
    public function get_num_comments($user_id){
    
        $query = $this->db->query("SELECT * FROM ort_comments WHERE profile_user_id=$user_id");
        
        return $query->num_rows();
        
    }
    //add comment to user profile page
    public function add_comment_live($user_id){

        $this->webHeaders();
            $data = array(
            
                        'profile_user_id'=> $user_id,
                        'user_id'=>$this->input->post('session_user_id'),
                        'parent_id'=>'NULL',
                        'parent_path'=>'/',
                        'comment'=> $this->input->post('comment'),
                        'date'=> date('d-m-Y')
                
                    );
        
        
        
        // insert into database for comments
        $query = $this->db->insert('ort_comments',$data);
        
        if($query){
            return true;
        }else return false;
        
    }
    
    
    
      //add comment to user profile page
    public function add_comment_track($track_id){
        $this->webHeaders();
            $data = array(
            
                        'track_id'=> $track_id,
                        'user_id'=>$this->input->post('user_id'),
                        'parent_id'=>'NULL',
                        'parent_path'=>'/',
                        'comment'=> $this->input->post('comment'),
                        'date'=> date('d-m-Y')
                
                    );
        
        
        
        // insert into database for comments
        $query = $this->db->insert('ort_comments',$data);
        
        if($query){
            return true;
        }else return false;
        
    }
    
    public function fetch_live_comments($user_id,$last_id){
    
        if($last_id != 1){
            $this->db->select('ort_comments.*');
            $this->db->select('profiles.name');
            $this->db->from('ort_comments');
            $this->db->where("ort_comments.profile_user_id","$user_id");
            $this->db->where("ort_comments.comment_id <",$last_id);
            $this->db->order_by("ort_comments.comment_id","DESC");
            $this->db->limit(25);
            $this->db->join('profiles','ort_comments.user_id=profiles.user_id');
            $query = $this->db->get();
        }else{
              $this->db->select('ort_comments.*');
              $this->db->select('profiles.name');
            $this->db->from('ort_comments');
            $this->db->where("ort_comments.profile_user_id","$user_id");
            $this->db->order_by("ort_comments.comment_id","DESC");
            $this->db->limit(25);
            $this->db->join('profiles','ort_comments.user_id=profiles.user_id');
            $query = $this->db->get();      
        
        }
        
        
        $num = $query->num_rows();
        if($num!=0){
            return $query->result();
        }else{
            return null;
        }
        
    }
    
     public function get_user_followers($user_id,$last_id){
        
        if($last_id!=1){
        
            $this->db->select("ort_follow.*");
            $this->db->select("profiles.*");
            $this->db->from('ort_follow');
            $this->db->where('ort_follow.followed_id',"$user_id");
            $this->db->where('ort_follow.follow_id <',"$last_id");
            $this->db->order_by("ort_follow.follow_id","DESC");
            $this->db->limit(25);
            $this->db->join('profiles','ort_follow.follower_id=profiles.user_id');
            $query = $this->db->get();
        }else{
         
            $this->db->select("ort_follow.*");
            $this->db->select("profiles.*");
            $this->db->from('ort_follow');
            $this->db->where('ort_follow.followed_id',"$user_id");
            $this->db->order_by("ort_follow.follow_id","DESC");
            $this->db->limit(25);
            $this->db->join('profiles','ort_follow.follower_id=profiles.user_id');
            $query = $this->db->get();       
        
        
        }
        
        $num = $query->num_rows();
        
        if($num>0){
            return $query->result();
        }else{
            return null;
        }
                
    
    }    
    
     public function get_user_following($user_id,$last_id){
        
        if($last_id!=1){
        
            $this->db->select("ort_follow.*");
            $this->db->select("profiles.*");
            $this->db->from('ort_follow');
            $this->db->where('ort_follow.follower_id',"$user_id");
            $this->db->where('ort_follow.follow_id <',"$last_id");
            $this->db->order_by("ort_follow.follow_id","DESC");
            $this->db->limit(25);
            $this->db->join('profiles','ort_follow.followed_id=profiles.user_id');
            $query = $this->db->get();
        }else{
         
            $this->db->select("ort_follow.*");
            $this->db->select("profiles.*");
            $this->db->from('ort_follow');
            $this->db->where('ort_follow.follower_id',"$user_id");
            $this->db->order_by("ort_follow.follow_id","DESC");
            $this->db->limit(25);
            $this->db->join('profiles','ort_follow.followed_id=profiles.user_id');
            $query = $this->db->get();       
        
        
        }
        
        $num = $query->num_rows();
        
        if($num>0){
            return $query->result();
        }else{
            return null;
        }
                
    
    }        
    
    public function fetch_children_comments($comment_id){
    
        $query = $this->db->query("SELECT comment_id FROM ort_comments WHERE parent_id=$comment_id");
        
        $num = $query->num_rows();
        if($num!=0){
            return $query->result();
        }else{
            return null;
        }
        
    }
   
    public function fetch_comment_data($comment_id){
        $query = $this->db->query("SELECT * FROM ort_comments WHERE comment_id=$comment_id");
        
        return $query->result();
    }
    
    public function get_collection($collection_id){
    
        $query = $this->db->query("SELECT * FROM ort_collection WHERE collection_id=$collection_id");
        
        $num = $query->num_rows();
        
        if($num!=0){
        
            return $query->result();
        
        }else return null;
    
    }
        //Check query here later when you have implemented audio feeds
    public function get_user_popular_not_in_collection($user_id){
    
        $query = $this->db->query("SELECT * FROM ort_tracks WHERE user_id=$user_id AND collection_id!=0 ORDER BY listens DESC LIMIT 4");
        
        $num = $query->num_rows();
        
        if($num>0){
            return $query->result();
        }else return null;
    
    } 
    public function count_track_listens($track_id){
        $query = $this->db->query("SELECT track_id FROM ort_listens WHERE track_id=$track_id");
        
        return $query->num_rows();
    }
    
    public function count_track_downloads($track_id){
        $query = $this->db->query("SELECT track_id FROM ort_downloads WHERE track_id=$track_id");
        
        return $query->num_rows();
    }
    public function get_track($track_id){
        $query = $this->db->query("SELECT * from ort_tracks WHERE track_id=$track_id");
        
        return $query->result();
    }
    
    //fetch latest tracks
    public function get_latest_tracks(){
        
        $query = $this->db->query("SELECT * FROM ort_tracks WHERE track_genre!='' ORDER BY track_id DESC LIMIT 3");
    
        $num = $query->num_rows();
        
        if($num>0){
            return $query->result();
        }else return null;
    }
    
    //fetch popular tracks
    public function get_popular_tracks(){
        
        $query = $this->db->query("SELECT * FROM ort_tracks WHERE track_genre!='' ORDER BY listens DESC LIMIT 3");
    
        $num = $query->num_rows();
        
        if($num>0){
            return $query->result();
        }else return null;
    }

    //fetch a users subscriptions
    public function get_subscriptions($user_id){
    
        $query = $this->db->query("SELECT * FROM ort_subscribe WHERE user_id=$user_id");
        $num = $query->num_rows();
        
        if($num>0){
            return $query->result();
        }else return null;
    
    }
    
    //fetch number of user subscriptions
    public function get_subscriptions_num($user_id){
    
        $query = $this->db->query("SELECT * FROM ort_subscribe WHERE user_id=$user_id");
        return $query->num_rows();
        
    }
    
    public function get_collection_listens_num($collection_id){
    
        $this->webHeaders();
        $user_id = $this->input->post('user_id');
        
        $query = $this->db->query("SELECT * FROM ort_listens WHERE user_id=$user_id AND collection_id=$collection_id  GROUP BY track_id");
        
        return $query->num_rows();
    
    }
    
    public function get_collection_path($collection_id){
    
        $query = $this->db->query("SELECT collection_directory FROM ort_collection WHERE collection_id=$collection_id");
        
        $num = $query->num_rows();
        
        if($num!=0){
        
            return $query->result();
        
        }else return null;
    
    }
    
}