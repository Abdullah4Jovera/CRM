<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiLeadController extends Controller
{
    public function wpFormData(Request $request){
        $usr = \Auth::user();
        if (!$usr->can('create lead')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
        $validator = $this->validateLeadRequest($request);
        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->getMessageBag()->first());
        }

        if (empty($stageId)) {
            $stageId = $this->getLeadStage($request->pipeline, $request->stage_id);
        }
        $client = $this->createOrUpdateClient($request);
        $lead = $this->createLead($request, $client->id, $stageId);
        $this->createProductSpecificRecord($request, $lead);
        $this->createUserLeads($usr->id, $lead->id, $request->user_id);
        $this->createLeadActivityLog($usr->id, $lead->id);
        return redirect()->back()->with('success', __('Lead successfully created!'));
    }
    private function validateLeadRequest(Request $request)
    {
        $commonRules = [
            'products' => 'required',
            'pipeline' => 'required',
            'lead_type' => 'required',
            'sources' => 'required',
            'stage_id' => 'required',
        ];

        $clientSpecificRules = [
            'phone' => ['required', 'numeric', 'digits:9', new UniquePhone],
            'name' => 'required',
        ];


        $rules = ($request->client_id != null) ? $commonRules : array_merge($commonRules, $clientSpecificRules);

        return \Validator::make($request->all(), $rules);
    }
    private function getLeadStage($pipelineId, $customStageId)
    {
        if ($customStageId) {
            return $customStageId;
        }
        $data = LeadStage::where('pipeline_id', '=', $pipelineId)
            ->when($customStageId, function ($query) use ($customStageId) {
                return $query->orWhere('id', $customStageId);
            })
            ->first();
        return $data->id;
    }
    private function createOrUpdateClient(Request $request)
    {
        if (empty($request->client_id)) {
            return Client::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => "+971" . $request->phone,
                'nationality' => $request->nationality,
                'language' => $request->language,
                'e_id' => $request->eid,
                'address' => $request->address,
            ]);
        }

        $client = Client::findOrFail($request->client_id);
        return $client;
    }
    private function createLead(Request $request, $clientId, $stageId)
    {
        return Lead::create([
            'client_id' => $clientId,
            'pipeline_id' => $request->pipeline,
            'lead_type' => $request->lead_type,
            'products' => $request->products,
            'sources' => $request->sources,
            'notes' => $request->notes,
            'stage_id' => $stageId,
            'is_active' => 1,
            'created_by' => \Auth::user()->creatorId(),
            'date' => now(),
        ]);
    }
    private function createProductSpecificRecord(Request $request, Lead $lead)
    {
        if ($request->products == 1) {
            BusinessBanking::create([
                'lead_id' => $lead->id,
                'business_banking_services' => $request->business_banking_services,
                'company_name' => $request->company_name,
                'yearly_turnover' => $request->yearly_turnover,
                'have_any_pos' => $request->have_any_pos,
                'monthly_amount' => $request->monthly_amount,
                'have_auto_finance' => $request->have_auto_finance,
                'monthly_emi' => $request->monthly_emi,
                'lgcs' => $request->lgcs,
                'notes' => $request->notes,
            ]);
        } elseif ($request->products == 2) {
            PersonalLoan::create([
                'lead_id' => $lead->id,
                'company_name' => $request->company_name,
                'monthly_salary' => $request->monthly_salary,
                'load_amount' => $request->load_amount,
                'have_any_loan' => $request->have_any_loan,
                'taken_loan_amount' => $request->taken_loan_amount,
                'notes' => $request->notes,
            ]);
        } elseif ($request->products == 3) {
            MortgageLoan::create([
                'lead_id' => $lead->id,
                'type_of_property' => $request->type_of_property,
                'location' => $request->location,
                'monthly_income' => $request->monthly_income,
                'have_any_other_loan' => $request->have_any_other_loan,
                'loanAmount' => $request->loanAmount,
                'notes' => $request->notes,
            ]);
        } else {
            RealEstate::create([
                'lead_id' => $lead->id,
                'locationChoice' => $request->locationChoice,
                'propertyPurpose' => $request->propertyPurpose,
                'propertyType' => $request->propertyType,
                'priceRange' => $request->priceRange,
                'propertyTypeSale' => $request->propertyTypeSale,
                'bedrooms' => $request->bedrooms,
                'notes' => $request->notes,
            ]);
        }
    }
}
