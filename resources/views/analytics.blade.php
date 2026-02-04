@extends('layouts.app')

@section('title', 'Energy Analytics & Cost - Power Monitoring')

@section('page-title', 'Energy Analytics')
@section('page-icon', 'business_chart-bar-32')

@section('content')
<div class="content" style="padding-top: 0;">
    <div class="panel-header panel-header-sm" style="height: 50px !important; background: transparent !important; box-shadow: none;"></div>
    
    <!-- Top Summary Cards -->
    <div class="row">
        <!-- Today's Usage -->
        <div class="col-md-4">
            <div class="card card-status shadow-lg border-0" style="background: linear-gradient(87deg, #fb6340 0, #fbb140 100%) !important; border-radius: 20px;">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-big text-center text-white mr-3">
                            <i class="now-ui-icons objects_support-17" style="font-size: 2em; opacity: 0.8;"></i>
                        </div>
                        <div>
                            <p class="card-category text-white mb-0" style="opacity: 0.9;">Today's Consumption</p>
                            <h3 class="card-title font-weight-bold mb-0 text-white">{{ number_format($todayKwh, 3) }} <small style="font-size:0.6em">kWh</small></h3>
                        </div>
                    </div>
                    <div class="stats text-white small" style="opacity: 0.8;">
                        <i class="now-ui-icons ui-1_calendar-60 mr-1"></i> {{ date('d F Y') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Cost -->
        <div class="col-md-4">
            <div class="card card-status shadow-lg border-0" style="background: linear-gradient(87deg, #2dce89 0, #2dcecc 100%) !important; border-radius: 20px;">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-big text-center text-white mr-3">
                            <i class="now-ui-icons business_money-coins" style="font-size: 2em; opacity: 0.8;"></i>
                        </div>
                        <div>
                            <p class="card-category text-white mb-0" style="opacity: 0.9;">Today's Cost</p>
                            <h3 class="card-title font-weight-bold mb-0 text-white">Rp {{ number_format($todayCost, 0, ',', '.') }}</h3>
                        </div>
                    </div>
                    <div class="stats text-white small" style="opacity: 0.8;">
                         Based on Rp {{ number_format($tariff, 0, ',', '.') }}/kWh
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Projection -->
        <div class="col-md-4">
            <div class="card card-status shadow-lg border-0" style="background: linear-gradient(87deg, #11cdef 0, #1171ef 100%) !important; border-radius: 20px;">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-big text-center text-white mr-3">
                            <i class="now-ui-icons business_bank" style="font-size: 2em; opacity: 0.8;"></i>
                        </div>
                        <div>
                            <p class="card-category text-white mb-0" style="opacity: 0.9;">Est. Monthly Bill</p>
                            <h3 class="card-title font-weight-bold mb-0 text-white">Rp {{ number_format($estimatedMonthlyCost, 0, ',', '.') }}</h3>
                        </div>
                    </div>
                    <div class="stats text-white small" style="opacity: 0.8;">
                        <i class="now-ui-icons arrows-1_refresh-69 mr-1"></i> Projection based on current usage
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Chart -->
        <div class="col-lg-8 col-md-12">
            <div class="card shadow-lg border-0" style="border-radius: 20px;">
                <div class="card-header border-0" style="background-color: transparent; padding: 25px 25px 0;">
                     <h5 class="card-title m-0 font-weight-bold" style="color: #32325d;">Weekly Energy Usage</h5>
                     <p class="category text-muted mb-0">Daily Power Consumption (kWh) - Last 7 Days</p>
                </div>
                <div class="card-body px-4 pb-4">
                     <div class="chart-area" style="height: 320px;">
                        <canvas id="energyChart"></canvas>
                     </div>
                </div>
            </div>
        </div>

        <!-- Configuration -->
        <div class="col-lg-4 col-md-12">
            <div class="card shadow-lg border-0" style="border-radius: 20px;">
                <div class="card-header border-0 pt-4 px-4" style="border-radius: 20px 20px 0 0;">
                    <h6 class="text-uppercase text-muted font-weight-bold mb-0" style="font-size: 0.75rem;">Configuration</h6>
                </div>
                <div class="card-body px-4 pb-4">
                    @if(auth()->check() && auth()->user()->role === 'admin')
                    <form action="{{ route('analytics.settings') }}" method="POST">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-dark mb-2 small text-uppercase">Electricity Tariff (IDR / kWh)</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text font-weight-bold bg-white" style="border-top-left-radius: 20px; border-bottom-left-radius: 20px;">Rp</span>
                                </div>
                                <input type="number" step="0.01" name="electricity_tariff" class="form-control" value="{{ $tariff }}" style="border-top-right-radius: 20px; border-bottom-right-radius: 20px;">
                            </div>
                            <small class="text-muted pl-1">Standard PLN R1 1300VA ~ Rp 1.444,70</small>
                        </div>
                        <button type="submit" class="btn btn-primary btn-round btn-block font-weight-bold shadow-none mb-4">Update Tariff</button>
                    </form>
                    @else
                        <div class="alert alert-info py-2 small rounded text-center mb-4">
                            <i class="now-ui-icons travel_info mr-1"></i> Current Tariff: <strong>Rp {{ number_format($tariff, 0, ',', '.') }} / kWh</strong>
                        </div>
                    @endif

                    <div class="pt-3 border-top">
                        <h6 class="text-muted text-uppercase mb-3" style="font-size: 0.7rem; font-weight: 800;">Analytics & Stats</h6>
                        
                        <!-- Peak Demand -->
                        <div class="d-flex justify-content-between align-items-center mb-3 p-2 rounded" style="background: #f6f9fc;">
                             <span class="text-muted small font-weight-bold">Peak Demand</span>
                             <span class="font-weight-bold text-danger">{{ number_format($peakDemand, 0) }} Watt</span>
                        </div>

                         <!-- Usage So Far -->
                        <div class="d-flex justify-content-between align-items-center mb-2 px-2">
                             <span class="text-muted small">Usage This Month</span>
                             <span class="font-weight-bold text-dark">{{ number_format($usageMonth, 1) }} kWh</span>
                        </div>
                        
                        <!-- Cost Breakdown -->
                        <div class="d-flex justify-content-between align-items-center mb-2 px-2">
                             <span class="text-muted small">Cost So Far</span>
                             <span class="font-weight-bold text-dark">Rp {{ number_format($costMonthSoFar, 0, ',', '.') }}</span>
                        </div>
                        
                         <!-- Comparison -->
                        <div class="d-flex justify-content-between align-items-center mb-2 px-2">
                             <span class="text-muted small">Vs Last Month</span>
                             @if($comparisonPercent > 0)
                                <span class="badge badge-danger badge-pill"><i class="fas fa-arrow-up"></i> {{ number_format(abs($comparisonPercent), 1) }}%</span>
                             @elseif($comparisonPercent < 0)
                                <span class="badge badge-success badge-pill"><i class="fas fa-arrow-down"></i> {{ number_format(abs($comparisonPercent), 1) }}%</span>
                             @else
                                <span class="badge badge-secondary badge-pill">- 0%</span>
                             @endif
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-3 px-2">
                             <span class="text-muted small">Days Remaining</span>
                             <span class="font-weight-bold text-info">{{ now()->daysInMonth - now()->day }} Days</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Chart Data
        var ctx = document.getElementById("energyChart").getContext("2d");

        var gradientFill = ctx.createLinearGradient(0, 170, 0, 50);
        gradientFill.addColorStop(0, "rgba(128, 182, 244, 0)");
        gradientFill.addColorStop(1, "rgba(249, 99, 50, 0.4)");

        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($dailyLabels) !!},
                datasets: [{
                    label: "Energy (kWh)",
                    pointBorderWidth: 2,
                    pointHoverRadius: 4,
                    pointHoverBorderWidth: 1,
                    pointRadius: 4,
                    fill: true,
                    backgroundColor: gradientFill,
                    borderColor: "#f96332",
                    borderWidth: 2,
                    data: {!! json_encode($dailyData) !!}
                }]
            },
            options: {
                maintainAspectRatio: false,
                legend: {
                    display: false
                },
                tooltips: {
                    bodySpacing: 4,
                    mode: "nearest",
                    intersect: 0,
                    position: "nearest",
                    xPadding: 10,
                    yPadding: 10,
                    caretPadding: 10
                },
                responsive: true,
                scales: {
                    yAxes: [{
                        gridLines: {
                            zeroLineColor: "transparent",
                            drawBorder: false
                        },
                         ticks: {
                            padding: 20,
                            fontColor: "#9a9a9a"
                        }
                    }],
                    xAxes: [{
                        gridLines: {
                            zeroLineColor: "transparent",
                            drawBorder: false,
                        },
                        ticks: {
                            padding: 20,
                            fontColor: "#9a9a9a"
                        }
                    }]
                },
                layout: {
                    padding: {
                        left: 0,
                        right: 0,
                        top: 15,
                        bottom: 15
                    }
                }
            }
        });
    });
</script>
@endpush
