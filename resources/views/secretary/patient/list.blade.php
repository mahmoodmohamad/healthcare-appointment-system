@extends('secretary.layout')

@section('contents')
{{-- {{dd(Route::currentRouteName())}} --}}
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">@lang('site.patients-list')</h1>
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
                        <h3 class="card-title"></h3>
                        <div class="float-left">
                            <a class="btn btn-primary " href="{{ route('secretary.patient.edit') }}
                            "> @lang('site.add-patient')</a>
                        </div>
                        <form method="get" class="search-form" role="search" action="{{ route('secretary.patient.list') }}" accept-charset="UTF-8">
                            <div class="row">
                              <div class="col-xs-8 col-sm-8 col-md-8">
                                <div class="form-group">
                                  <input id="userId" type="search" class="form-control" name="search"
                                    value="{{old('search') ?? request('search') }}" placeholder="@lang('site.by-name-national-id')">
                                </div>
                              </div>
                              <div class="col-xs-2 col-sm-2 col-md-2">
                                <div class="form-group">
                                  <button type="submit" class="btn btn-default">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                      stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                      <circle cx="11" cy="11" r="8" />
                                      <line x1="21" y1="21" x2="16.65" y2="16.65" />
                                    </svg>
                                  </button>
                                </div>
                              </div>
                            </div>
                          </form>

                    </div>
                    <div class="card-body ">
                        <table  class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th> @lang('site.patient_name')</th>
                                    <th> @lang('site.age')</th>
                                    <th>@lang('site.secretary')</th>
                                    <th>@lang('site.actions')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($patients as $patient)
                                <tr>
                                    <td><a href="{{ route('secretary.patient.details',$patient->id) }}">{{$patient->name}}</a></td>
                                    <td>{{$patient->age}}</td> 
                                    <td>{{ json_decode($patient->secretary->user)->name_ar }}</td>


                                    <td>
                                        <a class="btn btn-xs btn-primary"
                                        href="{{ route('secretary.patient.details',$patient->id) }}
                                        ">@lang('site.details')</a>
                                        <a class="btn btn-xs btn-primary"
                                        href="{{ route('secretary.patient.edit',$patient->id) }}
                                        ">@lang('site.edit')</a>
                                        <a class="btn btn-xs btn-primary"
                                        href="{{ route('secretary.patient.clinic.edit',$patient->id) }}
                                        ">@lang('site.add-to-clinic')</a>


{{--     <form class="btn btn-xs" action="{{ route('admin.secretary.destroy',$patient->user) }}"
                                         method="POST">@csrf @method('DELETE')
                                            <button type="submit" class="btn  btn-xs btn-danger" onclick="return confirm('Are You Sure ?')">Delete</button>
                                        </form> --}}

                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex align-items-center justify-content-between flex-wrap">
                        <p class="mb-0">
                          @lang('site.showing') {{min($patients->perPage(), $patients->total())}}،@lang('site.of'){{$patients->total()}}
                        </p>
                        <nav>
                          {{ $patients->appends(request()->query())->links() }}
                        </nav>
                      </div>
                </div>
            </div>
        </div>
</section>
@endsection
