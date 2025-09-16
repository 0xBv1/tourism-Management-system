@extends('layouts.dashboard.app')

@section('content')
    <div class="container-fluid">
        <h5 class="mb-3">Create Company Profile</h5>
        <div class="card">
            <div class="card-body">
                <form action="{{ route('supplier.profile.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Company Name</label>
                                <input type="text" name="company_name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Company Email</label>
                                <input type="email" name="company_email" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Address</label>
                                <textarea name="address" class="form-control" required></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Commission Rate (%)</label>
                                <input type="number" step="0.01" name="commission_rate" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Logo</label>
                                <input type="file" name="logo" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Banner</label>
                                <input type="file" name="banner" class="form-control">
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-primary">Create</button>
                </form>
            </div>
        </div>
    </div>
@endsection


