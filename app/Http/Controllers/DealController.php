<?php

namespace App\Http\Controllers;

use App\Mail\SendDealEmail;
use App\Models\ActivityLog;
use App\Models\ClientDeal;
use App\Models\ClientPermission;
use App\Models\CustomField;
use App\Models\Deal;
use App\Models\Lead;
use App\Models\DealCall;
use App\Models\DealDiscussion;
use App\Models\DealEmail;
use App\Models\DealFile;
use App\Models\DealTask;
use App\Models\ServiceCommission;
use App\Models\Label;
use App\Models\Pipeline;
use App\Models\ProductService;
use App\Models\Source;
use App\Models\Stage;
use App\Models\User;
use App\Models\UserDeal;
use App\Models\UserLead;
use App\Models\UserNotifications;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class DealController extends Controller
{
    /**
     * Display a listing of the redeal.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $usr      = \Auth::user();
        $pipeline = Pipeline::where('created_by', '=', $usr->ownerId())->where('id', '=', $usr->default_pipeline)->first();

        if($usr->can('manage deal'))
        {
            if($usr->default_pipeline)
            {
                $pipeline = Pipeline::where('created_by', '=', $usr->ownerId())->where('id', '=', $usr->default_pipeline)->first();
                if(!$pipeline)
                {
                    $pipeline = Pipeline::where('created_by', '=', $usr->ownerId())->first();
                }
                if (\Auth::user()->default_pipeline == 1) {
                    $allDeals = Deal::where('created_by',\Auth::user()->creatorId())->get();
                }else {
                    $allDeals = Deal::where('pipeline_id',\Auth::user()->default_pipeline)->where('created_by',\Auth::user()->creatorId())->get();
                }

            }
            else
            {
                $pipeline = Pipeline::where('created_by', '=', $usr->ownerId())->first();
            }
            $pipelines = Pipeline::where('created_by', '=', $usr->ownerId())->get()->pluck('name', 'id');

            $pipelineDeals = Pipeline::where('created_by', '=', $usr->ownerId())->get();

            if($usr->type == 'client')
            {
                $id_deals = $usr->clientDeals->pluck('id');
            }
            else
            {
                $id_deals = $usr->deals->pluck('id');
            }


            $lead_id       = Lead::where('deal_stage_id',6)->join('user_leads','leads.id','=','user_leads.lead_id')->where('user_leads.user_id','=',\Auth::user()->id)->where('pipeline_id',\Auth::user()->default_pipeline)->get()->pluck('is_converted');
            $collection       = ServiceCommission::whereIn('deal_id',$lead_id)->sum('with_vat_commission');
            $collection_month  = ServiceCommission::whereIn('deal_id',$lead_id)->whereMonth('created_at', '=', date('m'))->sum('with_vat_commission');

            $lead_id_reject       = Lead::where('deal_stage_id',4)->join('user_leads','leads.id','=','user_leads.lead_id')->where('user_leads.user_id','=',\Auth::user()->id)->where('pipeline_id',\Auth::user()->default_pipeline)->get()->pluck('is_converted');
            $reject       = ServiceCommission::whereIn('deal_id',$lead_id_reject)->sum('with_vat_commission');
            $reject_month  = ServiceCommission::whereIn('deal_id',$lead_id_reject)->whereMonth('created_at', '=', date('m'))->sum('with_vat_commission');

            $deal       = Lead::join('user_leads','leads.id','=','user_leads.lead_id')->where('user_leads.user_id','=',\Auth::user()->id)->where('pipeline_id',\Auth::user()->default_pipeline)->get()->pluck('is_converted');
            $id  = Deal::whereIn('id',$deal)->where('contract_stage','cm_signed')->where('pipeline_id',\Auth::user()->default_pipeline)->get()->pluck('id');
            $curr_month  = ServiceCommission::whereIn('deal_id',$id)->whereMonth('created_at', '=', date('m'))->sum('with_vat_commission');
            $deals       = ServiceCommission::whereIn('deal_id',$id)->sum('with_vat_commission');
            $cnt_deal                = [];
            $cnt_deal['total']       = number_format($deals - $reject);
            $cnt_deal['curr_month'] = number_format($curr_month - $reject_month);
            $cnt_deal['collection']  = number_format($collection);
            $cnt_deal['collection_month']   = number_format($collection_month);
            $cnt_deal['reject']  = number_format($reject);
            $cnt_deal['reject_month']   = number_format($reject_month);

            $users = User::select('name','id')->where('created_by', '=', \Auth::user()->creatorId())->where('type', '!=', 'client')->where('type', '!=', 'company')->where('id', '!=', \Auth::user()->id)->get();
            $new_pipelines = Pipeline::select('name','id')->where('created_by', '=', \Auth::user()->creatorId())->get();
            return view('deals.index', compact('pipelines', 'pipeline', 'cnt_deal','allDeals','pipelineDeals' ,'new_pipelines', 'users'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function deal_list()
    {
        $usr = \Auth::user();
        if($usr->can('manage deal'))
        {
            $pipeline = Pipeline::where('created_by', '=', $usr->ownerId())->where('id', '=', 10)->first();

            // $deals = Deal::where('pipeline_id',10)->get();
            $user_id = $usr->id;

            $deals = Deal::leftJoin('leads', 'deals.id', '=', 'leads.is_converted')
                ->leftJoin('pipelines', 'deals.pipeline_id', '=', 'pipelines.id')
                ->leftJoin('stages', 'leads.deal_stage_id', '=', 'stages.id')
                ->leftJoin('clients', 'leads.client_id', '=', 'clients.id')
                ->leftJoin('user_leads', 'leads.id', '=', 'user_leads.lead_id')
                ->leftJoin('users', 'user_leads.user_id', '=', 'users.id')
                ->with([
                    // 'usersLeads.user:id,name,avatar', // Adjust relationship names as needed
                    'pipeline:id,name',
                    'stage:id,name',
                ])
                ->select('deals.*', 'stages.name as stage_name', 'pipelines.name as pipeline_name','users.name as u_name','users.avatar','clients.phone as client_phone','clients.name as client_name')
                ->where('user_leads.user_id', $user_id)
                ->where('deals.pipeline_id', 10)
                ->get();

                // dd($deals);
            return view('deals.list', compact('deals','pipeline'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Show the form for creating a new redeal.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(\Auth::user()->can('create deal'))
        {
            $clients      = User::where('created_by', '=', \Auth::user()->ownerId())->where('type', 'client')->get()->pluck('name', 'id');
            $customFields = CustomField::where('module', '=', 'deal')->get();

            return view('deals.create', compact('clients', 'customFields'));
        }
        else
        {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    /**
     * Store a newly created redeal in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $usr = \Auth::user();
        if($usr->can('create deal'))
        {
            $countDeal = Deal::where('created_by', '=', $usr->ownerId())->count();
            $validator = \Validator::make(
                $request->all(), [
                                   'name' => 'required',
                               ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            // Default Field Value
            if($usr->default_pipeline)
            {
                $pipeline = Pipeline::where('created_by', '=', $usr->ownerId())->where('id', '=', $usr->default_pipeline)->first();
                if(!$pipeline)
                {
                    $pipeline = Pipeline::where('created_by', '=', $usr->ownerId())->first();
                }
            }
            else
            {
                $pipeline = Pipeline::where('created_by', '=', $usr->ownerId())->first();
            }

            $stage = Stage::where('pipeline_id', '=', $pipeline->id)->first();
            // End Default Field Value

            // Check if stage are available or not in pipeline.
            if(empty($stage))
            {
                return redirect()->back()->with('error', __('Please Create Stage for This Pipeline.'));
            }
            else
            {
                $deal        = new Deal();
                $deal->name  = $request->name;
                $deal->phone = $request->phone;
                if(empty($request->price))
                {
                    $deal->price = 0;
                }
                else
                {
                    $deal->price = $request->price;
                }
                $deal->pipeline_id = $pipeline->id;
                $deal->stage_id    = $stage->id;
                $deal->status      = 'Active';
                $deal->created_by  = $usr->ownerId();
                $deal->save();

                //send email
                $clients = User::whereIN('id', array_filter($request->clients))->get()->pluck('email', 'id')->toArray();
                $dealArr = [
                    'deal_id' => $deal->id,
                    'name' => $deal->name,
                    'updated_by' => $usr->id,
                ];
                $dArr = [
                    'deal_name' => $deal->name,
                    'deal_pipeline' => $pipeline->name,
                    'deal_stage' => $stage->name,
                    'deal_status' => $deal->status,
                    'deal_price' => $usr->priceFormat($deal->price),
                ];

                foreach(array_keys($clients) as $client)
                {
                    ClientDeal::create(
                        [
                            'deal_id' => $deal->id,
                            'client_id' => $client,
                        ]
                    );
                }

                if($usr->type=='company'){
                    $usrDeals = [
                        $usr->id,

                    ];
                }else{
                    $usrDeals = [
                        $usr->id,
                        $usr->ownerId()
                    ];
                }

                foreach($usrDeals as $usrDeal)
                {
                    UserDeal::create(
                        [
                            'user_id' => $usrDeal,
                            'deal_id' => $deal->id,
                        ]
                    );
                }



                CustomField::saveData($deal, $request->customField);

                // Send Email
                $setings = Utility::settings();

                if($setings['deal_assigned'] == 1)
                {
                    $clients = User::whereIN('id', array_filter($request->clients))->get()->pluck('email', 'id')->toArray();
                    $dealAssignArr = [
                        'deal_name' => $deal->name,
                        'deal_pipeline' => $pipeline->name,
                        'deal_stage' => $stage->name,
                        'deal_status' => $deal->status,
                        'deal_price' => $usr->priceFormat($deal->price),
                    ];
                    $resp = Utility::sendEmailTemplate('deal_assigned',  $clients, $dealAssignArr);
//                    return redirect()->back()->with('success', __('Deal successfully created!')  .(($resp['is_success'] == false && !empty($resp['error'])) ? '<br> <span class="text-danger">' . $resp['error'] . '</span>' : ''));

                }

                //For Notification
                $setting  = Utility::settings(\Auth::user()->creatorId());
                $dealNotificationArr = [
                    'user_name' => \Auth::user()->name,
                    'deal_name' => $deal->name,
                ];
                //Slack Notification
                if(isset($setting['deal_notification']) && $setting['deal_notification'] ==1)
                {
                    Utility::send_slack_msg('new_deal', $dealNotificationArr);
                }
                //Telegram Notification
                if(isset($setting['telegram_deal_notification']) && $setting['telegram_deal_notification'] ==1)
                {
                    Utility::send_telegram_msg('new_deal', $dealNotificationArr);
                }

                //webhook
                $module ='New Deal';
                $webhook=  Utility::webhookSetting($module);
                if($webhook)
                {
                    $parameter = json_encode($deal);
                    $status = Utility::WebhookCall($webhook['url'],$parameter,$webhook['method']);
                    if($status == true)
                    {
                        return redirect()->back()->with('success', __('Deal successfully created!')  .((!empty ($resp) && $resp['is_success'] == false && !empty($resp['error'])) ? '<br> <span class="text-danger">' . $resp['error'] . '</span>' : ''));
                    }
                    else
                    {
                        return redirect()->back()->with('error', __('Webhook call failed.'));
                    }
                }
                return redirect()->back()->with('success', __('Deal successfully created!')  .((!empty ($resp) &&  $resp['is_success'] == false && !empty($resp['error'])) ? '<br> <span class="text-danger">' . $resp['error'] . '</span>' : ''));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Display the specified redeal.
     *
     * @param \App\Deal $deal
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Deal $deal)
    {
        if($deal->is_active && \Auth::user()->can('view deal'))
        {
            // $calenderTasks = [];
            // if(\Auth::user()->can('view task'))
            // {
            //     foreach($deal->tasks as $task)
            //     {
            //         $calenderTasks[] = [
            //             'title' => $task->name,
            //             'start' => $task->date,
            //             'url' => route(
            //                 'deals.tasks.show', [
            //                                       $deal->id,
            //                                       $task->id,
            //                                   ]
            //             ),
            //             'className' => ($task->status) ? 'bg-success border-success' : 'bg-warning border-warning',
            //         ];
            //     }

            // }
            // $permission        = [];
            // $customFields      = CustomField::where('module', '=', 'deal')->get();
            // $deal->customField = CustomField::getData($deal, 'deal')->toArray();

                return view('deals.show', compact('deal'));



        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Show the form for editing the specified redeal.
     *
     * @param \App\Deal $deal
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Deal $deal)
    {
        if(\Auth::user()->can('edit deal'))
        {
            if($deal->created_by == \Auth::user()->ownerId())
            {
                $pipelines         = Pipeline::where('created_by', '=', \Auth::user()->ownerId())->get()->pluck('name', 'id');
                $sources           = Source::where('created_by', '=', \Auth::user()->ownerId())->get()->pluck('name', 'id');
                $products          = ProductService::where('created_by', '=', \Auth::user()->ownerId())->get()->pluck('name', 'id');
                $deal->customField = CustomField::getData($deal, 'deal');
                $customFields      = CustomField::where('module', '=', 'deal')->get();

                $deal->sources  = explode(',', $deal->sources);
                $deal->products = explode(',', $deal->products);

                return view('deals.edit', compact('deal', 'pipelines', 'sources', 'products', 'customFields'));
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
     * Update the specified redeal in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Deal $deal
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Deal $deal)
    {
        if(\Auth::user()->can('edit deal'))
        {
            if($deal->created_by == \Auth::user()->ownerId())
            {
                $validator = \Validator::make(
                    $request->all(), [
                                       'name' => 'required|max:50',
                                       'pipeline_id' => 'required',
                                   ]
                );

                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }

                $deal->name  = $request->name;
                $deal->phone = $request->phone;
                if(empty($request->price))
                {
                    $deal->price = 0;
                }
                else
                {
                    $deal->price = $request->price;
                }
                $deal->pipeline_id = $request->pipeline_id;
                $deal->stage_id    = $request->stage_id;
                $deal->sources     = implode(",", array_filter($request->sources));
                $deal->products    = implode(",", array_filter($request->products));
                $deal->notes       = $request->notes;
                $deal->save();

                CustomField::saveData($deal, $request->customField);

                return redirect()->back()->with('success', __('Deal successfully updated!'));
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

    /**
     * Remove the specified redeal from storage.
     *
     * @param \App\Deal $deal
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Deal $deal)
    {
        if(\Auth::user()->can('delete deal'))
        {
            if($deal->created_by == \Auth::user()->ownerId())
            {
                DealDiscussion::where('deal_id', '=', $deal->id)->delete();
                DealFile::where('deal_id', '=', $deal->id)->delete();
                ClientDeal::where('deal_id', '=', $deal->id)->delete();
                UserDeal::where('deal_id', '=', $deal->id)->delete();
                DealTask::where('deal_id', '=', $deal->id)->delete();
                ActivityLog::where('deal_id', '=', $deal->id)->delete();
//                ClientPermission::where('deal_id', '=', $deal->id)->delete();

                $deal->delete();

                return redirect()->route('deals.index')->with('success', __('Deal successfully deleted!'));
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

    public function order(Request $request)
    {

        $post       = $request->all();
        $usr = \Auth::user();


        if($usr->can('move deal'))
        {
            $deal   = Lead::where('is_converted',$post['deal_id'])->first();
            if ($deal->deal_stage_id != 6) {
                $newStage = Stage::find($post['stage_id']);
                    $activity = ActivityLog::create(
                        [
                            'user_id' => $usr->id,
                            'deal_id' => $post['deal_id'],
                            'log_type' => 'Move',
                            'remark' => json_encode(
                                [
                                    'title' => $deal->name,
                                    'old_status' => $deal->dealstage->name,
                                    'new_status' => $newStage->name,
                                ]
                            ),
                        ]
                    );
                    if ($newStage->id == 6) {
                        $deal->collection_date =date("Y-m-d");
                    }
                    $deal->deal_stage_id = $post['stage_id'];
                    $deal->save();
                    $usersIds = UserLead::where('lead_id',$deal->id)->get();
                    foreach ($usersIds as $usersId) {
                        UserNotifications::create([
                            'activity_id'=>$activity->id,
                            'user_id'=>$usersId->user_id,
                        ]);

                    }
            }else {
                return response()->json(['error' => __('Permission Denied.')], 401);
            }
        }
        else
        {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    public function labels($id)
    {
        if(\Auth::user()->can('edit deal'))
        {
            $deal = Deal::find($id);
            if($deal->created_by == \Auth::user()->ownerId())
            {
                $labels   = Label::where('pipeline_id', '=', $deal->pipeline_id)->where('created_by', \Auth::user()->creatorId())->get();
                $selected = $deal->labels();
                if($selected)
                {
                    $selected = $selected->pluck('name', 'id')->toArray();
                }
                else
                {
                    $selected = [];
                }

                return view('deals.labels', compact('deal', 'labels', 'selected'));
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
        if(\Auth::user()->can('edit deal'))
        {
            $deal = Deal::find($id);
            if($deal->created_by == \Auth::user()->ownerId())
            {
                if($request->labels)
                {
                    $deal->labels = implode(',', $request->labels);
                }
                else
                {
                    $deal->labels = $request->labels;
                }
                $deal->save();

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
        if(\Auth::user()->can('edit deal'))
        {
            $deal = Lead::where('is_converted',$id)->first();
            if($deal->created_by == \Auth::user()->ownerId())
            {
                $users = User::where('created_by', '=', \Auth::user()->ownerId())->where('type','!=','client')->where('type', '!=', 'company')->whereNOTIn(
                    'id', function ($q) use ($deal){
                    $q->select('user_id')->from('user_leads')->where('deal_id', '=', $deal->id);
                }
                )->get();

                foreach($users as $key => $user)
                {
                    if(!$user->can('manage deal'))
                    {
                        $users->forget($key);
                    }
                }
                $users = $users->pluck('name', 'id');

                $users->prepend(__('Select Users'), '');

                return view('deals.users', compact('deal', 'users'));
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
        $usr = \Auth::user();
        if($usr->can('edit deal'))
        {
            $usr  = \Auth::user();
            $lead = Lead::find($id);
            // dd($lead->id);
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
        if(\Auth::user()->can('edit deal'))
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

    public function clientEdit($id)
    {
        if(\Auth::user()->can('edit deal'))
        {
            $deal = Deal::find($id);
            if($deal->created_by == \Auth::user()->ownerId())
            {
                $clients = User::where('created_by', '=', \Auth::user()->ownerId())->where('type', 'client')->whereNOTIn(
                    'id', function ($q) use ($deal){
                    $q->select('client_id')->from('client_deals')->where('deal_id', '=', $deal->id);
                }
                )->get()->pluck('name', 'id');

                return view('deals.clients', compact('deal', 'clients'));
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

    public function clientUpdate($id, Request $request)
    {
        if(\Auth::user()->can('edit deal'))
        {
            $deal = Deal::find($id);
            if($deal->created_by == \Auth::user()->ownerId())
            {
                if(!empty($request->clients))
                {
                    $clients = array_filter($request->clients);
                    foreach($clients as $client)
                    {
                        ClientDeal::create(
                            [
                                'deal_id' => $deal->id,
                                'client_id' => $client,
                            ]
                        );
                    }
                }

                if(!empty($clients) && !empty($request->clients))
                {
                    return redirect()->back()->with('success', __('Clients successfully updated!'))->with('status', 'clients');
                }
                else
                {
                    return redirect()->back()->with('error', __('Please Select Valid Clients!'))->with('status', 'clients');
                }
            }
            else
            {
                return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'clients');
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'clients');
        }
    }

    public function clientDestroy($id, $client_id)
    {
        if(\Auth::user()->can('edit deal'))
        {
            $deal = Deal::find($id);
            if($deal->created_by == \Auth::user()->ownerId())
            {
                ClientDeal::where('deal_id', '=', $deal->id)->where('client_id', '=', $client_id)->delete();

                return redirect()->back()->with('success', __('Client successfully deleted!'))->with('status', 'clients');
            }
            else
            {
                return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'clients');
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'clients');
        }
    }

    public function productEdit($id)
    {
        if(\Auth::user()->can('edit deal'))
        {
            $deal = Deal::find($id);
            if($deal->created_by == \Auth::user()->ownerId())
            {
                $products = ProductService::where('created_by', '=', \Auth::user()->ownerId())->whereNOTIn('id', explode(',', $deal->products))->get()->pluck('name', 'id');

                return view('deals.products', compact('deal', 'products'));
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
        $usr = \Auth::user();
        if($usr->can('edit deal'))
        {
            $deal       = Deal::find($id);
            $clients    = ClientDeal::select('client_id')->where('deal_id', '=', $id)->get()->pluck('client_id')->toArray();
            $deal_users = $deal->users->pluck('id')->toArray();

            if($deal->created_by == $usr->ownerId())
            {
                if(!empty($request->products))
                {
                    $products       = array_filter($request->products);
                    $old_products   = explode(',', $deal->products);
                    $deal->products = implode(',', array_merge($old_products, $products));
                    $deal->save();

                    $objProduct = ProductService::whereIN('id', $products)->get()->pluck('name', 'id')->toArray();
                    ActivityLog::create(
                        [
                            'user_id' => $usr->id,
                            'deal_id' => $deal->id,
                            'log_type' => 'Add Product',
                            'remark' => json_encode(['title' => implode(",", $objProduct)]),
                        ]
                    );

                    $productArr = [
                        'deal_id' => $deal->id,
                        'name' => $deal->name,
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
        if(\Auth::user()->can('edit deal'))
        {
            $deal = Deal::find($id);
            if($deal->created_by == \Auth::user()->ownerId())
            {
                $products = explode(',', $deal->products);
                foreach($products as $key => $product)
                {
                    if($product_id == $product)
                    {
                        unset($products[$key]);
                    }
                }
                $deal->products = implode(',', $products);
                $deal->save();

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

    public function fileUpload($id, Request $request)
    {
        if(\Auth::user()->can('edit deal'))
        {
            $deal = Deal::find($id);
            if($deal->created_by == \Auth::user()->ownerId())
            {
                $request->validate(['file' => 'required']);
                $file_name = $request->file->getClientOriginalName();
                $file_path = $request->deal_id . "_" . md5(time()) . "_" . $request->file->getClientOriginalName();
                $request->file->storeAs('deal_files', $file_path);

                $file                 = DealFile::create(
                    [
                        'deal_id' => $request->deal_id,
                        'file_name' => $file_name,
                        'file_path' => $file_path,
                    ]
                );
                $return               = [];
                $return['is_success'] = true;
                $return['download']   = route(
                    'deals.file.download', [
                                             $deal->id,
                                             $file->id,
                                         ]
                );
                $return['delete']     = route(
                    'deals.file.delete', [
                                           $deal->id,
                                           $file->id,
                                       ]
                );

                ActivityLog::create(
                    [
                        'user_id' => \Auth::user()->id,
                        'deal_id' => $deal->id,
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
        if(\Auth::user()->can('edit deal'))
        {
            $deal = Deal::find($id);
            if($deal->created_by == \Auth::user()->ownerId())
            {
                $file = DealFile::find($file_id);
                if($file)
                {
                    $file_path = storage_path('deal_files/' . $file->file_path);
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
        if(\Auth::user()->can('edit deal'))
        {
            $deal = Deal::find($id);
            if($deal->created_by == \Auth::user()->ownerId())
            {
                $file = DealFile::find($file_id);
                if($file)
                {
                    $path = storage_path('deal_files/' . $file->file_path);
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
        if(\Auth::user()->can('edit deal'))
        {
            $deal = Deal::find($id);
            if($deal->created_by == \Auth::user()->ownerId())
            {
                $deal->notes = $request->notes;
                $deal->save();

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

    public function taskCreate($id)
    {
        if(\Auth::user()->can('create task'))
        {
            $deal = Deal::find($id);
            if($deal->created_by == \Auth::user()->ownerId())
            {
                $priorities = DealTask::$priorities;
                $status     = DealTask::$status;

                return view('deals.tasks', compact('deal', 'priorities', 'status'));
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

    public function taskStore($id, Request $request)
    {
        $usr = \Auth::user();
        if($usr->can('create task'))
        {
            $deal       = Deal::find($id);
            $clients    = ClientDeal::select('client_id')->where('deal_id', '=', $id)->get()->pluck('client_id')->toArray();
            $deal_users = $deal->users->pluck('id')->toArray();
            $usrs       = User::whereIN('id', array_merge($deal_users, $clients))->get()->pluck('email', 'id')->toArray();

            if($deal->created_by == $usr->ownerId())
            {
                $validator = \Validator::make(
                    $request->all(), [
                                       'name' => 'required',
                                       'date' => 'required',
                                       'time' => 'required',
                                       'priority' => 'required',
                                       'status' => 'required',
                                   ]
                );
                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }

                $dealTask = DealTask::create(
                    [
                        'deal_id' => $deal->id,
                        'name' => $request->name,
                        'date' => $request->date,
                        'time' => date('H:i:s', strtotime($request->date . ' ' . $request->time)),
                        'priority' => $request->priority,
                        'status' => $request->status,
                    ]
                );

                ActivityLog::create(
                    [
                        'user_id' => $usr->id,
                        'deal_id' => $deal->id,
                        'log_type' => 'Create Task',
                        'remark' => json_encode(['title' => $dealTask->name]),
                    ]
                );

                $taskArr = [
                    'deal_id' => $deal->id,
                    'name' => $deal->name,
                    'updated_by' => $usr->id,
                ];

                $tArr = [
                    'deal_name' => $deal->name,
                    'deal_pipeline' => $deal->pipeline->name,
                    'deal_stage' => $deal->stage->name,
                    'deal_status' => $deal->status,
                    'deal_price' => $usr->priceFormat($deal->price),
                    'task_name' => $dealTask->name,
                    'task_priority' => DealTask::$priorities[$dealTask->priority],
                    'task_status' => DealTask::$status[$dealTask->status],
                ];

                // Send Email
                Utility::sendEmailTemplate('Create Task', $usrs, $tArr);

                return redirect()->back()->with('success', __('Task successfully created!'))->with('status', 'tasks');
            }
            else
            {
                return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'tasks');
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'tasks');
        }
    }

    public function taskShow($id, $task_id)
    {
        if(\Auth::user()->can('view task'))
        {
            $deal = Deal::find($id);
            if($deal->created_by == \Auth::user()->ownerId())
            {
                $task = DealTask::find($task_id);

                return view('deals.tasksShow', compact('task', 'deal'));
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

    public function taskEdit($id, $task_id)
    {
        if(\Auth::user()->can('edit task'))
        {
            $deal = Deal::find($id);
            if($deal->created_by == \Auth::user()->ownerId())
            {
                $priorities = DealTask::$priorities;
                $status     = DealTask::$status;
                $task       = DealTask::find($task_id);

                return view('deals.tasks', compact('task', 'deal', 'priorities', 'status'));
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

    public function taskUpdate($id, $task_id, Request $request)
    {
        if(\Auth::user()->can('edit task'))
        {
            $deal = Deal::find($id);
            if($deal->created_by == \Auth::user()->ownerId())
            {

                $validator = \Validator::make(
                    $request->all(), [
                                       'name' => 'required',
                                       'date' => 'required',
                                       'time' => 'required',
                                       'priority' => 'required',
                                       'status' => 'required',
                                   ]
                );

                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }

                $task = DealTask::find($task_id);

                $task->update(
                    [
                        'name' => $request->name,
                        'date' => $request->date,
                        'time' => date('H:i:s', strtotime($request->date . ' ' . $request->time)),
                        'priority' => $request->priority,
                        'status' => $request->status,
                    ]
                );

                return redirect()->back()->with('success', __('Task successfully updated!'))->with('status', 'tasks');
            }
            else
            {
                return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'tasks');
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'tasks');
        }
    }

    public function taskUpdateStatus($id, $task_id, Request $request)
    {
        if(\Auth::user()->can('edit task'))
        {
            $deal = Deal::find($id);
            if($deal->created_by == \Auth::user()->ownerId())
            {

                $validator = \Validator::make(
                    $request->all(), [
                                       'status' => 'required',
                                   ]
                );

                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return response()->json(
                        [
                            'is_success' => false,
                            'error' => $messages->first(),
                        ], 401
                    );
                }

                $task = DealTask::find($task_id);
                if($request->status)
                {
                    $task->status = 0;
                }
                else
                {
                    $task->status = 1;
                }
                $task->save();

                return response()->json(
                    [
                        'is_success' => true,
                        'success' => __('Task successfully updated!'),
                        'status' => $task->status,
                        'status_label' => __(DealTask::$status[$task->status]),
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

    public function taskDestroy($id, $task_id)
    {
        if(\Auth::user()->can('delete task'))
        {
            $deal = Deal::find($id);
            if($deal->created_by == \Auth::user()->ownerId())
            {
                $task = DealTask::find($task_id);
                $task->delete();

                return redirect()->back()->with('success', __('Task successfully deleted!'))->with('status', 'tasks');
            }
            else
            {
                return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'tasks');
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'tasks');
        }
    }

    public function sourceEdit($id)
    {
        if(\Auth::user()->can('edit deal'))
        {
            $deal = Deal::find($id);
            if($deal->created_by == \Auth::user()->ownerId())
            {
                $sources  = Source::where('created_by', '=', \Auth::user()->ownerId())->get();
                $selected = $deal->sources();

                if($selected)
                {
                    $selected = $selected->pluck('name', 'id')->toArray();
                }

                return view('deals.sources', compact('deal', 'sources', 'selected'));
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
        $usr = \Auth::user();

        if($usr->can('edit deal'))
        {
            $deal       = Deal::find($id);
            $clients    = ClientDeal::select('client_id')->where('deal_id', '=', $id)->get()->pluck('client_id')->toArray();
            $deal_users = $deal->users->pluck('id')->toArray();

            if($deal->created_by == $usr->ownerId())
            {
                if(!empty($request->sources) && count($request->sources) > 0)
                {
                    $deal->sources = implode(',', $request->sources);
                }
                else
                {
                    $deal->sources = "";
                }

                $deal->save();
                ActivityLog::create(
                    [
                        'user_id' => $usr->id,
                        'deal_id' => $deal->id,
                        'log_type' => 'Update Sources',
                        'remark' => json_encode(['title' => 'Update Sources']),
                    ]
                );

                $dealArr = [
                    'deal_id' => $deal->id,
                    'name' => $deal->name,
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
        if(\Auth::user()->can('edit deal'))
        {
            $deal = Deal::find($id);
            if($deal->created_by == \Auth::user()->ownerId())
            {
                $sources = explode(',', $deal->sources);
                foreach($sources as $key => $source)
                {
                    if($source_id == $source)
                    {
                        unset($sources[$key]);
                    }
                }
                $deal->sources = implode(',', $sources);
                $deal->save();

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

    public function permission($id, $clientId)
    {
        if(\Auth::user()->can('edit deal'))
        {
            $deal     = Deal::find($id);
            $client   = User::find($clientId);
            $selected = $client->clientPermission($deal->id);
            if($selected)
            {
                $selected = explode(',', $selected->permissions);
            }
            else
            {
                $selected = [];
            }
            $permissions = Deal::$permissions;

            return view('deals.permissions', compact('deal', 'client', 'selected', 'permissions'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'clients');
        }
    }

    public function permissionStore($id, $clientId, Request $request)
    {
        if(\Auth::user()->can('edit deal'))
        {
            $deal = Deal::find($id);
            if($deal->created_by == \Auth::user()->ownerId())
            {
                $client      = User::find($clientId);
                $permissions = $client->clientPermission($deal->id);
                if($permissions)
                {
                    if(!empty($request->permissions) && count($request->permissions) > 0)
                    {
                        $permissions->permissions = implode(',', $request->permissions);
                    }
                    else
                    {
                        $permissions->permissions = "";
                    }
                    $permissions->save();

                    return redirect()->back()->with('success', __('Permissions successfully updated!'))->with('status', 'clients');
                }
                elseif(!empty($request->permissions) && count($request->permissions) > 0)
                {
                    ClientPermission::create(
                        [
                            'client_id' => $clientId,
                            'deal_id' => $deal->id,
                            'permissions' => implode(',', $request->permissions),
                        ]
                    );

                    return redirect()->back()->with('success', __('Permissions successfully updated!'))->with('status', 'clients');
                }
                else
                {
                    return redirect()->back()->with('error', __('Invalid Permission.'))->with('status', 'clients');
                }
            }
            else
            {
                return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'clients');
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'clients');
        }
    }

    public function jsonUser(Request $request)
    {
        $users = [];
        if(!empty($request->deal_id))
        {
            $deal  = Deal::find($request->deal_id);
            $users = $deal->users->pluck('name', 'id');
        }

        return response()->json($users, 200);
    }

    public function changePipeline(Request $request)
    {
        $user                   = \Auth::user();
        $user->default_pipeline = $request->default_pipeline_id;
        $user->save();

        return redirect()->back();
    }

    public function discussionCreate($id)
    {
        $deal = Deal::find($id);
        if($deal->created_by == \Auth::user()->ownerId())
        {
            return view('deals.discussions', compact('deal'));
        }
        else
        {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    public function discussionStore($id, Request $request)
    {
        $usr        = \Auth::user();
        $deal       = Deal::find($id);
        $clients    = ClientDeal::select('client_id')->where('deal_id', '=', $id)->get()->pluck('client_id')->toArray();
        $deal_users = $deal->users->pluck('id')->toArray();

        if($deal->created_by == \Auth::user()->ownerId())
        {
            $discussion             = new DealDiscussion();
            $discussion->comment    = $request->comment;
            $discussion->deal_id    = $deal->id;
            $discussion->created_by = \Auth::user()->id;
            $discussion->save();

            $dealArr = [
                'deal_id' => $deal->id,
                'name' => $deal->name,
                'updated_by' => $usr->id,
            ];

            return redirect()->back()->with('success', __('Message successfully added!'))->with('status', 'discussion');
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'discussion');
        }
    }

    public function changeStatus(Request $request, $id)
    {
        $deal         = Deal::where('id', '=', $id)->first();
        $deal->status = $request->deal_status;
        $deal->save();

        return redirect()->back();
    }

    // Deal Calls
    public function callCreate($id)
    {
        if(\Auth::user()->can('create deal call'))
        {
            $deal = Deal::find($id);
            if($deal->created_by == \Auth::user()->ownerId())
            {
                $users = UserDeal::where('deal_id', '=', $deal->id)->get();

                return view('deals.calls', compact('deal', 'users'));
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
        $usr = \Auth::user();

        if($usr->can('create deal call'))
        {
            $deal = Deal::find($id);
            if($deal->created_by == $usr->ownerId())
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

                DealCall::create(
                    [
                        'deal_id' => $deal->id,
                        'subject' => $request->subject,
                        'call_type' => $request->call_type,
                        'duration' => $request->duration,
                        'user_id' => $request->user_id,
                        'description' => $request->description,
                        'call_result' => $request->call_result,
                    ]
                );

                ActivityLog::create(
                    [
                        'user_id' => $usr->id,
                        'deal_id' => $deal->id,
                        'log_type' => 'Create Deal Call',
                        'remark' => json_encode(['title' => 'Create new Deal Call']),
                    ]
                );

                $dealArr = [
                    'deal_id' => $deal->id,
                    'name' => $deal->name,
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
        if(\Auth::user()->can('edit deal call'))
        {
            $deal = Deal::find($id);
            if($deal->created_by == \Auth::user()->ownerId())
            {
                $call  = DealCall::find($call_id);
                $users = UserDeal::where('deal_id', '=', $deal->id)->get();

                return view('deals.calls', compact('call', 'deal', 'users'));
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
        if(\Auth::user()->can('edit deal call'))
        {
            $deal = Deal::find($id);
            if($deal->created_by == \Auth::user()->ownerId())
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

                $call = DealCall::find($call_id);

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
        if(\Auth::user()->can('delete deal call'))
        {
            $deal = Deal::find($id);
            if($deal->created_by == \Auth::user()->ownerId())
            {
                $task = DealCall::find($call_id);
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

    // Deal email
    public function emailCreate($id)
    {
        if(\Auth::user()->can('create deal email'))
        {
            $deal = Deal::find($id);
            if($deal->created_by == \Auth::user()->ownerId())
            {
                return view('deals.emails', compact('deal'));
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
        if(\Auth::user()->can('create deal email'))
        {
            $deal = Deal::find($id);

            if($deal->created_by == \Auth::user()->ownerId())
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

             DealEmail::create(
                    [
                        'deal_id' => $deal->id,
                        'to' => $request->to,
                        'subject' => $request->subject,
                        'description' => $request->description,
                    ]
                );

                $dealEmail =
                    [
                        'deal_name' => $deal->name,
                        'to' => $request->to,
                        'subject' => $request->subject,
                        'description' => $request->description,
                    ];

//                dd($deal->name);


                try
                {
                    Mail::to($request->to)->send(new SendDealEmail($dealEmail, $settings));
                }
                catch(\Exception $e)
                {
//                    dd($e);
                    $smtp_error = __('E-Mail has been not sent due to SMTP configuration');
                }


                ActivityLog::create(
                    [
                        'user_id' => \Auth::user()->id,
                        'deal_id' => $deal->id,
                        'log_type' => 'Create Deal Email',
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

    // Get Pipeline Data{}
    public function getPipeline($id){
        $usr      = \Auth::user();
        $user = User::find($usr->id);
        $user->default_pipeline = $id;
        $user->save();
        $pipeline = Pipeline::where('created_by', '=', $usr->ownerId())->where('id', $id)->first();
        return view('deals.deal', compact('pipeline'));
    }
    public function getTotal() {
        $lead_id       = Lead::where('deal_stage_id',6)->join('user_leads','leads.id','=','user_leads.lead_id')->where('user_leads.user_id','=',\Auth::user()->id)->where('pipeline_id',\Auth::user()->default_pipeline)->get()->pluck('is_converted');
        $collection       = ServiceCommission::whereIn('deal_id',$lead_id)->sum('without_vat_commission');
        $collection_month = ServiceCommission::whereIn('deal_id', $id)
                            ->whereMonth('updated_at', '=', now()->month)
                            ->sum('without_vat_commission');
        // $collection_month  = ServiceCommission::whereIn('deal_id',$lead_id)->whereMonth('updated_at', '=', date('d'))->sum('without_vat_commission');

        $lead_id_reject       = Lead::where('deal_stage_id',4)->join('user_leads','leads.id','=','user_leads.lead_id')->where('user_leads.user_id','=',\Auth::user()->id)->where('pipeline_id',\Auth::user()->default_pipeline)->get()->pluck('is_converted');
        $reject       = ServiceCommission::whereIn('deal_id',$lead_id_reject)->sum('without_vat_commission');
        $reject_month = ServiceCommission::whereIn('deal_id', $id)
                            ->whereMonth('updated_at', '=', now()->month)
                            ->sum('without_vat_commission');
        // $reject_month  = ServiceCommission::whereIn('deal_id',$lead_id_reject)->whereMonth('updated_at', '=', date('d'))->sum('without_vat_commission');

        $deal       = Lead::join('user_leads','leads.id','=','user_leads.lead_id')->where('user_leads.user_id','=',\Auth::user()->id)->where('pipeline_id',\Auth::user()->default_pipeline)->get()->pluck('is_converted');
        $id  = Deal::whereIn('id',$deal)->where('contract_stage','cm_signed')->where('pipeline_id',\Auth::user()->default_pipeline)->get()->pluck('id');
        $curr_month = ServiceCommission::whereIn('deal_id', $id)
                    ->whereMonth('updated_at', '=', now()->month)
                    ->sum('without_vat_commission');
        // $curr_month  = ServiceCommission::whereIn('deal_id',$id)->whereMonth('updated_at', '=', date('d'))->sum('without_vat_commission');
        $deals       = ServiceCommission::whereIn('deal_id',$id)->sum('without_vat_commission');
        $cnt_deal                = [];
        $cnt_deal['total']       = number_format($deals - $reject);
        $cnt_deal['curr_month'] = number_format($curr_month - $reject_month);
        $cnt_deal['collection']  = number_format($collection);
        $cnt_deal['collection_month']   = number_format($collection_month);
        $cnt_deal['reject']  = number_format($reject);
        $cnt_deal['reject_month']   = number_format($reject_month);

        return $cnt_deal;

    }
    function getDealByUsers(Request $request){
        $usr = \Auth::user();
        if($usr->can('manage deal'))
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

                $a= UserDeal::where('user_id',$request->user)->get()->pluck('lead_id');
                $b = Deal::where('id',$request->user)->get()->pluck('id');
                $users  = $a->merge($b)->unique();

                $pipe = $request->pipeline;
                $startDate = $request->startDate;
                $endDate = $request->endDate;
                $searchDeals = Deal::when(!empty($users), function ($query) use ($users) {
                    $query->whereIn('id', $users);
                })
                ->when(($pipe != "null"), function ($query) use ($pipe) {
                    $query->whereIn('pipeline_id', $pipe);
                })
                ->when(!empty($startDate) && !empty($endDate), function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('date', [$startDate, $endDate]);
                })
                ->get();
                return view('deals.filterSearch', compact('pipelines', 'pipeline','searchDeals'));



        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }
}
