@extends('secretary.layout')

@section('contents')
<!-- Content Wrapper. Contains page content -->

<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                
                <h2>@lang($patient->exists ? 'site.patient-edit' : 'site.patient-create', ['name' => $patient->name])
                </h2>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-left">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">@lang('site.home')</a></li>
                    <li class="breadcrumb-item active">@lang('site.secretary')</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->
<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">@lang('site.patient-edit')</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body ">
                        <form action="{{ route('secretary.patient.save', $patient) }}" method="POST"
                        enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-6 col-sm-6 col-md-6">
                                    <div class="form-group">
                                        <strong>@lang('site.name')</strong>
                                        <input type="text" name="name" class="form-control" dir="rtl" value="{{
                                        old('name')?? $patient->name}}">
                                        @error('name')
                                        <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-6 col-sm-6 col-md-6">
                                    <div class="form-group">
                                        <strong> @lang('site.national_id')</strong>
                                        <input type="text" name="national_id" class="form-control" dir="rtl" value="{{
                                        old('national_id')?? $patient->national_id}}">
                                        @error('national_id')
                                        <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-6 col-sm-6 col-md-6">
                                    <div class="form-group">
                                        <strong>@lang('site.age')</strong>
                                        <input type="number" name="age" class="form-control" dir="rtl" value="{{
                                        old('age')?? $patient->age}}" min="1" max="100">
                                    </div>
                                </div>
                                
                                <div class="col-6 col-sm-6 col-md-6">
                                    <div class="form-group">
                                        <strong>@lang('site.phone')</strong>
                                        <input type="text" name="phone" class="form-control" dir="rtl" value="{{
                                        old('phone')?? $patient->phone }}">
                                        @error('phone')
                                        <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-6 col-sm-6 col-md-6">
                                    <div class="form-group">
                                        <strong>@lang('site.country')</strong>
                                        <select id="country" name="country_id" class="form-control">
                                            <option value="{{ $country->id }}">{{ $country->name_ar }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6 col-sm-6 col-md-6">
                                    <div class="form-group">
                                        <strong>@lang('site.city')</strong>
                                        <select name="city_id" id="city" class="form-control">
                                            @php $CityId = old('city_id') ?? $user->secretary->city_id ?? 0 @endphp
                                            @if($cities)
                                            @foreach ($cities as $id => $city)
                                            <option value="{{ $id }} " {{($patient->city_id===$id) ?'selected' : null}}>
                                                {{ $city }}
                                            </option>
                                            @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6 col-sm-6 col-md-6">
                                    <div class="form-group">
                                        <strong>@lang('site.address')</strong>
                                        <textarea name="address" class="form-control body" rows="3">{{
                                        old('address') ?? $patient->address }}</textarea>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-6 col-sm-6 col-md-6">
                                <div class="form-group">
                                    <strong>@lang('site.notes')</strong>
                                    <textarea name="notes" class="form-control body" rows="3">{{
                                    old('notes') ?? $patient->notes }}</textarea>
                                </select>
                            </div>
                            </div>
                            <div class="col-6 col-sm-6 col-md-6">
                            <div class="form-group">
                            <div class="form-check">
                            
                            <input type="checkbox" name="emergency" value="1" {{ $patient->emergency== 1
                            ? 'checked' : 0 }}>
                            <label class="form-check-label"
                            for="exampleCheck1">@lang('site.emergency')</label>
                            </div>
                            </div>
                            </div>
                            
                            
                            </div>
                            
                            <button type="submit" class="btn btn-primary">@lang('site.save')</button>
                            
                            </form>
                            </div>
                            </div>
                            </div>
                            </div><!-- /.container-fluid -->
                            
                            </section>
                            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
                            
                            <script type="text/javascript">
                            //Initialize Select2 Elements
                            $(function () {
                            $('.select2').select2()
                            
                            $('.select2bs4_country').select2({
                            theme: 'bootstrap4'
                            })
                            
                            })
                            
                            $('.countryname').change(function () {
                            var id = $(this).find(':selected')[0].value;
                            var token = $("input[name='_token']").val();
                            $.ajax({
                            type: 'POST',
                            url: "{{route('secretary.ajax.country') }}",
                            data: {
                            'id': id,
                            '_token': token,
                            },
                            success: function (data) {
                            console.log(data)
                            var $city = $('#city');
                            $city.empty();
                            for (var i = 0; i < data.cities.length; i++) {
                            $city.append('<option id=' + data.cities[i].id + ' value=' + data.cities[i].id + '>' + data.cities[i].name_en + '</option>');
                            }
                            }
                            });
                            });
                            </script>
                            @endsection
                                                        