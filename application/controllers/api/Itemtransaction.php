<?php
   
   require APPPATH . 'libraries/REST_Controller.php';
   use Restserver\Libraries\REST_Controller;
     
class Itemtransaction extends REST_Controller {
    
	  /**
     * Get All Data from this method.
     *
     * @return Response
    */
    public function __construct() {
       parent::__construct();
       $this->load->database();
        
       $this->load->model('data_model');
       

       
    }
       
    /**
     * Get All Data from this method.
     *
     * @return Response
    */
	public function index_get($id = 0)
	{
        if(!empty($id)){
            $data = $this->db->get_where("itemtransaction_tbl", ['trans_link_id' => $id])->result();
        }else{
            $data = $this->db->get("itemtransaction_tbl")->result();
        }
     
        $this->response($data, REST_Controller::HTTP_OK);
	}


    public function pursal_byid_get($id=null,$query = null)
    {
        

            $data = $this->data_model->getSalesPurchaseItems($id,$query);
if($data)
{
        $this->response($data, REST_Controller::HTTP_OK);

}
     
    }


    public function gstr1_get($id = 0)
    {
        if(!empty($id)){
            $data = $this->db->get_where("itemtransaction_tbl", ['trans_link_id' => $id])->row_array();
        }else{
            $data = $this->db->get_where("itemtransaction_tbl",['trans_type'=>'RCPT'])->result();
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
        $this->db->insert('itemtransaction_tbl',$input);
        $msg= array("status"=>"1", "success"=>true,"messages"=>"Invoice updated successfully");     
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
        $this->db->update('itemtransaction_tbl', $input, array('trans_link_id'=>$id));
         $msg= array("status"=>"1", "success"=>true,"messages"=>"Items delflag set successfully");    
        $this->response($msg, REST_Controller::HTTP_OK);
    }
     
    /**
     * Get All Data from this method.
     *
     * @return Response
    */
    public function index_delete($id)
    {
        $this->db->delete('itemtransaction_tbl', array('trans_link_id'=>$id));
       
        $this->response(['deleted successfully.'], REST_Controller::HTTP_OK);
    }
    	
}