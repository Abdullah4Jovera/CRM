<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'project_id',
        'task_id',
        'deal_id',
        'log_type',
        'remark',
    ];

    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    public function userdetail()
    {
        return $this->hasOne('App\Models\UserDetail', 'user_id', 'user_id');
    }

    public function notif()
    {
        return $this->hasMany('App\Models\UserNotifications', 'activity_id', 'id');
    }


    public function getRemark()
    {
        $remark = json_decode($this->remark, true);
        if($remark)
        {
            $user_name = $this->user ? $this->user->name : '';
            // dd($user_name);

            if($this->log_type == 'Invite User')
            {
                return $user_name . ' ' . __('has invited') . ' <b>' . $remark['title'] . '</b>';
            }
            elseif($this->log_type == 'User Assigned to the Task')
            {
                return $user_name . ' ' . __('has assigned task ') . ' <b>' . $remark['task_name'] . '</b> ' . __(' to') . ' <b>' . $remark['member_name'] . '</b>';
            }
            elseif($this->log_type == 'User Removed from the Task')
            {
                return $user_name . ' ' . __('has removed ') . ' <b>' . $remark['member_name'] . '</b>' . __(' from task') . ' <b>' . $remark['task_name'] . '</b>';
            }
            elseif($this->log_type == 'Upload File')
            {
                return $user_name . ' ' . __('Upload new file') . ' <b>' . $remark['file_name'] . '</b>';
            }
            elseif($this->log_type == 'Create Bug')
            {
                return $user_name . ' ' . __('Created new bug') . ' <b>' . $remark['title'] . '</b>';
            }
            elseif($this->log_type == 'Create Milestone')
            {
                return $user_name . ' ' . __('Create new milestone') . " <b>" . $remark['title'] . "</b>";
            }
            elseif($this->log_type == 'Create Task')
            {
                return $user_name . ' ' . __('Create new Task') . " <b>" . $remark['title'] . "</b>";
            }
            elseif($this->log_type == 'Move Task')
            {
                return $user_name . ' ' . __('Moved the Task') . " <b>" . $remark['title'] . "</b> " . __('from') . " " . __(ucwords($remark['old_stage'])) . " " . __('to') . " " . __(ucwords($remark['new_stage']));
            }
            elseif($this->log_type == 'Create Expense')
            {
                return $user_name . ' ' . __('Create new Expense') . " <b>" . $remark['title'] . "</b>";
            }
            elseif($this->log_type == 'Create Task')
            {
                return $user_name . ' ' . __('Create new Task') . " <b>" . $remark['title'] . "</b>";
            }
            elseif($this->log_type == 'Add Product')
            {
                return $user_name . ' ' . __('Add new Products') . " <b>" . $remark['title'] . "</b>";
            }
            elseif($this->log_type == 'Update Sources')
            {
                return $user_name . ' ' . __('Update Sources');
            }
            elseif($this->log_type == 'Create Deal Call')
            {
                return $user_name . ' ' . __('Create new Deal Call');
            }
            elseif($this->log_type == 'Create Deal Email')
            {
                return $user_name . ' ' . __('Create new Deal Email');
            }
            elseif($this->log_type == 'Convert')
            {
                return $user_name . ' ' . __('Convert Lead to Service Application ');
            }
            elseif($this->log_type == 'Service Appliaction Update')
            {
                return $user_name . ' ' . __('Service Application Form Update ');
            }
            elseif($this->log_type == 'Service Appliaction Status')
            {
                return $user_name . " " . __('Update Status') . " <b>" . $remark['title'] . "</b> " . __('from') . " " . __(ucwords($remark['old_status'])) . " " . __('to') . " " . __(ucwords($remark['new_status']));
            }
            elseif($this->log_type == 'Convert To Deal')
            {
                return $user_name . " " . __('Converted') . " <b>" . $remark['title'] . "</b> " . __('from') . " " . __(ucwords($remark['old_status'])) . " " . __('to') . " " . __(ucwords($remark['new_status']));
            }
            elseif($this->log_type == 'Move')
            {
                return $user_name . " " . __('Move Deal') . " <b>" . $remark['title'] . "</b> " . __('from') . " " . __(ucwords($remark['old_status'])) . " " . __('to') . " " . __(ucwords($remark['new_status']));
            }
        }
        else
        {
            return $this->remark;
        }
    }

    public function logIcon()
    {
        $type = $this->log_type;
        $icon = '';


        if(!empty($type))
        {
            if($type == 'Invite User')
            {
                $icon = 'user';
            }
            else if($type == 'User Assigned to the Task')
            {
                $icon = 'user-check';
            }
            else if($type == 'User Removed from the Task')
            {
                $icon = 'user-x';
            }
            else if($type == 'Upload File')
            {
                $icon = 'cloud-upload';
            }
            else if($type == 'Create Milestone')
            {
                $icon = 'crop';
            }
            else if($type == 'Create Bug')
            {
                $icon = 'bug';
            }
            else if($type == 'Create Task')
            {
                $icon = 'square-plus';
            }
            else if($type == 'Move Task')
            {
                $icon = 'command';
            }
            else if($type == 'Create Expense')
            {
                $icon = 'clipboard-list';
            }
            else if($type == 'Move')
            {
                $icon = 'arrows-maximize';
            }
            elseif($type == 'Add Product')
            {
                $icon = 'shopping-cart-plus';
            }
            elseif($type == 'Update Sources')
            {
                $icon = 'brand-open-source';
            }
            elseif($type == 'Create Deal Call')
            {
                $icon = 'phone-plus';
            }
            elseif($type == 'Create Deal Email')
            {
                $icon = 'record-mail';
            }
            elseif($type == 'Create Invoice')
            {
                $icon = 'file-plus';
            }
            elseif($type == 'Add Contact')
            {
                $icon = 'notebook';
            }
            elseif($type == 'Create Task')
            {
                $icon = 'list';
            }
        }

        return $icon;
    }
}
