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
                                <th><span>Actions</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($indicators as $indicator)
                            <tr>
                                <td class="lalign">{{$indicator->name}}</td>
                                <td>
                                    <a href="{{route('settings.statistic_indicator.view', ['id' => $indicator->id])}}" class="btn btn-xs btn-success"><i class="nav-icon fa fa-eye"></i></a>
                                    <a href="{{route('settings.statistic_indicator.edit', ['id' => $indicator->id])}}" class="btn btn-xs btn-success"><i class="nav-icon fa fa-pencil"></i></a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>