@extends('layout.main')

@section('content')
    <div class="page-inner">
        <div class="page-header">
            <h4 class="page-title">Data Training</h4>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">{{ $title }}</div>
                        @if (session()->has('success'))
                            <div class="alert alert-success bg-success">
                                <span class="text-white">{{ session('success') }}</span>
                            </div>
                        @endif
                        @if (session()->has('error'))
                            <div class="alert alert-danger bg-danger">
                                <span class="text-white">{{ session('error') }}</span>
                            </div>
                        @endif
                        <div class="row justify-content-start">
                            <div class="col-md-8">
                                <a href="{{ route('data-training') }}" class="btn btn-dark mt-2">Kembali</a>

                            </div>

                        </div>

                    </div>
                    <div class="card-body">
                        <table class="table table-hover table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Nama Produk</th>
                                    <th scope="col">Ukuran</th>
                                    <th scope="col">Stok Produk</th>
                                    <th scope="col">Output</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($result[0][0] as $item)
                                    <tr>
                                        <th scope="row">{{ $loop->iteration }}</th>
                                        <td>{{ $item->nama_produk }}</td>
                                        <td>{{ $item->ukuran }}</td>
                                        <td>{{ $item->stok_produk }}</td>
                                        <td>{{ $item->output }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Probability Produk</div>

                    </div>
                    <div class="card-body">
                        <table class="table table-hover table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Nama Produk</th>
                                    <th scope="col">Laku</th>
                                    <th scope="col">Kurang Laku</th>
                                    <th scope="col">Tidak Laku</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- @php
                                    dd($produk_prior);
                                @endphp --}}
                                @foreach ($result[0][1] as $item)
                                    <tr>
                                        <th scope="row">{{ $loop->iteration }}</th>
                                        <td>{{ $item['nama_produk'] }}</td>
                                        <td>{{ $item['laku'] }}</td>
                                        <td>{{ $item['kurang laku'] }}</td>
                                        <td>{{ $item['tidak laku'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Probability Ukuran</div>
                    </div>
                    <div class="card-body">
                        <table class="table table-hover table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Jenis Ukuran</th>
                                    <th scope="col">Laku</th>
                                    <th scope="col">Kurang Laku</th>
                                    <th scope="col">Tidak Laku</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($result[0][2] as $item)
                                    <tr>
                                        <th scope="row">{{ $loop->iteration }}</th>
                                        <td>{{ $item['ukuran'] }}</td>
                                        <td>{{ $item['laku'] }}</td>
                                        <td>{{ $item['kurang laku'] }}</td>
                                        <td>{{ $item['tidak laku'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Probability Stok</div>

                    </div>
                    <div class="card-body">
                        <table class="table table-hover table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col">Stok</th>
                                    <th scope="col">Laku</th>
                                    <th scope="col">Kurang Laku</th>
                                    <th scope="col">Tidak Laku</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- @php
                                    dd($ukuran_prior);
                                @endphp --}}
                                @foreach ($result[0][3] as $item)
                                    <tr>
                                        <td>{{ $item['stok'] }}</td>
                                        <td>{{ $item['laku'] }}</td>
                                        <td>{{ $item['kurang laku'] }}</td>
                                        <td>{{ $item['tidak laku'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Hasil Prediksi</div>
                    </div>
                    <div class="card-body">
                        <h5>Hasil Akurasi : {{ $result[2] . ' %' }}</h5>
                        <table class="table table-hover table-bordered table-responsive">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Nama Ukuran</th>
                                    <th scope="col">Ukuran</th>
                                    <th scope="col">Stok Product</th>
                                    <th scope="col">Output</th>
                                    <th scope="col">Prediksi</th>
                                    <th scope="col">Laku</th>
                                    <th scope="col">Kurang Laku</th>
                                    <th scope="col">Tidak Laku</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($result[1] as $item)
                                    <tr>
                                        <th scope="row">{{ $loop->iteration }}</th>
                                        <td>{{ $item['nama'] }}</td>
                                        <td>{{ $item['ukuran'] }}</td>
                                        <td>{{ $item['stok_produk'] }}</td>
                                        <td>{{ $item['output'] }}</td>
                                        <td>{{ $item['prediksi'] }}</td>
                                        <td>{{ number_format($item['laku'], 4) }}</td>
                                        <td>{{ number_format($item['kurang laku'], 4) }}</td>
                                        <td>{{ number_format($item['tidak laku'], 4) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
