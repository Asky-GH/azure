@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    <!-- @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    You are logged in! -->

                    <table class="table table-striped">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Файл</th>
                                <th scope="col">Управление</th>
                            </tr>
                        </thead>
                        
                        <tbody>
                        @for ($i = 0; $i < count($blobs); $i++)
                            <tr>
                                <th scope="row">{{ $i + 1 }}</th>
                                <th>{{ $blobs[$i]->getName() }}</th>
                                <th><a href="/home/download?name={{ $blobs[$i]->getName() }}">Скачать</a></th>
                            </tr>
                        @endfor
                        </tbody>
                    </table>

                    <form action="/home/upload" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        Выберите файл для загрузки:
                        <input type="file" name="fileToUpload" id="fileToUpload">
                        <input type="submit" value="Загрузить" name="submit">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
