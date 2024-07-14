<?php

namespace App\Http\Controllers;

use App\Models\Prediksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class PrediksiController extends Controller
{
    public $predict_result;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $title = 'Data Prediksi';
        $data = Prediksi::all();
        return view('pages.prediksi.index', compact('title', 'data'));
    }

    public function import(Request $request)
    {
        $this->validate($request, [
            'file' => 'required',
        ]);

        try {
            if ($request->hasFile('file')) {
                DB::table('prediksi')->truncate();

                Excel::import(new \App\Imports\PrediksiImport, $request->file('file'));

                return redirect()->route('prediksi')->with('success', 'Data berhasil diimport');
            }

            return back()->with('error', 'Data gagal diimport');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }
    public function proses()
    {
        $title = 'Proses Prediksi';

        $result = $this->getPredict();
        // dd($result);
        // dd($training->proses(), $table_produk, $table_ukuran, $table_stok);
        return view('pages.prediksi.proses', compact('title', 'result'));
    }
    public function getPredict()
    {
        $training = new DataTrainingController();
        $training->proses();

        $table_produk   = $training->p_nama_produk;
        $table_ukuran   = $training->p_ukuran;
        $table_stok     = $training->p_stok;

        $predict = DB::table('prediksi')->get()->toArray();

        foreach ($predict as $key => $value) {
            $this->predict_result[$key]['nama'] = $value->nama_produk;
            $this->predict_result[$key]['ukuran'] = $value->ukuran;
            $this->predict_result[$key]['stok_produk'] = $value->stok_produk;
            $this->predict_result[$key]['output'] = $value->output;

            $filter_produk = array_filter($table_produk, function ($item) use ($key) {
                return $item['nama_produk'] == $this->predict_result[$key]['nama'];
            });
            $filter_ukuran = array_filter($table_ukuran, function ($item) use ($key) {
                return $item['ukuran'] == $this->predict_result[$key]['ukuran'];
            });

            $filter_stok = array_filter($table_stok, function ($item) use ($key) {
                return $item['stok'] == $this->predict_result[$key]['stok_produk'];
            });
            $reset_produk = reset($filter_produk);
            $reset_ukuran = reset($filter_ukuran);
            $reset_stok = reset($filter_stok);

            // $produk[$key]['nama'] = $reset_produk;
            // $produk[$key]['ukuran'] = $reset_ukuran;
            // $produk[$key]['stok'] = $reset_stok;
            // $produk[$key]['probabilitas'] = $value['output'] === 'laku' ? $this->p_output_laku : ($value['output'] === 'kurang laku' ? $this->p_output_kurang_laku : $this->p_output_tidak_laku);

            $this->predict_result[$key]['laku'] = $reset_produk['laku'] * $reset_ukuran['laku'] * $reset_stok['laku'] * $training->p_output_laku;
            $this->predict_result[$key]['kurang laku'] = $reset_produk['kurang laku'] * $reset_ukuran['kurang laku'] * $reset_stok['kurang laku'] * $training->p_output_kurang_laku;
            $this->predict_result[$key]['tidak laku'] = $reset_produk['tidak laku'] * $reset_ukuran['tidak laku'] * $reset_stok['tidak laku'] * $training->p_output_tidak_laku;

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
        // dd($this->predict_result);

        $accuracy = $this->getMatrixConfusion($this->predict_result);

        return [$training->proses(), $this->predict_result, $accuracy];
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
    public function show(Prediksi $prediksi)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Prediksi $prediksi)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Prediksi $prediksi)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Prediksi $prediksi)
    {
        //
    }
}
