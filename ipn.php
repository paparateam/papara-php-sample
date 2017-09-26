<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Alışveriş Sayfası</title>
<link href="css/style.css" rel="stylesheet" type="text/css" />
<style>
table {
    font-family: arial, sans-serif;
    border-collapse: collapse;
    width: 100%;
}
td, th {
    border: 1px solid #dddddd;
    text-align: left;
    padding: 8px;
}
tr:nth-child(even) {
    background-color: #dddddd;
}
</style>
</head>
<body>

<h1 align="center">Alınan Ödemeler </h1>
<div style="width:400px; margin:0 auto;">
<a class="button" style="float:left" href="index.php">Mağaza</a>
<a class="button" style="margin-left:20px;"  href="ipn.php">Siparişler</a>
<a class="button" style="float:right" href="masspayment.php">Ödeme Al</a>
</div>
<hr style="width:720px; margin-top:20px; height:2px;border:none;color:#333;background-color:#333;">

<div style="margin-left:25px;margin-right:25px;">
<table>
  <tr>
    <th>Id</th>
    <th>Tarih</th>
    <th>İşlem Kodu</th>
    <th>Kullanıcı</th>
    <th>Tutar</th>
    <th>İşlem Açıklaması</th>
  </tr>
  

  <?php
    $json_decoded = json_decode(file_get_contents('json/all_ipn.json'),true);
    foreach ($json_decoded as $item) {
      $html = "<tr>
        <td>{{id}}</td>
        <td>{{date}}</td>
        <td>{{process_code}}</td>
        <td>{{user}}</td>
        <td>{{amount}}</td>
        <td>{{description}}</td>
      </tr>";
      $date = explode("T",$item['createdAt']);
      $date[1] = substr($date[1],0,8);
      $date[0] = explode("-",$date[0]);
      $today = $date[0][2]. "-". $date[0][1]. "-". $date[0][0];
      $html = str_replace('{{id}}',$item['id'],$html);
      $html = str_replace('{{date}}',$date[1]. " | ". $today,$html);
      $html = str_replace('{{process_code}}',$item['referenceId'],$html);
      $html = str_replace('{{user}}',$item['userId'],$html);
      $html = str_replace('{{amount}}',$item['amount'],$html);
      $html = str_replace('{{description}}',$item['orderDescription'],$html);
      echo $html;
    }
  ?>

</table>    
</div>


</body>
</html>
