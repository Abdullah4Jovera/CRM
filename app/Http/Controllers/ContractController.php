<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Contract;
use App\Models\Contract_attachment;
use App\Models\ContractComment;
use App\Models\ContractNotes;
use App\Models\ContractType;
use App\Models\Project;
use App\Models\ServiceCommission;
use App\Models\ActivityLog;
use App\Models\User;
use App\Models\Deal;
use App\Models\Lead;
use App\Models\UserDefualtView;
use App\Models\UserLead;
use App\Models\UserNotifications;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class ContractController extends Controller
{

    public function index()
    {
        if(\Auth::user()->can('manage contract'))
        {
            if (\Auth::user()->designation == 'Jovera') {
                $contracts   = Deal::where('contract_stage','readToSign')->orderBy('created_at','DESC')->get();
            } else {
                $contracts   = Deal::where('contract_stage','unsigned')->orWhere('contract_stage','pending')->orWhere('contract_stage','readToSign')->orderBy('created_at','DESC')->get();
            }

            return view('contract.index', compact('contracts'));

        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

    }


    public function create()
    {
        $contractTypes = ContractType::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
        $clients       = User::where('type', 'client')->where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
        $clients->prepend(__('Select Client'),0);
        $project       = Project::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('project_name', 'id');
        return view('contract.create', compact('contractTypes', 'clients','project'));
    }


    public function store(Request $request)
    {
        if(\Auth::user()->can('create contract'))
        {
            $rules = [
                'client_name' => 'required',
                'subject' => 'required',
                'type' => 'required',
                'value' => 'required',
                'start_date' => 'required',
                'end_date' => 'required',
            ];

            $validator = \Validator::make($request->all(), $rules);

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->route('contract.index')->with('error', $messages->first());
            }

            $contract              = new Contract();
            $contract->client_name      = $request->client_name;
            $contract->subject     = $request->subject;
            $contract->project_id  =$request->project_id;
            $contract->type        = $request->type;
            $contract->value       = $request->value;
            $contract->start_date  = $request->start_date;
            $contract->end_date    = $request->end_date;
            $contract->description = $request->description;
            $contract->created_by  = \Auth::user()->creatorId();
            $contract->save();

            //Send Email
            $setings = Utility::settings();
            if($setings['new_contract'] == 1) {

                $client = \App\Models\User::find($request->client_name);
                $contractArr = [
                    'contract_subject' => $request->subject,
                    'contract_client' => $client->name,
                    'contract_value' => \Auth::user()->priceFormat($request->value),
                    'contract_start_date' => \Auth::user()->dateFormat($request->start_date),
                    'contract_end_date' => \Auth::user()->dateFormat($request->end_date),
                    'contract_description' => $request->description,
                ];

                // Send Email
                $resp = Utility::sendEmailTemplate('new_contract', [$client->id => $client->email], $contractArr);

            }

            //For Notification
            $setting  = Utility::settings(\Auth::user()->creatorId());
            $client = \App\Models\User::find($request->client_name);
            $contractNotificationArr = [
                'contract_subject' => $request->subject,
                'contract_client' => $client->name,
                'contract_value' => \Auth::user()->priceFormat($request->value),
                'contract_start_date' => \Auth::user()->dateFormat($request->start_date),
                'contract_end_date' =>\Auth::user()->dateFormat($request->end_date),
                'user_name' => \Auth::user()->name,
            ];
            //Slack Notification
            if(isset($setting['contract_notification']) && $setting['contract_notification'] ==1)
            {
                Utility::send_slack_msg('new_contract', $contractNotificationArr);
            }
            //Telegram Notification
            if(isset($setting['telegram_contract_notification']) && $setting['telegram_contract_notification'] ==1)
            {
                Utility::send_telegram_msg('new_contract', $contractNotificationArr);
            }

            //webhook
            $module ='New Contract';
            $webhook=  Utility::webhookSetting($module);
            if($webhook)
            {
                $parameter = json_encode($contract);
                $status = Utility::WebhookCall($webhook['url'],$parameter,$webhook['method']);

                if($status == true)
                {
                    return redirect()->back()->with('success', __('Contract successfully created!') .((!empty ($resp) && $resp['is_success'] == false && !empty($resp['error'])) ? '<br> <span class="text-danger">' . $resp['error'] . '</span>' : ''));
                }
                else
                {
                    return redirect()->back()->with('error', __('Webhook call failed.'));
                }
            }

            return redirect()->back()->with('success', __('Contract successfully created!') .((!empty ($resp) && $resp['is_success'] == false && !empty($resp['error'])) ? '<br> <span class="text-danger">' . $resp['error'] . '</span>' : ''));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

    }

    public function show($id)
    {
        if (\Auth::user()->can('show contract')) {
            $dealUser = Deal::join('leads as ld', 'deals.id', '=', 'ld.is_converted')
                ->join('user_leads as ul', 'ld.id', '=', 'ul.lead_id')
                ->where('deals.id', $id)
                ->where('ul.user_id', \Auth::user()->id)
                ->first();
            if ($dealUser) {
                $deal = Deal::find($id);

                if ($deal && $deal->contract_stage != 'cm_signed' ) {
                    return view('contract.show', compact('deal'));
                } else {
                    return redirect()->route('dashboard')->with('error', __('Permission Denied.'));
                }
            } else {
                return redirect()->back()->with('error', __('Permission Denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
    public function edit(Deal $contract)
    {
        $dealUser = Deal::join('leads as ld', 'deals.id', '=', 'ld.is_converted')
                ->join('user_leads as ul', 'ld.id', '=', 'ul.lead_id')
                ->where('deals.id', $contract->id)
                ->where('ul.user_id', \Auth::user()->id)
                ->first();

        if ($dealUser ){
            if ($dealUser->contract_stage != 'cm_signed' || \Auth::user()->id == 6 || \Auth::user()->id == 13) {
                $users = User::where('created_by', \Auth::user()->creatorId())->get();
                return view('contract.edit', compact('users', 'contract'));
            }else{
                return redirect()->route('dashboard')->with('error', __('Permission Denied.'));
            }
        }else {
            return redirect()->route('dashboard')->with('error', __('Permission Denied.'));
        }
    }



    public function update(Request $request, ServiceCommission $contract)
    {
        if(\Auth::user()->can('edit contract'))
        {
            $contract->finance_amount=  $request->finance_amount;
            $contract->bank_commission=  $request->bank_commission;
            $contract->customer_commission=  $request->customer_commission;
            $contract->with_vat_commission=  $request->with_vat_commission;
            $contract->without_vat_commission=  $request->without_vat_commission;
            $contract->hodsale= $request->hodsale;
            $contract->hodsalecommission= $request->hodsalecommission;
            $contract->ts_hod = $request->ts_hod;
            $contract->ts_hod_commision = $request->ts_hod_commission;
            $contract->salemanager= $request->salemanager;
            $contract->salemanagercommission= $request->salemanagercommission;
            $contract->coordinator= $request->coordinator;
            $contract->coordinator_commission= $request->coordinator_commission;
            $contract->team_leader= $request->team_leader;
            $contract->team_leader_commission= $request->team_leader_commission;
            $contract->team_leader_one = $request->team_leader_one;
            $contract->team_leader_one_commission = $request->team_leader_one_commission;
            $contract->salesagent= $request->salesagent;
            $contract->salesagent_commission= $request->salesagent_commission;
            $contract->sale_agent_one = $request->sale_agent_one;
            $contract->sale_agent_one_commission = $request->sale_agent_one_commission;
            $contract->salemanagerref= $request->salemanagerref;
            $contract->salemanagerrefcommission= $request->salemanagerrefcommission;
            $contract->agentref= $request->agentref;
            $contract->agent_commission= $request->agent_commission;
            $contract->ts_team_leader= $request->ts_team_leader;
            $contract->ts_team_leader_commission= $request->ts_team_leader_commission;
            $contract->tsagent= $request->tsagent;
            $contract->tsagent_commission= $request->tsagent_commission;
            $contract->marketingmanager= $request->marketingmanager;
            $contract->marketingmanagercommission= $request->marketingmanagercommission;
            $contract->marketingagent= $request->marketingagent;
            $contract->marketingagentcommission= $request->marketingagentcommission;
            $contract->other_name= $request->other_name;
            $contract->other_name_commission= $request->other_commission;
            $contract->broker_name= $request->broker_name;
            $contract->broker_name_commission= $request->broker_commission;
            $contract->save();

            $deal = Deal::find($contract->deal_id);
            $activity = ActivityLog::create([
                'user_id' => \Auth::user()->id,
                'deal_id' => $deal->id,
                'log_type' => 'Service Appliaction Update',
                'remark' => json_encode([
                    'title' => $deal->leads->name,
                    'old_status' => 'Update',
                    'old_status' => 'Service Application',
                ]),
            ]);
            $lead = Lead::where('is_converted',$deal->id)->first();
            $usersIds = UserLead::where('lead_id',$lead->id)->get();
            // dd($usersIds);
            foreach ($usersIds as $usersId) {
                UserNotifications::create([
                    'activity_id'=>$activity->id,
                    'user_id'=>$usersId->user_id,
                ]);

            }


            // return redirect()->route('contract.index')->with('success', __('Contract successfully updated.'));
            return redirect()->back()->with('success', __('Contract successfully updated.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function destroy(Contract $contract)
    {
        if(\Auth::user()->can('delete contract'))
        {
            $contract->delete();

            return redirect()->route('contract.index')->with('success', __('Contract successfully deleted.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function description($id)
    {
        $contract = Contract::find($id);

        return view('contract.description', compact('contract'));
    }

    public function grid()
    {
        if(\Auth::user()->type == 'company' || \Auth::user()->type == 'client')
        {
            if(\Auth::user()->type == 'company')
            {
                $contracts = Contract::where('created_by', '=', \Auth::user()->creatorId())->get();
            }
            else
            {
                $contracts = Contract::where('client_name', '=', \Auth::user()->id)->get();
            }

         /*   $defualtView         = new UserDefualtView();
            $defualtView->route  = \Request::route()->getName();
            $defualtView->module = 'contract';
            $defualtView->view   = 'grid';
            User::userDefualtView($defualtView);*/
            return view('contract.grid', compact('contracts'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

    }

    public function fileUpload($id, Request $request)
    {


            $contract = Deal::find($id);
            $request->validate(['file' => 'required']);
            $files = $id . $request->file->getClientOriginalName();
            $dir = 'contract_attechment/';
            // $files = $request->file->getClientOriginalName();
            $path = Utility::upload_file($request,'file',$files,$dir,[]);
            if($path['flag'] == 1){
                $file = $path['url'];
            }
            else{

                return redirect()->back()->with('error', __($path['msg']));
            }

            // $request->file->storeAs('contract_attechment', $files);
            $file = Contract_attachment::create(
                [
                    'deal_id' => $request->contract_id,
                    'user_id' => \Auth::user()->id,
                    'files' => $files,
                ]
            );

            $return               = [];
            $return['is_success'] = true;
            $return['download']   = route(
                'contracts.file.download', [
                    $contract->id,
                    $file->id,
                ]
            );

            $return['delete']     = route(
                'contracts.file.delete', [
                    $contract->id,
                    $file->id,
                ]
            );

            return response()->json($return);

    }
    public function fileDownload($id, $file_id)
    {

        $contract        =Contract::find($id);
        if(\Auth::user()->type == 'company')
        {
            $file = Contract_attachment::find($file_id);
            if($file)
            {
                $file_path = storage_path('contract_attechment/' . $file->files);


                return \Response::download(
                    $file_path, $file->files, [
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

    public function fileDelete($id, $file_id)
    {
        $contract = Contract::find($id);

        $file =  Contract_attachment::find($file_id);
        if($file)
        {
            $path = storage_path('contract_attechment/' . $file->files);
            if(file_exists($path))
            {
                \File::delete($path);
            }
            $file->delete();

            return redirect()->back()->with('success', __('contract file successfully deleted.'));

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

    public function contract_status_edit(Request $request, $id)
    {
        // dd($request->all());
        $contract = Contract::find($id);
        $contract->status   = $request->status;
        $contract->save();

    }
    public function commentStore(Request $request ,$id)
    {
        $contract              = new ContractComment();
        $contract->comment     = $request->comment;
        $contract->contract_id = $request->id;
        $contract->user_id     = \Auth::user()->id;
        $contract->save();
        // dd($contract);


        return redirect()->back()->with('success', __('comments successfully created!') . ((isset($smtp_error)) ? '<br> <span class="text-danger">' . $smtp_error . '</span>' : ''))->with('status', 'comments');

    }
//    public function contract_descriptionStore($id, Request $request)
//    {
//        if(\Auth::user()->type == 'company')
//        {
//            $contract        =Contract::find($id);
//            $contract->contract_description = $request->contract_description;
//            $contract->save();
//            return redirect()->back()->with('success', __('Contact Description successfully saved.'));
//
//        }
//        else
//        {
//            return redirect()->back()->with('error', __('Permission denied'));
//
//        }
//    }

    public function contract_descriptionStore($id, Request $request)
    {
        if(\Auth::user()->type == 'company')
        {
            $contract        =Contract::find($id);
            if($contract->created_by == \Auth::user()->creatorId())
            {
                $contract->contract_description = $request->contract_description;
                $contract->save();

                return response()->json(
                    [
                        'is_success' => true,
                        'success' => __('Contract description successfully saved!'),
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

    public function commentDestroy( $id)
    {
        $contract = ContractComment::find($id);

        $contract->delete();

        return redirect()->back()->with('success', __('Comment successfully deleted!'));

    }
    public function noteStore($id, Request $request)
    {
        $contract              = Contract::find($id);
        $notes                 = new ContractNotes();
        $notes->contract_id    = $contract->id;
        $notes->notes           = $request->notes;
        $notes->user_id        = \Auth::user()->id;
        $notes->save();
        return redirect()->back()->with('success', __('Note successfully saved.'));


    }
    public function noteDestroy($id)
    {
        $contract = ContractNotes::find($id);
        $contract->delete();

        return redirect()->back()->with('success', __('Note successfully deleted!'));

    }
    public function clientwiseproject($id)

    {
        $projects = Project::where('client_id', $id)->get();


        $users=[];
        foreach($projects as $key => $value )
        {
            $users[]=[
                'id' => $value->id,
                'name' => $value->project_name,
            ];

        }
        // dd($users);

        return \Response::json($users);
    }

    public function printContract($id)
    {
        $deal  = Deal::findOrFail($id);
        $dealUser = Deal::join('leads as ld', 'deals.id', '=', 'ld.is_converted')
                ->join('user_leads as ul', 'ld.id', '=', 'ul.lead_id')
                ->where('deals.id', $deal->id)
                ->where('ul.user_id', \Auth::user()->id)
                ->first();
        $settings = Utility::settings();

        // $client   = $contract->clients->first();
        //Set your logo
        $logo         = asset(Storage::url('uploads/logo/'));
        $company_logo = Utility::getValByName('company_logo');
        $img          = asset($logo . '/' . (isset($company_logo) && !empty($company_logo) ? $company_logo : 'logo-dark.png'));


        if($dealUser)
        {
            $color      = '#' . $settings['invoice_color'];
            $font_color = Utility::getFontColor($color);

            return view('contract.preview' , compact('deal', 'color', 'img','settings','font_color'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function copycontract($id)
    {
        $contract = Contract::find($id);
        $clients       = User::where('type', '=', 'Client')->get()->pluck('name', 'id');
        $contractTypes = ContractType::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
        $project       = Project::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('title','id');
        $date         = $contract->start_date . ' to ' . $contract->end_date;
        $contract->setAttribute('date', $date);

        return view('contract.copy', compact('contract','contractTypes','clients','project'));


    }

    public function copycontractstore(Request $request)
    {

        if(\Auth::user()->type == 'company')
        {
            $rules = [
                'client' => 'required',
                'subject' => 'required',
                'project_id' => 'required',
                'type' => 'required',
                'value' => 'required',
                'status'=>'Pending',
                'start_date' => 'required',
                'end_date' => 'required',
            ];

            $validator = \Validator::make($request->all(), $rules);

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->route('contract.index')->with('error', $messages->first());
            }
            // $date = explode(' to ', $request->date);
            $contract              = new Contract();
            $contract->client_name      = $request->client;
            $contract->subject     = $request->subject;
            $contract->project_id  = implode(',',$request->project_id);
            $contract->type        = $request->type;
            $contract->value       = $request->value;
            $contract->start_date  = $request->start_date;
            $contract->end_date    = $request->end_date;
            $contract->description = $request->description;
            $contract->created_by  = \Auth::user()->creatorId();
            $contract->save();

            //Send Email
            $setings = Utility::settings();
            if($setings['new_contract'] == 1) {

                $client = \App\Models\User::find($request->client);
                $contractArr = [
                    'contract_subject' => $request->subject,
                    'contract_client' => $client->name,
                    'contract_value' => \Auth::user()->priceFormat($request->value),
                    'contract_start_date' => \Auth::user()->dateFormat($request->start_date),
                    'contract_end_date' => \Auth::user()->dateFormat($request->end_date),
                    'contract_description' => $request->description,
                ];

                // Send Email
                $resp = Utility::sendEmailTemplate('new_contract', [$client->id => $client->email], $contractArr);

                return redirect()->route('contract.index')->with('success', __('Contract successfully created.') . (($resp['is_success'] == false && !empty($resp['error'])) ? '<br> <span class="text-danger">' . $resp['error'] . '</span>' : ''));

            }


            //Slack Notification
            $setting  = Utility::settings(\Auth::user()->creatorId());
            if(isset($setting['contract_notification']) && $setting['contract_notification'] ==1){
                $msg = $request->subject .' '.__("created by").' ' .\Auth::user()->name.'.';
                Utility::send_slack_msg($msg);
            }

            //Telegram Notification
            $setting  = Utility::settings(\Auth::user()->creatorId());
            if(isset($setting['telegram_contract_notification']) && $setting['telegram_contract_notification'] ==1){
                $msg = $request->subject .' '.__("created by").' ' .\Auth::user()->name.'.';
                Utility::send_telegram_msg($msg);
            }

            return redirect()->route('contract.index')->with('success', __('Contract successfully created.'));


        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

    }

    public function sendmailContract($id,Request $request)
    {

        $contract = Contract::find($id);
        $contractArr = [
            'contract_id' => $contract->id,
        ];
        $setings = Utility::settings();
        if ($setings['new_contract'] == 1) {

            $client = User::find($contract->client_name);

            $estArr = [
                'email' => $client->email,
                'contract_subject' => $contract->subject,
                'contract_client' => $client->name,
                'contract_start_date' => $contract->start_date,
                'contract_end_date' => $contract->end_date,
            ];
            $resp = Utility::sendEmailTemplate('new_contract', [$client->id => $client->email], $estArr);
            return redirect()->route('contract.show', $contract->id)->with('success', __('Email Send successfully!') . (($resp['is_success'] == false && !empty($resp['error'])) ? '<br> <span class="text-danger">' . $resp['error'] . '</span>' : ''));
        }
    }

    public function signature($id)
    {
        $contract = Contract::find($id);
        return view('contract.signature', compact('contract'));

    }
    public function signatureStore(Request $request)
    {
        $contract              = Contract::find($request->contract_id);

        if(\Auth::user()->type == 'company'){
            $contract->company_signature       = $request->company_signature;
        }
        if(\Auth::user()->type == 'client'){
            $contract->client_signature       = $request->client_signature;
        }

        $contract->save();

        return response()->json(
            [
                'Success' => true,
                'message' => __('Contract Signed successfully'),
            ], 200
        );

    }

    public function pdffromcontract($deal_id)
    {
        $id = \Illuminate\Support\Facades\Crypt::decrypt($deal_id);
        $deal  = Deal::findOrFail($id);
        return view('contract.template', compact('deal'));
    }
    public function dealStatus($id) {
        $status = Deal::where('id',$id)->first();
        return view('contract.dealStatus' ,compact('status'));
    }
    // public function statusUpdate($id,Request $request) {
    //     $status = Deal::find($id);
    //     $old_status;
    //     $new_status;
    //     if ($request->contract_status == 'cm_signed') {
    //         $activity = ActivityLog::create([
    //             'user_id' => \Auth::user()->id,
    //             'deal_id' => $status->id,
    //             'log_type' => 'Convert To Deal',
    //             'remark' => json_encode([
    //                 'title' => $status->leads->name,
    //                 'old_status' => 'Service Application',
    //                 'new_status' => 'Deal',
    //             ]),
    //         ]);
    //         $status->contract_stage = $request->contract_status;
    //         $status->save();
    //         $lead = Lead::where('is_converted',$status->id)->first();
    //         $usersIds = UserLead::where('lead_id',$lead->id)->get();
    //         // dd($usersIds);
    //         foreach ($usersIds as $usersId) {
    //             UserNotifications::create([
    //                 'activity_id'=>$activity->id,
    //                 'user_id'=>$usersId->user_id,
    //             ]);

    //         }
    //             return redirect()->route('deals.index')->with('success', __('Service Application Converted Successfully To Deal .'));

    //     }
    //     if ($status->contract_stage == 'unsigned' ) {
    //         $old_status = 'New';
    //     }elseif ($status->contract_stage == 'pending') {
    //         $old_status = "Pending";
    //     }elseif ($status->contract_stage == 'readToSign') {
    //         $old_status =' Read To Sign';
    //     }else {
    //         $old_status = 'Customer Sign';
    //     }
    //     if ($request->contract_status == 'unsigned' ) {
    //         $new_status = 'New';
    //     }elseif ($request->contract_status == 'pending') {
    //         $new_status = "Pending";
    //     }elseif ($request->contract_status == 'readToSign') {
    //         $new_status =' Read To Sign';
    //     }else {
    //         $new_status = 'Customer Sign';
    //         Lead::update(['deal_stage_id'=>1])->wherewhere('is_converted',$status->id);
    //     }

    //         $activity = ActivityLog::create([
    //             'user_id' => \Auth::user()->id,
    //             'deal_id' => $status->id,
    //             'log_type' => 'Service Appliaction Status',
    //             'remark' => json_encode([
    //                 'title' => $status->leads->name,
    //                 'old_status' => $old_status,
    //                 'new_status' => $new_status,
    //             ]),
    //         ]);
    //         $status->contract_stage = $request->contract_status;
    //         $status->save();
    //         $lead = Lead::where('is_converted',$status->id)->first();
    //         $usersIds = UserLead::where('lead_id',$lead->id)->get();
    //         // dd($usersIds);
    //         foreach ($usersIds as $usersId) {
    //             UserNotifications::create([
    //                 'activity_id'=>$activity->id,
    //                 'user_id'=>$usersId->user_id,
    //             ]);

    //         }
    //     return redirect()->route('contract.index')->with('success', __('Service Application Status successfully updated.'));
    // }
    public function statusUpdate($id, Request $request) {
        // dd($id, $request);
        $status = Deal::find($id);
        $lead = Lead::where('is_converted', $status->id)->first();
        $old_status = $this->getContractStatus($status->contract_stage, $lead->id);
        $new_status = $this->getContractStatus($request->contract_status , $lead->id);

        $activityData = [
            'user_id' => auth()->id(),
            'deal_id' => $status->id,
            'log_type' => ($request->contract_status == 'cm_signed') ? 'Convert To Deal' : 'Service Application Status',
            'remark' => json_encode([
                'title' => $status->leads->name,
                'old_status' => $old_status,
                'new_status' => $new_status,
            ]),
        ];

        $activity = ActivityLog::create($activityData);

        $status->contract_stage = $request->contract_status;
        $status->save();


        $usersIds = UserLead::where('lead_id', $lead->id)->pluck('user_id');

        foreach ($usersIds as $userId) {
            UserNotifications::create([
                'activity_id' => $activity->id,
                'user_id' => $userId,
            ]);
        }

        $redirectRoute = ($request->contract_status == 'cm_signed') ? 'deals.index' : 'contract.index';

        return redirect()->route($redirectRoute)->with('success', __('Service Application Status successfully updated.'));
    }

    private function getContractStatus($status , $id) {
        switch ($status) {
            case 'unsigned':
                return 'New';
            case 'pending':
                return 'Pending';
            case 'readToSign':
                return 'Read To Sign';
            default:
                // dd($id);
                Lead::where('id', $id)->update(['deal_stage_id' => 1]);

                return 'Customer Sign';
        }
    }








}
