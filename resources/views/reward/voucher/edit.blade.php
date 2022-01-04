@extends('layouts.app')
@section('content')

<div class="container">
    <div class="row ">
        <div class="col">
            <form method="POST" action="{{ route('voucher.edit_db') }}">
                <!-- <form method="POST"> -->
                @csrf
                <div class="card">
                    <div class="card-header">{{ __('Edit Voucher') }}</div>

                    <div class="card-body">
                        <div id="body">
                            <input type="hidden" name="id" value="{{$voucher->id}}">
                            <input type="hidden" name="reward_id" value="{{$voucher->reward_id}}">

                            <div class="form-group row mt-5">
                                <label for="code" class="col-md-4 col-form-label text-md-right">{{ __('Code') }}</label>

                                <div class="col-md-6">
                                    <input type="text" value="{{$voucher->code}}" class="form-control @error('code') is-invalid @enderror" name="code">

                                    @error('code')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="expiry_date" class="col-md-4 col-form-label text-md-right">{{ __('Expiry Date') }}</label>

                                <div class="col-md-6">
                                    <input id="expiry_date" type="date" value="{{ old('expiry_date',date('Y-m-d',strtotime($voucher['expiry_date']))) }}" class="form-control @error('expiry_date') is-invalid @enderror" name="expiry_date" autofocus>

                                    @error('expiry_date')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="is_redeem" class="col-md-4 col-form-label text-md-right">{{ __('Is redeem?') }}</label>

                                <div class="col-form-label">
                                    @if($voucher->is_redeem == 1)
                                    <input type="checkbox" name="is_redeem" checked>
                                    @elseif($voucher->is_redeem == 0)
                                    <input type="checkbox" name="is_redeem">
                                    @endif

                                </div>

                            </div>
                        </div>
                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button class="btn btn-xs btn-success pull-right" type="submit">{{ __('Submit') }}</button>
                            </div>
                        </div>
                    </div>

                </div>

        </div>
        </form>
    </div>
</div>
</div>

@endsection

@section('scripts')
<script>

</script>
@endsection