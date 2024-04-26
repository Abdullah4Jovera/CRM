<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class LeadStage extends Model
{
    protected $fillable = [
        'name',
        'pipeline_id',
        'created_by',
        'order',
    ];

    public function lead()
    {
        if (\Auth::user()->type == 'company') {
            return Lead::select('leads.*', 'clients.name as client_name')
                ->leftJoin('clients', 'leads.client_id', '=', 'clients.id')
                ->with([
                    'users:id,name,designation,avatar',
                    'product:id,name'
                ])
                ->where('created_by', '=', \Auth::user()->creatorId())
                ->where('stage_id', '=', $this->id)
                ->whereNull('is_reject')
                ->whereNotNull('leads.client_id') // Ensure this condition aligns with your data and business logic
                ->orderBy('order')
                ->orderBy('leads.created_at', 'DESC') // Specify the table for 'created_at'
                ->get();
        } else {
            // You were missing the 'return' statement here
            return Lead::select('leads.*', 'clients.name as client_name')
                ->leftJoin('clients', 'leads.client_id', '=', 'clients.id')
                ->leftJoin('user_leads', 'leads.id', '=', 'user_leads.lead_id')
                ->with([
                    'users:id,name,designation,avatar',
                    'product:id,name'
                ])
                ->where('user_leads.user_id', '=', \Auth::user()->id)
                ->where('stage_id', '=', $this->id)
                ->whereNull('is_reject')
                ->whereNotNull('leads.client_id') // Ensure this condition aligns with your data and business logic
                ->orderBy('order')
                ->orderBy('leads.created_at', 'DESC') // Specify the table for 'created_at'
                ->get();
        }
    }
}
