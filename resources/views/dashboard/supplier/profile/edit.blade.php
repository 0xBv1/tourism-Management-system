@extends('layouts.dashboard.app')

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('supplier.profile.update' , $supplier) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Company Name</label>
                                <input type="text" name="company_name" value="{{ old('company_name', $supplier->company_name) }}" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Company Email</label>
                                <input type="email" name="company_email" value="{{ old('company_email', $supplier->company_email) }}" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" value="{{ old('phone', $supplier->phone) }}" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Address</label>
                                <textarea name="address" class="form-control" required>{{ old('address', $supplier->address) }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Logo</label>
                                <x-dashboard.form.media name="logo" :value="$supplier->logo" title="Upload Logo" />
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
@endsection


