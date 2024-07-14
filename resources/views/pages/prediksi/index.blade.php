@extends('layout.main')

@section('content')
    <div class="page-inner">
        <div class="page-header">
            <h4 class="page-title">Halaman Data Prediksi</h4>
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
                        <div class="row justify-content-end">
                            <div class="col-md-8">
                                <a href="{{ route('prediksi-create') }}" class="btn btn-secondary mt-2">Tambah</a>
                                <a href="{{ route('prediksi-proses') }}" class="btn btn-info mt-2">Proses</a>
                            </div>
                            <div class="col-md-4">
                                <form action="{{ route('prediksi-import') }}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group">
                                        <input type="file" class="form-control-file" id="exampleFormControlFile1"
                                            name="file">

                                        <button type="submit" class="btn btn-secondary btn-sm d-inline">import</button>
                                        @error('file')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </form>
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
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $item)
                                    <tr>
                                        <th scope="row">{{ $loop->iteration }}</th>
                                        <td>{{ $item->nama_produk }}</td>
                                        <td>{{ $item->ukuran }}</td>
                                        <td>{{ $item->stok_produk }}</td>
                                        <td>{{ $item->output }}</td>
                                        <td>
                                            <a href="{{ route('prediksi-edit', $item->id) }}"
                                                class="btn btn-primary btn-sm">Edit</a>
                                            <form action="{{ route('prediksi-destroy', $item->id) }}" method="post"
                                                class="d-inline">
                                                @method('DELETE')
                                                @csrf
                                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                            </form>

                                        </td>
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
