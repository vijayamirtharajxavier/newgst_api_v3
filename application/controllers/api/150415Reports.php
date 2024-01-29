<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Reports extends CI_Controller {

	public function __construct(){
        parent::__construct();
     //   check_login_user();
       // $this->load->library('curl');
$this->load->helper('form');
       $this->load->database();
      $this->load->model('data_model');

}
public function gettaxesbyid()
{
$data=array();
   extract($_POST);
   $data_arr=get_defined_vars();
 // var_dump($data_arr);
   $cid=$data_arr['compId'];
   //$finyear=$data_arr['finyear'];
   $id=$data_arr['id'];
$taxData=$this->data_model->getTaxesDatabyid($cid,$id);
if($taxData)
{
    foreach ($taxData as $key => $txvalue) {
              $taxdata[]=array(
            "gst_pc" => $txvalue['item_gstpc'],
            "taxable_amount" => $txvalue['taxable_amount'],
            "item_cgst" => $txvalue['item_cgst'],
            "item_sgst"=>$txvalue['item_sgst'],
            "item_igst" => $txvalue['item_igst']
        );
    }
    echo json_encode($taxdata);
}


}


public function glreport()
{
//$data = array("s_date"=>$s_date,"e_date"=>$e_date,"finyear"=>$finyear,"compId"=>$compId,"actid"=>$actid,"fdate"=>$fdate,"tdate"=>$tdate);
$data=array();
   extract($_POST);
   $data_arr=get_defined_vars();
   //var_dump($data_arr);
   $cid=$data_arr['compId'];
   $finyear=$data_arr['finyear'];
   $actid=$data_arr['actid'];
   $start_date=$data_arr['fdate'];
   $end_date=$data_arr['tdate'];
   $sdate=$data_arr['s_date'];
$lopbal=0;
$op_bal=0;
$opbal=0;
$gl_ldg = $this->data_model->getLedgerbyId($actid);
if($gl_ldg)
{
  foreach ($gl_ldg as $key => $value) {
    # code...
    $lopbal=$value['account_openbal'];
  }
}


$gldata_op = $this->data_model->getglop($cid,$finyear,$start_date,$actid);

if($gldata_op)
{
//var_dump($gldata_op);
 foreach ($gldata_op as $key => $opglvalue) 
    {
        # code...
    //$lopbal=$opglvalue['account_openbal'];    
    $opbal=$opglvalue['opbal'];
    $op_bal = $lopbal+$opbal;
    }   
}


$gldata = $this->data_model->getgltransaction($cid,$finyear,$start_date,$end_date,$actid);
if($gldata)
{
 foreach ($gldata as $key => $glvalue) {
        # code...
        
$data[]=array("id" =>  $glvalue['id'],
            "trans_id" => $glvalue['trans_id'],
            "trans_date" => $glvalue['trans_date'],
            "db_account" => $glvalue['db_account'],
            "cr_account" => $glvalue['cr_account'],
            "trans_type" =>$glvalue['trans_type'],
            "trans_amount" => $glvalue['trans_amount'],
            "net_amount" => $glvalue['net_amount'],
            "trans_reference" => $glvalue['trans_reference'],
            "trans_narration" => $glvalue['trans_narration'],
            "opbal"=>$op_bal
);



    }   
}

echo json_encode($data);
   
}

public function getmonthwiseclientdata()
{
  $data=array();
$osdata=array("outstand"=>0);
   extract($_POST);
   $data_arr=get_defined_vars();
   //var_dump($data_arr);
   $cid=$data_arr['compId'];
   $finyear=$data_arr['finyear'];
   //$actid=$data_arr['act_id'];
   $trans_type=$data_arr['trans_type'];

   $getCWCode=$this->data_model->clientwisemsalescode($cid,$finyear,$trans_type);
   if($getCWCode)
   {
    foreach ($getCWCode as $key => $value) {
      # code...
      $actid=$value['db_account'];





   $getCWDataos=$this->data_model->clientwisemsalesdataos($actid,$finyear,$cid);
   if($getCWDataos)
   {
  
    foreach ($getCWDataos as $key => $ovalue) {
      # code...
      //$osdata=$value['outstand'];
$osdata=array("outstand"=>$ovalue['outstand']); 
}
//var_dump($osdata);
}

$getCWData=$this->data_model->clientwisemsalesdata($cid,$finyear,$trans_type,$actid);
if($getCWData)
{
  foreach ($getCWData as $key => $cwvalue) {
    # code...
    $data[]=array_merge($cwvalue,$osdata);
  }
}

    
   
//$fdata=array_merge($data,$osdata);
    } //CWCode



   }


echo json_encode($data);

}

public function getgstr32bdata()
{
  $data=array();
   extract($_POST);
   $data_arr=get_defined_vars();
 //  var_dump($data_arr);
   $cid=$data_arr['compId'];
   $fdate=$data_arr['fdate'];
   $tdate=$data_arr['tdate'];
   $cstatecode=$data_arr['cstatecode'];

$gstr32bData=$this->data_model->get_gstr32bData($fdate,$tdate,$cstatecode,$cid);
//var_dump($gstr32bData);
if($gstr32bData)
{
foreach ($gstr32bData as $key => $gvalue) {
$data[]=array("statecode"=>$gvalue['statename'],"txbamt"=>$gvalue['txb_amt'],"igstamt"=>$gvalue['igst_amt']);

  }  
}
echo json_encode($data);


}


public function getGstJson()

{
  $data=array();
   extract($_POST);
   $data_arr=get_defined_vars();
//$data_post = array("fdate"=>$fdate,"tdate"=>$tdate,"compId"=>$compId,"compStatecode"=>$compStatecode,"compGstin"=>$compGstin,"retmon"=>$retmon,"rcm"=>$rcm);

   //var_dump($data_arr);
   $cid=$data_arr['compId'];
   $fdate=$data_arr['fdate'];
   $tdate=$data_arr['tdate'];
   $compStatecode=$data_arr['compStatecode'];
   $compGstin=$data_arr['compGstin'];
   $retmon=$data_arr['retmon'];
   $rcm=$data_arr['rcm'];
   $ec=$data_arr['ec'];
   $gstGroupData=$this->data_model->gstGroup($fdate,$tdate,$cid);
   if($gstGroupData)
   {
    foreach ($gstGroupData as $key => $gstvalue) {
      
      $gstin=$gstvalue['gstin'];

//var_dump($gstin);
    $transbyGstin=$this->data_model->get_b2bTransbyGstin($fdate,$tdate,$cid,$gstin);
    if($transbyGstin)
    {
      foreach ($transbyGstin as $key => $tbgvalue) {
        
      $inv_no = $tbgvalue['trans_id'];
      $tr_date = $tbgvalue['trans_date'];
      $tr_id = $tbgvalue['trans_id'];
      $igstno = $tbgvalue['gstin'];

//var_dump($inv_no);

    $transbyInvSum=$this->data_model->get_b2bTransbyInvSum($fdate,$tdate,$cid,$igstno,$inv_no);
    if($transbyInvSum)
    {
      foreach ($transbyInvSum as $key => $tbisvalue) {
        # code...
   $invamt=$tbisvalue['inv_amt'];
   




      } //tbisvalue
    }


 $itms = array();
    $transbyInv=$this->data_model->get_b2bTransbyInv($fdate,$tdate,$cid,$gstin,$inv_no);
    if($transbyInv)
    {
      foreach ($transbyInv as $key => $b2bvalue) {

  if($inv_no==$b2bvalue['trans_id'])
{
  
if($compStatecode==$b2bvalue['pos'])
{

$itm_cgst= $b2bvalue['item_cgst'];
$itm_sgst= $b2bvalue['item_sgst'];
$itm_cs = $b2bvalue['item_cess'];
$itm_gstpc = $b2bvalue['gst_pc'].'01';

$itms[]=array('num'=>(int)$itm_gstpc,
    'itm_det' => array('txval'=>(float)$b2bvalue['taxable_amt'],
      'rt' =>(float)$b2bvalue['gst_pc'],
      'camt'=>(float)$itm_cgst,
      'samt'=>(float)$itm_sgst,
      'csamt'=>(float)$itm_cs
  ),);

}
else {
$itm_igst= $b2bvalue['item_igst'];
$itm_cs = $b2bvalue['item_cess'];
$itm_gstpc = $b2bvalue['gst_pc'].'01';
$itms[]=array('num'=>(int)$itm_gstpc,
    'itm_det' => array('txval'=>(float)$b2bvalue['taxable_amt'],
      'rt' =>(float)$b2bvalue['gst_pc'],
      'iamt'=>(float)$itm_igst,
      'csamt'=>(float)$itm_cs
  ),);

}

}




    } //b2bvalue

$inv_amt = $invamt;
//var_dump($inv_amt);
$invdate =date("d-m-Y", strtotime($tr_date));

    $inv['inv'][]= array('inum' => $tr_id,
    'idt'=>$invdate,
    'val'=> (float)$inv_amt,
    'pos' =>"'" . substr($gstin,0,2) ."'", // $gstvalue['placeofsupply'] ."'",
    'rchrg' => $rcm,
    'inv_typ' => 'R','itms'=>$itms);


      } //transbyGstin

    
}

    } //gstvalue
$arrmerge1[]= array_merge(array('ctin' => $gstin),$inv);

$inv = array();
$itms = array();
   
}

$data['b2b']=$arrmerge1;
$arrmerge1=array();
$finalmerge = array_merge(array('gstin'=>$compGstin,'fp'=>"'". $retmon,'version'=>"GST3.0.0",'hash'=>"hash"),$data);


//B2C

$b2arr = array();
$b2cData=$this->data_model->getB2C($fdate,$tdate,$cid);

if($b2cData)
  //var_dump($b2cData);
{
foreach ($b2cData as $key => $b2cvalue) {
  
  

if($compStatecode==$b2cvalue['pos'])
{

//$itm_gstpc = $b2bvalue['gst_pc'].'01';

$b2arr[] = array('sply_ty'=>"INTRA",'pos'=>"'" . $b2cvalue['pos'],'typ'=>$ec,'txval'=>(float)$b2cvalue['taxable_amt'],'rt'=>(float)$b2cvalue['gst_pc'],'iamt'=>(float)$b2cvalue['item_igst'],'camt'=>(float)$b2cvalue['item_cgst'],'samt'=>(float)$b2cvalue['item_sgst'],'csamt'=>(float)$b2cvalue['item_cess']);
}
else {
$b2arr[] = array('sply_ty'=>"INTER",'pos'=>"'" . $b2cvalue['pos'],'typ'=>$ec,'txval'=>(float)$b2cvalue['taxable_amt'],'rt'=>(float)$b2cvalue['gst_pc'],'iamt'=>(float)$b2cvalue['item_igst'],'camt'=>(float)$b2cvalue['item_cgst'],'samt'=>(float)$b2cvalue['item_sgst'],'csamt'=>(float)$item_cess);
}

}
}
$data['b2cs']=$b2arr;
$fmerge = array_merge($finalmerge,$data);
$inv = array();
$itms = array();

ini_set('precision',10);
ini_set('serialize_precision',10);

//echo json_encode($fmerge);
$output = json_encode($fmerge,JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
  $outp = str_replace("'", "", $output);
  echo $outp;





//$output = json_encode($finalmerge,JSON_NUMERIC_CHECK | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
 // $outp = str_replace("'", "", $output);
 // echo $outp;


//echo json_encode($finalmerge,JSON_PRETTY_PRINT);


}


}

public function getgstr1data()
{
  $data=array();
   extract($_POST);
   $data_arr=get_defined_vars();
   //var_dump($data_arr);
   $cid=$data_arr['compId'];
   $fdate=$data_arr['fdate'];
   $tdate=$data_arr['tdate'];
   $type=$data_arr['type'];

if($type=="B2B")
{
$gstr1Data=$this->data_model->getGstr1b2b($fdate,$tdate,$cid);

if($gstr1Data)
{
foreach ($gstr1Data as $key => $gvalue) {
     $data["data"][]=array("gstin"=>$gvalue['gstin'],"inv_no"=>$gvalue['trans_id'],"trans_date"=>date("d-m-Y",strtotime($gvalue['trans_date'])), "gstpc"=>$gvalue['item_gstpc'],"igst"=>$gvalue['igst'],"cgst"=>$gvalue['cgst'],"sgst"=>$gvalue['sgst'],"txb_amt"=>$gvalue['txb_amt'],"net_amt"=>$gvalue['net_amt']);


  }  
}


}
else
{
$gstr1Data=$this->data_model->getGstr1b2c($fdate,$tdate,$cid);  
if($gstr1Data)
{
foreach ($gstr1Data as $key => $gvalue) {
     $data["data"][]=array("gstin"=>$gvalue['statecode'],"inv_no"=>$gvalue['trans_id'],"trans_date"=>date("d-m-Y",strtotime($gvalue['trans_date'])), "gstpc"=>$gvalue['item_gstpc'],"igst"=>$gvalue['igst'],"cgst"=>$gvalue['cgst'],"sgst"=>$gvalue['sgst'],"txb_amt"=>$gvalue['txb_amt'],"net_amt"=>$gvalue['net_amt']);


  }  
}

}

echo json_encode($data);
}


public function getgstr3b5data()
{
  $data=array();
   extract($_POST);
   $data_arr=get_defined_vars();
  // var_dump($data_arr);
   $cid=$data_arr['compId'];
   $fdate=$data_arr['fdate'];
   $tdate=$data_arr['tdate'];
   $trans_type=$data_arr['trans_type'];

$gstr3b5Data=$this->data_model->get_gstr3b5Data($trans_type,$fdate,$tdate,$cid);
//var_dump($gstr3b5Data);
if($gstr3b5Data)
{
foreach ($gstr3b5Data as $key => $gvalue) {
  $data=array("intra_zero"=>$gvalue['intra_zero'],"inter_zero"=>$gvalue['inter_zero']);
//$data=array("zerogst"=>$gvalue['zerogst'],"txbgst"=>$gvalue['txbgst'],"igst_amt"=>$gvalue['txbigst'],"cgst_amt"=>$gvalue['txbcgst'],"sgst_amt"=>$gvalue['txbsgst']);


  }  
}
echo json_encode($data);


}


public function getgstr3bdata()
{
  $data=array();
   extract($_POST);
   $data_arr=get_defined_vars();
   //var_dump($data_arr);
   $cid=$data_arr['compId'];
   $fdate=$data_arr['fdate'];
   $tdate=$data_arr['tdate'];
   $trans_type=$data_arr['trans_type'];

$gstr3bData=$this->data_model->get_gstr3bData($trans_type,$fdate,$tdate,$cid);
if($gstr3bData)
{
foreach ($gstr3bData as $key => $gvalue) {
$data=array("zerogst"=>$gvalue['zerogst'],"txbgst"=>$gvalue['txbgst'],"igst_amt"=>$gvalue['txbigst'],"cgst_amt"=>$gvalue['txbcgst'],"sgst_amt"=>$gvalue['txbsgst']);


  }  
}
echo json_encode($data);
}




public function getmonthwiseclientcode()
{
  $data=array();
   extract($_POST);
   $data_arr=get_defined_vars();
   //var_dump($data_arr);
   $cid=$data_arr['compId'];
   $finyear=$data_arr['finyear'];
   $trans_type=$data_arr['trans_type'];
   $getCWCode=$this->data_model->clientwisemsalescode($cid,$finyear,$trans_type);
   if($getCWCode)
   {
    foreach ($getCWCode as $key => $value) {
      # code...
      $data[]=$value;

    }
   }
echo json_encode($data);
}



public function cashbankreport()
{
//$data = array("s_date"=>$s_date,"e_date"=>$e_date,"finyear"=>$finyear,"compId"=>$compId,"actid"=>$actid,"fdate"=>$fdate,"tdate"=>$tdate);
$data=array();
   extract($_POST);
   $data_arr=get_defined_vars();
   //var_dump($data_arr);
   $cid=$data_arr['compId'];
   $finyear=$data_arr['finyear'];
   $actid=$data_arr['actid'];
   $start_date=$data_arr['fdate'];
   $end_date=$data_arr['tdate'];
   
$lopbal=0;
$op_bal=0;
$opbal=0;
$gl_ldg = $this->data_model->getLedgerbyId($actid);
if($gl_ldg)
{
  foreach ($gl_ldg as $key => $value) {
    # code...
    $lopbal=$value['account_openbal'];
  }
}


$gldata_op = $this->data_model->getcbOp($cid,$finyear,$start_date,$actid);
if($gldata_op)
{
 foreach ($gldata_op as $key => $opglvalue) {
        # code...
    //$lopbal=$opglvalue['account_openbal'];    
    $opbal=$opglvalue['opbal'];
    $op_bal = $lopbal+$opbal;
    }   
}


$gldata = $this->data_model->getgltransaction($cid,$finyear,$start_date,$end_date,$actid);
if($gldata)
{
 foreach ($gldata as $key => $glvalue) {
        # code...
        
$data[]=array("id" =>  $glvalue['id'],
            "trans_id" => $glvalue['trans_id'],
            "trans_date" => $glvalue['trans_date'],
            "db_account" => $glvalue['db_account'],
            "cr_account" => $glvalue['cr_account'],
            "trans_type" =>$glvalue['trans_type'],
            "trans_amount" => $glvalue['trans_amount'],
            "net_amount" => $glvalue['net_amount'],
            "trans_reference" => $glvalue['trans_reference'],
            "trans_narration" => $glvalue['trans_narration'],
            "opbal"=>$op_bal
);



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


public function getcashbankledger()
{
extract($_POST);  
   $data_arr=get_defined_vars();
   //var_dump($data_arr);
   $cid=$data_arr['compId'];
        $this->db->where("company_id",$cid);
        $this->db->where("account_groupid",1);
       // $this->db->like("account_name",$qry);
        $data=$this->db->get('ledgermaster_tbl')->result();
    echo json_encode($data);

}


public function getcashledgerbyname()
{
extract($_POST);  
   $data_arr=get_defined_vars();
   //var_dump($data_arr);
   $cid=$data_arr['compId'];
   $qry=$data_arr['itemkeyword'];
   $flag=$data_arr['flag'];

 if($flag=="csh")
 {
        $this->db->where("company_id",$cid);
        $this->db->where("account_groupid",1);
        $this->db->like("account_name",$qry);

        $data=$this->db->get('ledgermaster_tbl')->result();
    echo json_encode($data);
 }
 else
 {
      $gid=array("1");
        $this->db->where("company_id",$cid);
        $this->db->where_not_in("account_groupid",$gid);
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



        public function ldgbyname()
    {
            extract($_POST);
   $data_arr=get_defined_vars();
   //var_dump($data_arr);
   $cid=$data_arr['compId'];
   $qry=$data_arr['itemkeyword'];
 $flag=$data_arr['flag'];
    //var_dump($cid . $qry);
        if(!empty($flag=="gen")){
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



}