@extends('layouts.adminDashboard')

@section('links')
    @include('css.adminLinks')
@endsection

@section('content')
    <section class="content-header">
        <h1>{{__('messages.sidebar.matter_management')}}</h1>
        <div class="row">
            <div class="col-lg-2 col-md-3 text-center">
                <div class="position-relative">
                    <img src="{{ $producer->avatar === 'default.png' ? asset('assets/img/utils/default.png') : asset('avatars/'.$producer->avatar) }}" alt="Photo 1" class="img-fluid img-rounded">
                    <div class="ribbon-wrapper ribbon-lg">
                        <div class="ribbon bg-success text-lg">
                            {{ $producer->role == 'producer' ? __('messages.role.producer') : __('messages.role.worker') }}
                        </div>
                    </div>
                </div>
                <h2 class="lead"><b>{{ $producer->family_name }}</b></h2>
                <p class="text-sm">{{ $producer->email }}</p>
            </div>
            <div class="col-lg-10 col-md-9 p-3 pl-5">
                <div class="row">
                    <div class="col-6">
                        <p><b>{{__('messages.profile.name')}}: </b> {{ $producer->family_name }} </p>
                        <p><b>{{__('messages.profile.name_read')}}: </b> {{ $producer->name }} </p>
                        <p><b>{{__('messages.profile.contact_address')}}: </b> {{ format_address($producer->post_number, $producer->prefectures, $producer->city, $producer->contact_address) }} </p>
                        <p><b>{{__('messages.profile.management_mode.title')}}: </b> {{ $producer['management_mode'] == 'individual' ? __('messages.profile.management_mode.individual') : __('messages.profile.management_mode.corporation') }} </p>
                    </div>
                    <div class="col-6">
                        <p><b>{{__('messages.profile.agency_name')}}: </b> {{ $producer->agency_name }} </p>
                        <p><b>{{__('messages.profile.agency_phone')}}: </b> {{ $producer->agency_phone }} </p>
                        <p><b>{{__('messages.profile.insurance.title')}}: </b> {{ $producer->insurance ? __('messages.profile.insurance.yes') : __('messages.profile.insurance.no') }} </p>
                        <p><b>{{__('messages.profile.appeal_point')}}: </b> <br/> {{ $producer->appeal_point }} </p>
                    </div>
                </div>
            </div>
        </div>


        <div class="row">
            @foreach($matters as $matter)
                <div class="col-md-3">
                    <div class="card card-widget widget-user shadow-lg">
                        <div class="ribbon-wrapper ribbon-lg">
                            @if($matter['status'] == 'draft')
                                <div class="ribbon text-lg text-white" style="background: #b61889">
                                    {{__('messages.recruitment.status.draft')}}
                                </div>
                            @elseif($matter['status'] == 'collecting')
                                <div class="ribbon text-lg text-white" style="background: #02bef5">
                                    {{__('messages.recruitment.status.collecting')}}
                                </div>
                            @elseif($matter['status'] == 'working')
                                <div class="ribbon text-lg text-white" style="background: #172a89">
                                    {{__('messages.recruitment.status.working')}}
                                </div>
                            @elseif($matter['status'] == 'completed')
                                <div class="ribbon text-sm text-white" style="background: #0b5306">
                                    {{__('messages.recruitment.status.completed')}}
                                </div>
                            @elseif($matter['status'] == 'canceled')
                                <div class="ribbon text-sm text-white" style="background: #fa0428">
                                    {{__('messages.recruitment.status.canceled')}}
                                </div>
                            @elseif($matter['status'] == 'deleted')
                                <div class="ribbon text-sm text-white" style="background: #d09010">
                                    {{__('messages.recruitment.status.deleted')}}
                                </div>
                            @endif
                        </div>
                        <!-- Add the bg color to the header using any of the bg-* classes -->
                        <div class="widget-user-header text-white"
                             style="background: url('{{asset('uploads/recruitments/'.$matter['image'])}}') center center;">
                            <h5 class="text-left" style="padding-right: 40%">{{ $matter['title'] }}</h5>
                        </div>
                        <div class="widget-user-image">
                            <img class="img-rounded" src="{{ $matter['avatar'] === 'default.png' ? asset('assets/img/utils/default.png') : asset('avatars/'.$matter['avatar']) }}" alt="User Avatar">
                        </div>
                        <div class="card-footer pt-3">
                            <div class="text-center">
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="approve{{$matter['id']}}" {{ $matter['approved'] ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="approve{{$matter['id']}}">{{__("messages.recruitment.approved")}}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4 border-right">
                                    <div class="description-block">
                                        <h5 class="description-header" style="font-size: 10pt">{{__('messages.profile.producer_name')}}</h5>
                                        <a class="text-sm" href="{{route('view_user_detail', ['id' => $matter['producer_id']])}}">{{ $matter['family_name'] }}</a>
                                    </div>
                                    <!-- /.description-block -->
                                </div>
                                <!-- /.col -->
                                <div class="col-sm-4 border-right">
                                    <div class="description-block">
                                        <h5 class="description-header" style="font-size: 10pt">{{__('messages.recruitment.worker_amount')}}</h5>
                                        <span class="text-sm">{{ $matter['worker_amount'] }}</span>
                                    </div>
                                    <!-- /.description-block -->
                                </div>
                                <!-- /.col -->
                                <div class="col-sm-4">
                                    <div class="description-block">
                                        <h5 class="description-header" style="font-size: 10pt">{{__('messages.recruitment.work_date')}}</h5>
                                        <span class="text-sm">{{ $matter['work_date_start'] }}</span>
                                    </div>
                                    <!-- /.description-block -->
                                </div>
                                <!-- /.col -->
                            </div>
                            <div class="text-center">
                                <a href="{{route('view_matter_detail', ['id' => $matter['id']])}}" class="btn btn-outline-success">{{__('messages.action.detail')}}</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        {{ $matters->links('vendor.pagination.custom') }}

        <div style="text-align: right; margin-right: 30px">
            <button class="btn btn-primary mb-3" onclick="javascript:history.go(-1);">
                <i class="fa fa-arrow-left"></i>
                {{__('messages.action.back')}}
            </button>
        </div>
    </section>
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
    </script>
@endsection
