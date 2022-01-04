@extends('layouts.app')
@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col">
            <div class="card">
                <div class="card-header">{{ __('View Activity') }}</div>

                <div class="card-body">
                    <div class="form-group row">
                        <label for="title" class="col-md-4 col-form-label text-md-right">{{ __('Title') }}</label>
                        <div class="col-md-6 col-form-label">
                            {{$activity->title}}
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="description" class="col-md-4 col-form-label text-md-right">{{ __('Description') }}</label>
                        <div class="col-md-6 col-form-label">
                            {!!$activity->description !!}
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="image" class="col-md-4 col-form-label text-md-right">{{ __('Image') }}</label>

                        <div class="col-md-6">
                            <img id="imagePreview" width="100%" src="<?php echo env('APP_URL') . '/nucycle-admin/images/activity_image/' . $activity->image ?>">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="banner" class="col-md-4 col-form-label text-md-right">{{ __('Banner Tag') }}</label>

                        <div class="col-md-6 col-form-label">
                            {{optional($activity->banner_tag)->name}}
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="start_date" class="col-md-4 col-form-label text-md-right">{{ __('Start Date') }}</label>

                        <div class="col-md-6 col-form-label">
                            {{$activity->start_date}}
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="end_date" class="col-md-4 col-form-label text-md-right">{{ __('End Date') }}</label>

                        <div class="col-md-6 col-form-label">
                            {{$activity->end_date}}
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="status" class="col-md-4 col-form-label text-md-right">{{ __('Status') }}</label>
                        <div class="col-md-6 col-form-label">
                            {{$activity->status == 1 ? 'Publish' : 'Draft'}}
                        </div>
                    </div>

                    <div class="form-group row mb-0">
                        <div class="col-md-6 offset-md-4">
                            <a href="{{ route('activity.edit', $activity->id) }}" class="btn btn-xs btn-success pull-right" type="button">{{ __('Edit') }}</a>

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