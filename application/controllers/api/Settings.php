<?php
   
   require APPPATH . 'libraries/REST_Controller.php';
   use Restserver\Libraries\REST_Controller;
     
class Settings extends REST_Controller {
    
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
	public function index_get($finyear = null,$cid=null)
	{
        if(!empty($finyear)){
    $data = $this->db->get_where("settings_tbl", ['finyear' => $finyear,'company_id'=>$cid])->row_array();
        }else{
    $data = $this->db->get_where("settings_tbl", ['finyear' => $finyear,'company_id'=>$cid])->row_array();

   //       $data = $this->db->get_where("settings_tbl", ['finyear'=> $finyear,'company_id'=>$cid])->get("settings_tbl")->result();
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
        $this->db->insert('settings_tbl',$input);
     
        $this->response(['created successfully.'], REST_Controller::HTTP_OK);
    } 
     
    /**
     * Get All Data from this method.
     *
     * @return Response
    */
    public function index_put($id)
    {
        $input = $this->put();
        $this->db->update('settings_tbl', $input, array('id'=>$id));
     
        $this->response(['updated successfully.'], REST_Controller::HTTP_OK);
    }
     
    /**
     * Get All Data from this method.
     *
     * @return Response
    */
    public function index_delete($id)
    {
        $this->db->delete('settings_tbl', array('id'=>$id));
       
        $this->response(['deleted successfully.'], REST_Controller::HTTP_OK);
    }
    	
}