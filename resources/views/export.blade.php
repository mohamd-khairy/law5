<!DOCTYPE HTML>
<html lang="ar">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>شهاده المسانده التصديريه</title>

</head>

<body class="container">
	<?php 
		$url = $app->make('url')->to('/');
		if (explode("/", $url)[2] == "localhost:8000") {
			$u=" ";
			if($manufactor){
				$m = "/certificate/export_new.png";
			}else{
				$m = "/certificate/export_man_new.png";
			}
		} else {
			$u = $app->make('url')->to('/public');
			if($manufactor){
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
	<?php if (strlen($applicant->facilityName) > 100){ ?>
		<div style=" font-size:11px;font-wight:bold;position: absolute;top:240px;right: 120px;"><?php if (!empty($applicant))  echo $applicant->facilityName; ?></div>
	<?php }else{ ?>
		<div style=" position: absolute;top:240px;right: 250px;"><?php if (!empty($applicant))  echo $applicant->facilityName; ?></div>
	<?php } ?>
	<div style=" position: absolute;top:270px;right: 250px;">
		<?php if (!empty($applicant->government->nameAr)){ echo "<bdi>".$applicant->government->nameAr."</bdi>"; ?>
		<bdi> -</bdi> <?php } ?>
		<?php if (!empty($applicant->city->nameAr)){  echo "<bdi>".$applicant->city->nameAr."</bdi>"; ?> 
		<bdi> -</bdi> <?php } ?>
		<?php if (!empty($applicant->indusrialAreaName)){  echo "<bdi>".$applicant->indusrialAreaName."</bdi>"; ?> 
		<bdi> -</bdi> <?php } ?>
		<?php if (!empty($applicant->blockNumber)){ echo "<bdi>".$applicant->blockNumber."</bdi>بلوك رقم"; ?> 
		<bdi> -</bdi> <?php } ?>
		<?php if (!empty($applicant->areaNumber)) echo  "<bdi>".$applicant->areaNumber."</bdi>منطقه رقم"; ?>
		<?php if (!empty($applicant->areaOrDistrict)) echo "<bdi>".$applicant->areaOrDistrict."</bdi>"; ?> 
		<?php if (!empty($applicant->buildingNumber)) echo "<bdi>".$applicant->buildingNumber."</bdi>"; ?> 
	</div>
	<?php if($manufactor){ ?>
		<?php if (strlen($certificate->manufacturingCompanyName) > 15){ ?>
				<div style=" font-size:10.5px;font-wight:bold;position: absolute;top:310px;right: 380px;"><?php if (!empty($certificate)) echo "<bdi>".$certificate->manufacturingCompanyName."<bdi>"; ?> </div>
		<?php }else{ ?>
			<div style=" position: absolute;top:305px;right: 300px;"><?php if (!empty($certificate)) echo "<bdi>".$certificate->manufacturingCompanyName."<bdi>"; ?> </div>
		<?php } ?>
		<div style=" position: absolute;top:305px;right: 820px;"><?php if (!empty($certificate)) echo "<bdi>".$certificate->manufacturingCompanyIndustrialRegistry."<bdi>"; ?> </div>
	<?php }?>
	<div style=" position: absolute;top:335px;right: 220px;"><?php if (!empty($assessment)) echo $assessment['chamber']['nameAr'] ?> </div>
	<div style=" position: absolute;top:365px;right: 290px;"><?php if (!empty($assessment)) echo $assessment['productName']; ?> </div>
	<div style=" position: absolute;top:400px;right: 590px;"><?php if (!empty($assessment)) echo  round($assessment['assessmentScorePercent'], 2) . " %"; ?>  </div>
	<div style=" position: absolute;top:445px;right: 530px;left:73px"><?php if (!empty($assessment)) echo $assessment['productName']; ?> </div>
	<div style=" position: absolute;top:480px;right: 180px;"><?php if (!empty($assessment)) echo  round($assessment['assessmentScorePercent'], 2) . " %"; ?> </div>
	<div style=" position: absolute;top:480px;right: 515px;"><?php if (!empty($certificate->issueDate)) echo \Carbon\Carbon::parse($certificate->issueDate)->format('Y'); ?> </div>
	<div style=" position: absolute;top:560px;right: 210px;"><?php if (!empty($certificate->issueDate)) echo \Carbon\Carbon::parse($certificate->issueDate)->format('m/d/Y'); ?> </div>
	<div style=" position: absolute;top:582px;right: 695px;"><?php if (!empty($setting)) echo $setting->executiveManagerName; ?></div>
	<div style=" font-size:10px;font-wight:bold;position: absolute;top:125px;right: 843px;"><?php echo "EXP-$certificateNumber"; ?> </div>

</body>

</html>
