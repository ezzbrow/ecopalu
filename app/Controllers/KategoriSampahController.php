<?php

namespace App\Controllers;

use App\Models\KategoriSampahModel;

class KategoriSampahController extends BaseController
{
    protected $kategoriModel;

    public function __construct()
    {
        $this->kategoriModel = new KategoriSampahModel();
    }

    public function index()
    {
        $data['kategori'] = $this->kategoriModel->findAll();
        return view('kategori_sampah/index', $data);
    }

    public function create()
    {
        return view('kategori_sampah/create');
    }

    public function store()
    {
        $this->kategoriModel->save([
            'nama_kategori' => $this->request->getPost('nama_kategori'),
            'coin_value'    => $this->request->getPost('coin_value')
        ]);

        return redirect()->to('/kategori-sampah');
    }

    public function edit($id)
    {
        $data['kategori'] = $this->kategoriModel->find($id);
        return view('kategori_sampah/edit', $data);
    }

    public function update($id)
    {
        $this->kategoriModel->update($id, [
            'nama_kategori' => $this->request->getPost('nama_kategori'),
            'coin_value'    => $this->request->getPost('coin_value')
        ]);

        return redirect()->to('/kategori-sampah');
    }

    public function delete($id)
    {
        $this->kategoriModel->delete($id);
        return redirect()->to('/kategori-sampah');
    }
}