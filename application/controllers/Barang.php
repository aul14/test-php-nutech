<?php


defined('BASEPATH') or exit('No direct script access allowed');

class Barang extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('barang_model', 'barang');
    }
    public function index()
    {
        $this->load->view('barang');
    }

    public function barang_list()
    {
        $this->load->helper('url');

        $list = $this->barang->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $barang) {
            $no++;
            $row = array();
            $row[] = $barang->nama_barang;
            $row[] = $barang->harga_beli;
            $row[] = $barang->harga_jual;
            $row[] = $barang->stok;
            if ($barang->foto_barang)
                $row[] = '<a href="' . base_url('upload/' . $barang->foto_barang) . '" target="_blank"><img src="' . base_url('assets/upload/' . $barang->foto_barang) . '" class="img-responsive" /></a>';
            else
                $row[] = '(No Foto Barang)';

            //add html for action
            $row[] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit" onclick="edit_barang(' . "'" . $barang->id . "'" . ')"><i class="glyphicon glyphicon-pencil"></i> Edit</a>
				  <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Hapus" onclick="hapus_barang(' . "'" . $barang->id . "'" . ')"><i class="glyphicon glyphicon-trash"></i> Delete</a>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->barang->count_all(),
            "recordsFiltered" => $this->barang->count_filtered(),
            "data" => $data,
        );

        echo json_encode($output);
        exit;
    }

    public function barang_edit($id)
    {
        $data = $this->barang->get_by_id($id);

        echo json_encode($data);
        exit;
    }

    public function barang_tambah()
    {
        $this->_validasi();

        $data = array(
            'nama_barang' => $this->input->post('nama_barang'),
            'harga_beli' => $this->input->post('harga_beli'),
            'harga_jual' => $this->input->post('harga_jual'),
            'stok' => $this->input->post('stok'),
        );

        if (!empty($_FILES['foto_barang']['name'])) {
            $upload = $this->_do_upload();
            $data['foto_barang'] = $upload;
        }

        $this->barang->save($data);

        echo json_encode(array("status" => TRUE));
        exit;
    }

    public function barang_update()
    {
        $this->_validasi();
        $data = array(
            'nama_barang' => $this->input->post('nama_barang'),
            'harga_beli' => $this->input->post('harga_beli'),
            'harga_jual' => $this->input->post('harga_jual'),
            'stok' => $this->input->post('stok'),
        );

        if ($this->input->post('hapus_foto_barang')) {
            if (file_exists('assets/upload/' . $this->input->post('hapus_foto_barang')) && $this->input->post('hapus_foto_barang'))
                unlink('assets/upload/' . $this->input->post('hapus_foto_barang'));
            $data['foto_barang'] = '';
        }

        if (!empty($_FILES['foto_barang']['name'])) {
            $upload = $this->_do_upload();


            $barang = $this->barang->get_by_id($this->input->post('id'));
            if (file_exists('assets/upload/' . $barang->foto_barang) && $barang->foto_barang)
                unlink('assets/upload/' . $barang->foto_barang);

            $data['foto_barang'] = $upload;
        }

        $this->barang->update(array('id' => $this->input->post('id')), $data);
        echo json_encode(array("status" => TRUE));
        exit;
    }

    public function barang_hapus($id)
    {
        //delete file
        $barang = $this->barang->get_by_id($id);
        if (file_exists('assets/upload/' . $barang->foto_barang) && $barang->foto_barang)
            unlink('assets/upload/' . $barang->foto_barang);

        $this->barang->delete_by_id($id);
        echo json_encode(array("status" => TRUE));
        exit;
    }

    private function _do_upload()
    {
        $config['upload_path']          = 'assets/upload/';
        $config['allowed_types']        = 'gif|jpg|png|jpeg';
        $config['max_size']             = 2000;
        $config['file_name']            = round(microtime(true) * 1000);

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('foto_barang')) {
            $data['inputerror'][] = 'foto_barang';
            $data['error_string'][] = 'Upload error: ' . $this->upload->display_errors('', '');
            $data['status'] = FALSE;
            echo json_encode($data);
            exit;
        }
        return $this->upload->data('file_name');
    }

    private function _validasi()
    {
        $data = array();
        $data['error_string'] = array();
        $data['inputerror'] = array();
        $data['status'] = TRUE;

        if ($this->input->post('nama_barang') == '') {
            $data['inputerror'][] = 'nama_barang';
            $data['error_string'][] = 'Nama barang is required';
            $data['status'] = FALSE;
        }

        if ($this->input->post('harga_beli') == '') {
            $data['inputerror'][] = 'harga_beli';
            $data['error_string'][] = 'Harga beli is required';
            $data['status'] = FALSE;
        }

        if ($this->input->post('harga_jual') == '') {
            $data['inputerror'][] = 'harga_jual';
            $data['error_string'][] = 'Harga jual is required';
            $data['status'] = FALSE;
        }

        if ($this->input->post('stok') == '') {
            $data['inputerror'][] = 'stok';
            $data['error_string'][] = 'Stok is required';
            $data['status'] = FALSE;
        }


        if ($data['status'] === FALSE) {
            echo json_encode($data);
            exit;
        }
    }
}

/* End of file Test.php */