@extends('layouts.dashboard.app')

@section('content')
    <form action="{{ route('dashboard.coupons.update' , $coupon) }}" method="POST" class="page-body">
        @csrf
        @method('PUT')

        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="Edit Coupon" :hideFirst="true">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard.coupons.index') }}">Coupons</a>
            </li>
        </x-dashboard.partials.breadcrumb>
        <!-- Container-fluid Ends-->

        <!-- Container-fluid starts-->
        <div class="container-fluid">
            <div class="row">
                <x-dashboard.partials.message-alert/>


                <div class="card">
                    <div class="card-body">
                        <x-dashboard.form.input-text error-key="title" name="title" :value="$coupon->title" id="title"
                                                     label-title="Title"/>

                        <x-dashboard.form.input-text error-key="code" name="code" :value="$coupon->code" id="code"
                                                     label-title="Code"/>

                        <x-dashboard.form.input-checkbox error-key="active" name="active" :value="$coupon->active"
                                                         id="active"
                                                         resourceName="Coupon"
                                                         label-title="Active"/>

                        <x-dashboard.form.input-text error-key="value" name="value" :value="$coupon->value" id="value"
                                                     label-title="Value"/>

                        <x-dashboard.form.input-select :options="\App\Enums\CouponType::options()"
                                                       error-key="discount_type" name="discount_type"
                                                       :value="$coupon->discount_type" id="discount_type"
                                                       label-title="Discount Type"/>

                        <x-dashboard.form.input-text class="input-datepicker" error-key="start_date" name="start_date"
                                                     :value="optional($coupon->start_date)->toDateString()"
                                                     id="start_date"
                                                     label-title="Start Date"/>

                        <x-dashboard.form.input-text class="input-datepicker" error-key="end_date" name="end_date"
                                                     :value="optional($coupon->end_date)->toDateString()"
                                                     id="end_date" label-title="End Date"/>

                        <x-dashboard.form.input-text error-key="limit_per_usage" name="limit_per_usage"
                                                     :value="$coupon->limit_per_usage" id="limit_per_usage"
                                                     label-title="Limit Per Usage"/>

                        <x-dashboard.form.input-text error-key="limit_per_customer" name="limit_per_customer"
                                                     :value="$coupon->limit_per_customer" id="limit_per_customer"
                                                     label-title="Limit Per Customer"/>

                        <x-dashboard.form.input-select :value="old('tours', $coupon->tours->pluck('id')->toArray())" name="tours[]"
                                                       multible
                                                       :options="$tours"
                                                       track-by="id" option-lable="title"
                                                       label-title="Tours"
                                                       id="tours" error-key="tours"/>

                        <x-dashboard.form.input-select :value="old('categories', $coupon->categories->pluck('id')->toArray())" name="categories[]"
                                                       multible
                                                       :options="$categories"
                                                       track-by="id" option-lable="title"
                                                       label-title="Categories"
                                                       id="categories" error-key="categories"/>


                        <x-dashboard.form.submit-button/>
                    </div>
                </div>


            </div>
        </div>
        <!-- Container-fluid Ends-->

    </form>
@endsection

@push('js')
    <script>
        $(document).ready(function () {
            const tours_select_box_selector = '#tours'
            $(tours_select_box_selector).select2('destroy')
            $(tours_select_box_selector).select2({
                ajax: {
                    url: "{{ route('api.tours.index') }}",
                    dataType: 'json',
                    placeholder: 'Search for a tour',
                    minimumInputLength: 1,
                    data: function (params) {
                        return {
                            title: `*${params.term}*`,
                            columns: 'id,translation.title',
                            page: params.page || 1
                        }
                    },
                    processResults: function (data) {
                        let tours =  data.data.data.map(function (tour) {
                            return {
                                id: tour.id,
                                text: tour.title
                            }
                        });
                        return {
                            pagination: {
                                more: data.data.next_page_url != null
                            },
                            results: tours
                        };
                    }
                }
            });
        })

        $(document).ready(function () {
            const tour_categories_select_box_selector = '#categories'
            $(tour_categories_select_box_selector).select2('destroy')
            $(tour_categories_select_box_selector).select2({
                ajax: {
                    url: "{{ route('api.categories.index') }}",
                    dataType: 'json',
                    placeholder: 'Search for a tour category',
                    minimumInputLength: 1,
                    data: function (params) {
                        return {
                            title: `*${params.term}*`,
                            columns: 'id,translation.title',
                            page: params.page || 1
                        }
                    },
                    processResults: function (data) {
                        let categories =  data.data.data.map(function (category) {
                            return {
                                id: category.id,
                                text: category.title
                            }
                        });
                        return {
                            pagination: {
                                more: data.data.next_page_url != null
                            },
                            results: categories
                        };
                    }
                }
            });
        })
    </script>
@endpush
