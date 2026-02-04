@extends('layouts.app')

@section('title', 'Control & Automation - Power Monitoring')

@section('page-title', 'Control & Automation')
@section('page-icon', 'ui-2_settings-90')

@section('content')
<style>
/* Custom Toggle Switch (Same as Alarms) */
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
  background-color: #f96332;
}
input:checked + .slider:before {
  transform: translateX(14px);
}
</style>

<div class="content" style="padding-top: 0;">
    <div class="panel-header panel-header-sm" style="height: 50px !important; background: transparent !important; box-shadow: none;"></div>

    <div class="row">
        <!-- 1. MASTER RELAY CONTROL (Premium Card) -->
        <div class="col-md-5">
            <div class="card card-user shadow-lg border-0" style="min-height: 400px; overflow: hidden; border-radius: 20px;">
                <!-- Artistic Background Header -->
                <div class="card-image" style="height: 130px; background: linear-gradient(to right, #f96332, #ff5722); position: relative; border-radius: 20px 20px 0 0;">
                    <div class="text-white font-weight-bold text-center pt-4" style="font-size: 1.4rem; letter-spacing: 1px;">
                        CONTROL PANEL
                    </div>
                </div>
                
                <div class="card-body text-center pt-0" style="position: relative; margin-top: -60px;">
                    <form action="{{ route('control.toggle') }}" method="POST">
                        @csrf
                        <!-- Solid Power Button (Fixed Centering) -->
                        <div class="author">
                             <button type="submit" class="btn btn-round mx-auto" 
                                style="width: 130px; height: 130px; padding: 0; cursor: pointer; border: 5px solid rgba(255,255,255,0.3); border-radius: 50%;
                                display: flex; justify-content: center; align-items: center;
                                background: {{ $relayState ? 'linear-gradient(145deg, #2dce89, #2dcecc)' : 'linear-gradient(145deg, #f5365c, #f56036)' }};
                                box-shadow: 0 10px 25px {{ $relayState ? 'rgba(45, 206, 137, 0.5)' : 'rgba(245, 54, 92, 0.5)' }}, inset 0 2px 5px rgba(255,255,255,0.2); margin-bottom: 20px;">
                                <i class="fas fa-power-off text-white" style="font-size: 4.5rem; margin: 0; padding: 0; transform: translateX(-3px); text-shadow: 0 2px 4px rgba(0,0,0,0.2);"></i>
                            </button>
                            <h3 class="title font-weight-bold mb-0 {{ $relayState ? 'text-success' : 'text-danger' }}">
                                Master Control
                            </h3>
                            <p class="description text-muted font-weight-bold">
                                {{ $relayState ? 'Main Power Connected' : 'Power Cut (Relay Open)' }}
                            </p>
                        </div>

                        <div class="button-container mt-4">
                            <button type="submit" class="btn {{ $relayState ? 'btn-danger' : 'btn-success' }} btn-round btn-lg font-weight-bold shadow-sm px-5">
                                {{ $relayState ? 'TURN OFF POWER' : 'ACTIVATE POWER' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- 2. AUTOMATION & SCHEDULE (Right Column) -->
        <div class="col-md-7">
            
            <!-- Auto Load Shedding -->
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 20px;">
                <div class="card-header bg-white border-0 pt-4 px-4" style="border-radius: 20px 20px 0 0;">
                    <div class="d-flex align-items-center">
                        <div class="icon icon-shape bg-warning text-white rounded-circle shadow-sm mr-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                            <i class="now-ui-icons tech_controller-modern" style="font-size: 1.5rem;"></i>
                        </div>
                        <div>
                            <h5 class="title mb-0 font-weight-bold text-dark">Smart Load Shedding</h5>
                            <small class="text-muted">Auto-protect system from overload</small>
                        </div>
                    </div>
                </div>
                <div class="card-body px-4 pb-4">
                    <form action="{{ route('control.settings') }}" method="POST">
                        @csrf
                        <input type="hidden" name="update_cutoff" value="1">
                        
                        <div class="d-flex align-items-center justify-content-between my-3 p-3 rounded" style="background: #f8f9fa; border-radius: 15px !important;">
                            <div>
                                <h6 class="mb-1 font-weight-bold text-dark">Auto Cut-Off Status</h6>
                                <span class="badge {{ $autoCutoff ? 'badge-success' : 'badge-secondary' }}">{{ $autoCutoff ? 'ENABLED' : 'DISABLED' }}</span>
                            </div>
                            <label class="toggle-switch m-0">
                                <input type="checkbox" name="auto_cutoff_enabled" {{ $autoCutoff ? 'checked' : '' }}>
                                <span class="slider"></span>
                            </label>
                        </div>

                        <div class="form-group mt-3">
                            <label class="font-weight-bold text-muted small text-uppercase">Max Power Limit (Watt)</label>
                            <div class="input-group">
                                <input type="number" name="max_power_cutoff" class="form-control" value="{{ $maxPower }}" style="height: 45px; border-right: 0; border-top-left-radius: 30px; border-bottom-left-radius: 30px;">
                                <div class="input-group-append">
                                    <span class="input-group-text bg-white font-weight-bold text-muted" style="border-left: 0; border-top-right-radius: 30px; border-bottom-right-radius: 30px;">Watts</span>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-round btn-block shadow-none font-weight-bold mt-3">Save Protection Settings</button>
                    </form>
                </div>
            </div>

            <!-- Schedule -->
             <div class="card shadow-sm border-0" style="border-radius: 20px;">
                <div class="card-header bg-white border-0 pt-4 px-4" style="border-radius: 20px 20px 0 0;">
                     <div class="d-flex align-items-center">
                        <div class="icon icon-shape bg-info text-white rounded-circle shadow-sm mr-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                            <i class="now-ui-icons ui-1_calendar-60" style="font-size: 1.5rem;"></i>
                        </div>
                        <div>
                             <h5 class="title mb-0 font-weight-bold text-dark">Power Scheduler</h5>
                             <small class="text-muted">Automated ON/OFF Timer</small>
                        </div>
                    </div>
                </div>
                <div class="card-body px-4 pb-4">
                     <form action="{{ route('control.settings') }}" method="POST">
                        @csrf
                        <input type="hidden" name="update_schedule" value="1">

                         <div class="d-flex align-items-center justify-content-between my-3 p-3 rounded" style="background: #f8f9fa; border-radius: 15px !important;">
                            <div>
                                <h6 class="mb-1 font-weight-bold text-dark">Scheduler Status</h6>
                                <span class="badge {{ $scheduleEnabled ? 'badge-info' : 'badge-secondary' }}">{{ $scheduleEnabled ? 'ACTIVE' : 'INACTIVE' }}</span>
                            </div>
                            <label class="toggle-switch m-0">
                                <input type="checkbox" name="schedule_enabled" {{ $scheduleEnabled ? 'checked' : '' }}>
                                <span class="slider"></span>
                            </label>
                        </div>

                         <div class="row">
                             <div class="col-6">
                                 <div class="form-group text-center border p-2" style="border-radius: 20px;">
                                     <label class="small font-weight-bold text-success mb-2"><i class="now-ui-icons media-1_button-power"></i> AUTO ON</label>
                                     <input type="time" name="schedule_time_on" class="form-control text-center font-weight-bold" style="font-size: 1.2em; height: auto; border-radius: 15px;" value="{{ $scheduleOn }}">
                                 </div>
                             </div>
                             <div class="col-6">
                                  <div class="form-group text-center border p-2" style="border-radius: 20px;">
                                     <label class="small font-weight-bold text-danger mb-2"><i class="now-ui-icons media-1_button-power"></i> AUTO OFF</label>
                                     <input type="time" name="schedule_time_off" class="form-control text-center font-weight-bold" style="font-size: 1.2em; height: auto; border-radius: 15px;" value="{{ $scheduleOff }}">
                                 </div>
                             </div>
                         </div>
                         <button type="submit" class="btn btn-outline-info btn-round btn-block shadow-none font-weight-bold mt-3">Update Schedule</button>
                     </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
