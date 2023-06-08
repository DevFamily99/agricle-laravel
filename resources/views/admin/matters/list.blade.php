@extends('layouts.adminDashboard')

@section('links')
    @include('css.adminLinks')
@endsection

@section('content')
    <section class="content-header">
        <h1>{{__('messages.sidebar.matter_management')}}</h1>
    </section>

    <section class="content">
        <div class="card card-primary card-tabs">
            <div class="card-header p-0 pt-1">
                <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="custom-tabs-one-home-tab" data-toggle="pill" href="#custom-tabs-one-home" role="tab" aria-controls="custom-tabs-one-home" aria-selected="true">All</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="custom-tabs-one-profile-tab" data-toggle="pill" href="#custom-tabs-one-profile" role="tab" aria-controls="custom-tabs-one-profile" aria-selected="false">Farm</a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="custom-tabs-one-tabContent">
                    <div class="tab-pane fade show active" id="custom-tabs-one-home" role="tabpanel" aria-labelledby="custom-tabs-one-home-tab">
                        <div class="card card-outline card-primary collapsed-card">
                            <div class="card-header">
                                <h3 class="card-title">{{__('messages.action.filter')}}</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="form-group row">
                                    <label for="producer_name" class="col-sm-2 col-form-label">{{__('messages.profile.producer_name')}}</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="producer_name">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="recruitment_status" class="col-sm-2 col-form-label">{{__('messages.recruitment.status.title')}}</label>
                                    <div class="col-sm-10">
                                        <select class="custom-select" id="recruitment_status">
                                            <option value="all">{{__('messages.applications.status_all')}}</option>
                                            <option value="collecting">{{__('messages.recruitment.status.collecting')}}</option>
                                            <option value="working">{{__('messages.recruitment.status.working')}}</option>
                                            <option value="completed">{{__('messages.recruitment.status.completed')}}</option>
                                            <option value="canceled">{{__('messages.recruitment.status.canceled')}}</option>
                                            <option value="deleted">{{__('messages.recruitment.status.deleted')}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="work_date_start" class="col-sm-2 col-form-label">{{__('messages.profile.producer_name')}}</label>
                                    <div class="col-sm-5">
                                        <input type="date" class="form-control" id="work_date_start">
                                    </div>
                                    <div class="col-sm-5">
                                        <input type="date" class="form-control" id="work_date_end">
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="button" class="btn btn-info float-right" onclick="search_matter()">{{__('messages.action.filter')}}</button>
                            </div>
                        </div>
                        <div id="matters_body">

                        </div>
                    </div>
                    <div class="tab-pane fade" id="custom-tabs-one-profile" role="tabpanel" aria-labelledby="custom-tabs-one-profile-tab">
                        <div class="row">
                            @foreach($farms as $farm)
                                <div class="col-md-3">
                                    <div class="card card-widget widget-user shadow" style="cursor: pointer" onclick="location.href='{{route('view_matter_list_by_producer', ['id' => $farm['id']])}}'">
                                        <!-- Add the bg color to the header using any of the bg-* classes -->
                                        <div class="widget-user-header bg-info">
                                            <h3 class="widget-user-username">{{$farm['family_name']}}</h3>
                                            <h5 class="widget-user-desc">{{$farm['email']}}</h5>
                                        </div>
                                        <div class="widget-user-image">
                                            <img class="img-circle elevation-2" src="{{ $farm['avatar'] === 'default.png' ? asset('assets/img/utils/default.png') : asset('avatars/'.$farm['avatar']) }}" alt="User Avatar">
                                        </div>
                                        <div class="card-footer">
                                            <div class="row">
                                                <div class="col-sm-4 border-right">
                                                    <div class="description-block">
                                                        <h5 class="description-header">{{ $farm['collecting'] }}</h5>
                                                        <span class="description-text">{{__('messages.recruitment.status.collecting')}}</span>
                                                    </div>
                                                    <!-- /.description-block -->
                                                </div>
                                                <!-- /.col -->
                                                <div class="col-sm-4 border-right">
                                                    <div class="description-block">
                                                        <h5 class="description-header">{{ $farm['working'] }}</h5>
                                                        <span class="description-text">{{__('messages.recruitment.status.working')}}</span>
                                                    </div>
                                                    <!-- /.description-block -->
                                                </div>
                                                <!-- /.col -->
                                                <div class="col-sm-4">
                                                    <div class="description-block">
                                                        <h5 class="description-header">{{ $farm['completed'] }}</h5>
                                                        <span class="description-text">{{__('messages.recruitment.status.completed')}}</span>
                                                    </div>
                                                    <!-- /.description-block -->
                                                </div>
                                                <!-- /.col -->
                                            </div>
                                            <!-- /.row -->
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        {{ $farms->links('vendor.pagination.custom') }}
                    </div>
                </div>
            </div>
            <!-- /.card -->
        </div>

    </section>

    <a id="back-to-top" href="#" class="btn btn-primary back-to-top" role="button" aria-label="Scroll to top">
        <i class="fas fa-chevron-up"></i>
    </a>
@endsection

@section('scripts')
    @include('scripts.adminScripts')
    <script>
        $('[type="checkbox"]').on('change', function(){
            $.ajax({
                url: "{{route('set_matter_approve')}}",
                type: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    id: this.id.replace('approve', ''),
                    approved: this.checked?1:0
                },
                success: function(result) {
                    if(!!result) toastr.success(`{{__('messages.alert.done_success')}}`)
                }
            })
        })
        $(document).ready(() => {
            search_matter();
        })
        const search_matter = () => {
            const data = {
                recruitment_status: $('#recruitment_status').val()
            };

            if(!!$('#producer_name').val()) data.producer_name = $('#producer_name').val();
            if(!!$('#work_date_start').val()) data.work_date_start = $('#work_date_start').val();
            if(!!$('#work_date_end').val()) data.work_date_end = $('#work_date_end').val();

            $.ajax({
                url: "{{ route('search_matter_admin') }}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                data,
                success: function (response) {
                    $("#matters_body").html(response);
                }
            });
        }
    </script>
@endsection
