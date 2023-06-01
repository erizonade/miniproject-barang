@can('edit_barang')
    <button class="btn btn-info btn-sm" onclick="loadEditBarang({{ $model->id }})">Edit</button>
@endcan
@can('delete_barang')
    <button class="btn btn-danger btn-sm" onclick="deleteBarang({{ $model->id }})">Delete</button>
@endcan
