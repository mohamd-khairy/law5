<!DOCTYPE HTML>
<html lang="ar">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>شهادة المساندة التصديرية</title>

</head>

<body class="container">
	
	<?php 
		$url = $app->make('url')->to('/');
		if (explode("/", $url)[2] == "localhost:8000") {
			$u=" ";
			if($manufacture){
				$m = "/certificate/export_new.png";
			}else{
				$m = "/certificate/export_man_new.png";
			}
		} else {
			$u = $app->make('url')->to('/public');
			if($manufacture == 1){
				$m = $app->make('url')->to('/public/certificate/export_new.png');
			}else{
				$m = $app->make('url')->to('/public/certificate/export_man_new.png');
			}
		}
	?>
	<style>
		.container {
			position: relative;
			text-align: center;
		}

		body {
			font-family: DejaVu Sans, sans-serif;
			background-image: url(<?= $m ?>);
			background-size: 100% 100%;
			background-repeat: no-repeat;
			background-position: center;
			max-width: 100%;
			max-height: 100%;
			min-width: 100%;
			min-height: 100%;
		}
		
	</style>
	<?php if (strlen($certificate->companyName) > 100){ ?>
		<div style=" font-size:11px;font-wight:bold;position: absolute;top:240px;right: 120px;"><?php if (!empty($certificate))  echo $certificate->companyName; ?></div>
	<?php }else{ ?>
		<div style=" position: absolute;top:240px;right: 250px;"><?php if (!empty($certificate))  echo $certificate->companyName; ?></div>
	<?php } ?>
	<div style=" position: absolute;top:265px;right: 250px;">
		<?php if (!empty($certificate)){ echo "<bdi>".$certificate->companyAddress."</bdi>"; } ?>
	</div>
	<?php if($manufacture){ ?>
		<?php if (strlen($certificate->manufacturingCompanyName) > 15){ ?>
				<div style=" font-size:10.5px;font-wight:bold;position: absolute;top:310px;right: 380px;"><?php if (!empty($certificate)) echo "<bdi>".$certificate->manufacturingCompanyName."<bdi>"; ?> </div>
		<?php }else{ ?>
			<div style=" position: absolute;top:305px;right: 300px;"><?php if (!empty($certificate)) echo "<bdi>".$certificate->manufacturingCompanyName."<bdi>"; ?> </div>
		<?php } ?>
		<div style=" position: absolute;top:305px;right: 820px;"><?php if (!empty($certificate)) echo "<bdi>".$certificate->manufacturingCompanyIndustrialRegistry."<bdi>"; ?> </div>
	<?php }?>
	<div style=" position: absolute;top:335px;right: 220px;"><?php if (!empty($certificate)) echo $certificate->comanyActivity?> </div>
	<div style=" position: absolute;top:365px;right: 290px;"><?php if (!empty($certificate)) echo $certificate->productName; ?> </div>
	<div style=" position: absolute;top:400px;right: 590px;"><?php if (!empty($certificate)) echo  round($certificate->localPercentage, 2) . " %"; ?>  </div>
	<div style=" position: absolute;top:445px;right: 530px;left:73px"><?php if (!empty($certificate)) echo $certificate->productName; ?> </div>
	<div style=" position: absolute;top:480px;right: 180px;"><?php if (!empty($certificate)) echo  round($certificate->localPercentage, 2) . " %"; ?> </div>
	<div style=" position: absolute;top:480px;right: 515px;"><?php if (!empty($certificate->startDate)) echo \Carbon\Carbon::parse($certificate->startDate)->format('Y'); ?> </div>
	<div style=" position: absolute;top:560px;right: 210px;"><?php if (!empty($certificate->startDate)) echo \Carbon\Carbon::parse($certificate->startDate)->format('m/d/Y'); ?> </div>
	<div style=" position: absolute;top:582px;right: 695px;"><?php if (!empty($certificate)) echo $certificate->executiveManagerName; ?></div>
	<div style=" font-size:10px;font-wight:bold;position: absolute;top:125px;right: 843px;"><?php echo "$certificate->id"; ?> </div>

</body>

</html>
