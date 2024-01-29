<?php
   
   require APPPATH . 'libraries/REST_Controller.php';
   use Restserver\Libraries\REST_Controller;
     
class Salesperson extends REST_Controller {
    
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
            $data = $this->db->get_where("salesperson_tbl", ['id' => $id])->row_array();
        }else{
            $data = $this->db->get("salesperson_tbl")->result();
        }
     
        $this->response($data, REST_Controller::HTTP_OK);
	}
      

//Keyword search
        public function keyword_get($query =null,$compid=null)
    {
        if(!empty($query)){
            //$data = $this->db->get_where("salesperson_tbl", ['prod_name' => $query])->row_array();
        $this->db->where("company_id",$compid);
        $this->db->like("prod_name",$query);
        $data=$this->db->get('salesperson_tbl')->result();
        }else{
            $data = $this->db->get("salesperson_tbl")->result();
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
        $this->db->insert('salesperson_tbl',$input);
     $msg= array("success"=>true,"messages"=>"Product created successfully");
        //$this->response(['Product updated successfully.'], REST_Controller::HTTP_OK);
        $this->response($msg, REST_Controller::HTTP_OK);
        //$this->response(['Product created successfully.'], REST_Controller::HTTP_OK);
    } 
     
    /**
     * Get All Data from this method.
     *
     * @return Response
    */
    public function index_put($id)
    {
        $input = $this->put();
        $this->db->update('salesperson_tbl', $input, array('id'=>$id));
        $msg= array("success"=>true,"messages"=>"Product updated successfully");
        $this->response($msg, REST_Controller::HTTP_OK);
    }
     
    /**
     * Get All Data from this method.
     *
     * @return Response
    */
    public function index_delete($id)
    {
        $this->db->delete('salesperson_tbl', array('id'=>$id));
       
        $this->response(['Product deleted successfully.'], REST_Controller::HTTP_OK);
    }
    	
}