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
    public $count_laku;
    public $count_kurang_laku;
    public $count_tidak_laku;
    public $p_output_laku;
    public $p_output_kurang_laku;
    public $p_output_tidak_laku;
    public $p_nama_produk;
    public $p_ukuran;
    public $p_stok;
    public $predict_result;

    // public function __construct()
    // {
    //     $this->proses();
    // }

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

    public function showProses()
    {
        $title = 'Proses Training';
        $result = $this->proses();

        // dd($result);
        return view('pages.data-training.proses', compact(
            'title',
            'result'
        ));
    }
    public function proses()
    {
        $title = 'Proses Training';
        try {
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

            $this->p_output_laku = $this->count_laku / $this->trainingData;
            $this->p_output_kurang_laku = $this->count_kurang_laku / $this->trainingData;
            $this->p_output_tidak_laku = $this->count_tidak_laku / $this->trainingData;

            $produk_prior = $this->getPiorProduk($data);;
            $ukuran_prior = $this->getPiorUkuran($data_ukuran);
            $stok_prior   = $this->getPiorStok($data_stok);

            $predict = $this->getPredict();
            // dd($data, $produk_prior, $ukuran_prior, $stok_prior, $predict);

            return [$data, $produk_prior, $ukuran_prior, $stok_prior];
            // return view('pages.data-training.proses', compact(
            //     'title',
            //     'data',
            //     'produk_prior',
            //     'ukuran_prior',
            //     'stok_prior',
            //     'predict'
            // ));
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
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
            $this->p_nama_produk[$key]['laku'] = $laku / $this->count_laku;
            $this->p_nama_produk[$key]['kurang laku'] = $kurang_laku / $this->count_kurang_laku;
            $this->p_nama_produk[$key]['tidak laku'] = $tidak_laku / $this->count_tidak_laku;
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

            $this->p_ukuran[$keys]['laku'] = $laku / $this->count_laku;
            $this->p_ukuran[$keys]['kurang laku'] = $kurang_laku / $this->count_kurang_laku;
            $this->p_ukuran[$keys]['tidak laku'] = $tidak_laku / $this->count_tidak_laku;
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

            $this->p_stok[$key]['laku'] = $laku / $this->count_laku;
            $this->p_stok[$key]['kurang laku'] = $kurang_laku / $this->count_kurang_laku;
            $this->p_stok[$key]['tidak laku'] = $tidak_laku / $this->count_tidak_laku;
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

            $this->predict_result[$key]['laku'] = $reset_produk['laku'] * $reset_ukuran['laku'] * $reset_stok['laku'] * $this->p_output_laku;
            $this->predict_result[$key]['kurang laku'] = $reset_produk['kurang laku'] * $reset_ukuran['kurang laku'] * $reset_stok['kurang laku'] * $this->p_output_kurang_laku;
            $this->predict_result[$key]['tidak laku'] = $reset_produk['tidak laku'] * $reset_ukuran['tidak laku'] * $reset_stok['tidak laku'] * $this->p_output_tidak_laku;

            if ($this->predict_result[$key]['laku'] > $this->predict_result[$key]['kurang laku'] && $this->predict_result[$key]['laku'] > $this->predict_result[$key]['tidak laku']) {
                $prediksi_hasil = 'laku';
            } elseif ($this->predict_result[$key]['kurang laku'] > $this->predict_result[$key]['laku'] && $this->predict_result[$key]['kurang laku'] > $this->predict_result[$key]['tidak laku']) {
                $prediksi_hasil = 'kurang laku';
            } elseif ($this->predict_result[$key]['tidak laku'] > $this->predict_result[$key]['laku'] && $this->predict_result[$key]['tidak laku'] > $this->predict_result[$key]['kurang laku']) {
                $prediksi_hasil = 'tidak laku';
            } else {
                $prediksi_hasil = 'tidak diketahui';
            }

            $this->predict_result[$key]['prediksi'] = $prediksi_hasil;
        }

        $accuracy = $this->getMatrixConfusion($this->predict_result);



        return [$this->predict_result, $accuracy];
    }

    public function getMatrixConfusion($data)
    {
        $truthtable = [];

        $lakulaku = 0;
        $kuranglakulaku = 0;
        $tidaklakulaku = 0;

        $lakukuranglaku = 0;
        $kuranglakukuranglaku = 0;
        $tidaklakukuranglaku = 0;

        $lakutidaklaku = 0;
        $kuranglakutidaklaku = 0;
        $tidaklakutidaklaku = 0;


        $truePositive = 0;
        $trueNegative = 0;
        $falsePositive = 0;
        $falseNegative = 0;

        foreach ($data as $key => $value) {


            if ($value['output'] === 'laku' && $value['prediksi'] === 'laku') {
                $lakulaku++;
            } elseif ($value['output'] === 'kurang laku' && $value['prediksi'] === 'laku') {
                $kuranglakulaku++;
            } elseif ($value['output'] === 'tidak laku' && $value['prediksi'] === 'laku') {
                $tidaklakulaku++;
            } elseif ($value['output'] === 'laku' && $value['prediksi'] === 'kurang laku') {
                $lakukuranglaku++;
            } elseif ($value['output'] === 'kurang laku' && $value['prediksi'] === 'kurang laku') {
                $kuranglakukuranglaku++;
            } elseif ($value['output'] === 'tidak laku' && $value['prediksi'] === 'kurang laku') {
                $tidaklakukuranglaku++;
            } elseif ($value['output'] === 'laku' && $value['prediksi'] === 'tidak laku') {
                $lakutidaklaku++;
            } elseif ($value['output'] === 'kurang laku' && $value['prediksi'] === 'tidak laku') {
                $kuranglakutidaklaku++;
            } elseif ($value['output'] === 'tidak laku' && $value['prediksi'] === 'tidak laku') {
                $tidaklakutidaklaku++;
            }
        }

        $accuracy = ($lakulaku + $kuranglakukuranglaku + $tidaklakutidaklaku)
            / ($lakulaku + $kuranglakulaku + $tidaklakulaku + $lakukuranglaku + $kuranglakukuranglaku + $tidaklakukuranglaku + $lakutidaklaku + $kuranglakutidaklaku + $tidaklakutidaklaku) * 100;


        return $accuracy;
    }

    public function numberFormat($number)
    {

        return number_format($number, 3,);
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
