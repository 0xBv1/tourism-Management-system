<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sun Pyramids Dashboard">
    <meta name="keywords" content="admin dashboard, sun pyramids, web app">
    <meta name="author" content="Ahmed Nasr">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{--    <link rel="icon" href="assets/images/dashboard/favicon.png" type="image/x-icon">--}}
    {{--    <link rel="shortcut icon" href="assets/images/dashboard/favicon.png" type="image/x-icon">--}}
    <title>{{ config('app.name') }} | Dashboard</title>

    <!-- Google font-->
    <link rel="stylesheet"  href="https://fonts.googleapis.com/css2?family=Work+Sans:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,500;1,600;1,700;1,800;1,900&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap">
    <!-- App css-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/admin.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/custom.css') }}?ver=1.0.2">
    @stack('css')
</head>

<body class="{{ admin()->theme }}">
<!-- page-wrapper Start-->
<div class="page-wrapper">

    @include('layouts.dashboard.navbar')

    <!-- Page Body Start -->
    <div class="page-body-wrapper">
        @include('layouts.dashboard.sidebar')
        <div class="page-body">
        @yield('content')
        </div>
        @include('layouts.dashboard.footer')
    </div>
</div>
<!-- page-wrapper end-->
@stack('js-upper')
<!--script admin-->
<script>window.supportedLocales = {!! collect(config('translatable.locales'))->toJson() !!} </script>
<script src="{{ str(config('tinymce.sdk-url'))->replace('API_KEY', setting(\App\Enums\SettingKey::TINY_EDITOR->value, true)) }}" referrerpolicy="origin"></script>
<script src="{{ asset('assets/admin/js/admin.js') }}?ver=1.1.0"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.12/ace.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.12/ext-language_tools.js"></script>

{{--<script src="{{ asset('assets/admin/js/tinymce-jquery.js') }}?ver=1.0.0"></script>--}}
@isset($dataTable)
    <x-dashboard.partials.delete-resource-modal />
    {!! $dataTable->scripts() !!}
@endisset
<script>
    const tinymceInitEditor = async(ele) => {
        await ele.tinymce({
            relative_urls : false,
            remove_script_host : false,
            plugins: 'code anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
            toolbar: 'wordcount | code | forecolor backcolor undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
        })
    }

    // $('.code-editor-tiny').each(function(){
    //     tinymceInitEditor($(this))
    // })

    tinymce.init({
        relative_urls : false,
        remove_script_host : false,
        selector: '.code-editor-tiny',
        height: 500,
        plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount code',
        toolbar: 'wordcount | forecolor backcolor undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat | code',
    });

    $('.auto-translate').each(function () {
        $(this).on('click', function () {
            if(confirm('This will overwrite any translated property!')) {
                $(this).addClass('disabled')
                let $icon = $(this).find('.icon')
                $icon.removeClass('fa-language')
                $icon.addClass('fa-spinner fa-spin')
                axios.post("{{ route('dashboard.model.auto.translate') }}", {
                    model: $(this).data('model'),
                    id: $(this).data('id'),
                })
                    .then(response=> toastr.success(response.data.message))
                    .catch(error=> toastr.error(error?.response?.data?.message || "Unexpected Error!"))
                    .finally(()=> {
                        $(this).removeClass('disabled')
                        $icon.addClass('fa-language')
                        $icon.removeClass('fa-spinner fa-spin')
                    })
            }
        })
    })

    $('.page-main-header .switch').on('click', function(){
        $('.dataTables_wrapper table.dataTable').css({width: '100% !important'})
    })

    // $(`#en-title,#en-name`).on('keyup', function () {
    //     let slug = slugify($(this).val())
    //     $(`#slug`).val(slug)
    // })

    $('.clear-app-cache').on('click', function (e) {
        e.preventDefault()
        if(confirm('Are you sure purge cache?')) {
            axios.post($(this).attr('href'), {
                _token: '{{ csrf_token() }}'
            }).then(response => {
                toastr.success(response.data.message)
            }).catch(error=> {
                toastr.success(error.response.data.message)
            })
        }
    })
</script>
@stack('js')
</body>
</html>
