<?php

namespace App\Controllers;

use App\Models\TransaksiCoinModel;

class TransaksiCoinController extends BaseController
{
    protected $transaksiModel;

    public function __construct()
    {
        $this->transaksiModel = new TransaksiCoinModel();
    }

    public function index()
    {
        $data['transaksi'] = $this->transaksiModel->findAll();
        return view('transaksi_coin/index', $data);
    }

    public function create()
    {
        return view('transaksi_coin/create');
    }

    public function store()
    {
        $this->transaksiModel->save([
            'user_id'       => $this->request->getPost('user_id'),
            'coin_masuk'    => $this->request->getPost('coin_masuk'),
            'coin_keluar'   => $this->request->getPost('coin_keluar'),
            'saldo_akhir'   => $this->request->getPost('saldo_akhir'),
            'keterangan'    => $this->request->getPost('keterangan')
        ]);

        return redirect()->to('/transaksi-coin');
    }

    public function edit($id)
    {
        $data['transaksi'] = $this->transaksiModel->find($id);
        return view('transaksi_coin/edit', $data);
    }

    public function update($id)
    {
        $this->transaksiModel->update($id, [
            'coin_masuk'  => $this->request->getPost('coin_masuk'),
            'coin_keluar' => $this->request->getPost('coin_keluar'),
            'saldo_akhir' => $this->request->getPost('saldo_akhir'),
            'keterangan'  => $this->request->getPost('keterangan')
        ]);

        return redirect()->to('/transaksi-coin');
    }

    public function delete($id)
    {
        $this->transaksiModel->delete($id);
        return redirect()->to('/transaksi-coin');
    }
}