@if (\Session::has('successMsg'))
<div class="alert alert-success">
    <ul>
        <li>{!! \Session::get('successMsg') !!}</li>
    </ul>
</div>
@endif
@if (\Session::has('warningMsg'))
<div class="alert alert-danger">
    <ul>
        <li>{!! \Session::get('warningMsg') !!}</li>
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
                            <th><span>Role</span></th>
                            <th><span>Actions</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td class="lalign">{{optional($user->user)->email}}</td>
                            <td>{{$user->role->role}}</td>
                            <td>
                                <a href="{{route('user.edit', ['id' => $user->id])}}" class="btn btn-xs btn-success"><i class="nav-icon fa fa-pencil"></i></a>
                                <a href="{{route('user.delete', ['id' => $user->id])}}" class="btn btn-xs btn-danger"><i class="nav-icon fa fa-trash"></i></a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>