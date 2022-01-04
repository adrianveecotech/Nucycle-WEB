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
                            <th><span>Name</span></th>
                            <th><span>Points From</span></th>
                            <th><span>Points To</span></th>
                            <th><span>Multiplier</span></th>
                            <th><span>Free monthly voucher</span></th>
                            <th><span>Image</span></th>
                            <th><span>Action</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($levels as $level)
                        <tr>
                            <td class="lalign">{{$level->name}}</td>
                            <td>{{$level->points_from}}</td>
                            <td>{{$level->points_to}}</td>
                            <td>{{$level->multiplier}}</td>
                            <td>{{$level->free_monthly_voucher}}</td>
                            <td><img width="40%" src=<?php echo env('APP_URL') . '/nucycle-admin/images/avatar/' . $level->image; ?>></td>
                            <td>
                                <a href="{{route('level.view', ['id' => $level->id])}}" class="btn btn-xs btn-success"><i class="nav-icon fa fa-eye"></i></a>
                                <a href="{{route('level.edit', ['id' => $level->id])}}" class="btn btn-xs btn-success"><i class="nav-icon fa fa-pencil"></i></a>
                                <a href="{{route('level.delete', ['id' => $level->id])}}" class="btn btn-xs btn-danger"><i class="nav-icon fa fa-trash"></i></a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>