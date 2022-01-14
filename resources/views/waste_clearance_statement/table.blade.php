@if (\Session::has('successMsg'))
<div class="alert alert-success">
    <ul>
        <li>{!! \Session::get('successMsg') !!}</li>
    </ul>
</div>
@endif

<div class="row justify-content-center">
    <div class="col">
        <div id="wrapper">
            <div class="tbl-header">
                <table class="table table-striped" id="tableMain">
                    <thead>
                        <tr>
                            <th><span>ID</span></th>
                            <th><span>Collection Time</span></th>
                            <th><span>Collection Hub</span></th>
                            <th><span>Collection Hub State</span></th>
                            <th><span>Total Weight</span></th>
                            <th><span>Buyer Name</span></th>
                            <th><span>Action</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($schedules as $schedule)
                        <tr>
                            <td class="lalign">{{Helper::lpadClearanceId($schedule->id)}}</td>
                            <td>{{$schedule->completed_at}}</td>
                            <td>{{$schedule->collection_hub->hub_name}}</td>
                            <td>{{$schedule->collection_hub->state->name}}</td>
                            <td>{{number_format((float)$schedule->itemsCollected->sum('weight'), 2, '.', '')}}</td>
                            <td>{{$schedule->buyer_name}}</td>
                            <td>
                                <a href="{{route('waste_clearance_statement.view', ['id' => $schedule->id])}}" class="btn btn-xs btn-success"><i class="nav-icon fa fa-eye"></i></a>
                                <a href="{{route('waste_clearance_statement.payment', ['id' => $schedule->id])}}" class="btn btn-xs btn-success"><i class="nav-icon fa fa-file"></i></a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>