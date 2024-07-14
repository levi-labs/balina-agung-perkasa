@extends('layout.main')

@section('content')
    <div class="page-inner">
        <div class="page-header">
            <h4 class="page-title">Halaman Data Users</h4>
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
                            <div class="col-md-12">
                                <a href="{{ route('user-create') }}" class="btn btn-secondary mt-2">Tambah</a>
                            </div>

                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-hover table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Nama </th>
                                    <th scope="col">Username</th>
                                    <th scope="col">Level</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $item)
                                    <tr>
                                        <th scope="row">{{ $loop->iteration }}</th>
                                        <td>{{ $item->nama }}</td>
                                        <td>{{ $item->username }}</td>
                                        <td>{{ $item->level }}</td>

                                        <td>
                                            <a href="{{ route('user-edit', $item->id) }}"
                                                class="btn btn-primary btn-sm">Edit</a>
                                            <form action="{{ route('user-destroy', $item->id) }}" method="post"
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
