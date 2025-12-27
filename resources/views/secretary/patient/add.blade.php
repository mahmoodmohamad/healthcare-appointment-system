@extends('secretary.layout')

@section('contents')
<!-- Content Wrapper. Contains page content -->

<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">

                <h2>@lang('site.add-to-clinic') - {{ $patient->name}}
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
                        <h3 class="card-title">@lang('site.add-to-clinic')</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body ">
                        <form action="{{ route('secretary.patient.clinic.save', $patient) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-6 col-sm-6 col-md-6">
                                    <div class="form-group">
                                        <strong>@lang('site.clinics')</strong>
                                        <select class="form-control" name="clinic_id" id="clinic_id">
                                            @foreach ($clinics as $id => $clinic)
                                         
                                            <option value="{{ $id }}">{{ $clinic }}</option>
                                            @endforeach
                                        </select>
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
