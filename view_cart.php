<?php
session_start();
$shipping_cost      = 1.50; //shipping cost
$taxes              = array( //List your Taxes percent here.
                            'KDV' => 18,
                            'ÖTV' => 5
							);
$currency = '&#8378; '; //Currency Character or code
$current_url = urlencode($url="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
$_SESSION['paymentRecordCreated'] = false;
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>View shopping cart</title>
<link href="css/style.css" rel="stylesheet" type="text/css"></head>
<body>
<h1 align="center">Sepet</h1>
<div class="cart-view-table-back">
<form method="post" action="cart_update.php">
<table width="100%"  cellpadding="6" cellspacing="0"><thead><tr><th>Miktar</th><th>Ürün Adı</th><th>Fiyat</th><th>Toplam</th><th>Sil</th></tr></thead>
  <tbody>
 	<?php
	if(isset($_SESSION["cart_products"])) //check session var
    {
		$total = 0; //set initial total value
		$b = 0; //var for zebra stripe table
		foreach ($_SESSION["cart_products"] as $cart_itm)
        {
			//set variables to use in content below
			$product_name = $cart_itm["product_name"];
			$product_qty = $cart_itm["product_qty"];
			$product_price = $cart_itm["product_price"];
			$product_code = $cart_itm["product_code"];
			$product_color = $cart_itm["product_color"];
			$subtotal = ($product_price * $product_qty); //calculate Price x Qty

		   	$bg_color = ($b++%2==1) ? 'odd' : 'even'; //class for zebra stripe
		  echo '<tr class="'.$bg_color.'">';
			echo '<td><input type="text" size="2" maxlength="2" name="product_qty['.$product_code.']" value="'.$product_qty.'" /></td>';
			echo '<td>'.$product_name.'</td>';
			echo '<td>'.$product_price.$currency.'</td>';
			echo '<td>'.$subtotal.$currency.'</td>';
			echo '<td><input type="checkbox" name="remove_code[]" value="'.$product_code.'" /></td>';
            echo '</tr>';
			$total = ($total + $subtotal); //add subtotal to total var
        }

		$grand_total = $total + $shipping_cost; //grand total including shipping cost
		foreach($taxes as $key => $value){ //list and calculate all taxes in array
				$tax_amount     = $total * ($value / 100);
				$tax_item[$key] = $tax_amount;
				$grand_total    = $grand_total + $tax_amount;  //add tax values to grand total
		}
		$_SESSION['grand_total'] = $grand_total;
		$list_tax       = '';
		foreach($tax_item as $key => $value){ //List all taxes
			$list_tax .= $key. ' : '.sprintf("%01.2f", $value).$currency.'<br />';
		}
		$shipping_cost = ($shipping_cost)?'Kargo Ücreti : '. sprintf("%01.2f", $shipping_cost).$currency.'<br />':'';
	}
    ?>
    <tr><td colspan="5"><span style="float:right;text-align: right;"><?php echo $shipping_cost. $list_tax; ?>Toplam : <?php echo sprintf("%01.2f", $grand_total).$currency;?></span></td></tr>
    <tr><td colspan="5">
	<a class="button" href="view_cart.php?send=true">Papara ile Öde</a>
  	<a href="index.php" class="button">Alışverişe Devam Et</a>
  	<button type="submit">Sepeti Güncelle</button>
  	</td>
	</tr>
  </tbody>
</table>
<input type="hidden" name="return_url" value="<?php
$current_url = urlencode($url="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
echo $current_url; ?>" />
</form>
</div>
</body>
</html>

<?php
// make first payment record
if (isset($_GET['send']) && $_SESSION['paymentRecordCreated'] != true) {
	$working_key = 'So1SUG9N6O/s9afEz6gxL6TENlGxSs1JiE6DFj5ScLsvJtjKVAb9xd7Fxmmq/9K5epPYe/UHl8A5q6UV1WRLtw==';
	$environment_url = 'https://merchantapi-test-master.papara.com/payments';

	$json_decoded = json_decode(file_get_contents('json/information.json'),true);
	$json_decoded['order_id'] = $json_decoded['order_id'] + 1;
	$json_encoded = json_encode($json_decoded);
	$myfile = fopen('json/information.json','w');
	fwrite($myfile,$json_encoded);
	
	$notify_url = "http://".$_SERVER['HTTP_HOST']."/notification.php";
	$redirect_url = "http://".$_SERVER['HTTP_HOST']."/redirect.php";

	$description = '';
	foreach($_SESSION['cart_products'] as $value) {
		$description .= $value['product_name']."(".$value['product_qty'].")";
		if ( count($_SESSION['cart_products']) > 1 && $value != end($_SESSION['cart_products']) ) {
			$description .= ', ';
		}
	}

	$payload = array(
		'amount' => $grand_total,
		'referenceId' => $json_decoded['order_id'],
		'orderDescription' => $description,
		'notificationUrl'     => $notify_url,
		'redirectUrl'       => $redirect_url
	);

	$curl = curl_init($environment_url);
	curl_setopt_array($curl, array(
			CURLOPT_POST => true,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPHEADER => array(
			'ApiKey: '.$working_key,
			'Content-Type: application/json'
			),
			CURLOPT_POSTFIELDS => json_encode($payload)
		));
	$response = curl_exec($curl);
	if ($response === false) {
		die(curl_error($curl));
	}
	$response_data = json_decode($response, true);
	$_SESSION['paymentRecordCreated'] = true;
	curl_close($curl);
	
	if ($response_data['succeeded'] != true) {
		switch ($response_data['error']['code']) {
			case 997:
				# code...
				die('997');
				break;
			case 998:
				# code...
				die('998');
				break;
			case 999:
				# code...
				die('999');
				break;
			default:
				# code...
				break;
		}
	}
	
	// kullanıcıyı paparaya yönlendir
	$redirectUrl = $response_data['data']['paymentUrl'];
	echo "<script> window.location.replace('$redirectUrl');</script>";
}
?>