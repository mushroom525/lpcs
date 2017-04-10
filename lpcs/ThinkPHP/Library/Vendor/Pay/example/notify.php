<?php
ini_set('date.timezone','Asia/Shanghai');
error_reporting(E_ERROR);

require_once "../lib/WxPay.Api.php";
require_once '../lib/WxPay.Notify.php';
require_once 'log.php';

//初始化日志
$logHandler= new CLogFileHandler("../logs/".date('Y-m-d').'.log');
$log = Log::Init($logHandler, 15);

class PayNotifyCallBack extends WxPayNotify
{
	//查询订单
	public function Queryorder($transaction_id)
	{
		$input = new WxPayOrderQuery();
		$input->SetTransaction_id($transaction_id);
		$result = WxPayApi::orderQuery($input);
		Log::DEBUG("query:" . json_encode($result));
		if(array_key_exists("return_code", $result)
			&& array_key_exists("result_code", $result)
			&& $result["return_code"] == "SUCCESS"
			&& $result["result_code"] == "SUCCESS")
		{
			return true;
		}
		return false;
	}

	//重写回调处理函数
	public function NotifyProcess($data, &$msg)
	{
		Log::DEBUG("call back:" . json_encode($data));
		$notfiyOutput = array();

		if(!array_key_exists("transaction_id", $data)){
			$msg = "输入参数不正确";
			return false;
		}
		//查询订单，判断订单真实性
		if(!$this->Queryorder($data["transaction_id"])){
			$msg = "订单查询失败";
			return false;
		}
		if($data["result_code"]=="SUCCESS" && $data["return_code"]=="SUCCESS"){
            $order_id=$data['out_trade_no'];
            $wx_orderid=$data['transaction_id'];
            $order_step=1;
            $pay_time=date('Y-m-d H:i:s', time());
            $openid=$data['openid'];
		    $db_connect=new mysqli('hdm174585329.my3w.com','hdm174585329','cxi2a12c','hdm174585329_db');
            $sql="select order_step from `lp_order` where `order_id`='$order_id'";
            $result=$db_connect->query($sql);
            while ($row = mysqli_fetch_assoc($result))
            {
                $step=$row['order_step'];
            }
            if($step==0){
                $updatesql="update `lp_order` set `order_step`='$order_step',`wx_orderid`='$wx_orderid',`pay_time`='$pay_time' where `order_id`='$order_id'";
                $updatestep=$db_connect->query($updatesql);
                if($updatestep){
                    $delsql="delete from `lp_cart` where `user_openid`='$openid'";
                    $db_connect->query($delsql);
                }
            }
        }
		return true;
	}
    public function xmlToArray($xml)
    {
        //将XML转为array
        $array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $array_data;
    }
}

Log::DEBUG("begin notify");
$notify = new PayNotifyCallBack();
$notify->Handle(false);
