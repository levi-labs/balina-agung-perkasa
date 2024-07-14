@extends('layout.main')

@section('content')
    <div class="page-inner">
        <div class="page-header"></div>
        <div class="row justify-content-center">
            <div class="col-md-6">
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
                    </div>
                    <div class="card-body">
                        <form action="{{ route('data-training-update', $training->id) }}" method="post">
                            @method('PUT')
                            @csrf
                            <div class="form-group">
                                <label for="nama_produk">Nama Produk</label>
                                <input type="text" class="form-control" id="nama_produk" name="nama_produk"
                                    value="{{ $training->nama_produk }}">
                                @error('nama_produk')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="ukuran">Ukuran</label>
                                <input type="number" class="form-control" id="ukuran" name="ukuran"
                                    value="{{ $training->ukuran }}">
                                @error('ukuran')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="stok_produk">Stok Produk</label>
                                <select class="form-select" id="stok_produk" name="stok_produk">
                                    <option selected disabled>Pilih Stok</option>
                                    <option {{ $training->stok_produk == 'tinggi' ? 'selected' : '' }} value="tinggi">Tinggi
                                    </option>
                                    <option {{ $training->stok_produk == 'normal' ? 'selected' : '' }} value="normal">Normal
                                    </option>
                                    <option {{ $training->stok_produk == 'rendah' ? 'selected' : '' }} value="rendah">
                                        Rendah</option>
                                </select>
                                @error('stok_produk')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="output">Output</label>
                                <select class="form-select" id="output" name="output">
                                    <option selected disabled>Pilih Stok</option>
                                    <option {{ $training->output == 'laku' ? 'selected' : '' }} value="laku">Laku
                                    </option>
                                    <option {{ $training->output == 'kurang laku' ? 'selected' : '' }} value="kurang laku">
                                        Kurang Laku</option>
                                    <option {{ $training->output == 'tidak laku' ? 'selected' : '' }} value="tidak laku">
                                        Tidak Laku</option>
                                </select>
                                @error('output')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-2 float-end mt-3">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
