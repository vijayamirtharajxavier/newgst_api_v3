<?php
   
   require APPPATH . 'libraries/REST_Controller.php';
   use Restserver\Libraries\REST_Controller;
     
class Group extends REST_Controller {
    
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
            $data = $this->db->get_where("group_tbl", ['id' => $id])->row_array();
        }else{
            $data = $this->db->get("group_tbl")->result();
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
        $this->db->insert('group_tbl',$input);
     
        $msg= array("success"=>true,"messages"=>"Group created successfully");
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
        $this->db->update('group_tbl', $input, array('id'=>$id));
     
     
        $msg= array("success"=>true,"messages"=>"Group updated successfully");
        $this->response($msg, REST_Controller::HTTP_OK);
    }
     
    /**
     * Get All Data from this method.
     *
     * @return Response
    */
    public function index_delete($id)
    {
        $this->db->delete('group_tbl', array('id'=>$id));
       
        $this->response(['deleted successfully.'], REST_Controller::HTTP_OK);
    }
    	
}