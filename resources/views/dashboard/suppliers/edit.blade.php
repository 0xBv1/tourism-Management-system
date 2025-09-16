@extends('layouts.dashboard.app')

@section('content')
    <form action="{{ route('dashboard.suppliers.update', $supplier) }}" method="POST" class="page-body">
        @csrf
        @method('PUT')

        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="Edit Supplier" :hideFirst="true">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard.suppliers.index') }}">Suppliers</a>
            </li>
        </x-dashboard.partials.breadcrumb>
        <!-- Container-fluid Ends-->

        <!-- Container-fluid starts-->
        <div class="container-fluid">
            <div class="row">
                <x-dashboard.partials.message-alert/>

                <div class="card tab2-card">
                    <div class="card-body needs-validation">
                        <x-dashboard.form.multi-tab-card :tabs="['account', 'company']" tab-id="supplier-edit-tabs">

                            <!-- Account Tab -->
                            <div class="tab-pane fade active show" id="{{ 'supplier-edit-tabs-0' }}" role="tabpanel" aria-labelledby="{{ 'supplier-edit-tabs-0' }}-tab">
                                <x-dashboard.form.input-text 
                                    id="user_name" name="user_name"
                                    error-key="user_name" label-title="Name" required="true"
                                    :value="old('user_name', $supplier->user->name ?? '')"/>

                                <x-dashboard.form.input-text 
                                    id="user_email" name="user_email"
                                    error-key="user_email" label-title="Email" required="true"
                                    :value="old('user_email', $supplier->user->email ?? '')"/>

                                <x-dashboard.form.input-text 
                                    id="user_phone" name="user_phone"
                                    error-key="user_phone" label-title="Phone"
                                    :value="old('user_phone', $supplier->user->phone ?? '')"/>

                                <x-dashboard.form.input-password 
                                    id="password" name="password"
                                    label-title="Password (leave blank to keep)"/>

                                <x-dashboard.form.input-password 
                                    id="password_confirmation" name="password_confirmation"
                                    label-title="Confirm Password"/>
                            </div>

                            <!-- Company Tab -->
                            <div class="tab-pane fade" id="{{ 'supplier-edit-tabs-1' }}" role="tabpanel" aria-labelledby="{{ 'supplier-edit-tabs-1' }}-tab">
                                <x-dashboard.form.input-text 
                                    id="company_name" name="company_name"
                                    error-key="company_name" label-title="Company Name" required="true"
                                    :value="old('company_name', $supplier->company_name)"/>

                                <x-dashboard.form.input-text 
                                    id="company_email" name="company_email"
                                    error-key="company_email" label-title="Company Email" required="true"
                                    :value="old('company_email', $supplier->company_email)"/>

                                <x-dashboard.form.input-text 
                                    id="company_phone" name="phone"
                                    error-key="phone" label-title="Company Phone"
                                    :value="old('phone', $supplier->phone)"/>

                                <x-dashboard.form.input-textarea 
                                    id="company_address" name="address"
                                    error-key="address" label-title="Address" rows="3"
                                    :value="old('address', $supplier->address)"/>

                                <x-dashboard.form.input-number 
                                    id="commission_rate" name="commission_rate"
                                    label-title="Commission Rate (%)" 
                                    :value="old('commission_rate', $supplier->commission_rate)"/>

                                <x-dashboard.form.input-text 
                                    id="website" name="website"
                                    error-key="website" label-title="Website"
                                    :value="old('website', $supplier->website)"/>

                                <x-dashboard.form.input-text 
                                    id="tax_number" name="tax_number"
                                    error-key="tax_number" label-title="Tax Number"
                                    :value="old('tax_number', $supplier->tax_number)"/>

                                <x-dashboard.form.input-text 
                                    id="business_license" name="business_license"
                                    error-key="business_license" label-title="Business License"
                                    :value="old('business_license', $supplier->business_license)"/>

                                <x-dashboard.form.input-checkbox 
                                    id="is_verified" name="is_verified"
                                    resource-name="Supplier" label-title="Verified"
                                    :value="old('is_verified', $supplier->is_verified)" error-key="is_verified"/>

                                <x-dashboard.form.input-checkbox 
                                    id="is_active" name="is_active"
                                    resource-name="Supplier" label-title="Active"
                                    :value="old('is_active', $supplier->is_active)" error-key="is_active"/>
                            </div>

                        </x-dashboard.form.multi-tab-card>

                        <x-dashboard.form.submit-button title="Update Supplier" class="btn-primary"/>
                    </div>
                </div>
            </div>
        </div>
        <!-- Container-fluid Ends-->
    </form>
@endsection

