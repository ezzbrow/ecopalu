<?php

namespace App\Controllers;

use App\Models\KategoriSampahModel;

class Home extends BaseController
{
    public function index()
    {
        $model = new KategoriSampahModel();

        $data = [
            'kategori' => $model->findAll()
        ];

        return view('home', $data);
    }
}