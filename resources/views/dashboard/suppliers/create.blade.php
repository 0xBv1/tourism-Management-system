@extends('layouts.dashboard.app')

@section('content')
    <form action="{{ route('dashboard.suppliers.store') }}" method="POST" class="page-body">
        @csrf

        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="Create Supplier" :hideFirst="true">
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
                        <x-dashboard.form.multi-tab-card :tabs="['account', 'company']" tab-id="supplier-tabs">

                            <!-- Account Tab -->
                            <div class="tab-pane fade active show" id="{{ 'supplier-tabs-0' }}" role="tabpanel" aria-labelledby="{{ 'supplier-tabs-0' }}-tab">
                                <x-dashboard.form.input-text 
                                    id="user_name" name="user_name"
                                    error-key="user_name" label-title="Name" required="true"/>

                                <x-dashboard.form.input-text 
                                    id="user_email" name="user_email"
                                    error-key="user_email" label-title="Email" required="true"/>

                                <x-dashboard.form.input-text 
                                    id="user_phone" name="user_phone"
                                    error-key="user_phone" label-title="Phone"/>

                                <x-dashboard.form.input-password 
                                    id="password" name="password"
                                    label-title="Password" required="true"/>

                                <x-dashboard.form.input-password 
                                    id="password_confirmation" name="password_confirmation"
                                    label-title="Confirm Password" required="true"/>
                            </div>

                            <!-- Company Tab -->
                            <div class="tab-pane fade" id="{{ 'supplier-tabs-1' }}" role="tabpanel" aria-labelledby="{{ 'supplier-tabs-1' }}-tab">
                                <x-dashboard.form.input-text 
                                    id="company_name" name="company_name"
                                    error-key="company_name" label-title="Company Name" required="true"/>

                                <x-dashboard.form.input-text 
                                    id="company_email" name="company_email"
                                    error-key="company_email" label-title="Company Email" required="true"/>

                                <x-dashboard.form.input-text 
                                    id="company_phone" name="phone"
                                    error-key="phone" label-title="Company Phone"/>

                                <x-dashboard.form.input-textarea 
                                    id="company_address" name="address"
                                    error-key="address" label-title="Address" rows="3"/>

                                <x-dashboard.form.input-number 
                                    id="commission_rate" name="commission_rate"
                                    label-title="Commission Rate (%)" />

                                <x-dashboard.form.input-text 
                                    id="website" name="website"
                                    error-key="website" label-title="Website"/>

                                <x-dashboard.form.input-text 
                                    id="tax_number" name="tax_number"
                                    error-key="tax_number" label-title="Tax Number"/>

                                <x-dashboard.form.input-text 
                                    id="business_license" name="business_license"
                                    error-key="business_license" label-title="Business License"/>

                                <x-dashboard.form.input-checkbox 
                                    id="is_verified" name="is_verified"
                                    resource-name="Supplier" label-title="Verified"
                                    :value="true" error-key="is_verified"/>

                                <x-dashboard.form.input-checkbox 
                                    id="is_active" name="is_active"
                                    resource-name="Supplier" label-title="Active"
                                    :value="true" error-key="is_active"/>
                            </div>

                        </x-dashboard.form.multi-tab-card>

                        <x-dashboard.form.submit-button title="Create Supplier" class="btn-primary"/>
                    </div>
                </div>
            </div>
        </div>
        <!-- Container-fluid Ends-->
    </form>
@endsection


