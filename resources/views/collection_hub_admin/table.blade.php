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
                                <th><span>Email</span></th>
                                <th><span>Hub</span></th>
                                <th><span>Name</span></th>
                                <th><span>Action</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($hub_admins as $hub_admin)
                            <tr>
                                <td class="lalign">{{$hub_admin->email}}</td>
                                <td>{{$hub_admin->hub_name}}</td>
                                <td>{{$hub_admin->name}}</td>
                                <td>
                                    <a href="{{route('collection_hub_admin.view', ['id' => $hub_admin->user_id])}}" class="btn btn-xs btn-success"><i class="nav-icon fa fa-eye"></i></a>
                                    <a href="{{route('collection_hub_admin.edit', ['id' => $hub_admin->user_id])}}" class="btn btn-xs btn-success "><i class="nav-icon fa fa-pencil"></i></a>
                                    <a href="{{route('collection_hub_admin.delete', ['id' => $hub_admin->user_id])}}" class="btn btn-xs btn-danger"><i class="nav-icon fa fa-trash"></i></a>
                                    <!-- <a href="{{ URL('collection-hub-admin/delete/'. $hub_admin->user_id) }}" class="btn btn-xs btn-danger button-float-right"><i class="nav-icon fa fa-trash"></i></a>
                                    <a href="{{ URL('collection-hub-admin/edit/'. $hub_admin->user_id) }}" class="btn btn-xs btn-success button-float-right"><i class="nav-icon fa fa-eye"></i></a> -->
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>