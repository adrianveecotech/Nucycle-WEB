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
            @foreach($readers as $reader)
            <tr>
              <td class="lalign">{{$reader->email}}</td>
              <td>{{$reader->hub_name}}</td>
              <td>{{$reader->name}}</td>
              <td>
                <a href="{{route('collection_hub_reader.view', ['id' => $reader->user_id])}}" class="btn btn-xs btn-success"><i class="nav-icon fa fa-eye"></i></a>
                <a href="{{route('collection_hub_reader.edit', ['id' => $reader->user_id])}}" class="btn btn-xs btn-success"><i class="nav-icon fa fa-pencil"></i></a>
                <a href="{{route('collection_hub_reader.delete', ['id' => $reader->user_id])}}" class="btn btn-xs btn-danger"><i class="nav-icon fa fa-trash"></i></a>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>