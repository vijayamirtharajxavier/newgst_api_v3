<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Data_model extends CI_Model {


public function getSalesPurchaseData($transtype=null,$finyear=null)
{
	   if(!empty($transtype)){
//	$sql="SELECT t.id, t.trans_id,t.trans_date,t.order_date,t.order_no,t.dc_no,t.dc_date,t.trans_type,t.db_account,t.cr_account,t.statecode,t.gstin,t.inv_type,t.rcm,sum(it.nett_amount) as `net_amount`,t.trans_amount,t.trans_reference,t.trans_narration,t.salebyperson,t.finyear,l.account_name`custname` FROM transaction_tbl t,ledgermaster_tbl l, itemtransaction_tbl it where  (t.db_account=l.id or t.db_account=0) and t.trans_type=? AND t.finyear=? and t.delflag=0 and it.delflag=0 and t.trans_id=it.trans_id GROUP BY t.id order by t.trans_date desc, t.id desc";
//$sql="SELECT t.id, t.trans_id, t.trans_date, t.order_date, t.order_no, t.dc_no, t.dc_date, t.trans_type, t.db_account, t.cr_account, t.statecode, t.gstin, t.inv_type, t.rcm, SUM(it.nett_amount) AS net_amount, t.trans_amount, t.trans_reference, t.trans_narration, t.salebyperson, t.finyear, l.account_name AS custname FROM transaction_tbl t LEFT JOIN ledgermaster_tbl l ON t.db_account = l.id INNER JOIN itemtransaction_tbl it ON t.trans_id = it.trans_id WHERE t.trans_type = ? AND t.finyear = ? AND t.delflag = 0 AND it.delflag = 0 GROUP BY t.id ORDER BY t.trans_date DESC, t.id DESC";
$sql="SELECT 
    t.id, 
    t.trans_id, 
    t.trans_date, 
    t.order_date, 
    t.order_no, 
    t.dc_no, 
    t.dc_date, 
    t.trans_type, 
    t.db_account, 
    t.cr_account, 
    t.statecode, 
    t.gstin, 
    t.inv_type, 
    t.rcm, 
    SUM(it.nett_amount) AS net_amount, 
    t.trans_amount, 
    t.trans_reference, 
    t.trans_narration, 
    t.salebyperson, 
    t.finyear, 
    l.account_name AS custname,
    IFNULL(agg.noi, 0) AS noi, 
    IFNULL(agg.txb_amt, 0) AS txb_amt,
    IFNULL(agg.net_amt, 0) AS net_amt
FROM 
    transaction_tbl t
LEFT JOIN 
    ledgermaster_tbl l 
    ON t.db_account = l.id
INNER JOIN 
    itemtransaction_tbl it 
    ON t.trans_id = it.trans_id
LEFT JOIN (
    SELECT 
        trans_link_id, 
        COUNT(trans_link_id) AS noi, 
        SUM(taxable_amount) AS txb_amt, 
        SUM(nett_amount) AS net_amt
    FROM 
        itemtransaction_tbl
    WHERE 
        delflag = 0
    GROUP BY 
        trans_link_id
) agg
ON t.id = agg.trans_link_id
WHERE 
    t.trans_type = ? 
    AND t.finyear = ? 
    AND t.delflag = 0 
    AND it.delflag = 0
GROUP BY 
    t.id
ORDER BY 
    t.trans_date DESC, 
    t.id DESC";

$query = $this->db->query($sql, array($transtype,$finyear));
    return $query->result_array();

}
    

}

public function getTransDatabyid($cid=null,$finyear=null,$trans_type=null,$id=null)
{
    $sql="SELECT * from transaction_tbl where company_id=? and finyear=? and trans_type=? and id=?";
    $query=$this->db->query($sql,array($cid,$finyear, $trans_type,$id));
    return $query->result_array();

}


public function getCompanyDetails($cid=null)
{
$sql = "SELECT *  FROM company_tbl WHERE  id=?";
$query = $this->db->query($sql, array($cid));
return $query->result_array();
}




public function getSalesPurchaseReg($transtype=null,$finyear=null,$cid=null,$fdate=null,$tdate=null)
{
    //var_dump($transtype . $finyear . $cid . $fdate . $tdate);
       if(!empty($transtype)){
    $sql="SELECT t.id, t.trans_id,t.trans_date,t.order_date,t.order_no,t.dc_no,t.dc_date,t.trans_type,t.db_account,t.cr_account,t.statecode,t.gstin,t.inv_type,t.rcm,t.net_amount,t.trans_amount,t.trans_reference,t.trans_narration,t.salebyperson,t.finyear,l.account_name`custname` FROM transaction_tbl t,ledgermaster_tbl l where  t.db_account=l.id and t.trans_type=? AND t.finyear=? AND t.company_id=? AND t.trans_date>=? AND t.trans_date<=?  and t.delflag=0 GROUP BY t.id order by t.trans_date desc, t.id desc";

$query = $this->db->query($sql, array($transtype,$finyear,$cid,$fdate,$tdate));
    return $query->result_array();

}
    

}


public function gstGroup($fdate,$tdate,$cid)
{
$sql="SELECT gstin from transaction_tbl where  trans_type='SALE' AND trans_date>=? and trans_date<=? and company_id=? and gstin<>'' and delflag=0 group by gstin order by gstin";
$query = $this->db->query($sql, array($fdate,$tdate,$cid));
return $query->result_array();

}

public function get_b2bTransbyInv($fdate,$tdate,$cid,$gstin,$invno)
{

$sql = "SELECT t.trans_id, t.gstin,t.statecode `pos`,t.rcm,t.trans_date,itm.item_gstpc `gst_pc`,ROUND(sum(itm.cgst_amount),2) item_cgst,ROUND(sum(itm.sgst_amount),2) item_sgst,ROUND(sum(itm.igst_amount),2) item_igst,ROUND(sum(itm.cess_amount),2) item_cess,ROUND(sum(itm.taxable_amount),2) taxable_amt,ROUND(sum(itm.nett_amount),2) `net_amt` FROM `itemtransaction_tbl` itm, transaction_tbl t WHERE  t.trans_type='SALE' AND itm.delflag=0 AND itm.trans_date>=? and itm.trans_date<=? and itm.company_id=? and t.gstin=? and itm.trans_id=t.trans_id and  t.trans_id=? and t.delflag=0 GROUP by itm.item_gstpc order by itm.trans_link_id";
$query = $this->db->query($sql, array($fdate,$tdate,$cid,$gstin,$invno));
return $query->result_array();

}


public function get_b2bTransbyInvSum($fdate,$tdate,$cid,$gstin,$invno)
{
    $sql = "SELECT ROUND(sum(itm.nett_amount),2) inv_amt FROM `itemtransaction_tbl` itm, transaction_tbl t WHERE  t.trans_type='SALE' AND itm.delflag=0 AND itm.trans_date>=? and itm.trans_date<=? and itm.company_id=? and t.gstin=? and itm.trans_id=t.trans_id and  t.trans_id=? and t.delflag=0";
$query = $this->db->query($sql, array($fdate,$tdate,$cid,$gstin,$invno));
return $query->result_array();

}

public function get_b2bTransbyGstin($fdate,$tdate,$cid,$gstin)
{
    $sql="SELECT * from transaction_tbl  where trans_type='SALE' AND trans_date>=? and trans_date<=? and company_id=? and gstin=? and delflag=0 order by id asc";
$query = $this->db->query($sql, array($fdate,$tdate,$cid,$gstin));
    return $query->result_array();

}

public function getB2C($fdate,$tdate,$cid)
{


$sql = "SELECT t.gstin,t.statecode `pos`,t.rcm,itm.item_gstpc `gst_pc`,sum(itm.cgst_amount) item_cgst,sum(itm.sgst_amount) item_sgst,sum(itm.igst_amount) item_igst,sum(itm.cess_amount) item_cess,sum(itm.taxable_amount) taxable_amt,sum(itm.nett_amount) `net_amt` FROM `itemtransaction_tbl` itm, transaction_tbl t WHERE  t.trans_type='SALE' AND itm.delflag=0 AND itm.trans_date>=? and itm.trans_date<=? and itm.company_id=? and t.gstin='' and itm.trans_id=t.trans_id and t.delflag=0 GROUP by t.statecode,itm.item_gstpc";
$query = $this->db->query($sql, array($fdate,$tdate,$cid));
return $query->result_array();


}


public function getGstr1hsn($fdate,$tdate,$cid,$trans_type)
{
$sql="SELECT item_hsnsac,item_unit,sum(item_qty)`item_qty`,sum(taxable_amount)`taxable_amount`,item_gstpc,sum(igst_amount)`igst_amount`,sum(cgst_amount)`cgst_amount`,sum(sgst_amount)`sgst_amount`,sum(cess_amount)`cess_amount` FROM `itemtransaction_tbl` WHERE company_id=1 and trans_date>=? AND trans_date<=? AND trans_type=? and company_id=? AND delflag=0 GROUP BY item_hsnsac ORDER BY item_hsnsac";
$query = $this->db->query($sql, array($fdate,$tdate,$trans_type,$cid));
    return $query->result_array();
}


public function getGstr1b2bhsn($fdate,$tdate,$cid,$trans_type)
{
$sql="SELECT it.trans_id,it.item_hsnsac,it.item_unit,sum(it.item_qty)`item_qty`,sum(it.taxable_amount)`taxable_amount`,it.item_gstpc,sum(it.igst_amount)`igst_amount`,sum(it.cgst_amount)`cgst_amount`,sum(it.sgst_amount)`sgst_amount`,sum(it.cess_amount)`cess_amount` FROM `itemtransaction_tbl` it, transaction_tbl t WHERE it.company_id=t.company_id and it.trans_date>=? AND it.trans_date<=? AND it.trans_type=? and it.company_id=? AND it.delflag=0 and it.trans_id=t.trans_id and t.gstin<>'' GROUP BY item_hsnsac ORDER BY item_hsnsac";
$query = $this->db->query($sql, array($fdate,$tdate,$trans_type,$cid));
    return $query->result_array();
}

public function getGstr1b2chsn($fdate,$tdate,$cid,$trans_type)
{
$sql="SELECT it.trans_id,it.item_hsnsac,it.item_unit,sum(it.item_qty)`item_qty`,sum(it.taxable_amount)`taxable_amount`,it.item_gstpc,sum(it.igst_amount)`igst_amount`,sum(it.cgst_amount)`cgst_amount`,sum(it.sgst_amount)`sgst_amount`,sum(it.cess_amount)`cess_amount` FROM `itemtransaction_tbl` it, transaction_tbl t WHERE it.company_id=t.company_id and it.trans_date>=? AND it.trans_date<=? AND it.trans_type=? and it.company_id=? AND it.delflag=0 and it.trans_id=t.trans_id and t.gstin='' GROUP BY item_hsnsac ORDER BY item_hsnsac";
$query = $this->db->query($sql, array($fdate,$tdate,$trans_type,$cid));
    return $query->result_array();
}


public function getGstr1b2b($fdate,$tdate,$cid)
{
$sql="SELECT l.account_name, itm.trans_id,itm.trans_date,t.gstin,itm.item_gstpc, round(sum(itm.taxable_amount),2)`txb_amt`,round(sum(itm.nett_amount),2)`net_amt`,round(sum(itm.igst_amount),2)`igst`,round(sum(itm.sgst_amount),2)`sgst`,round(sum(itm.cgst_amount),2)`cgst` FROM `itemtransaction_tbl` itm,transaction_tbl t,ledgermaster_tbl l WHERE t.db_account=l.id AND itm.delflag=0 AND itm.trans_type='SALE' AND t.id=itm.trans_link_id and t.gstin<>'' and itm.trans_date>=? and itm.trans_date<=? and itm.company_id=? and t.delflag=0 group by itm.trans_id,itm.item_gstpc ORDER BY itm.trans_id,t.gstin";
$query = $this->db->query($sql, array($fdate,$tdate,$cid));
    return $query->result_array();
}

public function getGstr1b2c($fdate,$tdate,$cid)
{
$sql="SELECT l.account_name, t.statecode, itm.trans_id,itm.trans_date,t.gstin,itm.item_gstpc, round(sum(itm.taxable_amount),2)`txb_amt`,round(sum(itm.nett_amount),2)`net_amt`,round(sum(itm.igst_amount),2)`igst`,round(sum(itm.sgst_amount),2)`sgst`,round(sum(itm.cgst_amount),2)`cgst` FROM `itemtransaction_tbl` itm,transaction_tbl t,ledgermaster_tbl l WHERE t.db_account=l.id AND itm.delflag=0 AND itm.trans_type='SALE' AND t.id=itm.trans_link_id and t.gstin='' and itm.trans_date>=? and itm.trans_date<=? and itm.company_id=? and t.delflag=0 group by itm.trans_id,itm.item_gstpc ORDER BY itm.trans_id,t.gstin";
$query = $this->db->query($sql, array($fdate,$tdate,$cid));
    return $query->result_array();
}

public function get_gstr32bData($fdate,$tdate,$cstatecode,$cid)
{
    $sql="SELECT CONCAT(t.statecode,' - ',st.state_name)`statename`,sum(itm.taxable_amount)`txb_amt`,sum(itm.igst_amount)`igst_amt` FROM `itemtransaction_tbl` itm,transaction_tbl t,gststate_tbl st WHERE itm.delflag=0 and t.gstin='' and t.id=itm.trans_link_id and st.statecode_id=t.statecode and itm.trans_date>=? and itm.trans_type='SALE' and itm.trans_date<=? and t.statecode<>?  and t.company_id=? and t.delflag=0 GROUP BY t.statecode";

$query = $this->db->query($sql, array($fdate,$tdate,$cstatecode,$cid));
    return $query->result_array();

}


public function get_gstr2bData($trans_type,$fdate,$tdate,$cid)
{
    $sql="SELECT t.gstin,l.account_name,t.trans_id,t.inv_type,t.trans_date,SUM(it.nett_amount)`invval`,t.statecode,t.rcm,SUM(it.taxable_amount) `txblval`,it.item_gstpc,sum(igst_amount)`igstval`,sum(cgst_amount)`cgstval`,sum(sgst_amount)`sgstval`,sum(cess_amount)`cessval` FROM `transaction_tbl` t,ledgermaster_tbl l,itemtransaction_tbl it WHERE t.id=it.trans_link_id AND t.db_account=l.id and  t.trans_type=? AND t.company_id=1 and t.trans_date>=? and t.trans_date<=? AND it.delflag=0 and t.company_id=? and t.delflag=0 GROUP BY it.trans_link_id, it.item_gstpc ORDER BY t.trans_date";

$query = $this->db->query($sql, array($trans_type,$fdate,$tdate,$cid));
    return $query->result_array();
}


public function get_gstr3bData($trans_type,$fdate,$tdate,$cid)
{
/*$sql="SELECT SUM(CASE WHEN it.item_gstpc=0 and t.statecode=c.company_statecode THEN taxable_amount END) `intra_zero`,SUM(CASE WHEN it.item_gstpc=0 and t.statecode<>c.company_statecode THEN taxable_amount END) `inter_zero` FROM itemtransaction_tbl it,transaction_tbl t,company_tbl c WHERE it.delflag=0 and it.trans_type=? and it.trans_date>=? and it.trans_date<=? and it.company_id=? and it.trans_link_id=t.id and it.company_id=c.id";
*/

$sql="SELECT SUM(CASE WHEN  t.inv_type<>'DE' and  it.item_gstpc=0 THEN it.taxable_amount END) `zerogst`,SUM(CASE WHEN t.inv_type='DE' THEN it.taxable_amount END) `zerorate`,SUM(CASE WHEN t.inv_type<>'DE' and   it.item_gstpc<>0 THEN it.taxable_amount END) `txbgst`,SUM(CASE WHEN  t.inv_type<>'DE' and  it.item_gstpc<>0 THEN it.igst_amount END) `txbigst`,SUM(CASE WHEN t.inv_type<>'DE' and  it.item_gstpc<>0 THEN it.cgst_amount END) `txbcgst`,SUM(CASE WHEN  t.inv_type<>'DE' and  it.item_gstpc<>0 THEN it.sgst_amount END) `txbsgst` FROM itemtransaction_tbl it, transaction_tbl t WHERE it.trans_link_id=t.id and  it.delflag=0 and it.trans_type=? and it.trans_date>=? and it.trans_date<=? and it.company_id=? and t.delflag=0";

$query = $this->db->query($sql, array($trans_type,$fdate,$tdate,$cid));
    return $query->result_array();
}


public function get_gstr3b5Data($trans_type,$fdate,$tdate,$cid)
{

$sql="SELECT SUM(CASE WHEN it.item_gstpc=0 and t.statecode=c.company_statecode THEN it.taxable_amount END) `intra_zero`,SUM(CASE WHEN it.item_gstpc=0 and t.statecode<>c.company_statecode THEN it.taxable_amount END) `inter_zero` FROM itemtransaction_tbl it,transaction_tbl t,company_tbl c WHERE it.delflag=0 and it.trans_type=? and it.trans_date>=? and it.trans_date<=? and it.company_id=? and it.trans_link_id=t.id and it.company_id=c.id and t.delflag=0";

/*$sql="SELECT SUM(CASE WHEN item_gstpc=0 THEN taxable_amount END) `zerogst`,SUM(CASE WHEN item_gstpc<>0 THEN taxable_amount END) `txbgst`,SUM(CASE WHEN item_gstpc<>0 THEN igst_amount END) `txbigst`,SUM(CASE WHEN item_gstpc<>0 THEN cgst_amount END) `txbcgst`,SUM(CASE WHEN item_gstpc<>0 THEN sgst_amount END) `txbsgst` FROM itemtransaction_tbl WHERE delflag=0 and trans_type=? and trans_date>=? and trans_date<=? and company_id=?";
*/
$query = $this->db->query($sql, array($trans_type,$fdate,$tdate,$cid));
    return $query->result_array();


}





public function clientwisemsalesdata($cid,$finyear,$trans_type,$acct_id)
{
    $sql="SELECT t.db_account, l.account_name, SUM(case WHEN month(itm.trans_date)='04' THEN itm.taxable_amount ELSE '' END)`apr`,SUM(case WHEN month(itm.trans_date)='05' THEN itm.taxable_amount ELSE '' END)`may`,SUM(case WHEN month(itm.trans_date)='06' THEN itm.taxable_amount ELSE '' END)`jun`,SUM(case WHEN month(itm.trans_date)='07' THEN itm.taxable_amount ELSE '' END)`jul`,SUM(case WHEN month(itm.trans_date)='08' THEN itm.taxable_amount ELSE '' END)`aug`,SUM(case WHEN month(itm.trans_date)='09' THEN itm.taxable_amount ELSE '' END)`sep`,SUM(case WHEN month(itm.trans_date)='10' THEN itm.taxable_amount ELSE '' END)`oct`,SUM(case WHEN month(itm.trans_date)='11' THEN itm.taxable_amount ELSE '' END)`nov`,SUM(case WHEN month(itm.trans_date)='12' THEN itm.taxable_amount ELSE '' END)`dec`,SUM(case WHEN month(itm.trans_date)='01' THEN itm.taxable_amount ELSE '' END)`jan`,SUM(case WHEN month(itm.trans_date)='02' THEN itm.taxable_amount ELSE '' END)`feb`,SUM(case WHEN month(itm.trans_date)='03' THEN itm.taxable_amount ELSE '' END)`mar`,SUM(case WHEN t.db_account=$acct_id THEN (itm.igst_amount+itm.cgst_amount+itm.sgst_amount) ELSE '0' END)`gst` FROM itemtransaction_tbl itm,transaction_tbl t,ledgermaster_tbl l WHERE itm.trans_link_id=t.id and t.db_account=l.id and itm.delflag=0 AND itm.company_id=? AND itm.finyear=?  and t.db_account=? and t.delflag=0";
 	$query=$this->db->query($sql,array($cid,$finyear,$acct_id));
 	return $query->result_array();

}


public function getsptransaction($cid,$finyear,$start_date,$end_date,$trans_type)
{
       if(!empty($transtype)){
    $sql="SELECT t.id, t.trans_id,t.trans_date,t.order_date,t.order_no,t.dc_no,t.dc_date,t.trans_type,t.db_account,t.cr_account,t.statecode,t.gstin,t.inv_type,t.rcm,t.net_amount,t.trans_amount,t.net_amount-t.trans_amount `gst_amount`, t.trans_reference,t.trans_narration,t.salebyperson,t.finyear,l.account_name`custname` FROM transaction_tbl t,ledgermaster_tbl l where  t.db_account=l.id and t.trans_type=? AND  t.finyear=? AND t.company_id=? AND t.trans_date>=? and t.trans_date<=? and t.delflag=0 GROUP BY t.id order by t.trans_date, t.id asc";

$query = $this->db->query($sql, array($transtype,$finyear,$cid,$start_date,$end_date));
    return $query->result_array();
}

}




public function getCBTrans($cid,$finyear,$acct_id,$alcode)
{

 if($alcode==1)
 {   

$sql_os ="SELECT SUM(CASE WHEN t.trans_type='CNTR' AND (t.db_account=" . $acct_id . "  ) THEN t.trans_amount WHEN t.trans_type='RCPT' AND (t.db_account=" . $acct_id . "  or t.cr_account=" . $acct_id . "  ) THEN t.trans_amount ELSE 0 END)-SUM(CASE WHEN t.trans_type='CNTR' AND (t.cr_account=" . $acct_id . "  ) THEN t.trans_amount WHEN t.trans_type='PYMT' AND (t.db_account=" . $acct_id . "  or t.cr_account=" . $acct_id . "  ) THEN t.trans_amount ELSE 0 END) `outstand` FROM `transaction_tbl` t WHERE t.finyear=? and t.company_id=? and t.delflag=0";

  $query=$this->db->query($sql_os,array($finyear,$cid));
  return $query->result_array();

/*
$sql_os = "SELECT SUM(CASE WHEN t.trans_type='PURC' AND (t.db_account=" . $acct_id . "  or t.cr_account=" . $acct_id . " )  THEN t.net_amount  WHEN t.trans_type='CNTR' AND (t.db_account=" . $acct_id . " ) THEN t.trans_amount WHEN t.trans_type='RCPT' AND (t.db_account=" . $acct_id . "  or t.cr_account=" . $acct_id . " ) THEN t.trans_amount ELSE 0 END)-SUM(CASE WHEN t.trans_type='SALE' AND (t.db_account=" . $acct_id . " or t.cr_account=" . $acct_id . " )  THEN net_amount WHEN t.trans_type='CNTR' AND (t.cr_account=" . $acct_id . " ) THEN t.trans_amount  WHEN t.trans_type='PYMT' AND (t.db_account=" . $acct_id . "  or t.cr_account=" . $acct_id . " ) THEN t.trans_amount ELSE 0 END) `outstand` FROM `transaction_tbl` t WHERE t.finyear=? and t.company_id=?  and t.delflag=0"; */
}
}






public function getTrans($cid,$finyear,$acct_id,$gcode,$cbcode)
{

 if($gcode==1 && $cbcode==1)
 {   

$sql_os ="SELECT SUM(CASE WHEN t.trans_type='CNTR' AND (t.db_account=" . $acct_id . "  ) THEN t.trans_amount WHEN t.trans_type='RCPT' AND (t.db_account=" . $acct_id . "  or t.cr_account=" . $acct_id . "  ) THEN t.trans_amount ELSE 0 END)-SUM(CASE WHEN t.trans_type='CNTR' AND (t.cr_account=" . $acct_id . "  ) THEN t.trans_amount WHEN t.trans_type='PYMT' AND (t.db_account=" . $acct_id . "  or t.cr_account=" . $acct_id . "  ) THEN t.trans_amount ELSE 0 END) `outstand` FROM `transaction_tbl` t WHERE t.finyear=? and t.company_id=? and t.delflag=0";
/*

$sql_os = "SELECT SUM(CASE WHEN t.trans_type='PURC' AND (t.db_account=" . $acct_id . "  or t.cr_account=" . $acct_id . " )  THEN t.net_amount  WHEN t.trans_type='CNTR' AND (t.db_account=" . $acct_id . " ) THEN t.trans_amount WHEN t.trans_type='RCPT' AND (t.db_account=" . $acct_id . "  or t.cr_account=" . $acct_id . " ) THEN t.trans_amount ELSE 0 END)-SUM(CASE WHEN t.trans_type='SALE' AND (t.db_account=" . $acct_id . " or t.cr_account=" . $acct_id . " )  THEN net_amount WHEN t.trans_type='CNTR' AND (t.cr_account=" . $acct_id . " ) THEN t.trans_amount  WHEN t.trans_type='PYMT' AND (t.db_account=" . $acct_id . "  or t.cr_account=" . $acct_id . " ) THEN t.trans_amount ELSE 0 END) `outstand` FROM `transaction_tbl` t WHERE t.finyear=? and t.company_id=?  and t.delflag=0"; */

}
elseif ($gcode==1 && $cbcode==0) {


$sql_os ="SELECT SUM(CASE WHEN t.trans_type='CNTR' AND (t.db_account=" . $acct_id . "  ) THEN t.trans_amount WHEN t.trans_type='RCPT' AND (t.db_account=" . $acct_id . "  or t.cr_account=" . $acct_id . "  ) THEN t.trans_amount ELSE 0 END)-SUM(CASE WHEN t.trans_type='CNTR' AND (t.cr_account=" . $acct_id . "  ) THEN t.trans_amount WHEN t.trans_type='PYMT' AND (t.db_account=" . $acct_id . "  or t.cr_account=" . $acct_id . "  ) THEN t.trans_amount ELSE 0 END) `outstand` FROM `transaction_tbl` t WHERE t.finyear=? and t.company_id=? and t.delflag=0";    
    # code...
}



elseif($gcode<>1) {
/*
$sql_os = "SELECT SUM(CASE WHEN t.trans_type='SALE' AND (t.db_account=" . $acct_id . " or t.cr_account=" . $acct_id . " )  THEN net_amount ELSE 0 END)-SUM(CASE WHEN t.trans_type='PURC' AND (t.db_account=" . $acct_id . "  or t.cr_account=" . $acct_id . " )  THEN t.net_amount ELSE 0 END) `outstand` FROM `transaction_tbl` t WHERE t.finyear=? and t.company_id=?  and t.delflag=0";
*/

$sql_os = "SELECT SUM(CASE WHEN t.trans_type='SALE' AND (t.db_account=" . $acct_id . " or t.cr_account=" . $acct_id . " )  THEN net_amount WHEN t.trans_type='CNTR' AND (t.cr_account=" . $acct_id . " ) THEN t.trans_amount  WHEN t.trans_type='PYMT' AND (t.db_account=" . $acct_id . "  or t.cr_account=" . $acct_id . " ) THEN t.trans_amount ELSE 0 END)-SUM(CASE WHEN t.trans_type='PURC' AND (t.db_account=" . $acct_id . "  or t.cr_account=" . $acct_id . " )  THEN t.net_amount  WHEN t.trans_type='CNTR' AND (t.db_account=" . $acct_id . " ) THEN t.trans_amount WHEN t.trans_type='RCPT' AND (t.db_account=" . $acct_id . "  or t.cr_account=" . $acct_id . " ) THEN t.trans_amount ELSE 0 END) `outstand` FROM `transaction_tbl` t WHERE t.finyear=? and t.company_id=?  and t.delflag=0";



}

    $query=$this->db->query($sql_os,array($finyear,$cid));
/*
$sql_os = "SELECT (CASE WHEN  l.ldger_id=".$acct_id." THEN l.open_bal ELSE 0 END) + SUM(CASE WHEN t.trans_type='SALE' AND (t.db_account=" . $acct_id . " or t.cr_account=" . $acct_id . " )  THEN net_amount WHEN t.trans_type='PYMT' AND (t.db_account=" . $acct_id . "  or t.cr_account=" . $acct_id . " ) THEN t.trans_amount ELSE 0 END)-SUM(CASE WHEN t.trans_type='PURC' AND (t.db_account=" . $acct_id . "  or t.cr_account=" . $acct_id . " )  THEN t.net_amount WHEN t.trans_type='RCPT' AND (t.db_account=" . $acct_id . "  or t.cr_account=" . $acct_id . " ) THEN t.trans_amount ELSE 0 END) `outstand` FROM `transaction_tbl` t, openingbalance_tbl l WHERE  l.ldger_id=" . $acct_id . " AND t.finyear=? and t.company_id=?  and l.delflag=0 and t.delflag=0";
*/

 //   $query=$this->db->query($sql_os,array($finyear,$cid));
    return $query->result_array();



}



public function clientwisemsalesdataos($acct_id,$finyear,$cid)
{
$sql_os = "SELECT (CASE WHEN  l.ldger_id=".$acct_id." THEN l.open_bal ELSE 0 END) + SUM(CASE WHEN t.trans_type='SALE' AND (t.db_account=" . $acct_id . " or t.cr_account=" . $acct_id . " )  THEN net_amount WHEN t.trans_type='PYMT' AND (t.db_account=" . $acct_id . "  or t.cr_account=" . $acct_id . " ) THEN t.trans_amount ELSE 0 END)-SUM(CASE WHEN t.trans_type='PURC' AND (t.db_account=" . $acct_id . "  or t.cr_account=" . $acct_id . " )  THEN t.net_amount WHEN t.trans_type='RCPT' AND (t.db_account=" . $acct_id . "  or t.cr_account=" . $acct_id . " ) THEN t.trans_amount ELSE 0 END) `outstand` FROM `transaction_tbl` t, openingbalance_tbl l WHERE  l.ldger_id=" . $acct_id . " AND t.finyear=? and t.company_id=?  and l.delflag=0 and t.delflag=0";
 	$query=$this->db->query($sql_os,array($finyear,$cid));
 	return $query->result_array();



}


public function clientwisemsalescode($cid,$finyear,$transtype)
{
$sql="SELECT db_account FROM transaction_tbl WHERE trans_type=? AND finyear=? and company_id=? and delflag=0 group by db_account order by db_account";
 	$query=$this->db->query($sql,array($transtype,$finyear,$cid));
 	return $query->result_array();

}





public function getcbOp($cid,$finyear,$fdate,$actid)
{
    $opQuery="SELECT (SUM(CASE WHEN (t.db_account=? and t.trans_type='RCPT') or (t.db_account=? and t.trans_type='CNTR') THEN t.trans_amount ELSE '0' END)) - (SUM(CASE WHEN (t.db_account=? and t.trans_type='PYMT') or (t.cr_account=? and t.trans_type='CNTR')  THEN t.trans_amount ELSE '0' END)) `opbal` FROM transaction_tbl t,ledgermaster_tbl l WHERE t.delflag=0 and t.company_id=? and t.finyear=? and t.trans_date<? and (t.db_account=? or t.cr_account=?) and t.db_account=l.id";

$query = $this->db->query($opQuery, array($actid,$actid,$actid,$actid,$cid,$finyear,$fdate,$actid,$actid));
//$this->output->enable_profiler(TRUE); 
return $query->result_array();

}


//$gldata_op = $this->data_model->getglop($cid,$finyear,$sdate,$start_date,$actid);
public function getglop($cid,$finyear,$fdate,$actid)
{
$opQuery ="SELECT (SUM(CASE WHEN t.trans_type='SALE' THEN t.net_amount ELSE '0' END) + SUM(CASE WHEN t.trans_type='PYMT' THEN t.trans_amount ELSE '0' END)) - (SUM(CASE WHEN t.trans_type='PURC' THEN t.net_amount ELSE '0' END)+SUM(CASE WHEN t.trans_type='RCPT' THEN t.trans_amount ELSE '0' END)) `opbal` FROM transaction_tbl t,ledgermaster_tbl l WHERE t.delflag=0 and t.company_id=? and t.finyear=? and t.trans_date<? and (t.db_account=? or t.cr_account=?) and t.db_account=l.id and t.delflag=0";

$query = $this->db->query($opQuery, array($cid,$finyear,$fdate,$actid,$actid));
//$this->output->enable_profiler(TRUE); 
return $query->result_array();

}

public function getgltransaction($cid,$finyear,$fdate,$tdate,$actid)
{
$sql ="SELECT * FROM transaction_tbl t WHERE t.delflag=0 and t.company_id=? and t.finyear=? and t.trans_date>=? and t.trans_date<=? and (t.db_account=? or t.cr_account=?) order by t.trans_date";

$query = $this->db->query($sql, array($cid,$finyear,$fdate,$tdate,$actid,$actid));
return $query->result_array();

}

public function getSumSP($trid=null)
{
	$sql="SELECT count(trans_link_id)`noi`,sum(taxable_amount) `txb_amt`, sum(nett_amount)`net_amt` FROM itemtransaction_tbl where delflag=0 and trans_link_id=?";
$query = $this->db->query($sql, array($trid));
return $query->result_array();

}





public function getcmtData($cid=null,$finyear=null,$trans_type=null,$start_date=null,$end_date=null)
{

$sql = "SELECT sum(taxable_amount) taxable_tot,sum(igst_amount)+sum(cgst_amount)+sum(sgst_amount) gst_tot, sum(nett_amount) netamount_tot FROM itemtransaction_tbl  WHERE delflag=0 and finyear=? and company_id=? and trans_type=? and trans_date>=? and trans_date<=?";


$query = $this->db->query($sql,array($finyear,$cid,$trans_type,$start_date,$end_date));
return $query->result_array();

}

public function updateTrans()
{
 $upd_sql = "UPDATE transaction_tbl
                    SET
                        
                        trans_date = :trans_date, 
                        order_no = :order_no, 
                        order_date = :order_date, 
                        dc_no = :dc_no,
                        dc_date = :dc_date, 
                        trans_type = :trans_type,
                        db_account = :db_account,
                        cr_account = :cr_account,
                        statecode = :statecode,
                        gstin = :gstin,
                        inv_type=:invtype,
                        salebyperson = :salebyperson,
                        trans_amount =:trans_amount,
                        net_amount =:net_amount
                         WHERE 
                        id =:id";



}


public function setdelflagopbal($cid=null,$finyear=null)
{

  $upd = array('delflag' =>"1");
  $this->db->where('company_id',$cid);
  $this->db->where('finyear',$finyear);
$sts=  $this->db->update('openingbalance_tbl',$upd);


}


public function getmonwisegstData($cid=null,$finyear=null,$trans_type=null)
{
    $sql="SELECT SUM(case WHEN month(trans_date)='04' THEN (igst_amount+cgst_amount+sgst_amount) ELSE 0 END)`apr`,SUM(case WHEN month(trans_date)='05' THEN  (igst_amount+cgst_amount+sgst_amount)  ELSE 0 END)`may`,SUM(case WHEN month(trans_date)='06' THEN  (igst_amount+cgst_amount+sgst_amount)  ELSE 0 END)`jun`,SUM(case WHEN month(trans_date)='07' THEN  (igst_amount+cgst_amount+sgst_amount)  ELSE 0 END)`jul`,SUM(case WHEN month(trans_date)='08' THEN  (igst_amount+cgst_amount+sgst_amount)  ELSE 0 END)`aug`,SUM(case WHEN month(trans_date)='09' THEN  (igst_amount+cgst_amount+sgst_amount)  ELSE 0 END)`sep`,SUM(case WHEN month(trans_date)='10' THEN  (igst_amount+cgst_amount+sgst_amount)  ELSE 0 END)`oct`,SUM(case WHEN month(trans_date)='11' THEN  (igst_amount+cgst_amount+sgst_amount)  ELSE 0 END)`nov`,SUM(case WHEN month(trans_date)='12' THEN  (igst_amount+cgst_amount+sgst_amount)  ELSE 0 END)`dec`,SUM(case WHEN month(trans_date)='01' THEN  (igst_amount+cgst_amount+sgst_amount)  ELSE 0 END)`jan`,SUM(case WHEN month(trans_date)='02' THEN  (igst_amount+cgst_amount+sgst_amount)  ELSE 0 END)`feb`,SUM(case WHEN month(trans_date)='03' THEN  (igst_amount+cgst_amount+sgst_amount)  ELSE 0 END)`mar` FROM itemtransaction_tbl WHERE delflag=0 AND company_id=? AND finyear=? AND trans_type=?";


$query = $this->db->query($sql,array($cid,$finyear,$trans_type));
return $query->result_array();
}


public function getTaxesDatabyid($cid,$invno)
{
//    $taxdata=array();
 $sql="SELECT item_gstpc,sum(taxable_amount) AS `taxable_amount`, sum(cgst_amount) AS `item_cgst`,sum(sgst_amount) AS `item_sgst`,sum(igst_amount) AS `item_igst` FROM itemtransaction_tbl   WHERE delflag=0 and trans_link_id=? and company_id=? GROUP by item_gstpc";
$query = $this->db->query($sql,array($invno,$cid));
return $query->result_array();

}

public function getmonwiseData($cid=null,$finyear=null,$trans_type=null)
{
    $sql="SELECT SUM(case WHEN month(trans_date)='04' THEN taxable_amount ELSE 0 END)`apr`,SUM(case WHEN month(trans_date)='05' THEN taxable_amount ELSE 0 END)`may`,SUM(case WHEN month(trans_date)='06' THEN taxable_amount ELSE 0 END)`jun`,SUM(case WHEN month(trans_date)='07' THEN taxable_amount ELSE 0 END)`jul`,SUM(case WHEN month(trans_date)='08' THEN taxable_amount ELSE 0 END)`aug`,SUM(case WHEN month(trans_date)='09' THEN taxable_amount ELSE 0 END)`sep`,SUM(case WHEN month(trans_date)='10' THEN taxable_amount ELSE 0 END)`oct`,SUM(case WHEN month(trans_date)='11' THEN taxable_amount ELSE 0 END)`nov`,SUM(case WHEN month(trans_date)='12' THEN taxable_amount ELSE 0 END)`dec`,SUM(case WHEN month(trans_date)='01' THEN taxable_amount ELSE 0 END)`jan`,SUM(case WHEN month(trans_date)='02' THEN taxable_amount ELSE 0 END)`feb`,SUM(case WHEN month(trans_date)='03' THEN taxable_amount ELSE 0 END)`mar` FROM itemtransaction_tbl WHERE delflag=0 AND company_id=? AND finyear=? AND trans_type=?";

$query = $this->db->query($sql,array($cid,$finyear,$trans_type));
return $query->result_array();


}

public function getSalesPurchaseDatabyId($id=null,$qry=null)
{
	   if(!empty($id)){
	$sql="SELECT t.id, t.trans_id,t.trans_date,t.order_date,t.order_no,t.dc_no,t.dc_date,t.trans_type,t.db_account,t.cr_account,t.statecode,t.gstin,t.inv_type,t.rcm,t.net_amount,t.trans_amount,t.trans_reference,t.trans_narration,t.salebyperson,t.finyear,l.account_name`custname` FROM transaction_tbl t,ledgermaster_tbl l where  (t.db_account=l.id or t.db_account=0) and t.id=? AND t.trans_type=? and t.delflag=0 GROUP BY t.id";


/*	$sql="SELECT t.id, t.trans_id,t.trans_date,t.order_date,t.order_no,t.dc_no,t.dc_date,t.trans_type,t.db_account,t.cr_account,t.statecode,t.gstin,t.inv_type,t.rcm,t.net_amount,t.trans_amount,t.trans_reference,t.trans_narration,t.salebyperson,t.finyear,l.account_name`custname`,sum(itm.taxable_amount)`txb_amt`,sum(itm.nett_amount)`net_amt` FROM transaction_tbl t,ledgermaster_tbl l,itemtransaction_tbl itm where t.id=itm.trans_link_id and t.db_account=l.id and itm.delflag=0 and t.id=? and t.trans_type=? GROUP BY t.id";
*/
$query = $this->db->query($sql, array($id,$qry));
return $query->result_array();
}
    

}



public function getTransactionDatabyId($id=null)
{
       if(!empty($id)){
    $sql="SELECT t.id, t.trans_id,t.trans_date,t.order_date,t.order_no,t.dc_no,t.dc_date,t.trans_type,t.db_account,t.cr_account,t.statecode,t.gstin,t.inv_type,t.rcm,t.net_amount,t.trans_amount,t.trans_reference,t.trans_narration,t.salebyperson,t.finyear,l.account_name`custname` FROM transaction_tbl t,ledgermaster_tbl l where  t.db_account=l.id and t.id=? AND t.delflag=0 GROUP BY t.id";


/*  $sql="SELECT t.id, t.trans_id,t.trans_date,t.order_date,t.order_no,t.dc_no,t.dc_date,t.trans_type,t.db_account,t.cr_account,t.statecode,t.gstin,t.inv_type,t.rcm,t.net_amount,t.trans_amount,t.trans_reference,t.trans_narration,t.salebyperson,t.finyear,l.account_name`custname`,sum(itm.taxable_amount)`txb_amt`,sum(itm.nett_amount)`net_amt` FROM transaction_tbl t,ledgermaster_tbl l,itemtransaction_tbl itm where t.id=itm.trans_link_id and t.db_account=l.id and itm.delflag=0 and t.id=? and t.trans_type=? GROUP BY t.id";
*/
$query = $this->db->query($sql, array($id));
return $query->result_array();
}
    

}


public function updSettings($cid=null,$finyear=null,$next_no=null,$trans_type=null)
{
	//var_dump($cid . $finyear . $next_no . $trans_type );
if($trans_type=="SALE")
{


  $upd = array('inv_no' =>$next_no);
  $this->db->where('company_id',$cid);
  $this->db->where('finyear',$finyear);
$sts=  $this->db->update('settings_tbl',$upd);

}

elseif ($trans_type=="RCPT") {
	# code...

  $upd = array('receipt_no' =>$next_no);
  $this->db->where('company_id',$cid);
  $this->db->where('finyear',$finyear);
$sts=  $this->db->update('settings_tbl',$upd);

}

elseif ($trans_type=="PYMT") {
	# code...

  $upd = array('payment_no' =>$next_no);
  $this->db->where('company_id',$cid);
  $this->db->where('finyear',$finyear);
$sts=  $this->db->update('settings_tbl',$upd);

}

elseif ($trans_type=="JRNL") {
    # code...

  $upd = array('journal_no' =>$next_no);
  $this->db->where('company_id',$cid);
  $this->db->where('finyear',$finyear);
$sts=  $this->db->update('settings_tbl',$upd);

}


return $sts;
}


public function getSalesPurchaseItems($id=null,$qry=null)
{
	   if(!empty($id)){
$sql="SELECT * FROM itemtransaction_tbl where delflag=0 and trans_link_id=? and trans_type=?";

$query = $this->db->query($sql, array($id,$qry));
}
    
    return $query->result_array();

}



public function getChartData($finyear=null,$compid=null,$transtype=null)
{
	   $sql ='SELECT SUM(CASE WHEN month(trans_date)="01" THEN trans_amount ELSE 0 END)`JAN`,SUM(CASE WHEN month(trans_date)="02" THEN trans_amount ELSE 0 END)`FEB`,SUM(CASE WHEN month(trans_date)="03" THEN trans_amount ELSE 0 END)`MAR`,SUM(CASE WHEN month(trans_date)="04" THEN trans_amount ELSE 0 END)`APR`,SUM(CASE WHEN month(trans_date)="05" THEN trans_amount ELSE 0 END)`MAY`,SUM(CASE WHEN month(trans_date)="06" THEN trans_amount ELSE 0 END)`JUN`,SUM(CASE WHEN month(trans_date)="07" THEN trans_amount ELSE 0 END)`JUL`,SUM(CASE WHEN month(trans_date)="08" THEN trans_amount ELSE 0 END)`AUG`,SUM(CASE WHEN month(trans_date)="09" THEN trans_amount ELSE 0 END)`SEP`,SUM(CASE WHEN month(trans_date)="10" THEN trans_amount ELSE 0 END)`OCT`,SUM(CASE WHEN month(trans_date)="11" THEN trans_amount ELSE 0 END)`NOV`,SUM(CASE WHEN month(trans_date)="12" THEN trans_amount ELSE 0 END)`DEC` FROM  transaction_tbl  WHERE delflag=0 and finyear=? and company_id=? and trans_type=?';

$query = $this->db->query($sql, array($finyear,$compid,$transtype));

return $query->result_array();
 
}

public function getpiChartData($finyear=null,$compid=null)
{
	$sql ='SELECT s.sales_person,sum(it.trans_amount) `tot_rev` FROM  transaction_tbl it, salesperson_tbl s WHERE s.delflag=0 and it.finyear=? and it.company_id=? and it.trans_type="SALE" and it.salebyperson=s.id group by it.salebyperson';
$query = $this->db->query($sql, array($finyear,$compid));

return $query->result_array();



}

public function getall_ledgers()
{
    $sql="SELECT * from ledgermaster_tbl where delflag=0 and predefined=0 order by id";
    $query=$this->db->query($sql);
    return $query->result_array();
}


public function getLedgerbyId($actid)
{
 if($actid)
 {
    $sql="SELECT * FROM ledgermaster_tbl where id=? order by id";
    $query=$this->db->query($sql,array($actid));
    return $query->result_array();
 }
}


public function getopbalDatabyid($compid=null,$finyear=null,$lid=null)
{
      
$sql="SELECT * from openingbalance_tbl where delflag=0 and finyear=? and company_id=? AND ldger_id=? order by ldger_id";

            $query = $this->db->query($sql, array($finyear,$compid,$lid));
        //    $data = $this->db->get("ledgermaster_tbl")->result();
        return $query->result_array();

}

public function getopbalData($compid=null,$finyear=null)
{
      
$sql="SELECT * from openingbalance_tbl where delflag=0 and finyear=? and company_id=? order by cur_timestamp desc";

            $query = $this->db->query($sql, array($finyear,$compid));
        //    $data = $this->db->get("ledgermaster_tbl")->result();
        return $query->result_array();

}



public function getLedgerbyidbycid($cid=null,$id=null)
{
    $sql="SELECT * from ledgermaster_tbl where company_id=? and id=? and delflag=0 and predefined=0 order by id";
    $query=$this->db->query($sql,array($cid,$id));
    return $query->result_array();

}



public function getmonwiseITCData($finyear=null,$compid=null)
{
	 $sql="SELECT SUM(case WHEN month(trans_date)='04' AND trans_type='PURC' THEN (igst_amount+cgst_amount+sgst_amount) ELSE 0 END)-SUM(case WHEN month(trans_date)='04' AND trans_type='SALE' THEN (igst_amount+cgst_amount+sgst_amount) ELSE 0 END) `apr`,SUM(case WHEN month(trans_date)='05' AND trans_type='PURC' THEN  (igst_amount+cgst_amount+sgst_amount)  ELSE 0 END)-SUM(case WHEN month(trans_date)='05' AND trans_type='SALE' THEN  (igst_amount+cgst_amount+sgst_amount)  ELSE 0 END)`may`,SUM(case WHEN month(trans_date)='06' AND trans_type='PURC' THEN  (igst_amount+cgst_amount+sgst_amount)  ELSE 0 END)-SUM(case WHEN month(trans_date)='06' AND trans_type='SALE' THEN  (igst_amount+cgst_amount+sgst_amount)  ELSE 0 END)`jun`,SUM(case WHEN month(trans_date)='07' AND trans_type='PURC' THEN  (igst_amount+cgst_amount+sgst_amount)  ELSE 0 END)-SUM(case WHEN month(trans_date)='07' AND trans_type='SALE' THEN  (igst_amount+cgst_amount+sgst_amount)  ELSE 0 END)`jul`,SUM(case WHEN month(trans_date)='08' AND trans_type='PURC' THEN  (igst_amount+cgst_amount+sgst_amount)  ELSE 0 END)-SUM(case WHEN month(trans_date)='08' AND trans_type='SALE' THEN  (igst_amount+cgst_amount+sgst_amount)  ELSE 0 END)`aug`,SUM(case WHEN month(trans_date)='09' AND trans_type='PURC' THEN  (igst_amount+cgst_amount+sgst_amount)  ELSE 0 END)-SUM(case WHEN month(trans_date)='09' AND trans_type='SALE' THEN  (igst_amount+cgst_amount+sgst_amount)  ELSE 0 END)`sep`,SUM(case WHEN month(trans_date)='10' AND trans_type='PURC' THEN  (igst_amount+cgst_amount+sgst_amount)  ELSE 0 END)-SUM(case WHEN month(trans_date)='10' AND trans_type='SALE' THEN  (igst_amount+cgst_amount+sgst_amount)  ELSE 0 END)`oct`,SUM(case WHEN month(trans_date)='11' AND trans_type='PURC' THEN  (igst_amount+cgst_amount+sgst_amount)  ELSE 0 END)-SUM(case WHEN month(trans_date)='11' AND trans_type='SALE' THEN  (igst_amount+cgst_amount+sgst_amount)  ELSE 0 END)`nov`,SUM(case WHEN month(trans_date)='12' AND trans_type='PURC' THEN  (igst_amount+cgst_amount+sgst_amount)  ELSE 0 END)-SUM(case WHEN month(trans_date)='12' AND trans_type='SALE' THEN  (igst_amount+cgst_amount+sgst_amount)  ELSE 0 END)`dec`,SUM(case WHEN month(trans_date)='01' AND trans_type='PURC' THEN  (igst_amount+cgst_amount+sgst_amount)  ELSE 0 END)-SUM(case WHEN month(trans_date)='01' AND trans_type='SALE' THEN  (igst_amount+cgst_amount+sgst_amount)  ELSE 0 END)`jan`,SUM(case WHEN month(trans_date)='02' AND trans_type='PURC' THEN  (igst_amount+cgst_amount+sgst_amount)  ELSE 0 END)-SUM(case WHEN month(trans_date)='02' AND trans_type='SALE' THEN  (igst_amount+cgst_amount+sgst_amount)  ELSE 0 END)`feb`,SUM(case WHEN month(trans_date)='03' AND trans_type='PURC' THEN  (igst_amount+cgst_amount+sgst_amount)  ELSE 0 END)-SUM(case WHEN month(trans_date)='03' AND trans_type='SALE' THEN  (igst_amount+cgst_amount+sgst_amount)  ELSE 0 END)`mar` FROM itemtransaction_tbl WHERE delflag=0 AND company_id=? AND finyear=?";

$query = $this->db->query($sql, array($compid,$finyear));

return $query->result_array();

}




}
