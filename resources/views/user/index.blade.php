@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex flex-row justify-content-between">
                        <h4>User</h4>
                        @can('create_user')
                            <button class="btn btn-primary btn-sm"  onclick="modalTambahUser()">Tambah</button>
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tbUser" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Barang</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form id="formUser" class="needs-validation" novalidate >
                <input type="hidden" id="id" name="id">
                <div class="mb-3 ">
                    <label for="name" class="form-label">Nama</label>
                    <input type="text" class="form-control" required id="name" placeholder="Nama" name="name">
                </div>

                <div class="mb-3 ">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" required id="email" name="email" placeholder="Email">
                </div>

                <div class="mb-3 ">
                    <label for="email" class="form-label">Password</label>
                    <input type="password" class="form-control" required id="password" name="password" placeholder="Password">
                </div>

                <div class="mb-3 ">
                    <label for="role" class="form-label">Role</label>
                    <select class="form-control" name="role" id="role">
                        <option disabled value="">Pilih Role</option>
                        @foreach ($role as $item)
                            <option value="{{ $item->name }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>

            </form>

            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-primary"  form="formUser">Save</button>
            </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('script')
<script>
    let myModal = new bootstrap.Modal($("#exampleModal"),{keyboard:false})

    const modalTambahUser = () => {
        myModal.show()
    }

    $("#formUser").submit(function (e){
        formSubmitUser(e)
    })

    const formSubmitUser = (event) => {
        event.preventDefault()
            removeXhr()
            let id = $("#id").val()
            if (id == "") {
                url  = "user"
                type = "POST"
            } else {
                url  = `user/${id}?_method=PATCH`
                type = "POST"
            }
            let form = document.getElementById('formUser');
            let formData = new FormData(form)
            $.ajax({
                type: type,
                url: url,
                data:formData,
                contentType: false,
                processData: false,
                error : function (xhr) {
                    $("#formUser").addClass("was-validated")
                    handleErrorXhr(xhr)
                },
                success: function (response) {
                    if (response.code == 200) {
                        responSwalAlert('center', 'success' , response.message)
                        myModal.hide()
                        loadDataUser()
                    } else {
                        responSwalAlert('center', 'error' , response.message)
                    }
                }
        });
    }

    const loadEditUser = (id) => {
        clearFormInput()
        removeXhr()
        $("#divShowFoto").addClass('d-none')
        myModal.show()

        $.ajax({
            type: "GET",
            url: `user/${id}/edit`,
            success: function (response) {
                $("#id").val(response.id)
                $("#name").val(response.name)
                $("#email").val(response.email)
                $("#role").val(response.roles[0].name)
            }
        });
    }

    const deleteUser = (id) => {
        swalConfirmasion('Anda yakin ingin menghapus user ini?', () => {
            $.ajax({
                type: "DELETE",
                url: `user/${id}`,
                success: function (response) {
                    if (response.code == 200) {
                        loadDataUser()
                        responSwalAlert('center', 'success' , response.message)
                    } else {
                        responSwalAlert('center', 'error' , response.message)
                    }
                }
            });
        })
    }

    const loadDataUser = () => {
        $("#tbUser").DataTable({
            processing: true,
            serverside: true,
            bDestroy: true,
            autoWidth: false,
            ajax : "user",
            columns : [
                {
                    data : "DT_RowIndex",
                    className : 'center'
                },
                {
                    data : "name",
                    className : 'left'
                },
                {
                    data : "email",
                    className : 'left'
                },
                {
                    data : "roles[0].name",
                    className : 'left'
                },
                {
                    data : "action",
                    className : 'left'
                },
            ]
        })
    }

    const clearFormInput = () => {
        $("#formUser")[0].reset()
        $("#id").val("")
    }

    $(document).ready(function () {
        loadDataUser()
    });

</script>
@endsection
