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
                            <th><span>Name</span></th>
                            <th><span>Recycle Category</span></th>
                            <th><span>Actions</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recycle_types as $recycle_type)
                        <tr>
                            <td class="lalign">{{$recycle_type->id}}</td>
                            <td>{{$recycle_type->name}}</td>
                            <td>{{$recycle_type->recycle_category->name}}</td>
                            <td>
                                <a href="{{route('settings.recycle_type.view', ['id' => $recycle_type->id])}}" class="btn btn-xs btn-success"><i class="nav-icon fa fa-eye"></i></a>
                                <a href="{{route('settings.recycle_type.edit', ['id' => $recycle_type->id])}}" class="btn btn-xs btn-success"><i class="nav-icon fa fa-pencil"></i></a>
                                <a href="{{route('settings.recycle_type.delete', ['id' => $recycle_type->id])}}" class="btn btn-xs btn-danger"><i class="nav-icon fa fa-trash"></i></a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>