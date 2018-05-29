<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Accounts extends CI_Controller {

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
    
    public function login(){
        $this->webHeaders();
                
        $this->load->library("form_validation");

        $this->form_validation->set_rules("email","Email","required|xss_clean|trim");
        $this->form_validation->set_rules("password","Password","required|xss_clean|md5|trim");
        $this->form_validation->set_rules("platform","Platform","required|xss_clean|trim");    
        
        $response = array();
        
        $this->load->model("model_accounts");
        
        if( $this->form_validation->run() == FALSE ){
            
            $response['log'] = "Incomplete Data";
            $response['success'] = 0;
            
        }else if($this->model_accounts->can_log_in()){
            
            $this->load->model("model_accounts");
            
            // fetch user_id from logged in email 
            
            $user_data = $this->model_accounts->get_user_data_email($this->input->post('email'));
            
            foreach($user_data as $row){
            
                $email = $row->email;
                $user_id = $row->user_id;
                $username = $row->name;
                $user_type = $row->type;
                $profile_pix = $row->wallpaper;
                $fullname = $row->fullname;
            
            }
            
            //echo base_url();
            //create folders for user in storage subdomain
             if(!file_exists("uploads/".$user_id)) mkdir("uploads/".$user_id); 
             if(!file_exists("uploads/".$user_id."/wallpapers")) mkdir("uploads/".$user_id."/wallpapers");
             if(!file_exists("uploads/".$user_id."/collections")) mkdir("uploads/".$user_id."/collections");
             if(!file_exists("uploads/".$user_id."/podcasts")) mkdir("uploads/".$user_id."/podcasts");
             if(!file_exists("uploads/".$user_id."/voices")) mkdir("uploads/".$user_id."/voices");
                
            
            //give user session key
            
            $uniqid = md5(uniqid());
            $platform = $this->input->post('platform');
            if($this->model_accounts->create_session_ort($user_id,$uniqid,$platform)){
            
                $response['log'] = "Logged In";
                $response['success'] = 1;
                $response['user_id'] = $user_id;
                $response['session_id'] = $uniqid;
                $response['username'] = $username;
                $response['fullname'] = $fullname;
                $response['type'] = $user_type;
                $response['profile_pix'] = $profile_pix;
                $response['email'] = $email;
             
            }else{

                $response['log'] = "Could not create session";
                $response['success'] = 0;
            
            }
        
        }else{
            $response['log'] = "Incorrect Login Parameters";
            $response['success'] = 0;
            
        }
        echo json_encode($response);
            
    }
    
    public function validate_login(){
    
        
        // load user model
        $this->load->model('model_accounts');
        
        // check if user email exists and/or matches a password on the database using precreated login method
        if($this->model_accounts->can_log_in()){
        
            return true;// allow user to login
            
        } else {
            
            return false;// do not allow user to login
        
        }
    
    }
    
    public function sign_up(){
    
        $this->webHeaders();
        
    // load validation library
        $this->load->library('form_validation');
    
        // setting validation rules
        $this->form_validation->set_rules('username','Username','required|xss_clean|trim|is_unique[profiles.name]');
        $this->form_validation->set_rules('fullname','Fullname','xss_clean|trim');
        $this->form_validation->set_rules('email','Email','required|valid_email|xss_clean|trim|is_unique[profiles.email]');
        $this->form_validation->set_rules("password","Password","required|xss_clean|trim");
        $this->form_validation->set_rules('type','Account Type','xss_clean|trim');
        
        //response array
        $response = array();
        
        if($this->form_validation->run()){
            
            //generate a random key
            $key = md5(uniqid());
            
            //create email
            $this->load->library('email',array('mailtype'=>'html'));
            $this->load->model('model_accounts');
            
            $this->email->from('accounts@voiga.com','Administrator');
            $this->email->to($this->input->post('email'));
            $this->email->subject("Confirm your OnlineRadioTime account.");
            
            $message = "<p>Thank you for signing up!</p>";
            $message .= "<p>Welcome! to the online audio blogging service. Share the power of your voice with the world!</p>";
            
            //change url here please
            $message .= "<p><a href='http://".base_url()."/confirm_user/$key'>click here</a> to confirm your account; login afterwards</p>";
            
            //send an email to the user
            $this->email->message($message);
            
            
            // add user to temp_users db
            if($this->model_accounts->add_temp_user($key)){    
                
                if($this->email->send()){
                
                    $response['log'] = "Check your Email for Account Confirmation!";
                    $response['success'] = 1;
                }else{
                
                    $response['log'] = "Confirmation Email not sent!";
                    $response['success'] = 0;
                    
                }

            }else{
            
                $response['log'] = "Problem creating user!";
                $response['success'] = 0;
            }
            
                
        }else{
            //ensure that client side validation is done
            $response['log'] = "Username or Email already in use";
            $response['success'] = 0;
        }
        
        echo json_encode($response);
    }
    
    
    public function validate_email(){
    
        // load user model
        $this->load->model('model_accounts');
        
        // check if user email exists and/or matches a password on the database using precreated login method
        if($this->model_accounts->valid_email()){
        
            return true;// allow user to login
            
        } else {
            
            // notify user of incorrect login parameters
            $this->form_validation->set_message('validate_login','Incorrect Login Parameters');
            
            return false;// do not allow user to login
        
        }
    
    }
    
    // confirm user account page
    public function confirm_user($key){
        
        // check if user sign up key is set else redirect to restricted page
        if(!isset($key)) redirect("home/restricted");
        
        // load model accounts containing confirmation methods
        $this->load->model('model_accounts');
        
        // check for validity of key with model method
        if($this->model_accounts->is_key_valid($key)){
        
          // add user to permanent database and redirect to profile page
          if($this->model_accounts->add_user($key)){
              
               
            echo "confirmation successful";           
          
          }else echo " confirmation failed";// notify user of failed confirmation
            
        }else echo "invalid key";// notify user of invalid key
    
    }    
    
    
    public function forgot_password(){
     
        $this->webHeaders();
        
        $this->load->library("form_validation");
        $this->form_validation->set_rules("email","Email","required|valid_email|xss_clean|trim|callback_validate_email");
        
        $response = array();
        if($this->form_validation->run()){
        
            $this->load->model("model_accounts");
                       
            
            $mykey = md5(uniqid());
            $email = $this->input->post('email');
            
            //create email
            $this->load->library('email',array('mailtype'=>'html'));
            $this->load->model('model_accounts');
            
            $this->email->from('accounts@voiga.com','Administrator');
            $this->email->to($this->input->post('email'));
            $this->email->subject("Change your OnlineRadioTime account password.");
            
            $message = "<p>Change your OnlineRadioTime password</p>";
            $message .= "<p>Welcome! to the online audio blogging service. Share the power of your voice with the world!</p>";
            
            $message .= "<p>Your password change key is $mykey. You can <a href='radii.host22.com/change_password/$mykey'>click here</a> to change your passsword; login afterwards</p>";
            
            
            if($this->model_accounts->forgot_action($mykey)){
                
                $this->email->message($message);
                if($this->email->message($message)){ 
                    $response['log'] = "Check $email for our mail";
                    $response['success'] = 1;
                }else{
                    $response['log'] = "Couldn't send email. Please try again!";
                    $response['success'] = 0;
                }
            
            }else{
                $response['log'] = "Connection Error. please try again!";
                $response['success'] = 0;
            }
            

        
        }else{
        
            $response['log'] = "Email Address invalid or non-existent database";
            $response['success'] = 0;
            
        }
        
        echo json_encode($response);
        
    }
    
    public function logout($mykey){

        $this->webHeaders();
        $response = array();
        if(!isset($mykey) || $mykey==""){//primary valiadation
            $response['log'] = "bad url";
            $response['success'] = 0;
        }else{
            $this->load->library('form_validation');
            
            $this->form_validation->set_rules("user_id","User_id","required|xss_clean|trim");
            
            if($this->form_validation->run()){
                
                $this->load->model('model_accounts');
                
                if($this->model_accounts->url_key_is_valid($mykey,$this->input->post('user_id'))){//main authentication 
                    
                    if($this->model_accounts->logout($mykey,$this->input->post('user_id'))){
                        $response['log'] = "Logged Out";
                        $response['success'] = 1;
                    }else{
                        $response['log'] = "Could not log out";
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
