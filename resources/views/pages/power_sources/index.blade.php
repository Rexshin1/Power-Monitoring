@extends('layouts.app')

@section('title', 'Power Sources')
@section('page-title', 'Power Sources')
@section('page-icon', 'fas fa-bolt')

@section('content')
<style>
    /* Premium Table Styling (Matched with User Management Page) */
    .card-clean {
        box-shadow: 0 10px 30px -12px rgba(0, 0, 0, 0.42), 0 4px 25px 0px rgba(0, 0, 0, 0.12), 0 8px 10px -5px rgba(0, 0, 0, 0.2);
        border: 0;
        background-color: #fff;
        border-radius: 20px;
    }
    .table-responsive {
        overflow-x: auto;
    }
    .table-custom {
        width: 100%;
        margin-bottom: 0;
        background-color: transparent;
        border-collapse: separate; 
        border-spacing: 0;
    }
    .table-custom thead th {
        background-color: #f6f9fc;
        color: #8898aa;
        border-color: #f6f9fc;
        text-transform: uppercase;
        font-size: 0.7rem;
        letter-spacing: 1px;
        font-weight: 800;
        padding: 15px 24px;
        border-bottom: 1px solid #e9ecef;
        text-align: center;
    }
    .table-custom thead th:first-child {
        border-top-left-radius: 0;
        padding-left: 24px;
        text-align: left;
    }
    .table-custom thead th:last-child {
        border-top-right-radius: 0;
        padding-right: 24px;
        text-align: right;
    }
    .table-custom tbody td {
        padding: 1rem 1.5rem;
        vertical-align: middle;
        border-top: 1px solid #e9ecef;
        font-size: 0.9rem;
        color: #525f7f;
        font-weight: 500;
    }
    .table-custom tbody tr:hover td {
        background-color: #f6f9fc;
        color: #172b4d;
        cursor: default;
    }
    .table-custom tbody tr:last-child td {
        border-bottom: 0;
    }
    
    /* Pagination Tweaks */
    .pagination {
        justify-content: center;
        margin-top: 0;
        margin-bottom: 0;
    }
</style>

<div class="content" style="padding-top: 0;">
    <div class="panel-header panel-header-sm" style="height: 50px !important; background: transparent !important; box-shadow: none;"></div>

    <div class="row">
        <div class="col-md-12">
            <div class="card card-clean">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0" style="border-radius: 20px 20px 0 0;">
                    <div class="row align-items-center mb-3">
                        <div class="col-md-6">
                            <h4 class="card-title m-0 font-weight-bold" style="color: #32325d;">Power Source Management</h4>
                            <p class="text-muted small mb-0">Manage electricity sources (Grid/Battery) and tariffs.</p>
                        </div>
                        <div class="col-md-6 text-right">
                                <button type="button" class="btn btn-primary btn-round shadow-lg font-weight-bold px-4" data-toggle="modal" data-target="#addSourceModal">
                                    <i class="now-ui-icons ui-1_simple-add mr-1"></i> Add Source
                                </button>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pt-0">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show mx-4 mt-2" style="border-radius: 10px;">
                            <button type="button" aria-hidden="true" class="close" data-dismiss="alert" aria-label="Close">
                                <i class="now-ui-icons ui-1_simple-remove"></i>
                            </button>
                            {{ session('success') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show mx-4 mt-2" style="border-radius: 10px;">
                            <button type="button" aria-hidden="true" class="close" data-dismiss="alert" aria-label="Close">
                                <i class="now-ui-icons ui-1_simple-remove"></i>
                            </button>
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                            <table class="table-custom">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Area (Lokasi)</th>
                                        <th class="text-center">Type</th>
                                        <th class="text-center">Voltage</th>
                                        <th class="text-right">Capacity/Cost</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($powerSources as $source)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="icon icon-shape text-white rounded-circle shadow-sm mr-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background: linear-gradient(45deg, #FFD700, #fd7e14);">
                                                    <i class="fas fa-bolt"></i>
                                                </div>
                                                <span class="font-weight-bold text-dark">{{ $source->name }}</span>
                                            </div>
                                        </td>
                                        <td>{{ $source->area ?? '-' }}</td>
                                        <td class="text-center">
                                            @if($source->type == 'grid')
                                                <span class="badge badge-info rounded-pill">PLN / Grid</span>
                                            @else
                                                <span class="badge badge-primary rounded-pill">Battery</span>
                                            @endif
                                        </td>
                                        <td class="text-center">{{ $source->nominal_voltage }}V</td>
                                        <td class="text-right">
                                            @if($source->type == 'grid')
                                                Rp {{ number_format($source->cost_per_kwh, 0, ',', '.') }}
                                            @else
                                                {{ $source->capacity }} Ah
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($source->is_active)
                                                <span class="badge badge-success rounded-pill">Active</span>
                                            @else
                                                <span class="badge badge-secondary rounded-pill">Inactive</span>
                                            @endif
                                        </td>
                                        <td class="text-right">
                                            <button type="button" class="btn btn-info btn-icon btn-round edit-source-btn shadow-sm" title="Edit" 
                                                data-id="{{ $source->id }}"
                                                data-name="{{ $source->name }}"
                                                data-area="{{ $source->area }}"
                                                data-type="{{ $source->type }}"
                                                data-voltage="{{ $source->nominal_voltage }}"
                                                data-capacity="{{ $source->capacity }}"
                                                data-cost="{{ $source->cost_per_kwh }}"
                                                data-desc="{{ $source->description }}"
                                                data-active="{{ $source->is_active }}"
                                                data-toggle="modal" data-target="#editSourceModal">
                                                <i class="fas fa-edit" style="font-size: 1.1rem;"></i>
                                            </button>
                                            <form action="{{ route('power-sources.destroy', $source->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-icon btn-round shadow-sm" title="Delete">
                                                    <i class="fas fa-trash-alt" style="font-size: 1.1rem;"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <div class="d-flex flex-column align-items-center">
                                                <i class="fas fa-bolt text-muted mb-3" style="font-size: 2em; opacity:0.3;"></i>
                                                <h6 class="text-muted">Belum ada data power source.</h6>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ================= MODALS (PREMIUM STYLE) ================= -->

<!-- ADD SOURCE MODAL -->
<div class="modal fade" id="addSourceModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <form action="{{ route('power-sources.store') }}" method="POST">
            @csrf
            <div class="modal-content shadow-lg border-0" style="border-radius: 20px;">
                <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                    <h5 class="modal-title font-weight-bold">Add Power Source</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body pt-2 px-4">
                    <div class="form-group">
                        <label>Lokasi / Gedung</label>
                        <select name="area" class="form-control rounded-pill px-3 shadow-sm" style="height: unset; padding:10px;" required>
                            <option value="">-- Pilih Lokasi --</option>
                            @foreach($locations as $loc)
                                <option value="{{ $loc->name }}">{{ $loc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group"><label>Source Name</label><input type="text" name="name" class="form-control rounded-pill px-3 shadow-sm" required></div>
                    <div class="row">
                        <div class="col-md-6"><div class="form-group"><label>Type</label><select name="type" class="form-control rounded-pill px-3 shadow-sm" style="height: unset; padding:10px;"><option value="grid">Grid (PLN)</option><option value="battery">Battery/Solar</option></select></div></div>
                        <div class="col-md-6"><div class="form-group"><label>Nominal Voltage (V)</label><input type="number" name="nominal_voltage" class="form-control rounded-pill px-3 shadow-sm" required value="220"></div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6"><div class="form-group"><label>Capacity (Ah) / Grid Limit</label><input type="number" step="0.01" name="capacity" class="form-control rounded-pill px-3 shadow-sm" value="0"></div></div>
                        <div class="col-md-6"><div class="form-group"><label>Cost per kWh</label><input type="number" step="0.01" name="cost_per_kwh" class="form-control rounded-pill px-3 shadow-sm" value="1444"></div></div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 px-4 pb-4">
                    <button type="button" class="btn btn-secondary btn-round" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary btn-round shadow-lg">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- EDIT SOURCE MODAL -->
<div class="modal fade" id="editSourceModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <form id="editFormSource" action="" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content shadow-lg border-0" style="border-radius: 20px;">
                <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                    <h5 class="modal-title font-weight-bold">Edit Source</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body pt-2 px-4">
                    <div class="form-group">
                        <label>Lokasi / Gedung</label>
                        <select name="area" id="editSourceArea" class="form-control rounded-pill px-3 shadow-sm" style="height: unset; padding:10px;" required>
                            <option value="">-- Pilih Lokasi --</option>
                            @foreach($locations as $loc)
                                <option value="{{ $loc->name }}">{{ $loc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group"><label>Source Name</label><input type="text" name="name" id="editSourceName" class="form-control rounded-pill px-3 shadow-sm" required></div>
                    <div class="row">
                        <div class="col-md-6"><div class="form-group"><label>Type</label><select name="type" id="editSourceType" class="form-control rounded-pill px-3 shadow-sm" style="height: unset; padding:10px;"><option value="grid">Grid (PLN)</option><option value="battery">Battery/Solar</option></select></div></div>
                        <div class="col-md-6"><div class="form-group"><label>Nominal Voltage (V)</label><input type="number" name="nominal_voltage" id="editSourceVoltage" class="form-control rounded-pill px-3 shadow-sm" required></div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6"><div class="form-group"><label>Capacity (Ah)</label><input type="number" step="0.01" name="capacity" id="editSourceCapacity" class="form-control rounded-pill px-3 shadow-sm"></div></div>
                        <div class="col-md-6"><div class="form-group"><label>Cost per kWh</label><input type="number" step="0.01" name="cost_per_kwh" id="editSourceCost" class="form-control rounded-pill px-3 shadow-sm"></div></div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 px-4 pb-4">
                    <button type="button" class="btn btn-secondary btn-round" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary btn-round shadow-lg">Update</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Edit Location Script (Only run if button exists)
        if ($('.edit-loc-btn').length > 0) {
            $('.edit-loc-btn').on('click', function() {
                var id = $(this).data('id');
                var name = $(this).data('name');
                var code = $(this).data('code');
                var floor = $(this).data('floor');
                var category = $(this).data('category');
                var power = $(this).data('power');
                var tariff = $(this).data('tariff');

                var actionUrl = '{{ route("master-data.update", ":id") }}';
                actionUrl = actionUrl.replace(':id', id);
                $('#editFormLocation').attr('action', actionUrl);

                $('#editLocName').val(name);
                $('#editLocCode').val(code);
                $('#editLocFloor').val(floor);
                
                // Set Tariff Dropdown
                var selectedValue = category + '|' + power + ' VA|' + tariff;
                 var found = false;
                 $('#editLocTariff option').each(function(){
                     if($(this).text().indexOf(category) !== -1 && $(this).text().indexOf(power) !== -1) {
                         $(this).prop('selected', true);
                         found = true;
                     }
                 });
                 if(!found) {
                    // fallback to first
                 }
            });
        }

        // Edit Source Script (Only run if button exists)
        if ($('.edit-source-btn').length > 0) {
            $('.edit-source-btn').on('click', function() {
                var id = $(this).data('id');
                var name = $(this).data('name');
                var area = $(this).data('area');
                var type = $(this).data('type');
                var voltage = $(this).data('voltage');
                var capacity = $(this).data('capacity');
                var cost = $(this).data('cost');

                var actionUrl = '{{ route("power-sources.update", ":id") }}';
                actionUrl = actionUrl.replace(':id', id);
                $('#editFormSource').attr('action', actionUrl);

                $('#editSourceName').val(name);
                $('#editSourceArea').val(area); 
                $('#editSourceType').val(type); 
                $('#editSourceVoltage').val(voltage);
                $('#editSourceCapacity').val(capacity);
                $('#editSourceCost').val(cost);
            });
        }
    });
</script>
@endsection
