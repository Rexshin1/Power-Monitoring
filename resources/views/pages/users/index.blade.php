@extends('layouts.app')

@section('title', 'User Management')
@section('page-title', 'USER MANAGEMENT')
@section('page-icon', 'users_single-02')

@section('content')
@section('content')
<style>
    /* Premium Table Styling (Matched with History Page) */
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
        background-color: #f96332;
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
                        <div class="col-md-8">
                            <h4 class="card-title m-0 font-weight-bold" style="color: #32325d;">User Management</h4>
                            <p class="text-muted small mb-0">Manage system access and roles</p>
                        </div>
                        <div class="col-md-4 text-right">
                            <button type="button" class="btn btn-primary btn-round btn-lg shadow-lg font-weight-bold px-4" data-toggle="modal" data-target="#addUserModal">
                                <i class="now-ui-icons ui-1_simple-add mr-1"></i> Add User
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
                                    <th class="text-center">Email</th>
                                    <th class="text-center">Role</th>
                                    <th class="text-center">Created At</th>
                                    <th class="text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="icon icon-shape bg-gradient-primary text-white rounded-circle shadow-sm mr-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background: linear-gradient(45deg, #f96332, #ff922b);">
                                                <i class="now-ui-icons users_circle-08"></i>
                                            </div>
                                            <span class="font-weight-bold text-dark">{{ $user->name }}</span>
                                        </div>
                                    </td>
                                    <td class="text-center">{{ $user->email }}</td>
                                    <td class="text-center">
                                        @if($user->role == 'admin')
                                            <span class="badge badge-danger rounded-pill px-3 py-2 shadow-sm" style="font-size: 11px; letter-spacing: 0.5px;">ADMIN</span>
                                        @else
                                            <span class="badge badge-info rounded-pill px-3 py-2 shadow-sm" style="font-size: 11px; letter-spacing: 0.5px;">USER</span>
                                        @endif
                                    </td>
                                    <td class="text-center text-muted small">{{ $user->created_at->format('d M Y') }}</td>
                                    <td class="text-right">
                                        <button type="button" class="btn btn-info btn-icon btn-round edit-btn shadow-sm" 
                                            data-id="{{ $user->id }}"
                                            data-name="{{ $user->name }}"
                                            data-email="{{ $user->email }}"
                                            data-role="{{ $user->role }}"
                                            data-toggle="modal" data-target="#editUserModal"
                                            title="Edit User">
                                            <i class="fas fa-edit" style="font-size: 1.1rem;"></i>
                                        </button>
                                        
                                        @if(auth()->id() != $user->id)
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-icon btn-round shadow-sm" title="Delete User">
                                                <i class="fas fa-trash-alt" style="font-size: 1.1rem;"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="d-flex flex-column align-items-center justify-content-center">
                                            <i class="now-ui-icons users_single-02 text-muted" style="font-size: 3rem; opacity: 0.3;"></i>
                                            <p class="text-muted mt-3 mb-0 font-weight-bold">No users found.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="px-4 py-4 border-top d-flex justify-content-center" style="background: transparent; border-radius: 0 0 20px 20px;">
                         {{ $users->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Modal (Premium Style - LARGE & CENTERED 2-COL) -->
<div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <form action="{{ route('users.store') }}" method="POST">
            @csrf
            <div class="modal-content shadow-lg border-0" style="border-radius: 20px;">
                <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                    <h5 class="modal-title font-weight-bold" id="addUserModalLabel">Add New User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body pt-2 px-4">
                    <p class="text-muted small mb-4">Create a new account with access privileges.</p>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="text-primary font-weight-bold small text-uppercase ml-2">Full Name</label>
                                <input type="text" name="name" class="form-control rounded-pill px-3 shadow-sm" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="text-primary font-weight-bold small text-uppercase ml-2">Email Address</label>
                                <input type="email" name="email" class="form-control rounded-pill px-3 shadow-sm" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                         <div class="col-md-6">
                            <div class="form-group">
                                <label class="text-primary font-weight-bold small text-uppercase ml-2">User Role</label>
                                <select name="role" class="form-control rounded-pill px-3 shadow-sm" style="height: unset; padding-top:10px; padding-bottom:10px;">
                                    <option value="user">User (Standard Access)</option>
                                    <option value="admin">Admin (Full Control)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="text-primary font-weight-bold small text-uppercase ml-2">Password</label>
                                <input type="password" name="password" class="form-control rounded-pill px-3 shadow-sm" required minlength="6">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 px-4 pb-4">
                    <button type="button" class="btn btn-secondary btn-round px-4 font-weight-bold" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-round shadow-lg px-4 font-weight-bold">Save User</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal (Premium Style - LARGE & CENTERED 2-COL) -->
<div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <form id="editForm" action="" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content shadow-lg border-0" style="border-radius: 20px;">
                <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                    <h5 class="modal-title font-weight-bold" id="editUserModalLabel">Edit User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body pt-2 px-4">
                     <p class="text-muted small mb-4">Update user details and permissions.</p>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="text-primary font-weight-bold small text-uppercase ml-2">Full Name</label>
                                <input type="text" name="name" id="editName" class="form-control rounded-pill px-3 shadow-sm" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                             <div class="form-group">
                                <label class="text-primary font-weight-bold small text-uppercase ml-2">Email Address</label>
                                <input type="email" name="email" id="editEmail" class="form-control rounded-pill px-3 shadow-sm" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="text-primary font-weight-bold small text-uppercase ml-2">User Role</label>
                                <select name="role" id="editRole" class="form-control rounded-pill px-3 shadow-sm" style="height: unset; padding-top:10px; padding-bottom:10px;">
                                    <option value="user">User (Standard Access)</option>
                                    <option value="admin">Admin (Full Control)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                             <div class="form-group">
                                <label class="text-primary font-weight-bold small text-uppercase ml-2">New Password (Optional)</label>
                                <input type="password" name="password" class="form-control rounded-pill px-3 shadow-sm" placeholder="Leave empty to keep current" minlength="6">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 px-4 pb-4">
                    <button type="button" class="btn btn-secondary btn-round px-4 font-weight-bold" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-round shadow-lg px-4 font-weight-bold">Update User</button>
                </div>
            </div>
        </form>
    </div>
</div>


@endsection

@section('scripts')
<script>
    // Populate Edit Modal
    $('.edit-btn').on('click', function() {
        let id = $(this).data('id');
        let name = $(this).data('name');
        let email = $(this).data('email');
        let role = $(this).data('role');

        $('#editForm').attr('action', '/users/' + id);
        $('#editName').val(name);
        $('#editEmail').val(email);
        $('#editRole').val(role);
    });
</script>
@endsection
