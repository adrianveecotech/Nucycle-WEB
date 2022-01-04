@extends('layouts.app')
@section('content')

<div class="container">
<div class="row ">
        <div class="col">
            <div class="card">
                <div class="card-header">{{ __('Send New Notification') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('notification.insert_db') }}">
                        @csrf
                        <div class="form-group row">
                            <label for="title" class="col-md-4 col-form-label text-md-right">{{ __('Title') }}</label>
                            <div class="col-md-6">
                                <input id="title" type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title')  }}" autofocus>

                                @error('title')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="message" class="col-md-4 col-form-label text-md-right">{{ __('Message') }}</label>
                            <div class="col-md-6">
                                <input id="message" type="text" class="form-control @error('message') is-invalid @enderror" name="message" value="{{ old('message')  }}" autofocus>

                                @error('message')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="user_type" class="col-md-4 col-form-label text-md-right">{{ __('Recipient') }}</label>

                            <div class="col-md-6 col-form-label">
                                <select id="user_type" name="user_type">
                                    <option value='all'>All</option>
                                    <option value='customer'>Customer</option>
                                    <option value="collector">Collector</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="user_type" class="col-md-4 col-form-label text-md-right">{{ __('When') }}</label>

                            <div class="col-md-6 col-form-label">
                                <input type="radio" id="now" name="when" value="now" onclick="handleClick(this);">
                                <label style="font-weight: normal;" for="now">Now</label><br>
                                <input type="radio" id="date" name="when" value="date" onclick="handleClick(this);">
                                <input type="datetime-local" id="send_date" name="send_date">
                                <input type="hidden" class="form-control @error('when') is-invalid @enderror">
                                <input type="hidden" class="form-control @error('send_date') is-invalid @enderror">
                                @error('when')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                                @error('send_date')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button class="btn btn-xs btn-success pull-right" type="submit">{{ __('Create') }}</button>

                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script type="text/javascript">
    function handleClick(myRadio) {
        if (myRadio.value == 'now') {
            $("#send_date").prop('disabled', true);
        } else {
            $("#send_date").prop('disabled', false);
        }
    }
    $("#send_date").click(function() {
        $("#date").prop("checked", true);
    });
</script>
@endsection