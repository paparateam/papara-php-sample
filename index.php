<?php
session_start();
//current URL of the Page. cart_update.php redirects back to this URL
$current_url = urlencode($url="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
$currency = '&#8378; '; //Currency Character or code
$products = '';
?>

<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Alışveriş Sayfası</title>
<link href="css/style.css" rel="stylesheet" type="text/css" />
</head>
<body>

<h1 align="center">Ürünler </h1>
<div style="width:400px; margin:0 auto;">
<a class="button" style="float:left" href="index.php">Mağaza</a>
<a class="button" style="margin-left:20px;"  href="ipn.php">Siparişler</a>
<a class="button" style="float:right" href="masspayment.php">Ödeme Al</a>
</div>
<hr style="width:720px; margin-top:20px; height:2px;border:none;color:#333;background-color:#333;">
<!-- Products List Start -->
<?php
$products = array(
	[
		array("id"=>2,"product_code"=>"PD1002","product_name"=>"Televizyon","product_desc"=>"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.","product_img_name"=>"lcd-tv.jpg","price"=>5.85),
		array("id"=>3,"product_code"=>"PD1003","product_name"=>"Harici Hard Disk","product_desc"=>"Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.","product_img_name"=>"external-hard-disk.jpg","price"=>1.00),
		array("id"=>4,"product_code"=>"PD1004","product_name"=>"Saat","product_desc"=>"Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.","product_img_name"=>"wrist-watch.jpg","price"=>4.30)
		]
);

if($products){
$products_item = '<ul class="products">';
//fetch products set as object and output HTML
$index = 0;
foreach ($products[$index] as $obj)
{
$products_item .= <<<EOT
	<li class="product">
	<form method="post" action="cart_update.php">
	<div class="product-content"><h3>{$obj['product_name']}</h3>
	<div class="product-thumb"><img src="images/{$obj['product_img_name']}"></div>
	<div class="product-desc">{$obj['product_desc']}</div>
	<div class="product-info">
	Fiyat {$obj['price']}{$currency}

	<fieldset>

	<label>
		<span>Renk</span>
		<select name="product_color">
		<option value="Black">Siyah</option>
		<option value="Silver">Gümüş</option>
		</select>
	</label>

	<label>
		<span>Miktar</span>
		<input type="text" size="2" maxlength="2" name="product_qty" value="1" />
	</label>

	</fieldset>
	<input type="hidden" name="product_code" value="{$obj['product_code']}" />
	<input type="hidden" name="type" value="add" />
	<input type="hidden" name="return_url" value="{$current_url}" />
	<div align="center"><button type="submit" class="add_to_cart">Ekle</button></div>
	</div></div>
	</form>
	</li>
EOT;
$index += 1;
}
$products_item .= '</ul>';
echo $products_item;
}
?>
<!-- Products List End -->

<!-- View Cart Box Start -->
<?php
if(isset($_SESSION["cart_products"]) && count($_SESSION["cart_products"])>0)
{
	echo '<div class="cart-view-table-front" id="view-cart">';
	echo '<h3>Sepetiniz</h3>';
	echo '<form method="post" action="cart_update.php">';
	echo '<table width="100%"  cellpadding="6" cellspacing="0">';
	echo '<tbody>';

	$total = 0;
	$b = 0;
	foreach ($_SESSION["cart_products"] as $cart_itm)
	{
		$product_name = $cart_itm["product_name"];
		$product_qty = $cart_itm["product_qty"];
		$product_price = $cart_itm["product_price"];
		$product_code = $cart_itm["product_code"];
		$product_color = $cart_itm["product_color"];
		$bg_color = ($b++%2==1) ? 'odd' : 'even'; //zebra stripe
		echo '<tr class="'.$bg_color.'">';
		echo '<td>Miktar <input type="text" size="2" maxlength="2" name="product_qty['.$product_code.']" value="'.$product_qty.'" /></td>';
		echo '<td>'.$product_name.'</td>';
		echo '<td><input type="checkbox" name="remove_code[]" value="'.$product_code.'" /> Sil</td>';
		echo '</tr>';
		$subtotal = ($product_price * $product_qty);
		$total = ($total + $subtotal);
	}
	echo '<td colspan="4">';
	echo '<a href="view_cart.php" class="button">Satın Al</a><button type="submit">Güncelle</button>';
	echo '</td>';
	echo '</tbody>';
	echo '</table>';

	$current_url = urlencode($url="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
	echo '<input type="hidden" name="return_url" value="'.$current_url.'" />';
	echo '</form>';
	echo '</div>';

}
?>
<!-- View Cart Box End -->

</body>
</html>
