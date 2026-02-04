@extends('layouts.app')

@section('title', 'History & Logs - Power Monitoring')
@section('page-title', 'History & Logs')
@section('page-icon', 'design_bullet-list-67')

@section('content')
<style>
    /* Professional Table Styling */
    .card-clean {
        box-shadow: 0 10px 30px -12px rgba(0, 0, 0, 0.42), 0 4px 25px 0px rgba(0, 0, 0, 0.12), 0 8px 10px -5px rgba(0, 0, 0, 0.2);
        border: 0;
        background-color: #fff;
        border-radius: 20px;
    }
    .table-responsive {
        overflow-x: auto;
    }
    .table-history {
        width: 100%;
        margin-bottom: 0;
        background-color: transparent;
        border-collapse: separate; 
        border-spacing: 0;
    }
    .table-history thead th {
        background-color: #f6f9fc;
        color: #8898aa;
        border-color: #f6f9fc;
        text-transform: uppercase;
        font-size: 0.7rem;
        letter-spacing: 1px;
        font-weight: 800; /* Inter Bold */
        padding: 15px 24px;
        border-bottom: 1px solid #e9ecef;
        text-align: center;
    }
    .table-history thead th:first-child {
        border-top-left-radius: 0;
        padding-left: 24px;
    }
    .table-history thead th:last-child {
        border-top-right-radius: 0;
        padding-right: 24px;
    }
    .table-history tbody td {
        padding: 1rem 1.5rem;
        vertical-align: middle;
        border-top: 1px solid #e9ecef;
        font-size: 0.85rem; /* 14px */
        color: #525f7f;
        font-weight: 500; /* Inter Medium */
        font-variant-numeric: tabular-nums; /* Aligns numbers perfectly */
        text-align: center;
    }
    .table-history tbody tr:hover td {
        background-color: #f6f9fc;
        color: #172b4d;
        cursor: default;
    }
    .table-history tbody tr:last-child td {
        border-bottom: 0;
    }
    .table-history tbody tr:last-child td:first-child {
        border-bottom-left-radius: 20px;
    }
    .table-history tbody tr:last-child td:last-child {
        border-bottom-right-radius: 20px;
    }
    
    /* Control Toolbar Styles */
    .control-label {
        font-size: 0.75rem;
        color: #8898aa;
        font-weight: 700;
        margin-right: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .custom-select-pill {
        height: calc(1.5em + 0.75rem + 2px);
        padding: 0.375rem 1.75rem 0.375rem 1rem;
        font-size: 0.875rem;
        line-height: 1.5;
        border-radius: 20px;
        border: 1px solid #dee2e6;
        color: #32325d;
        font-weight: 600;
        background-color: #fff;
        box-shadow: 0 1px 3px rgba(50,50,93,.15), 0 1px 0 rgba(0,0,0,.02);
        transition: all 0.15s ease;
    }
    .custom-select-pill:focus {
        border-color: #f96332;
        box-shadow: 0 1px 3px rgba(249, 99, 50, .25), 0 1px 0 rgba(0,0,0,.02);
    }
    
    /* Pagination Tweaks */
    .pagination {
        justify-content: center;
        margin-top: 0;
        margin-bottom: 0;
    }
    .page-item .page-link {
        border: 0;
        border-radius: 50% !important;
        margin: 0 3px;
        color: #525f7f;
        background: transparent;
        font-weight: 600;
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }
    .page-item.active .page-link {
        background-color: #f96332; /* Primary Orange */
        color: #fff;
        box-shadow: 0 4px 6px rgba(50, 50, 93, 0.11), 0 1px 3px rgba(0, 0, 0, 0.08);
        transform: scale(1.1);
    }
</style>

<div class="content" style="padding-top: 0;">
    <div class="panel-header panel-header-sm" style="height: 50px !important; background: transparent !important; box-shadow: none;"></div>

    <div class="row">
        <div class="col-md-12">
            <div class="card card-clean">
                <div class="card-header bg-white border-0" style="padding: 25px 30px; border-radius: 20px 20px 0 0;">
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <h4 class="card-title m-0 font-weight-bold" style="color: #32325d;">Data Log History</h4>
                            <p class="text-muted small mb-0">Historical power measurements</p>
                        </div>
                        <div class="col-md-8 d-flex justify-content-end align-items-center">
                            <form action="{{ route('history') }}" method="GET" class="d-flex align-items-center mr-4 mb-0">
                                <div class="d-flex align-items-center mr-3 mb-0">
                                    <label class="control-label mb-0">SHOW</label>
                                    <select name="limit" class="form-control custom-select-pill" onchange="this.form.submit()" style="width: auto;">
                                        <option value="10" {{ $limit == 10 ? 'selected' : '' }}>10</option>
                                        <option value="20" {{ $limit == 20 ? 'selected' : '' }}>20</option>
                                        <option value="50" {{ $limit == 50 ? 'selected' : '' }}>50</option>
                                        <option value="100" {{ $limit == 100 ? 'selected' : '' }}>100</option>
                                    </select>
                                </div>

                                <div class="d-flex align-items-center mb-0">
                                    <label class="control-label mb-0">INTERVAL</label>
                                    <select name="interval" class="form-control custom-select-pill" onchange="this.form.submit()" style="width: auto; min-width: 140px;">
                                        <option value="1s" {{ $interval == '1s' ? 'selected' : '' }}>Realtime (1s)</option>
                                        <option value="5s" {{ $interval == '5s' ? 'selected' : '' }}>5s Aggregated</option>
                                        <option value="1m" {{ $interval == '1m' ? 'selected' : '' }}>1m Aggregated</option>
                                    </select>
                                </div>
                            </form>

                            <!-- Retention Settings (Admin Only) -->
                            @if(auth()->check() && auth()->user()->role === 'admin')
                            <button type="button" class="btn btn-outline-secondary btn-round mr-2 shadow-sm border-0 font-weight-bold text-muted bg-light" data-toggle="modal" data-target="#settingsModal" style="padding: 10px 15px; font-size: 0.8rem;">
                                <i class="now-ui-icons ui-1_settings-gear-63 mr-1"></i> Settings
                            </button>
                            @endif

                            <!-- Export Buttons -->
                            <div class="btn-group">
                                <a href="{{ route('history.export', ['format' => 'csv']) }}" class="btn btn-primary btn-round shadow-lg" title="Export CSV" style="font-weight: 700; padding: 10px 20px; font-size: 0.8rem;">
                                    <i class="now-ui-icons arrows-1_cloud-download-93 mr-1"></i> CSV
                                </a>
                                <a href="{{ route('history.export', ['format' => 'excel']) }}" class="btn btn-success btn-round shadow-lg" title="Export Excel" style="font-weight: 700; padding: 10px 20px; font-size: 0.8rem;">
                                    <i class="now-ui-icons arrows-1_cloud-download-93 mr-1"></i> Excel
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pt-0">
                    <div class="table-responsive">
                        <table class="table-history align-items-center">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>Timestamp</th>
                                    <th>Voltage <span style="font-size: 0.7em; opacity: 0.7;">(V)</span></th>
                                    <th>Current <span style="font-size: 0.7em; opacity: 0.7;">(A)</span></th>
                                    <th>Power <span style="font-size: 0.7em; opacity: 0.7;">(W)</span></th>
                                    <th>Energy <span style="font-size: 0.7em; opacity: 0.7;">(kWh)</span></th>
                                    <th>PF</th>
                                    <th>Frequency <span style="font-size: 0.7em; opacity: 0.7;">(Hz)</span></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($measurements as $data)
                                <tr>
                                    <td class="text-muted">{{ ($measurements->currentPage() - 1) * $measurements->perPage() + $loop->iteration }}</td>
                                    <td class="font-weight-bold text-dark">{{ $data->created_at->format('Y-m-d H:i:s') }}</td>
                                    <td>{{ number_format($data->voltage, 1) }}</td>
                                    <td>{{ number_format($data->current, 2) }}</td>
                                    <td class="font-weight-bold text-primary">{{ number_format($data->power, 0) }}</td>
                                    <td>{{ number_format($data->energy, 2) }}</td>
                                    <td>{{ number_format($data->power_factor, 2) }}</td>
                                    <td>{{ number_format($data->frequency, 1) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <p class="text-muted mb-0">No data available for the selected interval.</p>
                                    </td>

                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination Area -->
                    <div class="px-4 py-4 border-top d-flex justify-content-center" style="background: transparent; border-radius: 0 0 20px 20px;">
                        {{ $measurements->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Settings Modal (Premium) - Admin Only -->
@if(auth()->check() && auth()->user()->role === 'admin')
<div class="modal fade" id="settingsModal" tabindex="-1" role="dialog" aria-labelledby="settingsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <form action="{{ route('history.settings') }}" method="POST">
        @csrf
        <div class="modal-content shadow-lg border-0" style="border-radius: 20px;">
          <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
            <h5 class="modal-title font-weight-bold" id="settingsModalLabel">Data Retention</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body pt-2 px-4">
            <p class="text-muted small mb-4">Configure how long the system keeps historical measurement data before automatically deleting it.</p>
            <div class="form-group">
                <label for="retention_days" class="text-primary font-weight-bold small text-uppercase ml-2">Keep data for (days)</label>
                <div class="input-group">
                    <input type="number" class="form-control rounded-pill px-3" name="retention_days" id="retention_days" value="{{ $retention }}" min="1" max="3650" required style="font-size: 1.1rem; font-weight: 600;">
                    <div class="input-group-append" style="margin-left: -50px; z-index: 5;">
                        <span class="input-group-text bg-transparent border-0 font-weight-bold text-muted small mt-1">DAYS</span>
                    </div>
                </div>
            </div>
          </div>
          <div class="modal-footer border-top-0 px-4 pb-4">
            <button type="button" class="btn btn-secondary btn-simple" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary btn-round shadow-lg px-4 font-weight-bold">Save Settings</button>
          </div>
        </div>
    </form>
  </div>
</div>
@endif
@endsection
