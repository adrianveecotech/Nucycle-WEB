@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col">
            <div class="card">
                <div class="card-header">{{ __('Total number of users visited collection center by Individual vs Company Accounts') }}</div>
                <div class="card-body">
                    <canvas id="myChart" width="300" height="100"></canvas>
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
    console.log(data[0]);
    var ctx = document.getElementById('myChart');
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: label,
            datasets: [{
                    label: "Individual",
                    backgroundColor: "blue",
                    data: data[0]
                },
                {
                    label: "Company",
                    backgroundColor: "red",
                    data: data[1]
                },
            ]
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
            }
        }
    });
</script>
@endsection