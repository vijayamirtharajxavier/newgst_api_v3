<?php
   
   require APPPATH . 'libraries/REST_Controller.php';
   use Restserver\Libraries\REST_Controller;
     
class Transaction extends REST_Controller {
    
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
            $data = $this->db->get_where("transaction_tbl", ['id' => $id])->result();
        }else{
            $data = $this->db->get_where("transaction_tbl",['finyear'=>$finyear])->result();
        }
     
        $this->response($data, REST_Controller::HTTP_OK);
	}


public function glreport()
{
//$data = array("s_date"=>$s_date,"e_date"=>$e_date,"finyear"=>$finyear,"compId"=>$compId,"actid"=>$actid,"fdate"=>$fdate,"tdate"=>$tdate);
$data=array();
   extract($_POST);
   $data_arr=get_defined_vars();
//   var_dump($data_arr);
   $cid=$data_arr['compId'];
   $finyear=$data_arr['finyear'];
   $actid=$data_arr['actid'];
   $start_date=$data_arr['fdate'];
   $end_date=$data_arr['tdate'];
   $sdate=$data_arr['s_date'];


$gldata_op = $this->data_model->getglop($cid,$finyear,$sdate,$fdate,$actid);
if($gldata_op)
{
 foreach ($gldata_op as $key => $opglvalue) {
        # code...
        
    $opbal=$opglvalue['opbal'];

    }   
}


$gldata = $this->data_model->getgltransaction($cid,$finyear,$sdate,$fdate,$actid);
if($gldata)
{
 foreach ($gldata as $key => $glvalue) {
        # code...
        
$data=array("id" =>  $id,
            "trans_id" => $glvalue['trans_id'],
            "trans_date" => $glvalue['trans_date'],
            "db_account" => $glvalue['db_account'],
            "cr_account" => $glvalue['cr_account'],
            "trans_type" =>$glvalue['trans_type'],
            "trans_amount" => $glvalue['trans_amount'],
            "net_amount" => $glvalue['net_amount'],
            "trans_reference" => $glvalue['trans_reference'],
            "trans_narration" => $glvalue['trans_narration'],
            "opbal"=>$opbal
);



    }   
}

echo json_encode($data);



    
}

    public function pursal_byid_get($id=null,$query=null)
    {
        $maindata=array();
        $itm=array();
            $tdata = $this->data_model->getSalesPurchaseDatabyId($id,$query);
            if($tdata)
            {
                foreach ($tdata as $key => $tvalue) {
                   // $id=$tvalue['id'];
                  //  $query="PURC";

  /*                 $itmdata = $this->data_model->getSalesPurchaseItems($id,$query);
                 //  var_dump($itmdata);
                   if($itmdata)
                   {
                    foreach ($itmdata as $key => $itvalue) {
                        
                        $itm['items']=$itvalue;

                    }
                   }

*/
                   $data[]=$tvalue;

                }
            }
    //$data= array_merge($maindata,$itm);

if($data)
{
        $this->response($data, REST_Controller::HTTP_OK);

}
    }



    public function trans_byid_get($id=null)
    {
        $maindata=array();
        $itm=array();
            $tdata = $this->data_model->getTransactionDatabyId($id);
            if($tdata)
            {
                foreach ($tdata as $key => $tvalue) {
                   $data[]=$tvalue;

                }
            }

if($data)
{
        $this->response($data, REST_Controller::HTTP_OK);

}
    }


public function pursal_get($transtype = null, $finyear=null)
{
        


$sdata = $this->data_model->getSalesPurchaseData($transtype,$finyear);


if($sdata)
{
foreach ($sdata as $key => $svalue) {
    # code...
$trid = $svalue['id'];

$noi=0;
$txb_tot=0;
$net_tot=0;

$sumdata = $this->data_model->getSumSP($trid);
if($sumdata)
{
    foreach ($sumdata as $key => $smvalue) {

        $noi = $smvalue['noi'];
        $txb_tot=$smvalue['txb_amt'];
        $net_tot=$smvalue['net_amt'];

        # code...
    }



}

  
$data[] = array("id"=>$svalue["id"], "noi"=>$noi,"txb_tot"=>$txb_tot,"net_tot"=>$net_tot,"trans_id"=>$svalue['trans_id'],"trans_date"=>$svalue['trans_date'],"order_date"=>$svalue['order_date'],"order_no"=>$svalue['order_no'],"dc_no"=>$svalue['dc_no'],"dc_date"=>$svalue['dc_date'],"trans_type"=>$svalue['trans_type'],"db_account"=>$svalue['db_account'],"cr_account"=>$svalue['cr_account'],"statecode"=>$svalue['statecode'],"gstin"=>$svalue['gstin'],"inv_type"=>$svalue['inv_type'],"rcm"=>$svalue['rcm'],"trans_reference"=>$svalue['trans_reference'],"trans_narration"=>$svalue['trans_narration'],"salebyperson"=>$svalue['salebyperson'],"finyear"=>$svalue['finyear'],"custname"=>$svalue['custname']);

}

//        $this->response($data, REST_Controller::HTTP_OK);

}

echo json_encode($data);
     
}


public function pursalreg_get($transtype = null, $finyear=null,$cid=null,$fdate=null,$tdate=null)
{
        


$sdata = $this->data_model->getSalesPurchaseReg($transtype,$finyear,$cid,$fdate,$tdate);
//var_dump($sdata);
//var_dump($sdata);
if($sdata)
{
foreach ($sdata as $key => $svalue) {
    # code...
$trid = $svalue['id'];

$noi=0;
$txb_tot=0;
$net_tot=0;

$sumdata = $this->data_model->getSumSP($trid);
if($sumdata)
{
    foreach ($sumdata as $key => $smvalue) {

        $noi = $smvalue['noi'];
        $txb_tot=$smvalue['txb_amt'];
        $net_tot=$smvalue['net_amt'];

        # code...
    }



}

  
$data[] = array("id"=>$svalue["id"], "noi"=>$noi,"txb_tot"=>$txb_tot,"net_tot"=>$net_tot,"trans_id"=>$svalue['trans_id'],"trans_date"=>$svalue['trans_date'],"order_date"=>$svalue['order_date'],"order_no"=>$svalue['order_no'],"dc_no"=>$svalue['dc_no'],"dc_date"=>$svalue['dc_date'],"trans_type"=>$svalue['trans_type'],"db_account"=>$svalue['db_account'],"cr_account"=>$svalue['cr_account'],"statecode"=>$svalue['statecode'],"gstin"=>$svalue['gstin'],"inv_type"=>$svalue['inv_type'],"rcm"=>$svalue['rcm'],"trans_reference"=>$svalue['trans_reference'],"trans_narration"=>$svalue['trans_narration'],"salebyperson"=>$svalue['salebyperson'],"finyear"=>$svalue['finyear'],"custname"=>$svalue['custname']);

}

//        $this->response($data, REST_Controller::HTTP_OK);

}

echo json_encode($data);
     
}



    public function gstr1_get($id = 0)
    {
        if(!empty($id)){
            $data = $this->db->get_where("transaction_tbl", ['id' => $id])->row_array();
        }else{
            $data = $this->db->get_where("transaction_tbl",['trans_type'=>'RCPT'])->result();
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
        $this->db->insert('transaction_tbl',$input);
     
     $msg=array("last_ins_id"=>$this->db->insert_id());
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
        $this->db->update('transaction_tbl', $input, array('id'=>$id));
        $msg= array("status"=>"1");     
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


public function post_auditlog_post()
{
//            $ins_log = $this->db->insert('audit_log_tbl',$logdata);
        $input = $this->input->post();
     $status=$this->db->insert('audit_log_tbl',$input);
      $msg = array("status" => $status);
     //$msg=array("last_ins_id"=>$this->db->insert_id());
        $this->response($msg, REST_Controller::HTTP_OK);


}


public function delpursal_byid_put($id=null,$finyear=null,$cid=null)
{
    $input = $this->put();

         $status =   $this->db->update('transaction_tbl', $input, array('id'=>$id,'finyear'=>$finyear,'company_id'=>$cid));
  //      if($status=true)
    //    {
//            $log_txt="Deleted record Id:" . $id; //. "  on Purchase Tbl with the reason " . $dreason . " deleted by user : " . $login;
         //   $logdata=array("log_description"=>$log_txt,"log_date"=>date("Y-m-d"));
       //     $ins_log = $this->db->insert('audit_log_tbl',$logdata);
      //  }
//var_dump($status);
        $msg= array("success"=>$status);     
        $this->response($msg, REST_Controller::HTTP_OK);

}


    	
}