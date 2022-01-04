@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col">
            <div class="card">
                <div class="card-header">{{ __('Rewards Redeem by category') }}</div>
                <div class="card-body">
                    <div class="row justify-content-center mb-4 mt-3">
                        <select id="date" name="date">
                            <option value='Select'>Select a date</option>
                            @foreach ($monthLists as $monthList)
                            <option value='{{$monthList}}'>{{$monthList}}</option>
                            @endforeach
                        </select>
                    </div>
                    <h7 hidden>Monthly</h7>
                    <canvas id="myChart" width="300" height="100"></canvas>
                    <br></br>
                    <br></br>
                    <div>
                        <div class="row">
                            <div class="col-md-6">
                                <h7 hidden>Week 1</h7>
                                <canvas id="week1" width="300" height="100"></canvas>
                            </div>
                            <div class="col-md-6">
                                <h7 hidden>Week 2</h7>
                                <canvas id="week2" width="300" height="100"></canvas>
                            </div>
                        </div>
                        <br></br>
                        <br></br>
                        <div class="row">
                            <div class="col-md-6">
                                <h7 hidden>Week 3</h7>
                                <canvas id="week3" width="300" height="100"></canvas>
                            </div>
                            <div class="col-md-6">
                                <h7 hidden>Week 4</h7>
                                <canvas id="week4" width="300" height="100"></canvas>
                            </div>
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
    var color = '';
    getItem();

    jQuery('#date').change(function() {
        getItem();
    });
    var myChart = '';
    var myChart1 = '';
    var myChart2 = '';
    var myChart3 = '';
    var myChart4 = '';

    function getItem() {
        var date = $('#date').find(":selected").val();
        if (date == "Select") {
            return;
        }
        jQuery.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        jQuery.ajax({
            url: '{{ route("report.get_reward_redeemed") }}',
            method: 'post',
            data: {
                "_token": "{{ csrf_token() }}",
                date: date
            },
            success: function(result) {
                if (result) {
                    console.log(result);
                    let x = document.getElementsByTagName('h7');
                    x[0].removeAttribute("hidden");
                    x[1].removeAttribute("hidden");
                    x[2].removeAttribute("hidden");
                    x[3].removeAttribute("hidden");
                    x[4].removeAttribute("hidden");
                    // jQuery.noConflict();]
                    var label = result[0];
                    var data = result[1];
                    if (!color)
                        color = result[2];
                    var weeklyResult = result[3];

                    var ctx = document.getElementById('myChart');
                    if (myChart) myChart.destroy();
                    myChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: label,
                            datasets: [{
                                data: data,
                                backgroundColor: color,
                            }],

                        },
                        options: {
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

                    var ctx = document.getElementById('week1');
                    if (myChart1) myChart1.destroy();
                    myChart1 = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: weeklyResult[0][0],
                            datasets: [{
                                data: weeklyResult[0][1],
                                backgroundColor: color,
                                hoverOffset: 10
                            }]
                        },
                        options: {
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

                    var ctx = document.getElementById('week2');
                    if (myChart2) myChart2.destroy();
                    myChart2 = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: weeklyResult[1][0],
                            datasets: [{
                                data: weeklyResult[1][1],
                                backgroundColor: color,
                                hoverOffset: 10
                            }]
                        },
                        options: {
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

                    var ctx = document.getElementById('week3');
                    if (myChart3) myChart3.destroy();
                    myChart3 = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: weeklyResult[2][0],
                            datasets: [{
                                data: weeklyResult[2][1],
                                backgroundColor: color,
                                hoverOffset: 10
                            }]
                        },
                        options: {
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

                    var ctx = document.getElementById('week4');
                    if (myChart4) myChart4.destroy();
                    myChart4 = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: weeklyResult[3][0],
                            datasets: [{
                                data: weeklyResult[3][1],
                                backgroundColor: color,
                                hoverOffset: 10
                            }]
                        },
                        options: {
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
                } else {

                }

            },
            error: function(data) {
                console.log(data.responseJSON.message);
            }

        });
    }
</script>
@endsection