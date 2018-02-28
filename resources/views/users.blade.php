@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    <table class="table table-striped">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Имя</th>
                                <th scope="col">Почта</th>
                                <th scope="col">Элементы управления</th>
                            </tr>
                        </thead>
                        
                        <tbody>
                        @for ($i = 0; $i < count($users); $i++)
                            <tr>
                                <th scope="row">{{ $i + 1 }}</th>
                                <th>{{ $users[$i]->getPropertyValue("Name") }}</th>
                                <th>{{ $users[$i]->getPropertyValue("Email") }}</th>
                                <th><a href="/users/delete?email={{ $users[$i]->getPropertyValue('Email') }}">Удалить</a></th>
                            </tr>
                        @endfor
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
