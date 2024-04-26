@php
    $users=\Auth::user();
    //$profile=asset(Storage::url('uploads/avatar/'));
    $profile=\App\Models\Utility::get_file('uploads/avatar/');
    $languages=\App\Models\Utility::languages();
    $lang = isset($users->lang)?$users->lang:'en';
    $setting = \App\Models\Utility::colorset();
    $mode_setting = \App\Models\Utility::mode_layout();


    $unseenCounter=App\Models\ChMessage::where('to_id', Auth::user()->id)->where('seen', 0)->count();
@endphp
<style>
    .loan-calculator {
    font-family: "Roboto", sans-serif;
    width: 750px;
    margin: 0px auto;
    background: #fff;
    box-shadow: 0 12px 50px -11px rgba(0, 0, 0, 0.2);
    border-radius: 8px;
    color: #14213d;
    overflow: hidden;
    }
    .loan-calculator,
    .loan-calculator * {
    box-sizing: border-box;
    }
    .loan-calculator .top {
        background: #14213d;
        color: #fff;
        padding: 32px;
        border-radius: 10px ;
    }
    .loan-calculator .top h2 {
    margin-top: 0;
    color: #ffa000;
    }
    .loan-calculator form {
    display: flex;
    gap: 8px;
    justify-content: space-between;
    }
    .loan-calculator .title {
    margin-bottom: 16px;
    }
    .loan-calculator form input {
    font-size: 20px;
    padding: 8px 24px;
    width: 100%;
    }
    .loan-calculator .result {
    display: flex;
    justify-content: space-between;
    align-items: center;
    }
    .loan-calculator .result .left {
    width: 100%;
    padding: 8px 32px;
    }
    .loan-calculator .left h3 {
    font-size: 16px;
    font-weight: 400;
    margin-bottom: 8px;
    }
    .loan-calculator .result .value {
    font-size: 30px;
    font-weight: 900;
    padding-bottom: 10px;
    border-bottom: 1px solid rgba(20, 33, 61, 0.2);
    }
    .loan-calculator .result .value::after {
    content: " AED";
    font-size: 24px;
    font-weight: 400;
    margin-right: 6px;
    opacity: 0.4;
    }
    .loan-calculator .calculate-btn {
    background: #ffa000;
    color: #fff;
    border: none;
    padding: 8px 32px;
    border-radius: 8px;
    font-size: 18px;
    font-weight: 900;
    cursor: pointer;
    margin: 24px 0;
    }
    .loan-calculator .right {
    width: 50%;
    }
    @media (max-width: 650px) {
    .loan-calculator {
    width: 90%;
    max-width: 500px;
    }
    .loan-calculator form {
    flex-direction: column;
    gap: 20px;
    }
    .loan-calculator .result {
    flex-direction: column;
    text-align: center;
    }
    }
</style>

@if (isset($setting['cust_theme_bg']) && $setting['cust_theme_bg'] == 'on')
    <header class="dash-header transprent-bg">
@else
    <header class="dash-header">
@endif
    <div class="header-wrapper">
        <div class="me-auto dash-mob-drp">
            <ul class="list-unstyled">
                <li class="dash-h-item mob-hamburger">
                    <a href="#!" class="dash-head-link" id="mobile-collapse">
                        <div class="hamburger hamburger--arrowturn">
                            <div class="hamburger-box">
                                <div class="hamburger-inner"></div>
                            </div>
                        </div>
                    </a>
                </li>

                <li class="dropdown dash-h-item drp-company">
                    <a
                        class="dash-head-link dropdown-toggle arrow-none me-0"
                        data-bs-toggle="dropdown"
                        href="#"
                        role="button"
                        aria-haspopup="false"
                        aria-expanded="false"
                    >
                        <span class="theme-avtar">
                             <img src="{{ !empty(\Auth::user()->avatar) ? $profile . \Auth::user()->avatar :  $profile.'avatar.png'}}" class="img-fluid rounded-circle">
                        </span>
                        <span class="hide-mob ms-2">{{__('Hi, ')}}{{\Auth::user()->name }}!</span>
                        <i class="ti ti-chevron-down drp-arrow nocolor hide-mob"></i>
                    </a>
                    <div class="dropdown-menu dash-h-dropdown">

                        <!-- <a href="{{ route('change.mode') }}" class="dropdown-item">
                            <i class="ti ti-circle-plus"></i>
                            <span>{{(Auth::user()->mode == 'light') ? __('Dark Mode') : __('Light Mode')}}</span>
                        </a> -->

                        <a href="{{route('profile')}}" class="dropdown-item">
                            <i class="ti ti-user"></i>
                            <span>{{__('Profile')}}</span>
                        </a>
                        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('frm-logout').submit();" class="dropdown-item">
                            <i class="ti ti-power"></i>
                            <span>{{__('Logout')}}</span>
                        </a>
                        <form id="frm-logout" action="{{ route('logout') }}" method="POST" class="d-none">
                            {{ csrf_field() }}
                        </form>

                    </div>
                </li>

            </ul>
        </div>
        <div class="ms-auto">
            <ul class="list-unstyled">
                @if( \Auth::user()->type !='client' && \Auth::user()->type !='super admin' )
                    <li class="dropdown dash-h-item drp-notification">
                        <a class="dash-head-link arrow-none me-0" href="{{ url('chats') }}" aria-haspopup="false"
                           aria-expanded="false">
                            <i class="ti ti-brand-hipchat"></i>
                            <span class="bg-danger dash-h-badge message-toggle-msg  message-counter custom_messanger_counter beep"> {{ $unseenCounter }}<span
                                    class="sr-only"></span>
                            </span>
                        </a>
                    </li>
                @endif
                    <li class="dropdown dash-h-item drp-notification_data drp-language">
                        <a class="dash-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                            <i class="ti ti-bell"></i>
                            <span class="notifications ">

                            </span>
                        </a>
                        <div class="dropdown-menu dash-h-dropdown notifications_dropdown dropdown-menu-end" style="height:500px;overflow-y: overlay;">

                        </div>
                    </li>
                    <li class="dropdown dash-h-item drp-notification ">
                        <button class="dash-head-link arrow-none me-0" aria-haspopup="false" data-bs-toggle="modal" data-bs-target="#Calcul"aria-expanded="false">
                            <i class="ti ti-calculator"></i>
                        </button>
                    </li>





                <li class="dropdown dash-h-item drp-language">
                    <a
                        class="dash-head-link dropdown-toggle arrow-none me-0"
                        data-bs-toggle="dropdown"
                        href="#"
                        role="button"
                        aria-haspopup="false"
                        aria-expanded="false"
                    >
                        <i class="ti ti-world nocolor"></i>
                        <span class="drp-text hide-mob">{{Str::upper(isset($lang)?$lang:'en')}}</span>
                        <i class="ti ti-chevron-down drp-arrow nocolor"></i>
                    </a>
                    <div class="dropdown-menu dash-h-dropdown dropdown-menu-end">

                        @foreach($languages as $language)
                            <a href="{{route('change.language',$language)}}" class="dropdown-item @if($language == $lang) text-danger @endif">
                                <span>{{Str::upper($language)}}</span>
                            </a>
                        @endforeach
                        <h></h>
                            @if(\Auth::user()->type=='super admin')

                                <a class="dropdown-item text-primary" href="{{route('manage.language',[isset($lang)?$lang:'en'])}}">{{ __('Manage Language ') }}</a>
                            @endif
                    </div>
                </li>
            </ul>
        </div>
    </div>
    </header>

    <!-- Button trigger modal -->


    <!-- Modal -->
