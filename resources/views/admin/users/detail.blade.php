@extends('layouts.adminDashboard')

@section('links')
    @include('css.adminLinks')
@endsection

@section('content')
    <section class="content-header">
        <h1>{{__('messages.sidebar.user_detail')}}</h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-lg-2 col-md-3 text-center">
                <div class="position-relative">
                    <img src="{{ $user->avatar === 'default.png' ? asset('assets/img/utils/default.png') : asset('avatars/'.$user->avatar) }}" alt="Photo 1" class="img-fluid img-rounded">
                    <div class="ribbon-wrapper ribbon-lg">
                        <div class="ribbon bg-success text-lg">
                            {{ $user->role == 'producer' ? __('messages.role.producer') : __('messages.role.worker') }}
                        </div>
                    </div>
                </div>
                <h2 class="lead"><b>{{ $user->family_name }}</b></h2>
                <p class="text-sm">{{ $user->email }}</p>
            </div>
            <div class="col-lg-10 col-md-9 p-3 pl-5">
                <div class="row">
                    <div class="col-6">
                        <p><b>{{__('messages.profile.name')}}: </b> {{ $user->family_name }} </p>
                        <p><b>{{__('messages.profile.name_read')}}: </b> {{ $user->name }} </p>
                        @if($user->role == 'worker')
                            <p><b>{{__('messages.profile.nickname')}}: </b> {{ $user->nickname }} </p>
                            <p><b>{{__('messages.profile.gender.title')}}: </b> {{ $user->gender ? __('messages.profile.gender.man') : __('messages.profile.gender.woman') }} </p>
                            <p><b>{{__('messages.profile.birthday')}}: </b> {{ format_date($user->birthday) }} </p>
                            <p><b>{{__('messages.profile.address')}}: </b> {{ format_address($user->post_number, $user->prefectures, $user->city, $user->address) }} </p>
                        @endif
                        @if($user->role == 'producer')
                            <p><b>{{__('messages.profile.contact_address')}}: </b> {{ format_address($user->post_number, $user->prefectures, $user->city, $user->contact_address) }} </p>
                            <p><b>{{__('messages.profile.management_mode.title')}}: </b> {{ $user['management_mode'] == 'individual' ? __('messages.profile.management_mode.individual') : __('messages.profile.management_mode.corporation') }} </p>
                        @endif
                    </div>
                    <div class="col-6">
                        @if($user->role == 'worker')
                            <p><b>{{__('messages.profile.emergency_phone')}}: </b> {{ $user->emergency_phone }} </p>
                            <p><b>{{__('messages.profile.emergency_relation')}}: </b> {{ $user->emergency_relation }} </p>
                            <p><b>{{__('messages.profile.job')}}: </b> {{ $user->job }} </p>
                            <p><b>{{__('messages.profile.bio')}}: </b> {{ $user->bio }} </p>
                        @endif
                        @if($user->role == 'producer')
                            <p><b>{{__('messages.profile.agency_name')}}: </b> {{ $user->agency_name }} </p>
                            <p><b>{{__('messages.profile.agency_phone')}}: </b> {{ $user->agency_phone }} </p>
                            <p><b>{{__('messages.profile.insurance.title')}}: </b> {{ $user->insurance ? __('messages.profile.insurance.yes') : __('messages.profile.insurance.no') }} </p>
                        @endif
                        <p><b>{{__('messages.profile.appeal_point')}}: </b> <br/> {{ $user->appeal_point }} </p>
                    </div>
                </div>
            </div>
        </div>

        <h4>{{__('messages.profile.login_history')}}</h4>
        @unless(count($login_history))
            <div class="text-center m-5">
                <i class="fa fa-exclamation-triangle" style="font-size: 60px"></i>
                <p class="text-center text-bold m-3"> {{__('messages.profile.no_login_history')}} </p>
            </div>
        @endunless
        @if(count($login_history))
        <table class="table table-striped projects">
            <thead>
            <tr>
                <th>
                    {{__('messages.profile.login_date')}}
                </th>
                <th>
                    {{__('messages.profile.login_time')}}
                </th>
            </tr>
            </thead>
            <tbody>
            @foreach ($login_history as $history)
                <tr>
                    <td>
                        {{format_date($history['created_at'], 'text')}}
                    </td>
                    <td>
                        {{format_time($history['created_at'])}}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        @endif
        {{ $login_history->links('vendor.pagination.custom') }}

        <h4>{{__('messages.profile.matching_history')}}</h4>
        @unless(count($recruitments))
            <div class="text-center m-5">
                <i class="fa fa-exclamation-triangle" style="font-size: 60px"></i>
                <p class="text-center text-bold m-3"> {{__('messages.recruitment.no_data')}} </p>
            </div>
        @endunless

        <div class="row">
            @foreach ($recruitments as $recruitment)
                @if($user['role'] == 'producer')
                    <div class="col-3">
                        <div class="card card-widget collapsed-card">
                            <div class="card-header">
                                <div class="user-block">
                                    <img class="img-circle" src="{{ !empty($recruitment['image']) ? asset('uploads/recruitments/'.$recruitment['image']) : asset('assets/img/utils/empty.png') }}" alt="User Image">
                                    <span class="username"><a href="#">{{ $recruitment['title'] }}</a></span>

                                    <span class="description">
                                        <input value="{{ $recruitment['review'] }}" type="text" class="rating" data-size="xs" readonly>
                                    </span>
                                </div>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <p class="m-0">{{ $recruitment['description'] }}</p>
                            </div>
                            <div class="card-footer card-comments">
                                @unless(count($recruitment['applicants']))
                                    <div class="text-center m-5">
                                        <i class="fa fa-exclamation-triangle" style="font-size: 60px"></i>
                                        <p class="text-center text-bold m-3"> {{__('messages.applicants.no_applicants')}} </p>
                                    </div>
                                @endunless
                                @foreach($recruitment['applicants'] as $applicant)
                                    <div class="card-comment">
                                        <img class="img-circle img-sm" src="{{ $applicant['avatar'] === 'default.png' ? asset('assets/img/utils/default.png') : asset('avatars/'.$applicant['avatar']) }}" alt="User Image">

                                        <div class="comment-text">
                                            <span class="username">
                                                {{ $applicant['nickname'] }}
                                            </span><!-- /.username -->
                                            {{ $applicant['recruitment_evaluation'] ? $applicant['recruitment_evaluation'] : __('messages.applications.no_recruitment_evaluation') }}
                                            <input value="{{ $applicant['recruitment_review'] }}" type="text" class="rating" data-size="xs" readonly>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @else
                    <div class="col-3">
                        <div class="card card-widget collapsed-card">
                            <div class="card-header">
                                <div class="user-block">
                                    <img class="img-circle" src="{{ $recruitment->avatar === 'default.png' ? asset('assets/img/utils/default.png') : asset('avatars/'.$recruitment->avatar) }}" alt="User Image">
                                    <span class="username"><a href="{{route('view_user_detail', ['id' => $recruitment->producer_id])}}">{{$recruitment['family_name']}}</a></span>
                                    <span class="description">{{format_date($recruitment['created_at'])}}</span>
                                </div>
                                <!-- /.user-block -->
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <h5 class="m-0">{{$recruitment['title']}}</h5>
                                <p class="m-0">{{$recruitment['description']}}</p>
                                <img class="img-thumbnail img-fluid pad" src={{ !empty($recruitment['image']) ? asset('uploads/recruitments/'.$recruitment['image']) : asset('assets/img/utils/empty.png') }} alt="Photo">
                                <input value="{{ $recruitment['worker_review'] }}" type="text" class="rating" data-size="xs" readonly>
                                <p>
                                    {{ $recruitment['worker_evaluation'] ? $recruitment['worker_evaluation'] : __('messages.applications.no_worker_evaluation') }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>


        {{ $recruitments->links('vendor.pagination.custom') }}

        <div style="text-align: right; margin-right: 30px; margin-bottom: 10px">
            <button class="btn btn-primary" onclick="javascript:history.go(-1);">
                <i class="fa fa-arrow-left"></i>
                {{__('messages.action.back')}}
            </button>
        </div>
    </section>
@endsection

@section('scripts')
    @include('scripts.adminScripts')
@endsection
