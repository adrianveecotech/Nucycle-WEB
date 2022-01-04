@extends('layouts.app')
@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col">
            <div class="card">
                <div class="card-header">{{ __('Contact Us Enquiry') }}</div>

                <div class="card-body">
                    <div class="form-group row">
                        <label for="answer" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>
                        <div class="col-md-6 col-form-label">
                            {{$enquiry->name }}
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('Email') }}</label>
                        <div class="col-md-6 col-form-label">
                            {{$enquiry->email }}
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="phone_number" class="col-md-4 col-form-label text-md-right">{{ __('Phone Number') }}</label>
                        <div class="col-md-6 col-form-label">
                            {{$enquiry->phone_number}}
                        </div>
                    </div>


                    <div class="form-group row">
                        <label for="message" class="col-md-4 col-form-label text-md-right">{{ __('Message') }}</label>
                        <div class="col-md-6 col-form-label">
                            {{$enquiry->message}}
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