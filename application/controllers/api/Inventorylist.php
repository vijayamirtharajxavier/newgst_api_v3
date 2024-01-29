<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Inventorylist extends CI_Controller {

	public function __construct(){
        parent::__construct();
     //   check_login_user();
       // $this->load->library('curl');
$this->load->helper('form');
       $this->load->database();
      $this->load->model('data_model');

}


    
public function opbal()
    {

        $data=array();
   extract($_POST);
   $data_arr=get_defined_vars();
 //var_dump($data_arr);
   $compid=$data_arr['compId'];
   //$finyear=$data_arr['finyear'];
   $finyear=$data_arr['finyear'];

/*        if(!empty($id)){
            $data = $this->db->get_where("ledgermaster_tbl", ['id' => $id])->row_array();
        }else{*/
$ldgData=$this->data_model->getall_ledgers($compid);
$opbal=0;
if($ldgData)
{
  foreach ($ldgData as $key => $lvalue) {
    # code...

$ldg_id=$lvalue['id'];
$account_name=$lvalue['account_name'];


$opbalData=$this->data_model->getopbalData($compid,$finyear,$ldg_id);
//var_dump($opbalData);
if($opbalData)
{
 $opbal = $opbalData[0]['open_bal'];

}

else {
  $opbal=0;
}
$o_bal ='<input type="text" id="open_bal" value="'.$opbal.'" name="open_bal[]" style="text-align:right">';
$data['data'][]=array("id"=>$ldg_id,"account_name"=>$account_name,"open_bal"=>$opbal);

  } //Ledgermaster loop

echo json_encode($data);

}
else
{
  echo "data[]";
}


        
     

    }


    


    public function accountstrans($trans_type=null)
    {
   $input = $this->input->post();
 //   var_dump($input);
if($trans_type=="RCPT")
{
$msg="Receipt Entry Inserted successfully..!";
}

if($trans_type=="PYMT")
{
$msg="Payment Entry Inserted successfully..!";
}

if($trans_type=="CNTR")
{
$msg="Contra Entry Inserted successfully..!";
}

if($trans_type=="JRNL")
{
$msg="Journal Entry Inserted successfully..!";
}

$this->db->insert('transaction_tbl',$input);
$msg= array("status"=>"1", "success"=>true,"messages"=>$msg);     
echo json_encode($msg);
//$this->response($msg, REST_Controller::HTTP_OK);


 //   extract($_POST);
 //   $data_arr=get_defined_vars();
//var_dump($data_arr);

//     $this->db->insert('openingbalance_tbl',$input);
  //   $msg= array("status"=>"1", "success"=>true,"messages"=>"Invoice updated successfully");     
  //  echo json_encode($msg);
    // $this->response($msg, REST_Controller::HTTP_OK);
    
    
     //       $this->db->insert('openingbalance_tbl ',$input);
      //      $msg= array("status"=>"1", "success"=>true,"messages"=>"Invoice updated successfully");     
      //      $this->response($msg, REST_Controller::HTTP_OK);
    }
    




public function insertOpBal()
{
        $input = $this->input->post();
// var_dump($input);
 $this->db->insert('openingbalance_tbl',$input);
 $msg= array("status"=>"1", "success"=>true,"messages"=>"Invoice updated successfully");     
echo json_encode($msg);
// $this->response($msg, REST_Controller::HTTP_OK);


 //       $this->db->insert('openingbalance_tbl ',$input);
  //      $msg= array("status"=>"1", "success"=>true,"messages"=>"Invoice updated successfully");     
  //      $this->response($msg, REST_Controller::HTTP_OK);
}



public function updateOpBalDel()
{

   $data=array();
   extract($_POST);
   $data_arr=get_defined_vars();
 //  var_dump($data_arr);
   $compid=$data_arr['compId'];
   //$finyear=$data_arr['finyear'];
   $finyear=$data_arr['finyear'];

$delqry= $this->data_model->setdelflagopbal($compid,$finyear);


}


public function getsettings()
{
  $data=array();
  extract($_POST);
  $data_arr=get_defined_vars();
  $cid=$data_arr['compId'];
  $finyear=$data_arr['finyear'];
//var_dump($data_arr);
  $settdata= $this->data_model->getSettings($cid,$finyear);
  if($settdata)
  {
    foreach($settdata as $settings)
    {
      $data[]=$settings;		

    }
  }
  echo json_encode($data);
}




public function finyearsettings()
{
  $data=array();
  extract($_POST);
  $data_arr=get_defined_vars();
  $cid=$data_arr['compId'];
  $fyeardata= $this->data_model->getFYData($cid);
  if($fyeardata)
  {
    foreach($fyeardata as $fy)
    {
      $data[]=$fy;		

    }
    echo json_encode($data);
  }
}



public function salespersonbycid()
{
  $data=array();
  extract($_POST);
  $data_arr=get_defined_vars();
  $cid=$data_arr['compId'];
  $stfdata= $this->data_model->getStaffbycid($cid);
  if($stfdata)
  {
    foreach($stfdata as $stf)
    {
      $data[]=$stf;		

    }
  }
  echo json_encode($data);

}



public function accountstransaction()
{
  $data=array();
  extract($_POST);
  $data_arr=get_defined_vars();
 // var_dump($data_arr);
  $cid=$data_arr['compId'];
  $finyear=$data_arr['finyear'];
  $trans_type=$data_arr['trans_type'];

//var_dump($data_arr);
//$compid=$this->input->get('cid');
//$finyear=$data_arr['finyear'];
//$finyear=$this->input->get('finyear');
//$trans_type=$this->input->get('trans_type');
//var_dump($finyear);
$transdata = $this->data_model->getTransData($cid,$finyear,$trans_type);
if($transdata)
{
  foreach($transdata as $td)
  {
    $data[]=$td;		

  }

}
echo json_encode($data);

}


public function accountstransactionbyid()
{
  $data=array();
  extract($_POST);
  $data_arr=get_defined_vars();
 var_dump($data_arr);
  $cid=$data_arr['compId'];
  $finyear=$data_arr['finyear'];
  $trans_type=$data_arr['trans_type'];
  $id=$data_arr['id'];

//var_dump($data_arr);
//$compid=$this->input->get('cid');
//$finyear=$data_arr['finyear'];
//$finyear=$this->input->get('finyear');
//$trans_type=$this->input->get('trans_type');
//var_dump($finyear);
$transdata = $this->data_model->getTransDatabyid($cid,$finyear,$trans_type,$id);
if($transdata)
{
  foreach($transdata as $td)
  {
    $data[]=$td;		

  }

}
echo json_encode($data);

}


public function statelist()
{
  $ldgdata= $this->data_model->getGstStatebycid();
  if($ldgdata)
  {
    foreach($ldgdata as $ldg)
    {
      $data[]=$ldg;		

    }
  }
  echo json_encode($data);


}




public function ledgerbyidbycid()
{
  $data=array();
  extract($_POST);
  $data_arr=get_defined_vars();
  $cid=$data_arr['compId'];
  $id=$data_arr['id'];
  $ldgdata= $this->data_model->getLedgerbyidbycid($cid,$id);
  if($ldgdata)
  {
    foreach($ldgdata as $ldg)
    {
      $data[]=$ldg;		

    }
  }
  echo json_encode($data);

}




public function ledgerbycid()
{
  $data=array();
  extract($_POST);
  $data_arr=get_defined_vars();
  $cid=$data_arr['compId'];
  $ldgdata= $this->data_model->getLedgerbycid($cid);
  if($ldgdata)
  {
    foreach($ldgdata as $ldg)
    {
      $data[]=$ldg;		

    }
  }
  echo json_encode($data);

}

public function prodcatbycid()
{
  $data=array();
  extract($_POST);
  $data_arr=get_defined_vars();
  $cid=$data_arr['compId'];
  $prodcatdata= $this->data_model->getProductcatbycid($cid);
  if($prodcatdata)
  {
    foreach($prodcatdata as $prodcat)
    {
      $data[]=$prodcat;		

    }
  }
  echo json_encode($data);


}


public function unitsbycid()
{
  $data=array();
  extract($_POST);
  $data_arr=get_defined_vars();
  $cid=$data_arr['compId'];
  $unitdata= $this->data_model->getUnitsbycid($cid);
  if($unitdata)
  {
    foreach($unitdata as $unit)
    {
      $data[]=$unit;		

    }
  }
  echo json_encode($data);

}



public function productsbycid()
{
  $data=array();
  extract($_POST);
  $data_arr=get_defined_vars();
  $cid=$data_arr['compId'];
  $proddata= $this->data_model->getProductsbycid($cid);
  if($proddata)
  {
    foreach($proddata as $prod)
    {
      $data[]=$prod;		

    }
  }
  echo json_encode($data);

}

public function getmonthwisedata()
{
   extract($_POST);
   $data_arr=get_defined_vars();
   $cid=$data_arr['compId'];
   $finyear=$data_arr['finyear'];
   $trans_type=$data_arr['trans_type'];

$monwiseData=$this->data_model->getmonwiseData($cid,$finyear,$trans_type);
if($monwiseData)
{
	foreach ($monwiseData as $key => $value) {
		# code...
    $data[]=$value;		
	}
}
echo json_encode($data);
}


public function getcurmonthtransaction()
{
   extract($_POST);
   $data_arr=get_defined_vars();
   //var_dump($data_arr);
   $cid=$data_arr['compId'];
   $finyear=$data_arr['finyear'];
   $trans_type=$data_arr['trans_type'];
   $start_date=$data_arr['start_date'];
   $end_date=$data_arr['end_date'];


$cmtrans= $this->data_model->getcmtData($cid,$finyear,$trans_type,$start_date,$end_date);

if($cmtrans)
{
	foreach ($cmtrans as $key => $value) {
		# code...
            $data[]=array("taxable_tot"=>$value['taxable_tot'],"netamount_tot"=>$value['netamount_tot'],"gst_tot"=>$value['gst_tot']);
 

	}

            echo json_encode($data);

}
else {
            $data[]=array("taxable_tot"=>"0.00","netamount_tot"=>"0.00","gst_tot"=>"0.00");
            echo json_encode($data);


}



}

    public function chartData()
    {
   extract($_POST);
   $data_arr=get_defined_vars();
   //var_dump($data_arr);
   $cid=$data_arr['compId'];
   $finyear=$data_arr['finyear'];
   $trans_type=$data_arr['trans_type'];

   $schartdata= $this->data_model->getChartData($finyear,$cid,"SALE");
   $pchartdata= $this->data_model->getChartData($finyear,$cid,"PURC");
   $pichartdata=$this->data_model->getpiChartData($finyear,$cid);
$lable['labels'] = array("Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec","Jan","Feb","Mar");
if($pichartdata)
{
 foreach ($pichartdata as $key => $pivalue) {
 		# code...
$pilabel['pilabels'][]=$pivalue['sales_person'];
$pidata['pidata'][]=$pivalue['tot_rev'];


 	}	
}  
else
{
$pilabel['pilabels'][]="";// $pivalue['sales_person'];
$pidata['pidata'][]="0";

}
  
if($schartdata)
{
foreach ($schartdata as  $value) {
	# code...
// var_dump($value);
                        
                       
 $sdata['sales']=array(intval($value['APR']),intval($value['MAY']),intval($value['JUN']),intval($value['JUL']),intval($value['AUG']),intval($value['SEP']),intval($value['OCT']),intval($value['NOV']),intval($value['DEC']),intval($value['JAN']),intval($value['FEB']),intval($value['MAR']));



}


}


  
if($pchartdata)
{
foreach ($pchartdata as  $value) {
	# code...
// var_dump($value);
//$lable['labels'] = array("Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec","Jan","Feb","Mar");                        
                       
 $pdata['purchase']=array(intval($value['APR']),intval($value['MAY']),intval($value['JUN']),intval($value['JUL']),intval($value['AUG']),intval($value['SEP']),intval($value['OCT']),intval($value['NOV']),intval($value['DEC']),intval($value['JAN']),intval($value['FEB']),intval($value['MAR']));



}


}

$mdata['data']= array_merge($sdata,$pdata,$pidata);

$data=array_merge($lable,$pilabel,$mdata);
//$data = array_merge($data,$pdata);
echo json_encode($data);







}


public function getCompanybyid()
{
  extract($_POST);
  $data_arr=get_defined_vars();
  $cid=$data_arr['compId'];
 // $finyear=$data_arr['finyear'];
$compdata = $this->data_model->getCompanyDetails($cid); 
$data= array();
if($compdata)
{
  foreach ($compdata as $key => $value) {
    $data[] = $value;
  }

 echo json_encode($data);

}
}


public function getmonthwisegstdata()
{
   extract($_POST);
   $data_arr=get_defined_vars();
   $cid=$data_arr['compId'];
   $finyear=$data_arr['finyear'];
   $trans_type=$data_arr['trans_type'];

$cmgsttrans= $this->data_model->getmonwisegstData($cid,$finyear,$trans_type);
//var_dump($cmgsttrans);
if($cmgsttrans)
{
	foreach ($cmgsttrans as $key => $value) {
		# code...
$data[]=$value;

	}

echo json_encode($data);

}


}



public function getmonthwiseitcdata()
{
   extract($_POST);
   $data_arr=get_defined_vars();
   $cid=$data_arr['compId'];
   $finyear=$data_arr['finyear'];
//   $trans_type=$data_arr['trans_type'];

   $mitcData=$this->data_model->getmonwiseITCData($finyear,$cid);
if($mitcData)
{
	foreach ($mitcData as $key => $value) {
		# code...
		$data[]=$value;
	}
echo json_encode($data);
}


}




        public function byname()
    {
            extract($_POST);
   $data_arr=get_defined_vars();
   //var_dump($data_arr);
   $cid=$data_arr['compId'];
   $qry=$data_arr['itemkeyword'];
   //var_dump($cid . $qry);
        if(!empty($qry)){
            //$data = $this->db->get_where("products_tbl", ['prod_name' => $query])->row_array();
        $this->db->where("company_id",$cid);
        $this->db->where("prod_name",$qry);
        $data=$this->db->get('products_tbl')->result();
        }else{
            $data = $this->db->get("products_tbl")->result();
        }
     echo json_encode($data);
      //  $this->response($data, REST_Controller::HTTP_OK);
    }

    public function ldggroup($cid =0)
    {
        if(!empty($cid)){
            $this->db->where("company_id",$cid);
            
            $data = $this->db->get("group_tbl")->result();
        }else{
            $data = $this->db->get("group_tbl",['company_id'=>$cid])->result();
        }
     
echo json_encode($data);

}


public function createPurchase()
    {
        $input = $this->input->post();
        $this->db->insert('transaction_tbl',$input);
        $msg=array("last_ins_id"=>$this->db->insert_id());
  echo json_encode($msg);

//        $this->response($msg, REST_Controller::HTTP_OK);
    } 
    
    public function createPurchaseItems()
    {
        $input = $this->input->post();
    $status=$this->db->insert('itemtransaction_tbl',$input);
        $msg= array("status"=>$status, "success"=>true,"messages"=>"Invoice updated successfully");     
 echo json_encode($msg);
        //       $this->response($msg, REST_Controller::HTTP_OK);
    } 
     



public function getunit($id = 0)
{
    $data=array();
    if(!empty($id)){
        $data = $this->db->get_where("units_tbl", ['id' => $id])->row_array();
    }else{
        $data = $this->db->get("units_tbl")->result();
    }
 echo json_encode($data);
//    $this->response($data, REST_Controller::HTTP_OK);
}




public function pursal_byid_get($id=null,$trans_type=null,$cid=nul,$finyear=null)
{
$data=array([]);    

        $data = $this->data_model->getSalesPurchaseItems($id,$trans_type,$cid,$finyear);
echo json_encode($data);
 
}





public function pursal_byid($id=null,$trans_type=null,$cid=null,$finyear=null)
{
    $maindata=array();
    $itm=array();
    $data=array();
        $tdata = $this->data_model->getSalesPurchaseDatabyId($id,$trans_type,$cid,$finyear);
        if($tdata)
        {
            foreach ($tdata as $key => $tvalue) {
               $data[]=$tvalue;

            }
        }
echo json_encode($data);
}



public function invoicetype()
{
    $data=array();
    $data = $this->db->get("invoicetype_tbl")->resutl();
    echo json_encode($data);
}

public function getinvoicetype($id=0)
	{
        $data=array();
        if(!empty($id)){
            $data = $this->db->get_where("invoicetype_tbl", ['id' => $id])->row_array();
        }else{
            $data = $this->db->get("invoicetype_tbl")->result();
        }
     echo json_encode($data);
     //   $this->response($data, REST_Controller::HTTP_OK);
	}
    
public function ldg_keyword($query =null,$compid=null)
{
    $data=array();
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
 echo json_encode($data);
  //  $this->response($data, REST_Controller::HTTP_OK);
}



        public function keyword()
    {
            extract($_POST);
   $data_arr=get_defined_vars();
   //var_dump($data_arr);
   $cid=$data_arr['compId'];
   $qry=$data_arr['itemkeyword'];
   //var_dump($cid . $qry);
        if(!empty($qry)){
            //$data = $this->db->get_where("products_tbl", ['prod_name' => $query])->row_array();
        $this->db->where("company_id",$cid);
        $this->db->like("prod_name",$qry);
        $data=$this->db->get('products_tbl')->result();
        }else{
            $data = $this->db->get("products_tbl")->result();
        }
     echo json_encode($data);
      //  $this->response($data, REST_Controller::HTTP_OK);
    }

public function updsettings()
{
extract($_POST);
$data_arr=get_defined_vars();
//$data_array=array("finyear"=>$finyear,"compId"=>$compId,"next_no"=>$next_invno,"trans_type"=>"SALE
//var_dump($data_arr);
$cid=$data_arr['compId'];
$finyear=$data_arr['finyear'];
$next_no=$data_arr['next_no'];
$trans_type=$data_arr['trans_type'];

   $updSett=$this->data_model->updSettings($cid,$finyear,$next_no,$trans_type);
//var_dump($updSett);
//return json_encode(array("status"==$updSett));

}


public function getcashledgerbyname()
{
extract($_POST);  
   $data_arr=get_defined_vars();
  // var_dump($data_arr);
   $cid=$data_arr['compId'];
   $qry=$data_arr['itemkeyword'];
   $flag=$data_arr['flag'];
$data=array();
 if($flag=="csh")
 {
  $gpid=1;
     $this->db->where("company_id",$cid);
     $this->db->where("account_groupid",$gpid);
     $this->db->like("account_name",$qry);
     $data=$this->db->get('ledgermaster_tbl')->result();
 
     
     echo json_encode($data);





  }
  
//        $data=$this->db->get('ledgermaster_tbl')->result();
 
 else
 {
 //     $gid=array("1");
        $this->db->where("company_id",$cid);
        $this->db->like("account_name",$qry);

        $data=$this->db->get('ledgermaster_tbl')->result();
    echo json_encode($data);


 }

}


        public function lbyid()
    {
  extract($_POST);
   $data_arr=get_defined_vars();
   //var_dump($data_arr);
   $cid=$data_arr['compId'];
   $qry=$data_arr['actid'];
   //var_dump($cid . $qry);
        if(!empty($qry)){
            //$data = $this->db->get_where("products_tbl", ['prod_name' => $query])->row_array();
        $this->db->where("company_id",$cid);
        $this->db->where("id",$qry);
        $data=$this->db->get('ledgermaster_tbl')->result();
        }else{
            $data = $this->db->get("ledgermaster_tbl")->result();
        }
     echo json_encode($data);
      //  $this->response($data, REST_Controller::HTTP_OK);
    }


        public function lbyname()
    {
            extract($_POST);
   $data_arr=get_defined_vars();
   //var_dump($data_arr);
   $cid=$data_arr['compId'];
   $qry=$data_arr['itemkeyword'];
   //var_dump($cid . $qry);
        if(!empty($qry)){
            //$data = $this->db->get_where("products_tbl", ['prod_name' => $query])->row_array();
        $this->db->where("company_id",$cid);
        $this->db->where("account_name",$qry);
        $data=$this->db->get('ledgermaster_tbl')->result();
        }else{
            $data = $this->db->get("ledgermaster_tbl")->result();
        }
     echo json_encode($data);
      //  $this->response($data, REST_Controller::HTTP_OK);
    }


    public function getCashBankGroupid()
    {
      $this->db->where("id",1);
      $data=$this->db->get('group_tbl')->result();
      echo json_encode($data);
    }


        public function ldgbyname()
    {
            extract($_POST);
   $data_arr=get_defined_vars();
   //var_dump($data_arr);
   $cid=$data_arr['compId'];
   $qry=$data_arr['itemkeyword'];
 $flag=$data_arr['flag'];


    //var_dump($cid . $qry);
      if($flag=="gen") {
            //$data = $this->db->get_where("products_tbl", ['prod_name' => $query])->row_array();
        $this->db->where("company_id",$cid);
        $this->db->where("account_name",$qry);
        $data=$this->db->get('ledgermaster_tbl')->result();
        }
     echo json_encode($data);
      //  $this->response($data, REST_Controller::HTTP_OK);
    }






    public function pursal($cid =null, $transtype = null, $finyear=null)
    {
    $data = array();        
    
    
    $sdata = $this->data_model->getSalesPurchaseData($cid,$transtype,$finyear);
    
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
    


    public function accountstransaction_put($id)
    {
        $input = $this->input->post();
       var_dump($input);
    $this->db->where('id',$id);
     $status= $this->db->update('transaction_tbl', $input);
//        $msg= array("status"=>$status);     
     //   $this->response($msg, REST_Controller::HTTP_OK);
     $msg= array("status"=>$status, "success"=>true,"messages"=>"record updated successfully");     

   echo json_encode($msg);
    }



    public function transaction_put($id)
    {
        $input = $this->input->post();
 //       var_dump($input);
     $status= $this->db->update('transaction_tbl', $input, array('id'=>$id));
//        $msg= array("status"=>$status);     
     //   $this->response($msg, REST_Controller::HTTP_OK);
     $msg= array("status"=>$status, "success"=>true,"messages"=>"record updated successfully");     

   echo json_encode($msg);
    }

    public function transactionitem_put($id,$cid,$finyear)
    {
      var_dump($id);
        $input = $this->input->post();
        var_dump($input);
        $this->db->set($input);
 $status= $this->data_model->updateTransItems($id,$cid,$finyear,$input);


//        $status= $this->db->update('itemtransaction_tbl', $input, array('trans_link_id'=>$id));
         $msg= array("status"=>$status, "success"=>true,"messages"=>"Items delflag set successfully");    
   //     $this->response($msg, REST_Controller::HTTP_OK);
  echo json_encode($msg); 
  }

  public function createTransItems()
  {
      $input = $this->input->post();
  $status=$this->db->insert('itemtransaction_tbl',$input);
      $msg= array("status"=>$status, "success"=>true,"messages"=>"Invoice updated successfully");     
echo json_encode($msg);
      //       $this->response($msg, REST_Controller::HTTP_OK);
  } 
  



    
}