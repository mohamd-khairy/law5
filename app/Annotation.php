<?php
//php artisan swagger-lume:generate
//http://apps.orchtech.com:81/law5/api/documentation
/**
 * @OA\Get(
 *   path="/law5/",
 *   @OA\Response(
 *     response=200,
 *     description="Working"
 *   ),
 *   @OA\Response(
 *     response="default",
 *     description="an ""unexpected"" error"
 *   )
 * )
 */

 /**
 * @OA\Get(
 *     path="/law5/api/logging",
 *     tags={"logging"},
 *     security={{"bearerAuth":{}}}, 
 *     @OA\Response(
 *         response="200",
 *         description="Returns some sample category things",
 *         @OA\JsonContent()
 *     )
 * )
 */

 /**
 * @OA\Get(
 *     path="/law5/api/loggingById/{id}",
 *     security={{"bearerAuth":{}}}, 
 *     tags={"logging"},
 *      @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="The log id",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ), 
 *     @OA\Response(
 *         response="200",
 *         description="Returns some sample category things",
 *         @OA\JsonContent()
 *     )
 * )
 */
/**
 * @OA\Get(
 *     path="/law5/api/chamber",
 *     tags={"chamber"},
 *     security={{"bearerAuth":{}}}, 
 *     @OA\Response(
 *         response="200",
 *         description="Returns some sample category things",
 *         @OA\JsonContent()
 *     )
 * )
 */



 /**
 * @OA\Get(
 *     path="/law5/api/assessment-method",
 *      tags={"assessment-method"},
 *     security={{"bearerAuth":{}}}, 
 *     @OA\Response(
 *         response="200",
 *         description="Returns some sample category things",
 *         @OA\JsonContent()
 *     )
 * )
 */




/**
 * @OA\Get(
 *     path="/law5/api/role",
 *     security={{"bearerAuth":{}}}, 
 *     @OA\Response(
 *         response="200",
 *         description="Returns some sample category things",
 *         @OA\JsonContent()
 *     )
 * )
 */

/**
 * @OA\Get(
 *     path="/law5/api/section",
 *     tags={"sections"},
 *     security={{"bearerAuth":{}}}, 
 *     @OA\Response(
 *         response="200",
 *         description="Returns some sample category things",
 *         @OA\JsonContent()
 *     )
 * )
 */

  /**
 * @OA\Get(
 *      path="/law5/api/sectionsByChamberId/{chamberId}",
 *      tags={"sections"},
 *      security={{"bearerAuth":{}}}, 
 *      @OA\Parameter(
 *         name="chamberId",
 *         in="path",
 *         description="The id of chamber",
 *         required=true,
 *         @OA\Schema(type="number")
 *      ),
 *     @OA\Response(
 *         response="200",
 *         description="Returns some sample category things",
 *         @OA\JsonContent()
 *     )
 * )
 * 
 */

  /**
 * @OA\Post(
 *      path="/law5/api/section",
 *      tags={"sections"},
 *      security={{"bearerAuth":{}}}, 
 *      @OA\Parameter(
 *          name="nameAr",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="string")
 *      ),
 *      @OA\Parameter(
 *          name="nameEn",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="string")
 *      ),
 *      @OA\Parameter(
 *          name="chamberId",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="number")
 *      ),
 *     @OA\Response(
 *         response="200",
 *         description="Returns some sample category things",
 *         @OA\JsonContent()
 *     )
 * )
 * 
 */

 /**
 * @OA\Put(
 *      path="/law5/api/section/{id}",
 *      tags={"sections"},
 *      security={{"bearerAuth":{}}}, 
 *      @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="The id of section",
 *         required=true,
 *         @OA\Schema(type="number")
 *      ),
 *      @OA\Parameter(
 *          name="nameAr",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="string")
 *      ),
 *      @OA\Parameter(
 *          name="nameEn",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="string")
 *      ),
 *      @OA\Parameter(
 *          name="chamberId",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="number")
 *      ),
 *     @OA\Response(
 *         response="200",
 *         description="Returns some sample category things",
 *         @OA\JsonContent()
 *     )
 * )
 * 
 */

 /**
 * @OA\Delete(
 *      path="/law5/api/section/{id}",
 *      tags={"sections"},
 *      security={{"bearerAuth":{}}}, 
 *      @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="The id of section",
 *         required=true,
 *         @OA\Schema(type="number")
 *      ),
 *     @OA\Response(
 *         response="200",
 *         description="Returns some sample category things",
 *         @OA\JsonContent()
 *     )
 * )
 * 
 */

/**
 * @OA\Get(
 *    path="/law5/api/getGovernments",
 *  *      tags={"city"},
 *     security={{"bearerAuth":{}}}, 
 *     @OA\Response(
 *         response="200",
 *         description="Returns some sample category things",
 *         @OA\JsonContent()
 *     )
 * )
 */
/**
 * @OA\Get(
 *     path="/law5/api/getRepresentativeTypes",
 *     security={{"bearerAuth":{}}}, 
 *     @OA\Response(
 *         response="200",
 *         description="Returns some sample category things",
 *         @OA\JsonContent()
 *     )
 * )
 */
/**
 * @OA\Get(
 *     path="/law5/api/getCitiesByGovId/{governorateId}",
 *      tags={"city"},
 *     security={{"bearerAuth":{}}}, 
 *     @OA\Parameter(
 *         name="governorateId",
 *         in="path",
 *         description="The governorateId",
 *         required=true,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Returns some sample category things",
 *         @OA\JsonContent()
 *     )
 * )
 */

/**
 * @OA\Post(
 *      path="/law5/api/login",
 *      tags={"user"},
 *      @OA\Parameter(
 *          name="email",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="string")
 *      ),
 *      @OA\Parameter(
 *          name="password",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="string")
 *      ),
 *     @OA\Response(
 *         response="200",
 *         description="Returns some sample category things",
 *         @OA\JsonContent()
 *     )
 * )
 */
/**
 * @OA\Post(
 *      path="/law5/api/register",
 *      tags={"user"},
 *      @OA\Parameter(
 *          name="name",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="string")
 *      ),
 *      @OA\Parameter(
 *          name="email",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="string")
 *      ),
 *      @OA\Parameter(
 *          name="password",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="string")
 *      ),
 *      @OA\Parameter(
 *          name="telephone",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="string")
 *      ),
 *     @OA\Response(
 *         response="200",
 *         description="Returns some sample category things",
 *         @OA\JsonContent()
 *     )
 * )
 * 
 */
/**
 * @OA\Post(
 *      path="/law5/api/chamber",
 *      tags={"chamber"},
 *      security={{"bearerAuth":{}}}, 
 *      @OA\Parameter(
 *          name="nameEn",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="string")
 *      ),
 *      @OA\Parameter(
 *          name="nameAr",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="string")
 *      ),
 *      @OA\Parameter(
 *          name="assessmentMethodId",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="number")
 *      ),
 *     @OA\Response(
 *         response="200",
 *         description="Returns some sample category things",
 *         @OA\JsonContent()
 *     )
 * )
 * 
 */

 /**
 * @OA\Delete(
 *      path="/law5/api/chamber/{id}",
 *      tags={"chamber"},
 *      security={{"bearerAuth":{}}}, 
 *      @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="The id of chamber",
 *         required=true,
 *         @OA\Schema(type="number")
 *      ),
 *     @OA\Response(
 *         response="200",
 *         description="Returns some sample category things",
 *         @OA\JsonContent()
 *     )
 * )
 * 
 */
/**
 * @OA\Post(
 *      path="/law5/api/employee",
 *      tags={"Employee"},
 *      security={{"bearerAuth":{}}}, 
 *      @OA\Parameter(
 *          name="name",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="string")
 *      ),
 *      @OA\Parameter(
 *          name="email",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="string")
 *      ),
 *      @OA\Parameter(
 *          name="mobile",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="number")
 *      ),
 *      @OA\Parameter(
 *          name="roleKey",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="string")
 *      ),
 *      @OA\Parameter(
 *          name="isActive",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="number")
 *      ),
 *      @OA\Parameter(
 *          name="chamberId",
 *          in="query",
 *          required=false, 
 *         @OA\Schema(type="number")
 *      ),
 *       @OA\Parameter(
 *          name="sectors",
 *          in="query",
 *          required=false, 
 *        @OA\Schema( 
 *              type="array", 
 *              @OA\Items( type="enum", enum={1,2,3} ),
 *          ),
 *          style="form" 
 *      ),
 *     @OA\Response(
 *         response="200",
 *         description="Returns some sample category things",
 *         @OA\JsonContent()
 *     )
 * )
 * 
 */

 /**
 * @OA\Put(
 *      path="/law5/api/employee/{empID}",
 *      tags={"Employee"},
 *      security={{"bearerAuth":{}}}, 
 *      @OA\Parameter(
 *         name="empID",
 *         in="path",
 *         description="The employee id",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *      @OA\Parameter(
 *          name="name",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="string")
 *      ),
 *      @OA\Parameter(
 *          name="email",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="string")
 *      ),
 *      @OA\Parameter(
 *          name="mobile",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="number")
 *      ),
 *      @OA\Parameter(
 *          name="roleKey",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="string")
 *      ),
 *      @OA\Parameter(
 *          name="isActive",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="number")
 *      ),
 *      @OA\Parameter(
 *          name="chamberId",
 *          in="query",
 *          required=false, 
 *         @OA\Schema(type="number")
 *      ),
 *       @OA\Parameter(
 *          name="sectors",
 *          in="query",
 *          required=false, 
 *        @OA\Schema( 
 *              type="array", 
 *              @OA\Items( type="enum", enum={1,2,3} ),
 *          ),
 *          style="form" 
 *      ),
 *     @OA\Response(
 *         response="200",
 *         description="Returns some sample category things",
 *         @OA\JsonContent()
 *     )
 * )
 * 
 */
/**
 * @OA\Get(
 *     path="/law5/api/employee/{id}",
 *     security={{"bearerAuth":{}}}, 
 *     tags={"Employee"},
 *      @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="The employee id",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Returns some sample category things",
 *         @OA\JsonContent()
 *     )
 * )
 */
/**
 * @OA\Get(
 *     path="/law5/api/employee",
 *     security={{"bearerAuth":{}}}, 
 *     tags={"Employee"},
 *     @OA\Response(
 *         response="200",
 *         description="Returns some sample category things",
 *         @OA\JsonContent()
 *     )
 * )
 */

 /**
 * @OA\Get(
 *     path="/law5/api/getMostExpensiveMaterialsImported",
 *     security={{"bearerAuth":{}}}, 
 *     tags={"report"},
 *     @OA\Response(
 *         response="200",
 *         description="Returns Most Expensive Materials Imported",
 *         @OA\JsonContent()
 *     )
 * )
 */
 /**
 * @OA\Get(
 *     path="/law5/api/getMostExpensiveMaterialsLocal",
 *     security={{"bearerAuth":{}}}, 
 *     tags={"report"},
 *     @OA\Response(
 *         response="200",
 *         description="Returns Most Expensive Materials Local",
 *         @OA\JsonContent()
 *     )
 * )
 */

 /**
 * @OA\Get(
 *     path="/law5/api/requestCountByManufacturingType",
 *     security={{"bearerAuth":{}}}, 
 *     tags={"report"},
 *      @OA\Parameter(
 *         name="statusId",
 *         in="query",
 *         description="The status id",
 *         required=false,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Returns some sample category things",
 *         @OA\JsonContent()
 *     )
 * )
 */

/**
 * @OA\Get(
 *     path="/law5/api/applicantsWithIssuedCertificates",
 *     security={{"bearerAuth":{}}}, 
 *     tags={"report"},
 *      @OA\Parameter(
 *         name="certificateTypeId",
 *         in="query",
 *         description="The certificate Type Id",
 *         required=false,
 *         @OA\Schema(type="integer")
 *     ),
 *         @OA\Parameter(
 *         name="sectorId",
 *         in="query",
 *         description="The status id",
 *         required=false,
 *         @OA\Schema(type="integer")
 *     ),
 *         @OA\Parameter(
 *         name="chamberId",
 *         in="query",
 *         description="The chamber Id",
 *         required=false,
 *         @OA\Schema(type="integer")
 *     ),
 *         @OA\Parameter(
 *         name="governorateId",
 *         in="query",
 *         description="The governorate Id",
 *         required=false,
 *         @OA\Schema(type="integer")
 *     ),
 *      @OA\Response(
 *         response="200",
 *         description="Returns some sample category things",
 *         @OA\JsonContent()
 *     )
 * )
 */


 /**
 * @OA\Get(
 *     path="/law5/api/certificatedProducts",
 *     security={{"bearerAuth":{}}}, 
 *     tags={"report"},
 *      @OA\Parameter(
 *         name="applicantId",
 *         in="query",
 *         description="The applicant Id",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *      @OA\Parameter(
 *         name="certificateTypeId",
 *         in="query",
 *         description="The certificate Type Id",
 *         required=false,
 *         @OA\Schema(type="integer")
 *     ),
 *         @OA\Parameter(
 *         name="sectorId",
 *         in="query",
 *         description="The status id",
 *         required=false,
 *         @OA\Schema(type="integer")
 *     ),
 *         @OA\Parameter(
 *         name="chamberId",
 *         in="query",
 *         description="The chamber Id",
 *         required=false,
 *         @OA\Schema(type="integer")
 *     ),
 *         @OA\Parameter(
 *         name="governorateId",
 *         in="query",
 *         description="The governorate Id",
 *         required=false,
 *         @OA\Schema(type="integer")
 *     ),
 *         @OA\Parameter(
 *         name="productName",
 *         in="query",
 *         description="The product Name ",
 *         required=false,
 *         @OA\Schema(type="string")
 *     ),
 *         @OA\Parameter(
 *         name="companyName",
 *         in="query",
 *         description="The company Name ",
 *         required=false,
 *         @OA\Schema(type="string")
 *     ),
 *      @OA\Response(
 *         response="200",
 *         description="Returns some sample category things",
 *         @OA\JsonContent()
 *     )
 * )
 */

 /**
 * @OA\Get(
 *     path="/law5/api/certificatesCount",
 *     security={{"bearerAuth":{}}}, 
 *     tags={"report"},
 *      @OA\Parameter(
 *         name="groupBy",
 *         in="query",
 *         description=" groupBy",
 *         required=true,
 *         @OA\Schema(type="string")
 *     ),
 *      @OA\Parameter(
 *         name="fromDate",
 *         in="query",
 *         description="The date foramte m/d/Y",
 *         required=false,
 *         @OA\Schema(type="date")
 *     ),
 *         @OA\Parameter(
 *         name="toDate",
 *         in="query",
 *         description="The date foramte m/d/Y",
 *         required=false,
 *         @OA\Schema(type="date")
 *     ),
 *      @OA\Response(
 *         response="200",
 *         description="Returns some sample category things",
 *         @OA\JsonContent()
 *     )
 * )
 */

 /**
 * @OA\Get(
 *     path="/law5/api/expensesBySector",
 *     security={{"bearerAuth":{}}}, 
 *     tags={"report"},
 *      @OA\Parameter(
 *         name="expensesItem",
 *         in="query",
 *         description=" expensesItem should be one of [ 
 *          powerResources,localSpareParts,importedSpareParts,wages,annualDepreciation,researchAndDevelopment,marketingExpenses,
 *          administrativeExpenses,localComponents,localPackagingComponents,importedComponents,importedPackagingComponents]",
 *         required=true,
 *         @OA\Schema(type="string")
 *     ),
 *      @OA\Parameter(
 *         name="sectorId",
 *         in="query",
 *         description="The sectorId",
 *         required=false,
 *         @OA\Schema(type="integer")
 *     ),
 *      @OA\Response(
 *         response="200",
 *         description="Returns some sample category things",
 *         @OA\JsonContent()
 *     )
 * )
 */

/**
 * @OA\Get(
 *     path="/law5/api/expensesBySectorKeys",
 *     security={{"bearerAuth":{}}}, 
 *     tags={"report"},
 *      @OA\Response(
 *         response="200",
 *         description="Returns some sample category things",
 *         @OA\JsonContent()
 *     )
 * )
 */

/**
 * @OA\Get(
 *     path="/law5/api/requestActions/{id}",
 *     security={{"bearerAuth":{}}}, 
 *     tags={"Action"},
 *      @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="The request id",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Returns some sample category things",
 *         @OA\JsonContent()
 *     )
 * )
 */

 /**
 * @OA\Get(
 *     path="/law5/api/requestLastAction/{id}",
 *     security={{"bearerAuth":{}}}, 
 *     tags={"Action"},
 *      @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="The request id",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Returns some sample category things",
 *         @OA\JsonContent()
 *     )
 * )
 */

 /**
 * @OA\Get(
 *     path="/law5/api/assessment/{id}",
 *     security={{"bearerAuth":{}}}, 
 *     tags={"assessment"},
 *      @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="The assessment id",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Returns some sample category things",
 *         @OA\JsonContent()
 *     )
 * )
 */
 /**
 * @OA\Get(
 *     path="/law5/api/assessment_request_id/{request_id}",
 *     security={{"bearerAuth":{}}}, 
 *     tags={"assessment"},
 *      @OA\Parameter(
 *         name="request_id",
 *         in="path",
 *         description="The request id",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Returns some sample category things",
 *         @OA\JsonContent()
 *     )
 * )
 */

 /**
 * @OA\Post(
 *      path="/law5/api/applicantRequest",
 *      tags={"request"},
 *      security={{"bearerAuth":{}}}, 
 *      @OA\Parameter(
 *          name="sectorId",
 *          in="query",
 *          required=false, 
 *         @OA\Schema(type="number")
 *      ), 
 *      @OA\Parameter(
 *          name="assessmentId",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="number")
 *      ),
 *      @OA\Parameter(
 *          name="sectionId",
 *          in="query",
 *          required=false, 
 *         @OA\Schema(type="number")
 *      ),
 *      @OA\Parameter(
 *          name="representativeName",
 *          in="query",
 *          required=false, 
 *         @OA\Schema(type="string")
 *      ),
 *      @OA\Parameter(
 *          name="representativeNationalId",
 *          in="query",
 *          required=false, 
 *         @OA\Schema(type="number")
 *      ),
 *      @OA\Parameter(
 *          name="representativeTypeId",
 *          in="query",
 *          required=false, 
 *         @OA\Schema(type="number")
 *      ),
 *      @OA\Parameter(
 *          name="representativeDelegationNumber",
 *          in="query",
 *          required=false, 
 *         @OA\Schema(type="number")
 *      ),
 *       @OA\Parameter(
 *          name="representativeDelegationIssuedBy",
 *          in="query",
 *          required=false, 
 *         @OA\Schema(type="number")
 *      ),
 *      @OA\Parameter(
 *          name="representativeMailingAddress",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="string")
 *      ),
 *      @OA\Parameter(
 *          name="industrialRegistry",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="number")
 *      ),
 *      @OA\Parameter(
 *          name="representativeFax",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="number")
 *      ),
 *       @OA\Parameter(
 *          name="representativeTelephone",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="number")
 *      ),
 *      @OA\Parameter(
 *          name="representativeMobile",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="number")
 *      ),
 *       @OA\Parameter(
 *          name="representativeEmail",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="string")
 *      ),
 *      @OA\Parameter(
 *          name="isOriginalsReceived",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="number")
 *      ),
 *      @OA\Parameter(
 *          name="isChamberMember",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="number")
 *      ),
 *      @OA\Parameter(
 *          name="isIDAFeesPaid",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="number")
 *      ),
 *       @OA\Parameter(
 *          name="isFEIFeesPaid",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="number")
 *      ),
 *       @OA\Parameter(
 *          name="isSubscriptionFeesPaid",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="number")
 *      ), 
 *       @OA\Parameter(
 *          name="exportSupportCertificateRequested",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="number")
 *      ),
 *       @OA\Parameter(
 *          name="governmentTendersCertificateRequested",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="number")
 *      ),
 *     @OA\Response(
 *         response="200",
 *         description="Returns some sample category things",
 *         @OA\JsonContent()
 *     )
 * )
 * 
 */


 /**
 * @OA\Put(
 *      path="/law5/api/applicantRequest/{id}",
 *      tags={"request"},
 *      security={{"bearerAuth":{}}},
 *       @OA\Parameter(
 *          name="id",
 *          in="path",
 *          required=true, 
 *         @OA\Schema(type="number")
 *      ),   
 *      @OA\Parameter(
 *          name="assessmentId",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="number")
 *      ),
 *      @OA\Parameter(
 *          name="sectionId",
 *          in="query",
 *          required=false, 
 *         @OA\Schema(type="number")
 *      ),
 *      @OA\Parameter(
 *          name="representativeName",
 *          in="query",
 *          required=false, 
 *         @OA\Schema(type="string")
 *      ),
 *      @OA\Parameter(
 *          name="representativeNationalId",
 *          in="query",
 *          required=false, 
 *         @OA\Schema(type="number")
 *      ),
 *      @OA\Parameter(
 *          name="representativeTypeId",
 *          in="query",
 *          required=false, 
 *         @OA\Schema(type="number")
 *      ),
 *      @OA\Parameter(
 *          name="representativeDelegationNumber",
 *          in="query",
 *          required=false, 
 *         @OA\Schema(type="number")
 *      ),
 *       @OA\Parameter(
 *          name="representativeDelegationIssuedBy",
 *          in="query",
 *          required=false, 
 *         @OA\Schema(type="number")
 *      ),
 *      @OA\Parameter(
 *          name="representativeMailingAddress",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="string")
 *      ),
 *      @OA\Parameter(
 *          name="industrialRegistry",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="number")
 *      ),
 *      @OA\Parameter(
 *          name="representativeFax",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="number")
 *      ),
 *       @OA\Parameter(
 *          name="representativeTelephone",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="number")
 *      ),
 *      @OA\Parameter(
 *          name="representativeMobile",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="number")
 *      ),
 *       @OA\Parameter(
 *          name="representativeEmail",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="string")
 *      ),
 *      @OA\Parameter(
 *          name="isOriginalsReceived",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="number")
 *      ),
 *      @OA\Parameter(
 *          name="isChamberMember",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="number")
 *      ),
 *      @OA\Parameter(
 *          name="isIDAFeesPaid",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="number")
 *      ),
 *       @OA\Parameter(
 *          name="isFEIFeesPaid",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="number")
 *      ),
 *       @OA\Parameter(
 *          name="isSubscriptionFeesPaid",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="number")
 *      ), 
 *       @OA\Parameter(
 *          name="exportSupportCertificateRequested",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="number")
 *      ),
 *       @OA\Parameter(
 *          name="governmentTendersCertificateRequested",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="number")
 *      ),
 *     @OA\Response(
 *         response="200",
 *         description="Returns some sample category things",
 *         @OA\JsonContent()
 *     )
 * )
 * 
 */


 /**
 * @OA\Get(
 *     path="/law5/api/request/{request_id}",
 *     security={{"bearerAuth":{}}}, 
 *     tags={"request"},
 *      @OA\Parameter(
 *         name="request_id",
 *         in="path",
 *         description="The request id",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Returns some sample category things",
 *         @OA\JsonContent()
 *     )
 * )
 */

/**
 * @OA\Put(
 *     path="/law5/api/resend_request",
 *     security={{"bearerAuth":{}}}, 
 *     tags={"request"},
 *      @OA\Parameter(
 *         name="requestId",
 *         in="query",
 *         description="The request id",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *         @OA\Parameter(
 *         name="comment",
 *         in="query",
 *         description="The comment",
 *         required=false,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Returns some sample category things",
 *         @OA\JsonContent()
 *     )
 * )
 */

 /**
 * @OA\Put(
 *     path="/law5/api/assign",
 *     security={{"bearerAuth":{}}}, 
 *     tags={"request"},
 *      @OA\Parameter(
 *         name="requestId",
 *         in="query",
 *         description="The request id",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *         @OA\Parameter(
 *         name="employeeId",
 *         in="query",
 *         description="The employee Id ",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *         @OA\Parameter(
 *         name="comment",
 *         in="query",
 *         description="The comment",
 *         required=false,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Returns some sample category things",
 *         @OA\JsonContent()
 *     )
 * )
 */

  /**
 * @OA\Put(
 *     path="/law5/api/ConfirmRequest",
 *     security={{"bearerAuth":{}}}, 
 *     tags={"request"},
 *      @OA\Parameter(
 *         name="requestId",
 *         in="query",
 *         description="The request id",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Returns some sample category things",
 *         @OA\JsonContent()
 *     )
 * )
 */

   /**
 * @OA\Put(
 *     path="/law5/api/returnToEmployee",
 *     security={{"bearerAuth":{}}}, 
 *     tags={"request"},
 *      @OA\Parameter(
 *         name="requestId",
 *         in="query",
 *         description="The request id",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ), 
 *          @OA\Parameter(
 *         name="comment",
 *         in="query",
 *         description="The comment",
 *         required=false,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Returns some sample category things",
 *         @OA\JsonContent()
 *     )
 * )
 */

  /**
 * @OA\Put(
 *     path="/law5/api/VerifyMembership",
 *     security={{"bearerAuth":{}}}, 
 *     tags={"request"},
 *      @OA\Parameter(
 *         name="requestId",
 *         in="query",
 *         description="The request id",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ), 
 *         @OA\Parameter(
 *         name="isChamberMember",
 *         in="query",
 *         description="The isChamberMember",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *         @OA\Parameter(
 *         name="isSubscriptionFeesPaid",
 *         in="query",
 *         description="The isSubscriptionFeesPaid",
 *         required=false,
 *         @OA\Schema(type="integer")
 *      ),
 *         @OA\Parameter(
 *         name="comment",
 *         in="query",
 *         description="The comment",
 *         required=false,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Returns some sample category things",
 *         @OA\JsonContent()
 *     )
 * )
 */

/**
 * @OA\Put(
 *     path="/law5/api/ChangeChamber",
 *     security={{"bearerAuth":{}}}, 
 *     tags={"request"},
 *      @OA\Parameter(
 *         name="requestId",
 *         in="query",
 *         description="The request id",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ), 
 *         @OA\Parameter(
 *         name="chamberId",
 *         in="query",
 *         description="The chamberId",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Returns some sample category things",
 *         @OA\JsonContent()
 *     )
 * )
 */


/**
 * @OA\Put(
 *     path="/law5/api/openRequest",
 *     security={{"bearerAuth":{}}}, 
 *     tags={"request"},
 *      @OA\Parameter(
 *         name="requestId",
 *         in="query",
 *         description="The request id",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ), 
 *     @OA\Response(
 *         response="200",
 *         description="Returns some sample category things",
 *         @OA\JsonContent()
 *     )
 * )
 */

 /**
 * @OA\Put(
 *     path="/law5/api/closeRequest",
 *     security={{"bearerAuth":{}}}, 
 *     tags={"request"},
 *      @OA\Parameter(
 *         name="requestId",
 *         in="query",
 *         description="The request id",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ), 
 *     @OA\Response(
 *         response="200",
 *         description="Returns some sample category things",
 *         @OA\JsonContent()
 *     )
 * )
 */

/**
 * @OA\Get(
 *     path="/law5/api/pdf",
 *     security={{"bearerAuth":{}}}, 
 *     tags={"certificate"},
 *      @OA\Parameter(
 *         name="cert_id",
 *         in="query",
 *         description="The cert id",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ), 
 *     @OA\Response(
 *         response="200",
 *         description="Returns some sample category things",
 *         @OA\JsonContent()
 *     )
 * )
 */

/**
 * @OA\Put(
 *     path="/law5/api/saveReview",
 *     security={{"bearerAuth":{}}}, 
 *     tags={"request"},
 *      @OA\Parameter(
 *         name="requestId",
 *         in="query",
 *         description="The request id",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ), 
 *         @OA\Parameter(
 *         name="isOriginalsReceived",
 *         in="query",
 *         description="The isOriginalsReceived",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 * *         @OA\Parameter(
 *         name="isAutoSave",
 *         in="query",
 *         description="The isAutoSave",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 * *         @OA\Parameter(
 *         name="closeAfterSave",
 *         in="query",
 *         description="The closeAfterSave",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 * *         @OA\Parameter(
 *         name="comment",
 *         in="query",
 *         description="The comment",
 *         required=false,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Returns some sample category things",
 *         @OA\JsonContent()
 *     )
 * )
 */

 /**
 * @OA\Put(
 *     path="/law5/api/RespondToRequest",
 *     security={{"bearerAuth":{}}}, 
 *     tags={"request"},
 *      @OA\Parameter(
 *         name="requestId",
 *         in="query",
 *         description="The request id",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ), 
 *         @OA\Parameter(
 *         name="actionId",
 *         in="query",
 *         description="The actionId",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *         @OA\Parameter(
 *         name="comment",
 *         in="query",
 *         description="The comment",
 *         required=false,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Returns some sample category things",
 *         @OA\JsonContent()
 *     )
 * )
 */



 /**
 * @OA\Get(
 *     path="/law5/api/request_status",
 *     security={{"bearerAuth":{}}}, 
 *     tags={"request"},
 *     @OA\Response(
 *         response="200",
 *         description="Returns some sample category things",
 *         @OA\JsonContent()
 *     )
 * )
 */

  /**
 * @OA\Get(
 *     path="/law5/api/send",
 *     tags={"mail"},
 *     @OA\Response(
 *         response="200",
 *         description="Returns some sample category things",
 *         @OA\JsonContent()
 *     )
 * )
 */

  /**
 * @OA\Get(
 *     path="/law5/api/getLatestReturnAction/{request_id}",
 *     security={{"bearerAuth":{}}}, 
 *     tags={"request"},
 *      @OA\Parameter(
 *         name="request_id",
 *         in="path",
 *         description="The request id",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Returns some sample category things",
 *         @OA\JsonContent()
 *     )
 * )
 */

 /**
 * @OA\Post(
 *      path="/law5/api/get_request",
 *      tags={"request"},
 *      security={{"bearerAuth":{}}}, 
 *      @OA\Parameter(
 *          name="statusId",
 *          in="query",
 *          required=false, 
 *         @OA\Schema(type="number")
 *      ),
 *      @OA\Parameter(
 *          name="assignedTo",
 *          in="query",
 *          required=false, 
 *         @OA\Schema(type="number")
 *      ),
 *      @OA\Parameter(
 *          name="pageSize",
 *          in="query",
 *          required=false, 
 *         @OA\Schema(type="number")
 *      ),
 *      @OA\Parameter(
 *          name="pageIndex",
 *          in="query",
 *          required=false, 
 *         @OA\Schema(type="number")
 *      ),
 *      @OA\Parameter(
 *          name="sortColumn",
 *          in="query",
 *          required=false, 
 *         @OA\Schema(type="number")
 *      ),
 *      @OA\Parameter(
 *          name="sortDirection",
 *          in="query",
 *          required=false, 
 *         @OA\Schema(type="number")
 *      ),
 *       @OA\Parameter(
 *          name="searchText",
 *          in="query",
 *          required=false, 
 *         @OA\Schema(type="string")
 *      ),
 *     @OA\Response(
 *         response="200",
 *         description="Returns some sample category things",
 *         @OA\JsonContent()
 *     )
 * )
 * 
 */

 /**
 * @OA\Get(
 *     path="/law5/api/applicant_assessments",
 *     security={{"bearerAuth":{}}}, 
 *     tags={"assessment"},
 *     @OA\Response(
 *         response="200",
 *         description="Returns some sample category things",
 *         @OA\JsonContent()
 *     )
 * )
 */

 /**
 * @OA\Post(
 *      path="/law5/api/assessment",
 *      description="this for total assessment only",
 *      tags={"assessment"},
 *      security={{"bearerAuth":{}}}, 
 *      @OA\Parameter(
 *          name="productName",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="string")
 *      ),
 *      @OA\Parameter(
 *          name="chamberId",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="number")
 *      ),
 *      @OA\Parameter(
 *          name="manufactoringByOthers",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="number")
 *      ),
 *      @OA\Parameter(
 *          name="isTotals",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="number")
 *      ),
 *      @OA\Parameter(
 *          name="annualProductionCapacity",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="string")
 *      ),
 *      @OA\Parameter(
 *          name="powerResources",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="string")
 *      ),
 *      @OA\Parameter(
 *          name="localSpareParts",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="string")
 *      ),
 *      @OA\Parameter(
 *          name="importedSpareParts",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="string")
 *      ),
 *      @OA\Parameter(
 *          name="researchAndDevelopment",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="string")
 *      ),
 *      @OA\Parameter(
 *          name="wages",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="string")
 *      ),
 *      @OA\Parameter(
 *          name="annualDepreciation",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="string")
 *      ),
 *      @OA\Parameter(
 *          name="administrativeExpenses",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="string")
 *      ),
 *      @OA\Parameter(
 *          name="marketingExpenses",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="string")
 *      ),
 *      @OA\Parameter(
 *          name="otherExpenses",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="string")
 *      ),
 *      @OA\Parameter(
 *          name="localComponentsTotals",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="string")
 *      ),
 *      @OA\Parameter(
 *          name="localPackagingComponentsTotals",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="string")
 *      ),
 *      @OA\Parameter(
 *          name="importedComponentsTotals",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="string")
 *      ),
 *      @OA\Parameter(
 *          name="importedPackagingComponentsTotals",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="string")
 *      ),
 *     @OA\Response(
 *         response="200",
 *         description="Returns some sample category things",
 *         @OA\JsonContent()
 *     )
 * )
 * 
 */
/**
 * @OA\Put(
 *      path="/law5/api/assessment/{id}",
 *      description="this for total assessment only",
 *      tags={"assessment"},
 *      security={{"bearerAuth":{}}}, 
 * *      @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="The assessment id",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *      @OA\Parameter(
 *          name="productName",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="string")
 *      ),
 *      @OA\Parameter(
 *          name="chamberId",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="number")
 *      ),
 *      @OA\Parameter(
 *          name="manufactoringByOthers",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="number")
 *      ),
 *      @OA\Parameter(
 *          name="isTotals",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="number")
 *      ),
 *      @OA\Parameter(
 *          name="annualProductionCapacity",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="string")
 *      ),
 *      @OA\Parameter(
 *          name="powerResources",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="string")
 *      ),
 *      @OA\Parameter(
 *          name="localSpareParts",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="string")
 *      ),
 *      @OA\Parameter(
 *          name="importedSpareParts",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="string")
 *      ),
 *      @OA\Parameter(
 *          name="researchAndDevelopment",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="string")
 *      ),
 *      @OA\Parameter(
 *          name="wages",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="string")
 *      ),
 *      @OA\Parameter(
 *          name="annualDepreciation",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="string")
 *      ),
 *      @OA\Parameter(
 *          name="administrativeExpenses",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="string")
 *      ),
 *      @OA\Parameter(
 *          name="marketingExpenses",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="string")
 *      ),
 *      @OA\Parameter(
 *          name="otherExpenses",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="string")
 *      ),
 *      @OA\Parameter(
 *          name="localComponentsTotals",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="string")
 *      ),
 *      @OA\Parameter(
 *          name="localPackagingComponentsTotals",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="string")
 *      ),
 *      @OA\Parameter(
 *          name="importedComponentsTotals",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="string")
 *      ),
 *      @OA\Parameter(
 *          name="importedPackagingComponentsTotals",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="string")
 *      ),
 *     @OA\Response(
 *         response="200",
 *         description="Returns some sample category things",
 *         @OA\JsonContent()
 *     )
 * )
 * 
 */

  /**
 * @OA\Get(
 *     path="/law5/api/settings",
 *     security={{"bearerAuth":{}}}, 
 *     tags={"setting"},
 *     @OA\Response(
 *         response="200",
 *         description="Returns some sample category things",
 *         @OA\JsonContent()
 *     )
 * )
 */
 /**
 * @OA\Get(
 *     path="/law5/api/getEmailSettings",
 *     security={{"bearerAuth":{}}}, 
 *     tags={"setting"},
 *     @OA\Response(
 *         response="200",
 *         description="Returns some sample category things",
 *         @OA\JsonContent()
 *     )
 * )
 */

 /**
 * @OA\Put(
 *      path="/law5/api/settings",
 *      tags={"setting"},
 *      security={{"bearerAuth":{}}}, 
 *      @OA\Parameter(
 *          name="automaticAssignDelay",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="number")
 *      ),
 *      @OA\Parameter(
 *          name="automaticIDAApproveDelay",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="number")
 *      ),
 *      @OA\Parameter(
 *          name="law5CertificatePercentage",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="number")
 *      ),
 *      @OA\Parameter(
 *          name="exportFundPercentage",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="number")
 *      ),
 *      @OA\Parameter(
 *          name="executiveManagerName",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="string")
 *      ),
 *     @OA\Response(
 *         response="200",
 *         description="Returns some sample category things",
 *         @OA\JsonContent()
 *     )
 * )
 * 
 */

 /**
 * @OA\Put(
 *      path="/law5/api/saveEmailSettings",
 *      tags={"setting"},
 *      security={{"bearerAuth":{}}}, 
 *      @OA\Parameter(
 *          name="mailServer",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="string")
 *      ),
 *      @OA\Parameter(
 *          name="mailServerPort",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="string")
 *      ),
 *      @OA\Parameter(
 *          name="mailEnableSSL",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="string")
 *      ),
 *      @OA\Parameter(
 *          name="fromEmail",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="string")
 *      ),
 *     @OA\Parameter(
 *          name="fromEmailPassword",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="string")
 *      ),
 *     @OA\Response(
 *         response="200",
 *         description="Returns some sample category things",
 *         @OA\JsonContent()
 *     )
 * )
 * 
 */

 /**
 * @OA\Get(
 *     path="/law5/api/sector",
 *     tags={"sectors"},
 *     security={{"bearerAuth":{}}}, 
 *     @OA\Response(
 *         response="200",
 *         description="Returns some sample category things",
 *         @OA\JsonContent()
 *     )
 * )
 */

  /**
 * @OA\Put(
 *      path="/law5/api/sector/{id}",
 *      tags={"sectors"},
 *      security={{"bearerAuth":{}}}, 
 *      @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="The id of sector",
 *         required=true,
 *         @OA\Schema(type="number")
 *      ),
 *      @OA\Parameter(
 *          name="nameAr",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="string")
 *      ),
 *      @OA\Parameter(
 *          name="nameEn",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="string")
 *      ),
 *     @OA\Response(
 *         response="200",
 *         description="Returns some sample category things",
 *         @OA\JsonContent()
 *     )
 * )
 * 
 */
  /**
 * @OA\Post(
 *      path="/law5/api/sector",
 *      tags={"sectors"},
 *      security={{"bearerAuth":{}}}, 
 *      @OA\Parameter(
 *          name="nameAr",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="string")
 *      ),
 *      @OA\Parameter(
 *          name="nameEn",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="string")
 *      ),
 *     @OA\Response(
 *         response="200",
 *         description="Returns some sample category things",
 *         @OA\JsonContent()
 *     )
 * )
 * 
 */

  /**
 * @OA\Delete(
 *      path="/law5/api/sector/{id}",
 *      tags={"sectors"},
 *      security={{"bearerAuth":{}}}, 
 *      @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="The id of sector",
 *         required=true,
 *         @OA\Schema(type="number")
 *      ),
 *     @OA\Response(
 *         response="200",
 *         description="Returns some sample category things",
 *         @OA\JsonContent()
 *     )
 * )
 * 
 */





 
 /**
 * @OA\Get(
 *     path="/law5/api/unit",
 *     tags={"units"},
 *     security={{"bearerAuth":{}}}, 
 *     @OA\Response(
 *         response="200",
 *         description="Returns some sample category things",
 *         @OA\JsonContent()
 *     )
 * )
 */

  /**
 * @OA\Put(
 *      path="/law5/api/unit/{id}",
 *      tags={"units"},
 *      security={{"bearerAuth":{}}}, 
 *      @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="The id of unit",
 *         required=true,
 *         @OA\Schema(type="number")
 *      ),
 *      @OA\Parameter(
 *          name="nameAr",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="string")
 *      ),
 *      @OA\Parameter(
 *          name="nameEn",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="string")
 *      ),
 *     @OA\Response(
 *         response="200",
 *         description="Returns some sample category things",
 *         @OA\JsonContent()
 *     )
 * )
 * 
 */
  /**
 * @OA\Post(
 *      path="/law5/api/unit",
 *      tags={"units"},
 *      security={{"bearerAuth":{}}}, 
 *      @OA\Parameter(
 *          name="nameAr",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="string")
 *      ),
 *      @OA\Parameter(
 *          name="nameEn",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="string")
 *      ),
 *     @OA\Response(
 *         response="200",
 *         description="Returns some sample category things",
 *         @OA\JsonContent()
 *     )
 * )
 * 
 */

  /**
 * @OA\Delete(
 *      path="/law5/api/unit/{id}",
 *      tags={"units"},
 *      security={{"bearerAuth":{}}}, 
 *      @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="The id of unit",
 *         required=true,
 *         @OA\Schema(type="number")
 *      ),
 *     @OA\Response(
 *         response="200",
 *         description="Returns some sample category things",
 *         @OA\JsonContent()
 *     )
 * )
 * 
 */




  /**
 * @OA\Put(
 *      path="/law5/api/change_product_name",
 *      tags={"assessment"},
 *      security={{"bearerAuth":{}}}, 
 *      @OA\Parameter(
 *         name="requestId",
 *         in="query",
 *         description="The id of request",
 *         required=true,
 *         @OA\Schema(type="number")
 *      ),
 *      @OA\Parameter(
 *          name="newProductName",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="string")
 *      ),
 *     @OA\Response(
 *         response="200",
 *         description="Returns some sample category things",
 *         @OA\JsonContent()
 *     )
 * )
 * 
 */
  /**
 * @OA\Post(
 *      path="/law5/api/expired_and_notRenewed",
 *      tags={"certificate"},
 *      security={{"bearerAuth":{}}}, 
 *      @OA\Parameter(
 *          name="toDate",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="date")
 *      ),
 *     @OA\Response(
 *         response="200",
 *         description="Returns some sample category things",
 *         @OA\JsonContent()
 *     )
 * )
 * 
 */

 /**
 * @OA\Post(
 *      path="/law5/api/renewCertificate",
 *      tags={"certificate"},
 *      security={{"bearerAuth":{}}}, 
 *      @OA\Parameter(
 *          name="cert_id",
 *          in="query",
 *          required=true, 
 *         @OA\Schema(type="number")
 *      ),
 *     @OA\Response(
 *         response="200",
 *         description="Returns some sample category things",
 *         @OA\JsonContent()
 *     )
 * )
 * 
 */
