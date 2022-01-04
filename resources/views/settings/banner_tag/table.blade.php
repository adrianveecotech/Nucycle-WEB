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
                                <th><span>Active</span></th>
                                <th><span>Actions</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($banner_tags as $banner_tag)
                            <tr>
                                <td class="lalign">{{$banner_tag->id}}</td>
                                <td>{{$banner_tag->name}}</td>
                                <td>{{$banner_tag->is_active == 1? 'Active' : 'Inactive'}}</td>
                                <td>
                                    <a href="{{route('settings.banner_tag.view', ['id' => $banner_tag->id])}}" class="btn btn-xs btn-success"><i class="nav-icon fa fa-eye"></i></a>
                                    <a href="{{route('settings.banner_tag.edit', ['id' => $banner_tag->id])}}" class="btn btn-xs btn-success"><i class="nav-icon fa fa-pencil"></i></a>
                                    <a href="{{route('settings.banner_tag.delete', ['id' => $banner_tag->id])}}" class="btn btn-xs btn-danger"><i class="nav-icon fa fa-trash"></i></a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>