@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-4">

    <div class="row">
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
        <div class="card">
            <div class="card-body p-3">
            <div class="row">
                <div class="col-8">
                <div class="numbers">
                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Total Booking</p>
                    <h5 class="font-weight-bolder mb-0">
                    {{ $totalBookings }}
                    </h5>
                </div>
                </div>
                <div class="col-4 text-end">
                <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                    <i class="fa fa-star text-lg opacity-10" aria-hidden="true"></i>
                </div>
                </div>
            </div>
            </div>
        </div>
        </div>

        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
        <div class="card">
            <div class="card-body p-3">
            <div class="row">
                <div class="col-8">
                <div class="numbers">
                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Pending</p>
                    <h5 class="font-weight-bolder mb-0">
                    {{ $pendingBookings }}
                    </h5>
                </div>
                </div>
                <div class="col-4 text-end">
                <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                    <i class="fa fa-stopwatch text-lg opacity-10" aria-hidden="true"></i>
                </div>
                </div>
            </div>
            </div>
        </div>
        </div>

        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
        <div class="card">
            <div class="card-body p-3">
            <div class="row">
                <div class="col-8">
                <div class="numbers">
                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Approved</p>
                    <h5 class="font-weight-bolder mb-0">
                    {{ $approvedBookings }}
                    </h5>
                </div>
                </div>
                <div class="col-4 text-end">
                <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                    <i class="fa fa-box text-lg opacity-10" aria-hidden="true"></i>
                </div>
                </div>
            </div>
            </div>
        </div>
        </div>

        <div class="col-xl-3 col-sm-6">
        <div class="card">
            <div class="card-body p-3">
            <div class="row">
                <div class="col-8">
                <div class="numbers">
                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Rejected</p>
                    <h5 class="font-weight-bolder mb-0">
                    {{ $rejectedBookings }}
                    </h5>
                </div>
                </div>
                <div class="col-4 text-end">
                <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                    <i class="fa fa-ban text-lg opacity-10" aria-hidden="true"></i>
                </div>
                </div>
            </div>
            </div>
        </div>
        </div>
    </div>
    
    {{-- BANNER AREA --}}
    <div class="row mb-5 mt-5">
        <div class="col-12">
            <div id="mainCarousel" class="carousel slide rounded-xl overflow-hidden shadow-sm" data-bs-ride="carousel" data-bs-interval="3000">
                <div class="carousel-inner">
                    
                    <div class="carousel-item active">
                        <div class="main-banner" style="background-image: url('{{ asset('assets/img/slider/building.jpg') }}')"> 
                            
                            <div class="banner-content col-lg-7 col-md-9">
                                <span class="text-warning fw-bold text-uppercase ls-1 mb-2">Access Anywhere</span>
                                <h2 class="banner-title display-4 fw-bolder mb-3">Find The Perfect</h2>
                                <p class="banner-desc mb-4">From meeting rooms to event halls, browse our complete facility catalog and book from any device, anytime..</p>
                                <div>
                                    {{-- <a href="#" class="btn btn-banner btn-lg px-5 rounded-pill shadow-sm">Shop Now</a> --}}
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="carousel-item">
                        <div class="main-banner" style="background-image: url('{{ asset('assets/img/slider/smart_management.jpg') }}');" background-position: center bottom;">
                            
                            <div class="banner-content col-lg-7 col-md-9">
                                <span class="text-warning fw-bold text-uppercase ls-1 mb-2">Smart Management</span>
                                <h2 class="banner-title display-4 fw-bolder mb-3">Your Schedule</h2>
                                <p class="banner-desc mb-4">Manage all your reservations in one place. View history, reschedule, and get instant confirmation effortlessly.</p>
                            </div>

                        </div>
                    </div>

                    <div class="carousel-item">
                        <div class="main-banner" style="background-image: url('{{ asset('assets/img/slider/access_anywhere.jpg') }}'); background-position: center bottom;">
                            
                            <div class="banner-content col-lg-7 col-md-9">
                                <span class="text-warning fw-bold text-uppercase ls-1 mb-2">Effortless & Fast</span>
                                <h2 class="banner-title display-4 fw-bolder mb-3">Streamlined Booking</h2>
                                <p class="banner-desc mb-4">Say goodbye to complicated paperwork. Check real-time availability and secure your spot in seconds.</p>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>


    {{-- CATEGORY --}}
    <div class="row">
        <div class="col-12">
            <div class="category-header">
                <h4 class="mb-0 fw-bold">Category</h4>
                {{-- <a href="#" class="btn btn-sm btn-outline-secondary rounded-pill">View All Categories &rarr;</a> --}}
            </div>
        </div>
    </div>

    <div class="row g-4">
        @foreach($categories as $category)
        <div class="col-6 col-md-4 col-lg-2">
            <a href="#" class="card category-card rounded-xl text-decoration-none text-dark">
                
                <div class="category-icon overflow-hidden p-0"> 
                    
                    <img src="{{ asset($category->image) }}" 
                        alt="{{ $category->name }}" 
                        class="w-100 h-100 rounded-circle"
                        style="object-fit: cover;">
                        
                </div>
                
                <span class="category-title mt-2">{{ $category->name }}</span>
            </a>
        </div>
        @endforeach
    </div>
</div>

@endsection

@push('dashboard')
  <script>
    
  </script>
@endpush