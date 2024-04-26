<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stage extends Model
{
    protected $fillable = [
        'name','created_by','order'
    ];

    public function deals(){
        // if(\Auth::user()->type == 'client'){
        //     return Deal::select('deals.*')->join('lead','lead.deal_id','=','deals.id')->where('client_deals.client_id', '=', \Auth::user()->id)->where('deals.stage_id', '=', $this->id)->orderBy('deals.order')->get();
        // }else {
            return Deal::select('deals.*','leads.deal_stage_id')->join('leads', 'leads.is_converted', '=', 'deals.id')->join('user_leads', 'leads.id', '=', 'user_leads.lead_id')->where('leads.deal_stage_id', '=', $this->id)->where('user_leads.user_id', '=', \Auth::user()->id)->where('deals.contract_stage','=','cm_signed')->orderBy('created_at','DESC')->get();
        // }
        // if(\Auth::user()->type=='company'){
        //     return Lead::select('leads.*')->where('leads.created_by', '=', \Auth::user()->creatorId())->where('leads.stage_id', '=', $this->id)->orderBy('leads.deal_stage_id')->orderBy('created_at', 'DESC')->get();
        // }else{
        //     return Lead::select('leads.*')->join('user_leads', 'user_leads.lead_id', '=', 'leads.id')->where('user_leads.user_id', '=', \Auth::user()->id)->where('leads.stage_id', '=', $this->id)->orderBy('leads.deal_stage_id')->get();

        // }
    }
}
