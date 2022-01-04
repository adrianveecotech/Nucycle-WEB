@extends('layouts.app')
@section('content')

<div class="container">
    <div class="row ">        <div class="col">
            <div class="card">
                <div class="card-header">{{ __('Edit Contact Us Information') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('contact_us.content.edit_db') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group row">
                            <label for="fb" class="col-md-4 col-form-label text-md-right">{{ __('Facebook URL') }}</label>
                            <div class="col-md-6">
                                <input id="fb" type="text" class="form-control @error('fb') is-invalid @enderror" name="fb" value="{{ old('fb',$content->facebook_url)  }}" autofocus>

                                @error('fb')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="ig" class="col-md-4 col-form-label text-md-right">{{ __('Instagram URL') }}</label>
                            <div class="col-md-6">
                                <input id="ig" type="text" class="form-control @error('ig') is-invalid @enderror" name="ig" value="{{ old('ig',$content->instagram_url)  }}" autofocus>

                                @error('ig')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="web" class="col-md-4 col-form-label text-md-right">{{ __('Website URL') }}</label>
                            <div class="col-md-6">
                                <input id="web" type="text" class="form-control @error('web') is-invalid @enderror" name="web" value="{{ old('web',$content->website_url)  }}" autofocus>

                                @error('web')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="phone" class="col-md-4 col-form-label text-md-right">{{ __('Phone') }}</label>
                            <div class="col-md-6">
                                <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone',$content->phone)  }}" autofocus>

                                @error('phone')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>


                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('Email') }}</label>
                            <div class="col-md-6">
                                <input id="email" type="text" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email',$content->email)  }}" autofocus>

                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>


                        <div class="form-group row">
                            <label for="address" class="col-md-4 col-form-label text-md-right">{{ __('Address') }}</label>
                            <div class="col-md-6">
                                <input id="address" type="text" class="form-control @error('address') is-invalid @enderror" name="address" value="{{ old('address',$content->address)  }}" autofocus>

                                @error('address')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>


                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button class="btn btn-xs btn-success pull-right" type="submit">{{ __('Submit') }}</button>

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
</script>
@endsection