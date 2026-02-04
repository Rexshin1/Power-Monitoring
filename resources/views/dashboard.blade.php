@extends('layouts.app')

@section('title', 'Dashboard - Power Monitoring')

@section('content')
<div class="panel-header panel-header-sm" style="height: 50px !important; background: transparent !important; box-shadow: none;">
</div>

<div class="content" style="padding-top: 0;">
    
    <!-- 1. Header & Controls -->
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h4 class="font-weight-bold m-0 text-dark" style="font-weight: 800 !important; letter-spacing: -0.5px;">
                Power Monitoring Dashboard
            </h4>
            <span class="text-muted small">Real-time status information</span>
        </div>
        <div class="col-md-6 text-right d-flex justify-content-end align-items-center">
            <!-- Status Badge -->
            <div class="mr-3">
                <span class="badge badge-success px-3 py-2" id="device-status" style="font-size: 0.8rem;">ONLINE</span>
            </div>

        </div>
    </div>

    <!-- Custom CSS for Orange/White Theme -->
    <style>
        .card-clean {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            background: #fff;
            transition: all 0.2s;
        }
        .card-clean:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .card-clean .card-body {
            padding: 20px;
        }
        .metric-value {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0;
            line-height: 1;
            letter-spacing: -1px;
        }
        .metric-unit {
            font-size: 0.85rem;
            font-weight: 600;
            margin-left: 4px;
            opacity: 0.8;
            margin-bottom: 4px;
        }
        .metric-label {
            color: #888;
            font-size: 0.70rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }
        .icon-circle {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .bg-light-orange { background-color: #FFF2E6; color: #f96332; }
        
        /* New Color Palette */
        .text-blue { color: #2CA8FF !important; }
        .bg-light-blue { background-color: #E6F5FF; color: #2CA8FF; }

        .text-yellow { color: #FFB236 !important; }
        .bg-light-yellow { background-color: #FFF4E0; color: #FFB236; }

        .text-red { color: #FF3636 !important; }
        .bg-light-red { background-color: #FFE6E6; color: #FF3636; }

        .text-green { color: #18ce0f !important; }
        .bg-light-green { background-color: #E6FFE6; color: #18ce0f; }

        .text-purple { color: #9368E9 !important; }
        .bg-light-purple { background-color: #F2E6FF; color: #9368E9; }

        .text-teal { color: #11cdef !important; }
        .bg-light-teal { background-color: #E0F7FA; color: #11cdef; }
    </style>

    <!-- 2. Metric Cards Row -->
    <div class="row">
        <!-- Voltage (Blue) -->
        <div class="col-lg-2 col-md-4 col-6">
            <div class="card card-clean mb-4">
                <div class="card-body d-flex justify-content-between align-items-center p-3">
                    <div>
                        <div class="metric-label" style="font-size: 0.65rem;">VOLTAGE</div>
                        <div class="metric-value text-blue" style="font-size: 1.5rem;"><span id="voltage-val">0</span></div>
                        <div class="metric-unit text-blue" style="font-size: 0.8rem;">V</div>
                    </div>
                    <div class="icon-circle bg-light-blue" style="width: 35px; height: 35px;">
                        <i class="now-ui-icons objects_support-17" style="font-size: 1rem;"></i>
                    </div>
                </div>
            </div>
        </div>
        <!-- Current (Yellow) -->
        <div class="col-lg-2 col-md-4 col-6">
            <div class="card card-clean mb-4">
                <div class="card-body d-flex justify-content-between align-items-center p-3">
                    <div>
                        <div class="metric-label" style="font-size: 0.65rem;">CURRENT</div>
                        <div class="metric-value text-yellow" style="font-size: 1.5rem;"><span id="current-val">0</span></div>
                        <div class="metric-unit text-yellow" style="font-size: 0.8rem;">A</div>
                    </div>
                    <div class="icon-circle bg-light-yellow" style="width: 35px; height: 35px;">
                        <i class="now-ui-icons business_chart-bar-32" style="font-size: 1rem;"></i>
                    </div>
                </div>
            </div>
        </div>
        <!-- Power (Red) -->
        <div class="col-lg-2 col-md-4 col-6">
            <div class="card card-clean mb-4">
                <div class="card-body d-flex justify-content-between align-items-center p-3">
                    <div>
                        <div class="metric-label" style="font-size: 0.65rem;">POWER</div>
                        <div class="metric-value text-red" style="font-size: 1.5rem;"><span id="power-val">0</span></div>
                        <div class="metric-unit text-red" style="font-size: 0.8rem;">W</div>
                    </div>
                    <div class="icon-circle bg-light-red" style="width: 35px; height: 35px;">
                        <i class="now-ui-icons media-1_button-power" style="font-size: 1rem;"></i>
                    </div>
                </div>
            </div>
        </div>
        <!-- Energy (Green) -->
        <div class="col-lg-2 col-md-4 col-6">
            <div class="card card-clean mb-4">
                <div class="card-body d-flex justify-content-between align-items-center p-3">
                    <div>
                        <div class="metric-label" style="font-size: 0.65rem;">ENERGY</div>
                        <div class="metric-value text-green" style="font-size: 1.3rem;"><span id="energy-val">0</span></div>
                        <div class="metric-unit text-green" style="font-size: 0.8rem;">kWh</div>
                    </div>
                    <div class="icon-circle bg-light-green" style="width: 35px; height: 35px;">
                        <i class="now-ui-icons objects_spaceship" style="font-size: 1rem;"></i>
                    </div>
                </div>
            </div>
        </div>
        <!-- Frequency (Purple) -->
        <div class="col-lg-2 col-md-4 col-6">
            <div class="card card-clean mb-4">
                <div class="card-body d-flex justify-content-between align-items-center p-3">
                    <div>
                        <div class="metric-label" style="font-size: 0.65rem;">FREQ</div>
                        <div class="metric-value text-purple" style="font-size: 1.5rem;"><span id="frequency-val">0</span></div>
                        <div class="metric-unit text-purple" style="font-size: 0.8rem;">Hz</div>
                    </div>
                    <div class="icon-circle bg-light-purple" style="width: 35px; height: 35px;">
                        <i class="now-ui-icons tech_watch-time" style="font-size: 1rem;"></i>
                    </div>
                </div>
            </div>
        </div>
        <!-- Power Factor (Teal) -->
        <div class="col-lg-2 col-md-4 col-6">
            <div class="card card-clean mb-4">
                <div class="card-body d-flex justify-content-between align-items-center p-3">
                    <div>
                        <div class="metric-label" style="font-size: 0.65rem;">P.  FACTOR</div>
                        <div class="metric-value text-teal" style="font-size: 1.5rem;"><span id="pf-val">0</span></div>
                        <div class="metric-unit text-teal" style="font-size: 0.8rem;">PF</div>
                    </div>
                    <div class="icon-circle bg-light-teal" style="width: 35px; height: 35px;">
                        <i class="now-ui-icons media-2_sound-wave" style="font-size: 1rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Chart & Heatmap Row -->
    <div class="row">
        <!-- Real-time Power Chart -->
        <div class="col-lg-8">
            <div class="card card-clean mb-4 h-100">
                <div class="card-header bg-transparent pt-3 pl-3">
                    <h5 class="card-title text-dark font-weight-bold m-0">REAL-TIME POWER (W)</h5>
                </div>
                <div class="card-body d-flex flex-column flex-grow-1 p-0">
                    <div class="chart-area" style="position: relative; flex: 1; min-height: 450px; width: 100%;">
                        <canvas id="realtimeChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <!-- Monthly Heatmap -->
        <div class="col-lg-4">
            <div class="card card-clean mb-4 h-100">
                <div class="card-header bg-transparent pt-4 pl-4 border-0">
                    <h5 class="card-title text-dark font-weight-bold m-0 text-uppercase" style="letter-spacing: 0.5px;">Intensity Map</h5>
                    <p class="category text-muted mb-0 small font-weight-bold">{{ \Carbon\Carbon::now()->format('F Y') }}</p>
                </div>
                <div class="card-body px-4">
                    <!-- Day Labels -->
                    <div class="d-flex justify-content-between mb-2 px-1">
                        @foreach(['S','M','T','W','T','F','S'] as $day)
                            <small class="text-muted font-weight-bold text-center" style="width: 14.28%;">{{ $day }}</small>
                        @endforeach
                    </div>

                    <!-- Calendar Grid -->
                    <div class="d-flex flex-wrap justify-content-start align-content-start" style="gap: 6px;">
                        
                        <!-- Empty slots for days before start of month -->
                        @php 
                            $firstDayOfWeek = \Carbon\Carbon::now()->startOfMonth()->dayOfWeek; 
                        @endphp
                        @for($i = 0; $i < $firstDayOfWeek; $i++)
                            <div style="width: calc(14.28% - 6px); aspect-ratio: 1;"></div>
                        @endfor

                        <!-- Days -->
                        @if(isset($heatmapData))
                            @foreach($heatmapData as $data)
                                @php
                                    $usage = $data['usage'];
                                    $colorClass = 'bg-light'; 
                                    $textClass = 'text-muted';

                                    if($usage > 0) {
                                        $textClass = 'text-white';
                                        
                                        // Thresholds based on typical power (2100-2300W in simulation)
                                        if($usage > 2250) {
                                            $colorClass = 'bg-danger shadow-sm'; // Red (High)
                                        } elseif($usage > 2150) {
                                            $colorClass = 'bg-warning shadow-sm'; // Yellow/Orange (Med)
                                        } else {
                                            $colorClass = 'bg-success shadow-sm'; // Green (Low)
                                        }
                                    }
                                @endphp
                                <div class="{{ $colorClass }} rounded"
                                     data-toggle="tooltip" 
                                     data-placement="top"
                                     title="{{ $data['date'] }}: {{ $usage }} W (Avg)"
                                     style="width: calc(14.28% - 6px); aspect-ratio: 1; display: flex; align-items:center; justify-content:center; font-size:0.7rem; font-weight: 700; cursor: pointer; transition: transform 0.2s;"
                                     onmouseover="this.style.transform='scale(1.2) translateY(-2px)'; this.style.zIndex='10';"
                                     onmouseout="this.style.transform='scale(1) translateY(0)'; this.style.zIndex='1';">
                                    <span class="{{ $textClass }}">{{ $data['day'] }}</span>
                                </div>
                            @endforeach
                        @else
                            <div class="col-12 text-center text-muted small py-5">No Data</div>
                        @endif
                    </div>

                    <!-- Legend -->
                    <div class="mt-4 pt-2 d-flex justify-content-center align-items-center">
                         <div class="d-flex align-items-center mr-3">
                             <div class="bg-success rounded-circle" style="width:10px; height:10px; margin-right:6px;"></div> 
                             <small class="text-muted font-weight-bold" style="font-size: 0.7rem;">Low</small>
                         </div>
                         <div class="d-flex align-items-center mr-3">
                             <div class="bg-warning rounded-circle" style="width:10px; height:10px; margin-right:6px;"></div> 
                             <small class="text-muted font-weight-bold" style="font-size: 0.7rem;">Med</small>
                         </div>
                         <div class="d-flex align-items-center">
                             <div class="bg-danger rounded-circle" style="width:10px; height:10px; margin-right:6px;"></div> 
                             <small class="text-muted font-weight-bold" style="font-size: 0.7rem;">High</small>
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
        
        // --- 1. Initialize Real-time Chart ---
        var ctx = document.getElementById('realtimeChart').getContext("2d");
        var gradientFill = ctx.createLinearGradient(0, 300, 0, 50);
        gradientFill.addColorStop(0, "rgba(255, 255, 255, 0)");
        gradientFill.addColorStop(1, "rgba(255, 54, 54, 0.3)"); // Red Fade

        var myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: "Power (W)",
                    borderColor: "#FF3636",
                    pointBorderColor: "#FF3636",
                    pointBackgroundColor: "#fff",
                    pointHoverBackgroundColor: "#FF3636",
                    pointBorderWidth: 2,
                    pointHoverRadius: 6,
                    pointRadius: 4,
                    fill: true,
                    backgroundColor: gradientFill,
                    borderWidth: 3,
                    tension: 0.4, // Smooth curves
                    data: []
                }]
            },
            options: {
                layout: { padding: { left: 15, right: 15, top: 20, bottom: 10 } },
                maintainAspectRatio: false,
                legend: { display: false },
                tooltips: {
                    backgroundColor: '#fff',
                    titleFontColor: '#333',
                    bodyFontColor: '#666',
                    borderColor: '#ddd',
                    borderWidth: 1,
                },
                scales: {
                    yAxes: [{
                        ticks: { fontColor: "#9a9a9a", stepSize: 1000, beginAtZero: true, suggestedMax: 5000 },
                        gridLines: { drawBorder: false, color: "rgba(0,0,0,0.05)" }
                    }],
                    xAxes: [{
                        gridLines: { display: false },
                        ticks: { fontColor: "#9a9a9a", maxTicksLimit: 6 }
                    }]
                }
            }
        });

        // --- 2. Real-time Data Polling (Backend Connected) ---
        function updateRealTimeData() {
            $.ajax({
                url: '/api/measurements/history',
                type: 'GET',
                cache: false,
                success: function(response) {
                    
                    // A. System Status Badge
                    // A. System Status Badge
                    var statusBadge = $('#device-status');
                    statusBadge.removeClass('badge-danger badge-success badge-warning'); // Reset classes

                    if(response.status === 'Online') {
                        statusBadge.addClass('badge-success').text('ONLINE');
                    } else if(response.status === 'Relay OFF') {
                        statusBadge.addClass('badge-warning').text('POWER CUT (OFF)');
                        statusBadge.css('color', '#fff'); // Ensure readable text
                    } else {
                        statusBadge.addClass('badge-danger').text('OFFLINE');
                    }

                    // B. Update Chart & Metrics
                    if(response.history && response.history.length > 0) {
                        
                        // Chart Data (Oldest -> Newest)
                        var historyData = response.history; 
                        var labels = [];
                        var powerData = [];

                        historyData.forEach(function(item) {
                            var date = new Date(item.created_at);
                            // Format HH:mm:ss
                            var hours = ("0" + date.getHours()).slice(-2);
                            var minutes = ("0" + date.getMinutes()).slice(-2);
                            var seconds = ("0" + date.getSeconds()).slice(-2);
                            labels.push(hours + ":" + minutes + ":" + seconds);
                            powerData.push(item.power);
                        });

                        myChart.data.labels = labels;
                        myChart.data.datasets[0].data = powerData;
                        myChart.update();

                        // Metrics Cards (Latest Data)
                        var latest = response.history[response.history.length - 1]; 
                        if(latest) {
                            $('#voltage-val').text(latest.voltage);
                            $('#current-val').text(latest.current);
                            $('#power-val').text(latest.power);
                            $('#energy-val').text(latest.energy);
                            $('#frequency-val').text(latest.frequency);
                            $('#pf-val').text(latest.power_factor);
                        }
                    }

                    // C. Update Heatmap
                    if(response.heatmap) {
                        response.heatmap.forEach(function(val, index) {
                            var boxFn = $('#heatmap-' + index);
                            
                            // 1. Reset Styles first
                            // We remove specific inline styles that JS might have added previously
                            boxFn.css('background-color', '').css('color', '').css('border', '');

                            if (val > 0) {
                                // ACTIVE STATE
                                boxFn.removeClass('empty-day'); // It has data, so handled by inline colors
                                boxFn.css('color', '#fff'); 

                                if (val < 500) { 
                                    boxFn.css('background-color', '#2DCE89'); // Low Green
                                } 
                                else if (val < 1500) { 
                                    boxFn.css('background-color', '#FFD600').css('color', '#212529'); // Med Yellow
                                } 
                                else { 
                                    boxFn.css('background-color', '#F5365C'); // High Red
                                }
                            } else {
                                // EMPTY STATE
                                // We add the class and let CSS handle the Dark/Light coloring!
                                boxFn.addClass('empty-day');
                            }
                        });
                    }
                }
            });
        }

        setInterval(updateRealTimeData, 2000); // Poll every 2 seconds
        updateRealTimeData();
    });
</script>
@endpush