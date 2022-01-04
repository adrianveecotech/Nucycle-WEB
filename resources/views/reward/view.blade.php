@extends('layouts.app')
@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col">
            <div class="card">
                <div class="card-header">{{ __('View Reward') }}</div>
                <div class="card-body">
                    <div id="body">
                        <div class="form-group row">
                            <label for="hub_recycle" class="col-md-4 col-form-label text-md-right">{{ __('Merchant') }}</label>

                            <div class="col-md-6  col-form-label">
                                @foreach ($merchants as $merchant)
                                {{$merchant->name}}
                                @endforeach
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="hub_recycle" class="col-md-4 col-form-label text-md-right">{{ __('Reward Category') }}</label>

                            <div class="col-md-6  col-form-label">
                                @foreach ($categories as $category)
                                @if($category->id == $reward->reward_category_id)
                                {{$category->name }}
                                @endif
                                @endforeach
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="title" class="col-md-4 col-form-label text-md-right">{{ __('Title') }}</label>

                            <div class="col-md-6  col-form-label">
                                {{ $reward['title'] }}
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="image" class="col-md-4 col-form-label text-md-right">{{ __('Image') }}</label>

                            <div class="col-md-6">
                                <img id="imagePreview" width="100%" src="<?php echo env('APP_URL') . '/nucycle-admin/images/reward_image/' . $reward->image ?>">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="point" class="col-md-4 col-form-label text-md-right">{{ __('Point') }}</label>

                            <div class="col-md-6  col-form-label">
                                {{ $reward['point'] }}
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="redemption" class="col-md-4 col-form-label text-md-right">{{ __('Redemption per user') }}</label>

                            <div class="col-md-6  col-form-label">
                                {{ $reward['redemption_per_user'] }}
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="description" class="col-md-4 col-form-label text-md-right">{{ __('Description') }}</label>

                            <div class="col-md-6  col-form-label">
                                {!! $reward['description'] !!}
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="tag-typher" class="col-md-4 col-form-label text-md-right">{{ __('Tag') }}</label>
                            <!-- </div>
                        <div class="form-group row"> -->
                            <!-- <div class="col-md-6">
                                <div id="tags">
                                    <input id="tag-typer" name="tag-typher" type="text" placeholder="Add tag..." />
                                </div>
                            </div> -->
                            <div class="col-md-6  col-form-label">
                                {{ $reward['tag'] }}
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="start_date" class="col-md-4 col-form-label text-md-right">{{ __('Start Date') }}</label>

                            <div class="col-md-6  col-form-label">
                                {{ $reward['start_date'] }}
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="end_date" class="col-md-4 col-form-label text-md-right">{{ __('End Date') }}</label>

                            <div class="col-md-6  col-form-label">
                                {{ $reward['end_date'] }}
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="terms" class="col-md-4 col-form-label text-md-right">{{ __('Terms') }}</label>

                            <div class="col-md-6  col-form-label">
                                {{ $reward['terms'] }}
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="status" class="col-md-4 col-form-label text-md-right">{{ __('Status') }}</label>

                            <div class="col-md-6  col-form-label">
                                <?php
                                if ($reward['status'] == 1)
                                    echo ('Publish');
                                elseif ($reward['status'] == 0)
                                    echo ('Draft');
                                elseif ($reward['status'] == 2)
                                    echo ('Expired')
                                ?>

                            </div>
                        </div>

                    </div>
                    <div class="form-group row mb-0">
                        <div class="col-md-6 offset-md-4">
                            <a href="{{route('reward.edit', ['id'=> $id]) }}" class="btn btn-xs btn-success">Edit</a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $("#tag").keypress(function(event) {
            var key = event.which;
            if (key == 13 || key == 44) {
                event.preventDefault();
                var tag = $(this).val();
                if (tag.length > 0) {

                    // $("<span class='tag' style='display:none'><span class='close'>&times;</span>" + tag + " </span>").insertBefore(this).fadeIn(100);
                    $(this).val(tag + ' ; ');
                }
            }
        });

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