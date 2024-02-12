@extends('layouts.index')

@section('content')
    {{-- Content Header (Page header) --}}
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Inquiries</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{url('inquiries')}}">Inquiries List</a></li>
                        <li class="breadcrumb-item active">View Inquiry</li>
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
                <div class="col-md-6">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Inquiry</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th style="width: 30%;">Sender</th>
                                        <td style="width: 70%;">{{$data['sender']}}</td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td>{{$data['email']}}</td>
                                    </tr>
                                    <tr>
                                        <th>Inquiry</th>
                                        <td>{{$data['inquiry']}}</td>
                                    </tr>
                                    <tr>
                                        <th>Date Inquiry Sent</th>
                                        @if($date_created)
                                            <td>{{date('Y-m-d h:i:s a', strtotime($date_created->timestamp))}}</td>
                                        @else
                                            <td></td>
                                        @endif
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Response</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th style="width: 30%;">Title</th>
                                        <td style="width: 70%;">{{$data['response_title']}}</td>
                                    </tr>
                                    <tr>
                                        <th>Body</th>
                                        <td>{!!htmlspecialchars_decode($data['response_body'])!!}</td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td>{{$data['response_email']}}</td>
                                    </tr>
                                    <tr>
                                        <th>Date Responded</th>
                                        @if($data['response_timestamp'])
                                            <td>{{date('Y-m-d h:i:s a', strtotime($data['response_timestamp']))}}</td>
                                        @else
                                            <td></td>
                                        @endif
                                    </tr>
                                </tbody>
                            </table>

                            @if($data['response_body'] == "")
                                <a href="{{route('inquiries.create_response', $data['inquiry_id'])}}" class="btn btn-primary mt-4 float-right" title="Respond"><i class="fa fa-edit"></i> Respond</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    {{-- End of main content --}}
@endsection
