@extends('layouts.app')
@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col">
            <div class="card">
                <div class="card-header">{{ __('View Article') }}</div>

                <div class="card-body">
                    <input type="hidden" value={{$id}} name="article_id">
                    <div class="form-group row">
                        <label for="title" class="col-md-4 col-form-label text-md-right">{{ __('Title') }}</label>
                        <div class="col-md-6 col-form-label">
                            {{$article->title}}
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="description" class="col-md-4 col-form-label text-md-right">{{ __('Description') }}</label>

                        <div class="col-md-6 col-form-label">
                            {!!$article->description!!}
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="image" class="col-md-4 col-form-label text-md-right">{{ __('Image') }}</label>

                        <div class="col-md-6 col-form-label">
                            <img id="imagePreview" width="100%" src="<?php echo env('APP_URL') . '/nucycle-admin/images/article_image/' . $article->image ?>">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="banner" class="col-md-4 col-form-label text-md-right">{{ __('Banner Tag') }}</label>

                        <div class="col-md-6 col-form-label">
                            @foreach ($banners as $key=>$banner)
                            @if (old('banner') == $banner->id || $article->banner_tag_id == $banner->id)
                            {{ $banner ->name }}
                            @endif
                            @endforeach
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="start_date" class="col-md-4 col-form-label text-md-right">{{ __('Start Date') }}</label>

                        <div class="col-md-6 col-form-label">
                            {{$article->start_date}}
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="end_date" class="col-md-4 col-form-label text-md-right">{{ __('End Date') }}</label>

                        <div class="col-md-6 col-form-label">
                            {{$article->end_date}}
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="status" class="col-md-4 col-form-label text-md-right">{{ __('Status') }}</label>
                        <div class="col-md-6 col-form-label">
                            {{$article->status == 1 ? 'Publish' : 'Draft'}}
                        </div>
                    </div>

                    <div class="form-group row mb-0">
                        <div class="col-md-6 offset-md-4">
                            <a href="{{ route('article.edit', $id) }}" class="btn btn-xs btn-success" type="button">{{ __('Edit') }}</a>

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
    $(document).ready(function() {
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('#imagePreview').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]); // convert to base64 string
            } else {
                $('#imagePreview').attr('src', '');
            }
        }

        $("#image").change(function() {
            readURL(this);
        });
    });
</script>
@endsection