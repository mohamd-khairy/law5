<!DOCTYPE HTML>
<html lang="ar">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Test Generate pdf certificate</title>

</head>
<style>
	.container {
		position: relative;
		text-align: center;
		/* color: white; */
	}

	body {
		font-family: DejaVu Sans, sans-serif;
		background-image: url(/images/export.png);
		background-size: 100% 100%;
		background-repeat: no-repeat;
		background-position: center;
		max-width: 100%;
		max-height: 100%;
		min-width: 100%;
		min-height: 100%;
	}
</style>

<body class="container">
	<input type="checkbox" <?php if (($assessment['manufactoringByOthers'])) echo ("checked"); ?> style=" position: absolute;top:310px;right: 280px;" />
	<div style=" position: absolute;top:240px;right: 250px;"><?php if (!empty($applicant))  echo $applicant->facilityName; ?></div>
	<div style=" position: absolute;top:270px;right: 250px;"><?php if (!empty($applicant->government->nameEn)) echo $applicant->government->nameEn;
																?> / <?php if (!empty($applicant->city->nameEn))  echo $applicant->city->nameEn; ?></div>
	<div style=" position: absolute;top:305px;right: 480px;"><?php if (!empty($applicant)) echo $applicant->facilityName; ?> </div>
	<div style=" position: absolute;top:305px;right: 820px;"><?php if (!empty($applicant)) echo $applicant->entryNumberInIndusterialRecord; ?> </div>
	<div style=" position: absolute;top:350px;right: 250px;"><?php if (!empty($applicant)) echo $applicant->experienceTypeId; ?> </div>
	<div style=" position: absolute;top:390px;right: 290px;"><?php if (!empty($assessment)) echo $assessment['productName']; ?> </div>
	<div style=" position: absolute;top:435px;right: 590px;"><?php if (!empty($setting)) echo ($setting->exportFundPercentage) . " %"; ?> </div>
	<div style=" position: absolute;top:520px;right: 180px;"><?php if (!empty($assessment)) echo  round($assessment['assessmentScorePercent'], 2) . " %"; ?> </div>
	<div style=" position: absolute;top:520px;right: 500px;"><?php if (!empty($certificate->issueDate)) echo $certificate->issueDate; ?> </div>
	<div style=" position: absolute;top:575px;right: 210px;"><?php if (!empty($certificate->issueDate)) echo $certificate->issueDate; ?> </div>
	<div style=" position: absolute;top:585px;right: 710px;"><?php if (!empty($applicant)) echo $applicant->managerName; ?> </div>
	<div style=" position: absolute;top:117px;right: 843px;"><?php if (!empty($applicant)) echo "EXP-396"; ?> </div>
</body>

</html>