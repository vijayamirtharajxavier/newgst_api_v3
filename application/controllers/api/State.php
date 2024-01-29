<?php
   
   require APPPATH . 'libraries/REST_Controller.php';
   use Restserver\Libraries\REST_Controller;
     
class State extends REST_Controller {
    
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
            $data = $this->db->get_where("gststate_tbl", ['id' => $id])->row_array();
        }else{
            $data = $this->db->get("gststate_tbl")->result();
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
        $this->db->insert('gststate_tbl',$input);
     
        $msg= array("success"=>true,"messages"=>"State created successfully");
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
        $this->db->update('gststate_tbl', $input, array('id'=>$id));
     
     
        $msg= array("success"=>true,"messages"=>"State updated successfully");
        $this->response($msg, REST_Controller::HTTP_OK);
    }
     
    /**
     * Get All Data from this method.
     *
     * @return Response
    */
    public function index_delete($id)
    {
        $this->db->delete('gststate_tbl', array('id'=>$id));
       
        $this->response(['deleted successfully.'], REST_Controller::HTTP_OK);
    }
    	
}