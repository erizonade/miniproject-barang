@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex flex-row justify-content-between">
                        <h4>Barang</h4>
                        @can('create_barang')
                            <button class="btn btn-primary btn-sm"  onclick="modalTambahBarang()">Tambah</button>
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tbBarang" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Foto Barang</th>
                                    <th>Kode Barang</th>
                                    <th>Nama Barang</th>
                                    <th>Harga</th>
                                    <th>Stok</th>
                                    <th>Satuan</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>

            @can('read_log')
                <div class="card mt-5">
                    <div class="card-header">
                        <div class="d-flex flex-row justify-content-between">
                            <h4>Log Aktivitas Barang</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="tbLogAktivitas" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Barang Id</th>
                                    <th>Nama Barang</th>
                                    <th>Pesan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            @endcan


            <!-- Modal -->
            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="exampleModalLabel">Barang</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formBarang" class="needs-validation" novalidate {{-- onsubmit="formSubmitBarang(event)" --}} enctype="multipart/form-data">
                            <input type="hidden" id="id" name="id">
                            <div class="mb-3 ">
                                <label for="kode_barang" class="form-label">Kode Barang</label>
                                <input type="text" class="form-control" required id="kode_barang" placeholder="Kode Barang" name="kode_barang">
                            </div>

                            <div class="mb-3 ">
                                <label for="nama_barang" class="form-label">Nama Barang</label>
                                <input type="text" class="form-control" required id="nama_barang" name="nama_barang" placeholder="Nama Barang">
                            </div>

                            <div class="mb-3 ">
                                <label for="satuan" class="form-label">Satuan Barang</label>
                                <input type="text" class="form-control" required id="satuan" name="satuan" placeholder="Satuan Barang">
                            </div>

                            <div class="mb-3 ">
                                <label for="harga" class="form-label">Harga Barang</label>
                                <input type="number" class="form-control" required id="harga" name="harga" placeholder="Harga Barang">
                            </div>

                            <div class="mb-3 ">
                                <label for="stok" class="form-label">Stok Barang</label>
                                <input type="number" class="form-control" required id="stok" name="stok"  placeholder="Stok Barang">
                            </div>

                            <div class="mb-3 ">
                                <label for="foto_barang" class="form-label">Foto Barang</label>
                                <input class="form-control" required onchange="showFoto(event)" type="file" id="foto_barang" name="foto_barang">
                            </div>


                        </form>

                        <div class="justify-content-center d-none" id="divShowFoto">
                            <img src="" id="showNow" class="img-thumbnail w-96 h-96" alt="" >
                        </div>

                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                          <button type="submit" class="btn btn-primary"  form="formBarang">Save</button>
                        </div>
                    </div>
                  </div>
                </div>
              </div>

        </div>
    </div>
</div>
@endsection
@section('script')
<script>

    let myModal = new bootstrap.Modal($("#exampleModal"),{keyboard:false})

    const showFoto = (event) => {
        $("#divShowFoto").removeClass('d-none')
        $("#showNow").attr('src', URL.createObjectURL(event.target.files[0]));
    }

    $("#formBarang").submit(function (e){
        formSubmitBarang(e)
    })

    const formSubmitBarang = (event) => {
        event.preventDefault()
            removeXhr()
            let id = $("#id").val()
            if (id == "") {
                url  = "barang"
                type = "POST"
            } else {
                url  = `barang/${id}?_method=PATCH`
                type = "POST"
            }
            let form = document.getElementById('formBarang');
            let formData = new FormData(form)

            $.ajax({
                type: type,
                url: url,
                data: formData,
                contentType: false,
                processData: false,
                error : function (xhr) {
                    $("#formBarang").addClass("was-validated")
                    handleErrorXhr(xhr)
                },
                success: function (response) {
                    if (response.code == 200) {
                        responSwalAlert('center', 'success' , response.message)
                        myModal.hide()
                        loadTableBarang()
                        loadLogAktivitas()
                    } else {
                        responSwalAlert('center', 'error' , response.message)
                    }
                }
        });
    }

    const modalTambahBarang = () => {
        clearFormInput()
        removeXhr()
        $("#divShowFoto").addClass('d-none')
        myModal.show()
    }

    const loadEditBarang = (id) => {
        clearFormInput()
        removeXhr()
        $("#divShowFoto").addClass('d-none')
        myModal.show()

        $.ajax({
            type: "GET",
            url: `barang/${id}/edit`,
            success: function (response) {
                $("#id").val(response.id)
                $("#kode_barang").val(response.kode_barang)
                $("#nama_barang").val(response.nama_barang)
                $("#satuan").val(response.satuan)
                $("#harga").val(response.harga)
                $("#stok").val(response.stok)
            }
        });
    }

    const deleteBarang = (id) => {
        swalConfirmasion('Anda yakin ingin menghapus barang ini?', () => {
            $.ajax({
                type: "DELETE",
                url: `barang/${id}`,
                success: function (response) {
                    if (response.code == 200) {
                        loadLogAktivitas()
                        loadTableBarang()
                        responSwalAlert('center', 'success' , response.message)
                    } else {
                        responSwalAlert('center', 'error' , response.message)
                    }
                }
            });
        })
    }

    const loadTableBarang = () => {
        $('#tbBarang').DataTable({
            processing: true,
            serverside: true,
            bDestroy: true,
            autoWidth: false,
            ajax: 'barang',
            columns : [
                {
                    data : 'DT_RowIndex',
                    className : 'center'
                },
                {
                    data : 'foto_barang',
                    className : 'left'
                },
                {
                    data : 'kode_barang',
                    className : 'left'
                },
                {
                    data : 'nama_barang',
                    className : 'left'
                },
                {
                    data : 'satuan',
                    className : 'right'
                },
                {
                    data : 'harga',
                    className : 'right'
                },
                {
                    data : 'stok',
                    className : 'right'
                },
                {
                    data : 'action',
                    className : 'center'
                }
            ]
        });
    }


    const clearFormInput = () => {
        $("#formBarang")[0].reset()
        $("#id").val("")
    }

    const loadLogAktivitas = () => {
        $("#tbLogAktivitas").DataTable({
            processing: true,
            serverside: true,
            bDestroy: true,
            autoWidth: false,
            ajax: 'log',
            columns : [
                {
                    data : 'DT_RowIndex',
                    className : 'center'
                },
                {
                    data : 'timestamp',
                    className : 'left'
                },
                {
                    data : 'barang_id',
                    className : 'left'
                },
                {
                    data : 'nama_barang',
                    className : 'left'
                },
                {
                    data : 'message',
                    className : 'left'
                },
                {
                    data : 'action',
                    className : 'right'
                },
            ]
        })
    }

    $(document).ready(function() {
        loadTableBarang()
        loadLogAktivitas()
    })
</script>
@endsection
