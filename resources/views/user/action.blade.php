@can('edit_user')
<button class="btn btn-info btn-sm" onclick="loadEditUser({{ $model->id }})">Edit</button>
@endcan
@can('delete_user')
<button class="btn btn-danger btn-sm" onclick="deleteUser({{ $model->id }})">Delete</button>
@endcan
