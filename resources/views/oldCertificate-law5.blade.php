<!DOCTYPE HTML>
<html >

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>شهادة تفضيل المنتج المصري</title>

</head>

<body class="container">
	<?php 
	$url = $app->make('url')->to('/');
	if (explode("/", $url)[2] == "localhost:8000") {
		$u = "/certificate/law5_new.png";
	} else {
		$u = $app->make('url')->to('/public/certificate/law5_new.png');
	}
	?>
	<style>
		.container {
			position: relative;
			text-align: center;
		}

		body {
			font-family: DejaVu Sans, sans-serif !important;
			background-image: url(<?= $u ?>);
			background-size: 100% 100%;
			background-repeat: no-repeat;
			background-position: center;
			max-width: 100%;
			max-height: 100%;
			min-width: 100%;
			min-height: 100%;
		}

		
	</style>
	<?php if (strlen($certificate->companyName) > 80){ ?>
		<div style=" font-size:11px;font-wight:bold;position: absolute;top:240px;right: 120px;"><?php if (!empty($certificate))  echo $certificate->companyName; ?></div>
	<?php }else{ ?>
		<div style=" position: absolute;top:235px;right: 80px;left:430px"><?php if (!empty($certificate))  echo $certificate->companyName ?></div>
	<?php } ?>
	<div style=" position: absolute;top:265px;right: 250px;">
		<?php if (!empty($certificate)){ echo "<bdi>".$certificate->companyAddress."</bdi>"; ?>
		<bdi> -</bdi> <?php } ?>
	</div>
	<div style=" position: absolute;top:295px;right: 360px;"><?php if (!empty($certificate)) echo $certificate->companyRegNo; ?> </div>
	<div style=" position: absolute;top:323px;right: 250px;"><?php if (!empty($certificate)) echo $certificate->companyActivity;  ?>  </div>
	<div style=" position: absolute;top:350px;right: 300px"><?php if (!empty($certificate)) echo $certificate->productName; ?> </div>
	<div style=" position: absolute;top:380px;right: 590px;"><?php if (!empty($certificate)) echo  round($certificate->localPercentage, 2) . " %"; ?>  </div>
	<div style=" position: absolute;top:410px;right: 400px;left:130px"><?php if (!empty($certificate)) echo $certificate->productName;  ?> </div>
	<div style=" position: absolute;top:480px;right: 400px;"><?php if (!empty($certificate)) echo date('m/d/Y', strtotime($certificate->startDate)) ?> </div>
	<div style=" position: absolute;top:480px;right: 750px;"><?php if (!empty($certificate)) echo date('m/d/Y', strtotime(date($certificate->startDate). " + 365 day")) ?> </div>
	<div style=" position: absolute;top:567px;right: 720px;"><?php if (!empty($certificate)) echo $certificate->executiveManagerName; ?> </div>
	<div style=" font-size:10px;font-wight:bold;position: absolute;top:125px;right: 843px;"><?php echo "$certificate->id"; ?></div>
</body>

</html>