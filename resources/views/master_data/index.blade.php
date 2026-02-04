@extends('layouts.app')

@section('title', 'Data Gedung')
@section('page-title', 'Data Gedung')
@section('page-icon', 'now-ui-icons business_bank')

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

    <!-- Added Statistics Cards for "Master Data" Insight -->
    <!-- Stats removed as per request -->

    <div class="row">
        <div class="col-md-12">
            <div class="card card-clean">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0" style="border-radius: 20px 20px 0 0;">
                    <div class="row align-items-center mb-3">
                        <div class="col-md-6">
                            <h4 class="card-title m-0 font-weight-bold" style="color: #32325d;">Building & Location Management</h4>
                            <p class="text-muted small mb-0">Manage registered building locations and their specifications.</p>
                        </div>
                        <div class="col-md-6 text-right">
                           <button type="button" class="btn btn-primary btn-round shadow-lg font-weight-bold px-4" data-toggle="modal" data-target="#addLocationModal">
                                <i class="now-ui-icons ui-1_simple-add mr-1"></i> Add Building
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
                                        <th>Nama Gedung</th>
                                        <th class="text-center">Kode</th>
                                        <th class="text-center">Lantai</th>
                                        <th class="text-center">Golongan</th>
                                        <th class="text-right">Daya</th>
                                        <th class="text-right">Tarif/kWh</th>
                                        <th class="text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($locations as $loc)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="icon icon-shape bg-gradient-primary text-white rounded-circle shadow-sm mr-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background: linear-gradient(45deg, #f96332, #ff8a2bff);">
                                                    <i class="now-ui-icons business_bank"></i>
                                                </div>
                                                <span class="font-weight-bold text-dark">{{ $loc->name }}</span>
                                            </div>
                                        </td>
                                        <td class="text-center"><span class="badge badge-info rounded-pill">{{ $loc->code }}</span></td>
                                        <td class="text-center">Lt. {{ $loc->floor }}</td>
                                        <td class="text-center">{{ $loc->power_category }}</td>
                                        <td class="text-right">{{ $loc->installed_power }} VA</td>
                                        <td class="text-right">Rp {{ number_format($loc->tariff_per_kwh, 0, ',', '.') }}</td>
                                        <td class="text-right">
                                            <button type="button" class="btn btn-info btn-icon btn-round edit-loc-btn shadow-sm" 
                                                data-id="{{ $loc->id }}"
                                                data-name="{{ $loc->name }}"
                                                data-code="{{ $loc->code }}"
                                                data-floor="{{ $loc->floor }}"
                                                data-category="{{ $loc->power_category }}"
                                                data-power="{{ $loc->installed_power }}"
                                                data-tariff="{{ $loc->tariff_per_kwh }}"
                                                data-toggle="modal" data-target="#editLocationModal">
                                                <i class="fas fa-edit" style="font-size: 1.1rem;"></i>
                                            </button>
                                            <form action="{{ route('master-data.destroy', $loc->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Yakin ingin menghapus data ini?');">
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
                                                <i class="now-ui-icons files_box text-muted mb-3" style="font-size: 2em; opacity:0.3;"></i>
                                                <h6 class="text-muted">Belum ada data gedung.</h6>
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

<!-- ADD LOCATION MODAL -->
<div class="modal fade" id="addLocationModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <form action="{{ route('master-data.store') }}" method="POST">
            @csrf
            <div class="modal-content shadow-lg border-0" style="border-radius: 20px;">
                <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                    <h5 class="modal-title font-weight-bold">Tambah Gedung Baru</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body pt-2 px-4">
                    <div class="row">
                        <div class="col-md-6"><div class="form-group"><label>Nama Gedung</label><input type="text" name="name" class="form-control rounded-pill px-3 shadow-sm" required></div></div>
                        <div class="col-md-6"><div class="form-group"><label>Kode Lokasi</label><input type="text" name="code" class="form-control rounded-pill px-3 shadow-sm" required></div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-4"><div class="form-group"><label>Lantai</label><input type="number" name="floor" class="form-control rounded-pill px-3 shadow-sm" required></div></div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <label>Golongan Tarif PLN</label>
                                <select name="tariff_data" class="form-control rounded-pill px-3 shadow-sm" style="height: unset; padding:10px;">
                                    <option value="R1|900 VA|1352">R1 - 900 VA (Rp 1.352)</option>
                                    <option value="R1|1300 VA|1444.70">R1 - 1300 VA (Rp 1.444,70)</option>
                                    <option value="R1|2200 VA|1444.70">R1 - 2200 VA (Rp 1.444,70)</option>
                                    <option value="B1|2200 VA|1444.70">B1 - Bisnis Kecil (Rp 1.445)</option>
                                    <option value="I3|200 kVA|1114.74">I3 - Industri Menengah (Rp 1.115)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 px-4 pb-4">
                    <button type="button" class="btn btn-secondary btn-round" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary btn-round shadow-lg">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- EDIT LOCATION MODAL -->
<div class="modal fade" id="editLocationModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <form id="editFormLocation" action="" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content shadow-lg border-0" style="border-radius: 20px;">
                <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                    <h5 class="modal-title font-weight-bold">Edit Gedung</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body pt-2 px-4">
                    <div class="row">
                        <div class="col-md-6"><div class="form-group"><label>Nama Gedung</label><input type="text" name="name" id="editLocName" class="form-control rounded-pill px-3 shadow-sm" required></div></div>
                        <div class="col-md-6"><div class="form-group"><label>Kode Lokasi</label><input type="text" name="code" id="editLocCode" class="form-control rounded-pill px-3 shadow-sm" required></div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-4"><div class="form-group"><label>Lantai</label><input type="number" name="floor" id="editLocFloor" class="form-control rounded-pill px-3 shadow-sm" required></div></div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <label>Golongan Tarif PLN</label>
                                <select name="tariff_data" id="editLocTariff" class="form-control rounded-pill px-3 shadow-sm" style="height: unset; padding:10px;">
                                    <option value="R1|900 VA|1352">R1 - 900 VA (Rp 1.352)</option>
                                    <option value="R1|1300 VA|1444.70">R1 - 1300 VA (Rp 1.444,70)</option>
                                    <option value="R1|2200 VA|1444.70">R1 - 2200 VA (Rp 1.444,70)</option>
                                    <option value="B1|2200 VA|1444.70">B1 - Bisnis Kecil (Rp 1.445)</option>
                                    <option value="I3|200 kVA|1114.74">I3 - Industri Menengah (Rp 1.115)</option>
                                </select>
                            </div>
                        </div>
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
