@extends('layouts.app')
@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col">
            <div class="card">
                <div class="card-header">{{ __('View Waste Clearance Schedule') }}</div>

                <div class="card-body">
                    <div class="form-group row">
                        <label for="time" class="col-md-4 col-form-label text-md-right">{{ __('Collection Time') }}</label>
                        <div class="col-md-6 col-form-label">
                            {{$schedule->collection_time}}
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="hub" class="col-md-4 col-form-label text-md-right">{{ __('Collection Hub') }}</label>
                        <div class="col-md-6 col-form-label">
                            {!!$schedule->collection_hub->hub_name !!}
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="buyer_name" class="col-md-4 col-form-label text-md-right">{{ __('Buyer Name') }}</label>
                        <div class="col-md-6 col-form-label">
                            {!!$schedule->buyer_name !!}
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="buyer_phone" class="col-md-4 col-form-label text-md-right">{{ __('Buyer Phone Number') }}</label>

                        <div class="col-md-6 col-form-label">
                            {{$schedule->buyer_phone_number}}
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="pin_code" class="col-md-4 col-form-label text-md-right">{{ __('Pin Code') }}</label>

                        <div class="col-md-6 col-form-label">
                            {{$schedule->pin_code}}
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="status" class="col-md-4 col-form-label text-md-right">{{ __('Status') }}</label>

                        <div class="col-md-6 col-form-label">
                            @if($schedule->status == 1)
                            Pending
                            @elseif($schedule->status == 2)
                            Completed
                            @elseif($schedule->status == 3)
                            Cancelled
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="item_lists" class="col-md-4 col-form-label text-md-right">{{ __('Item lists') }}</label>

                        <div class="col-md-6 col-form-label">
                            @foreach($items as $item)
                            <p class="col-6" style="width: 100%; ">{{$item->name}} - {{$item->weight}} kg</p>
                            @endforeach
                        </div>
                    </div>


                    <div class="form-group row mb-0">
                        <div class="col-md-6 offset-md-4">
                            <a href="{{ route('waste_clearance.edit', $schedule->id) }}" class="btn btn-xs btn-success pull-right" type="button">{{ __('Edit') }}</a>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script type="text/javascript">
</script>
@endsection