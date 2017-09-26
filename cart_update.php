<?php
session_start();
//add product to session or create new one
if(isset($_POST["type"]) && $_POST["type"]=='add' && $_POST["product_qty"]>0)
{
	foreach($_POST as $key => $value){ //add all post vars to new_product array
		$new_product[$key] = filter_var($value, FILTER_SANITIZE_STRING);
    }
	//remove unecessary vars
	unset($new_product['type']);
	unset($new_product['return_url']);

	$products = array(
		[
			array("id"=>2,"product_code"=>"PD1002","product_name"=>"Televizyon","product_desc"=>"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.","product_img_name"=>"lcd-tv.jpg","price"=>5.85),
			array("id"=>3,"product_code"=>"PD1003","product_name"=>"Harici Hard Disk","product_desc"=>"Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.","product_img_name"=>"external-hard-disk.jpg","price"=>1.00),
			array("id"=>4,"product_code"=>"PD1004","product_name"=>"Saat","product_desc"=>"Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.","product_img_name"=>"wrist-watch.jpg","price"=>4.30)
			]
	);
	 
	$i = 0;
	foreach($products[$i++] as $item) {
		if ($new_product['product_code'] == $item['product_code']) {
			$product_name = $item['product_name'];
			$price = $item['price'];
		}
	}

	$new_product["product_name"] = $product_name;
	$new_product["product_price"] = $price;

	if(isset($_SESSION["cart_products"])){  //if session var already exist
		if(isset($_SESSION["cart_products"][$new_product['product_code']])) //check item exist in products array
		{	
			$new_product['product_qty'] += $_SESSION["cart_products"][$new_product['product_code']]['product_qty'];
			unset($_SESSION["cart_products"][$new_product['product_code']]); //unset old array item
		}
	}
	$_SESSION["cart_products"][$new_product['product_code']] = $new_product; //update or create product session with new item
}

//update or remove items
if(isset($_POST["product_qty"]) || isset($_POST["remove_code"]))
{
	//update item quantity in product session
	if(isset($_POST["product_qty"]) && is_array($_POST["product_qty"])){
		foreach($_POST["product_qty"] as $key => $value){
			if(is_numeric($value)){
				$_SESSION["cart_products"][$key]["product_qty"] = $value;
			}
		}
	}
	//remove an item from product session
	if(isset($_POST["remove_code"]) && is_array($_POST["remove_code"])){
		foreach($_POST["remove_code"] as $key){
			unset($_SESSION["cart_products"][$key]);
		}
	}
}

//back to return url
$return_url = (isset($_POST["return_url"]))?urldecode($_POST["return_url"]):''; //return url
header('Location:'.$return_url);

?>
