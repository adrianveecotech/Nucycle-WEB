@extends('layouts.app')
@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col">
            <div class="card">
                <div class="card-header">{{ __('View Be Our Partner Content') }}</div>

                <div class="card-body">
                        <div class="form-group row">
                            <label for="question" class="col-md-4 col-form-label text-md-right">{{ __('Type') }}</label>
                            <div class="col-md-6 col-form-label">
                                {{$content->be_our_partner_type->name}}
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="answer" class="col-md-4 col-form-label text-md-right">{{ __('Content') }}</label>
                            <div class="col-md-6 col-form-label">
                                {!!$content->content !!}
                            </div>
                        </div>
                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <a href="{{ route('be_our_partner.content.edit', $content->id) }}" class="btn btn-xs btn-success pull-right" type="button">{{ __('Edit') }}</a>

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