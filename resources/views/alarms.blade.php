@extends('layouts.app')

@section('title', 'Alarms & Notifications - Power Monitoring')

@section('page-title', 'Alarms & Notifications')
@section('page-icon', 'ui-1_bell-53')

@section('content')
<style>
/* Custom Toggle Switch */
.toggle-switch {
  position: relative;
  display: inline-flex;
  align-items: center;
  width: 34px;
  height: 20px;
  margin-right: 12px;
  margin-bottom: 0;
  vertical-align: middle;
}
.toggle-switch input { 
  opacity: 0;
  width: 0;
  height: 0;
}
.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
  border-radius: 34px;
}
.slider:before {
  position: absolute;
  content: "";
  height: 14px;
  width: 14px;
  left: 3px;
  bottom: 3px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
  border-radius: 50%;
}
input:checked + .slider {
  background-color: #f96332; /* Primary Orange */
}
input:focus + .slider {
  box-shadow: 0 0 1px #f96332;
}
input:checked + .slider:before {
  -webkit-transform: translateX(14px);
  -ms-transform: translateX(14px);
  transform: translateX(14px);
}
</style>
<div class="content" style="padding-top: 0;">
    <div class="panel-header panel-header-sm" style="height: 50px !important; background: transparent !important; box-shadow: none;"></div>
    <div class="row">
        <!-- LEFT COLUMN: Alarm History -->
        <div class="col-lg-8 col-md-12">
            <div class="card shadow-lg border-0" style="border-radius: 20px;">
                <div class="card-header d-flex justify-content-between align-items-center" style="padding: 20px 25px; border-bottom: 0;">
                    <div>
                        <h5 class="card-title font-weight-bold m-0" style="color: #32325d;">Alarm History</h5>
                        <p class="category text-muted small m-0">Log of all detected anomalies</p>
                    </div>
                    <a href="{{ route('alarms') }}" class="btn btn-round btn-outline-primary btn-icon btn-sm shadow-none"><i class="now-ui-icons loader_refresh"></i></a>
                </div>
                <div class="card-body px-0 pt-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-items-center">
                            <thead class="text-primary">
                                <th class="pl-4" style="font-size: 0.7rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;">Timestamp</th>
                                <th style="font-size: 0.7rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;">Type</th>
                                <th style="font-size: 0.7rem; font-weight: 800; text-transform: uppercase;">Message</th>
                                <th style="font-size: 0.7rem; font-weight: 800; text-transform: uppercase;">Status</th>
                                <th class="text-right pr-4" style="font-size: 0.7rem; font-weight: 800;">Action</th>
                            </thead>
                            <tbody>
                                @forelse($alarms as $alarm)
                                <tr>
                                    <td class="pl-4 align-middle">
                                        <div class="d-flex flex-column">
                                            <span class="font-weight-bold text-dark">{{ $alarm->created_at->format('H:i:s') }}</span>
                                            <small class="text-muted">{{ $alarm->created_at->format('d M Y') }}</small>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        @php
                                            $badgeColor = 'badge-default';
                                            if(str_contains($alarm->type, 'Voltage')) $badgeColor = 'badge-info';
                                            elseif(str_contains($alarm->type, 'Current')) $badgeColor = 'badge-warning';
                                            elseif(str_contains($alarm->type, 'Overload')) $badgeColor = 'badge-danger';
                                        @endphp
                                        <span class="badge {{ $badgeColor }} badge-pill px-3">{{ $alarm->type }}</span>
                                    </td>
                                    <td class="align-middle text-muted small" style="white-space: normal; line-height: 1.4; max-width: 250px;">
                                        {{ $alarm->message }}
                                    </td>
                                    <td class="align-middle">
                                        @if($alarm->status == 'active')
                                            <span class="badge badge-danger badge-pill">ACTIVE</span>
                                        @else
                                            <span class="badge badge-success badge-pill">RESOLVED</span>
                                        @endif
                                    </td>
                                    <td class="text-right pr-4 align-middle">
                                        @if($alarm->status == 'active')
                                            @if(auth()->check() && auth()->user()->role === 'admin')
                                            <form action="{{ route('alarms.resolve', $alarm->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success btn-icon btn-round shadow-sm" title="Mark as Resolved">
                                                    <i class="now-ui-icons ui-1_check"></i>
                                                </button>
                                            </form>
                                            @else
                                                <span class="text-danger small font-weight-bold">Active</span>
                                            @endif
                                        @else
                                            <i class="now-ui-icons ui-1_check text-success mr-3" style="font-size: 1.2em;"></i>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="now-ui-icons emoticons_satisfied mb-2 d-block" style="font-size: 2em; opacity: 0.5;"></i>
                                        No alarms recorded yet. System is healthy!
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center mt-3 pb-3">
                        {{ $alarms->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: Settings -->
        <div class="col-lg-4 col-md-12">
            <form action="{{ route('alarms.settings') }}" method="POST">
                @csrf
                
                <!-- 1. THRESHOLD SETTINGS (Admin Only) -->
                @if(auth()->check() && auth()->user()->role === 'admin')
                <div class="card mb-4 shadow-lg border-0" style="border-radius: 20px;">
                    <div class="card-header border-0 pb-2 pt-4 px-4" style="border-radius: 20px 20px 0 0;">
                        <h6 class="text-uppercase text-muted font-weight-bold mb-0" style="font-size: 0.75rem;">Safety Thresholds</h6>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="small text-muted font-weight-bold mb-1 ml-2">Max Volt (V)</label>
                                <input type="number" name="alarm_max_voltage" class="form-control rounded-pill px-3" value="{{ $settings['alarm_max_voltage'] ?? 240 }}">
                            </div>
                            <div class="col-6 mb-3">
                                <label class="small text-muted font-weight-bold mb-1 ml-2">Max Amp (A)</label>
                                <input type="number" step="0.1" name="alarm_max_current" class="form-control rounded-pill px-3" value="{{ $settings['alarm_max_current'] ?? 15 }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <label class="small text-muted font-weight-bold mb-1 ml-2">Max Watt (W)</label>
                                <input type="number" name="alarm_max_power" class="form-control rounded-pill px-3" value="{{ $settings['alarm_max_power'] ?? 3000 }}">
                            </div>
                            <div class="col-6">
                                <label class="small text-muted font-weight-bold mb-1 ml-2">Min PF</label>
                                <input type="number" step="0.01" max="1" min="0" name="alarm_min_pf" class="form-control rounded-pill px-3" value="{{ $settings['alarm_min_pf'] ?? 0.85 }}">
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- 2. NOTIFICATION CHANNELS (Open for Everyone) -->
                <div class="card shadow-lg border-0" style="border-radius: 20px;">
                    <div class="card-header d-flex justify-content-between align-items-center border-0 py-4 px-4" 
                         style="border-radius: 20px; cursor: pointer;" 
                         data-toggle="collapse" data-target="#notificationChannels" aria-expanded="false">
                        <h6 class="text-uppercase text-muted font-weight-bold mb-0" style="font-size: 0.75rem;">Notification Channels</h6>
                        <i class="now-ui-icons arrows-1_minimal-down text-primary" style="font-size: 1.2em;"></i>
                    </div>
                    
                    <div id="notificationChannels" class="collapse show"> <!-- Show by default now -->
                        <div class="card-body px-4 pb-4 pt-0">
                            <hr class="mt-0 mb-4">
                            
                            <!-- EMAIL ITEM -->
                            <div class="channel-group mb-4">
                                <div class="d-flex align-items-center mb-2">
                                    <label class="toggle-switch">
                                        <input type="checkbox" name="notify_email" {{ ($settings['notify_email'] ?? 0) ? 'checked' : '' }}>
                                        <span class="slider"></span>
                                    </label>
                                    <div class="d-flex align-items-center">
                                        <i class="now-ui-icons ui-1_email-85 mr-2 text-primary" style="font-size: 1.1em;"></i>
                                        <span class="font-weight-bold text-dark">Email Notification</span>
                                    </div>
                                </div>
                                <div class="pl-4 ml-2">
                                    <input type="email" name="target_email" class="form-control bg-light rounded-pill px-3" placeholder="Enter email address..." value="{{ $settings['target_email'] ?? '' }}">
                                </div>
                            </div>

                            <div class="dropdown-divider mb-4"></div>

                            <!-- WHATSAPP ITEM -->
                            <div class="channel-group mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="d-flex align-items-center">
                                        <label class="toggle-switch">
                                            <input type="checkbox" name="notify_whatsapp" {{ ($settings['notify_whatsapp'] ?? 0) ? 'checked' : '' }}>
                                            <span class="slider"></span>
                                        </label>
                                        <div class="d-flex align-items-center">
                                            <i class="fab fa-whatsapp mr-2 text-success" style="font-size: 1.3em;"></i>
                                            <span class="font-weight-bold text-dark">WhatsApp Alert</span>
                                        </div>
                                    </div>
                                    <span class="badge badge-secondary badge-pill" id="wa-status-badge">Check..</span>
                                </div>
                                
                                <div class="pl-4 ml-2">
                                    <input type="text" name="target_whatsapp" class="form-control bg-light mb-3 rounded-pill px-3" placeholder="Ex: 628123456789" value="{{ $settings['target_whatsapp'] ?? '' }}">
                                    
                                    <!-- Compact WA Gateway Control -->
                                    <div class="rounded-lg border p-3 bg-white shadow-sm" style="border-radius: 15px;">
                                        <div id="wa-state-disconnected" style="display: none;">
                                            <button type="button" class="btn btn-outline-success btn-sm btn-block btn-round m-0 font-weight-bold" onclick="startConnection()">
                                                <i class="now-ui-icons tech_mobile mr-1"></i> Connect Device
                                            </button>
                                        </div>
                                        <div id="wa-state-loading" style="display: none;" class="text-center py-2">
                                            <div class="spinner-border spinner-border-sm text-success" role="status"></div>
                                            <span class="small ml-2 text-muted">Connecting...</span>
                                        </div>
                                        <div id="wa-state-qr" style="display: none;" class="text-center">
                                            <div id="qrcode-canvas" class="d-flex justify-content-center mb-2 p-2 bg-white rounded border mx-auto" style="width: fit-content;"></div>
                                            <small class="text-muted d-block font-weight-bold mb-2">Scan with WhatsApp</small>
                                            <button type="button" class="btn btn-link btn-sm text-danger p-0 m-0" onclick="cancelConnection()">Cancel</button>
                                        </div>
                                        <div id="wa-state-connected" style="display: none;">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div class="text-success font-weight-bold small"><i class="now-ui-icons ui-1_check mr-1"></i>Connected</div>
                                                <button type="button" class="btn btn-link text-danger p-0 m-0 font-weight-bold small" onclick="logoutWA()">Disconnect</button>
                                            </div>
                                            <small class="text-muted d-block mt-1 text-truncate" id="wa-phone-info">...</small>
                                        </div>
                                         <div id="wa-state-error" style="display: none;">
                                            <div class="d-flex align-items-center justify-content-center text-danger">
                                                <i class="now-ui-icons objects_support-17 mr-2"></i>
                                                <small class="font-weight-bold">Gateway Offline</small>
                                            </div>
                                            <small class="text-muted d-block text-center mt-1">Is the server running?</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="dropdown-divider mb-4"></div>

                            <!-- TELEGRAM ITEM -->
                            <div class="channel-group mb-4">
                                 <div class="d-flex align-items-center mb-2">
                                    <label class="toggle-switch">
                                        <input type="checkbox" name="notify_telegram" {{ ($settings['notify_telegram'] ?? 0) ? 'checked' : '' }}>
                                        <span class="slider"></span>
                                    </label>
                                    <div class="d-flex align-items-center">
                                        <i class="fab fa-telegram mr-2 text-info" style="font-size: 1.3em;"></i>
                                        <span class="font-weight-bold text-dark">Telegram Bot</span>
                                    </div>
                                </div>
                                <div class="pl-4 ml-2">
                                    <div class="form-group mb-2">
                                        <input type="text" name="telegram_bot_token" class="form-control bg-light rounded-pill px-3" placeholder="Bot Token (From @BotFather)" value="{{ $settings['telegram_bot_token'] ?? '' }}">
                                    </div>
                                    <div class="form-group mb-0">
                                        <input type="text" name="telegram_chat_id" class="form-control bg-light rounded-pill px-3" placeholder="Chat ID" value="{{ $settings['telegram_chat_id'] ?? '' }}">
                                    </div>
                                </div>
                            </div>

                            <!-- BUTTON SAVE -->
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary btn-round btn-block font-weight-bold shadow-none mb-0">SAVE NOTIFICATION CONFIG</button>
                            </div>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- QR Code Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    const GATEWAY_URL = "http://localhost:3000";
    let qrObj = null;
    let pollInterval = null;
    let isConnecting = false;

    $(document).ready(function() {
        checkWAStatus();
        pollInterval = setInterval(checkWAStatus, 3000); // Poll status
    });

    function showState(state) {
        // Hide all states first
        $('#wa-state-disconnected, #wa-state-loading, #wa-state-qr, #wa-state-connected, #wa-state-error').hide();
        // Show target state
        $('#' + state).fadeIn(200);
    }

    function checkWAStatus() {
        $.ajax({
            url: GATEWAY_URL + '/status',
            type: 'GET',
            timeout: 2000,
            success: function(resp) {
                // Update Badge Badge
                var badge = $('#wa-status-badge');
                badge.text(resp.status);
                
                if (resp.status === 'READY' || resp.status === 'AUTHENTICATED') {
                    badge.removeClass('badge-secondary badge-danger').addClass('badge-success');
                    showState('wa-state-connected');
                    if(resp.info) {
                        $('#wa-phone-info').text(`${resp.info.pushname}`);
                    }
                    isConnecting = false;
                } 
                else if (resp.status === 'QR_READY' && resp.qr) {
                    badge.removeClass('badge-secondary badge-danger').addClass('badge-warning');
                    if (isConnecting) {
                        showState('wa-state-qr');
                        // Render QR
                        $('#qrcode-canvas').empty();
                        new QRCode(document.getElementById("qrcode-canvas"), {
                            text: resp.qr,
                            width: 200, // Increased size for readability check
                            height: 200,
                            colorDark : "#000000",
                            colorLight : "#ffffff",
                            correctLevel : QRCode.CorrectLevel.L
                        });
                    } else {
                        showState('wa-state-disconnected');
                    }
                }
                else {
                    badge.removeClass('badge-success badge-danger').addClass('badge-secondary');
                     if (isConnecting) {
                        showState('wa-state-loading');
                     } else {
                        showState('wa-state-disconnected');
                     }
                }
            },
            error: function() {
                $('#wa-status-badge').text('OFFLINE').removeClass('badge-success badge-secondary').addClass('badge-danger');
                showState('wa-state-error');
            }
        });
    }

    function startConnection() {
        isConnecting = true;
        showState('wa-state-loading');
        // Trigger re-init check 
        checkWAStatus();
    }

    function cancelConnection() {
        isConnecting = false;
        checkWAStatus();
    }

    function logoutWA() {
        if(!confirm('Disconnect this number? You will need to re-scan to connect again.')) return;
        $.ajax({
            url: GATEWAY_URL + '/logout',
            type: 'POST',
            success: function() {
                isConnecting = false; // Reset flow
                alert('Disconnected successfully.');
                checkWAStatus();
            }
        });
    }
</script>
@endpush
