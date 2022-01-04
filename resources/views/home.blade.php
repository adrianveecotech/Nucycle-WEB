@extends('layouts.app')
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header content-header{{setting('fixed_header')}}">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <div class="row">
                    <h1>Dashboard </h1>
                    <h3 id="hub_name"></h3>

                </div>
                <br>
                @if (in_array(4, Auth::user()->users_roles_id()) || in_array(5, Auth::user()->users_roles_id()))
                @if(count($hubs) > 1)
                <select id="hub" name="hub">
                    <option value="0">All</option>
                    @foreach ($hubs as $hub)
                    <option value='{{$hub->collection_hub_id}}'>{{$hub->collection_hub->hub_name}}</option>
                    @endforeach
                </select>
                @endif
                @endif
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>

<div class="content">
    <!-- Small boxes (Stat box) -->
    @if (in_array(1, Auth::user()->users_roles_id()))
    <div class="row">
        <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{$customerCount}}</h3>

                    <p>Customers</p>
                </div>
                <div class="icon">
                    <i class="fa fa-group"></i>
                </div>
                <a href="{!! route('customer.index') !!}" class="small-box-footer">More Infos
                    <i class="fa fa-arrow-circle-right"></i></a>

            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{$collectorCount}}</h3>

                    <p>Collectors</p>
                </div>
                <div class="icon">
                    <i class="fa fa-truck"></i>
                </div>
                <a href="{!! route('collection_hub_collector.index') !!}" class="small-box-footer">More Infos
                    <i class="fa fa-arrow-circle-right"></i></a>

            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{$hubCount}}</h3>

                    <p>Collection Centers</p>
                </div>
                <div class="icon">
                    <i class="fa fa-building"></i>
                </div>
                <a href="{!! route('collection_hub.index') !!}" class="small-box-footer">More Infos
                    <i class="fa fa-arrow-circle-right"></i></a>

            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{$collectionCount}}</h3>

                    <p>Collections</p>
                </div>
                <div class="icon">
                    <i class="fa fa-recycle"></i>
                </div>
                <a href="{!! route('collection.index') !!}" class="small-box-footer">More Infos
                    <i class="fa fa-arrow-circle-right"></i></a>

            </div>
        </div>
        <!-- ./col -->

    </div>
    @endif
    <!-- /.row -->

    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header no-border">
                    <div class="d-flex justify-content-between">
                        @if(in_array(1, Auth::user()->users_roles_id()))
                        <h3 class="card-title">Collections in Month</h3>

                        @elseif(in_array(4, Auth::user()->users_roles_id()) || in_array(5, Auth::user()->users_roles_id()))

                        <h3 class="card-title">Number of transaction</h3>

                        @endif
                    </div>
                </div>
                <div class="card-body">

                    <div class="position-relative mb-4" id="chart1">

                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header no-border">
                    <div class="d-flex justify-content-between">
                        @if(in_array(1, Auth::user()->users_roles_id()))
                        <h3 class="card-title">Collections By Day in this Month</h3>
                        @elseif(in_array(4, Auth::user()->users_roles_id()) || in_array(5, Auth::user()->users_roles_id()))
                        <h3 class="card-title">Number of transaction by day in current month</h3>
                        @endif
                    </div>
                </div>
                <div class="card-body">

                    <div class="position-relative mb-4" id="chart2">

                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header no-border">
                    <div class="d-flex justify-content-between">
                        @if(in_array(1, Auth::user()->users_roles_id()))
                        <h3 class="card-title">New Users By Month</h3>
                        @elseif(in_array(4, Auth::user()->users_roles_id()) || in_array(5, Auth::user()->users_roles_id()))
                        <h3 class="card-title">Collected weight</h3>
                        @endif
                    </div>
                </div>
                <div class="card-body">

                    <div class="position-relative mb-4" id="chart3">

                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header no-border">
                    <div class="d-flex justify-content-between">
                        @if(in_array(1, Auth::user()->users_roles_id()))
                        <h3 class="card-title">Top 5 Collection Centers</h3>

                        @elseif(in_array(4, Auth::user()->users_roles_id()) || in_array(5, Auth::user()->users_roles_id()))

                        <h3 class="card-title">Total type of waste collected in current month</h3>

                        @endif
                    </div>
                </div>
                <div class="card-body">
                    @if(in_array(1, Auth::user()->users_roles_id()))
                    <div class="tbl-header">
                        <table class="table table-striped" id="tableMain">
                            <thead>
                                <tr>
                                    <th><span>Collection Hub</span></th>
                                    <th><span>Number of Collections</span></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($collectionhubs as $collectionhub)
                                <tr>
                                    <td class="lalign">{{$collectionhub->hub_name}}</td>
                                    <td>{{$collectionhub->total}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @elseif(in_array(4, Auth::user()->users_roles_id()) || in_array(5, Auth::user()->users_roles_id()))
                    <div class="position-relative mb-4" id="chart4">

                        @endif
                    </div>
                </div>
            </div>
        </div>
        @if(in_array(4, Auth::user()->users_roles_id()) || in_array(5, Auth::user()->users_roles_id()))
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header no-border">
                    <div class="d-flex justify-content-between">
                        <h3 class="card-title">New users visited</h3>
                    </div>
                </div>
                <div class="card-body">

                    <div class="position-relative mb-4" id="chart5">

                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header no-border">
                    <div class="d-flex justify-content-between">
                        <h3 class="card-title">Number of distinct users visited</h3>
                    </div>
                </div>
                <div class="card-body">

                    <div class="position-relative mb-4" id="chart6">

                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@endsection
@push('scripts_lib')
<script src="{{asset('plugins/chart.js/Chart.min.js')}}"></script>
@endpush
@push('scripts')
<script type="text/javascript">
    $(document).ready(function() {
        getHomeData();

        $('#hub').change(function() {
            getHomeData();
        })

        function getHomeData() {
            <?php if (in_array(1, Auth::user()->users_roles_id())) { ?>
                var dataCollectionsByMonth = <?php echo json_encode($dataCollectionsByMonth); ?>;
                var monthsName = <?php echo json_encode($monthsName); ?>;
                $('#chart1').append(' <canvas id="myChart1" width="200" ></canvas>');
                var ctx = document.getElementById('myChart1');
                var myChart = new Chart(ctx, {
                    type: 'bar',
                    plugins: [ChartDataLabels],
                    data: {
                        labels: monthsName,
                        datasets: [{
                            label: 'Number of Collections',
                            data: dataCollectionsByMonth,
                            backgroundColor: '#5886a5',
                            borderColor: '#5886a5',
                            borderWidth: 1,
                            minBarLength: 7,

                        }]
                    },
                    options: {
                        plugins: {
                            labels: false,
                            datalabels: {
                                backgroundColor: function(context) {
                                    return context.dataset.backgroundColor;
                                },
                                borderRadius: 4,
                                color: 'white',
                                font: {
                                    weight: 'bold'
                                },
                                formatter: Math.round,
                                padding: 6
                            }
                        },
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true
                                },
                                gridLines: {
                                    color: "rgba(0, 0, 0, 0)",
                                }
                            }],
                            xAxes: [{
                                barPercentage: 0.5,
                                gridLines: {
                                    color: "rgba(0, 0, 0, 0)",
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

                var dataDailyCollectionsByDay = <?php echo json_encode($dataDailyCollectionsByDay); ?>;
                var days = <?php echo json_encode($days); ?>;
                $('#chart2').append(' <canvas id="myChart2" width="200" ></canvas>');
                var ctx = document.getElementById('myChart2');
                var myChart = new Chart(ctx, {
                    type: 'bar',
                    plugins: [ChartDataLabels],

                    data: {
                        labels: days,
                        datasets: [{
                            label: 'Number of Collections',
                            data: dataDailyCollectionsByDay,
                            backgroundColor: '#9dc6e0',
                            borderColor: '#9dc6e0',
                            borderWidth: 1,

                        }]
                    },
                    options: {
                        plugins: {
                            labels: false,
                            datalabels: {
                                backgroundColor: function(context) {
                                    return context.dataset.backgroundColor;
                                },
                                borderRadius: 4,
                                color: 'white',
                                font: {
                                    weight: 'bold'
                                },
                                formatter: Math.round,
                                padding: 6
                            }
                        },
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true
                                },
                                gridLines: {
                                    color: "rgba(0, 0, 0, 0)",
                                }
                            }],
                            xAxes: [{
                                barPercentage: 0.5,
                                gridLines: {
                                    color: "rgba(0, 0, 0, 0)",
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

                var dataNewUsersByMonth = <?php echo json_encode($dataNewUsersByMonth); ?>;
                $('#chart3').append(' <canvas id="myChart3" width="200" ></canvas>');
                var ctx = document.getElementById('myChart3');
                var myChart = new Chart(ctx, {
                    type: 'bar',
                    plugins: [ChartDataLabels],

                    data: {
                        labels: monthsName,
                        datasets: [{
                            label: 'Number of Users',
                            data: dataNewUsersByMonth,
                            backgroundColor: '#c1e7ff',
                            borderColor: '#c1e7ff',
                            borderWidth: 1,

                        }]
                    },
                    options: {
                        plugins: {
                            labels: false,
                            datalabels: {
                                backgroundColor: function(context) {
                                    return context.dataset.backgroundColor;
                                },
                                borderRadius: 4,
                                color: 'white',
                                font: {
                                    weight: 'bold'
                                },
                                formatter: Math.round,
                                padding: 6
                            },
                            datalabels: {
                                backgroundColor: function(context) {
                                    return context.dataset.backgroundColor;
                                },
                                borderRadius: 4,
                                color: 'white',
                                font: {
                                    weight: 'bold'
                                },
                                formatter: Math.round,
                                padding: 6
                            }
                        },
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true
                                },
                                gridLines: {
                                    color: "rgba(0, 0, 0, 0)",
                                }
                            }],
                            xAxes: [{
                                barPercentage: 0.5,
                                gridLines: {
                                    color: "rgba(0, 0, 0, 0)",
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
            <?php }
            if (in_array(4, Auth::user()->users_roles_id()) || in_array(5, Auth::user()->users_roles_id())) { ?>
                var hub_id = $('#hub').find(":selected").val();
                jQuery.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                });
                jQuery.ajax({
                    url: '{{ route("get_home_data") }}',
                    method: 'post',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        hub_id: hub_id
                    },
                    success: function(data) {
                        var weekLabel = data[0];
                        var dayLabel = data[1];
                        var categoryLabel = data[2];
                        var transactionByMonth = data[3];
                        var transactionByDay = data[4];
                        var weightByMonth = data[5];
                        var newUserByMonth = data[6];
                        var weightByCategory = data[7];
                        var colorCategory = data[8];
                        var hubs = data[9];
                        var hub_info = data[10];
                        var monthsName = data[11];
                        var hubImage = data[12];
                        var distinctUserByMonth = data[13];
                        var profileImage = data[14];
                        $("#hub_logo").attr("src", hubImage);
                        $("#hub_logo1").attr("src", profileImage);
                        console.log(hubImage);
                        if (hub_info != '')
                            $("#hub_name").html('- ' + hub_info.hub_name);
                        else
                            $("#hub_name").html('- All');

                        $('#chart1').empty();
                        $('#chart1').append('<canvas id="myChart1" width="200"></canvas>');
                        var ctx = document.getElementById('myChart1');

                        var myChart = new Chart(ctx, {
                            type: 'bar',
                            plugins: [ChartDataLabels],

                            data: {
                                labels: monthsName,
                                datasets: [{
                                    label: 'Number of Collections',
                                    data: transactionByMonth,
                                    backgroundColor: colorCategory[0],
                                    borderColor: colorCategory[0],
                                    borderWidth: 1,

                                }]
                            },
                            options: {
                                plugins: {
                                    labels: false,
                                    datalabels: {
                                        backgroundColor: function(context) {
                                            return context.dataset.backgroundColor;
                                        },
                                        borderRadius: 4,
                                        color: 'white',
                                        font: {
                                            weight: 'bold'
                                        },
                                        formatter: Math.round,
                                        padding: 6
                                    }
                                },
                                scales: {
                                    yAxes: [{
                                        minBarLength: 7,
                                        ticks: {
                                            beginAtZero: true
                                        },
                                        gridLines: {
                                            color: "rgba(0, 0, 0, 0)",
                                        },

                                    }],
                                    xAxes: [{
                                        barPercentage: 0.5,
                                        gridLines: {
                                            color: "rgba(0, 0, 0, 0)",
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
                        $('#chart2').empty();
                        $('#chart2').append(' <canvas id="myChart2" width="200" ></canvas>');
                        var ctx = document.getElementById('myChart2');
                        var myChart = new Chart(ctx, {
                            type: 'bar',
                            plugins: [ChartDataLabels],

                            data: {
                                labels: dayLabel,
                                datasets: [{
                                    label: 'Number of Collections',
                                    data: transactionByDay,
                                    backgroundColor: colorCategory[1],
                                    borderColor: colorCategory[1],
                                    borderWidth: 1,

                                }]
                            },
                            options: {
                                plugins: {
                                    labels: false,
                                    datalabels: {
                                        backgroundColor: function(context) {
                                            return context.dataset.backgroundColor;
                                        },
                                        borderRadius: 4,
                                        color: 'white',
                                        font: {
                                            weight: 'bold'
                                        },
                                        formatter: Math.round,
                                        padding: 6
                                    }
                                },
                                scales: {
                                    yAxes: [{
                                        minBarLength: 7,
                                        ticks: {
                                            beginAtZero: true
                                        },
                                        gridLines: {
                                            color: "rgba(0, 0, 0, 0)",
                                        }
                                    }],
                                    xAxes: [{
                                        barPercentage: 0.5,
                                        gridLines: {
                                            color: "rgba(0, 0, 0, 0)",
                                        },
                                        barPercentage: 0.4
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
                        $('#chart3').empty();
                        $('#chart3').append(' <canvas id="myChart3" width="200" ></canvas>');
                        var ctx = document.getElementById('myChart3');
                        var myChart = new Chart(ctx, {
                            type: 'bar',
                            plugins: [ChartDataLabels],

                            data: {
                                labels: monthsName,
                                datasets: [{
                                    label: 'weight(kg)',
                                    data: weightByMonth,
                                    backgroundColor: colorCategory[2],
                                    borderColor: colorCategory[2],
                                    borderWidth: 1,

                                }]
                            },
                            options: {
                                plugins: {
                                    labels: false,
                                    datalabels: {
                                        backgroundColor: function(context) {
                                            return context.dataset.backgroundColor;
                                        },
                                        borderRadius: 4,
                                        color: 'white',
                                        font: {
                                            weight: 'bold'
                                        },
                                        formatter: Math.round,
                                        padding: 6
                                    }
                                },
                                scales: {
                                    yAxes: [{
                                        minBarLength: 7,
                                        ticks: {
                                            beginAtZero: true
                                        },
                                        gridLines: {
                                            color: "rgba(0, 0, 0, 0)",
                                        }
                                    }],
                                    xAxes: [{
                                        barPercentage: 0.5,
                                        gridLines: {
                                            color: "rgba(0, 0, 0, 0)",
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
                        $('#chart4').empty();
                        $('#chart4').append(' <canvas id="myChart4" width="200" ></canvas>');
                        var ctx = document.getElementById('myChart4');
                        var myChart = new Chart(ctx, {
                            type: 'pie',
                            data: {
                                labels: categoryLabel,
                                datasets: [{
                                    data: weightByCategory,
                                    backgroundColor: colorCategory,
                                    hoverOffset: 10,


                                }]
                            },
                            options: {
                                plugins: {
                                    // Change options for ALL labels of THIS CHART
                                    datalabels: {
                                        color: '#36A2EB'
                                    }
                                }
                            },
                        });
                        $('#chart5').empty();
                        $('#chart5').append(' <canvas id="myChart5" width="200" ></canvas>');
                        var ctx = document.getElementById('myChart5');
                        var myChart = new Chart(ctx, {
                            type: 'bar',
                            plugins: [ChartDataLabels],

                            data: {
                                labels: monthsName,
                                datasets: [{
                                    label: 'Number of user',
                                    data: newUserByMonth,
                                    backgroundColor: colorCategory[3],
                                    borderColor: colorCategory[3],
                                    borderWidth: 1,

                                }]
                            },
                            options: {
                                plugins: {
                                    labels: false,
                                    datalabels: {
                                        backgroundColor: function(context) {
                                            return context.dataset.backgroundColor;
                                        },
                                        borderRadius: 4,
                                        color: 'white',
                                        font: {
                                            weight: 'bold'
                                        },
                                        formatter: Math.round,
                                        padding: 6
                                    }
                                },
                                scales: {
                                    yAxes: [{
                                        minBarLength: 10,
                                        ticks: {
                                            beginAtZero: true
                                        },
                                        gridLines: {
                                            color: "rgba(0, 0, 0, 0)",
                                        }
                                    }],
                                    xAxes: [{
                                        barPercentage: 0.5,
                                        gridLines: {
                                            color: "rgba(0, 0, 0, 0)",
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

                        $('#chart6').empty();
                        $('#chart6').append(' <canvas id="myChart6" width="200" ></canvas>');
                        var ctx = document.getElementById('myChart6');
                        var myChart = new Chart(ctx, {
                            type: 'bar',
                            plugins: [ChartDataLabels],

                            data: {
                                labels: monthsName,
                                datasets: [{
                                    label: 'Number of user',
                                    data: distinctUserByMonth,
                                    backgroundColor: colorCategory[4],
                                    borderColor: colorCategory[4],
                                    borderWidth: 1,

                                }]
                            },
                            options: {
                                plugins: {
                                    labels: false,
                                    datalabels: {
                                        backgroundColor: function(context) {
                                            return context.dataset.backgroundColor;
                                        },
                                        borderRadius: 4,
                                        color: 'white',
                                        font: {
                                            weight: 'bold'
                                        },
                                        formatter: Math.round,
                                        padding: 6
                                    }
                                },
                                scales: {
                                    yAxes: [{
                                        minBarLength: 10,
                                        ticks: {
                                            beginAtZero: true
                                        },
                                        gridLines: {
                                            color: "rgba(0, 0, 0, 0)",
                                        }
                                    }],
                                    xAxes: [{
                                        barPercentage: 0.5,
                                        gridLines: {
                                            color: "rgba(0, 0, 0, 0)",
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
            <?php } ?>
        }

    });
</script>
@endpush