<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class RequestHandler {

	var $gateUrl;
	var $key;
	var $parameters;
	var $debugInfo;

	function __construct() {
		$this->RequestHandler();
	}

	function RequestHandler() {
		$this->gateUrl = "http://service.tenpay.com/cgi-bin/v3.0/payservice.cgi";
		$this->key = "";
		$this->parameters = array();
		$this->debugInfo = "";
	}

	function init() {

	}

	function getGateURL() {
		return $this->gateUrl;
	}

	function setGateURL($gateUrl) {
		$this->gateUrl = $gateUrl;
	}

	function getKey() {
		return $this->key;
	}

	function setKey($key) {
		$this->key = $key;
	}

	function getParameter($parameter) {
		return $this->parameters[$parameter];
	}

	function setParameter($parameter, $parameterValue) {
		$this->parameters[$parameter] = $parameterValue;
	}

	function getAllParameters() {
		return $this->parameters;
	}

	function getRequestURL() {

		$this->createSign();

		$reqPar = "";
		ksort($this->parameters);
		foreach($this->parameters as $k => $v) {
			$reqPar .= $k . "=" . urlencode($v) . "&";
		}
		$reqPar = substr($reqPar, 0, strlen($reqPar)-1);
		$requestURL = $this->getGateURL() . "?" . $reqPar;

		return $requestURL;

	}

	function getDebugInfo() {
		return $this->debugInfo;
	}

	function doSend() {
		header("Location:" . $this->getRequestURL());
		exit;
	}

	function createSign() {
		$signPars = "";
		ksort($this->parameters);
		foreach($this->parameters as $k => $v) {
			if("" != $v && "sign" != $k) {
				$signPars .= $k . "=" . $v . "&";
			}
		}
		$signPars .= "key=" . $this->getKey();
		$sign = strtolower(md5($signPars));
		$this->setParameter("sign", $sign);
		$this->_setDebugInfo($signPars . " => sign:" . $sign);

	}

	function _setDebugInfo($debugInfo) {
		$this->debugInfo = $debugInfo;
	}

}

class PayRequestHandler extends RequestHandler {

	function PayRequestHandler() {
		$this->setKey(TENPAY_KEY);
		$this->setGateURL("http://service.tenpay.com/cgi-bin/v3.0/payservice.cgi");
		$this->init();
	}

	function init() {
		global $_G, $totalmoney, $orderid, $subject;

		include_once DISCUZ_ROOT . './source/class/class_chinese.php';
		$chinese = new Chinese(strtoupper(CHARSET), 'GBK');

		$date = dgmdate(TIMESTAMP, 'Ymd');
		$suffix = dgmdate(TIMESTAMP, 'His').rand(1000, 9999);
		$transaction_id = TENPAY_PID.$date.$suffix;
		$this->setParameter("cmdno", "1");
		$this->setParameter("date",  date("Ymd"));
		$this->setParameter("bargainor_id", TENPAY_PID);
		$this->setParameter("transaction_id", $transaction_id);
		$this->setParameter("sp_billno", $orderid);
		$this->setParameter("total_fee", $totalmoney);
		$this->setParameter("fee_type", "1");
		$this->setParameter("return_url", $_G['siteurl'].'source/plugin/etuan/payment/tenpay_notify.php');
		$this->setParameter("attach", "");
		$this->setParameter("spbill_create_ip", $_G['clientip']);
		$this->setParameter("desc", $chinese->Convert($subject));
		$this->setParameter("bank_type", "0");
		$this->setParameter("cs", "gbk");
		$this->setParameter("sign", "");

	}

	function createSign() {
		$cmdno = $this->getParameter("cmdno");
		$date = $this->getParameter("date");
		$bargainor_id = $this->getParameter("bargainor_id");
		$transaction_id = $this->getParameter("transaction_id");
		$sp_billno = $this->getParameter("sp_billno");
		$total_fee = $this->getParameter("total_fee");
		$fee_type = $this->getParameter("fee_type");
		$return_url = $this->getParameter("return_url");
		$attach = $this->getParameter("attach");
		$spbill_create_ip = $this->getParameter("spbill_create_ip");
		$key = $this->getKey();
		$signPars = "cmdno=" . $cmdno . "&" .
				"date=" . $date . "&" .
				"bargainor_id=" . $bargainor_id . "&" .
				"transaction_id=" . $transaction_id . "&" .
				"sp_billno=" . $sp_billno . "&" .
				"total_fee=" . $total_fee . "&" .
				"fee_type=" . $fee_type . "&" .
				"return_url=" . $return_url . "&" .
				"attach=" . $attach . "&";

		if($spbill_create_ip != "") {
			$signPars .= "spbill_create_ip=" . $spbill_create_ip . "&";
		}
		$signPars .= "key=" . $key;
		$sign = strtolower(md5($signPars));
		$this->setParameter("sign", $sign);
		$this->_setDebugInfo($signPars . " => sign:" . $sign);

	}

}


class ResponseHandler  {

	var $key;
	var $parameters;
	var $debugInfo;

	function __construct() {
		$this->ResponseHandler();
	}

	function ResponseHandler() {
		$this->key = "";
		$this->parameters = array();
		$this->debugInfo = "";

		foreach($_GET as $k => $v) {
			$this->setParameter($k, $v);
		}

		foreach($_POST as $k => $v) {
			$this->setParameter($k, $v);
		}

	}

	function getKey() {
		return $this->key;
	}

	function setKey($key) {
		$this->key = $key;
	}

	function getParameter($parameter) {
		return $this->parameters[$parameter];
	}

	function setParameter($parameter, $parameterValue) {
		$this->parameters[$parameter] = $parameterValue;
	}

	function getAllParameters() {
		return $this->parameters;
	}

	function isTenpaySign() {
		$signPars = "";
		ksort($this->parameters);
		foreach($this->parameters as $k => $v) {
			if("sign" != $k && "" != $v) {
				$signPars .= $k . "=" . $v . "&";
			}
		}
		$signPars .= "key=" . $this->getKey();
		$sign = strtolower(md5($signPars));
		$tenpaySign = strtolower($this->getParameter("sign"));
		$this->_setDebugInfo($signPars . " => sign:" . $sign .
				" tenpaySign:" . $this->getParameter("sign"));

		return $sign == $tenpaySign;

	}

	function getDebugInfo() {
		return $this->debugInfo;
	}

	function doShow($show_url) {
		$strHtml = "<html><head>\r\n" .
			"<meta name=\"TENCENT_ONLINE_PAYMENT\" content=\"China TENCENT\">" .
			"<script language=\"javascript\">\r\n" .
				"window.location.href='" . $show_url . "';\r\n" .
			"</script>\r\n" .
			"</head><body></body></html>";

		echo $strHtml;

		exit;
	}

	function _isTenpaySign($signParameterArray) {

		$signPars = "";
		foreach($signParameterArray as $k) {
			$v = $this->getParameter($k);
			if("sign" != $k && "" != $v) {
				$signPars .= $k . "=" . $v . "&";
			}
		}
		$signPars .= "key=" . $this->getKey();
		$sign = strtolower(md5($signPars));
		$tenpaySign = strtolower($this->getParameter("sign"));
		$this->_setDebugInfo($signPars . " => sign:" . $sign .
				" tenpaySign:" . $this->getParameter("sign"));

		return $sign == $tenpaySign;


	}

	function _setDebugInfo($debugInfo) {
		$this->debugInfo = $debugInfo;
	}

}

class PayResponseHandler extends ResponseHandler {

	function isTenpaySign() {

		$this->setKey(TENPAY_KEY);

		$cmdno = $this->getParameter("cmdno");
		$pay_result = $this->getParameter("pay_result");
		$date = $this->getParameter("date");
		$transaction_id = $this->getParameter("transaction_id");
		$sp_billno = $this->getParameter("sp_billno");
		$total_fee = $this->getParameter("total_fee");
		$fee_type = $this->getParameter("fee_type");
		$attach = $this->getParameter("attach");
		$key = $this->getKey();
		$signPars = "";
		$signPars = "cmdno=" . $cmdno . "&" .
				"pay_result=" . $pay_result . "&" .
				"date=" . $date . "&" .
				"transaction_id=" . $transaction_id . "&" .
				"sp_billno=" . $sp_billno . "&" .
				"total_fee=" . $total_fee . "&" .
				"fee_type=" . $fee_type . "&" .
				"attach=" . $attach . "&" .
				"key=" . $key;

		$sign = strtolower(md5($signPars));
		$tenpaySign = strtolower($this->getParameter("sign"));

		$this->_setDebugInfo($signPars . " => sign:" . $sign .
				" tenpaySign:" . $this->getParameter("sign"));

		return $sign == $tenpaySign;

	}

}

?>