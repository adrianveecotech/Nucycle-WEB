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
              <th><span>Name</span></th>
              <th><span>Action</span></th>
            </tr>
          </thead>
          <tbody>
            @foreach($collectors as $collector)
            <tr>
              <td class="lalign">{{$collector->email}}</td>
              <td>{{$collector->name}}</td>
              <td>
                <a href="{{route('collection_hub_collector.view', ['id' => $collector->user_id])}}" class="btn btn-xs btn-success"><i class="nav-icon fa fa-eye"></i></a>
                <a href="{{route('collection_hub_collector.edit', ['id' => $collector->user_id])}}" class="btn btn-xs btn-success"><i class="nav-icon fa fa-pencil"></i></a>
                <a href="{{route('collection_hub_collector.delete', ['id' => $collector->user_id])}}" class="btn btn-xs btn-danger"><i class="nav-icon fa fa-trash"></i></a>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>