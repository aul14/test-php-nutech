<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Test Barang</title>
    <link href="<?php echo base_url('assets/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?php echo base_url('assets/datatables/css/dataTables.bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?php echo base_url('assets/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') ?>" rel="stylesheet">
</head>

<body>
    <div class="container">

        <h3>Test Barang</h3>
        <br>
        <button class="btn btn-success" onclick="tambah_barang()"><i class="glyphicon glyphicon-plus"></i> Tambah</button>
        <button class="btn btn-default" onclick="refresh_table()"><i class="glyphicon glyphicon-refresh"></i> Refresh</button>
        <br>
        <br>
        <table id="table" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>Nama Barang</th>
                    <th>Harga Beli</th>
                    <th>Harga Jual</th>
                    <th>Stok</th>
                    <th>Foto</th>
                    <th style="width:150px;">Opsi</th>
                </tr>
            </thead>
            <tbody>
            </tbody>

            <tfoot>
                <tr>
                    <th>Nama Barang</th>
                    <th>Harga Beli</th>
                    <th>Harga Jual</th>
                    <th>Stok</th>
                    <th>Foto</th>
                    <th>Opsi</th>
                </tr>
            </tfoot>
        </table>
    </div>

    <script src="<?php echo base_url('assets/jquery/jquery-2.1.4.min.js') ?>"></script>
    <script src="<?php echo base_url('assets/bootstrap/js/bootstrap.min.js') ?>"></script>
    <script src="<?php echo base_url('assets/datatables/js/jquery.dataTables.min.js') ?>"></script>
    <script src="<?php echo base_url('assets/datatables/js/dataTables.bootstrap.min.js') ?>"></script>
    <script src="<?php echo base_url('assets/bootstrap-datepicker/js/bootstrap-datepicker.min.js') ?>"></script>


    <script>
        var tipe_simpan;
        var table;
        var base_url = '<?= base_url(); ?>';



        $(document).ready(function() {

            $('.beli-num').keyup(function(event) {
                // skip for arrow keys
                if (event.which >= 37 && event.which <= 40) return;
                // format number
                $(this).val(function(index, value) {
                    return value
                        .replace(/\D/g, "")
                        .replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                });
            });
            $('.jual-num').keyup(function(event) {
                // skip for arrow keys
                if (event.which >= 37 && event.which <= 40) return;
                // format number
                $(this).val(function(index, value) {
                    return value
                        .replace(/\D/g, "")
                        .replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                });
            });

            table = $('#table').DataTable({

                "processing": true,
                "serverSide": true,
                "order": [],
                "ajax": {
                    "url": base_url + "barang/barang_list",
                    "type": "POST"
                },
                "columnDefs": [{
                        "targets": [-1],
                        "orderable": false,
                    },
                    {
                        "targets": [-2],
                        "orderable": false,
                    },
                ],

            });


            $('.datepicker').datepicker({
                autoclose: true,
                format: "yyyy-mm-dd",
                todayHighlight: true,
                orientation: "top auto",
                todayBtn: true,
                todayHighlight: true,
            });


            $("input").change(function() {
                $(this).parent().parent().removeClass('has-error');
                $(this).next().empty();
            });
            $("textarea").change(function() {
                $(this).parent().parent().removeClass('has-error');
                $(this).next().empty();
            });
            $("select").change(function() {
                $(this).parent().parent().removeClass('has-error');
                $(this).next().empty();
            });

        });



        function tambah_barang() {
            tipe_simpan = 'add';
            $('#form')[0].reset();
            $('.form-group').removeClass('has-error');
            $('.help-block').empty();
            $('#modal_form').modal('show');
            $('.modal-title').text('Add Barang');

            $('#photo-preview').hide();

            $('#label-photo').text('Upload Photo');
        }

        function edit_barang(id) {
            tipe_simpan = 'update';
            $('#form')[0].reset();
            $('.form-group').removeClass('has-error');
            $('.help-block').empty();



            $.ajax({
                url: base_url + "barang/barang_edit/" + id,
                type: "GET",
                dataType: "json",
                success: function(data) {

                    $('[name="id"]').val(data.id);
                    $('[name="nama_barang"]').val(data.nama_barang);
                    $('[name="harga_beli"]').val(data.harga_beli);
                    $('[name="harga_jual"]').val(data.harga_jual);
                    $('[name="stok"]').val(data.stok);
                    $('#modal_form').modal('show');
                    $('.modal-title').text('Edit Barang');

                    $('#photo-preview').show();

                    if (data.foto_barang) {
                        $('#label-photo').text('Change Photo');
                        $('#photo-preview div').html('<img src="' + base_url + 'assets/upload/' + data.foto_barang + '" class="img-responsive">');
                        $('#photo-preview div').append('<input type="checkbox" name="hapus_foto_barang" value="' + data.foto_barang + '"/> Remove photo when saving');

                    } else {
                        $('#label-photo').text('Upload Photo');
                        $('#photo-preview div').text('(No photo)');
                    }


                },
                error: function(jqXHR, textStatus, errorThrown) {
                    alert('Error get data from ajax');
                }
            });
        }

        function refresh_table() {
            table.ajax.reload(null, false);
        }

        function simpan() {
            $('#btnSave').text('saving...');
            $('#btnSave').attr('disabled', true);
            var url;

            if (tipe_simpan == 'add') {
                url = base_url + "barang/barang_tambah";
            } else {
                url = base_url + "barang/barang_update";
            }


            var formData = new FormData($('#form')[0]);
            $.ajax({
                url: url,
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                dataType: "json",
                success: function(data) {

                    if (data.status) {
                        $('#modal_form').modal('hide');
                        refresh_table();
                    } else {
                        // console.log(data.inputerror);
                        for (var i = 0; i < data.inputerror.length; i++) {
                            $('[name="' + data.inputerror[i] + '"]').parent().parent().addClass('has-error');
                            $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]);
                        }
                    }
                    $('#btnSave').text('save');
                    $('#btnSave').attr('disabled', false);


                },
                error: function(jqXHR, textStatus, errorThrown) {
                    alert('Error tambah/update data');
                    $('#btnSave').text('save');
                    $('#btnSave').attr('disabled', false);

                }
            });
        }

        function hapus_barang(id) {
            if (confirm('Apa kamu yakin akan hapus data ini?')) {
                $.ajax({
                    url: base_url + "barang/barang_hapus/" + id,
                    type: "POST",
                    dataType: "json",
                    success: function(data) {
                        $('#modal_form').modal('hide');
                        refresh_table();
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        alert('Error hapus data');
                    }
                });

            }
        }
    </script>


    <div class="modal fade" id="modal_form" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Form Barang</h3>
                </div>
                <div class="modal-body form">
                    <form action="#" id="form" class="form-horizontal">
                        <input type="hidden" value="" name="id" />
                        <div class="form-body">
                            <div class="form-group">
                                <label class="control-label col-md-3">Nama Barang</label>
                                <div class="col-md-9">
                                    <input name="nama_barang" placeholder="Nama Barang" class="form-control" type="text">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">Harga Beli</label>
                                <div class="col-md-9">
                                    <input name="harga_beli" placeholder="Harga Beli" class="form-control beli-num" type="text">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">Harga Jual</label>
                                <div class="col-md-9">
                                    <input name="harga_jual" placeholder="Harga Jual" class="form-control jual-num" type="text">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">Stok</label>
                                <div class="col-md-9">
                                    <input name="stok" placeholder="Stok" class="form-control" type="number"></input>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group" id="photo-preview">
                                <label class="control-label col-md-3">Foto Barang</label>
                                <div class="col-md-9">
                                    (No photo)
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3" id="label-photo">Upload Photo </label>
                                <div class="col-md-9">
                                    <input name="foto_barang" type="file">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btnSave" onclick="simpan()" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

</body>

</html>