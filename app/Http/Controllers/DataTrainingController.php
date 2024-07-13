<?php

namespace App\Http\Controllers;

use App\Models\Training;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\Console\Output\ConsoleOutput;

class DataTrainingController extends Controller
{
    private $trainingData;
    private $count_laku;
    private $count_kurang_laku;
    private $count_tidak_laku;
    private $p_output_laku;
    private $p_output_kurang_laku;
    private $p_output_tidak_laku;
    private $p_nama_produk;
    private $p_ukuran;
    private $p_stok;
    private $predict_result;


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $title = 'Data Training';
        $data = Training::all();

        return view('pages.data-training.index', compact('title', 'data'));
    }

    public function import(Request $request)
    {
        $this->validate($request, [
            'file' => 'required',
        ]);

        try {
            if ($request->hasFile('file')) {
                DB::table('data_training')->truncate();

                Excel::import(new \App\Imports\TrainingImport, $request->file('file'));

                return redirect('/data-training')->with('success', 'Data berhasil diimport');
            }

            return back()->with('error', 'Data gagal diimport');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    public function proses()
    {
        $data = DB::table('data_training')->get()->toArray();
        $data_ukuran = DB::table('data_training')
            ->distinct()
            ->pluck('ukuran')
            ->sortDesc()
            ->values()
            ->toArray();

        $data_stok = DB::table('data_training')
            ->distinct()
            ->pluck('stok_produk')
            ->sortDesc()
            ->values()
            ->toArray();


        $this->trainingData = DB::table('data_training')->count();
        $this->count_laku = DB::table('data_training')->where('output', 'laku')->count();
        $this->count_kurang_laku = DB::table('data_training')->where('output', 'kurang laku')->count();
        $this->count_tidak_laku = DB::table('data_training')->where('output', 'tidak laku')->count();

        $this->p_output_laku = floor($this->count_laku / $this->trainingData * 100);
        $this->p_output_kurang_laku = floor($this->count_kurang_laku / $this->trainingData * 100);
        $this->p_output_tidak_laku = floor($this->count_tidak_laku / $this->trainingData * 100);
        $produk_prior = $this->getPiorProduk($data);;
        $ukuran_prior = $this->getPiorUkuran($data_ukuran);
        $stok_prior   = $this->getPiorStok($data_stok);

        $predict = $this->getPredict();

        dd($predict);
    }

    public function getPiorProduk($data)
    {
        // dd($data);
        foreach ($data as $key => $value) {
            $this->p_nama_produk[$key]['nama_produk'] = $value->nama_produk;
            // dd($value->nama_produk === 'aqua 1500');
            $laku = DB::table('data_training')->where('nama_produk', $value->nama_produk)->where('output', 'laku')->count();

            $kurang_laku = DB::table('data_training')->where('output', 'kurang laku')->where('nama_produk', $value->nama_produk)->count();

            $tidak_laku = DB::table('data_training')->where('output', 'tidak laku')->where('nama_produk', $value->nama_produk)->count();
            $this->p_nama_produk[$key]['laku'] = ceil(($laku / $this->count_laku) * 100);
            $this->p_nama_produk[$key]['kurang laku'] = ceil(($kurang_laku / $this->count_kurang_laku) * 100);
            $this->p_nama_produk[$key]['tidak laku'] = ceil(($tidak_laku / $this->count_tidak_laku) * 100);
        }

        // dd($this->p_nama_produk, $percent);

        return $this->p_nama_produk;
    }

    public function getPiorUkuran($data)
    {

        foreach ($data as $keys => $value) {
            $this->p_ukuran[$keys]['ukuran'] = $value;
            $laku = DB::table('data_training')->where('output', 'laku')->where('ukuran', $value)->count();


            $kurang_laku = DB::table('data_training')->where('output', 'kurang laku')->where('ukuran', $value)->count();


            $tidak_laku = DB::table('data_training')->where('output', 'tidak laku')->where('ukuran', $value)->count();

            $this->p_ukuran[$keys]['laku'] = ceil($laku / $this->count_laku * 100);
            $this->p_ukuran[$keys]['kurang laku'] = ceil($kurang_laku / $this->count_kurang_laku * 100);
            $this->p_ukuran[$keys]['tidak laku'] = ceil($tidak_laku / $this->count_tidak_laku * 100);
        }

        return $this->p_ukuran;
    }

    public function getPiorStok($data)
    {

        foreach ($data as $key => $value) {
            $this->p_stok[$key]['stok'] = $value;
            $laku = DB::table('data_training')->where('output', 'laku')->where('stok_produk', $value)->count();


            $kurang_laku = DB::table('data_training')->where('output', 'kurang laku')->where('stok_produk', $value)->count();


            $tidak_laku = DB::table('data_training')->where('output', 'tidak laku')->where('stok_produk', $value)->count();

            $this->p_stok[$key]['laku'] = ceil($laku / $this->count_laku * 100);
            $this->p_stok[$key]['kurang laku'] = ceil($kurang_laku / $this->count_kurang_laku * 100);
            $this->p_stok[$key]['tidak laku'] = ceil($tidak_laku / $this->count_tidak_laku * 100);
        }

        return $this->p_stok;
    }

    public function getPredict()
    {
        $predict = [
            ['nama' => 'aqua 1500', 'ukuran' => '1500', 'stok_product' => 'normal', 'output' => 'laku'],
            ['nama' => 'aqua cube', 'ukuran' => '220', 'stok_product' => 'normal', 'output' => 'kurang laku'],
            ['nama' => 'vit 500', 'ukuran' => '550', 'stok_product' => 'tinggi', 'output' => 'laku'],
            ['nama' => 'sido c 100', 'ukuran' => '150', 'stok_product' => 'normal', 'output' => 'kurang laku'],
            ['nama' => 'sido beras kencur', 'ukuran' => '150', 'stok_product' => 'rendah', 'output' => 'tidak laku'],
        ];
        $table_produk   = $this->p_nama_produk;
        $table_ukuran   = $this->p_ukuran;
        $table_stok     = $this->p_stok;
        $produk = [];
        $ukuran = [];
        $stok = [];

        // dd($table_produk, $table_ukuran, $table_stok);
        // dd($table_produk);
        foreach ($predict as $key => $value) {
            $this->predict_result[$key]['nama'] = $value['nama'];
            $this->predict_result[$key]['ukuran'] = $value['ukuran'];
            $this->predict_result[$key]['stok_product'] = $value['stok_product'];
            $this->predict_result[$key]['output'] = $value['output'];

            $filter_produk = array_filter($table_produk, function ($item) use ($key) {
                return $item['nama_produk'] == $this->predict_result[$key]['nama'];
            });
            $filter_ukuran = array_filter($table_ukuran, function ($item) use ($key) {
                return $item['ukuran'] == $this->predict_result[$key]['ukuran'];
            });

            $filter_stok = array_filter($table_stok, function ($item) use ($key) {
                return $item['stok'] == $this->predict_result[$key]['stok_product'];
            });
            $reset_produk = reset($filter_produk);
            $reset_ukuran = reset($filter_ukuran);
            $reset_stok = reset($filter_stok);
            $produk[$key]['nama'] = $reset_produk;
            $produk[$key]['ukuran'] = $reset_ukuran;
            $produk[$key]['stok'] = $reset_stok;
            $produk[$key]['probabilitas'] = $value['output'] === 'laku' ? $this->p_output_laku : ($value['output'] === 'kurang laku' ? $this->p_output_kurang_laku : $this->p_output_tidak_laku);

            $this->predict_result[$key]['laku'] = floor($reset_produk['laku'] * $reset_ukuran['laku'] * $reset_stok['laku'] * $this->p_output_laku);

            $this->predict_result[$key]['kurang laku'] = floor($reset_produk['kurang laku'] * $reset_ukuran['kurang laku'] * $reset_stok['kurang laku'] * $this->p_output_kurang_laku);

            $this->predict_result[$key]['tidak laku'] = floor($reset_produk['tidak laku'] * $reset_ukuran['tidak laku'] * $reset_stok['tidak laku'] * $this->p_output_tidak_laku);

            // echo $this->predict_result[$key]['laku'] . ' ' . $this->predict_result[$key]['kurang laku'] . ' ' . $this->predict_result[$key]['tidak laku'] . ' ' . $this->p_output_laku . PHP_EOL;
            // $this->predict[$key]['laku'] = ceil($table_produk[$key]['laku'] * $table_ukuran[$key]['laku'] * $table_stok[$key]['laku'] / 100);
        }

        dd(
            $this->predict_result,
            $this->p_output_laku,
            $this->p_output_kurang_laku,
            $this->p_output_tidak_laku,
            $produk,
            $ukuran,
            $stok
        );
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Training $training)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Training $training)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Training $training)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Training $training)
    {
        //
    }
}
