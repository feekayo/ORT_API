<?php


class Model_music extends CI_Model{
   public function webHeaders(){
        
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    
        if(empty($_POST)){
            $_POST = json_decode(file_get_contents("php://input"),true);
        }
        
    }     
   
    // check input title
    public function check_collection_title(){
        
        $this->webHeaders();
        // comparing db parameters
        $this->db->where('collection_title',$this->input->post('title'));
        $this->db->where('user_id',$this->input->post('user_id'));
        
        // db table
        $query = $this->db->get('ort_collection');
        
        // if result matches
        if($query->num_rows() > 0){
        
            return false;    // allow login
            
        } else{
        
            return true; // do not allow login
        
        }
        
    }
   public function get_collection_id(){
        $this->webHeaders();
        // comparing db parameters
        $this->db->select('collection_id');
        $this->db->from('ort_collection');
        $this->db->where('collection_title',$this->input->post('title'));
        $this->db->where('user_id',$this->input->post('user_id'));
        
        $query = $this->db->get();
        
        if($query){
            
            //$collection_id;
            foreach($query->result() as $row){
                $collection_id = $row->collection_id;
            }
            return $collection_id;
        }else{
            return 0;
        }
        
    }    
    
    //creating new collection
    public function new_collection(){
        
        $this->webHeaders();
        $data = array(
        
            "user_id" => $this->input->post('user_id'),
            "collection_title" => $this->input->post('title'),
            //"collection_bio" => $this->input->post('description'),
            //"collection_type" => $this->input->post('type'),
            "collection_category" => $this->input->post('genre'),
            "collection_directory" =>  base_url()."uploads/".$this->input->post('user_id')."/collections/".preg_replace('/[^a-z0-9]+/i', '_', $this->input->post('title')),
            "date" => date('d-m-Y'),
                    
        );
        
        $query = $this->db->insert('ort_collection',$data);
    
        if($query){
            return true;
        }else{
            return false;
        }
        
    }
    
    public function get_popular_genre_tracks($type){
        
        $query = $this->db->query("SELECT * FROM ort_tracks WHERE track_genre!='' ORDER BY listens DESC LIMIT 1");
    
        $num = $query->num_rows();
        
        if($num>0){
            return $query->result();
        }else return null;
    
    }
    public function get_latest_genre_tracks($type){
        $query = $this->db->query("SELECT * FROM ort_tracks WHERE track_genre!='' ORDER BY track_id DESC LIMIT 1");
    
        $num = $query->num_rows();
        
        if($num>0){
            return $query->result();
        }else return null;
    
    }
    

    
}    