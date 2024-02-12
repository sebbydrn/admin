@extends('layouts.index')

@section('content')
    {{-- Content Header (Page header) --}}
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Contents</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{url('contents')}}">Contents List</a></li>
                        <li class="breadcrumb-item active">View Content</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    {{-- End of content header --}}

    {{-- Main Content --}}
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Content</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th style="width: 30%;">Page</th>
                                        <td style="width: 70%;">{{$data->page_name}}</td>
                                    </tr>
                                    <tr>
                                        <th>Section</th>
                                        <td>{{$data->section_name}}</td>
                                    </tr>
                                    <tr>
                                        <th>Subtitle</th>
                                        <td>{{$data->subtitle}}</td>
                                    </tr>
                                    <tr>
                                        <th>Content</th>
                                        <td>{{$data->content}}</td>
                                    </tr>
                                    <tr>
                                        <th>Image</th>
                                        <td>
                                            @if($data->image)
                                                @if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
                                                    <img src="{{'https://'.$_SERVER['SERVER_NAME'].'/portal/public/uploads/'.$data->image}}" style="height: 250px;">
                                                @else
                                                    <img src="{{'http://'.$_SERVER['SERVER_NAME'].'/portal/public/uploads/'.$data->image}}" style="height: 250px;">
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Date Created</th>
                                        @if($date_created)
                                            <td>{{date('Y-m-d h:i:s a', strtotime($date_created->timestamp))}}</td>
                                        @else
                                            <td></td>
                                        @endif
                                    </tr>
                                    <tr>
                                        <th>Last Update</th>
                                        @if($date_updated)
                                            <td>{{date('Y-m-d h:i:s a', strtotime($date_updated->timestamp))}}</td>
                                        @else
                                            <td></td>
                                        @endif
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    {{-- End of main content --}}
@endsection
