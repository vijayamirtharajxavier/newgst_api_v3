<?php
   
   require APPPATH . 'libraries/REST_Controller.php';
   use Restserver\Libraries\REST_Controller;
     
class Product extends REST_Controller {
    
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
            $data = $this->db->get_where("products_tbl", ['id' => $id])->row_array();
        }else{
            $data = $this->db->get("products_tbl")->result();
        }
     
        $this->response($data, REST_Controller::HTTP_OK);
	}
      

//Keyword search
        public function keyword_get($query =null,$compid=null)
    {
            extract($_POST);
   $data_arr=get_defined_vars();
   $cid=$data_arr['compid'];
   $qry=$data_arr['query'];
   //var_dump($cid . $qry);
        if(!empty($qry)){
            //$data = $this->db->get_where("products_tbl", ['prod_name' => $query])->row_array();
        $this->db->where("company_id",$cid);
        $this->db->like("prod_name",$qry);
        $data=$this->db->get('products_tbl')->result();
        }else{
            $data = $this->db->get("products_tbl")->result();
        }
     
        $this->response($data, REST_Controller::HTTP_OK);
    }


        public function byname()
    {
            extract($_POST);
   $data_arr=get_defined_vars();
   //var_dump($data_arr);
   //$cid=$data_arr['compid'];
   $qry=$data_arr['itemkeyword'];
   //var_dump($cid . $qry);
        if(!empty($qry)){
            //$data = $this->db->get_where("products_tbl", ['prod_name' => $query])->row_array();
     //   $this->db->where("company_id",$cid);
        $this->db->where("prod_name",$qry);
        $data=$this->db->get('products_tbl')->result();
        }else{
            $data = $this->db->get("products_tbl")->result();
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
        $this->db->insert('products_tbl',$input);
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
        $this->db->update('products_tbl', $input, array('id'=>$id));
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
        $this->db->delete('products_tbl', array('id'=>$id));
       
        $this->response(['Product deleted successfully.'], REST_Controller::HTTP_OK);
    }
    	
}