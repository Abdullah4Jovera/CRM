<?php

namespace App\Http\Controllers;

use App\Mail\SendLeadEmail;
use App\Models\ClientDeal;
use App\Models\Deal;
use App\Models\DealCall;
use App\Models\DealDiscussion;
use App\Models\DealEmail;
use App\Models\DealFile;
use App\Models\ActivityLog;
use App\Models\Label;
use App\Models\LeadType;
use App\Models\Lead;
use App\Models\Client;
use App\Models\Services;
use App\Models\ServiceCommission;
use App\Models\UserNotifications;
use App\Models\LeadActivityLog;
use App\Models\LeadCall;
use App\Models\LeadDiscussion;
use App\Models\LeadEmail;
use App\Models\LeadFile;
use App\Models\LeadStage;
use App\Models\Pipeline;
use App\Models\ProductService;
use App\Models\PersonalLoan;
use App\Models\MortgageLoan;
use App\Models\BusinessBanking;
use App\Models\RealEstate;
use App\Models\Source;
use App\Models\Stage;
use App\Models\User;
use App\Models\UserDeal;
use App\Models\UserLead;
use App\Models\Utility;
use App\Models\WebhookSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Rules\UniquePhone;

class LeadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(\Auth::user()->can('manage lead'))
        {
            if(\Auth::user()->default_pipeline)
            {

                $pipeline = Pipeline::where('created_by', '=', \Auth::user()->creatorId())->where('id', '=', \Auth::user()->default_pipeline)->first();
                if(!$pipeline)
                {
                    $pipeline = Pipeline::where('created_by', '=', \Auth::user()->creatorId())->first();
                }
                if (\Auth::user()->default_pipeline == 1) {
                    $allLeads = User::all();
                }else {
                    $allLeads = User::all();
                }
            }
            else
            {
                $pipeline = Pipeline::where('created_by', '=', \Auth::user()->creatorId())->get();

            }
            $pipelines = Pipeline::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $users = User::select('name','id')->where('created_by', '=', \Auth::user()->creatorId())->where('type', '!=', 'client')->where('type', '!=', 'company')->where('id', '!=', \Auth::user()->id)->get();
            $products = ProductService::where('created_by', '=', \Auth::user()->creatorId())->get();
            $lead_types = LeadType::all();

            $clients = DB::select("SELECT clients.name,clients.id,clients.phone
            FROM leads
            JOIN user_leads ON leads.id = user_leads.lead_id
            JOIN users ON user_leads.user_id = users.id
            JOIN clients ON leads.client_id = clients.id
            WHERE users.id = " . \Auth::user()->id . " AND pipeline_id = " . \Auth::user()->default_pipeline . " AND leads.is_reject IS NULL AND leads.is_converted IS NULL");

            $pipelineLeads = Pipeline::where('created_by', '=', \Auth::user()->creatorId())->get();
            $new_pipelines = Pipeline::select('name','id')->where('created_by', '=', \Auth::user()->creatorId())->get();

            return view('leads.index', compact('new_pipelines','pipelines', 'pipeline','products','clients','pipelineLeads','lead_types','allLeads','users'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function lead_list()
    {
        $usr = \Auth::user();

        if($usr->can('manage lead'))
        {
            if($usr->default_pipeline)
            {
                $pipeline = Pipeline::where('created_by', '=', $usr->creatorId())->where('id', '=', $usr->default_pipeline)->first();
                if(!$pipeline)
                {
                    $pipeline = Pipeline::where('created_by', '=', $usr->creatorId())->first();
                }
            }
            else
            {
                $pipeline = Pipeline::where('created_by', '=', $usr->creatorId())->first();
            }

            $pipelines = Pipeline::where('created_by', '=', $usr->creatorId())->get()->pluck('name', 'id');
            // $leads     = Lead::select('leads.*')->join('user_leads', 'user_leads.lead_id', '=', 'leads.id')->where('user_leads.user_id', '=', $usr->id)->where('leads.pipeline_id', '=', $pipeline->id)->orderBy('leads.order')->get();

            // $leads     = Lead::where('is_reject',1)->where('pipeline_id',10)->orderBy('created_at','desc')->with('users','client','pipeline')->paginate(200);
            $user_id = $usr->id ;
            $leads = Lead::leftJoin('clients', 'leads.client_id', '=', 'clients.id')
            ->leftJoin('pipelines', 'leads.pipeline_id', '=', 'pipelines.id')
            ->with([
                'users:id,name,avatar,designation',
            ])
            ->select('leads.id', 'leads.is_reject', 'leads.notes','leads.is_active', 'clients.name as client_name', 'pipelines.name as pipeline_name')
            ->where('is_reject', 1)
            ->where('pipeline_id', 10)
            ->whereHas('users', function ($query) use ($user_id) {
                $query->where($query->qualifyColumn('users.id'), $user_id);
            })
            ->orderBy('leads.created_at', 'desc')
            ->get();
            return view('leads.list', compact('pipelines', 'pipeline', 'leads'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        if(\Auth::user()->can('create lead'))
        {
            $clients = Client::all();
            $users = User::where('created_by', '=', \Auth::user()->creatorId())->where('type', '!=', 'client')->where('designation', '!=', 'Jovera')->where('id', '!=', \Auth::user()->id)->get()->pluck('name', 'id');
            $pipeline = Pipeline::all();
            $lead_types = LeadType::all();
            // $sources  = Source::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $products = ProductService::where('created_by', '=', \Auth::user()->creatorId())->get();
            return view('leads.create', compact('users','pipeline','products','clients','lead_types'));
        }
        else
        {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    public function filterByUsers()
    {

        if(!empty(\Auth::user()))
        {
            $users = User::select('name','id')->where('created_by', '=', \Auth::user()->creatorId())->where('type', '!=', 'client')->where('type', '!=', 'company')->where('id', '!=', \Auth::user()->id)->get();
            $pipelines = Pipeline::select('name','id')->where('created_by', '=', \Auth::user()->creatorId())->get();
            $products = ProductService::where('created_by', '=', \Auth::user()->creatorId())->get();
            $clients = User::join('user_leads', 'users.id', '=','user_leads.user_id' )->join('leads', 'user_leads.id', '=','leads.id')->join('clients', 'leads.client_id', '=','clients.id')->where('users.id',\Auth::user()->id)->get();
            return view('leads.filter', compact('users','pipelines','products','clients'));
        }
        else
        {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->client_id);
        $usr = \Auth::user();
        if($usr->can('create lead'))
        {
            if (empty($request->client_id)) {
                $validator = \Validator::make(
                    $request->all(), [
                        'products' => 'required',
                        'phone' => ['required', 'numeric', 'digits:9', new UniquePhone],
                        'name' => 'required',
                        'pipeline' => 'required',
                        'lead_type' => 'required',
                        'sources' => 'required',
                        'stage_id' => 'required',
                   ]
                );

                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }
            }

            $stage_id = LeadStage::where('pipeline_id', '=', $request->pipeline)->first();
            $stage = $request->stage_id ? $request->stage_id : $stage_id->id;



            if(empty($stage))
            {
                return redirect()->back()->with('error', __('Please Create Stage for This Pipeline.'));
            }
            else
            {

                if (empty($request->client_id)) {
                    $client = new Client();
                    $client->name        = $request->name;
                    $client->email       = $request->email;
                    $client->phone       = "+971".$request->phone;
                    $client->nationality       = $request->nationality;
                    $client->language       = $request->language;
                    $client->e_id       = $request->eid;
                    $client->address       = $request->address;
                    $client->save();
                }

                $lead              = new Lead();
                $lead->client_id     = (!empty($request->client_id))?$request->client_id:$client->id;
                $lead->pipeline_id     = $request->pipeline;
                $lead->lead_type     = $request->lead_type;
                $lead->products = $request->products;
                $lead->sources = $request->sources;
                $lead->notes = $request->notes;
                $lead->stage_id    = $stage;
                $lead->is_active   = 1;
                $lead->created_by  = $usr->creatorId();
                $lead->date        = date('Y-m-d');
                $lead->save();
                if ($request->products == 1) {
                    $businessBanking = new BusinessBanking();
                    $businessBanking->lead_id = $lead->id;
                    $businessBanking->business_banking_services = $request->business_banking_services;
                    $businessBanking->company_name = $request->company_name;
                    $businessBanking->yearly_turnover = $request->yearly_turnover;
                    $businessBanking->have_any_pos = $request->have_any_pos;
                    $businessBanking->monthly_amount = $request->monthly_amount;
                    $businessBanking->have_auto_finance = $request->have_auto_finance;
                    $businessBanking->monthly_emi = $request->monthly_emi;
                    $businessBanking->lgcs = $request->lgcs;
                    $businessBanking->notes=$request->notes;
                    $businessBanking->save();

                }elseif ($request->products == 2) {
                    $personalLoan = new PersonalLoan();
                    $personalLoan->lead_id = $lead->id;
                    $personalLoan->company_name = $request->company_name;
                    $personalLoan->monthly_salary = $request->monthly_salary;
                    $personalLoan->load_amount = $request->load_amount;
                    $personalLoan->have_any_loan = $request->have_any_loan;
                    $personalLoan->taken_loan_amount = $request->taken_loan_amount;
                    $personalLoan->notes = $request->notes;
                    $personalLoan->save();
                }elseif ($request->products == 3){
                    $mortgageLoan= new MortgageLoan();
                    $mortgageLoan->lead_id = $lead->id;
                    $mortgageLoan->type_of_property = $request->type_of_property;
                    $mortgageLoan->location = $request->location;
                    $mortgageLoan->monthly_income = $request->monthly_income;
                    $mortgageLoan->have_any_other_loan = $request->have_any_other_loan;
                    $mortgageLoan->loanAmount = $request->loanAmount;
                    $mortgageLoan->notes = $request->notes;
                    $mortgageLoan->save();
                }
                else{
                    $mortgageLoan= new RealEstate();
                    $mortgageLoan->lead_id = $lead->id;
                    $mortgageLoan->type_of_property = $request->propertyPurpose;
                    $mortgageLoan->location = $request->propertyType;
                    $mortgageLoan->monthly_income = $request->priceRange;
                    $mortgageLoan->have_any_other_loan = $request->propertyTypeSale;
                    $mortgageLoan->loanAmount = $request->bedrooms;
                    $mortgageLoan->notes = $request->notes;
                    $mortgageLoan->save();
                }
                // Create UserLead records for authenticated user, Jovera, and company users
                $designatedUserIds = User::whereIn('designation', ['Jovera', 'company'])->pluck('id')->prepend($usr->id)->toArray();
                $userLeadRecords = collect($designatedUserIds)->map(function ($userId) use ($lead) {
                    return ['user_id' => $userId, 'lead_id' => $lead->id];
                })->toArray();
                UserLead::insert($userLeadRecords);
                if (!empty($request->user_id)) {
                    $otherUserIds = array_map('intval', $request->user_id);
                    $otherUserLeadRecords = collect($otherUserIds)->map(function ($userId) use ($lead) {
                        return ['user_id' => $userId, 'lead_id' => $lead->id];
                    })->toArray();
                    UserLead::insert($otherUserLeadRecords);
                }
                LeadActivityLog::create([
                    'user_id'=> \Auth::user()->id,
                    'lead_id'=> $lead->id,
                    'log_type'=> "Create Lead",
                    'remark'=> "Create New Lead",
                ]);
                return redirect()->back()->with('success', __('Lead successfully created!') );
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Lead $lead
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Lead $lead)
    {
        if($lead->is_active)
        {
            // dd($lead);
            $calenderTasks = [];
            $deal          = Deal::where('id', '=', $lead->is_converted)->first();
            $stageCnt      = LeadStage::where('pipeline_id', '=', $lead->pipeline_id)->where('created_by', '=', $lead->created_by)->get();
            $i             = 0;
            foreach($stageCnt as $stage)
            {
                $i++;
                if($stage->id == $lead->stage_id)
                {
                    break;
                }
            }

            $user_lead = UserLead::where('lead_id',$lead->id)->where('user_id',\Auth::user()->id)->first();
            if (!empty($user_lead)) {
                return view('leads.show', compact('lead', 'calenderTasks', 'deal' ));
            }else{
                return response()->json(['error' => __('Permission Denied.')], 401);
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Lead $lead
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Lead $lead)
    {
        if(\Auth::user()->can('edit lead'))
        {
            if($lead->created_by == \Auth::user()->creatorId())
            {
                $user_lead = UserLead::where('lead_id',$lead->id)->where('user_id',\Auth::user()->id)->first();
                if (!empty($user_lead)) {
                    $pipelines      = Pipeline::where('created_by', '=', \Auth::user()->creatorId())->get();
                    $sources        = Source::select('id','name')->where('lead_type_id',$lead->lead_type)->get();
                    $lead_types     = LeadType::select('id','name')->get();
                    $products       = ProductService::where('created_by', '=', \Auth::user()->creatorId())->get();
                    $users          = User::where('created_by', '=', \Auth::user()->creatorId())->where('type', '!=', 'client')->where('type', '!=', 'company')->where('id', '!=', \Auth::user()->id)->get();

                    return view('leads.edit', compact('lead', 'pipelines', 'sources', 'products', 'users','lead_types'));
                }else{
                    return response()->json(['error' => __('Permission Denied.')], 401);
                }
            }
            else
            {
                return response()->json(['error' => __('Permission Denied.')], 401);
            }
        }
        else
        {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     *
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Lead $lead
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Lead $lead)
    {
        $user = \Auth::user();

        if ($user->can('edit lead') && $lead->created_by == $user->creatorId()) {
            $this->updateLeadAndClient($lead, $request);

            if ($lead->products == 1) {
                BusinessBanking::updateOrCreate(
                    ['lead_id' => $lead->id],
                    [
                        'business_banking_services' => $request->business_banking_services,
                        'company_name' => $request->company_name,
                        'yearly_turnover' => $request->yearly_turnover,
                        'have_any_pos' => $request->have_any_pos,
                        'monthly_amount' => $request->monthly_amount,
                        'have_auto_finance' => $request->have_auto_finance,
                        'monthly_emi' => $request->monthly_emi,
                        'lgcs' => $request->lgcs,
                        'notes' => $request->notes,
                    ]
                );
            } elseif ($lead->products == 2) {
                PersonalLoan::updateOrCreate(
                    ['lead_id' => $lead->id],
                    [
                        'company_name' => $request->company_name,
                        'monthly_salary' => $request->monthly_salary,
                        'load_amount' => $request->load_amount,
                        'have_any_loan' => $request->have_any_loan,
                        'taken_loan_amount' => $request->taken_loan_amount,
                        'notes' => $request->notes,
                    ]
                );
            } else {
                MortgageLoan::updateOrCreate(
                    ['lead_id' => $lead->id],
                    [
                        'type_of_property' => $request->type_of_property,
                        'location' => $request->location,
                        'monthly_income' => $request->monthly_income,
                        'have_any_other_loan' => $request->have_any_other_loan,
                        'loanAmount' => $request->loanAmount,
                        'notes' => $request->notes,
                    ]
                );
            }

            if ($lead->is_transfer == 1) {
                LeadActivityLog::create([
                    'lead_id' => $lead->id,
                    'user_id' => $user->id,
                    'log_type' => "Transfer Lead",
                    'remark' => "Transfer Lead Service Info Update",
                ]);
            }

            LeadActivityLog::create([
                'user_id' => $user->id,
                'lead_id' => $lead->id,
                'log_type' => "Update Lead",
                'remark' => "Update Lead",
            ]);

            return redirect()->route('leads.index')->with('success', __('Lead successfully updated!'));
        }

        return redirect()->back()->with('error', __('Permission Denied.'));
    }

    private function updateLeadAndClient(Lead $lead, Request $request)
    {
        $stage_id = LeadStage::where('pipeline_id', '=', $request->pipeline)->first();
        $stage = $request->stage_id ? $request->stage_id : $stage_id->id;

        $lead->pipeline_id = $request->pipeline;
        $lead->is_active = 1;
        $lead->notes = $request->notes;
        $lead->stage_id = $stage;
        $lead->is_reject = $request->is_reject;
        $lead->created_at = now();
        $lead->save();

        $client = Client::find($lead->client_id);
        $client->e_id = $request->eid;
        $client->name = $request->name;
        $client->email = $request->email;
        $client->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Lead $lead
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Lead $lead)
    {
        if(\Auth::user()->can('delete lead'))
        {
            if($lead->created_by == \Auth::user()->creatorId())
            {
                LeadDiscussion::where('lead_id', '=', $lead->id)->delete();
                LeadFile::where('lead_id', '=', $lead->id)->delete();
                UserLead::where('lead_id', '=', $lead->id)->delete();
                LeadActivityLog::where('lead_id', '=', $lead->id)->delete();
                $lead->delete();

                return redirect()->back()->with('success', __('Lead successfully deleted!'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission Denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }




    // Get Lead By Users Fuction getLeadByUsers

    function getLeadByUsers(Request $request){
        $usr = \Auth::user();
        if($usr->can('manage lead'))
        {
            if(\Auth::user()->default_pipeline)
            {
                $pipeline = Pipeline::where('created_by', '=', \Auth::user()->creatorId())->where('id', '=', \Auth::user()->default_pipeline)->first();
                if(!$pipeline)
                {
                    $pipeline = Pipeline::where('created_by', '=', \Auth::user()->creatorId())->first();
                }
            }
            else
            {
                $pipeline = Pipeline::where('created_by', '=', \Auth::user()->creatorId())->first();
            }

            $pipelines = Pipeline::where('created_by', '=', \Auth::user()->creatorId())->get();
                $users = null;
                if (!empty($request->user)) {
                    $a= UserLead::where('user_id',$request->user)->get()->pluck('lead_id');
                    $b = Lead::where('id',$request->user)->get()->pluck('id');
                    $users  = $a->merge($b)->unique();
                }
                $pipe = \Auth::user()->default_pipeline;
                $client_id = $request->clinet_id;
                $lead_type = $request->lead_type;
                $products = $request->products;




                $searchLead = Lead::
                when(($pipe != "null"), function ($query) use ($pipe) {
                    $query->where('pipeline_id', $pipe);
                })
                ->when(!empty($users), function ($query) use ($users) {
                    $query->whereIn('id', $users);
                })
                ->when(($client_id != "null"), function ($query) use ($client_id) {
                    $query->where('client_id', $client_id);
                })
                ->when(($lead_type != "null"), function ($query) use ($lead_type) {
                    $query->where('lead_type', $lead_type);
                })
                ->when(($products != "null"), function ($query) use ($products) {
                    $query->where('products', $products);
                })
                ->get();
                return view('leads.searchAllLeads', compact('pipeline','searchLead'));



        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function json(Request $request)
    {
        $lead_stages = new LeadStage();
        if($request->pipeline_id && !empty($request->pipeline_id))
        {
            $lead_stages = $lead_stages->where('pipeline_id', '=', $request->pipeline_id);
            $lead_stages = $lead_stages->get()->pluck('name', 'id');
        }
        else
        {
            $lead_stages = [];
        }

        return response()->json($lead_stages);
    }

    public function newJson(Request $request)
    {
        // return $request;
        $lead_stages = new Source();
        if($request->lead_type_id && !empty($request->lead_type_id))
        {
            $lead_stages = $lead_stages->where('lead_type_id', '=', $request->lead_type_id);
            $lead_stages = $lead_stages->get()->pluck('name', 'id');
        }
        else
        {
            $lead_stages = [];
        }

        return response()->json($lead_stages);
    }

    public function fileUpload($id, Request $request)
    {

        if(\Auth::user()->can('fileadd lead'))
        {
            $lead = Lead::find($id);
            if($lead->created_by == \Auth::user()->creatorId())
            {
            //    $request->validate(['file' => 'required|mimes:png,jpeg,jpg,pdf,doc,txt,application/octet-stream,audio/mpeg,mpga,mp3,wav|max:20480000']);
                $file_name = $request->file->getClientOriginalName();
                $file_path = $request->lead_id . "_" . md5(time()) . "_" . $request->file->getClientOriginalName();
                $request->file->storeAs('lead_files', $file_path);
                // dd($file_name,$file_path);
                $file                 = LeadFile::create(
                    [
                        'lead_id' => $request->lead_id,
                        'file_name' => $file_name,
                        'file_path' => $file_path,
                    ]
                );
                $return               = [];
                $return['is_success'] = true;
                $return['download']   = route(
                    'leads.file.download', [
                                             $lead->id,
                                             $file->id,
                                         ]
                );
                $return['delete']     = route(
                    'leads.file.delete', [
                                           $lead->id,
                                           $file->id,
                                       ]
                );
                LeadActivityLog::create(
                    [
                        'user_id' => \Auth::user()->id,
                        'lead_id' => $lead->id,
                        'log_type' => 'Upload File',
                        'remark' => json_encode(['file_name' => $file_name]),
                    ]
                );

                return response()->json($return);
            }
            else
            {
                return response()->json(
                    [
                        'is_success' => false,
                        'error' => __('Permission Denied.'),
                    ], 401
                );
            }
        }
        else
        {
            return response()->json(
                [
                    'is_success' => false,
                    'error' => __('Permission Denied.'),
                ], 401
            );
        }
    }

    public function fileDownload($id, $file_id)
    {
        if(\Auth::user()->can('filedownload lead'))
        {
            $lead = Lead::find($id);
            if($lead->created_by == \Auth::user()->creatorId())
            {
                $file = LeadFile::find($file_id);
                if($file)
                {
                    $file_path = storage_path('lead_files/' . $file->file_path);
                    $filename  = $file->file_name;

                    return \Response::download(
                        $file_path, $filename, [
                                      'Content-Length: ' . filesize($file_path),
                                  ]
                    );
                }
                else
                {
                    return redirect()->back()->with('error', __('File is not exist.'));
                }
            }
            else
            {
                return redirect()->back()->with('error', __('Permission Denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function fileDelete($id, $file_id)
    {
        if(\Auth::user()->can('filedelete lead'))
        {
            $lead = Lead::find($id);
            if($lead->created_by == \Auth::user()->creatorId())
            {
                $file = LeadFile::find($file_id);
                if($file)
                {
                    $path = storage_path('lead_files/' . $file->file_path);
                    if(file_exists($path))
                    {
                        \File::delete($path);
                    }
                    $file->delete();

                    return response()->json(['is_success' => true], 200);
                }
                else
                {
                    return response()->json(
                        [
                            'is_success' => false,
                            'error' => __('File is not exist.'),
                        ], 200
                    );
                }
            }
            else
            {
                return response()->json(
                    [
                        'is_success' => false,
                        'error' => __('Permission Denied.'),
                    ], 401
                );
            }
        }
        else
        {
            return response()->json(
                [
                    'is_success' => false,
                    'error' => __('Permission Denied.'),
                ], 401
            );
        }
    }

    public function noteStore($id, Request $request)
    {
        if(\Auth::user()->can('edit lead'))
        {
            $lead = Lead::find($id);
            if($lead->created_by == \Auth::user()->creatorId())
            {
                $lead->notes = $request->notes;
                $lead->save();

                return response()->json(
                    [
                        'is_success' => true,
                        'success' => __('Note successfully saved!'),
                    ], 200
                );
            }
            else
            {
                return response()->json(
                    [
                        'is_success' => false,
                        'error' => __('Permission Denied.'),
                    ], 401
                );
            }
        }
        else
        {
            return response()->json(
                [
                    'is_success' => false,
                    'error' => __('Permission Denied.'),
                ], 401
            );
        }
    }

    public function labels($id)
    {
        if(\Auth::user()->can('edit lead'))
        {
            $lead = Lead::find($id);
            if($lead->created_by == \Auth::user()->creatorId())
            {
                $labels   = Label::where('pipeline_id', '=', $lead->pipeline_id)->where('created_by', \Auth::user()->creatorId())->get();
                $selected = $lead->labels();
                if($selected)
                {
                    $selected = $selected->pluck('name', 'id')->toArray();
                }
                else
                {
                    $selected = [];
                }

                return view('leads.labels', compact('lead', 'labels', 'selected'));
            }
            else
            {
                return response()->json(['error' => __('Permission Denied.')], 401);
            }
        }
        else
        {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    public function labelStore($id, Request $request)
    {
        if(\Auth::user()->can('edit lead'))
        {
            $leads = Lead::find($id);
            if($leads->created_by == \Auth::user()->creatorId())
            {
                if($request->labels)
                {
                    $leads->labels = implode(',', $request->labels);
                }
                else
                {
                    $leads->labels = $request->labels;
                }
                $leads->save();

                return redirect()->back()->with('success', __('Labels successfully updated!'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission Denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function userEdit($id)
    {
        if(\Auth::user()->can('addUser lead'))
        {
            $lead = Lead::find($id);

            if($lead->created_by == \Auth::user()->creatorId())
            {
                $users = User::where('created_by', '=', \Auth::user()->creatorId())->where('type', '!=', 'client')->where('type', '!=', 'company')->whereNOTIn(
                    'id', function ($q) use ($lead){
                    $q->select('user_id')->from('user_leads')->where('lead_id', '=', $lead->id);
                }
                )->get();


                $users = $users->pluck('name', 'id');

                return view('leads.users', compact('lead', 'users'));
            }
            else
            {
                return response()->json(['error' => __('Permission Denied.')], 401);
            }
        }
        else
        {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    public function userUpdate($id, Request $request)
    {
        if(\Auth::user()->can('edit lead'))
        {
            $usr  = \Auth::user();
            $lead = Lead::find($id);

            if($lead->created_by == $usr->creatorId())
            {
                if(!empty($request->users))
                {
                    $users   = array_filter($request->users);
                    $leadArr = [
                        'lead_id' => $lead->id,
                        'name' => $lead->name,
                        'updated_by' => $usr->id,
                    ];

                    foreach($users as $user)
                    {
                        UserLead::create(
                            [
                                'lead_id' => $lead->id,
                                'user_id' => $user,
                            ]
                        );
                    }
                }

                if(!empty($users) && !empty($request->users))
                {
                    return redirect()->back()->with('success', __('Users successfully updated!'));
                }
                else
                {
                    return redirect()->back()->with('error', __('Please Select Valid User!'));
                }
            }
            else
            {
                return redirect()->back()->with('error', __('Permission Denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function userDestroy($id, $user_id)
    {
        if(\Auth::user()->can('deleteUser lead'))
        {
            $lead = Lead::find($id);
            if($lead->created_by == \Auth::user()->creatorId())
            {
                UserLead::where('lead_id', '=', $lead->id)->where('user_id', '=', $user_id)->delete();

                return redirect()->back()->with('success', __('User successfully deleted!'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission Denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function productEdit($id)
    {
        if(\Auth::user()->can('edit lead'))
        {
            $lead = Lead::find($id);
            if($lead->created_by == \Auth::user()->creatorId())
            {
                $products = ProductService::where('created_by', '=', \Auth::user()->creatorId())->whereNOTIn('id', explode(',', $lead->products))->get()->pluck('name', 'id');

                return view('leads.products', compact('lead', 'products'));
            }
            else
            {
                return response()->json(['error' => __('Permission Denied.')], 401);
            }
        }
        else
        {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    public function productUpdate($id, Request $request)
    {
        if(\Auth::user()->can('edit lead'))
        {
            $usr        = \Auth::user();
            $lead       = Lead::find($id);
            $lead_users = $lead->users->pluck('id')->toArray();

            if($lead->created_by == \Auth::user()->creatorId())
            {
                if(!empty($request->products))
                {
                    $products       = array_filter($request->products);
                    $old_products   = explode(',', $lead->products);
                    $lead->products = implode(',', array_merge($old_products, $products));
                    $lead->save();

                    $objProduct = ProductService::whereIN('id', $products)->get()->pluck('name', 'id')->toArray();

                    LeadActivityLog::create(
                        [
                            'user_id' => $usr->id,
                            'lead_id' => $lead->id,
                            'log_type' => 'Add Product',
                            'remark' => json_encode(['title' => implode(",", $objProduct)]),
                        ]
                    );

                    $productArr = [
                        'lead_id' => $lead->id,
                        'name' => $lead->name,
                        'updated_by' => $usr->id,
                    ];

                }

                if(!empty($products) && !empty($request->products))
                {
                    return redirect()->back()->with('success', __('Products successfully updated!'))->with('status', 'products');
                }
                else
                {
                    return redirect()->back()->with('error', __('Please Select Valid Product!'))->with('status', 'general');
                }
            }
            else
            {
                return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'products');
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'products');
        }
    }

    public function productDestroy($id, $product_id)
    {
        if(\Auth::user()->can('edit lead'))
        {
            $lead = Lead::find($id);
            if($lead->created_by == \Auth::user()->creatorId())
            {
                $products = explode(',', $lead->products);
                foreach($products as $key => $product)
                {
                    if($product_id == $product)
                    {
                        unset($products[$key]);
                    }
                }
                $lead->products = implode(',', $products);
                $lead->save();

                return redirect()->back()->with('success', __('Products successfully deleted!'))->with('status', 'products');
            }
            else
            {
                return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'products');
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'products');
        }
    }

    public function sourceEdit($id)
    {
        if(\Auth::user()->can('edit lead'))
        {
            $lead = Lead::find($id);
            if($lead->created_by == \Auth::user()->creatorId())
            {
                $sources = Source::where('created_by', '=', \Auth::user()->creatorId())->get();

                $selected = $lead->sources();
                if($selected)
                {
                    $selected = $selected->pluck('name', 'id')->toArray();
                }

                return view('leads.sources', compact('lead', 'sources', 'selected'));
            }
            else
            {
                return response()->json(['error' => __('Permission Denied.')], 401);
            }
        }
        else
        {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    public function sourceUpdate($id, Request $request)
    {
        if(\Auth::user()->can('edit lead'))
        {
            $usr        = \Auth::user();
            $lead       = Lead::find($id);
            $lead_users = $lead->users->pluck('id')->toArray();

            if($lead->created_by == \Auth::user()->creatorId())
            {
                if(!empty($request->sources) && count($request->sources) > 0)
                {
                    $lead->sources = implode(',', $request->sources);
                }
                else
                {
                    $lead->sources = "";
                }

                $lead->save();

                LeadActivityLog::create(
                    [
                        'user_id' => $usr->id,
                        'lead_id' => $lead->id,
                        'log_type' => 'Update Sources',
                        'remark' => json_encode(['title' => 'Update Sources']),
                    ]
                );

                $leadArr = [
                    'lead_id' => $lead->id,
                    'name' => $lead->name,
                    'updated_by' => $usr->id,
                ];

                return redirect()->back()->with('success', __('Sources successfully updated!'))->with('status', 'sources');
            }
            else
            {
                return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'sources');
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'sources');
        }
    }

    public function sourceDestroy($id, $source_id)
    {
        if(\Auth::user()->can('edit lead'))
        {
            $lead = Lead::find($id);
            if($lead->created_by == \Auth::user()->creatorId())
            {
                $sources = explode(',', $lead->sources);
                foreach($sources as $key => $source)
                {
                    if($source_id == $source)
                    {
                        unset($sources[$key]);
                    }
                }
                $lead->sources = implode(',', $sources);
                $lead->save();

                return redirect()->back()->with('success', __('Sources successfully deleted!'))->with('status', 'sources');
            }
            else
            {
                return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'sources');
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'sources');
        }
    }

    public function discussionCreate($id)
    {
        $lead = Lead::find($id);
        if($lead->created_by == \Auth::user()->creatorId())
        {
            return view('leads.discussions', compact('lead'));
        }
        else
        {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    public function discussionStore($id, Request $request)
    {
        $usr        = \Auth::user();
        $lead       = Lead::find($id);
        $lead_users = $lead->users->pluck('id')->toArray();

        if($lead->created_by == $usr->creatorId())
        {
            $discussion             = new LeadDiscussion();
            $discussion->comment    = $request->comment;
            $discussion->lead_id    = $lead->id;
            $discussion->created_by = $usr->id;
            $discussion->save();

            $leadArr = [
                'lead_id' => $lead->id,
                'name' => $lead->name,
                'updated_by' => $usr->id,
            ];

            return redirect()->back()->with('success', __('Message successfully added!'))->with('status', 'discussion');
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'discussion');
        }
    }

    public function order(Request $request)
    {
        if(\Auth::user()->can('move lead'))
        {
            $usr        = \Auth::user();
            $post       = $request->all();
            $lead       = Lead::find($post['lead_id']);
            $lead_users = $lead->users->pluck('email', 'id')->toArray();

            if($lead->stage_id != $post['stage_id'])
            {
                $newStage = LeadStage::find($post['stage_id']);

                LeadActivityLog::create(
                    [
                        'user_id' => \Auth::user()->id,
                        'lead_id' => $lead->id,
                        'log_type' => 'Move',
                        'remark' => json_encode(
                            [
                                'title' => $lead->name,
                                'old_status' => $lead->stage->name,
                                'new_status' => $newStage->name,
                            ]
                        ),
                    ]
                );

                $leadArr = [
                    'lead_id' => $lead->id,
                    'name' => $lead->name,
                    'updated_by' => $usr->id,
                    'old_status' => $lead->stage->name,
                    'new_status' => $newStage->name,
                ];

                $lArr = [
                    'lead_name' => $lead->name,
                    'lead_email' => $lead->email,
                    'lead_pipeline' => $lead->pipeline->name,
                    'lead_stage' => $lead->stage->name,
                    'lead_old_stage' => $lead->stage->name,
                    'lead_new_stage' => $newStage->name,
                ];

                // Send Email
                Utility::sendEmailTemplate('Move Lead', $lead_users, $lArr);
            }

            foreach($post['order'] as $key => $item)
            {
                $lead           = Lead::find($item);
                $lead->order    = $key;
                $lead->stage_id = $post['stage_id'];
                $lead->save();
            }
        }
        else
        {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    public function showConvertToDeal($id)
    {

        $lead         = Lead::findOrFail($id);
        $exist_client = User::where('type', '=', 'client')->where('email', '=', $lead->email)->where('created_by', '=', \Auth::user()->creatorId())->first();
        $clients      = User::where('type', '=', 'client')->where('created_by', '=', \Auth::user()->creatorId())->get();
        $users      = User::where('created_by', '=', \Auth::user()->creatorId())->where('type', '!=', 'client')->where('type', '!=', 'company')->get();



        return view('leads.convert', compact('lead', 'exist_client', 'clients', 'users'));
    }

    public function convertToDeal($id, Request $request)
    {
        $lead = Lead::findOrFail($id);
        $usr = \Auth::user();
        $deal = Deal::create([
            'is_transfer' => $lead->is_transfer,
            'client_id' => $lead->client_id,
            'pipeline_id' => $lead->pipeline_id,
            'lead_type' => $lead->lead_type,
            'sources' => $lead->sources,
            'products' => $lead->products,
            'labels' => $lead->labels,
            'created_by' => $lead->created_by,
            'date' => date('Y-m-d'),
        ]);

        $lead->update([
            'is_converted' => $deal->id,
            'deal_stage_id' => 1,
        ]);

        $serviceCommissionData = [
            'deal_id' => $deal->id,
            'finance_amount' => $request->finance_amount,
            'bank_commission' => $request->bank_commission,
            'customer_commission' => $request->customer_commission,
            'with_vat_commission' => $request->with_vat_commission,
            'without_vat_commission' => $request->without_vat_commission,
            'hodsale' => $request->hodsale,
            'hodsalecommission' => $request->hodsalecommission,
            'ts_hod' => $request->ts_hod,
            'ts_hod_commission' => $request->ts_hod_commission,
            'salemanager' => $request->salemanager,
            'salemanagercommission' => $request->salemanagercommission ?? 0,
            'coordinator' => $request->coordinator,
            'coordinator_commission' => $request->coordinator_commission ?? 0,
            'team_leader' => $request->team_leader,
            'team_leader_commission' => $request->team_leader_commission ?? 0,
            'salesagent' => $request->salesagent,
            'salesagent_commission' => $request->salesagent_commission ?? 0,
            'team_leader_one' => $request->team_leader_one,
            'team_leader_one_commission' => $request->team_leader_one_commission ?? 0,
            'sale_agent_one' => $request->sale_agent_one,
            'sale_agent_one_commission' => $request->sale_agent_one_commission ?? 0,
            'salemanagerref' => $request->salemanagerref,
            'salemanagerrefcommission' => $request->salemanagerrefcommission ?? 0,
            'agentref' => $request->agentref,
            'agent_commission' => $request->agent_commission ?? 0,
            'ts_team_leader' => $request->ts_team_leader,
            'ts_team_leader_commission' => $request->ts_team_leader_commission ?? 0,
            'tsagent' => $request->tsagent,
            'tsagent_commission' => $request->tsagent_commission ?? 0,
            'marketingmanager' => $request->marketingmanager,
            'marketingmanagercommission' => $request->marketingmanagercommission ?? 0,
            'marketingagent' => $request->marketingagent ?? 0,
            'marketingagentcommission' => $request->marketingagentcommission ?? 0,
            'other_name' => $request->other_name,
            'other_name_commission' => $request->other_commission ?? 0,
            'broker_name' => $request->broker_name,
            'broker_name_commission' => $request->broker_commission ?? 0,
        ];

        $servicecommission = ServiceCommission::create($serviceCommissionData);

        $activity = ActivityLog::create([
            'user_id' => $usr->id,
            'deal_id' => $deal->id,
            'log_type' => 'Convert',
            'remark' => json_encode([
                'title' => $lead->name,
                'old_status' => 'Lead',
                'new_status' => 'Deal',
            ]),
        ]);

        $usersIds = UserLead::where('lead_id', $id)->pluck('user_id');
        UserNotifications::insert($usersIds->map(function ($userId) use ($activity) {
            return ['activity_id' => $activity->id, 'user_id' => $userId];
        })->toArray());
        return redirect()->route('contract.index')->with('success', __('Lead successfully converted'));
    }

    // Lead Calls
    public function callCreate($id)
    {
        if(\Auth::user()->can('create lead call'))
        {
            $lead = Lead::find($id);
            if($lead->created_by == \Auth::user()->creatorId())
            {
                $users = UserLead::where('lead_id', '=', $lead->id)->get();

                return view('leads.calls', compact('lead', 'users'));
            }
            else
            {
                return response()->json(
                    [
                        'is_success' => false,
                        'error' => __('Permission Denied.'),
                    ], 401
                );
            }
        }
        else
        {
            return response()->json(
                [
                    'is_success' => false,
                    'error' => __('Permission Denied.'),
                ], 401
            );
        }
    }

    public function callStore($id, Request $request)
    {
        if(\Auth::user()->can('create lead call'))
        {
            $usr  = \Auth::user();
            $lead = Lead::find($id);
            if($lead->created_by == \Auth::user()->creatorId())
            {
                $validator = \Validator::make(
                    $request->all(), [
                                       'subject' => 'required',
                                       'call_type' => 'required',
                                       'user_id' => 'required',
                                   ]
                );

                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }

                $leadCall = LeadCall::create(
                    [
                        'lead_id' => $lead->id,
                        'subject' => $request->subject,
                        'call_type' => $request->call_type,
                        'duration' => $request->duration,
                        'user_id' => $request->user_id,
                        'description' => $request->description,
                        'call_result' => $request->call_result,
                    ]
                );

                LeadActivityLog::create(
                    [
                        'user_id' => $usr->id,
                        'lead_id' => $lead->id,
                        'log_type' => 'create lead call',
                        'remark' => json_encode(['title' => 'Create new Lead Call']),
                    ]
                );

                $leadArr = [
                    'lead_id' => $lead->id,
                    'name' => $lead->name,
                    'updated_by' => $usr->id,
                ];

                return redirect()->back()->with('success', __('Call successfully created!'))->with('status', 'calls');
            }
            else
            {
                return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'calls');
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'calls');
        }
    }

    public function callEdit($id, $call_id)
    {
        if(\Auth::user()->can('edit lead call'))
        {
            $lead = Lead::find($id);
            if($lead->created_by == \Auth::user()->creatorId())
            {
                $call  = LeadCall::find($call_id);
                $users = UserLead::where('lead_id', '=', $lead->id)->get();

                return view('leads.calls', compact('call', 'lead', 'users'));
            }
            else
            {
                return response()->json(
                    [
                        'is_success' => false,
                        'error' => __('Permission Denied.'),
                    ], 401
                );
            }
        }
        else
        {
            return response()->json(
                [
                    'is_success' => false,
                    'error' => __('Permission Denied.'),
                ], 401
            );
        }
    }

    public function callUpdate($id, $call_id, Request $request)
    {
        if(\Auth::user()->can('edit lead call'))
        {
            $lead = Lead::find($id);
            if($lead->created_by == \Auth::user()->creatorId())
            {
                $validator = \Validator::make(
                    $request->all(), [
                                       'subject' => 'required',
                                       'call_type' => 'required',
                                       'user_id' => 'required',
                                   ]
                );

                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }

                $call = LeadCall::find($call_id);

                $call->update(
                    [
                        'subject' => $request->subject,
                        'call_type' => $request->call_type,
                        'duration' => $request->duration,
                        'user_id' => $request->user_id,
                        'description' => $request->description,
                        'call_result' => $request->call_result,
                    ]
                );

                return redirect()->back()->with('success', __('Call successfully updated!'))->with('status', 'calls');
            }
            else
            {
                return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'calls');
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'tasks');
        }
    }

    public function callDestroy($id, $call_id)
    {
        if(\Auth::user()->can('delete lead call'))
        {
            $lead = Lead::find($id);
            if($lead->created_by == \Auth::user()->creatorId())
            {
                $task = LeadCall::find($call_id);
                $task->delete();

                return redirect()->back()->with('success', __('Call successfully deleted!'))->with('status', 'calls');
            }
            else
            {
                return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'calls');
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'calls');
        }
    }

    // Lead email
    public function emailCreate($id)
    {
        if(\Auth::user()->can('create lead email'))
        {
            $lead = Lead::find($id);
            if($lead->created_by == \Auth::user()->creatorId())
            {
                return view('leads.emails', compact('lead'));
            }
            else
            {
                return response()->json(
                    [
                        'is_success' => false,
                        'error' => __('Permission Denied.'),
                    ], 401
                );
            }
        }
        else
        {
            return response()->json(
                [
                    'is_success' => false,
                    'error' => __('Permission Denied.'),
                ], 401
            );
        }
    }

    public function emailStore($id, Request $request)
    {

        if(\Auth::user()->can('create lead email'))
        {
            $lead = Lead::find($id);

            if($lead->created_by == \Auth::user()->creatorId())
            {
                $settings  = Utility::settings();
                $validator = \Validator::make(
                    $request->all(), [
                                       'to' => 'required|email',
                                       'subject' => 'required',
                                       'description' => 'required',
                                   ]
                );

                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }

                $leadEmail = LeadEmail::create(
                    [
                        'lead_id' => $lead->id,
                        'to' => $request->to,
                        'subject' => $request->subject,
                        'description' => $request->description,
                    ]
                );

                $leadEmail =
                    [
                        'lead_name' => $lead->name,
                        'to' => $request->to,
                        'subject' => $request->subject,
                        'description' => $request->description,
                    ];


                try
                {
                    Mail::to($request->to)->send(new SendLeadEmail($leadEmail, $settings));
                }
                catch(\Exception $e)
                {

                    $smtp_error = __('E-Mail has been not sent due to SMTP configuration');
                }
//

                LeadActivityLog::create(
                    [
                        'user_id' => \Auth::user()->id,
                        'lead_id' => $lead->id,
                        'log_type' => 'create lead email',
                        'remark' => json_encode(['title' => 'Create new Deal Email']),
                    ]
                );

                return redirect()->back()->with('success', __('Email successfully created!') . ((isset($smtp_error)) ? '<br> <span class="text-danger">' . $smtp_error . '</span>' : ''))->with('status', 'emails');
            }
            else
            {
                return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'emails');
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'emails');
        }
    }
    public function getPipeline(Request $request) {

        $usr   = \Auth::user();
        $user = User::find($usr->id);
        $user->default_pipeline = $request->id;
        $user->save();
        if(!empty($request->user_id)) {
            $pipeline = Pipeline::where('created_by', '=', \Auth::user()->creatorId())->where('id', '=', $request->id)->first();
            $lead_id = UserLead :: where('user_id', $request->user_id)->get()->pluck('lead_id');
            $searchLead = Lead :: whereIn('id', $lead_id)->whereNull('is_converted')->get();

            return view('leads.searchAllLeads', compact( 'pipeline','searchLead'));
        }else{
            if ($request->id == null) {
                $pipelineSearch = \Auth::user()->default_pipeline ;
            }else{
                $pipelineSearch = $request->id;
            }



            $pipeline = Pipeline::where('created_by', '=', \Auth::user()->creatorId())->where('id',$request->id)->first();
            return view('leads.lead', compact('pipeline'));
        }
    }

     // Leads Search Function

     public function searchLead(Request $request){

        $usr = \Auth::user();
        if($usr->can('manage lead'))
        {
            $usr   = \Auth::user();
            $client_id = $request->clinet_id;
            $lead_type_id = $request->lead_type_id;
            $client_id = $request->client_id;
            $sources = $request->source_id;
            $pipeline = Pipeline::where('created_by', '=', \Auth::user()->creatorId())->where('id',$usr->default_pipeline)->first();

                $lead_id = UserLead :: where('user_id', $request->id)->get()->pluck('lead_id');
                $query = DB::table('leads')
                    ->whereNull('is_converted')
                    ->whereNull('is_reject');

                if ($lead_id && !$lead_id->isEmpty()) {
                    $query->whereIn('id', $lead_id->toArray());
                }

                if ($lead_type_id !== null) {
                    $query->where('lead_type', $lead_type_id);
                }

                if ($client_id !== null) {
                    $query->where('client_id', $client_id);
                }

                if ($sources !== null) {
                    $query->where('sources', $sources);
                }

                $searchLead = $query->get();

            // dd($searchLead);

            return view('leads.searchAllLeads', compact('pipeline','searchLead'));

        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

    }

    public function getService(Request $request){
        if ($request->service_type == 1) {
           return view('leads.personal_finanical');
        }elseif ($request->service_type == 2) {
            return view('leads.mortage');
        }else{
            return view('leads.sem');
        }
    }
    public function getClientData(Request $request)
    {
        return optional(Client::where('phone', $request->phone)->first(), function ($client) use ($request) {
            return [
                'leadData' => optional(Lead::with('pipeline', 'stage')
                    ->where('products', $request->products)
                    ->where('client_id', $client->id)
                    ->first(), function ($leadData) {
                        return [
                            'pipeline_name' => optional($leadData->pipeline)->name,
                            'stage_name' => optional($leadData->stage)->name,
                        ];
                    }),
            ];
        }) ?? response()->json(['error' => 'Client or lead not found'], 404);
    }

    //Lead Transfer Function

    public function transfer($id)
    {
        $lead = Lead::find($id);
        $pipelines      = Pipeline::where('created_by', '=', \Auth::user()->creatorId())->get();
        $sources        = Source::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
        $products       = ProductService::where('created_by', '=', \Auth::user()->creatorId())->get();
        $users          = User::where('created_by', '=', \Auth::user()->creatorId())->where('type', '!=', 'client')->where('type', '!=', 'company')->where('designation', '!=', 'Jovera')->where('id', '!=', \Auth::user()->id)->get();
        $lead->sources  = explode(',', $lead->sources);
        return view('leads.transfer', compact('lead', 'pipelines', 'sources', 'products', 'users'));
    }

    public function is_transfered($id,Request $request){
        $validator = \Validator::make(
            $request->all(), [
                'products' => 'required',
                'pipeline' => 'required',
                'user_id' => 'required',
            ]
        );

        if($validator->fails())
        {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }
        $stage_id = LeadStage::where('pipeline_id', '=', $request->pipeline)->first();
        $stage = $request->stage_id ? $request->stage_id : $stage_id->id;

        if(empty($stage))
        {
            return redirect()->back()->with('error', __('Please Create Stage for This Pipeline.'));
        }else{

            $lead = Lead::find($id);
            $lead->products = $request->products;
            $lead->pipeline_id = $request->pipeline;
            $lead->stage_id    = $stage;
            $lead->is_transfer    = 1;
            $lead->save();
            $lead->users()->sync($request->input('user_id', []));
            $additionalUserIds = [2, 6, 40];
            $lead->users()->syncWithoutDetaching($additionalUserIds);
            LeadActivityLog::create([
                'user_id'=> \Auth::user()->id,
                'lead_id'=> $lead->id,
                'log_type'=> "Transfer Lead",
                'remark'=> "Transfer Lead",
            ]);

            return redirect()->back()->with('success', __('Lead successfully transfered!'));
        }

    }


    public function reject($id,Request $request){
        $pipelines = lead::where('id',$id)->update(['is_reject' => 1,'notes'=> $request->note,'pipeline_id'=>10]);
        if ($pipelines == 1) {

            return 'true';
        }
    }

    public function getSourses($id){
        $sources  = Source::where('created_by', '=', \Auth::user()->creatorId())->where('lead_type_id',$id)->get();
        return view('leads.source',compact('sources'));
    }
    public function getServiceInfo($id){
        if ($id == 1) {
            return view('businessBanking.create');
        }elseif ($id == 2) {
            return view('personalLoad.create');
        }elseif ($id == 3) {
            return view('mortgageLoan.create');
        }elseif ($id == 4) {
            return view('realEstate.create');
        }else{
            return redirect()->back()->with('Error', __('There Is No any other Service Info Form!'));
        }
    }
    public function editServiceInfo($id , $lead_id){

        if ($id == 1) {
            $data = BusinessBanking::where('lead_id',$lead_id)->first();
            return view('businessBanking.edit', compact('data'));
        }elseif ($id == 2) {
            $data = PersonalLoan::where('lead_id',$lead_id)->first();
            return view('personalLoad.edit', compact('data'));
        }elseif ($id == 3) {
            $data = MortgageLoan::where('lead_id',$lead_id)->first();
            return view('mortgageLoan.edit', compact('data'));
        }elseif ($id == 4) {
            $data = RealEstate::where('lead_id',$lead_id)->first();
            return view('realEstate.edit','data');
        }else{
            return redirect()->back()->with('Error', __('There Is No any other Service Info Form!'));
        }


    }



}
