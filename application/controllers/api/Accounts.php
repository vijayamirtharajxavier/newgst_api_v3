<?php
   
   require APPPATH . 'libraries/REST_Controller.php';
   use Restserver\Libraries\REST_Controller;
     
class Accounts extends REST_Controller {
    
	  /**
     * Get All Data from this method.
     *
     * @return Response
    */
    public function __construct() {
       parent::__construct();
       $this->load->database();
       
    }
       
    /**
     * Get All Data from this method.
     *
     * @return Response
    */
	public function index_get($id = 0)
	{
        if(!empty($id)){
            $data = $this->db->get_where("transaction_tbl", ['id' => $id])->result();
        }else{
            $data = $this->db->get("transaction_tbl")->result();
        }
     
        $this->response($data, REST_Controller::HTTP_OK);
	}

    public function receipts_get()
    {
        $this->db->order_by('id','desc');
            $data = $this->db->get_where("transaction_tbl", ['trans_type' => "RCPT"])->result();
     
        $this->response($data, REST_Controller::HTTP_OK);
    }

   public function payments_get()
    {
        $this->db->order_by('id','desc');
            $data = $this->db->get_where("transaction_tbl", ['trans_type' => "PYMT"])->result();
        $this->response($data, REST_Controller::HTTP_OK);
    }
   public function journals_get()
    {
        $this->db->order_by('id','desc');
            $data = $this->db->get_where("transaction_tbl", ['trans_type' => "JRNL"])->result();
        $this->response($data, REST_Controller::HTTP_OK);
    }
 
    public function contra_get()
    {
        $this->db->order_by('id','desc');
            $data = $this->db->get_where("transaction_tbl", ['trans_type' => "CNTR"])->result();
        $this->response($data, REST_Controller::HTTP_OK);
    }
      
    /**
     * Get All Data from this method.
     *
     * @return Response
    */
    public function index_post()
    {
        $input = $this->input->post();
        $this->db->insert('transaction_tbl',$input);
             $msg= array("success"=>true,"messages"=>"created successfully");
        $this->response($msg, REST_Controller::HTTP_OK);
     
//        $this->response(['created successfully.'], REST_Controller::HTTP_OK);
    } 
     
    /**
     * Get All Data from this method.
     *
     * @return Response
    */
    public function index_put($id)
    {
        $input = $this->put();
      //  var_dump($input);
        $this->db->update('transaction_tbl', $input, array('id'=>$id));
             $msg= array("success"=>true,"messages"=>"updated successfully");
        $this->response($msg, REST_Controller::HTTP_OK);
    }
     
    /**
     * Get All Data from this method.
     *
     * @return Response
    */
    public function index_delete($id)
    {
        $this->db->delete('transaction_tbl', array('id'=>$id));
                    $msg= array("success"=>true,"messages"=>"deleted successfully");

        $this->response($msg, REST_Controller::HTTP_OK);
    }
    	
}