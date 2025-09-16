@extends('layouts.dashboard.app')

@section('content')
    <form action="{{ route('dashboard.blog-categories.update' , $blogCategory) }}" method="POST" class="page-body">
        @csrf
        @method('PUT')

        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="Edit BlogCategory" :hideFirst="true">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard.blog-categories.index') }}">Blog Categories</a>
            </li>
        </x-dashboard.partials.breadcrumb>
        <!-- Container-fluid Ends-->

        <!-- Container-fluid starts-->
        <div class="container-fluid">
            <div class="row">
                <x-dashboard.partials.message-alert/>

                <div class="card tab2-card">
                    <div class="card-body needs-validation">
                        <x-dashboard.form.language-multi-tab-card tab-id="blog-categories">
                            @foreach(config('translatable.supported_locales') as $localKey => $local)
                                <div @class(['tab-pane fade', 'active show' => $localKey == config('app.locale')])
                                     id="{{ 'blog-categories-'.$localKey }}" role="tabpanel"
                                     aria-labelledby="{{ 'blog-categories-'.$localKey }}-tab">
                                    <x-dashboard.form.input-text error-key="{{$localKey}}.title"
                                                                 name="{{$localKey}}[title]"
                                                                 :value="old($localKey.'.title', $blogCategory->translateOrNew($localKey)->title)"
                                                                 id="{{$localKey}}-title" label-title="Title"/>



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
                                                     :value="old('slug', $blogCategory->slug)"
                                                     id="slug" label-title="Slug"
                                                     placeholder="Leave empty for automatic generation from title"/>

                        <x-dashboard.form.related-tours :options="$blogCategory->relatedTours->toArray()"
                                                        :value="old('related_tours', $blogCategory->relatedTours->pluck('id')->toArray())" />

                        <x-dashboard.form.input-select :options="$categories"
                                                       error-key="parent_id"
                                                       name="parent_id"
                                                       id="parent_id"
                                                       :value="old('parent_id', $blogCategory->parent_id)"
                                                       label-title="Parent Category"/>

                        <x-dashboard.form.input-checkbox resource-name="Blog Category"
                                                         error-key="active" name="active" id="active"
                                                         :value="old('active', $blogCategory->active)"
                                                         label-title="Active"/>

                        <x-dashboard.form.media title="Add Featured Image"
                                                :images="$blogCategory->featured_image"
                                                name="featured_image"/>

                        <x-dashboard.form.submit-button/>
                    </div>
                </div>


                <!--Start SEO-->
                <x-dashboard.form.seo-form :seo="$blogCategory->seo" />
                <!--End SEO-->

            </div>
        </div>
        <!-- Container-fluid Ends-->
        <x-dashboard.partials.resource-translation model="BlogCategory" :id="$blogCategory->id"/>

    </form>
@endsection
