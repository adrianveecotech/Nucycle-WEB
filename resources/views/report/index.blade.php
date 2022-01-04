@extends('layouts.app')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">Reports<small class="ml-3 mr-3"></small></h1>
            </div>

        </div>
    </div>
</div>

<div class="content">
    <div class="clearfix"></div>
    <div class="card">
        <div class="card-header">
            <ul class="nav nav-tabs align-items-end card-header-tabs w-100">
                <li class="nav-item">
                    <a class="nav-link active" href="{!! url()->current() !!}"><i class="fa fa-list mr-2"></i>Report List</a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <li><a href="{{route('report.individual_vs_company')}}">Total number of Individual vs Company Accounts</a></li>
            <li><a href="{{route('report.user_by_state_city')}}">Total number of users by area and states</a></li>
            <li><a href="{{route('report.user_by_membership_tier')}}">Total number of user by Membership Tiers</a></li>
            <li><a href="{{route('report.new_vs_exisiting_recycling_frequency')}}">Weekly & Monthly Recycling Frequency New User vs Existing User</a></li>
            <li><a href="{{route('report.collected_recyling_type')}}">Weekly & Monthly Total Collected Recycling Type in KG</a></li>
            <li><a href="{{route('report.active_user')}}">Total Active Users per month (based on last 30 days login and transaction made)
                </a></li>
            <li><a href="{{route('report.point_redeemed')}}">Total points redeemed in 30 day
                </a></li>
            <li><a href="{{route('report.reward_redeemed')}}">Rewards Redeem by category in this month</a></li>
            <li><a href="{{route('report.individual_vs_company_visited_center')}}">Total number of users visited collection center by Individual vs Company Accounts
                </a></li>
            <li><a href="{{route('report.new_vs_existing_visited_center')}}">Total number of new users vs existing users visited collection center</a></li>
            <li><a href="{{route('report.waste_recyling_type')}}">Monthly / Weekly Total collected waste by recycling type in kg </a></li>
            <li><a href="{{route('report.point_hub_weekly_monthly')}}">Total points transacted per week / month by collection center</a></li>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
    $(document).ready(function() {
        $('#tableMain').DataTable();
    });
</script>
@endsection