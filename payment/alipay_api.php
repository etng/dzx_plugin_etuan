<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class AlipayService {

	var $parameter;
	var $alipay_gateway;
    var $api_type=1;
    var $partner='';
    var $account='';
    var $key='';

	function AlipayService($config) {
        global $_G;
        foreach($config as $k=>$v)
        {
            if(strpos($k, 'alipay_')===0)
            {
                $k = substr($k, strlen('alipay_'));
            }
            $this->$k = $v;
        }
		$this->alipay_gateway = 'https://mapi.alipay.com/gateway.do?';
		$this->parameter = array();

		if($this->api_type == 1){
			$this->parameter['service'] = "create_direct_pay_by_user";
			$this->parameter['paymethod'] = "directPay";
		}else if($this->api_type == 2){
			$this->parameter['service'] = "create_partner_trade_by_buyer";
			$this->parameter['quantity'] = 1;
			$this->parameter['logistics_fee'] = '0.00';
			$this->parameter['logistics_type'] = 'EXPRESS';
			$this->parameter['logistics_payment'] = 'SELLER_PAY';
		}else if($this->api_type == 3){
			$this->parameter['service'] = "trade_create_by_buyer";
			$this->parameter['quantity'] = 1;

			$this->parameter['logistics_fee'] = '0.00';
			$this->parameter['logistics_type'] = 'EXPRESS';
			$this->parameter['logistics_payment'] = 'SELLER_PAY';
		}

		$this->parameter['_input_charset'] = 'UTF8';
		$this->parameter['payment_type'] = "1";
		$this->parameter['partner'] = $this->partner;
		$this->parameter['seller_email'] = $this->account;
		$this->parameter['return_url'] = $_G['siteurl']."source/plugin/etuan/payment/alipay_return.php";
		$this->parameter['notify_url'] = $_G['siteurl']."source/plugin/etuan/payment/alipay_notify.php";
    }
	function createPayURL($orderid, $subject, $totalmoney, $show_url){
		$this->parameter['out_trade_no'] = $orderid;
		$this->parameter['subject'] = $subject;
		$this->parameter['body'] = $subject;
        $this->parameter['price'] = $totalmoney;
        $this->parameter['show_url'] = $show_url;
		$payurl = $sign = '';
		$args = $this->parameter;
		ksort($args);
		while (list ($key, $val) = each ($args)) {
			$sign .= '&'.$key.'='.$val;
			$payurl.= $key.'='.rawurlencode($val).'&';
        }
		$sign = substr($sign, 1);
		$sign = md5($sign.$this->key);

		$payurl = $this->alipay_gateway.$payurl.'sign='.$sign.'&sign_type=MD5';

		return $payurl;
	}

	function doSend($orderid, $subject, $totalmoney, $show_url) {
		header("Location:" . $this->createPayURL($orderid, $subject, $totalmoney, $show_url));
		exit;
	}

}

class AlipayNotify {

	var $http_verify_url;
    var $api_type=1;
    var $partner='';
    var $account='';
    var $key='';
    function AlipayNotify($config) {
        foreach($config as $k=>$v)
        {
            if(strpos($k, 'alipay_')===0)
            {
                $k = substr($k, strlen('alipay_'));
            }
            $this->$k = $v;
        }		
		$this->http_verify_url = 'http://notify.alipay.com/trade/notify_query.do?';
    }

	function verifyNotify(){
		if(empty($_POST)) {
			return false;
		}else {
			$mysign = $this->getMysign($_POST);
			$responseTxt = 'true';
			if (! empty($_POST["notify_id"])) {$responseTxt = $this->getResponse($_POST["notify_id"]);}

			if (preg_match("/true$/i",$responseTxt) && $mysign == $_POST["sign"]) {
				return true;
			} else {
				return false;
			}
		}
	}

	function verifyReturn(){
		if(empty($_GET)) {
			return false;
		}
		else {
			$mysign = $this->getMysign($_GET);
			$responseTxt = 'true';
			if (! empty($_GET["notify_id"])) {
				$responseTxt = $this->getResponse($_GET["notify_id"]);
			}
			if (preg_match("/true$/i",$responseTxt) && $mysign == $_GET["sign"]) {
				return true;
			} else {
				return false;
			}
		}
	}

	function getMysign($para) {

		$args = array();
		$sign = '';
		while (list ($key, $val) = each ($para)) {
			if($key == "sign" || $key == "sign_type" || $val == ""){
				continue;
			}else{
				$args[$key] = $para[$key];
			}
		}
		ksort($args);
		while (list ($key, $val) = each ($args)) {
			$sign .= '&'.$key.'='.$val;
        }
		$sign = substr($sign, 1);
		$sign = md5($sign.$this->key);
		return $sign;
	}

	function getResponse($notify_id) {
		$veryfy_url = $this->http_verify_url."partner=" . $this->partener . "&notify_id=" . $notify_id;
		$responseTxt = $this->getHttpResponse($veryfy_url);

		return $responseTxt;
	}

	function getHttpResponse($url, $input_charset = '', $time_out = "60") {
		$urlarr     = parse_url($url);
		$errno      = "";
		$errstr     = "";
		$transports = "";
		$responseText = "";
		if($urlarr["scheme"] == "https") {
			$transports = "ssl://";
			$urlarr["port"] = "443";
		} else {
			$transports = "tcp://";
			$urlarr["port"] = "80";
		}
		$fp=@fsockopen($transports . $urlarr['host'],$urlarr['port'],$errno,$errstr,$time_out);
		if(!$fp) {
			die("ERROR: $errno - $errstr<br />\n");
		} else {
			if (trim($input_charset) == '') {
				fputs($fp, "POST ".$urlarr["path"]." HTTP/1.1\r\n");
			}
			else {
				fputs($fp, "POST ".$urlarr["path"].'?_input_charset='.$input_charset." HTTP/1.1\r\n");
			}
			fputs($fp, "Host: ".$urlarr["host"]."\r\n");
			fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
			fputs($fp, "Content-length: ".strlen($urlarr["query"])."\r\n");
			fputs($fp, "Connection: close\r\n\r\n");
			fputs($fp, $urlarr["query"] . "\r\n\r\n");
			while(!feof($fp)) {
				$responseText .= @fgets($fp, 1024);
			}
			fclose($fp);
			$responseText = trim(stristr($responseText,"\r\n\r\n"),"\r\n");

			return $responseText;
		}
	}
}

?>