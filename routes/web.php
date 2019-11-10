<?php

use Laravel\Lumen\Routing\Router;

$app->get('/', function () use ($app) {
    return $app->app->version();
});

$app->get('/key', function () {
    return Illuminate\Support\Str::random(32);
});

//all
$app->group(['prefix' => 'api'], function () use ($app) {

    /** @var $app Router */
    $app->post('/detailed_assessment', 'AssessmentController@detailedAssessment'); // calc detailed
    $app->post('/totals_assessment', 'AssessmentController@totalsAssessment'); // calc total

    $app->post('/register', 'UsersController@register');
    $app->post('/login', 'UsersController@login');

    $app->put('/confirmEmail', 'UsersController@ConfirmEmail');

    $app->post('/forgotPassword', 'UsersController@ResetPassword');
    $app->post('/resetPassword', 'UsersController@SendResetPasswordCode');
    
    $app->get('send', 'EmployeeController@mail_test'); // for test only 
});

//login only ,, check token Onlyyyyy
$app->group(['prefix' => 'api', 'middleware' => 'authTokenOnly'], function () use ($app) {

    $app->post('/Step2Authentication', 'UsersController@Step2Authentication');
    $app->put('/resendStep2AuthenticationCode', 'UsersController@resendStep2AuthenticationCode');
    
});

    
//Definitions
$app->group(['prefix' => 'api'], function () use ($app) { 

    //city
    $app->get("getCitiesByGovId/{governorateId}", "CityController@get_all_data_for_this_city_by_gov_id");
    $app->get("/getGovernments", "GovernmentController@all");


    $app->get('getRepresentativeTypes', 'RepresentativeTypeController@all');
    $app->get('certificateType', 'CertificateTypeController@certificateType');

    /**
     * Routes for resource assessment-method
     */
    $app->get('assessment-method', 'AssessmentMethodsController@all');
    $app->get('assessment-method/{id}', 'AssessmentMethodsController@get');
    /**
     * Routes for resource chamber
     */
    $app->get('chamber', 'ChambersController@get_list');
    $app->get('chamber/{id}', 'ChambersController@get_by_id');
    /**
     * Routes for resource sector
     */
    $app->get('sector', 'SectorsController@all');
    $app->get('sector/{id}', 'SectorsController@get');
    /**
     * Routes for resource role
     */

    $app->get('role', 'RolesController@all');
    $app->get('role/{id}', 'RolesController@get');
    /**
     * Routes for resource section
     */
    $app->get('section', 'SectionController@sectionsList');
    $app->get('section/{id}', 'SectionController@getSection');
    $app->get('sectionsByChamberId/{chamberId}', 'SectionController@getSectionByChamberId');

    $app->get('unit', 'UnitController@all');
 
});

$app->group(['prefix' => 'api', "middleware" => "PermissionFor:Admin"], function () use ($app) { //
    /**
     * Routes for resource assessment-method
     */

    $app->post('assessment-method', 'AssessmentMethodsController@add');
    $app->put('assessment-method/{id}', 'AssessmentMethodsController@put');
    $app->delete('assessment-method/{id}', 'AssessmentMethodsController@remove');


    //logging
    $app->get("/logging" , "LogController@ListOfLogging");
    $app->get("/loggingById/{id}" , "LogController@Logging_by_id");

    //auto delete attachment that haven't request
    $app->get('/auto_delete', 'AttachmentsController@auto_delete');
    
    /**
     * Routes for resource chamber
     */
    $app->post('chamber', 'ChambersController@addChamber');
    $app->put('chamber/{id}', 'ChambersController@putChamber');
    $app->delete('chamber/{id}', 'ChambersController@remove');

    /**
     * Routes for resource sector
     */
    $app->post('sector', 'SectorsController@add');
    $app->put('sector/{id}', 'SectorsController@put');
    $app->delete('sector/{id}', 'SectorsController@remove');

    /**
     * Routes for resource role
     */
    $app->get('roleWithoutApplicant', 'RolesController@roleWithoutApplicant');

    $app->post('role', 'RolesController@add');
    $app->put('role/{id}', 'RolesController@put');
    $app->delete('role/{id}', 'RolesController@remove');
    /**
     * Routes for resource section
     */
    $app->post('section', 'SectionController@add');
    $app->put('section/{id}', 'SectionController@updateSection');
    $app->delete('section/{id}', 'SectionController@remove');
});

//Employee
$app->group(['prefix' => 'api', "middleware" => "PermissionFor:Admin"], function () use ($app) { //
    //reports
    $app->get('getMostExpensiveMaterialsImported', 'AssessmentController@getMostExpensiveMaterials_Imported');
    $app->get('getMostExpensiveMaterialsLocal', 'AssessmentController@getMostExpensiveMaterials_Local');

    $app->get('requestCountByManufacturingType', 'RequestController@GerRequestsBy_status_id');
    $app->get('applicantsWithIssuedCertificates', 'RequestController@GetApplicant_with_issue_certificate');
    $app->get('certificatedProducts', 'RequestController@CertificatedProducts_with_issue_certificate');

    $app->get('certificatesCount', 'RequestController@CertificatesCount_by_sector_cert_gover');
    $app->get('expensesBySector', 'RequestController@ExpensesBySector');
    $app->get('expensesBySectorKeys', 'RequestController@expensesBySectorKeys');

    
    /**
     * Routes for resource Employee
     */
    $app->get('employee', 'EmployeeController@employeesList'); //admin
    $app->post('employee', 'EmployeeController@createEmployee'); //admin
    $app->put('employee/{id}', 'EmployeeController@updateEmployee'); //admin
    $app->get('employee/{id}', 'EmployeeController@getEmployeeById'); //admin

});


$app->group(['prefix' => 'api', "middleware" => "PermissionFor:Admin&IDAManager"], function () use ($app) { //
    $app->get('employeeList', 'EmployeeController@getEmployee_by_roleId_or_not'); //ida manager

});
//Request_action
$app->group(['prefix' => 'api', "middleware" => "NotPermissionFor:Applicant"], function () use ($app) {
    $app->get('requestActions/{request_id}', 'ActionController@get_request_actions_by_request_id');
    $app->get('requestAction/{requestAction_id}', 'ActionController@show');
    $app->get('requestLastAction/{request_id}', 'ActionController@get_last_request_action_by_request_id');
});

//request 
$app->group(['prefix' => 'api', "middleware" => "PermissionFor:Applicant"], function () use ($app) {
    $app->post('applicantRequest', 'RequestController@create_request');
    $app->put('applicantRequest/{id}', 'RequestController@update_request');
    // $app->post('CloneRequest', 'RequestController@CloneRequest');
    $app->put('resend_request', 'RequestController@resend_request');
    $app->get("getLatestReturnAction/{requestId}", 'RequestController@getLatestReturnAction');
});

$app->group(['prefix' => 'api', "middleware" => "PermissionFor:IDAManager"], function () use ($app) { //
    $app->post('assign', 'RequestController@assign');
    $app->put('ConfirmRequest', 'RequestController@ConfirmRequest');
    $app->put('returnToEmployee', 'RequestController@Return_To_Employee');
});

$app->group(['prefix' => 'api', "middleware" => "PermissionFor:ChamberEmployee&FEIEmployee"], function () use ($app) {
    $app->put('VerifyMembership', 'RequestController@VerifyMembership');
    $app->put('ChangeChamber', 'RequestController@ChangeChamber');
});

$app->group(['prefix' => 'api', "middleware" => "NotPermissionFor:Admin"], function () use ($app) {
    $app->post('get_request', 'RequestController@get_request');
});

$app->group(['prefix' => 'api', "middleware" => "PermissionFor:IDAEmployee"], function () use ($app) {
    $app->put('saveReview', 'RequestController@SaveReview');
    $app->put('openRequest', 'RequestController@StartReview');
    $app->put('closeRequest', 'RequestController@CloseReview');
    $app->put('RespondToRequest', 'RequestController@RespondToRequest');
});

$app->group(['prefix' => 'api', "middleware" => "auth"], function () use ($app) { 
    $app->get('request/{id}', 'RequestController@get_request_by_id'); //all
    $app->get('request_status', 'RequestController@get_request_status'); //all
});

$app->group(['prefix' => 'api', "middleware" => "PermissionFor:ChamberEmployee&Applicant&IDAEmployee&IDAManager"], function () use ($app) {
    $app->get('requestAttachments/{request_id}', 'RequestController@get_request_attachments_by_request_id'); //all
});

$app->group(['prefix' => 'api', "middleware" => "PermissionFor:Applicant"], function () use ($app) {
    $app->get('applicantAttachments', 'RequestController@get_request_attachments_by_applicant_id'); //all
});

//Unit
$app->group(['prefix' => 'api', "middleware" => "PermissionFor:Admin"], function () use ($app) { //

    /**
     * Routes for resource unit
     */
    $app->post('unit', 'UnitController@add');
    $app->put('unit/{id}', 'UnitController@put');
    $app->delete('unit/{id}', 'UnitController@remove');
});

//Applicant
$app->group(['prefix' => 'api', "middleware" => "auth"], function () use ($app) { //
    $app->get('applicant/{id}', 'ApplicantController@getApplicantById');
});

$app->group(['prefix' => 'api', "middleware" => "PermissionFor:Applicant&Admin"], function () use ($app) { //
    /**
     * Routes for resource applicant
     */
    $app->get('applicant', 'ApplicantController@getApplicantProfile');
    $app->put('applicant', 'ApplicantController@saveApplicantProfile');
    $app->get('isProfileComplete', 'ApplicantController@isFullRegistered');
});

//attachment
$app->group(['prefix' => 'api', "middleware" => "PermissionFor:Applicant"], function () use ($app) { //
    /**
     * Routes for resource attachment
     */
    $app->get('attachments', 'AttachmentsController@all');
    $app->post('attachments', 'AttachmentsController@upload');
    $app->delete('attachments/{id}', 'AttachmentsController@delete_file');
});

$app->group(['prefix' => 'api' , "middleware" => "auth"], function () use ($app) {// 
    $app->get('download', 'AttachmentsController@download');
});
 
//Assessment
$app->group(['prefix' => 'api', "middleware" => "PermissionFor:IDAManager&IDAEmployee&Applicant"], function () use ($app) { //
    /**
     * Routes for resource Save Assessment
     */
    $app->post('assessment', 'AssessmentController@SaveAssessment');
    $app->get('assessment/{id}', 'AssessmentController@GetAssessment');
    $app->put('assessment/{id}', 'AssessmentController@UpdateAssessment');
    $app->get('assessment_request_id/{id}', 'AssessmentController@Get_assessment_by_request_id');
    $app->get('applicant_assessments', 'AssessmentController@Get_assessment_by_applicant_id'); //
});

//settings
$app->group(['prefix' => 'api'], function () use ($app) {
    $app->get('settings', 'SettingsController@all_settings');
});

//user settings
$app->group(['prefix' => 'api', "middleware" => "auth"], function () use ($app) {
    $app->get('user/settings', 'UserSettingsController@get');
    $app->put('user/settings', 'UserSettingsController@put');
});

$app->group(['prefix' => 'api', "middleware" => "PermissionFor:Admin"], function () use ($app) { //
    $app->get('getEmailSettings', 'SettingsController@mail_settings');
    $app->put('settings',  'SettingsController@setting');
    $app->put('saveEmailSettings', 'SettingsController@setting_mail');
    
});

 
$app->group(['prefix' => 'api', "middleware" => "PermissionFor:FEIEmployee"], function () use ($app) { //
    $app->post('IssueCertificates', 'CertificateController@IssueCertificates');
    $app->get('getCertificates', 'CertificateController@get_certificates');

});


$app->group(['prefix' => 'api', "middleware" => "PermissionFor:GAGSUser"], function () use ($app) { //
    $app->post('SetCertificateAsWinner', 'CertificateController@SetCertificateAsWinner');
});

$app->group(['prefix' => 'api', "middleware" => "PermissionFor:EOSUser&GAGSUser&FEIEmployee&ChamberEmployee&Applicant"], function () use ($app) { //
    $app->get('getRequestCertificates/{req_id}', 'CertificateController@listOfCertificate');
});


//CertificateCopyRequests
$app->group(['prefix' => 'api', "middleware" => "PermissionFor:Applicant"], function () use ($app) {
    $app->post('createCertificateCopyRequest', 'CertificateCopyRequestController@store');
});

$app->group(['prefix' => 'api', "middleware" => "PermissionFor:Applicant&FEIEmployee"], function () use ($app) {
    $app->get('getCertificateCopyRequests', 'CertificateCopyRequestController@index');
    $app->get('getCertificateCopyRequests/{id}', 'CertificateCopyRequestController@show');
    
});

$app->group(['prefix' => 'api', "middleware" => "PermissionFor:EOSUser&GAGSUser&FEIEmployee&ChamberEmployee"], function () use ($app) { //
    $app->get('pdf', 'CertificateController@pdf');
    $app->get('certificateCopyPDF', 'CertificateCopyRequestController@certificateCopyPDF');
});



$app->group(['prefix' => 'api', "middleware" => "PermissionFor:FEIEmployee"], function () use ($app) {
    $app->put('saveCertificateCopyRequests/{id}', 'CertificateCopyRequestController@update');
    $app->put('issueCertificateCopies/{id}', 'CertificateCopyRequestController@issueCertificateCopies');

});

//certificateMinimumPercentage
$app->group(['prefix' => 'api', "middleware" => "PermissionFor:Admin"], function () use ($app) {
    $app->post('certificateMinimumPecentage', 'CertificateMinimumPercentageController@store');
    $app->delete('certificateMinimumPecentage/{id}', 'CertificateMinimumPercentageController@destroy');
    $app->put('certificateMinimumPecentage/{id}', 'CertificateMinimumPercentageController@update');
    $app->get('certificateMinimumPecentage', 'CertificateMinimumPercentageController@index');
    $app->get('certificateMinimumPecentage/{id}', 'CertificateMinimumPercentageController@show');
});

$app->group(['prefix' => 'api', "middleware" => "auth"], function () use ($app) { 
    $app->get('certificateMinimumPecentageByDate', 'CertificateMinimumPercentageController@showByDate'); //all

});

//Assessment
$app->group(['prefix' => 'api', "middleware" => "auth"], function () use ($app) { 
    $app->get('assessmentSummary/{id}', 'AssessmentController@summary'); //all

});

//certificate Renew mohamed khairy
$app->group(['prefix' => 'api', "middleware" => "PermissionFor:Applicant"], function () use ($app) { 
    $app->post('renewCertificate', 'CertificateController@renewCertificate'); //applicant
});

$app->group(['prefix' => 'api', "middleware" => "PermissionFor:Admin"], function () use ($app) { 
    $app->get('expired_and_notRenewed', 'CertificateController@expired_and_notRenewed'); //admin
});

$app->group(['prefix' => 'api', "middleware" => "PermissionFor:IDAManager"], function () use ($app) { 
    $app->put('change_product_name', 'AssessmentController@change_product_name'); //IDAManager
});

$app->group(['prefix' => 'api', "middleware" => "PermissionFor:Admin"], function () use ($app) { 
    $app->get('certificate/renewAndCopiesCount', 'CertificateController@renewAndCopiesCount'); //admin

});

//Old Certificates

$app->group(['prefix' => 'api', "middleware" => "PermissionFor:Admin"], function () use ($app) { 
    $app->get('oldCertificate', 'OldCertificateController@index'); //admin
    $app->get('oldCertificate/{id}/generatePDF', 'OldCertificateController@generatePDF'); //admin
    $app->get('oldCertificate/{id}', 'OldCertificateController@show'); //admin
});
