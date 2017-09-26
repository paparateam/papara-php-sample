<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Üye İşyeri</title>
<link href="css/style.css" rel="stylesheet" type="text/css" />
</head>
<body>

<h1 align="center">Toplu Ödeme </h1>
<div style="width:400px; margin:0 auto;">
<a class="button" style="float:left" href="index.php">Mağaza</a>
<a class="button" style="margin-left:20px;"  href="ipn.php">Siparişler</a>
<a class="button" style="float:right" href="masspayment.php">Ödeme Al</a>
</div>
<hr style="width:720px; margin-top:20px; height:2px;border:none;color:#333;background-color:#333;">

<form method="post" action="masspayment.php">
<div style="width:450px; margin:0 auto;">
    <p style="text-align:center; margin-bottom:30px">Papara Demo üye işyerinden ödeme almak için bu sayfayı kullanabilirsiniz. 1 ile 100 TL arasında rastgele bir tutar gönderilecektir. Papara numarası 10 haneli olmalıdır. Başına PL eklerseniz de olur.</p>
    <p style="text-align:center;margin-bottom:20px" id="error_field"></p>
    <button type="submit" style="float:right;margin-left:45px;">Ödeme al</button>
    <label style="position:fixed;vertical-align:middle;margin-top:7px"for="masspayment">Papara Numarası</label>
    <input style="line-height:30px;float:right"type="text" size="20" maxlength="12" name="masspayment" value="PL" />
    
</div>

</form>
</body>
</html>

<?php if (isset($_POST['masspayment'])) {
    $paparaID = $_POST['masspayment'];
    $api_key = 'So1SUG9N6O/s9afEz6gxL6TENlGxSs1JiE6DFj5ScLsvJtjKVAb9xd7Fxmmq/9K5epPYe/UHl8A5q6UV1WRLtw==';
    $environment_url = 'https://merchantapi-test-master.papara.com/masspayment';
    
    $json_decoded = json_decode(file_get_contents('json/information.json'),true);
	$json_decoded['massPaymentId'] = $json_decoded['massPaymentId'] + 1;
	$json_encoded = json_encode($json_decoded);
	$info_file = fopen('json/information.json','w');
    fwrite($info_file,$json_encoded);
    $massPaymentId = $json_decoded['massPaymentId'];
    $random_payment = rand(1,100);

	$payload = array(
		'accountNumber' => "$paparaID",
		'amount' => $random_payment,
		'massPaymentId' => "$massPaymentId"
    );
    
    $curl = curl_init($environment_url);
	curl_setopt_array($curl, array(
			CURLOPT_POST => true,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPHEADER => array(
			'ApiKey: '.$api_key,
			'Content-Type: application/json'
			),
			CURLOPT_POSTFIELDS => json_encode($payload)
		));
    $response = curl_exec($curl);

	if ($response == false) {
		die(curl_error($curl));
	}
    $response_data = json_decode($response, true);
    curl_close($curl);

    if ($response_data['succeeded'] == true) {
        echo "<script> 
            document.getElementById('error_field').innerHTML = 'İşlem başarıyla gerçekleştirildi.';".
            "document.getElementById('error_field').style.color = 'green';" .
        "</script>";
    } else {
        echo "<script>
            document.getElementById('error_field').innerHTML =". '"'. 'Error: '.$response_data['error']['message'] . '";' .
            "document.getElementById('error_field').style.color = 'red';" .
        "</script>";
    }
}