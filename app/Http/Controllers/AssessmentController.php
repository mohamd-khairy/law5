<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use App\Model\Assessment;
use App\Model\RequestModel;
use App\Model\Component;
use App\Model\RequestStatus;
use Carbon\Carbon;


class AssessmentController extends Controller
{

    public function detailedAssessment(Request $request)
    {
        $budget = $request['budget'] ?? [];
        $rules = [
            'budget'                          => 'required|array',
            'budget.annualProductionCapacity' => 'nullable|numeric|required_if:budget.manufactoringByOthers,false|min:1',
            'budget.powerResources'           => 'nullable|numeric|required_if:budget.manufactoringByOthers,false',
            'budget.localSpareParts'          => ['nullable', 'numeric', Rule::requiredIf(!array_key_exists('importedSpareParts', $budget) && ($budget['manufactoringByOthers'] ?? '') == false)],
            'budget.importedSpareParts'       => ['nullable', 'numeric', Rule::requiredIf(!array_key_exists('localSpareParts', $budget) && ($budget['manufactoringByOthers'] ?? '') == false)],
            'budget.researchAndDevelopment'   => 'nullable|numeric|required_if:budget.manufactoringByOthers,false',
            'budget.wages'                    => 'nullable|numeric|required_if:budget.manufactoringByOthers,false',
            'budget.annualDepreciation'       => 'nullable|numeric|required_if:budget.manufactoringByOthers,false',
            'budget.administrativeExpenses'   => 'nullable|numeric|required_if:budget.manufactoringByOthers,false',
            'budget.marketingExpenses'        => 'nullable|numeric|required_if:budget.manufactoringByOthers,false',
            'budget.customsFees'              => 'nullable|numeric|required_if:budget.manufactoringByOthers,false',
            'budget.otherExpenses'            => 'required|numeric',
            'budget.manufactoringByOthers'    => ['required', 'boolean'],

            'localComponents'               => 'array|required_without_all:localPackagingComponents,importedComponents,importedPackagingComponents',
            'localComponents.*.componentName'   => 'required|string',
            'localComponents.*.unit'            => 'required|string',
            'localComponents.*.quantity'        => 'required|numeric',
            'localComponents.*.unitPrice'       => 'required|numeric',
            'localComponents.*.supplier'        => 'nullable|string',

            'localPackagingComponents'              => 'array|required_without_all:localComponents,importedComponents,importedPackagingComponents',
            'localPackagingComponents.*.componentName'   => 'required|string',
            'localPackagingComponents.*.unit'            => 'required|string',
            'localPackagingComponents.*.quantity'        => 'required|numeric',
            'localPackagingComponents.*.unitPrice'       => 'required|numeric',
            'localPackagingComponents.*.supplier'        => 'nullable|string',

            'importedComponents'              => 'array|required_without_all:localPackagingComponents,localComponents,importedPackagingComponents',
            'importedComponents.*.componentName' => 'required|string',
            'importedComponents.*.unit'          => 'required|string',
            'importedComponents.*.quantity'      => 'required|numeric',
            'importedComponents.*.unitPrice'     => 'required|numeric',
            'importedComponents.*.rate'          => 'required|numeric',
            'importedComponents.*.CIF'           => 'nullable|numeric',
            'importedComponents.*.supplier'      => 'nullable|string',

            'importedPackagingComponents'              => 'array|required_without_all:localPackagingComponents,importedComponents,localComponents',
            'importedPackagingComponents.*.componentName' => 'required|string',
            'importedPackagingComponents.*.unit'          => 'required|string',
            'importedPackagingComponents.*.quantity'      => 'required|numeric',
            'importedPackagingComponents.*.unitPrice'     => 'required|numeric',
            'importedPackagingComponents.*.rate'          => 'required|numeric',
            'importedPackagingComponents.*.CIF'           => 'nullable|numeric',
            'importedPackagingComponents.*.supplier'      => 'nullable|string',
        ];
        $this->validate($request, $rules);
        $data = $request->post();

        return $this->CalcDetailed($data);
    }

    public function CalcDetailed($data)
    {
        $budget = $data['budget'] ?? [];
        //2
        $budgetElements = [];
        if ($budget['manufactoringByOthers']) {
            $budgetElements['local']['otherExpenses'] = $budget['otherExpenses'];
            $budgetElements['customsFees'] = 0;
            $budgetElements['imported'] = 0;
        } else {
            $annualProduction = $budget['annualProductionCapacity'];
            $budgetElements['local']['localSpareParts']        = $budget['localSpareParts'] / $annualProduction;        //$localSpareParts
            $budgetElements['local']['powerResources']         = $budget['powerResources'] / $annualProduction;         //$PowerWaterOilResources
            $budgetElements['local']['researchAndDevelopment'] = $budget['researchAndDevelopment'] / $annualProduction; //$researchAndDevelopment
            $budgetElements['local']['wages']                  = $budget['wages'] / $annualProduction;                  //$wages
            $budgetElements['local']['annualDepreciation']     = $budget['annualDepreciation'] / $annualProduction;     //$AnnualDepreciation
            $budgetElements['local']['administrativeExpenses'] = $budget['administrativeExpenses'] / $annualProduction; //$administrativeExpenses
            $budgetElements['local']['marketingExpenses']      = $budget['marketingExpenses'] / $annualProduction;      //$marketingExpenses
            $budgetElements['local']['otherExpenses']          = $budget['otherExpenses'] / $annualProduction;          //$otherExpenses
            $budgetElements['customsFees']                     = $budget['customsFees'] / $annualProduction;          //$custom fees
            
            $budgetElements['imported'] = $budget['importedSpareParts'] / $annualProduction;
        }

        $totalLocalBudgetElements = array_sum($budgetElements['local']);

        //3
        $localComponentPriceArray = array_map(function ($component) {
            $componentPrice = ($component['unitPrice']) * $component['quantity'];
            return $componentPrice;
        }, $data['localComponents']);
        $localComponentsSum = array_sum($localComponentPriceArray);

        //4

        $localComponentPackagingPriceArray = array_map(function ($component) {
            $componentPrice = $component['unitPrice'] * $component['quantity'];
            return $componentPrice;
        }, $data['localPackagingComponents']);
        $localPackagingComponentsSum = array_sum($localComponentPackagingPriceArray);

        //5
        $importedComponentPriceArray = array_map(function ($component) {
            $componentPrice = ($component['unitPrice'] * $component['rate'] * $component['quantity']) + $component['CIF'];
            return $componentPrice;
        }, $data['importedComponents']);
        $importedComponentSum = array_sum($importedComponentPriceArray);

        //6
        $importedComponentPackagingPriceArray = array_map(function ($component) {
            $componentPrice = ($component['unitPrice'] * $component['rate'] * $component['quantity']) + $component['CIF'];
            return $componentPrice;
        }, $data['importedPackagingComponents']);
        $importedPackagingComponentsSum = array_sum($importedComponentPackagingPriceArray);

        //calc 1
        $totalLocalComponentCost =
            $localComponentsSum            // sum of tab imported components
            + $localPackagingComponentsSum // sum of tab imported packaging
            + $totalLocalBudgetElements;        // budget

        //calc 2
        $totalImportedComponentCost =
            $importedComponentSum             // sum of tab imported components
            + $importedPackagingComponentsSum // sum of tab imported packaging
            + $budgetElements['imported']            // from budget
            + $budgetElements['customsFees'];
        //calc 3
        $ProductPrice = $totalLocalComponentCost + $totalImportedComponentCost;
        $localCostPercentage = ($ProductPrice == 0) ? 0 : (($totalLocalComponentCost / $ProductPrice) * 100);

        return [
            'assessmentScorePercent'      => $localCostPercentage,
            'localComponentsCostTotal'    => $totalLocalComponentCost,
            'ImportedComponentsCostTotal' => $totalImportedComponentCost,
        ];
    }

    public function GenerateDetailedAssessment($assessment, $localComponents, $localPackagingComponents, $importedComponents, $importedPackagingComponents)
    {
        $obj = [
            "budget" => [
                "manufactoringByOthers" => $assessment->manufactoringByOthers,
                "annualProductionCapacity" => $assessment->annualProductionCapacity,
                "powerResources" => $assessment->powerResources,
                "localSpareParts" => $assessment->localSpareParts,
                "importedSpareParts" => $assessment->importedSpareParts,
                "researchAndDevelopment" => $assessment->researchAndDevelopment,
                "wages" => $assessment->wages,
                "annualDepreciation" => $assessment->annualDepreciation,
                "administrativeExpenses" => $assessment->administrativeExpenses,
                "marketingExpenses" => $assessment->marketingExpenses,
                "otherExpenses" => $assessment->otherExpenses,
                "customsFees" => $assessment->customsFees ?? 0,
            ],
            "localComponents" => $localComponents->toArray(),
            "localPackagingComponents" => $localPackagingComponents->toArray(),
            "importedComponents" => $importedComponents->toArray(),
            "importedPackagingComponents" => $importedPackagingComponents->toArray(),
        ];
        return $this->CalcDetailed($obj);
    }

    public function totalsAssessment(Request $request)
    {
        $data = $request->post();
        $rules = [
            'manufactoringByOthers'               => 'boolean|required',
            'localComponents'                     => 'nullable|numeric|required_without:importedComponents',
            'localPackagingComponents'            => 'nullable|numeric|required_without:importedPackagingComponents',
            'localSpareParts'                     => ['nullable', 'numeric', Rule::requiredIf(!array_key_exists('importedSpareParts', $data) && $data['manufactoringByOthers'] == false)],
            'importedComponents'                  => 'nullable|numeric|required_without:localComponents',
            'importedPackagingComponents'         => 'nullable|numeric|required_without:localPackagingComponents',
            'importedSpareParts'                  => ['nullable', 'numeric', Rule::requiredIf(!array_key_exists('localSpareParts', $data) && $data['manufactoringByOthers'] == false)],
            'powerResources'                      => 'numeric|nullable|required_if:manufactoringByOthers,false',
            'researchAndDevelopment'              => 'numeric|nullable|required_if:manufactoringByOthers,false',
            'wages'                               => 'numeric|nullable|required_if:manufactoringByOthers,false',
            'annualDepreciation'                  => 'numeric|nullable|required_if:manufactoringByOthers,false',
            'administrativeExpenses'              => 'numeric|nullable|required_if:manufactoringByOthers,false',
            'marketingExpenses'                   => 'numeric|nullable|required_if:manufactoringByOthers,false',
            'otherExpenses'                       => 'numeric|required',
            'customsFees'                         => 'numeric|nullable',
        ];
        $this->validate($request, $rules);

        if ($data['manufactoringByOthers']) {
            $costOfFixedAndVariableItems = ($data['otherExpenses'] ?? 0);
        } else {
            $costOfFixedAndVariableItems = ($data['localSpareParts'] ?? 0)          //$localSpareParts
                + ($data['powerResources'] ?? 0)         //$PowerWaterOilResources
                + ($data['researchAndDevelopment'] ?? 0) //$researchAndDevelopment
                + ($data['wages'] ?? 0)                  //$wages
                + ($data['annualDepreciation'] ?? 0)     //$AnnualDepreciation
                + ($data['administrativeExpenses'] ?? 0) //$administrativeExpenses
                + ($data['marketingExpenses'] ?? 0)      //$marketingExpenses
                + ($data['otherExpenses'] ?? 0);         //$otherExpenses
        }

        $localInput = ($data['localComponents'] ?? 0)
            + ($data['localPackagingComponents'] ?? 0);
        $importedInput = ($data['importedComponents'] ?? 0)
            + ($data['importedPackagingComponents'] ?? 0)
            + ($data['importedSpareParts'] ?? 0);

        $totalLocalComponentCost = $localInput + $costOfFixedAndVariableItems;
        $totalProductCost = $totalLocalComponentCost + $importedInput;
        $localCostPercentage = ($totalProductCost == 0) ? 0 : (($totalLocalComponentCost / $totalProductCost) * 100);
        return [
            'assessmentScorePercent'      => $localCostPercentage,
            'localComponentsCostTotal'    => $totalLocalComponentCost,
            'ImportedComponentsCostTotal' => $importedInput,
        ];
    }

    public function GeneratetotalsAssessment($data)
    {
        if ($data['manufactoringByOthers']) {
            $costOfFixedAndVariableItems = ($data['otherExpenses'] ?? 0);
        } else {
            $costOfFixedAndVariableItems = ($data['localSpareParts'] ?? 0)          //$localSpareParts
                + ($data['powerResources'] ?? 0)         //$PowerWaterOilResources
                + ($data['researchAndDevelopment'] ?? 0) //$researchAndDevelopment
                + ($data['wages'] ?? 0)                  //$wages
                + ($data['annualDepreciation'] ?? 0)     //$AnnualDepreciation
                + ($data['administrativeExpenses'] ?? 0) //$administrativeExpenses
                + ($data['marketingExpenses'] ?? 0)      //$marketingExpenses
                + ($data['otherExpenses'] ?? 0);         //$otherExpenses
        }

        $localInput = ($data['localComponentsTotals'] ?? 0)
            + ($data['localPackagingComponentsTotals'] ?? 0);
        $importedInput = ($data['importedComponentsTotals'] ?? 0)
            + ($data['importedPackagingComponentsTotals'] ?? 0)
            + ($data['importedSpareParts'] ?? 0);

        $totalLocalComponentCost = $localInput + $costOfFixedAndVariableItems;
        $totalProductCost = $totalLocalComponentCost + $importedInput;
        $localCostPercentage = ($totalProductCost == 0) ? 0 : (($totalLocalComponentCost / $totalProductCost) * 100);

        return [
            'assessmentScorePercent'      => $localCostPercentage,
            'localComponentsCostTotal'    => $totalLocalComponentCost,
            'ImportedComponentsCostTotal' => $importedInput,
        ];
    }

    public function GetAssessment($id)
    {
        $assessment = Assessment::findOrFail($id); 
        if ($assessment->isTotals == false) {
            $localComponents = Component::where("assessmentId", "=", $id)
                ->where("isPackaging", "=", false)->where("isImported", "=", false)->get();
            $localPackagingComponents = Component::where("assessmentId", "=", $id)
                ->where("isPackaging", "=", true)->where("isImported", "=", false)->get();
            $importedComponents = Component::where("assessmentId", "=", $id)
                ->where("isPackaging", "=", false)->where("isImported", "=", true)->get();
            $importedPackagingComponents = Component::where("assessmentId", "=", $id)
                ->where("isPackaging", "=", true)->where("isImported", "=", true)->get();
            $assessmentScorePercent = $this->GenerateDetailedAssessment($assessment, $localComponents, $localPackagingComponents, $importedComponents, $importedPackagingComponents)['assessmentScorePercent'];
        } else {
            $assessmentScorePercent = $this->GeneratetotalsAssessment($assessment)['assessmentScorePercent'];
        }
        $data =  app('App\Http\Controllers\ChambersController')->get_by_id($assessment->chamberId);
        return response()->json([
            "id" =>  $assessment->id,
            "applicantId" => $assessment->applicantId,
            "chamber" => $data,
            "productName" => $assessment->productName,
            "manufactoringByOthers" => $assessment->manufactoringByOthers? true : false,
            "manufactoringCompanyName" => $assessment->manufactoringCompanyName,
            "manufactoringCompanyIndustrialRegistry" => $assessment->manufactoringCompanyIndustrialRegistry,
            "manufactoringCompanyTaxId" => $assessment->manufactoringCompanyTaxId,
            "manufactoringCompanyCommercialRegistry" => $assessment->manufactoringCompanyCommercialRegistry,
            "isTotals" => $assessment->isTotals,
            "annualProductionCapacity" => $assessment->annualProductionCapacity,
            "powerResources" => $assessment->powerResources,
            "localSpareParts" => $assessment->localSpareParts,
            "importedSpareParts" => $assessment->importedSpareParts,
            "researchAndDevelopment" => $assessment->researchAndDevelopment,
            "wages" => $assessment->wages,
            "annualDepreciation" => $assessment->annualDepreciation,
            "administrativeExpenses" => $assessment->administrativeExpenses,
            "marketingExpenses" => $assessment->marketingExpenses,
            "otherExpenses" => $assessment->otherExpenses,
            "localComponentsTotals" => $assessment->localComponentsTotals,
            "localPackagingComponentsTotals" => $assessment->localPackagingComponentsTotals,
            "importedComponentsTotals" => $assessment->importedComponentsTotals,
            "importedPackagingComponentsTotals" => $assessment->importedPackagingComponentsTotals,
            "localComponentsDetailed" => ($assessment->isTotals == true) ? [] : $localComponents,
            "localPackagingComponentsDetailed" => ($assessment->isTotals == true) ? [] : $localPackagingComponents,
            "importedComponentsDetailed" => ($assessment->isTotals == true) ? [] : $importedComponents,
            "importedPackagingComponentsDetailed" => ($assessment->isTotals == true) ? [] : $importedPackagingComponents,
            "assessmentScorePercent" => $assessmentScorePercent,
            "customsFees" => $assessment->customsFees
        ]);
    }

    public function SaveTotalAssessment(Request $request)
    {
        $data = $request->all();

        $rules = [
            'manufactoringByOthers'               => 'boolean|required',
            'manufactoringCompanyName'               => 'string|nullable|required_if:manufactoringByOthers,1',
            'manufactoringCompanyIndustrialRegistry' => 'string|nullable|required_if:manufactoringByOthers,1',
            'manufactoringCompanyTaxId'               => 'string|nullable|required_if:manufactoringByOthers,1',
            'manufactoringCompanyCommercialRegistry' => 'string|nullable|required_if:manufactoringByOthers,1',
            'localComponentsTotals'                     => 'nullable|numeric|required_without:importedComponentsTotals',
            'localPackagingComponentsTotals'            => 'nullable|numeric|required_without:importedPackagingComponentsTotals',
            'localSpareParts'                     => ['nullable', 'numeric', Rule::requiredIf(!array_key_exists('importedSpareParts', $data) && $data['manufactoringByOthers'] == false)],
            'importedComponentsTotals'                  => 'nullable|numeric|required_without:localComponentsTotals',
            'importedPackagingComponentsTotals'         => 'nullable|numeric|required_without:localPackagingComponentsTotals',
            'importedSpareParts'                  => ['nullable', 'numeric', Rule::requiredIf(!array_key_exists('localSpareParts', $data) && $data['manufactoringByOthers'] == false)],
            'powerResources'                      => 'numeric|nullable|required_if:manufactoringByOthers,false',
            'researchAndDevelopment'              => 'numeric|nullable|required_if:manufactoringByOthers,false',
            'wages'                               => 'numeric|nullable|required_if:manufactoringByOthers,false',
            'annualDepreciation'                  => 'numeric|nullable|required_if:manufactoringByOthers,false',
            'administrativeExpenses'              => 'numeric|nullable|required_if:manufactoringByOthers,false',
            'marketingExpenses'                   => 'numeric|nullable|required_if:manufactoringByOthers,false',
            'otherExpenses'                       => 'numeric|required',
            'productName'                         => 'string|required',
            'assessmentDate'                      => 'date'

        ];
        /** validation */
        $this->validate($request, $rules);

        /** user data  */
        $user = userData();
        $applicant_id = $user->id;

        $data["applicantId"] = $applicant_id;
        $data['localComponentsDetailed'] = null;
        $data['localPackagingComponentsDetailed'] = null;
        $data['importedComponentsDetailed'] = null;
        $data['importedPackagingComponentsDetailed'] = null;
        $data['assessmentDate']=Carbon::now();
        $savedAssessment = Assessment::create($data);
        app('App\Http\Controllers\LogController')->Logging_create("assessments",$savedAssessment );

        return response()->json(['id' => $savedAssessment->id]);
    }

    public function SavedetailedAssessment(Request $request)
    {
        $data = $request->all();
        $rules = [
            'manufactoringByOthers'                  => 'boolean|required',
            'manufactoringCompanyName'               => 'string|nullable|required_if:manufactoringByOthers,1',
            'manufactoringCompanyIndustrialRegistry' => 'string|nullable|required_if:manufactoringByOthers,1',
            'manufactoringCompanyTaxId'               => 'string|nullable|required_if:manufactoringByOthers,1',
            'manufactoringCompanyCommercialRegistry' => 'string|nullable|required_if:manufactoringByOthers,1',
            'localSpareParts'                     => ['nullable','numeric', Rule::requiredIf(!array_key_exists('importedSpareParts', $data) && $data['manufactoringByOthers'] == false)],
            'importedSpareParts'                  => ['nullable', 'numeric', Rule::requiredIf(!array_key_exists('localSpareParts', $data) && $data['manufactoringByOthers'] == false)],
            'annualProductionCapacity'            => 'nullable|integer|min:1|required_if:manufactoringByOthers,false',
            'powerResources'                      => 'numeric|nullable|required_if:manufactoringByOthers,false',
            'researchAndDevelopment'              => 'numeric|nullable|required_if:manufactoringByOthers,false',
            'wages'                               => 'numeric|nullable|required_if:manufactoringByOthers,false',
            'annualDepreciation'                  => 'numeric|nullable|required_if:manufactoringByOthers,false',
            'administrativeExpenses'              => 'numeric|nullable|required_if:manufactoringByOthers,false',
            'marketingExpenses'                   => 'numeric|nullable|required_if:manufactoringByOthers,false',
            'otherExpenses'                       => 'numeric|required',
            'productName'                         => 'string|required',
            'isPackaging'                         => 'boolean',
            'isImported'                          => 'boolean',

            'localComponentsDetailed'                   => 'array|required_without_all:localPackagingComponentsDetailed,importedComponentsDetailed,importedPackagingComponentsDetailed',
            'localComponentsDetailed.*.componentName'   => 'required|string',
            'localComponentsDetailed.*.unit'            => 'required|string',
            'localComponentsDetailed.*.quantity'        => 'required|numeric',
            'localComponentsDetailed.*.unitPrice'       => 'required|numeric',
            'localComponentsDetailed.*.supplier'        => 'nullable|string',

            'localPackagingComponentsDetailed'                   => 'array|required_without_all:localComponentsDetailed,importedComponentsDetailed,importedPackagingComponentsDetailed',
            'localPackagingComponentsDetailed.*.componentName'   => 'required|string',
            'localPackagingComponentsDetailed.*.unit'            => 'required|string',
            'localPackagingComponentsDetailed.*.quantity'        => 'required|numeric',
            'localPackagingComponentsDetailed.*.unitPrice'       => 'required|numeric',
            'localPackagingComponentsDetailed.*.supplier'        => 'nullable|string',

            'importedComponentsDetailed'                 => 'array|required_without_all:localPackagingComponentsDetailed,localComponentsDetailed,importedPackagingComponentsDetailed',
            'importedComponentsDetailed.*.componentName' => 'required|string',
            'importedComponentsDetailed.*.unit'          => 'required|string',
            'importedComponentsDetailed.*.quantity'      => 'required|numeric',
            'importedComponentsDetailed.*.unitPrice'     => 'required|numeric',
            'importedComponentsDetailed.*.rate'          => 'required|numeric',
            'importedComponentsDetailed.*.CIF'           => 'nullable|numeric',
            'importedComponentsDetailed.*.supplier'      => 'nullable|string',

            'importedPackagingComponentsDetailed'              => 'array|required_without_all:localPackagingComponentsDetailed,importedComponentsDetailed,localComponentsDetailed',
            'importedPackagingComponentsDetailed.*.componentName' => 'required|string',
            'importedPackagingComponentsDetailed.*.unit'          => 'required|string',
            'importedPackagingComponentsDetailed.*.quantity'      => 'required|numeric',
            'importedPackagingComponentsDetailed.*.unitPrice'     => 'required|numeric',
            'importedPackagingComponentsDetailed.*.rate'          => 'required|numeric',
            'importedPackagingComponentsDetailed.*.CIF'           => 'nullable|numeric',
            'importedPackagingComponentsDetailed.*.supplier'      => 'nullable|string',
            'assessmentDate'                      => 'date'

        ];

        /** validation */
        $this->validate($request, $rules);

        /** user data */
        $user = userData();

        $applicant_id = $user->id;
        $data["applicantId"] = $applicant_id;
        $data['localComponentsTotals'] = null;
        $data['localPackagingComponentsTotals'] = null;
        $data['importedComponentsTotals'] = null;
        $data['importedPackagingComponentsTotals'] = null;
        $data['assessmentDate']=Carbon::now();
        $savedAssessment = Assessment::create($data);
        if (isset($savedAssessment)) {
            if (isset($request->all()['localComponentsDetailed'])) {
                foreach ($request->all()['localComponentsDetailed'] as $component) {
                    $component["assessmentId"] = $savedAssessment->id;
                    $component["isPackaging"] = false;
                    $component["isImported"] = false;
                    Component::create($component);
                }
            }
            if ($request->all()['localPackagingComponentsDetailed']) {
                foreach ($request->all()['localPackagingComponentsDetailed'] as $component) {
                    $component["assessmentId"] = $savedAssessment->id;
                    $component["isPackaging"] = true;
                    $component["isImported"] = false;
                    Component::create($component);
                }
            }
            if ($request->all()['importedComponentsDetailed']) {
                foreach ($request->all()['importedComponentsDetailed'] as $component) {
                    $component["assessmentId"] = $savedAssessment->id;
                    $component["isPackaging"] = false;
                    $component["isImported"] = true;
                    Component::create($component);
                }
            }
            if ($request->all()['importedPackagingComponentsDetailed']) {
                foreach ($request->all()['importedPackagingComponentsDetailed'] as $component) {
                    $component["assessmentId"] = $savedAssessment->id;
                    $component["isPackaging"] = true;
                    $component["isImported"] = true;
                    Component::create($component);
                }
            }
        }
        app('App\Http\Controllers\LogController')->Logging_create("assessments",$savedAssessment );
        return response()->json(['id' => $savedAssessment->id]);
    }

    public function SaveAssessment(Request $request)
    {
        if (isset($request) && ($request->isTotals == true)) {
            return $this->SaveTotalAssessment($request);
        } else {
            return $this->SavedetailedAssessment($request);
        }
    }

    public function UpdatedetailedAssessment(Request $request, $id)
    {
        $data = $request->all();
        $rules = [
            'manufactoringByOthers'               => 'boolean|required',
            'manufactoringCompanyName'               => 'string|nullable|required_if:manufactoringByOthers,true',
            'manufactoringCompanyIndustrialRegistry' => 'string|nullable|required_if:manufactoringByOthers,true',
            'manufactoringCompanyTaxId'               => 'string|nullable|required_if:manufactoringByOthers,1',
            'manufactoringCompanyCommercialRegistry' => 'string|nullable|required_if:manufactoringByOthers,1',
            'localSpareParts'                     => ['nullable', 'numeric', Rule::requiredIf(!array_key_exists('importedSpareParts', $data) && $data['manufactoringByOthers'] == false)],
            'importedSpareParts'                  => ['nullable', 'numeric', Rule::requiredIf(!array_key_exists('localSpareParts', $data) && $data['manufactoringByOthers'] == false)],
            'annualProductionCapacity'            => 'nullable|integer|min:1|required_if:manufactoringByOthers,false',
            'powerResources'                      => 'numeric|nullable|required_if:manufactoringByOthers,false',
            'researchAndDevelopment'              => 'numeric|nullable|required_if:manufactoringByOthers,false',
            'wages'                               => 'numeric|nullable|required_if:manufactoringByOthers,false',
            'annualDepreciation'                  => 'numeric|nullable|required_if:manufactoringByOthers,false',
            'administrativeExpenses'              => 'numeric|nullable|required_if:manufactoringByOthers,false',
            'marketingExpenses'                   => 'numeric|nullable|required_if:manufactoringByOthers,false',
            'otherExpenses'                       => 'numeric|required',
            'productName'                         => 'string|required',
            'isPackaging'                         => 'boolean',
            'isImported'                          => 'boolean',

            'localComponentsDetailed'                   => 'array|required_without_all:localPackagingComponentsDetailed,importedComponentsDetailed,importedPackagingComponentsDetailed',
            'localComponentsDetailed.*.componentName'   => 'required|string',
            'localComponentsDetailed.*.unit'            => 'required|string',
            'localComponentsDetailed.*.quantity'        => 'required|numeric',
            'localComponentsDetailed.*.unitPrice'       => 'required|numeric',
            'localComponentsDetailed.*.supplier'        => 'nullable|string',

            'localPackagingComponentsDetailed'                   => 'array|required_without_all:localComponentsDetailed,importedComponentsDetailed,importedPackagingComponentsDetailed',
            'localPackagingComponentsDetailed.*.componentName'   => 'required|string',
            'localPackagingComponentsDetailed.*.unit'            => 'required|string',
            'localPackagingComponentsDetailed.*.quantity'        => 'required|numeric',
            'localPackagingComponentsDetailed.*.unitPrice'       => 'required|numeric',
            'localPackagingComponentsDetailed.*.supplier'        => 'nullable|string',

            'importedComponentsDetailed'                 => 'array|required_without_all:localPackagingComponentsDetailed,localComponentsDetailed,importedPackagingComponentsDetailed',
            'importedComponentsDetailed.*.componentName' => 'required|string',
            'importedComponentsDetailed.*.unit'          => 'required|string',
            'importedComponentsDetailed.*.quantity'      => 'required|numeric',
            'importedComponentsDetailed.*.unitPrice'     => 'required|numeric',
            'importedComponentsDetailed.*.rate'          => 'required|numeric',
            'importedComponentsDetailed.*.CIF'           => 'nullable|numeric',
            'importedComponentsDetailed.*.supplier'      => 'nullable|string',

            'importedPackagingComponentsDetailed'              => 'array|required_without_all:localPackagingComponentsDetailed,importedComponentsDetailed,localComponentsDetailed',
            'importedPackagingComponentsDetailed.*.componentName' => 'required|string',
            'importedPackagingComponentsDetailed.*.unit'          => 'required|string',
            'importedPackagingComponentsDetailed.*.quantity'      => 'required|numeric',
            'importedPackagingComponentsDetailed.*.unitPrice'     => 'required|numeric',
            'importedPackagingComponentsDetailed.*.rate'          => 'required|numeric',
            'importedPackagingComponentsDetailed.*.CIF'           => 'nullable|numeric',
            'importedPackagingComponentsDetailed.*.supplier'      => 'nullable|string',
            'assessmentDate'                      => 'date'

        ];

        /** validation  */
        $this->validate($request, $rules);

        /** user data */
        $user = userData();

        $applicant_id = $user->id;
        $data["applicantId"] = $applicant_id;
        $data['localComponentsTotals'] = null;
        $data['localPackagingComponentsTotals'] = null;
        $data['importedComponentsTotals'] = null;
        $data['importedPackagingComponentsTotals'] = null;
        $model = Assessment::find($id);
        // $old=$model->getOriginal();
        $model->update($data);
        $model->save();
        if (isset($model)) {
            if (isset($request->all()['localComponentsDetailed'])) {
                $allComponentIds = Component::where(['assessmentId' =>$id , 'isPackaging'=>false ,'isImported'=>false])
                ->get('id');
                
                $ids = array_column($request->all()['localComponentsDetailed'] , 'id');
                foreach ($allComponentIds as  $value) {
                    if(!in_array($value->id , $ids)){
                    Component::where(['id'=> $value->id ,'assessmentId' =>$id , 'isPackaging'=>false ,'isImported'=>false])
                        ->delete();
                    }
                }
                foreach ($request->all()['localComponentsDetailed'] as $component) {
                    $component["assessmentId"] = $id;
                    $component["isPackaging"] = false;
                    $component["isImported"] = false;

                 
                    if(isset($component['id'])){

                        $model = Component::find($component['id']);
                        $model->update($component);
                        $model->save();
                    }else{
                        $model = Component::create($component);

                    }
                }
            }
            if ($request->all()['localPackagingComponentsDetailed']) {
                $allComponentIds = Component::where(['assessmentId' =>$id , 'isPackaging'=>true ,'isImported'=>false])
                ->get('id');
                
                $ids = array_column($request->all()['localPackagingComponentsDetailed'] , 'id');
                foreach ($allComponentIds as  $value) {
                    if(!in_array($value->id , $ids)){
                    Component::where(['id'=> $value->id ,'assessmentId' =>$id , 'isPackaging'=>true ,'isImported'=>false])
                        ->delete();
                    }
                }
                foreach ($request->all()['localPackagingComponentsDetailed'] as $component) {
                    $component["assessmentId"] = $id;
                    $component["isPackaging"] = true;
                    $component["isImported"] = false;
                    if(isset($component['id'])){

                        $model = Component::find($component['id']);
                        $model->update($component);
                        $model->save();
                    }else{
                        $model = Component::create($component);

                    }
                }
            }
            if ($request->all()['importedComponentsDetailed']) {
                $allComponentIds = Component::where(['assessmentId' =>$id , 'isPackaging'=>false ,'isImported'=>true])
                ->get('id');
                
                $ids = array_column($request->all()['importedComponentsDetailed'] , 'id');
                foreach ($allComponentIds as  $value) {
                    if(!in_array($value->id , $ids)){
                    Component::where(['id'=> $value->id ,'assessmentId' =>$id , 'isPackaging'=>false ,'isImported'=>true])
                        ->delete();
                    }
                }
                foreach ($request->all()['importedComponentsDetailed'] as $component) {
                    $component["assessmentId"] = $id;
                    $component["isPackaging"] = false;
                    $component["isImported"] = true;
                    if(isset($component['id'])){

                        $model = Component::find($component['id']);
                        $model->update($component);
                        $model->save();
                    }else{
                        $model = Component::create($component);

                    }
                }
            }
            if ($request->all()['importedPackagingComponentsDetailed']) {
                $allComponentIds = Component::where(['assessmentId' =>$id , 'isPackaging'=>true ,'isImported'=>true])
                ->get('id');
                
                $ids = array_column($request->all()['importedPackagingComponentsDetailed'] , 'id');
                foreach ($allComponentIds as  $value) {
                    if(!in_array($value->id , $ids)){
                    Component::where(['id'=> $value->id ,'assessmentId' =>$id , 'isPackaging'=>true ,'isImported'=>true])
                        ->delete();
                    }
                }
                foreach ($request->all()['importedPackagingComponentsDetailed'] as $component) {
                    $component["assessmentId"] = $id;
                    $component["isPackaging"] = true;
                    $component["isImported"] = true;
                    if(isset($component['id'])){

                        $model = Component::find($component['id']);
                        $model->update($component);
                        $model->save();
                    }else{
                        $model = Component::create($component);

                    }
                }
            }
        }
        $old=$model->getOriginal();
        app('App\Http\Controllers\LogController')->Logging_update("assessments",$model,$old);
        return response()->json(__("assessment.saveAssessment"),200);
    }

    public function UpdateTotalAssessment(Request $request, $id)
    {
        $data = $request->all();

        $rules = [
            'manufactoringByOthers'               => 'boolean|required',
            'manufactoringCompanyName'               => 'string|nullable|required_if:manufactoringByOthers,true',
            'manufactoringCompanyIndustrialRegistry' => 'string|nullable|required_if:manufactoringByOthers,true',
            'manufactoringCompanyTaxId'               => 'string|nullable|required_if:manufactoringByOthers,1',
            'manufactoringCompanyCommercialRegistry' => 'string|nullable|required_if:manufactoringByOthers,1',
            'localComponentsTotals'                     => 'nullable|numeric|required_without:importedComponentsTotals',
            'localPackagingComponentsTotals'            => 'nullable|numeric|required_without:importedPackagingComponentsTotals',
            'localSpareParts'                     => ['nullable', 'numeric', Rule::requiredIf(!array_key_exists('importedSpareParts', $data) && $data['manufactoringByOthers'] == false)],
            'importedComponentsTotals'                  => 'nullable|numeric|required_without:localComponentsTotals',
            'importedPackagingComponentsTotals'         => 'nullable|numeric|required_without:localPackagingComponentsTotals',
            'importedSpareParts'                  => ['nullable', 'numeric', Rule::requiredIf(!array_key_exists('localSpareParts', $data) && $data['manufactoringByOthers'] == false)],
            'powerResources'                      => 'numeric|nullable|required_if:manufactoringByOthers,false',
            'researchAndDevelopment'              => 'numeric|nullable|required_if:manufactoringByOthers,false',
            'wages'                               => 'numeric|nullable|required_if:manufactoringByOthers,false',
            'annualDepreciation'                  => 'numeric|nullable|required_if:manufactoringByOthers,false',
            'administrativeExpenses'              => 'numeric|nullable|required_if:manufactoringByOthers,false',
            'marketingExpenses'                   => 'numeric|nullable|required_if:manufactoringByOthers,false',
            'otherExpenses'                       => 'numeric|required',
            'productName'                         => 'string|required',
            'assessmentDate'                      => 'date'          
        ];

        $this->validate($request, $rules);
        $user = userData();
        $applicant_id = $user->id;
        $data["applicantId"] = $applicant_id;
        $data['localComponentsDetailed'] = null;
        $data['localPackagingComponentsDetailed'] = null;
        $data['importedComponentsDetailed'] = null;
        $data['importedPackagingComponentsDetailed'] = null;
        $model = Assessment::find($id);
        $old=$model->getOriginal();
        $model->update($data);
        $model->save();
        app('App\Http\Controllers\LogController')->Logging_update("assessments",$model,$old);
        return response()->json(__("assessment.saveAssessment"),200);
    }

    public function UpdateAssessment(Request $request, $id)
    {
        $check_belong_to_request_or_not = RequestModel::where("assessmentId", $id)->get();
        if (!empty($check_belong_to_request_or_not)) {
            foreach ($check_belong_to_request_or_not as $req) {
                if ($req->statusId !== RequestStatus::where("key", "Returned")->first()->id
                && $req->statusId !== RequestStatus::where("key", "Draft")->first()->id) {
                    // return response()->json(app("translator").get("assessment.updateAssessment"), 400);
                    return response()->json("Assessment Update failed", 400);
                
                }
            }
        }
        if (isset($request) && ($request->isTotals == true)) {
            return $this->UpdateTotalAssessment($request, $id);
        } else {
            return $this->UpdatedetailedAssessment($request, $id);
        }
    }

    public function Get_assessment_by_request_id($request_id)
    {
        $id = RequestModel::findOrFail($request_id)->toArray()['assessmentId'];
        $assessment = Assessment::findOrFail($id); 
        if ($assessment->isTotals == false) {
            $localComponents = Component::where("assessmentId", "=", $id)
                ->where("isPackaging", "=", false)->where("isImported", "=", false)->get();
            $localPackagingComponents = Component::where("assessmentId", "=", $id)
                ->where("isPackaging", "=", true)->where("isImported", "=", false)->get();
            $importedComponents = Component::where("assessmentId", "=", $id)
                ->where("isPackaging", "=", false)->where("isImported", "=", true)->get();
            $importedPackagingComponents = Component::where("assessmentId", "=", $id)
                ->where("isPackaging", "=", true)->where("isImported", "=", true)->get();
            $assessmentScorePercent = $this->GenerateDetailedAssessment($assessment, $localComponents, $localPackagingComponents, $importedComponents, $importedPackagingComponents)['assessmentScorePercent'];
        } else {
            $assessmentScorePercent = $this->GeneratetotalsAssessment($assessment)['assessmentScorePercent'];
        }

        return response()->json([
            "id" =>  $assessment->id,
            "applicantId" => $assessment->applicantId,
            "chamberId" => $assessment->chamberId,
            "productName" => $assessment->productName,
            "manufactoringByOthers" => $assessment->manufactoringByOthers? true : false,
            "manufactoringCompanyName" => $assessment->manufactoringCompanyName,
            "manufactoringCompanyIndustrialRegistry" => $assessment->manufactoringCompanyIndustrialRegistry,
            "manufactoringCompanyTaxId" => $assessment->manufactoringCompanyTaxId,
            "manufactoringCompanyCommercialRegistry" => $assessment->manufactoringCompanyCommercialRegistry,
            "isTotals" => $assessment->isTotals,
            "annualProductionCapacity" => $assessment->annualProductionCapacity,
            "powerResources" => $assessment->powerResources,
            "localSpareParts" => $assessment->localSpareParts,
            "importedSpareParts" => $assessment->importedSpareParts,
            "researchAndDevelopment" => $assessment->researchAndDevelopment,
            "wages" => $assessment->wages,
            "annualDepreciation" => $assessment->annualDepreciation,
            "administrativeExpenses" => $assessment->administrativeExpenses,
            "marketingExpenses" => $assessment->marketingExpenses,
            "otherExpenses" => $assessment->otherExpenses,
            "localComponentsTotals" => $assessment->localComponentsTotals,
            "localPackagingComponentsTotals" => $assessment->localPackagingComponentsTotals,
            "importedComponentsTotals" => $assessment->importedComponentsTotals,
            "importedPackagingComponentsTotals" => $assessment->importedPackagingComponentsTotals,
            "localComponentsDetailed" => ($assessment->isTotals == true) ? [] : $localComponents,
            "localPackagingComponentsDetailed" => ($assessment->isTotals == true) ? [] : $localPackagingComponents,
            "importedComponentsDetailed" => ($assessment->isTotals == true) ? [] : $importedComponents,
            "importedPackagingComponentsDetailed" => ($assessment->isTotals == true) ? [] : $importedPackagingComponents,
            "assessmentScorePercent" => $assessmentScorePercent,
            "customsFees" => $assessment->customsFees
        ]);
    }

    public function Get_assessment_by_applicant_id(Request $request)
    {
        $this->validate($request , [
            'sortColumn' => 'nullable|string',
            'sortDirection' => 'nullable|in:asc,desc'
        ]);
        $sortCol = $request->sortColumn ?? 'id';
        $sortDir = $request->sortDirection ?? 'desc';

        $user = userData();

        $applicant_id = $user->id;
        $assessmentData = Assessment::where("applicantId", $applicant_id)->orderBy($sortCol , $sortDir)->get();
        
        if (!empty($assessmentData)) {
            $response = array();
            $i= 1;
            foreach ($assessmentData as $value) {
                $id = $value->id;
                $check=RequestModel::where("assessmentId", $id)->get();
                if(count($check) == 0){
                    $assessment = $value;                   
                    if ($assessment->isTotals == false) {
                        $localComponents = Component::where("assessmentId", "=", $id)
                            ->where("isPackaging", "=", false)->where("isImported", "=", false)->get();
                        $localPackagingComponents = Component::where("assessmentId", "=", $id)
                            ->where("isPackaging", "=", true)->where("isImported", "=", false)->get();
                        $importedComponents = Component::where("assessmentId", "=", $id)
                            ->where("isPackaging", "=", false)->where("isImported", "=", true)->get();
                        $importedPackagingComponents = Component::where("assessmentId", "=", $id)
                            ->where("isPackaging", "=", true)->where("isImported", "=", true)->get();
                        $assessmentScorePercent = $this->GenerateDetailedAssessment($assessment, $localComponents, $localPackagingComponents, $importedComponents, $importedPackagingComponents)['assessmentScorePercent'];
                    } else {
                        $assessmentScorePercent = $this->GeneratetotalsAssessment($assessment)['assessmentScorePercent'];
                    }
                    array_push(
                        $response,
                        [
                            "id" =>  $assessment->id,
                            "applicantId" => $assessment->applicantId,
                            "chamberId" => $assessment->chamberId,
                            "productName" => $assessment->productName,
                            "manufactoringByOthers" => $assessment->manufactoringByOthers? true : false,
                            "manufactoringCompanyName" => $assessment->manufactoringCompanyName,
                            "manufactoringCompanyIndustrialRegistry" => $assessment->manufactoringCompanyIndustrialRegistry,                            "isTotals" => $assessment->isTotals,
                            "manufactoringCompanyTaxId" => $assessment->manufactoringCompanyTaxId,
                            "manufactoringCompanyCommercialRegistry" => $assessment->manufactoringCompanyCommercialRegistry,
                            "annualProductionCapacity" => $assessment->annualProductionCapacity,
                            "powerResources" => $assessment->powerResources,
                            "localSpareParts" => $assessment->localSpareParts,
                            "importedSpareParts" => $assessment->importedSpareParts,
                            "researchAndDevelopment" => $assessment->researchAndDevelopment,
                            "wages" => $assessment->wages,
                            "annualDepreciation" => $assessment->annualDepreciation,
                            "administrativeExpenses" => $assessment->administrativeExpenses,
                            "marketingExpenses" => $assessment->marketingExpenses,
                            "otherExpenses" => $assessment->otherExpenses,
                            "localComponentsTotals" => $assessment->localComponentsTotals,
                            "localPackagingComponentsTotals" => $assessment->localPackagingComponentsTotals,
                            "importedComponentsTotals" => $assessment->importedComponentsTotals,
                            "importedPackagingComponentsTotals" => $assessment->importedPackagingComponentsTotals,
                            "localComponentsDetailed" => ($assessment->isTotals == true) ? [] : $localComponents,
                            "localPackagingComponentsDetailed" => ($assessment->isTotals == true) ? [] : $localPackagingComponents,
                            "importedComponentsDetailed" => ($assessment->isTotals == true) ? [] : $importedComponents,
                            "importedPackagingComponentsDetailed" => ($assessment->isTotals == true) ? [] : $importedPackagingComponents,
                            "assessmentScorePercent" => $assessmentScorePercent,
                            "customsFees" => $assessment->customsFees
                        ]
                    );
                    
                }
            }
        }
    
        return $response ?? [];
        
    }

    public function getMostExpensiveMaterials_Imported(Request $request)
    {    
        $this->validate($request, ['count' => 'nullable|numeric']);
        $count = !empty($request->get('count')) ? $request->get('count') : 10;
        $component=Component::where("isImported" , true)->orderBy("unitPrice","desc")->take($count)->get();
        $response = array();
        foreach ($component as $item) {
            array_push($response,["componentName" => $item->componentName, "unitPrice" => $item->unitPrice]);
        }
        return response()->json( $response, 200);
       
    }

    public function getMostExpensiveMaterials_Local(Request $request)
    {  
        $this->validate($request, ['count' => 'nullable|numeric']);
        $count = !empty($request->get('count')) ? $request->get('count') : 10;
        $component=Component::where("isImported" , false)->orderBy("unitPrice","desc")->take($count)->get();
        $response = array();
        foreach ($component as $item) {
            array_push($response, ["componentName" => $item->componentName, "unitPrice" => $item->unitPrice]);
        }
        return response()->json( $response, 200);
    }

    public function summary(Request $request, $id)
    {
        $assessment = Assessment::findOrFail($id); 
        if ($assessment->isTotals == false) {
            $localComponents = Component::where("assessmentId", "=", $id)
                ->where("isPackaging", "=", false)->where("isImported", "=", false)->get();
            $localPackagingComponents = Component::where("assessmentId", "=", $id)
                ->where("isPackaging", "=", true)->where("isImported", "=", false)->get();
            $importedComponents = Component::where("assessmentId", "=", $id)
                ->where("isPackaging", "=", false)->where("isImported", "=", true)->get();
            $importedPackagingComponents = Component::where("assessmentId", "=", $id)
                ->where("isPackaging", "=", true)->where("isImported", "=", true)->get();
            $response = $this->GenerateDetailedAssessment($assessment, $localComponents, $localPackagingComponents, $importedComponents, $importedPackagingComponents);
            
        } else {

            $response = $this->GeneratetotalsAssessment($assessment);
        }

        if (!empty($response)) {
            return $this->respond(Response::HTTP_OK, $response);
        }
        else {
            return $this->respond(Response::HTTP_NOT_FOUND);
        }
    }

    public function change_product_name(Request $request)
    {
        $this->validate($request, [
            'requestId'      => 'required|integer',
            'newProductName' => 'required|string'
        ]);
        $allowStatus = ['New' , 'Assigned' , 'UnderReview_Closed' , 'UnderReview_Opened' ,'Accepted' , 'AcceptanceConfirmed'];
        $req = RequestModel::find($request->requestId);
        if(!empty($req)){
            $reqStatus = RequestStatus::find($req->statusId)->key;
            if(in_array($reqStatus , $allowStatus)){
                $assessment = Assessment::find($req->assessmentId);
                $assessment->productName = $request->newProductName;
                $old=$assessment->getOriginal();
                if($assessment->save()){
                    app('App\Http\Controllers\LogController')->Logging_update("assessments",$assessment,$old);
                    return response()->json(["message" => "the product name changed successfully"], 200);        
                }
                return response()->json(["message" => "the product name not changed! , someThing wrong .."], 400);        
            }
            return response()->json(["message" => "you don't have a permission to change the product name"], 400);        
        }
        return response()->json(["message" => "there is no request with this id"], 400);        
    }

    
    protected function respond($status, $data = [])
    {
        return response()->json($data, $status);
    }
}
