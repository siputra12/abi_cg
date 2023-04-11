<?php
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['tx'])) {
    $tx = $_GET['tx'];
    $result = get_func($tx);
    if ($result) {
        $response = array('success' => true, 'result' => $result);
    } else {
        $response = array('success' => false, 'error' => 'Failed to get function data');
    }

    $callback = isset($_GET['callback']) ? $_GET['callback'] : '';
    if (!empty($callback)) {
        // Jika ada parameter 'callback' dalam URL, kirimkan response sebagai JSONP
        header('Content-Type: application/javascript');
        echo $callback . '(' . json_encode($response) . ')';
    } else {
        // Jika tidak ada parameter 'callback', kirimkan response sebagai JSON
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    exit();
}

function getStr($a, $b, $c){
	return @explode($b, @explode($a, $c)[1])[0];
}

function get_func($tx){
	$arr = array();
	$datas = @getStr('LitFunctionName = "', '"', @get_contents("https://bscscan.com/tx/$tx"));
	$pisah = @explode("(", $datas);
	$arr['func'] = $pisah[0];
	$raw = @explode(")", $pisah[1])[0];
	$raw2 = @explode(", ", $raw);
	for($a=0;$a<count($raw2);$a++){
		$pRaw = @explode(" ", $raw2[$a]);
		if(count($pRaw)<=1) break;
		$arr['data'][$a]['p'] = $pRaw[1];
		$arr['data'][$a]['t'] = $pRaw[0];
	}
	return $arr;
}

function get_contents($url){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 0);
	$headers = array();
	$headers[] = "Host: bscscan.com";
	$headers[] = "User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:109.0) Gecko/20100101 Firefox/111.0";
	$headers[] = "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8";
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	$res = curl_exec($ch);
	curl_close($ch);
	return $res;

}