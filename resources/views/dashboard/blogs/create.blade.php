@extends('layouts.dashboard.app')

@section('content')
    <form action="{{ route('dashboard.blogs.store' ) }}" method="POST" class="page-body">
        @csrf

        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="Create Blog" :hideFirst="true">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard.blogs.index') }}">Blogs</a>
            </li>
        </x-dashboard.partials.breadcrumb>
        <!-- Container-fluid Ends-->

        <!-- Container-fluid starts-->
        <div class="container-fluid">
            <div class="row">
                <x-dashboard.partials.message-alert/>

                <div class="card tab2-card">
                    <div class="card-body needs-validation">
                        <x-dashboard.form.language-multi-tab-card tab-id="blogs">
                            @foreach(config('translatable.supported_locales') as $localKey => $local)
                                <div @class(['tab-pane fade', 'active show' => $localKey == config('app.locale')])
                                     id="{{ 'blogs-'.$localKey }}" role="tabpanel"
                                     aria-labelledby="{{ 'blogs-'.$localKey }}-tab">
                                    <x-dashboard.form.input-text error-key="{{$localKey}}.title"
                                                                 name="{{$localKey}}[title]" id="{{$localKey}}-title"
                                                                 :value="old($localKey.'.title')"
                                                                 label-title="Title"/>


                                    <x-dashboard.form.input-text error-key="{{$localKey}}.tags"
                                                                 name="{{$localKey}}[tags]" id="{{$localKey}}-tags"
                                                                 :value="old($localKey.'.tags')"
                                                                 class="tags-input"
                                                                 label-title="Tags"/>

                                    <x-dashboard.form.input-editor error-key="{{$localKey}}.description"
                                                                   name="{{$localKey}}[description]"
                                                                   id="{{$localKey}}-description"
                                                                   :value="old($localKey.'.description')"
                                                                   label-title="Description"/>


                                </div>
                            @endforeach
                        </x-dashboard.form.language-multi-tab-card>
                        <x-dashboard.form.submit-button/>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">

                        <x-dashboard.form.input-text error-key="slug"
                                                     name="slug"
                                                     id="slug"
                                                     :value="old('slug')"
                                                     label-title="Slug"
                                                     placeholder="Leave empty for automatic generation from title"/>
                        <x-dashboard.form.input-text error-key="display_order"
                        name="display_order"
                        id="display_order"
                        :value="old('display_order')"
                        label-title="Display Order"/>


                        <x-dashboard.form.input-select :options="$categories"
                                                       multible
                                                       :value="old('categories', [])"
                                                       error-key="categories[]"
                                                       name="categories[]"
                                                       id="categories"
                                                       label-title="Blog Categories"/>

                        <x-dashboard.form.related-tours  />

                        <x-dashboard.form.input-checkbox resource-name="Blog"
                                                         error-key="active" name="active" id="active"
                                                         :value="old('active', true)"
                                                         label-title="Active"/>

                        <x-dashboard.form.input-select :options="\App\Enums\BlogStatus::options()"
                                                       :value="old('status')"
                                                       error-key="status"
                                                       name="status"
                                                       id="status"
                                                       label-title="Status"/>

                        <x-dashboard.form.media title="Add Featured Image"
                                                name="featured_image"/>

                        <x-dashboard.form.media title="Add Gallery" :multiple="true"
                                                name="gallery[]"/>

                        <x-dashboard.form.submit-button/>
                    </div>
                </div>

                <!--Start SEO-->
                <x-dashboard.form.seo-form/>
                <!--End SEO-->

            </div>
        </div>
        <!-- Container-fluid Ends-->

    </form>
@endsection
