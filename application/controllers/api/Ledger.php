<?php
   
   require APPPATH . 'libraries/REST_Controller.php';
   use Restserver\Libraries\REST_Controller;
     
class Ledger extends REST_Controller {
    
	  /**
     * Get All Data from this method.
     *
     * @return Response
    */
    public function __construct() {
       parent::__construct();
       $this->load->database();
       
    }
 


//Keyword search
        public function keyword_get($query =null,$compid=null)
    {
        if(!empty($query)){
            //$data = $this->db->get_where("products_tbl", ['prod_name' => $query])->row_array();
        $this->db->where("company_id",$compid);
        $this->db->like("account_name",$query);
        $this->db->like("delflag",0);
        $data=$this->db->get('ledgermaster_tbl')->result();
        }else{
            $this->db->where("company_id",$compid);
        
        $this->db->like("delflag",0);
            $data = $this->db->get("ledgermaster_tbl")->result();
        }
     
        $this->response($data, REST_Controller::HTTP_OK);
    }



    /**
     * Get All Data from this method.
     *
     * @return Response
    */
	public function index_get($id = 0)
	{
        if(!empty($id)){
            $data = $this->db->get_where("ledgermaster_tbl", ['id' => $id])->row_array();
        }else{
            
            $this->db->like("delflag",0);
            $data = $this->db->get("ledgermaster_tbl")->result();
        }
     
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
        $this->db->insert('ledgermaster_tbl',$input);
     
        $msg= array("success"=>true,"messages"=>"Ledger Account created successfully");
        $this->response($msg, REST_Controller::HTTP_OK);
    } 
     
    /**
     * Get All Data from this method.
     *
     * @return Response
    */
    public function index_put($id)
    {
        $input = $this->put();
        $this->db->update('ledgermaster_tbl', $input, array('id'=>$id));
     
        $msg= array("success"=>true,"messages"=>"Ledger updated successfully");
        $this->response($msg, REST_Controller::HTTP_OK);

    }
     
    /**
     * Get All Data from this method.
     *
     * @return Response
    */
    public function index_delete($id)
    {
        $this->db->delete('ledgermaster_tbl', array('id'=>$id));
       
        $this->response(['deleted successfully.'], REST_Controller::HTTP_OK);
    }
    	
}