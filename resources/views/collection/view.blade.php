@extends('layouts.app')
@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col">
            <div class="card">
                <div class="card-header">{{ __('View Collection') }}</div>

                <div class="card-body">
                    <div class="form-group row">
                        <label for="question" class="col-md-4 col-form-label text-md-right">{{ __('Customer') }}</label>
                        <div class="col-md-6 col-form-label">
                            {{$collection->customer->name}}
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="answer" class="col-md-4 col-form-label text-md-right">{{ __('Collection Hub') }}</label>
                        <div class="col-md-6 col-form-label">
                            {{$collection->collection_hub->hub_name}}
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="answer" class="col-md-4 col-form-label text-md-right">{{ __('Collector') }}</label>
                        <div class="col-md-6 col-form-label">
                            {{$collection->collector->name}}
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="answer" class="col-md-4 col-form-label text-md-right">{{ __('Item Point') }}</label>
                        <div class="col-md-6 col-form-label">
                            {{$collection->total_point}}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="answer" class="col-md-4 col-form-label text-md-right">{{ __('Bonus Point') }}</label>
                        <div class="col-md-6 col-form-label">
                            {{$collection->bonus_point}}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="answer" class="col-md-4 col-form-label text-md-right">{{ __('All Point') }}</label>
                        <div class="col-md-6 col-form-label">
                            {{$collection->all_point}}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="answer" class="col-md-4 col-form-label text-md-right">{{ __('Total Weight') }}</label>
                        <div class="col-md-6 col-form-label">
                            {{$collection->total_weight}} kg
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="answer" class="col-md-4 col-form-label text-md-right">{{ __('Photo') }}</label>
                        <div class="col-md-6 col-form-label">
                            @foreach (explode(';', $collection->photo) as $value)
                            <img id="imagePreview" width="100%" src="<?php echo env('APP_URL') . '/nucycle-collector/images/collection_photo/' . $value ?>">
                            @endforeach
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="answer" class="col-md-4 col-form-label text-md-right">{{ __('Created At') }}</label>
                        <div class="col-md-6 col-form-label">
                            {{$collection->created_at}}
                        </div>
                    </div>
                        <table class="table table-striped px-5" id="tableMain">
                            <thead>
                                <tr>
                                    <th><span>Item</span></th>
                                    <th><span>Weight</span></th>
                                    <th><span>Point</span></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($collection->collection_detail as $item)
                                <tr>
                                    <td class="lalign">{{$item->recycle_type->name}}</td>
                                    <td>{{$item->weight}}</td>
                                    <td>{{$item->total_point}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    <div class="form-group row mb-5">
                        <div class="col-md-6 offset-md-4">
                            <a href="{{ route('collection.edit', $collection->id) }}" class="btn btn-xs btn-success pull-right" type="button">{{ __('Edit') }}</a>

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