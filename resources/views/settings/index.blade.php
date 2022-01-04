
@extends('layouts.app')
@section('content')

<body>

</body>

@endsection

@section('scripts')

<ul class="footer-list-top">
      <h4>Settings</h4>
    <li><a href="{{route('settings.user_role')}}">User role</a></li>
    <li><a href="{{route('settings.recycle_category')}}">Recycling category</a></li>
    <li><a href="{{route('settings.statistic_indicator')}}">Statistic Indicator</a></li>
    <li><a href="{{route('settings.recycle_type')}}">Recycling type</a></li>
    <li><a href="{{route('settings.merchant')}}">Merchant management</a></li>
    <li><a href="{{route('settings.banner_tag')}}">Tag for advertisement/news/event</a></li>
    <li><a href="{{route('settings.rewards_category')}}">Rewards category</a></li>
  </ul>
@endsection