@extends('secretary.layout')

@section('contents')
{{-- {{dd(Route::currentRouteName())}} --}}
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark"> ({{ $patient->name }})</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-left">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard')}} ">@lang('site.home')</a></li>
                    <li class="breadcrumb-item active">@lang('site.secretary')</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title float-left"></h2>
                        <div class="float-left">
                            <a class="btn btn-primary " href="{{ route('secretary.patient.clinic.edit', $patient->id) }}
                            ">@lang('site.add-to-clinic') </a>
                        </div>
                    </div>
                    <div class="card-body ">
                        <table class="table table-striped">
                            
                            <tbody>
                                <tr>
                                    <th scope="row">@lang('site.name')</th>
                                    <td>{{ $patient->name }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">@lang('site.age')</th>
                                    <td>{{ $patient->age }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">@lang('site.national_id')</th>
                                    <td>{{ $patient->national_id }}</td>
                                    
                                </tr>
                                <tr>
                                    <th scope="row">@lang('site.address')</th>
                                    <td>{{ $patient->address }}</td>
                                    
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title float-right">@lang('site.clinics')</h5>
                    </div>
                    
                    <div class="card-body ">
                        
                        <table  class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th> @lang('site.arabic-name')</th>
                                    <th> @lang('site.english-name')</th>
                                    <th> @lang('site.created_at')</th>
                                    
                                </tr>
                            </thead>
                            <tbody>
                                
                                @foreach ($clinicNames as $clinic)
                                <tr>
                                    <td>{{$clinic->clinic->name_ar}}</td> 
                                    <td>{{$clinic->clinic->name_en}}</td>
                                    <td>{{$clinic->created_at->toDateString()}}</td>
                                    
                                     
                                    
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex align-items-center justify-content-between flex-wrap">
                        
                        <nav>
                            
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endsection
