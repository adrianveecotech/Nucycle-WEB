@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col">
            <div class="card">
                <div class="card-header">{{ __('Total Points Redeemed in 30 days') }}</div>
                <div class="card-body">
                    {{$userByMembership[0]->total_point}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

