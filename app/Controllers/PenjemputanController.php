<?php

namespace App\Controllers;

use App\Models\PenjemputanModel;

class PenjemputanController extends BaseController
{
    protected $penjemputanModel;

    public function __construct()
    {
        $this->penjemputanModel = new PenjemputanModel();
    }

    public function index()
    {
        $data['penjemputan'] = $this->penjemputanModel->findAll();
        return view('penjemputan/index', $data);
    }

    public function create()
    {
        return view('penjemputan/create');
    }

    public function store()
    {
        $this->penjemputanModel->save([
            'user_id'          => $this->request->getPost('user_id'),
            'kategori_id'      => $this->request->getPost('kategori_id'),
            'berat_sampah'     => $this->request->getPost('berat_sampah'),
            'tanggal_jemput'   => $this->request->getPost('tanggal_jemput'),
            'status'           => $this->request->getPost('status')
        ]);

        return redirect()->to('/penjemputan');
    }

    public function edit($id)
    {
        $data['penjemputan'] = $this->penjemputanModel->find($id);
        return view('penjemputan/edit', $data);
    }

    public function update($id)
    {
        $this->penjemputanModel->update($id, [
            'user_id'        => $this->request->getPost('user_id'),
            'kategori_id'    => $this->request->getPost('kategori_id'),
            'berat_sampah'   => $this->request->getPost('berat_sampah'),
            'tanggal_jemput' => $this->request->getPost('tanggal_jemput'),
            'status'         => $this->request->getPost('status')
        ]);

        return redirect()->to('/penjemputan');
    }

    public function delete($id)
    {
        $this->penjemputanModel->delete($id);
        return redirect()->to('/penjemputan');
    }
}