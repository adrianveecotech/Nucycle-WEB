@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col">
            <div class="card">
                <div class="card-header">{{ __('Total Number of Users By City and State') }}</div>
                <div class="card-body">
                    <h5>Number of Users By State</h5>
                    <canvas id="myChart" width="300" height="100"></canvas>
                    <br />
                    <br />
                    <h5>Number of Users By City</h5>
                    <select id='state'>
                        <option value=''>None</option>
                        @foreach($states as $state)
                        <option value='{{$state->id}}'>{{$state->name}}</option>
                        @endforeach
                    </select>
                    <br />
                    <br />
                    <div id='chartDiv'></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
    var data = <?php echo json_encode($data); ?>;
    var label = <?php echo json_encode($label); ?>;
    var color = <?php echo json_encode($color); ?>;
    var ctx = document.getElementById('myChart');
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: label,
            datasets: [{
                label: 'Number of Users',
                data: data,
                backgroundColor: color,
                borderColor: color,
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            },
            plugins: {
                labels: false
            },
            legend: {
                display: false
            },
            tooltips: {
                enabled: true
            }
        }
    });

    $(document).ready(function() {
        $('#state').change(function() {
            var state_id = $('#state').find(":selected").val();
            if (state_id == '') {
                $('#chartDiv').empty();
                return;
            }
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: '{{ route("report.get_city_by_state") }}',
                method: 'post',
                data: {
                    "_token": "{{ csrf_token() }}",
                    state_id: state_id,
                },
                success: function(data) {
                    $('#chartDiv').append(' <canvas id="myChart1" width="300" height="100"></canvas>');
                    var ctx = document.getElementById('myChart1');
                    var myChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: data[0],
                            datasets: [{
                                label: 'Number of Users',
                                data: data[1],
                                backgroundColor: data[2],
                                borderColor: data[2],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            plugins: {
                                labels: false
                            },
                            scales: {
                                yAxes: [{
                                    ticks: {
                                        beginAtZero: true
                                    }
                                }]
                            },
                            legend: {
                                display: false
                            },
                            tooltips: {
                                enabled: true
                            }
                        }
                    });
                },
                error: function(data) {
                    console.log(data.responseJSON.message);
                }

            });



        });
    });
</script>
@endsection