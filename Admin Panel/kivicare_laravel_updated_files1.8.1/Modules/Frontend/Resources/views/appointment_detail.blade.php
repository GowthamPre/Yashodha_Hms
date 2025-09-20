@extends('frontend::layouts.master')

@section('title', __('frontend.appointment_detail'))

@section('content')
@include('frontend::components.section.breadcrumb')
<div class="list-page section-spacing px-0">
    <div class="page-title" id="page_title">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between gap-5 flex-wrap mb-5">
                <h6 class="font-size-18 mb-0">{{ __('frontend.appointment_detail') }}
                </h6>
            @php
            $id = $appointment ? $appointment->id : 0;
            $status = $appointment ? $appointment->status : null;
            $pay_status = $appointment ? optional($appointment->appointmenttransaction)->payment_status : 0;
        @endphp
        @if ($pay_status == 1 && $status == 'checkout')
            <div class="d-flex justify-content-end align-items-center ">
                <a class="btn btn-secondary"
                    href="{{ route('download_invoice', ['id' => $appointment->id]) }}">
                    <i class="fa-solid fa-download"></i>
                    {{ __('frontend.lbl_download_invoice') }}
                </a>
            </div>
        @endif
            </div>

            <div class="row">
                <div class="col-lg-8">

                    @if(empty($appointment->serviceRating) && $appointment->status == 'checkout' && optional($appointment->appointmenttransaction)->payment_status)
                        <div class="d-flex align-items-center justify-content-between gap-5 flex-wrap mb-5 pb-3">
                            <h6 class="font-size-18 mb-0">{{ __('frontend.havent_rated') }}
                            </h6>
                            <button class="btn btn-secondary d-flex gap-2 align-items-center" data-bs-toggle="modal" data-service-id="{{ optional($appointment->clinicservice)->id }}"
                                        data-doctor-id="{{ optional($appointment->doctor)->id }}"
                                        data-bs-target="#review-service">
                                        <i class="ph-fill ph-star"></i>{{ __('frontend.rate_us') }}
                            </button>
                        </div>
                    @endif
                    <div class="section-bg payment-box rounded">
                        <div class="d-flex align-items-center justify-content-between gap-5 flex-wrap">
                            <h6 class="mb-0">{{ __('frontend.appointment_id') }}
                            </h6>
                            <h6 class="mb-0 text-primary">#{{ $appointment->id }}</h6>
                        </div>
                    </div>
                    <div class="mt-5 pt-3">
                        <h6 class="font-size-18">{{ __('frontend.booking_detail') }}
                        </h6>
                        <div class="section-bg payment-box rounded">
                            <div class="row">
                                <div class="col-md-4">
                                    <span class="font-size-14">{{ __('frontend.appointment_date_time') }}
                                    </span>
                                    <p class="mb-0"> <span class="mb-0 h6">{{ DateFormate($appointment->appointment_date) }}</span> at <span class="mb-0 h6 text-uppercase">{{ \Carbon\Carbon::parse($appointment->appointment_time)->format(setting('time_formate') ?? 'h:i A') }}</span></p>
                                </div>
                                <div class="col-md-4 mt-md-0 mt-2">
                                    <span class="font-size-14">{{ __('frontend.service_name') }}
                                    </span>

                                    <a href="{{ route('service-details', ['id' => optional($appointment->clinicservice)->id]) }}">
                                    <h6 class="mb-0">{{ optional($appointment->clinicservice)->name ?? '-' }}</h6></a>
                                </div>
                                <div class="col-md-4 mt-md-0 mt-2">
                                    <span class="font-size-14">{{ __('frontend.doctor') }}</span>

                                    @if ($appointment->doctor === null)
                                        <h6 class="m-0">-</h6>
                                    @else
                                        <div class="d-flex gap-3 align-items-center">
                                            <img src="{{ optional($appointment->doctor)->profile_image ?? default_user_avatar() }}"
                                                alt="avatar" class="avatar avatar-50 rounded-pill">
                                            <div class="text-start">
                                                <h6 class="m-0">

                                                  {{getDisplayName($appointment->doctor)}}

                                                </h6>
                                              @php
                                                  $doctorEmail = optional($appointment->doctor)->email;
                                              @endphp

                                              @if ($doctorEmail)
                                                  <a href="mailto:{{ $doctorEmail }}">{{ $doctorEmail }}</a>
                                              @else
                                                  <span>-</span>
                                              @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="clinic-desc-box mt-4 pt-4 border-top">
                                <div class="row">
                                    <div class="col-md-4 mt-md-0 mt-2">
                                        <span class="font-size-14">{{ __('frontend.clinic_name') }}</span>
                                        <h6 class="m-0 line-count-1"> <img
                                                src="{{ optional($appointment->cliniccenter)->file_url ?? 'default_file_url()' }}"
                                                alt="avatar" class="avatar avatar-50 rounded-pill me-2">
                                            {{ $appointment->cliniccenter ? optional($appointment->cliniccenter)->name : '-' }}
                                        </h6>
                                    </div>
                                    <div class="col-md-4 mt-md-0 mt-2">
                                        <span class="font-size-14">{{ __('frontend.booking_status') }}</span>
                                        <h6 class="mb-0 {{ $appointment->status === 'cancelled' ? 'text-danger' : 'text-success' }}">
                                            {{ $appointment->status === 'checkout' ? 'Complete' : \Illuminate\Support\Str::title(str_replace('_', ' ', $appointment->status)) }}
                                        </h6>

                                    </div>
                                    <div class="col-md-4 mt-md-0 mt-2">
                                        <span class="font-size-14">{{ __('frontend.payment_status') }}</span>
                                        <h6 class="mb-0">
                                            @if($appointment->appointmenttransaction && $appointment->appointmenttransaction->payment_status)
                                                @if($appointment->status == 'cancelled')
                                                    @if($appointment->advance_paid_amount > 0)
                                                        <span class="text-warning">{{ __('frontend.advance_refunded') }}
                                                        </span>
                                                    @else
                                                        <span class="text-warning">{{ __('frontend.payment_refunded') }}
                                                        </span>
                                                    @endif
                                                @else
                                                    @if($appointment->appointmenttransaction->payment_method == 'cash')
                                                        <span class="text-danger">{{ __('frontend.pending') }}
                                                        </span>
                                                    @else
                                                        <span class="text-success">{{ __('frontend.paid') }}
                                                        </span>
                                                    @endif
                                                @endif
                                            @elseif($advancePaid)
                                                @if($appointment->status == 'cancelled')
                                                    <span class="text-warning">{{ __('frontend.advance_refunded') }}
                                                    </span>
                                                @else
                                                    <span class="text-success">{{ __('frontend.advance_paid') }}
                                                    </span>
                                                @endif
                                            @else
                                                <span class="text-danger">{{ __('frontend.pending') }}
                                                </span>
                                            @endif
                                        </h6>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4 pt-4 border-top">
                                <div class="row">
                                    <div class="col-md-4 mt-md-0 mt-2">
                                        <div class="d-flex align-items-center gap-2 flex-wrap mb-3">
                                            <span class="font-size-14">{{ __('frontend.booked_for') }}
                                            </span>
                                        </div>

                                        @if ($appointment->user === null)
                                            <h6 class="m-0">-</h6>
                                        @elseif($appointment->otherPatient)
                                        <div class="d-flex gap-3 align-items-center">
                                            <img src="{{ optional($appointment->otherPatient)->profile_image ?? default_user_avatar() }}"
                                                alt="avatar" class="avatar avatar-50 rounded-pill">
                                            <div class="text-start">
                                                <h6 class="m-0">
                                                    {{ optional($appointment->otherPatient)->first_name . ' ' . optional($appointment->otherPatient)->last_name ?? '-' }}
                                                </h6>
                                            </div>
                                        </div>
                                        @else
                                            <div class="d-flex gap-3 align-items-center">
                                                <img src="{{ optional($appointment->user)->profile_image ?? default_user_avatar() }}"
                                                    alt="avatar" class="avatar avatar-50 rounded-pill">
                                                <div class="text-start">
                                                    <h6 class="m-0">
                                                        {{ optional($appointment->user)->first_name . ' ' . optional($appointment->user)->last_name ?? '-' }}
                                                    </h6>
                                                    @php
                                                        $userEmail = optional($appointment->user)->email;
                                                    @endphp

                                                    @if ($userEmail)
                                                        <a href="mailto:{{ $userEmail }}">{{ $userEmail }}</a>
                                                    @else
                                                        <span>-</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="mt-5 pt-3">
                        <h6 class="font-size-18">{{ __('frontend.service_detail') }}
                        </h6>
                        <div class="section-bg payment-box rounded">

                            @if ($appointment->patientEncounter == null)
                                <div class="d-flex align-items-md-center bg-body p-4 rounded flex-md-row flex-column gap-3 payment-box-info">
                                    <div class="detail-box">
                                        <img src="{{ optional($appointment->clinicservice)->file_url ?? default_file_url() }}"
                                            alt="avatar" class="avatar avatar-80 rounded-pill">
                                    </div>

                                    <div class="row">
                                        <div class="">
                                            <div class="d-flex align-items-center">
                                                <span>
                                                <a href="{{ route('service-details', ['id' => optional($appointment->clinicservice)->id]) }}">
                                                    <b>{{ optional($appointment->clinicservice)->name ?? '-' }}</b></a>
                                                    {{ optional($appointment->clinicservice)->description ?? ' ' }}
                                                </span>
                                            </div>
                                            @php
                                                if (
                                                    optional($appointment->appointmenttransaction)->discount_type === 'percentage'
                                                ) {
                                                    $payable_Amount =
                                                        $appointment->service_price -
                                                        $appointment->service_price *
                                                            (optional($appointment->appointmenttransaction)->discount_value / 100);
                                                } else {
                                                    $payable_Amount =
                                                        $appointment->service_price -
                                                        optional($appointment->appointmenttransaction)->discount_value;
                                                }
                                                    $total_tax = 0;
                                                    $sub_total = $payable_Amount + (optional($appointment->appointmenttransaction)->inclusive_tax_price ?? 0);
                                                    $inclusive_tax_data = json_decode(optional($appointment->appointmenttransaction)->inclusive_tax, true); // decode tax details
                                            @endphp
                                            @if (optional($appointment->appointmenttransaction)->discount_value > 0)
                                            <div class="d-flex align-items-center gap-2">
                                                <h6 class="mb-0">
                                                    {{ Currency::format($sub_total) }}

                                                    <span class="text-primary">

                                                    @if (optional($appointment->appointmenttransaction)->discount_type === 'percentage')
                                                        (<span>{{ optional($appointment->appointmenttransaction)->discount_value ?? '--' }}%
                                                            </<span> off)
                                                        @else
                                                            (<span>{{ Currency::format(optional($appointment->appointmenttransaction)->discount_value) ?? '--' }}
                                                                </<span> off)

                                                    @endif

                                            </span>

                                                </h6>
                                                <del>{{ Currency::format($appointment->service_price) }}</del>

                                            </div>
                                            {{-- @if($appointment->appointmenttransaction->inclusive_tax_price != null && $appointment->patientEncounter == null)
                                                    @php
                                                        $total_tax = 0;
                                                        $sub_total = $payable_Amount + $appointment->appointmenttransaction->inclusive_tax_price;
                                                        $inclusive_tax_data = json_decode($appointment->appointmenttransaction->inclusive_tax, true); // decode tax details
                                                    @endphp
                                                    <li class="d-flex align-items-center justify-content-between pb-2 mb-2 mt-2 border-bottom">
                                                        <span>{{ __('appointment.service_price') }}</span>
                                                        <span class="text-primary">{{ Currency::format($payable_Amount) }}</span>
                                                    </li>
                                                    @if(!empty($inclusive_tax_data))
                                                        @foreach ($inclusive_tax_data as $t)
                                                            @if ($t['type'] == 'percent')
                                                                @php
                                                                    $tax_amount = $payable_Amount * $t['value'] / 100 ; // for inclusive, this is reverse calculated
                                                                    $total_tax += $tax_amount;
                                                                @endphp
                                                                <li class="d-flex align-items-center justify-content-between pb-2 mb-2 border-bottom">
                                                                    <span>{{ $t['title'] }} ({{ $t['value'] }}%)</span>
                                                                    <span class="text-primary">{{ Currency::format($tax_amount) }}</span>
                                                                </li>
                                                            @elseif($t['type'] == 'fixed')
                                                                @php
                                                                    $tax_amount = $t['value'];
                                                                    $total_tax += $tax_amount;
                                                                @endphp
                                                                <li class="d-flex align-items-center justify-content-between pb-2 mb-2 border-bottom">
                                                                    <span>{{ $t['title'] }}</span>
                                                                    <span class="text-primary">{{ Currency::format($tax_amount) }}</span>
                                                                </li>
                                                            @endif
                                                        @endforeach
                                                        <li class="d-flex align-items-center justify-content-between pb-2 mb-2 border-bottom">
                                                            <span>{{ __('messages.sub_total') }}</span>
                                                            <span class="text-primary">{{ Currency::format($sub_total) }}</span>
                                                        </li>
                                                    @endif
                                            @endif   --}}

                                            @else
                                                <h6 class="mb-0">
                                                    {{ Currency::format($appointment->service_amount) }}</h6>
                                            @endif
                                            @if(optional($appointment->appointmenttransaction)->inclusive_tax_price != null && $appointment->patientEncounter == null)

                                                    <small class="text-secondary"><i>{{ __('messages.lbl_with_inclusive_tax') }}</i></small>
                                                @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                            @if (
                                $appointment->patientEncounter !== null &&
                                    optional(optional($appointment->patientEncounter)->billingrecord)->billingItem != null)
                                @foreach (optional(optional($appointment->patientEncounter)->billingrecord)->billingItem as $billingItem)
                                    <div class="d-flex align-items-md-center bg-body p-4 rounded flex-md-row flex-column gap-3 payment-box-info">
                                        <div class="detail-box rounded">
                                            <img src="{{ optional($billingItem->clinicservice)->file_url ?? default_file_url() }}"
                                                alt="avatar" class="avatar avatar-80 rounded-pill">
                                        </div>

                                        <div>
                                            <div class="d-flex align-items-center gap-2">
                                                <span><b>{{ optional($billingItem->clinicservice)->name }}</b>
                                                    {{ optional($billingItem->clinicservice)->description ?? ' ' }}</span>

                                            </div>
                                            @php
                                                if ($billingItem->discount_type === 'percentage') {
                                                    $payable_Amount =
                                                        $billingItem->service_amount -
                                                        $billingItem->service_amount * ($billingItem->discount_value / 100);
                                                } else {
                                                    $payable_Amount = $billingItem->service_amount - $billingItem->discount_value;
                                                }

                                            @endphp
                                            @if ($billingItem->discount_value > 0)

                                            <div class="d-flex align-items-center gap-2">
                                                <h6 class="mb-0">
                                                    <span class="fw-normal">{{ Currency::format($billingItem->total_amount) }} *
                                                        {{ $billingItem->quantity }} = </span>
                                                        {{ Currency::format($billingItem->total_amount * $billingItem->quantity) }}


                                            <span>

                                                    @if ($billingItem->discount_type === 'percentage')
                                                        (<span>{{ $billingItem->discount_value ?? '--' }}%</<span> off)

                                                        @else
                                                            (<span>{{ Currency::format($billingItem->discount_value) ?? '--' }}
                                                                </<span> off)

                                                    @endif
                                                    @if($billingItem->inclusive_tax_amount > 0)
                                                        <small class="text-secondary"><i>{{ __('messages.lbl_with_inclusive_tax') }}</i></small>
                                                    @endif

                                            </span>


                                                </h6>


                                                <del>{{ Currency::format($billingItem->service_amount * $billingItem->quantity) }}</del>
                                            </div>
                                            @else
                                                <h6 class="mb-0"> <span
                                                        class="fw-normal">{{ Currency::format($billingItem->total_amount) }} *
                                                        {{ $billingItem->quantity }} = </span>
                                                    {{ Currency::format($billingItem->total_amount * $billingItem->quantity) }}
                                                    @if($billingItem->inclusive_tax_amount > 0)
                                                <small class="text-secondary"><i>{{ __('messages.lbl_with_inclusive_tax') }}</i></small></h6>
                                            @endif
                                            @endif

                                            {{-- @if (!empty($billingItem->clinicservice->inclusive_tax_price))
                                        @php
                                            $quantity = $billingItem->quantity;
                                            $inclusive_tax_price_per_unit = $billingItem->clinicservice->inclusive_tax_price;
                                            $inclusive_tax_price = $inclusive_tax_price_per_unit * $quantity;
                                            $inclusive_tax_data = json_decode($billingItem->clinicservice->inclusive_tax, true);

                                            $service_total = $payable_Amount * $quantity;
                                            $item_subtotal = ($payable_Amount + $inclusive_tax_price_per_unit) * $quantity;
                                            $total_item_tax = 0;
                                        @endphp

                                        <ul class="ps-0 w-100 mt-1">
                                            <li class="d-flex align-items-center justify-content-between border-bottom pb-2 mb-2">
                                                <span>{{ __('appointment.service_price') }}</span>
                                                <span class="text-primary">{{ Currency::format($service_total) }}</span>
                                            </li>

                                            @if (!empty($inclusive_tax_data))
                                                @foreach ($inclusive_tax_data as $t)
                                                    @if ($t['type'] == 'percent')
                                                        @php
                                                            $tax_per_unit = $payable_Amount * $t['value'] / 100 ;
                                                            $tax_total = $tax_per_unit * $quantity;
                                                            $total_item_tax += $tax_total;
                                                        @endphp
                                                        <li class="d-flex align-items-center justify-content-between border-bottom pb-2 mb-2">
                                                            <span>{{ $t['title'] }} ({{ $t['value'] }}% of {{ Currency::format($payable_Amount) }} × {{ $quantity }})</span>
                                                            <span class="text-primary">{{ Currency::format($tax_total) }}</span>
                                                        </li>
                                                    @elseif ($t['type'] == 'fixed')
                                                        @php
                                                            $tax_total = $t['value'] * $quantity;
                                                            $total_item_tax += $tax_total;
                                                        @endphp
                                                        <li class="d-flex align-items-center justify-content-between border-bottom pb-2 mb-2">
                                                            <span>{{ $t['title'] }} ({{ Currency::format($t['value']) }} × {{ $quantity }})</span>
                                                            <span class="text-primary">{{ Currency::format($tax_total) }}</span>
                                                        </li>
                                                    @endif
                                                @endforeach
                                                <li class="d-flex align-items-center justify-content-between border-bottom pb-2 mb-2">
                                                    <span>{{ __('appointment.sub_total') }}</span>
                                                    <span class="text-primary">{{ Currency::format($item_subtotal) }}</span>
                                                </li>
                                            @endif
                                        </ul>
                                    @endif --}}
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    <!-- Medical Reports Card Grid -->
                    @if(isset($medical_reports) && $medical_reports->count())
                    <div class="container my-5">
                        <h5 class="mb-3">{{ __('appointment.medical_report') }}</h5>
                        <div class="row g-3">
                            @foreach($medical_reports as $report)
                                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                                    <div class="card h-100 shadow-sm border-primary" style="cursor:pointer" onclick="window.open('{{ $report->file_url }}', '_blank')">
                                        <div class="card-body d-flex flex-column justify-content-between">
                                            <div>
                                                <h6 class="card-title mb-2">{{ $report->name }}</h6>
                                                <p class="card-text text-muted mb-0" style="font-size:13px;">{{ $report->date }}</p>
                                            </div>
                                            <div class="mt-3 text-center">
                                                <button class="btn btn-outline-primary btn-sm" type="button" onclick="event.stopPropagation();window.open('{{ $report->file_url }}', '_blank')">
                                                    <i class="ph ph-eye align-middle"></i> {{__('messages.lbl_view')}}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if ($appointment->status == 'checkout' || $appointment->status == 'check_in')
                        <div class="mt-5 pt-3">
                            <div class="d-flex align-items-center justify-content-between gap-3 section-bg p-3 rounded">
                                    <h6 class="font-size-18 mb-0">{{ __('frontend.encounter_details') }}
                                    </h6>
                                    <a data-bs-toggle="modal" data-bs-target="#encounter-details-view"
                                    class="font-size-14 fw-semibold text-secondary">{{__('messages.lbl_view')}}</a>
                            </div>
                        </div>
                    @endif
                    <!-- review -->
                    <!-- rate us modal -->
                    <x-frontend::section.review />

                    @if($review)
                        <div class="mt-5 pt-3">
                            <div class="d-flex align-items-center justify-content-between gap-5 flex-wrap mb-2">
                                <h6 class="font-size-18">{{ __('frontend.your_review') }}
                                </h6>
                                <div class="d-flex align-items-center gap-2 flex-wrap rate-us-btn">
                                    <button class="btn p-0" data-bs-toggle="modal" data-service-id="{{ optional($appointment->clinicservice)->id }}"
                                        data-doctor-id="{{ optional($appointment->doctor)->id }}"
                                        data-review-id="{{ $review->id }}"
                                        data-rating="{{ $review->rating }}"
                                        data-review-msg="{{ $review->review_msg }}"
                                        data-bs-target="#review-service">
                                        <i class="ph ph-pencil-simple-line"></i>
                                    </button>
                                    <!-- rate us modal -->
                                    <x-frontend::section.review />
                                    <button class="delete-rating-btn btn p-0" data-review-id="{{ $review->id }}">
                                        <i class="ph ph-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <ul class="list-inline m-0 p-0">
                                <li class="review-card">
                                    <div class="review-detail section-bg rounded">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="d-flex align-items-center gap-2 rounded-pill bg-primary-subtle badge">
                                                    <i class="ph-fill ph-star text-warning"></i>
                                                    <span class="font-size-14 fw-bold">{{ $review->rating }}</span>
                                                </div>
                                                <h6 class="m-0">{{ $review->title }}</h6>
                                            </div>
                                            <span class="bg-secondary-subtle badge rounded-pill">{{ optional($review->clinic_service)->name }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between flex-column flex-wrap gap-3">
                                            <div class="d-flex align-items-center gap-3">
                                                <img src="{{ optional($review->user)->profile_image }}" alt="user"
                                                    class="img-fluid user-img rounded-circle">
                                                <div>
                                                    <h6 class="line-count-1 font-size-14">By {{ optional($review->user)->gender == 'female' ? 'Miss.' : 'Mr.' }}
                                                        {{ optional($review->user)->first_name.' '.optional($review->user)->last_name }}
                                                    </h6>
                                                    <small class="mb-0 font-size-14">{{ $review->updated_at->diffForHumans() }}</small>
                                                </div>
                                            </div>
                                            <p class="mb-0 mt-2 font-size-14">{{ $review->review_msg }}</p>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    @endif
                </div>

                @php
                    $service_total_amount = 0; // Initialize outside the loop
                    $total_tax = 0;
                @endphp
                @if ($appointment->patientEncounter !== null)
                @foreach (optional(optional($appointment->patientEncounter)->billingrecord)->billingItem as $item)
                        @php
                            $quantity = $item->quantity ?? 1;

                            $service_total_amount += $item->total_amount; // Sum up service amounts
                            if($quantity > 1) {
                                if (isset($item->inclusive_tax_amount) && $item->inclusive_tax_amount > 0) {
                                    $service_total_amount += $item->inclusive_tax_amount *  $quantity;
                                }

                                if (!empty($item->discount_type)) {
                                    if ($item->discount_type === 'fixed') {
                                        $service_total_amount -= $item->discount_value * $quantity;
                                    } elseif ($item->discount_type === 'percentage') {
                                        $service_total_amount -= ($item->total_amount * $item->discount_value / 100);
                                    }
                                }
                            }
                        @endphp
                    @endforeach
                @endif

                <?php
                    $transaction = $appointment->appointmenttransaction ? $appointment->appointmenttransaction : null;
                    if ($transaction != null) {
                        $tax = json_decode(optional($transaction)->tax_percentage, true);
                    }
                    $total_amount = 0;
                    $discount_amount = 0;
                    if ($appointment->patientEncounter !== null) {
                        $transaction = optional($appointment->patientEncounter)->billingrecord ? optional($appointment->patientEncounter)->billingrecord : null;
                        if ($transaction['final_discount_type'] == 'percentage') {
                            $discount_amount = $service_total_amount * ($transaction['final_discount_value'] / 100);
                        } else {
                            $discount_amount = $transaction['final_discount_value'];
                        }
                        if ($transaction != null) {
                            foreach (optional(optional($appointment->patientEncounter)->billingrecord)->billingItem as $billingItem) {
                                $total_amount += $billingItem->total_amount;
                            }

                            $tax = json_decode(optional($transaction)->tax_data, true);
                        }
                        $sub_total = $service_total_amount - $discount_amount;
                    } else {
                        $sub_total = $appointment->service_amount;
                    }

                    if ($appointment->appointmenttransaction == null) {
                        $tax = Modules\Tax\Models\Tax::active()->whereNull('module_type')->orWhere('module_type', 'services')->where('status', 1)->where('tax_type', 'exclusive')->get();
                    }

                ?>

                <div class="col-lg-4 mt-lg-0 mt-5">
                    <h6 class="pb-1">{{ __('frontend.payment_details') }}</h6>
                    @if(
                        $appointment->status == 'cancelled' &&
                        optional($appointment->appointmenttransaction)->payment_status != 0 &&
                        optional($appointment->appointmenttransaction)->transaction_type != 'cash'
                    )
                    @php
                            $refundAmount = $appointment->getRefundAmount(); // Assumes this returns positive or negative amount
                    @endphp
                    <div class="payment-box section-bg rounded">
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="text-muted small">{{ formatDate($appointment->updated_at) }}</div>
                                <span class="badge {{ $refundAmount >= 0 ? 'bg-success' : 'bg-danger' }} rounded-pill px-3 py-2">
                                    {{ $refundAmount >= 0 ? __('frontend.refund_completed') : __('frontend.wallet_deducted') }}
                                </span>
                            </div>

                            <h6 class="fw-bold mb-4">
                                {{ $refundAmount >= 0 ? __('messages.refund_of').' '. \Currency::format($refundAmount) :  __('messages.wallet_deduction').' '. \Currency::format(abs($refundAmount)) }}
                            </h6>

                            <div class="row mb-2">
                              <div class="col-6 text-muted">{{ __('earning.lbl_payment_method') }}</div>
                              <div class="col-6 text-end text-primary">{{ __('messages.wallet') }}</div>
                            </div>

                            <div class="row mb-2">
                              <div class="col-6 text-muted">{{ __('clinic.price') }}</div>
                              <div class="col-6 text-end">{{ \Currency::format($appointment->total_amount) }}</div>
                            </div>
                            @if ($appointment->advance_paid_amount !=0 )
                            <div class="row mb-2">
                                <div class="col-6 text-muted">{{ __('messages.advanced_payment') }} </div>
                                <div class="col-6 text-end">{{ \Currency::format($appointment->advance_paid_amount) }}</div>
                              </div>
                            @endif

                            @if($appointment->cancellation_charge_amount != 0)
                            <div class="row mb-2">
                                <div class="col-6 text-muted">
                                    {{ __('messages.cancellation_fee') }}
                                    @if($appointment->cancellation_type === 'percentage')
                                        ({{ $appointment->cancellation_charge }}%)
                                    @else
                                        ({{ Currency::format($appointment->cancellation_charge) }})
                                    @endif
                                </div>
                                <div class="col-6 text-end">
                                    {{ Currency::format($appointment->cancellation_charge_amount) }}
                                </div>
                            </div>
                            @endif
                            <hr class="my-3">

                            <div class="row">
                                <div class="d-flex justify-content-between align-items-center px-4 py-2 rounded"
                                style="background-color: {{ $refundAmount >= 0 ? '#e6f4ea' : '#fdecea' }};">

                               <span class="fw-semibold {{ $refundAmount >= 0 ? 'text-success' : 'text-danger' }}">
                                   {{ $refundAmount >= 0 ? __('messages.refund_amount') : __('frontend.wallet_deducted') }}
                               </span>

                               <span class="fw-semibold text-dark">
                                   {{ \Currency::format(abs($refundAmount)) }}
                               </span>
                           </div>
                            </div>
                          </div>
                    </div>
                    @endif
                    <hr>
                    <div class="payment-box section-bg rounded">
                        @if ($transaction !== null)
                            @if ($appointment->patientEncounter !== null)
                                <div class="d-flex align-items-center gap-3 flex-wrap justify-content-between mb-2 pb-1">
                                    <p class="mb-0 font-size-14">{{ __('messages.total') }} </p>
                                    <h6 class="mb-0 font-size-14">{{ Currency::format($service_total_amount) ?? '--' }}</h6>
                                </div>
                                <div class="d-flex align-items-center gap-3 flex-wrap justify-content-between mb-2 pb-1">
                                    <p class="mb-0 font-size-14">{{ __('messages.discount') }}
                                        ( <span class="text-success">
                                            @if ($transaction->final_discount_type === 'percentage')
                                                {{ $transaction->final_discount_value ?? '--' }}%
                                            @else
                                                {{ Currency::format($transaction->final_discount_value) ?? '--' }}
                                            @endif
                                        </span>)
                                    </span>
                                    </p>
                                    <h6 class="mb-0 font-size-14 text-success">- {{ Currency::format($discount_amount) ?? '--' }}</h6>
                                </div>
                                <div class="d-flex align-items-center gap-3 flex-wrap justify-content-between mb-2 pb-1">
                                    <p class="mb-0 font-size-14">{{ __('frontend.sub_total') }}</p>
                                    <h6 class="mb-0 font-size-14">{{ Currency::format($sub_total) ?? '--' }}</h6>
                                </div>
                            @endif

                            @if ($appointment->patientEncounter == null)
                                <div class="d-flex align-items-center gap-3 flex-wrap justify-content-between mb-2 pb-1">
                                    <p class="mb-0 font-size-14">{{ __('messages.total') }}
                                        @if ($appointment->appointmenttransaction && $appointment->appointmenttransaction->inclusive_tax_price > 0)
                                        <small class="text-secondary"><i>{{ __('messages.lbl_with_inclusive_tax') }}</i></small>
                                        @endif</p>
                                    <h6 class="mb-0 font-size-14">{{ Currency::format($sub_total) ?? '--' }}</h6>
                                </div>
                            @endif
                        @else
                            <div class="d-flex align-items-center gap-3 flex-wrap justify-content-between mb-2 pb-1">
                                <p class="mb-0 font-size-14">{{ __('messages.total') }}</p>
                                <h6 class="mb-0 font-size-14">{{ Currency::format($sub_total) ?? '--' }}</h6>
                            </div>
                        @endif

                        @foreach ($tax as $t)
                            @if ($t['type'] == 'percent')
                                <li class="d-flex align-items-center justify-content-between pb-2 mb-2 border-bottom">
                                    {{-- <span>{{ $t['title'] }} ({{ $t['value'] }}%)</span> --}}
                                    <?php
                                    $tax_amount = ($sub_total * $t['value']) / 100;
                                    if ($sub_total > 0) {
                                        $tax_amount = ($sub_total * $t['value']) / 100;
                                    }
                                    $total_tax += $tax_amount;
                                    ?>
                                    {{-- <span class="text-primary">{{ Currency::format($tax_amount) }}</span> --}}
                                </li>
                            @elseif($t['type'] == 'fixed')
                                @php $total_tax += $t['value']; @endphp
                                {{-- <li class="d-flex align-items-center justify-content-between pb-2 mb-2 border-bottom">
                                    <span>{{ $t['title'] }}</span>
                                    <span class="text-primary">{{ Currency::format($t['value']) }}</span>
                                </li> --}}
                            @endif
                        @endforeach

                        <div class="d-flex align-items-center gap-3 mt-3 flex-wrap justify-content-between mb-2 pb-1">
                            <p class="mb-0 font-size-14">{{ __('appointment.tax') }}</p>
                            <div class="d-flex align-items-center gap-2">
                                @if($transaction !== null)
                               <i class="ph ph-info align-middle" data-bs-toggle="modal" data-bs-target="#taxDetailsModal" style="cursor: pointer;"></i>
                                @endif
                                <h6 class="mb-0 font-size-14 text-secondary">{{ Currency::format($total_tax) ?? '--' }}</h6>
                            </div>
                        </div>

                      <!-- <div class="modal fade " id="taxDetailsModal" tabindex="-1" aria-labelledby="taxDetailsModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog modal-sm">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="taxDetailsModalLabel">{{ __('frontend.tax_detail') }}
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <ul>
                                        @foreach($tax_percentage as $tax)
                                            @php
                                                if ($tax['type'] == 'percent' && $sub_total > 0) {
                                                    $tax_amount = ($sub_total * $tax['value']) / 100;
                                                } else {
                                                    $tax_amount = $tax['value'];
                                                }
                                            @endphp
                                            <li>
                                               <strong>
                                                   {{ $tax['title'] }}
                                                   @if($tax['type'] == 'percent')
                                                       ({{ $tax['value'] }}%)
                                                   @endif
                                                   :
                                               </strong>
                                               <span id="{{ strtolower(str_replace(' ', '', $tax['title'])) }}">
                                                   {{ Currency::format($tax_amount) ?? '--' }}
                                               </span>
                                           </li>

                                        @endforeach
                                    </ul>
                                    <p ><strong>{{ __('frontend.total_tax') }}</strong> <span id="totalTaxAmount">{{ Currency::format($total_tax) ?? '--' }}</span></p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('frontend.close') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div> -->


                    <div class="modal" id="taxDetailsModal">
               <div class="modal-dialog modal-dialog-centered modal-md">
               <div class="modal-content section-bg position-relative rounded">
                   <div class="modal-body modal-body-inner">
                       <div class="close-modal-btn" data-bs-dismiss="modal">
                           <i class="ph ph-x align-middle"></i>
                       </div>
                       <h5 class="mb-3" id="taxDetailsModalLabel">{{ __('frontend.tax_detail') }}</h5>
                       </strong></p>
                       <ul id="taxBreakdownList" class="p-0 mb-3 list-inline">

                       @foreach($tax_percentage as $tax)
                                                   @php
                                                       if ($tax['type'] == 'percent' && $sub_total > 0) {
                                                           $tax_amount = ($sub_total * $tax['value']) / 100;
                                                       } else {
                                                           $tax_amount = $tax['value'];
                                                       }
                                                   @endphp
                                                   <li class=" d-flex justify-content-between gap-3">
                                                      <strong>
                                                          {{ $tax['title'] }}
                                                          @if($tax['type'] == 'percent')
                                                              ({{ $tax['value'] }}%)
                                                          @endif

                                                      </strong>
                                                      <span id="{{ strtolower(str_replace(' ', '', $tax['title'])) }}">
                                                          {{ Currency::format($tax_amount) ?? '--' }}
                                                      </span>
                                                  </li>

                                               @endforeach
                       </ul>
                       <p class="mb-0 mt-3 d-flex flex-wrap justify-content-between gap-3"><strong>{{ __('frontend.total_tax') }}
                       </strong> <span id="totalTaxAmount" class="fw-bold text-secondary">{{ Currency::format($total_tax) ?? '--' }}</span></p>
                   </div>
                  </div>
               </div>
            </div>

                        @php
                            $grand_total = $appointment->patientEncounter
                                ? $service_total_amount + $total_tax - $discount_amount
                                : $appointment->total_amount;
                        @endphp

                        <div class="mt-3 pt-4 mb-1 border-top">
                            <div class="d-flex align-items-center mb-3 gap-3 flex-wrap justify-content-between">
                                <h6 class="mb-0">{{ __('appointment.total') }}</h6>
                                <h6 class="mb-0 text-primary">{{ Currency::format($grand_total) }}</h6>
                            </div>
                            @if($advancePaid)
                                <div class="d-flex align-items-center gap-3 flex-wrap justify-content-between">
                                    <h6 class="mb-3">{{ __('frontend.advance_paid_amount') }} ({{ $appointment->advance_payment_amount }}%)</h6>
                                    <h6 class="mb-3 text-success">{{ Currency::format($appointment->advance_paid_amount) }}</h6>
                                </div>

                                @if($appointment->status != 'cancelled')
                                    <div class="d-flex align-items-center gap-3 flex-wrap justify-content-between">
                                        <h6 class="mb-0">{{ __('frontend.remaining_amount') }}
                                            @if(optional($appointment->appointmenttransaction)->payment_status == 1)
                                                <span class="badge bg-success-subtle text-success">{{ __('frontend.paid') }}</span>
                                            @else
                                                <span class="badge bg-danger-subtle text-danger">{{ __('frontend.pending') }}</span>
                                            @endif
                                        </h6>
                                        <div class="d-flex align-items-center gap-2">
                                            <h6 class="mb-0 text-secondary">{{ Currency::format($grand_total - $appointment->advance_paid_amount) }}</h6>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-4">
                        @if($advancePaid && $appointment->status == 'check_in' && optional($appointment->appointmenttransaction)->payment_status == 0 && optional($appointment->patientEncounter)->status == 1)
                            <a href="#" class="btn btn-secondary"  data-bs-toggle="modal" data-bs-target="#paymentModal">{{ __('frontend.pay_now') }} {{ Currency::format($grand_total - $appointment->advance_paid_amount) }}</a>
                        @elseif($appointment->status == 'check_in' && optional($appointment->appointmenttransaction)->payment_status == 0 && optional($appointment->patientEncounter)->status == 1)
                            <a href="#" class="btn btn-secondary"  data-bs-toggle="modal" data-bs-target="#paymentModal">{{ __('frontend.pay_now') }} {{ Currency::format($grand_total) }}</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Encounter modal --}}
<div class="modal modal-xl fade" id="encounter-details-view">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content section-bg position-relative rounded">
            <div class="modal-body modal-body-inner modal-enocunter-detail">
                <div class="close-modal-btn" data-bs-dismiss="modal">
                    <i class="ph ph-x align-middle"></i>
                </div>
                <div class="encounter-info">
                    <h6>{{ __('frontend.basic_information') }}
                    </h6>
                    <div class="encounter-basic-info rounded">
                        <div class="d-flex justify-content-between align-items-start flex-wrap">
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <p class="mb-0 font-size-14">{{ __('frontend.appointment_id') }}
                                    </p>
                                    <span class="text-primary font-size-14 fw-bold">#{{ $appointment->id }}</span>
                                </div>
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <p class="mb-0 font-size-14">{{ __('frontend.doctor_name') }}
                                    </p>
                                    <span class="encounter-desc font-size-14 fw-bold">{{ optional($appointment->doctor)->first_name . ' ' . optional($appointment->doctor)->last_name ?? '-'}}</span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <p class="mb-0 font-size-14">{{ __('frontend.clinic_name') }}
                                    </p>
                                    <span class="encounter-desc font-size-14 fw-bold">{{ optional($appointment->cliniccenter)->name ?? '-' }}</span>
                                </div>
                            </div>
                            <span
                                class="bg-success-subtle badge rounded-pill text-uppercase text-uppercase font-size-10">{{ optional($appointment->patientEncounter)->status ? 'Active': 'Closed' }}</span>
                        </div>
                        <div class="encounter-descrption border-top">
                            <div class="d-flex gap-2 align-items-center">
                                <span class="font-size-14 flex-shrink-0">{{ __('frontend.description') }}
                                </span>
                                <p class="font-size-14 fw-semibold detail-desc mb-0">{{ optional($appointment->patientEncounter)->descrtiption ?? 'No records found' }}</p>
                            </div>
                        </div>
                    </div>

                    @php
                        $problems = $medical_history->get('encounter_problem', collect());
                        $observations = $medical_history->get('encounter_observations', collect());
                        $notes = $medical_history->get('encounter_notes', collect());
                    @endphp

                    <div class="encounter-box mt-5">
                        <a class="d-flex justify-content-between gap-3 mb-2 encounter-list" href="#problem"
                            data-bs-toggle="collapse">
                            <p class="mb-0 h6">{{ __('frontend.problem') }}
                            </p>
                            <i class="ph ph-caret-down"></i>
                        </a>
                        <div id="problem" class="collapse rounded encounter-inner-box">
                            @if($problems->isNotEmpty())
                                @foreach($problems as $problem)
                                    <p class="font-size-14">{{ $loop->iteration }}. {{ $problem->title }}</p>
                                @endforeach
                            @else
                                <p class="font-size-12 mb-0 text-danger text-center">{{ __('frontend.no_problems_found') }}
                                </p>
                            @endif
                        </div>
                    </div>
                    <div class="encounter-box mt-5">
                        <a class="d-flex justify-content-between gap-3 mb-2 encounter-list" href="#observation"
                            data-bs-toggle="collapse">
                            <p class="mb-0 h6">{{ __('frontend.observation') }}
                            </p>
                            <i class="ph ph-caret-down"></i>
                        </a>
                        <div id="observation" class="collapse  encounter-inner-box rounded">
                            @if($observations->isNotEmpty())
                                @foreach($observations as $observation)
                                    <p class="font-size-14">{{ $loop->iteration }}. {{ $observation->title }}</p>
                                @endforeach
                            @else
                                <p class="font-size-12 mb-0 text-danger text-center">{{ __('frontend.no_observation_found') }}
                                </p>
                            @endif
                        </div>
                    </div>
                    <div class="encounter-box mt-5">
                        <a class="d-flex justify-content-between gap-3 mb-2 encounter-list" href="#notes"
                            data-bs-toggle="collapse">
                            <p class="mb-0 h6">{{ __('frontend.notes') }}
                            </p>
                            <i class="ph ph-caret-down"></i>
                        </a>
                        <div id="notes" class="collapse  encounter-inner-box rounded">
                            @if($observations->isNotEmpty())
                                @foreach($notes as $note)
                                    <p class="font-size-14 mb-0">{{ $loop->iteration }}. {{ $note->title }}</p>
                                @endforeach
                            @else
                                <p class="font-size-12 mb-0 text-danger text-center">{{ __('frontend.no_note_found') }}
                                </p>
                            @endif
                        </div>
                    </div>
                    <div class="encounter-box mt-5">
                        <a class="d-flex justify-content-between gap-3 mb-2 encounter-list"
                           href="#medical-report-{{ $appointment->id }}" data-bs-toggle="collapse">
                            <p class="mb-0 h6">Medical Report</p>
                            <i class="ph ph-caret-down"></i>
                        </a>
                        <div id="medical-report-{{ $appointment->id }}" class="collapse encounter-inner-box rounded">
                            @if ($medical_report && $medical_report->file_url)
                                <a href="{{ asset($medical_report->file_url) }}" download class="btn btn-primary">
                                  Download Report
                                </a>
                            @endif
                        </div>
                    </div>
                       <div class="encounter-box mt-5">
                        <a class="d-flex justify-content-between gap-3 mb-2 encounter-list"
                            href="#body_chart-{{ $appointment->id }}" data-bs-toggle="collapse">
                            <p class="mb-0 h6">Body chart</p>
                            <i class="ph ph-caret-down"></i>
                        </a>
                        <div id="body_chart-{{ $appointment->id }}" class="collapse  encounter-inner-box rounded">
                        @if ($bodychart->isNotEmpty())
                            <div class="d-flex  flex-wrap gap-3">
                                @foreach ($bodychart as $chart)
                                    @foreach ($chart->media as $media) <!-- Iterate through the media collection -->
                                        <div class="body-chart-content text-center">
                                            <div class="image mb-2">
                                                <img src="{{ asset($media->getUrl()) }}" alt="{{ $media->name }}" class="img-fluid" width="100" height="100">
                                            </div>
                                            <a href="{{ asset($media->getUrl()) }}" download >
                                                Download
                                            </a>
                                        </div>
                                    @endforeach
                                @endforeach
                          </div>
                        @else
                            <p class="font-size-12 mb-0 text-danger text-center">No report found</p>
                        @endif
                        </div>
                    </div>
                    <div class="encounter-box mt-5">
                        <a class="d-flex justify-content-between gap-3 mb-2 encounter-list" href="#prescription"
                            data-bs-toggle="collapse">
                            <p class="mb-0 h6">{{ __('frontend.prescription') }}</p>
                            <i class="ph ph-caret-down"></i>
                        </a>
                        <div id="prescription" class="collapse encounter-inner-box rounded">
                            @if($prescriptions->isNotEmpty())
                                @foreach($prescriptions as $prescription)
                                    <h6>{{ $prescription->name }}</h6>
                                    @if($prescription->instruction)
                                        <p class="font-size-14 mb-0">{{ $prescription->instruction }}</p>
                                    @endif
                                    <div class="mt-3 pt-3 border-top mb-3">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <span class="font-size-14 mb-2">{{ __('frontend.frequency') }}
                                                </span>
                                                <h6 class="font-size-14">{{ $prescription->frequency }}</h6>
                                            </div>
                                            <div class="col-md-6 mt-md-0 mt-4">
                                                <span class="font-size-14 mb-2">{{ __('frontend.days') }}:
                                                </span>
                                                <h6 class="font-size-14">{{ $prescription->duration }} {{ __('frontend.days') }}
                                                </h6>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <p class="font-size-12 mb-0 text-danger text-center">{{ __('frontend.no_prescription_found') }}
                                </p>
                            @endif
                        </div>
                    </div>

                    <div class="encounter-box mt-5">
                        <a class="d-flex justify-content-between gap-3 mb-2 encounter-list" href="#soap"
                            data-bs-toggle="collapse">
                            <p class="mb-0 h6">{{ __('frontend.soap') }}
                            </p>
                            <i class="ph ph-caret-down"></i>
                        </a>
                        <div id="soap" class="collapse encounter-inner-box rounded">
                            @if($soap)

                                    <div class="border-top mb-3">
                                        <div class="row">
                                            <div class="col-md-6 ">

                                                <h6 class="font-size-14">{{ __('frontend.subjective') }}</h6>

                                                <span class="font-size-14 mb-2">{{ $soap->subjective }}</span>

                                            </div>
                                            <div class="col-md-6 ">
                                                <h6 class="font-size-14 mb-2">{{ __('frontend.objective') }}
                                                </h6>
                                                <span class="font-size-14">{{ $soap->objective }}</span>

                                            </div>

                                            <div class="col-md-6 ">
                                            <h6 class="font-size-14">{{ __('frontend.assessment') }}
                                            </h6>
                                                <span class="font-size-14 mb-2">
                                                    {{$soap->assessment}}
                                                </span>

                                            </div>
                                            <div class="col-md-6 ">
                                            <h6 class="font-size-14">{{ __('frontend.plan') }}
                                            </h6>
                                                <span class="font-size-14 mb-2">
                                                  {{$soap->plan}}
                                                </span>

                                            </div>
                                        </div>
                                    </div>

                            @else
                                <p class="font-size-12 mb-0 text-danger text-center">{{ __('frontend.no_soap_found') }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content section-bg rounded">
                <div class="close-modal-btn" data-bs-dismiss="modal">
                    <i class="ph ph-x align-middle"></i>
                </div>
            <div class="modal-body modal-payemnt-inner">
                <h6 class="mb-3 font-size-18" id="paymentModalLabel">{{ __('frontend.payment_method') }}</h6>
                    <div class="payment-modal-box rounded">
                        @foreach ($paymentMethods as $method)
                            <div class="form-check payment-method-items ps-0 d-flex justify-content-between align-items-center gap-3">
                                <label class="form-check-label d-flex gap-2 align-items-center"
                                    for="method-{{ $method }}">
                                    <img src="{{ asset('dummy-images/payment_icons/' . strtolower($method) . '.svg') }}"
                                        alt="{{ $method }}" style="width: 20px; height: 20px;">
                                    <span class="h6 fw-semibold m-0">{{ $method }}</span>
                                </label>
                                <input class="form-check-input" type="radio" name="payment_method"
                                    value="{{ $method }}" id="method-{{ $method }}"
                                    @if ($method === 'cash') checked @endif>
                            </div>
                        @endforeach
                    </div>
                <div class="text-end mt-5">
                    <button class="btn btn-secondary" id="pay_now"
                        data-bs-dismiss="modal">{{ __('frontend.submit') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@push('after-scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function () {
        $('.delete-rating-btn').on('click', function () {
            const reviewId = $(this).data('review-id');

            Swal.fire({
                title: 'Are you sure you want to remove your review?',
                text: 'Once deleted, your review cannot be recovered',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: 'var(--bs-secondary)',
                cancelButtonColor: 'var(--bs-gray-500)',
                confirmButtonText: 'Delete Review',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route('delete-rating') }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            id: reviewId
                        },
                        success: function (data) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: data.message
                            });
                            location.reload();
                        },
                        error: function (xhr, status, error) {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'There was an error deleting the review. Please try again.'
                            });
                        }
                    });
                }
            });
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        @if(session('paymentDetails'))
            const paymentDetails = @json(session('paymentDetails'));
            Swal.fire({
                title: 'Payment Success',
                html: `
                    <p>Your appointment with <strong>Dr. ${paymentDetails.doctorName}</strong> at
                    <strong>${paymentDetails.clinicName}</strong> has been confirmed on
                    <strong>${new Date(paymentDetails.appointmentDate).toLocaleDateString()}</strong> at
                    <strong>${new Date('1970-01-01T' + paymentDetails.appointmentTime).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</strong>.</p>
                    <div>
                        <p><strong>Booking ID:</strong> #${paymentDetails.bookingId}</p>
                        <p><strong>Payment via:</strong>${paymentDetails.paymentVia}</p>
                        <p><strong>Total Payment:</strong>${paymentDetails.currency} ${paymentDetails.totalAmount}</p>
                    </div>
                `,
                icon: 'success',
                confirmButtonText: 'Close',
                confirmButtonColor: '#FF6F61',
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('appointment-list') }}";
                }
            });
        @endif
    });

    document.querySelector('#pay_now').addEventListener('click', async function () {
        const appointmentId = "{{ $appointment->id }}";
        const selectedPaymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
        const baseUrl = "{{ url('/') }}";
        const totalAmount = parseFloat("{{ $appointment->total_amount }}");
        const advancePaymentAmount = parseFloat("{{ $appointment->advance_payment_amount }}");
        const advancePaymentStatus = parseInt("{{ $appointment->advance_payment_status }}");

        // Check wallet balance if wallet is selected payment method
        if (selectedPaymentMethod === 'Wallet') {
            try {
                const response = await fetch("{{ route('check.wallet.balance') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        totalAmount: advancePaymentStatus === 1 ? advancePaymentAmount : totalAmount
                    })
                });

                const data = await response.json();

                if (!data.success || (advancePaymentStatus === 1 ? data.balance < advancePaymentAmount : data.balance < totalAmount)) {
                    successSnackbar('Insufficient balance. Please add funds in wallet')
                    return;
                }
            } catch (error) {
                console.error('Error checking wallet balance:', error);
                return;
            }
        }

        fetch(`${baseUrl}/pay-now`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                appointment_id: appointmentId,
                transaction_type: selectedPaymentMethod
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.redirect) {
                window.location.href = data.redirect;
            } else if (data.status) {
                if (selectedPaymentMethod === 'Wallet') {
                    const paymentDetails = {
                        doctorName: "{{ optional($appointment->doctor)->first_name }} {{ optional($appointment->doctor)->last_name }}",
                        clinicName: "{{ optional($appointment->cliniccenter)->name }}",
                        appointmentDate: "{{ $appointment->appointment_date }}",
                        appointmentTime: "{{ $appointment->appointment_time }}",
                        bookingId: appointmentId,
                        paymentVia: selectedPaymentMethod,
                        currency: "{{ $appointment->currency_symbol }}",
                        totalAmount: advancePaymentStatus === 1 ? advancePaymentAmount.toFixed(2) : totalAmount.toFixed(2)
                    };

                    Swal.fire({
                        title: 'Payment Success',
                        html: `
                            <p>Your appointment with <strong>Dr. ${paymentDetails.doctorName}</strong> at
                            <strong>${paymentDetails.clinicName}</strong> has been confirmed on
                            <strong>${new Date(paymentDetails.appointmentDate).toLocaleDateString()}</strong> at
                            <strong>${new Date('1970-01-01T' + paymentDetails.appointmentTime).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</strong>.</p>
                            <div>
                                <p><strong>Booking ID:</strong> #${paymentDetails.bookingId}</p>
                                <p><strong>Payment via:</strong>${paymentDetails.paymentVia}</p>
                                <p><strong>Total Payment:</strong>${paymentDetails.currency} ${paymentDetails.totalAmount}</p>
                            </div>
                        `,
                        icon: 'success',
                        confirmButtonText: 'Close',
                        confirmButtonColor: '#FF6F61',
                        allowOutsideClick: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = `${baseUrl}/appointment-list`;
                        }
                    });
                } else {
                    window.location.href = `${baseUrl}/appointment-list`;
                }
            } else {
                alert(data.message || 'Payment failed.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred during payment processing.');
        });
    });
</script>

@endpush