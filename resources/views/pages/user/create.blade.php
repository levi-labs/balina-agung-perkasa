@extends('layout.main')

@section('content')
    <div class="page-inner">
        <div class="page-header">
            <h4 class="page-title">Halaman Tambah Data User</h4>
        </div>
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
                        <form action="{{ route('user-store') }}" method="post">
                            @csrf
                            <div class="form-group">
                                <label for="nama">Nama</label>
                                <input type="text" class="form-control" id="nama" name="nama">
                                @error('nama')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" class="form-control" id="username" name="username">
                                @error('username')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="level">Level User</label>
                                <select class="form-select" id="level" name="level">
                                    <option selected disabled>Pilih Level</option>
                                    <option value="superadmin">SuperAdmin</option>
                                    <option value="admin">Admin</option>
                                </select>
                                @error('level')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password" name="password">
                                @error('password')
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
