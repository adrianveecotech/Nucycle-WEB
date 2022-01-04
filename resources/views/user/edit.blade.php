@extends('layouts.app')
@section('content')

<div class="container">
    <div class="row ">
        <div class="col">
            <div class="card">
                <div class="card-header">{{ __('Edit User') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('user.edit_db') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" value={{$id}} name="id">
                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('Email') }}</label>
                            <div class="col-md-6 col-form-label">
                            {{$user->user->email}}
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="role" class="col-md-4 col-form-label text-md-right">{{ __('Role') }}</label>

                            <div class="col-md-6 col-form-label">
                                <select id="role" name="role">
                                    @foreach ($roles as $role)
                                    @if (old('role') == $role->id || $user->role_id == $role->id)
                                    <option value="{{ $role->id }}" selected>{{ $role ->role }}</option>
                                    @else
                                    <option value="{{ $role->id }}">{{ $role ->role }}</option>
                                    @endif
                                    @endforeach
                                </select>
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
    $(document).ready(function() {
        getCity();

        jQuery('#role').change(function() {
            getCity();
        });

        function getCity() {
            var user_city = '';
            <?php if ($user->city) ?>
            user_city = <?php echo $user->city ?>;
            $('#city').append('<option value="1">None</option>');
            var state_id = $('#role').find(":selected").val();
            jQuery.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            jQuery.ajax({
                url: '{{ route("get_city_by_state") }}',
                method: 'post',
                data: {
                    "_token": "{{ csrf_token() }}",
                    state_id: state_id,
                    selected_city_id: user_city,
                },
                success: function(data) {
                    $('#city').empty();
                    $('#city').append(data.html);
                },
                error: function(data) {
                    console.log(data.responseJSON.message);
                }

            });

            // console.log(pausecontent);
            // recycle[hub_id].forEach(element => {
            //     console.log(element);
            //     // $("#hubRecycle tbody").append("<tr>" +
            //     //     "<td class='lalign'>" + element. + "</td>" +
            //     //     "<td>" + $("#introdate").val() + "</td>" +
            //     //     "<td>" + $("#url").val() + "</td>" +
            //     //     "</tr>");
            // });
        };

    });
</script>
@endsection