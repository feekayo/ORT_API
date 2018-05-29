<?php


class Model_collection extends CI_Model{
 
    public function webHeaders(){
        
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    
        if(empty($_POST)){
            $_POST = json_decode(file_get_contents("php://input"),true);
        }
        
    }     
   
    //fetch data of a collection
    
    public function get_collection($collection_id){
    
        $query = $this->db->query("SELECT * FROM ort_collection WHERE collection_id=$collection_id");
        
        $num = $query->num_rows();
        
        if($num!=0){
        
            return $query->result();
        
        }else return null;
    
    }
    
    public function get_collection_path($collection_id){
    
        $query = $this->db->query("SELECT collection_directory FROM ort_collection WHERE collection_id=$collection_id");
        
        $num = $query->num_rows();
        
        if($num!=0){
        
            return $query->result();
        
        }else return null;
    
    }
    
    //fetching user data from user id;
    public function get_user_data($user_id){
    
        $query = $this->db->query("SELECT * FROM profiles WHERE user_id='$user_id'");
        
        if($query){ // if query is run
            
            return $query->result();
                
        }else return null;
    
    }
    
        //fetching user data from collection id;
    public function get_collection_user_id($collection_id){
    
        $query = $this->db->query("SELECT user_id FROM ort_collection WHERE collection_id='$collection_id'");
        
        $user_id = $query->result();
        
        foreach($user_id as $row){
        
            $user_id = $row->user_id;
            
        }
        
        return $user_id;
    
    }
    
    public function count_collection_listens($collection_id){
    
        $query = $this->db->query("SELECT * FROM ort_listens WHERE collection_id='$collection_id'");
        
        return $query->num_rows();
        
        
    }

    public function count_collection_likes($collection_id){
    
        $query = $this->db->query("SELECT * FROM ort_likes WHERE collection_id='$collection_id'");
        
        return $query->num_rows();
        
        
    }
    
    public function count_collection_comments($collection_id){
    
        $query = $this->db->query("SELECT * FROM ort_comments WHERE collection_id='$collection_id'");
        
        return $query->num_rows();
        
        
    }
    
    
    public function count_collection_subsctiptions($collection_id){
    
        $query = $this->db->query("SELECT * FROM ort_subscribe WHERE collection_id='$collection_id'");
        
        return $query->num_rows();
        
        
    }
    
    public function count_collection_tracks($collection_id){
    
        $query = $this->db->query("SELECT * FROM ort_tracks WHERE collection_id='$collection_id'");
        
        return $query->num_rows();
    
    }
    
    public function check_like_status($track_id){
        
        $this->webHeaders();
        $session_id = $this->input->post('user_id');
        
        $query = $this->db->query("SELECT * FROM ort_likes WHERE track_id='$track_id' AND user_id='$session_id'");
        
        $num = $query->num_rows();
        
        if($num==0){
        
            return false;
            
        }else return true;
    
    }
    
    public function check_like_exists($track_id){
        
        $this->webHeaders();
        if(isset($_POST['session_user_id'])){
            $session_id = $this->input->post('session_user_id');

            $query = $this->db->query("SELECT * FROM ort_likes WHERE track_id='$track_id' AND user_id='$session_id'");

            $num = $query->num_rows();

            if($num==0){

                return false;

            }else return true;
        }else return false;
    }    
    
    public function check_subscribe_status($collection_id){
        
        $this->webHeaders();
        $session_id = $this->input->post('user_id');
        
        $query = $this->db->query("SELECT * FROM ort_subscribe WHERE collection_id='$collection_id' AND user_id='$session_id'");
        
        $num = $query->num_rows();
        
        if($num==0){
        
            return false;
            
        }else return true;
    
    }
    
    public function check_subscribe_exists($collection_id){
        
        $this->webHeaders();
        if(isset($_POST['session_user_id'])){
            $session_id = $this->input->post('session_user_id');

            $query = $this->db->query("SELECT * FROM ort_subscribe WHERE collection_id='$collection_id' AND user_id='$session_id'");

            $num = $query->num_rows();

            if($num==0){

                return false;

            }else return true;
        }else return false;
    
    }
    
        //create playlist
    public function check_playlist_name($name,$user_id){
        
        $query = $this->db->query("SELECT * FROM ort_playlists WHERE playlist_name='$name' AND user_id='$user_id'");
        
        $num = $query->num_rows();
        
        if($num>0){
            return true;
        }else return false;
        
    }
    
    public function add_playlist($user_id,$name,$description){
        $data = array(
                    'user_id'=>$user_id,
                    'playlist_name' => $name,
                    'description' => $description
                );
        $query = $this->db->insert('ort_playlists',$data);
        
        if($query){
            
            return true;
            
        }else return false;
        
    }
    
    public function like_track_action($track_id){
      
        $this->webHeaders();
        $session_id = $this->input->post('user_id');
        
        $query = $this->db->query("SELECT user_id,likes FROM ort_tracks WHERE track_id='$track_id'");
        
        $user_id = $query->result();
        
        foreach($user_id as $row){
        
            $user_id = $row->user_id;
            $likes = $row->likes;
            
        }    
        $likes = $likes +1;
        
        $query2 = $this->db->query("UPDATE ort_tracks SET likes='$likes' WHERE track_id='$track_id'");
        $data = array(
        
                    'user_id'=> $session_id,
                    'track_id'=> $track_id,
                    'collection_user_id'=>$user_id
                );
        // insert into database for respects
        $query = $this->db->insert('ort_likes',$data);
        
        if($query){
            return true;
        }else return false;
        
    }
    
    public function unlike_track_action($track_id){
      $this->webHeaders();
        $session_id = $this->input->post('user_id');
        
        $query = $this->db->query("SELECT user_id,likes FROM ort_tracks WHERE track_id='$track_id'");
        
        $user_id = $query->result();
        
        foreach($user_id as $row){
        
            $user_id = $row->user_id;
            $likes = $row->likes;
            
        }    
        $likes = $likes - 1;
        
        $query2 = $this->db->query("UPDATE ort_tracks SET likes='$likes' WHERE track_id='$track_id'");

        // delete database for respects
        $query = $this->db->query("DELETE FROM ort_likes WHERE user_id='$session_id' AND track_id='$track_id'");
        
        if($query){
            return true;
        }else return false;
        
    }    
    
    public function unlike_collection_action($collection_id){
    
        $this->webHeaders();
        $session_id = $this->input->post('user_id');
        $query = $this->db->query("DELETE FROM ort_likes WHERE collection_id='$collection_id' AND user_id='$session_id'");
        
        if($query){
        
                return true;
            
        }else return false;
    
    }
   
    public function subscribe_collection_action($collection_id){
    
        $this->webHeaders();
        //return true;
        $session_id = $this->input->post('user_id');
        
        $query = $this->db->query("SELECT user_id FROM ort_collection WHERE collection_id='$collection_id'");
        
        $num = $query->num_rows();
        
        if($num>0){
            $user = $query->result();

            foreach($user as $row){

                $user_id = $row->user_id;

            }        
            $data = array(

                        'user_id'=> $session_id,
                        'collection_id'=> $collection_id,
                        'collection_user_id'=>$user_id
                    );
            // insert into database for respects
            $query = $this->db->insert('ort_subscribe',$data);

            if($query){
                return true;
            }else return false;
        }else return false;
    }
    
    public function unsubscribe_collection_action($collection_id){
    
        $this->webHeaders();
        $session_id = $this->input->post('user_id');
        $query = $this->db->query("DELETE FROM ort_subscribe WHERE collection_id='$collection_id' AND user_id='$session_id'");
        
        if($query){
        
                return true;
            
        }else return false;
        
    }
    
    public function update_trackname(){
        $this->webHeaders();
        
        $track_id = $this->input->post("track_id");
        $update = $this->input->post("update");
    
        $query = $this->db->query("UPDATE ort_tracks SET trackname='$update' WHERE track_id='$track_id'");
                
        if($query){
            return true;
            
        }else return false;
    }
    
    
    public function update_track_genre(){
        $this->webHeaders();
        
        $track_id = $this->input->post("track_id");
        $update = $this->input->post("update");
    
        $query = $this->db->query("UPDATE ort_tracks SET track_genre='$update' WHERE track_id='$track_id'");
                
        if($query){
            return true;
            
        }else return false;
    }    
  
        
    public function update_track_people(){
        $this->webHeaders();
        
        $track_id = $this->input->post("track_id");
        $update = $this->input->post("update");
    
        $query = $this->db->query("UPDATE ort_tracks SET people='$update' WHERE track_id='$track_id'");
                
        if($query){
            return true;
            
        }else return false;
    }
    
        
    public function update_downloadable($downloadable){
        $this->webHeaders();
        
        $track_id = $this->input->post("track_id");
    
        $query = $this->db->query("UPDATE ort_tracks SET downloadable='$downloadable' WHERE track_id='$track_id'");
                
        if($query){
            return true;
            
        }else return false;
    }
            //changing wallpaper
    public function update_wallpaper($collection_id){
        
        $this->webHeaders();
        $result = $this->model_collection->get_collection($collection_id);
        $user_id = $this->input->post('user_id');
        
        foreach($result as $row){
            
            $path = $row->collection_directory;
        }
        
        $dp = "$path/".$_FILES['wallpaper']['name'];
        
        $data = array(
                
                        "album_art"=>$dp
                    
                );
        
        $this->db->update("ort_collection",$data,"collection_id = $collection_id");
       // $query = $this->db->query("UPDATE users SET bio='$bio',dp='$dp' WHERE user_id='$user_id'");
        
        return true;
        
    }
    
    public function update_title($collection_id){
        
        $this->webHeaders();
        $data = array(
                
                   "collection_title"=>$this->input->post('title')
                    
                );
        
        $this->db->update("ort_collection",$data,"collection_id = $collection_id");
       // $query = $this->db->query("UPDATE users SET bio='$bio',dp='$dp' WHERE user_id='$user_id'");
        
        return true;
        
    }
    
    public function update_status($collection_id){
        
        $this->webHeaders();
        $data = array(
                
                   "collection_bio"=>$this->input->post('update')
                    
                );
        
        $this->db->update("ort_collection",$data,"collection_id = $collection_id");
       // $query = $this->db->query("UPDATE users SET bio='$bio',dp='$dp' WHERE user_id='$user_id'");
        
        return true;
        
    }
    
    public function delete_collection($collection_id){
    
        $query = $this->db->query("DELETE FROM ort_collection WHERE collection_id='$collection_id'");
        
        if($query){
            return true;
        }else return false;
    }
    
    public function add_track($collection_id){
        
        $this->webHeaders();
        $query = $this->db->query("SELECT collection_directory,collection_category FROM ort_collection WHERE collection_id=$collection_id");
        
        $result = $query->result();
        
        foreach($result as $row){
            $path = $row->collection_directory;
            $collection_category = $row->collection_category;
            
        }
        
        if(isset($_POST['download'])){
            $downloadable = 'Yes';
        }else $downloadable = 'No';
        
        $category = $this->input->post('genre');
        
        if($category=="" || $category==null){
            $category = $collection_category;   
        }
        
        $data = array(
        
                 'collection_id' => $collection_id,
                 'user_id' => $this->input->post('user_id'),
                 //in trackname replace all spaces with _
                 'trackname' => $this->input->post('title'),
                 'people' => $this->input->post('people'),
                 'track_genre' => $category,
                 'downloadable' => $downloadable,
                 'audio_file' => $this->input->post("file_path")
        
                );
        // insert into database for tracks
        $query = $this->db->insert('ort_tracks',$data);
        
        if($query){
            return true;
        }else return false;
        
        
    }
    public function user_owns_voice(){
        $this->webHeaders();
        
        $post_id = $this->input->post("post_id");
        $user_id = $this->input->post("user_id");
        
        $query = $this->db->query("SELECT * FROM ort_posts WHERE post_id='$post_id' AND user_id='$user_id'");
        
        $num = $query->num_rows();
        
        if($num>0){
            return true;
        }else{
            return false;
        }
        
    }    
    public function user_owns_tracks(){
        $this->webHeaders();
        
        $track_id = $this->input->post("track_id");
        $user_id = $this->input->post("user_id");
        
        $query = $this->db->query("SELECT * FROM ort_tracks WHERE track_id='$track_id' AND user_id='$user_id'");
        
        $num = $query->num_rows();
        
        if($num>0){
            return true;
        }else{
            return false;
        }
        
    }
    
/**     public function update_track($track_id){
        
        $query = $this->db->query("SELECT * FROM ort_collection WHERE track_id=$track_id");
        
        $result = $query->result();
        

        
        
    }
**/    
    
   
    public function get_user_collections(){
    
        $this->webHeaders();
        
        $last_id = $this->input->post("last_id");
        $order_by = $this->input->post("order_by");
        $user_id = $this->input->post("user_id");
        
        if(isset($_POST['type'])){
            $type = $this->input->post("type");   
        }
        
        
        if($last_id!=1){
            $this->db->select('ort_collection.*');
            $this->db->select('profiles.name');
            $this->db->from('ort_collection');
            $this->db->where("ort_collection.user_id",$user_id);
            
            if(isset($_POST['type']) && $_POST['type']=="Podcast"){
                $this->db->where("ort_collection.collection_type",$type);                
            }else if(isset($_POST['type']) && $_POST['type']=="Music"){
                $this->db->where("ort_collection.collection_type !=","Podcast");                  
            }
            
            if($order_by == "latest"){
                $this->db->where("ort_collection.collection_id <",$last_id);
                $this->db->order_by("ort_collection.collection_id","DESC");
            }else if ($order_by=="genre"){
                $this->db->where("ort_collection.collection_id >",$last_id);
                $this->db->order_by("ort_collection.collection_category");
            }else{
                $this->db->where("ort_collection.collection_id >",$last_id);
            }
            
            $this->db->join("profiles","ort_collection.user_id=profiles.user_id");
            $this->db->limit(25);
            
            $query = $this->db->get();
        }else{
            $this->db->select('ort_collection.*');
            $this->db->select('profiles.name');
            $this->db->from('ort_collection');
            $this->db->where("ort_collection.user_id",$user_id);
            
            if(isset($_POST['type']) && $_POST['type']=="Podcast"){
                $this->db->where("ort_collection.collection_type",$type);                
            }else if(isset($_POST['type']) && $_POST['type']=="Music"){
                $this->db->where("ort_collection.collection_type !=","Podcast");                  
            }
            
            if($order_by == "latest"){
                $this->db->order_by("ort_collection.collection_id","DESC");
            }else if ($order_by=="genre"){
                $this->db->order_by("ort_collection.collection_category");
            }
            
            $this->db->join("profiles","ort_collection.user_id=profiles.user_id");          
            $this->db->limit(25);
            
            $query = $this->db->get();
        }
        
      
        $num = $query->num_rows();
        
        if($num>0){
            return $query->result();
        }else{
            return null;
        }
    
    }
    
    public function get_collections(){
    
        $this->webHeaders();
        
        $last_id = $this->input->post("last_id");
        $order_by = $this->input->post("order_by");
        
        if(isset($_POST['type'])){
            $type = $this->input->post("type");   
        }
        
        
        if($last_id!=1){
            $this->db->select('ort_collection.*');
            $this->db->select('profiles.name');
            $this->db->from('ort_collection');
            
            if(isset($_POST['type'])){
                $this->db->where("ort_collection.collection_type",$type);                
            }
            
            if($order_by == "latest"){
                $this->db->where("ort_collection.collection_id <",$last_id);
                $this->db->order_by("ort_collection.collection_id","DESC");
            }else if ($order_by=="genre"){
                $this->db->where("ort_collection.collection_id >",$last_id);
                $this->db->order_by("ort_collection.collection_category");
            }else{
                $this->db->where("ort_collection.collection_id >",$last_id);
            }
            
            $this->db->join("profiles","ort_collection.user_id=profiles.user_id");
            $this->db->limit(25);
            
            $query = $this->db->get();
        }else{
            $this->db->select('ort_collection.*');
            $this->db->select('profiles.name');
            $this->db->from('ort_collection');
            
            if(isset($_POST['type'])){
                $this->db->where("ort_collection.collection_type",$type);                
            }
            
            if($order_by == "latest"){
                $this->db->order_by("ort_collection.collection_id","DESC");
            }else if ($order_by=="genre"){
                $this->db->order_by("ort_collection.collection_category");
            }
            
            $this->db->join("profiles","ort_collection.user_id=profiles.user_id");          
            $this->db->limit(25);
            $query = $this->db->get();
        }
        
      
        $num = $query->num_rows();
        
        if($num>0){
            return $query->result();
        }else{
            return null;
        }
    
    }    
    
    public function get_user_likes($last_id,$user_id){
        
        if($last_id!=1){
         
            $this->db->select('ort_likes.*');
            $this->db->select('profiles.name');
            $this->db->select('ort_tracks.*');
            $this->db->select('ort_collection.*');
            $this->db->from('ort_likes');
            $this->db->where('ort_likes.user_id',$user_id);
            $this->db->where('ort_likes.like_id <',$last_id);
            $this->db->order_by("ort_likes.like_id","DESC");
            $this->db->limit(25);
            $this->db->join('profiles','ort_likes.collection_user_id=profiles.user_id');
            $this->db->join('ort_tracks','ort_likes.track_id=ort_tracks.track_id');
            $this->db->join('ort_collection','ort_tracks.collection_id=ort_collection.collection_id');
            
            
            $query = $this->db->get();
        }else{
          
            $this->db->select('ort_likes.*');
            $this->db->select('profiles.name');
            $this->db->select('ort_tracks.*');
            $this->db->select('ort_collection.*');
            $this->db->from('ort_likes');
            $this->db->where('ort_likes.user_id',$user_id);
            $this->db->order_by("ort_likes.like_id","DESC");
            $this->db->limit(25);
            $this->db->join('profiles','ort_likes.collection_user_id=profiles.user_id');
            $this->db->join('ort_tracks','ort_likes.track_id=ort_tracks.track_id');
            $this->db->join('ort_collection','ort_tracks.collection_id=ort_collection.collection_id');
            
            $query = $this->db->get();           
        }
        $num = $query->num_rows();
        
        if($num>0){
            return $query->result();
        }else{
            return null;
        }      
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
    
    //add comment to collection
    public function add_comment($collection_id){
        
        $this->webHeaders();
        $query = $this->db->query("SELECT * FROM ort_collection WHERE collection_id=$collection_id");
        
        $result = $query->result();
        
        foreach($result as $row){
            $collection_creator_id = $row->user_id;
            
        }
        
        if(!$this->session->userdata('is_logged_in')){
        
            $data = array(
            
                        'collection_id' => $collection_id,
                        'collection_creator_id'=>$collection_creator_id,
                        'name'=> $this->input->post('author'),
                        'parent_id'=>'NULL',
                        'parent_path'=>'/',
                        'email'=> $this->input->post('email'),
                        'comment'=> $this->input->post('comment'),
                        'date'=> date('d-m-Y')
                
                    );
            
        }else{
            $data = array(
            
                        'collection_id' => $collection_id,
                        'collection_creator_id'=>$collection_creator_id,
                        'user_id'=>$this->input->post('user_id'),
                        'parent_id'=>'NULL',
                        'parent_path'=>'/',
                        'comment'=> $this->input->post('comment'),
                        'date'=> date('d-m-Y')
                
                    );
        
        }
        
        // insert into database for comments
        $query = $this->db->insert('ort_comments',$data);
        
        if($query){
            return true;
        }else return false;
        
    }
    
    
    
    public function fetch_parent_comments($collection_id){
    
        $query = $this->db->query("SELECT comment_id FROM ort_comments WHERE collection_id=$collection_id AND parent_id=0 ORDER BY comment_id DESC");
        
        $num = $query->num_rows();
        if($num!=0){
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
    public function get_voice_like_exists(){
        $this->webHeaders();
        
            
        $user_id = $this->input->post('user_id');
        $post_id = $this->input->post('post_id');
        
        $query = $this->db->query("SELECT * FROM ort_posts_likes WHERE user_id='$user_id' AND post_id='$post_id'");
            
        $num = $query->num_rows();
            
        if($num>0){
           return true;                
        }else return false;
        
    }    
    public function get_voice_like_status($post_id){
        $this->webHeaders();
        
        if(isset($_POST['session_user_id'])){
            
            $user_id = $this->input->post('session_user_id');
            
            $query = $this->db->query("SELECT * FROM ort_posts_likes WHERE user_id='$user_id' AND post_id='$post_id'");
            
            $num = $query->num_rows();
            
            if($num>0){
                return true;                
            }else return false;
            
        }else return false;
        
    }
    
    public function check_listened_status($track_id){
        $this->webHeaders();
        if(isset($_POST['session_user_id'])){
            $user_id = $this->input->post('session_user_id');
            $query = $this->db->query("SELECT * FROM ort_listens WHERE track_id='$track_id' AND user_id='$user_id' LIMIT 1");
            
           $num = $query->num_rows();
           
           if($num>0){
               return true;               
           }else return false;
        }else return false;
        
    }
    public function get_voice_replies_num($post_id){
        
        $query = $this->db->query("SELECT * FROM ort_posts WHERE reply_to='$post_id'");

        return $query->num_rows();
    }
    
    public function fetch_user_voices(){
        $this->webHeaders();
        $last_id = $this->input->post('last_id');
        $user_id = $this->input->post("user_id");
        
        if($last_id!=1){
            $this->db->select('ort_posts.*');
            $this->db-> select('profiles.*');
            $this->db->from('ort_posts');
            $this->db->where('ort_posts.user_id',$user_id);             
                        
            $this->db->where("ort_posts.post_id <",$last_id);
            $this->db->order_by("ort_posts.post_id", "DESC");
            $this->db->limit(25);
            $this->db->join('profiles','ort_posts.user_id=profiles.user_id');
            
            $query = $this->db->get();              
        }else{
            $this->db->select('ort_posts.*');
            $this->db-> select('profiles.*');
            $this->db->from('ort_posts');
            $this->db->where('ort_posts.user_id',$user_id);  
            $this->db->order_by("ort_posts.post_id", "DESC");
            $this->db->limit(25);
            $this->db->join('profiles','ort_posts.user_id=profiles.user_id');
            
            $query = $this->db->get();           
            
        }
        
        $num = $query->num_rows();
        
        if($num>0){
            return $query->result();
        }else{
            return null;
        }                          
    }

    public function listen_voice_note(){
        $this->webHeaders();
        
        $data = array(
            'post_id'=>$this->input->post('post_id'),
            'user_id'=>$this->input->post('user_id')
            
        );
        $query = $this->db->insert('ort_posts_listens',$data);
        
        
        if($query){
            $post_id = $this->input->post('post_id');
            $query = $this->db->query("UPDATE ort_posts SET listens=listens+1 WHERE post_id='$post_id'");
            return true;
        }else return false;
    }
    
    public function like_voice_note(){
        $this->webHeaders();
        
        $data = array(
            'post_id'=>$this->input->post('post_id'),
            'user_id'=>$this->input->post('user_id')
            
        );
        $query = $this->db->insert('ort_posts_likes',$data);
        
        
        if($query){
            $post_id = $this->input->post('post_id');
            $query = $this->db->query("UPDATE ort_posts SET likes=likes+1 WHERE post_id='$post_id'");
            return true;
        }else return false;
    }
    public function unlike_voice_note(){
        $this->webHeaders();

        $post_id = $this->input->post('post_id');
        $user_id = $this->input->post('user_id');
        $query = $this->db->query("DELETE FROM ort_posts_likes WHERE post_id='$post_id' AND user_id='$user_id'");
        
        
        if($query){
            $query = $this->db->query("UPDATE ort_posts SET likes=likes-1 WHERE post_id='$post_id'");
            return true;
        }else return false;
    }    


    public function fetch_voices_reply(){
        $this->webHeaders();
        $last_id = $this->input->post('last_id');
        $post_id = $this->input->post("post_id");
        
        if($last_id!=1){
            $this->db->select('ort_posts.*');
            $this->db-> select('profiles.*');
            $this->db->from('ort_posts');
            $this->db->where('ort_posts.reply_to',$post_id);             
                        
            $this->db->where("ort_posts.post_id <",$last_id);
            $this->db->order_by("ort_posts.post_id", "DESC");
            $this->db->limit(25);
            $this->db->join('profiles','ort_posts.user_id=profiles.user_id');
            
            $query = $this->db->get();              
        }else{
            $this->db->select('ort_posts.*');
            $this->db-> select('profiles.*');
            $this->db->from('ort_posts');
            $this->db->where('ort_posts.reply_to',$post_id);   
            $this->db->order_by("ort_posts.post_id", "DESC");
            $this->db->limit(25);
            $this->db->join('profiles','ort_posts.user_id=profiles.user_id');
            
            $query = $this->db->get();           
            
        }
        
        $num = $query->num_rows();
        
        if($num>0){
            return $query->result();
        }else{
            return null;
        }                          
    }
    
    public function home_voices(){
        
        $this->webHeaders();
        
        $user_id = $this->input->post('session_user_id');
        $last_id = $this->input->post('last_id');
        
        if($last_id!=1){
            $this->db->select('ort_posts.*');
            $this->db-> select('profiles.*');
            $this->db->from('ort_posts');
            if(isset($_POST['session_user_id'])){
                $this->db->where('ort_posts.user_id',$user_id);
                $this->db->or_where('ort_posts.user_id',"ort_follow.followed_id");                
            }
            
            $this->db->where("ort_posts.post_id <",$last_id);
            $this->db->order_by("ort_posts.post_id", "DESC");
            $this->db->limit(25);
            $this->db->join('profiles','ort_posts.user_id=profiles.user_id');
            if(isset($_POST['session_user_id'])){
                $this->db->join('ort_follow',"ort_posts.user_id=ort_follow.follower_id");                
            }
            
            $query = $this->db->get();              
        }else{
            $this->db->select('ort_posts.*');
            $this->db-> select('profiles.*');
            $this->db->from('ort_posts');
            if(isset($_POST['session_user_id'])){
                $this->db->where('ort_posts.user_id',$user_id);
                $this->db->or_where('ort_posts.user_id',"ort_follow.followed_id");                
            }
        
            $this->db->order_by("ort_posts.post_id", "DESC");
            $this->db->limit(25);
            $this->db->join('profiles','ort_posts.user_id=profiles.user_id');
            if(isset($_POST['session_user_id'])){
                $this->db->join('ort_follow',"ort_posts.ort_follow.follower_id");                
            }
            
            $query = $this->db->get();            
            
        }
        
        $num = $query->num_rows();
        
        if($num>0){
            return $query->result();
        }else{
            return null;
        }          
        
    }
    public function home_tracks(){
        
        $this->webHeaders();
        $last_id = $this->input->post('last_id');
        
        $user_id = $this->input->post('session_user_id');
        
        if($last_id !=1 ){
            
            $this->db->select('ort_tracks.*');
            $this->db->select('profiles.name');
            $this->db->select('ort_collection.*');
            if(isset($_POST['session_user_id'])){
               $this->db->select('ort_follow.*'); 
                
            }
            
            $this->db->from('ort_tracks');
            
            if(isset($_POST['session_user_id'])){
                $this->db->where('ort_tracks.user_id',$user_id);
                $this->db->or_where('ort_tracks.user_id',"ort_follow.followed_id");
            }
            
            $this->db->where('ort_tracks.track_id <',$last_id);
            $this->db->order_by("ort_tracks.track_id","DESC");
            $this->db->limit(25);
            $this->db->join('profiles','ort_tracks.user_id=profiles.user_id'); 
            $this->db->join('ort_collection','ort_tracks.collection_id=ort_collection.collection_id');
            if(isset($_POST['session_user_id'])){
                $this->db->join('ort_follow',"ort_tracks.user_id=ort_follow.followed_id");                
            }
            
            $query = $this->db->get();  
        }else{
   
            
            $this->db->select('ort_tracks.*');
            $this->db->select('profiles.name');
            $this->db->select('ort_collection.*');
            if(isset($_POST['session_user_id'])){
               $this->db->select('ort_follow.*'); 
                
            }
            
            $this->db->from('ort_tracks');
            
            if(isset($_POST['session_user_id'])){
                $this->db->where('ort_tracks.user_id',$user_id);
                $this->db->or_where('ort_tracks.user_id',"ort_follow.followed_id");
            }
        
            $this->db->order_by("ort_tracks.track_id","DESC");
            $this->db->limit(25);
            $this->db->join('profiles','ort_tracks.user_id=profiles.user_id'); 
            $this->db->join('ort_collection','ort_tracks.collection_id=ort_collection.collection_id');
            if(isset($_POST['session_user_id'])){
                $this->db->join('ort_follow',"ort_tracks.user_id=ort_follow.followed_id");                
            }
            
            $query = $this->db->get(); 
        }
        
        $num = $query->num_rows();
        
        if($num>0){
            return $query->result();
        }else{
            return null;
        }        
    }
    
    public function get_tracks_by_latest($last_id){
        
        if($last_id != 1){
        
            $this->db->select('ort_tracks.*');
            $this->db->select('profiles.name');
            $this->db->select('ort_collection.*');
            $this->db->from('ort_tracks');
            $this->db->where('ort_tracks.track_id <',$last_id);
            $this->db->order_by("ort_tracks.track_id","DESC");
            
            $this->db->limit(25);
            $this->db->join('profiles','ort_tracks.user_id=profiles.user_id');
            $this->db->join('ort_collection','ort_tracks.collection_id=ort_collection.collection_id');
            
            $query = $this->db->get();
        }else{
        
            $this->db->select('ort_tracks.*');
            $this->db->select('profiles.name');
            $this->db->select('ort_collection.*');
            $this->db->from('ort_tracks');
            $this->db->order_by("ort_tracks.track_id","DESC");
            
            $this->db->limit(25);
            $this->db->join('profiles','ort_tracks.user_id=profiles.user_id');
            $this->db->join('ort_collection','ort_tracks.collection_id=ort_collection.collection_id');
            
            $query = $this->db->get();        
        
        }
        
        $num = $query->num_rows();
        
        if($num>0){
            return $query->result();
        }else{
            return null;
        }
    
    }
    
    
                
    public function get_tracks_by_listens($last_id,$last_listens){
        
        if($last_id != 1){
                
            $this->db->select('ort_tracks.*');
            $this->db->select('profiles.name');
            $this->db->select('ort_collection.*');
            $this->db->from('ort_tracks');
            $this->db->where('ort_tracks.track_id <',$last_id);
            $this->db->where('ort_tracks.listens <',$last_listens);
            $this->db->order_by("ort_tracks.listens","DESC");
            
            $this->db->limit(25);
            $this->db->join('profiles','ort_tracks.user_id=profiles.user_id');
            $this->db->join('ort_collection','ort_tracks.collection_id=ort_collection.collection_id');
            
            $query = $this->db->get();
            
        }else{
                
            $this->db->select('ort_tracks.*');
            $this->db->select('profiles.name');
            $this->db->select('ort_collection.*');
            $this->db->from('ort_tracks');
            $this->db->order_by("ort_tracks.listens","DESC");
            
            $this->db->limit(25);
            $this->db->join('profiles','ort_tracks.user_id=profiles.user_id');
            $this->db->join('ort_collection','ort_tracks.collection_id=ort_collection.collection_id');
            
            $query = $this->db->get();       
        
        }
        
        $num = $query->num_rows();
        
        if($num>0){
            return $query->result();
            //return $query->result();
        }else{
            return null;
        }
    
    }
    
    public function get_tracks_by_likes($last_id,$last_likes){
        
        if($last_id != 1){
                
            $this->db->select('ort_tracks.*');
            $this->db->select('profiles.name');
            $this->db->select('ort_collection.*');
            $this->db->from('ort_tracks');
            $this->db->where('ort_tracks.track_id <',$last_id);
            $this->db->where('ort_tracks.likes <',$last_likes);
            $this->db->order_by("ort_tracks.likes","DESC");
            
            $this->db->limit(25);
            $this->db->join('profiles','ort_tracks.user_id=profiles.user_id');
            $this->db->join('ort_collection','ort_tracks.collection_id=ort_collection.collection_id');
            
            $query = $this->db->get();
            
        }else{
                
            $this->db->select('ort_tracks.*');
            $this->db->select('profiles.name');
            $this->db->select('ort_collection.*');
            $this->db->from('ort_tracks');
            $this->db->order_by("ort_tracks.likes","DESC");
            
            $this->db->limit(25);
            $this->db->join('profiles','ort_tracks.user_id=profiles.user_id');
            $this->db->join('ort_collection','ort_tracks.collection_id=ort_collection.collection_id');
            
            $query = $this->db->get();       
        
        }
        
        $num = $query->num_rows();
        
        if($num>0){
            return $query->result();
            //return $query->result();
        }else{
            return null;
        }
    
    }
    
   public function get_tracks_by_genre($last_id,$genre){
        
        if($last_id != 1){
                
            $this->db->select('ort_tracks.*');
            $this->db->select('profiles.name');
            $this->db->select('ort_collection.*');
            $this->db->from('ort_tracks');
            $this->db->where('ort_tracks.track_id <',$last_id);
            $this->db->where('ort_tracks.track_genre',$genre);
            $this->db->order_by("ort_tracks.track_id","DESC");
            
            $this->db->limit(25);
            $this->db->join('profiles','ort_tracks.user_id=profiles.user_id');
            $this->db->join('ort_collection','ort_tracks.collection_id=ort_collection.collection_id');
            
            $query = $this->db->get();
            
        }else{
                
            $this->db->select('ort_tracks.*');
            $this->db->select('profiles.name');
            $this->db->select('ort_collection.*');
            $this->db->from('ort_tracks');
            $this->db->where('ort_tracks.track_genre',$genre);
            $this->db->order_by("ort_tracks.track_id","DESC");
            
            $this->db->limit(25);
            $this->db->join('profiles','ort_tracks.user_id=profiles.user_id');
            $this->db->join('ort_collection','ort_tracks.collection_id=ort_collection.collection_id');
            
            $query = $this->db->get();       
        
        }
        
        $num = $query->num_rows();
        
        if($num>0){
            return $query->result();
            //return $query->result();
        }else{
            return null;
        }
    
    }    
 
    public function get_user_tracks($user_id,$last_id){
        
        if($last_id!=1){
        
            $this->db->select('ort_tracks.*');
            $this->db->select('profiles.name');
            $this->db->select('ort_collection.*');
            $this->db->from('ort_tracks');
            $this->db->where('ort_tracks.track_id <',$last_id);
            $this->db->where('ort_tracks.user_id',$user_id);
            $this->db->order_by("ort_tracks.track_id","DESC");
            $this->db->limit(25);
            $this->db->join('profiles','ort_tracks.user_id=profiles.user_id');
            $this->db->join('ort_collection','ort_tracks.collection_id=ort_collection.collection_id');
            $query= $this->db->get();
            
        }else{
        
            $this->db->select('ort_tracks.*');
            $this->db->select('profiles.name');
            $this->db->select('ort_collection.*');
            $this->db->from('ort_tracks');
            $this->db->where('ort_tracks.user_id',$user_id);
            $this->db->order_by("ort_tracks.track_id","DESC");
            $this->db->limit(25);
            $this->db->join('profiles','ort_tracks.user_id=profiles.user_id');
            $this->db->join('ort_collection','ort_tracks.collection_id=ort_collection.collection_id');
            $query= $this->db->get();         
        
        }
        
        $num = $query->num_rows();
        
        if($num>0){
            return $query->result();
        }else{
            return null;
        }
                
    
    }
    
    
    public function get_user_tracks_by_listens($user_id,$last_id,$last_listens){
        
        if($last_id!=1){
        
            $this->db->select('ort_tracks.*');
            $this->db->select('profiles.name');
            $this->db->select('ort_collection.*');
            $this->db->from('ort_tracks');
            $this->db->where('ort_tracks.track_id <',$last_id);
            $this->db->where('ort_tracks.listens <',$last_listens);
            $this->db->where('ort_tracks.user_id',$user_id);
            $this->db->order_by("ort_tracks.listens","DESC");
            $this->db->limit(25);
            $this->db->join('profiles','ort_tracks.user_id=profiles.user_id');
            $this->db->join('ort_collection','ort_tracks.collection_id=ort_collection.collection_id');
            $query= $this->db->get();
            
        }else{
        
            $this->db->select('ort_tracks.*');
            $this->db->select('profiles.name');
            $this->db->select('ort_collection.*');
            $this->db->from('ort_tracks');
            $this->db->where('ort_tracks.user_id',$user_id);
            $this->db->order_by("ort_tracks.listens","DESC");
            $this->db->limit(25);
            $this->db->join('profiles','ort_tracks.user_id=profiles.user_id');
            $this->db->join('ort_collection','ort_tracks.collection_id=ort_collection.collection_id');
            $query= $this->db->get();         
        
        }
        
        $num = $query->num_rows();
        
        if($num>0){
            return $query->result();
        }else{
            return null;
        }
                
    
    }
    
   public function get_user_tracks_by_likes($user_id,$last_id,$last_likes){
        
        if($last_id!=1){
        
            $this->db->select('ort_tracks.*');
            $this->db->select('profiles.name');
            $this->db->select('ort_collection.*');
            $this->db->from('ort_tracks');
            $this->db->where('ort_tracks.track_id <',$last_id);
            $this->db->where('ort_tracks.likes <',$last_likes);
            $this->db->where('ort_tracks.user_id',$user_id);
            $this->db->order_by("ort_tracks.listens","DESC");
            $this->db->limit(25);
            $this->db->join('profiles','ort_tracks.user_id=profiles.user_id');
            $this->db->join('ort_collection','ort_tracks.collection_id=ort_collection.collection_id');
            $query= $this->db->get();
            
        }else{
        
            $this->db->select('ort_tracks.*');
            $this->db->select('profiles.name');
            $this->db->select('ort_collection.*');
            $this->db->from('ort_tracks');
            $this->db->where('ort_tracks.user_id',$user_id);
            $this->db->order_by("ort_tracks.likes","DESC");
            $this->db->limit(25);
            $this->db->join('profiles','ort_tracks.user_id=profiles.user_id');
            $this->db->join('ort_collection','ort_tracks.collection_id=ort_collection.collection_id');
            $query= $this->db->get();         
        
        }
        
        $num = $query->num_rows();
        
        if($num>0){
            return $query->result();
        }else{
            return null;
        }
                
    
    }
    
       public function get_user_tracks_by_genre($user_id,$last_id,$genre){
        
        if($last_id!=1){
        
            $this->db->select('ort_tracks.*');
            $this->db->select('profiles.name');
            $this->db->select('ort_collection.*');
            $this->db->from('ort_tracks');
            $this->db->where('ort_tracks.track_id <',$last_id);
            $this->db->where('ort_tracks.track_genre',$genre);
            $this->db->where('ort_tracks.user_id',$user_id);
            $this->db->order_by("ort_tracks.track_id","DESC");
            $this->db->limit(25);
            $this->db->join('profiles','ort_tracks.user_id=profiles.user_id');
            $this->db->join('ort_collection','ort_tracks.collection_id=ort_collection.collection_id');
            $query= $this->db->get();
            
        }else{
        
            $this->db->select('ort_tracks.*');
            $this->db->select('profiles.name');
            $this->db->select('ort_collection.*');
            $this->db->from('ort_tracks');
            $this->db->where('ort_tracks.track_genre',$genre);
            $this->db->where('ort_tracks.user_id',$user_id);
            $this->db->order_by("ort_tracks.track_id","DESC");
            $this->db->limit(25);
            $this->db->join('profiles','ort_tracks.user_id=profiles.user_id');
            $this->db->join('ort_collection','ort_tracks.collection_id=ort_collection.collection_id');
            $query= $this->db->get();         
        
        }
        
        $num = $query->num_rows();
        
        if($num>0){
            return $query->result();
        }else{
            return null;
        }
                
    
    }
    
    public function get_collection_tracks($collection_id,$last_id){
        
        if($last_id!=1){
        
            $this->db->select('ort_tracks.*');
            $this->db->select('profiles.name');
            $this->db->select('ort_collection.*');
            $this->db->from('ort_tracks');
            $this->db->where('ort_tracks.track_id <',$last_id);
            $this->db->where('ort_tracks.collection_id',$collection_id);
            $this->db->order_by("ort_tracks.track_id","DESC");
            $this->db->limit(25);
            $this->db->join('profiles','ort_tracks.user_id=profiles.user_id');
            $this->db->join('ort_collection','ort_tracks.collection_id=ort_collection.collection_id');
            $query= $this->db->get();
            
        }else{
        
            $this->db->select('ort_tracks.*');
            $this->db->select('profiles.name');
            $this->db->select('ort_collection.*');
            $this->db->from('ort_tracks');
            $this->db->where('ort_tracks.collection_id',$collection_id);
            $this->db->order_by("ort_tracks.track_id","DESC");
            $this->db->limit(25);
            $this->db->join('profiles','ort_tracks.user_id=profiles.user_id');
            $this->db->join('ort_collection','ort_tracks.collection_id=ort_collection.collection_id');
            $query= $this->db->get();         
        
        }
        
        $num = $query->num_rows();
        
        if($num>0){
            return $query->result();
        }else{
            return null;
        }
                
    
    }
    
    public function get_collection_tracks_by_listens($collection_id,$last_id,$last_listens){
        
        if($last_id!=1){
        
            $this->db->select('ort_tracks.*');
            $this->db->select('profiles.name');
            $this->db->select('ort_collection.*');
            $this->db->from('ort_tracks');
            $this->db->where('ort_tracks.track_id <',$last_id);
            $this->db->where('ort_tracks.listens <',$last_listens);
            $this->db->where('ort_tracks.collection_id',$collection_id);
            $this->db->order_by("ort_tracks.listens","DESC");
            $this->db->limit(25);
            $this->db->join('profiles','ort_tracks.user_id=profiles.user_id');
            $this->db->join('ort_collection','ort_tracks.collection_id=ort_collection.collection_id');
            $query= $this->db->get();
            
        }else{
        
            $this->db->select('ort_tracks.*');
            $this->db->select('profiles.name');
            $this->db->select('ort_collection.*');
            $this->db->from('ort_tracks');
            $this->db->where('ort_tracks.collection_id',$collection_id);
            $this->db->order_by("ort_tracks.listens","DESC");
            $this->db->limit(25);
            $this->db->join('profiles','ort_tracks.user_id=profiles.user_id');
            $this->db->join('ort_collection','ort_tracks.collection_id=ort_collection.collection_id');
            $query= $this->db->get();         
        
        }
        
        $num = $query->num_rows();
        
        if($num>0){
            return $query->result();
        }else{
            return null;
        }
                
    
    }
    
     public function get_collection_tracks_by_likes($collection_id,$last_id,$last_likes){
        
        if($last_id!=1){
        
            $this->db->select('ort_tracks.*');
            $this->db->select('profiles.name');
            $this->db->select('ort_collection.*');
            $this->db->from('ort_tracks');
            $this->db->where('ort_tracks.track_id <',$last_id);
            $this->db->where('ort_tracks.likes <',$last_likes);
            $this->db->where('ort_tracks.collection_id',$collection_id);
            $this->db->order_by("ort_tracks.likes","DESC");
            $this->db->limit(25);
            $this->db->join('profiles','ort_tracks.user_id=profiles.user_id');
            $this->db->join('ort_collection','ort_tracks.collection_id=ort_collection.collection_id');
            $query= $this->db->get();
            
        }else{
        
            $this->db->select('ort_tracks.*');
            $this->db->select('profiles.name');
            $this->db->select('ort_collection.*');
            $this->db->from('ort_tracks');
            $this->db->where('ort_tracks.collection_id',$collection_id);
            $this->db->order_by("ort_tracks.likes","DESC");
            $this->db->limit(25);
            $this->db->join('profiles','ort_tracks.user_id=profiles.user_id');
            $this->db->join('ort_collection','ort_tracks.collection_id=ort_collection.collection_id');
            $query= $this->db->get();         
        
        }
        
        $num = $query->num_rows();
        
        if($num>0){
            return $query->result();
        }else{
            return null;
        }
                
    
    }
    
public function get_collection_tracks_by_genre($collection_id,$last_id,$genre){
        
        if($last_id!=1){
        
            $this->db->select('ort_tracks.*');
            $this->db->select('profiles.name');
            $this->db->select('ort_collection.*');
            $this->db->from('ort_tracks');
            $this->db->where('ort_tracks.track_id <',$last_id);
            $this->db->where('ort_tracks.track_genre',$genre);
            $this->db->where('ort_tracks.collection_id',$collection_id);
            $this->db->order_by("ort_tracks.track_id","DESC");
            $this->db->limit(25);
            $this->db->join('profiles','ort_tracks.user_id=profiles.user_id');
            $this->db->join('ort_collection','ort_tracks.collection_id=ort_collection.collection_id');
            $query= $this->db->get();
            
        }else{
        
            $this->db->select('ort_tracks.*');
            $this->db->select('profiles.name');
            $this->db->select('ort_collection.*');
            $this->db->from('ort_tracks');
            $this->db->where('ort_tracks.track_genre',$genre);
            $this->db->where('ort_tracks.collection_id',$collection_id);
            $this->db->order_by("ort_tracks.track_id","DESC");
            $this->db->limit(25);
            $this->db->join('profiles','ort_tracks.user_id=profiles.user_id');
            $this->db->join('ort_collection','ort_tracks.collection_id=ort_collection.collection_id');
            $query= $this->db->get();         
        
        }
        
        $num = $query->num_rows();
        
        if($num>0){
            return $query->result();
        }else{
            return null;
        }
                
    
    }    
    
    
    public function get_genre_tracks_by_latest($last_id,$genre){
        
        if($last_id!=1){
        
            $this->db->select('ort_tracks.*');
            $this->db->select('profiles.name');
            $this->db->select('ort_collection.*');
            $this->db->from('ort_tracks');
            $this->db->where('ort_tracks.track_id <',$last_id);
            $this->db->where('ort_tracks.track_genre',$genre);
            $this->db->order_by("ort_tracks.track_id","DESC");
            $this->db->limit(25);
            $this->db->join('profiles','ort_tracks.user_id=profiles.user_id');
            $this->db->join('ort_collection','ort_tracks.collection_id=ort_collection.collection_id');
            $query= $this->db->get();
            
        }else{
        
            $this->db->select('ort_tracks.*');
            $this->db->select('profiles.name');
            $this->db->select('ort_collection.*');
            $this->db->from('ort_tracks');
            $this->db->where('ort_tracks.track_genre',$genre);
            $this->db->order_by("ort_tracks.track_id","DESC");
            $this->db->limit(25);
            $this->db->join('profiles','ort_tracks.user_id=profiles.user_id');
            $this->db->join('ort_collection','ort_tracks.collection_id=ort_collection.collection_id');
            $query= $this->db->get();         
        
        }
        
        $num = $query->num_rows();
        
        if($num>0){
            return $query->result();
        }else{
            return null;
        }
                
    
    }

    public function get_genre_tracks_by_likes($last_id,$last_likes,$genre){
        
        if($last_id!=1){
        
            $this->db->select('ort_tracks.*');
            $this->db->select('profiles.name');
            $this->db->select('ort_collection.*');
            $this->db->from('ort_tracks');
            $this->db->where('ort_tracks.track_id <',$last_id);
            $this->db->where('ort_tracks.likes <',$last_likes);
            $this->db->where('ort_tracks.track_genre',$genre);
            $this->db->order_by("ort_tracks.likes","DESC");
            $this->db->limit(25);
            $this->db->join('profiles','ort_tracks.user_id=profiles.user_id');
            $this->db->join('ort_collection','ort_tracks.collection_id=ort_collection.collection_id');
            $query= $this->db->get();
            
        }else{
        
            $this->db->select('ort_tracks.*');
            $this->db->select('profiles.name');
            $this->db->select('ort_collection.*');
            $this->db->from('ort_tracks');
            $this->db->where('ort_tracks.track_genre',$genre);
            $this->db->order_by("ort_tracks.likes","DESC");
            $this->db->limit(25);
            $this->db->join('profiles','ort_tracks.user_id=profiles.user_id');
            $this->db->join('ort_collection','ort_tracks.collection_id=ort_collection.collection_id');
            $query= $this->db->get();         
        
        }
        
        $num = $query->num_rows();
        
        if($num>0){
            return $query->result();
        }else{
            return null;
        }
                
    
    }

    public function get_genre_tracks_by_listens($last_id,$last_listens,$genre){
        
        if($last_id!=1){
        
            $this->db->select('ort_tracks.*');
            $this->db->select('profiles.name');
            $this->db->select('ort_collection.*');
            $this->db->from('ort_tracks');
            $this->db->where('ort_tracks.track_id <',$last_id);
            $this->db->where('ort_tracks.listens <',$last_listens);
            $this->db->where('ort_tracks.track_genre',$genre);
            $this->db->order_by("ort_tracks.listens","DESC");
            $this->db->limit(25);
            $this->db->join('profiles','ort_tracks.user_id=profiles.user_id');
            $this->db->join('ort_collection','ort_tracks.collection_id=ort_collection.collection_id');
            $query= $this->db->get();
            
        }else{
        
            $this->db->select('ort_tracks.*');
            $this->db->select('profiles.name');
            $this->db->select('ort_collection.*');
            $this->db->from('ort_tracks');
            $this->db->where('ort_tracks.track_genre',$genre);
            $this->db->order_by("ort_tracks.listens","DESC");
            $this->db->limit(25);
            $this->db->join('profiles','ort_tracks.user_id=profiles.user_id');
            $this->db->join('ort_collection','ort_tracks.collection_id=ort_collection.collection_id');
            $query= $this->db->get();         
        
        }
        
        $num = $query->num_rows();
        
        if($num>0){
            return $query->result();
        }else{
            return null;
        }
                
    
    }
    

    
    public function fetch_track_comments($track_id,$last_id){
    
        if($last_id != 1){
            $this->db->select('ort_comments.*');
            $this->db->select('profiles.*');
            $this->db->from('ort_comments');
            $this->db->where("ort_comments.track_id","$track_id");
            $this->db->where("ort_comments.comment_id <",$last_id);
            $this->db->order_by("ort_comments.comment_id","DESC");
            $this->db->limit(25);
            $this->db->join('profiles','ort_comments.user_id=profiles.user_id');
            $query = $this->db->get();
        }else{
              $this->db->select('ort_comments.*');
              $this->db->select('profiles.*');
            $this->db->from('ort_comments');
            $this->db->where("ort_comments.track_id","$track_id");
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
    
    public function fetch_pod_subscriptions(){
        $this->webHeaders();
        
        $user_id = $this->input->post("user_id");

            
            $this->db->select('ort_subscribe.*');
            $this->db->select('profiles.*');
            $this->db->select('ort_collection.*');
            $this->db->from('ort_subscribe');
            $this->db->where("ort_subscribe.user_id",$user_id);
            
            $this->db->order_by("ort_subscribe.subs_id","DESC");
            $this->db->join('profiles','ort_subscribe.user_id=profiles.user_id');
            $this->db->join('ort_collection','ort_collection.collection_id=ort_subscribe.collection_id');
            $query = $this->db->get();
        
        $num = $query->num_rows();
        if($num!=0){
            return $query->result();
        }else{
            return null;
        }        
    }
    
    public function get_pod_subscribers($collection_id,$last_id){
        
        if($last_id!=1){
            
            $this->db->select('ort_subscribe.*');
            $this->db->select('profiles.*');
            $this->db->from('ort_subscribe');
            $this->db->where("ort_subscribe.collection_id",$collection_id);
            $this->db->where("ort_subscribe.subs_id <",$last_id);
            
            $this->db->order_by("ort_subscribe.subs_id","DESC");
            $this->db->limit(25);
            $this->db->join('profiles','ort_subscribe.user_id=profiles.user_id');
            $query = $this->db->get();
            
        }else{
            
            $this->db->select('ort_subscribe.*');
            $this->db->select('profiles.*');
            $this->db->from('ort_subscribe');
            $this->db->where("ort_subscribe.collection_id",$collection_id);            
            $this->db->order_by("ort_subscribe.subs_id","DESC");
            $this->db->limit(25);
            $this->db->join('profiles','ort_subscribe.user_id=profiles.user_id');
            $query = $this->db->get();
        
        }
        
        $num = $query->num_rows();
        if($num!=0){
            return $query->result();
        }else{
            return null;
        }
        
    
    }
    
   public function get_track_likers($track_id,$last_id){
        
        if($last_id!=1){
            
            $this->db->select('ort_likes.*');
            $this->db->select('profiles.*');
            $this->db->from('ort_likes');
            $this->db->where("ort_likes.track_id",$track_id);
            $this->db->where("ort_likes.like_id <",$last_id);
            
            $this->db->order_by("ort_likes.like_id","DESC");
            $this->db->limit(25);
            $this->db->join('profiles','ort_likes.user_id=profiles.user_id');
            $query = $this->db->get();
            
        }else{
            
            $this->db->select('ort_likes.*');
            $this->db->select('profiles.*');
            $this->db->from('ort_likes');
            $this->db->where("ort_likes.track_id",$track_id);
            
            $this->db->order_by("ort_likes.like_id","DESC");
            $this->db->limit(25);
            $this->db->join('profiles','ort_likes.user_id=profiles.user_id');
            $query = $this->db->get();
        
        }
        
        $num = $query->num_rows();
        if($num!=0){
            return $query->result();
        }else{
            return null;
        }
        
    
    }    
    public function get_voice_note_likers($post_id,$last_id){
        
        if($last_id!=1){
            
            $this->db->select('ort_posts_likes.*');
            $this->db->select('profiles.*');
            $this->db->from('ort_posts_likes');
            $this->db->where("ort_posts_likes.post_id",$post_id);
            $this->db->where("ort_posts_likes.like_id <",$last_id);
            
            $this->db->order_by("ort_posts_likes.like_id","DESC");
            $this->db->limit(25);
            $this->db->join('profiles','ort_posts_likes.user_id=profiles.user_id');
            $query = $this->db->get();
            
        }else{
            
            
            $this->db->select('ort_posts_likes.*');
            $this->db->select('profiles.*');
            $this->db->from('ort_posts_likes');
            $this->db->where("ort_posts_likes.post_id",$post_id);
            
            $this->db->order_by("ort_posts_likes.like_id","DESC");
            $this->db->limit(25);
            $this->db->join('profiles','ort_posts_likes.user_id=profiles.user_id');
            $query = $this->db->get();
        
        }
        
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
    public function fetch_comment_collection($comment_id){
        $query = $this->db->query("SELECT collection_id FROM ort_comments WHERE comment_id=$comment_id");
        
        $result = $query->result();
        
        foreach($result as $row){
            return $row->collection_id;            
        }
    }
    
    public function replyto_comment($comment_id){
        
        $query = $this->db->query("SELECT * FROM ort_comments WHERE comment_id=$comment_id");
        $comment_data = $query->result();   
        
        foreach($comment_data as $row){
            $collection_id = $row->collection_id;
            $collection_creator_id = $row->collection_creator_id;
            $profile_user_id = $row->profile_user_id;
            $parent_id = $row->parent_id;
            $parent_path = $row->parent_path;
        }
        
        if(!$this->session->userdata('is_logged_in')){
        
            $data = array(
            
                        'collection_id' => $collection_id,
                        'collection_creator_id'=>$collection_creator_id,
                        'name'=> $this->input->post('author'),
                        'profile_user_id'=> $profile_user_id,
                        'parent_id'=> $comment_id,
                        'parent_path'=>$parent_path.''.$parent_id.'/',
                        'email'=> $this->input->post('email'),
                        'comment'=> $this->input->post('comment'),
                        'date'=> date('d-m-Y')
                
                    );
            
        }else{
            $data = array(
            
                        'collection_id' => $collection_id,
                        'collection_creator_id'=>$collection_creator_id,
                        'user_id'=>$this->input->post('user_id'),
                        'profile_user_id'=> $profile_user_id,
                        'parent_id'=>$comment_id,
                        'parent_path'=>$parent_path.''.$parent_id.'/',
                        'comment'=> $this->input->post('comment'),
                        'date'=> date('d-m-Y')
                
                    );
        
        }
        // insert into database for comments
        $query = $this->db->insert('ort_comments',$data);
        
        if($query){
            return true;
        }else return false;
    
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
    
    public function collection_exists($collection_id){
        
        $query = $this->db->query("SELECT collection_id FROM ort_collection WHERE collection_id='$collection_id'");
        
        $num = $query->num_rows();
        
        if($num>0){
            return true;
        }else return false;
    
    }
    
    public function check_track_exists($trackname,$track_path,$user_id){
        $query = $this->db->query("SELECT * from ort_tracks WHERE (trackname='$trackname' OR audio_file='$track_path') AND user_id='$user_id'");
        
        $num = $query->num_rows();
        
        if($num>0){
            return true;
        }else return false;
    }
    
    public function fetch_track($track_id){
        $query = $this->db->query("SELECT * FROM ort_tracks WHERE track_id=$track_id");
        
        $num = $query->num_rows();
        
        if($num>0){
          
            return $query->result();
            
        }else{
            return null;
        }
    }
    public function update_download_num($track_id){
        
        $this->webHeaders();
        $q = $this->db->query("SELECT collection_id FROM ort_tracks WHERE track_id=$track_id");
        $result = $q->result();
        
        foreach($result as $row){
        
            $collection_id = $row->collection_id;
            
        }
        
            $user_id = $this->input->post('user_id');
        
        $data = array(
        
                'collection_id'=> $collection_id,
                'user_id'=>$user_id,
                'track_id'=>$track_id
            
                );
        $query = $this->db->insert('ort_downloads',$data);
        if($query){
            return true;
        }else return false;
    }
    
    
    public function update_track(){
        $this->webHeaders();
        
        if(isset($_POST['downloadable'])){
            $downloadable = "Yes";
        }else{
            $downloadable = "No";
        }
        $track_id = $this->input->post("track_id");
        $title = $this->input->post("title");
        $genre = $this->input->post("genre");
        $featured = $this->input->post("featured");
        
        $query = $this->db->query("UPDATE ort_tracks SET downloadable='$downloadable', trackname='$title', track_genre='$genre', people='$featured' WHERE track_id='$track_id'");
        
        if($query){
            return true;
        }else{
            return false;
        }
    }
    
    public function incrementListens($track_id,$user_id){
    
        $query = $this->db->query("SELECT listens,collection_id FROM ort_tracks WHERE track_id=$track_id");
        $result = $query->result();
        
        foreach($result as $row){
            $listens = $row->listens;
            $collection_id = $row->collection_id;
        }
        //increment listens
        $listens = $listens + 1;
    
        //update database
            
        $query = $this->db->query("UPDATE ort_tracks SET listens='$listens' WHERE track_id='$track_id'");
        
        
        
        $data = array('track_id'=>$track_id,'collection_id'=>$collection_id,'user_id'=>$user_id);
        $query = $this->db->insert('ort_listens',$data);

    
    }
    //Check query here later when you have implemented audio feeds
    public function get_user_popular_not_in_collection($user_id){
    
        $query = $this->db->query("SELECT * FROM ort_tracks WHERE user_id=$user_id AND collection_id!=0 ORDER BY listens DESC LIMIT 4");
        
        $num = $query->num_rows();
        
        if($num>0){
            return $query->result();
        }else return null;
    
    } 
    
    //get popular tracks of a genre
    public function get_genre_popular($genre){
    
        $query = $this->db->query("SELECT * FROM ort_tracks WHERE track_genre='$genre' ORDER BY listens DESC LIMIT 3");
        
        $num = $query->num_rows();
        
        if($num>0){
            return $query->result();
        }else return null;
        
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
    
    public function get_popular_genre_tracks($type){
        
        $query = $this->db->query("SELECT * FROM ort_tracks WHERE track_genre='$type' ORDER BY listens DESC LIMIT 10");
    
        $num = $query->num_rows();
        
        if($num>0){
            return $query->result();
        }else return null;
    
    }
    public function get_latest_genre_tracks($type){
        $query = $this->db->query("SELECT * FROM ort_tracks WHERE track_genre='$type' ORDER BY track_id DESC LIMIT 10");
    
        $num = $query->num_rows();
        
        if($num>0){
            return $query->result();
        }else return null;
    
    }
    
        public function get_relevant_posts(){
        if($this->session->userdata('is_logged_in')){
            
            //fetch where mine and that of those I follow
            $session_id = $this->input->post('user_id');
            $query = $this->db->query("SELECT ort_posts.* FROM ort_posts WHERE ort_posts.reply_to=0 AND (ort_posts.user_id=$session_id OR ort_posts.user_id=ort_follow.followed_id) INNER JOIN ort_follow ON ort_posts.user_id=ort_follow.follower_id ORDER BY post_id DESC LIMIT 25");
        
        }else{
        
            $query = $this->db->query("SELECT * FROM ort_posts WHERE reply_to=0 ORDER BY post_id DESC LIMIT 25");
        }
        
        $num = $query->num_rows();
        /**
        // if i'm not up to 25 find that of those who are not me and not the ones i follow
        if(($num<25) && ($this->session->userdata('is_logged_in'))){
        
            $left_num = 25-$num;
            
            $session_id = $this->input->post('user_id');
            $query = $this->db->query("SELECT ort_posts.* FROM ort_posts WHERE ort_posts.reply_to=0 AND (ort_posts.user_id!=$session_id OR ort_posts.user_id!=ort_follow.followed_id) INNER JOIN ort_follow ON ort_posts.user_id=ort_follow.follower_id ORDER BY post_id DESC LIMIT $left_num");
            
            
        }**/
        
        if($num>0){
            
            return $query->result();
        
        }else return null;
    
    }
    
    //check if user created collection and hence authorize change
    public function auth_collection_change($collection_id,$user_id){
        $query = $this->db->query("SELECT * FROM ort_collection WHERE collection_id='$collection_id' AND user_id='$user_id'");
        
        $num = $query->num_rows();
        
        if($num > 0){
            return true;
        }else{
            return false;
        }
    }
    
    //check if track exists in user playlist
    public function check_playlist($track_id,$play_id){
        
        
        $query = $this->db->query("SELECT * FROM ort_playlists_tracks WHERE track_id='$track_id' AND play_id='$play_id'");
        
        $num = $query->num_rows();
        
        if($num > 0){
            return false;
        }else{
            return true;
        }
    }
    
    
    public function check_playlist_owner($play_id,$user_id){
        $query = $this->db->query("SELECT * FROM ort_playlists WHERE play_id='$play_id' AND user_id='$user_id'");
        
        $num = $query->num_rows();
        if($num>0){
            return true;
        }else return false;
    }
    
    public function add_to_playlist(){
        $this->webHeaders();
        $data = array(
        
             'track_id' => $this->input->post('track_id'),
             'play_id' => $this->input->post('play_id'),

        );
        // insert into database for tracks
        $query = $this->db->insert('ort_playlists_tracks',$data);
        
        if($query){
            return true;
        }else return false;
    
    }
    
    public function fetch_user_playlists($user_id){
        
        $query = $this->db->query("SELECT * FROM ort_playlists WHERE user_id='$user_id'");
        
        $num = $query->num_rows();
        
        if($num>0){
            return $query->result();
        }else return null;
        
    }
    
    public function remove_from_playlist($track_id,$play_id){
    
        $query = $this->db->query("DELETE FROM ort_playlists_tracks WHERE track_id='$track_id' AND play_id='$play_id'");
        
        if($query){
            return true;
        }else return false;
    }
    
    public function delete_playlist($play_id){
        $query = $this->db->query("DELETE FROM ort_playlists WHERE play_id='$play_id'");
        
        if($query){
            return true;
        }else return false;
        
    }
    
    public function delete_track(){
        
        $this->webHeaders();
        $track_id = $this->input->post("track_id");
        
        $query1 = $this->db->query("DELETE FROM ort_likes WHERE track_id='$track_id'");
        $query2 = $this->db->query("DELETE FROM ort_playlists_tracks WHERE track_id='$track_id'");
        $query3 = $this->db->query("DELETE FROM ort_tracks WHERE track_id='$track_id'");
        
        if($query3){
            return true;
        }else return false;
    }
    
    public function fetch_playlist_tracks($play_id,$last_id){
        
        if($last_id !=1){
            
            $this->db->select("ort_playlists_tracks.*");
            $this->db->select("ort_tracks.*");
            $this->db->select("profiles.name");
            $this->db->select("ort_collection.collection_id");
            $this->db->select("ort_collection.collection_title");
            
            
            $this->db->from('ort_playlists_tracks');
            $this->db->where("ort_playlists_tracks.play_id","$play_id");
            $this->db->where("ort_playlists_tracks.id <",$last_id);
            $this->db->order_by("ort_playlists_tracks.id","DESC");
            $this->db->limit(25);
            $this->db->join('ort_tracks','ort_tracks.track_id=ort_playlists_tracks.track_id');
            $this->db->join('profiles','profiles.user_id=ort_tracks.user_id');
            $this->db->join('ort_collection','ort_collection.collection_id=ort_tracks.collection_id');
            
            $query = $this->db->get();
        }else{
        
            $this->db->select("ort_playlists_tracks.*");
            $this->db->select("ort_tracks.*");
            $this->db->select("profiles.name");
            $this->db->select("ort_collection.collection_id");
            $this->db->select("ort_collection.collection_title");
            
            
            $this->db->from('ort_playlists_tracks');
            $this->db->where("ort_playlists_tracks.play_id","$play_id");
            $this->db->order_by("ort_playlists_tracks.id","DESC");
            $this->db->limit(25);
            $this->db->join('ort_tracks','ort_tracks.track_id=ort_playlists_tracks.track_id');
            $this->db->join('profiles','profiles.user_id=ort_tracks.user_id');
            $this->db->join('ort_collection','ort_collection.collection_id=ort_tracks.collection_id');
            
            $query = $this->db->get();
        
        }
        if($query->num_rows()>0){
            return $query->result();
        }else return null;
    
    }
    
    public function add_voice_note(){
        
        $this->webHeaders();
        
        $data = array(
            
            'post_caption' => $this->input->post('caption'),
            'post_track' => base_url().'uploads/'.$this->input->post('user_id')."/voices/".$this->input->post('filename'),
            'user_id' => $this->input->post('user_id'),
            'reply_to' => $this->input->post('reply_to')
        );
        
        if($this->db->insert('ort_posts',$data)){
            
            return true;
            
        }else return false;
        
    }
    
    public function delete_voice(){
       
        $this->webHeaders();
        
        $post_id = $this->input->post('post_id');
        
        $query = $this->db->query("DELETE FROM ort_posts WHERE post_id='$post_id'");
        
        if($query){
            return true;
        }else return false;
        
    }
    
    public function update_caption(){
        
        $this->webHeaders();
        
        $caption = $this->input->post('caption');
        $post_id = $this->input->post('post_id');
        
        $query = $this->db->query("UPDATE ort_posts SET post_caption='$caption' WHERE post_id='$post_id'");
        
        if($query){
            return true;
        }else return false;        
    }
    
}