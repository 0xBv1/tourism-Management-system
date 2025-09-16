@extends('layouts.dashboard.app')

@section('content')
    <form action="{{ route('dashboard.blogs.update' , $blog) }}" method="POST" class="page-body">
        @csrf
        @method('PUT')

        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="Edit Blog" :hideFirst="true">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard.blogs.index') }}">Blogs</a>
            </li>
        </x-dashboard.partials.breadcrumb>
        <!-- Container-fluid Ends-->

        <!-- Container-fluid starts-->
        <div class="container-fluid">
            <div class="row">
                <x-dashboard.partials.message-alert/>

                <div class="w-100 text-right" style="color: blue; margin: 16px 0">
                    <a style="color: blue; margin: 16px 0" target="_blank" title="Visit On Site"
                       href="{{ $blog->site_url }}">{{ $blog->site_url }}</a>
                </div>

                <div class="card tab2-card">
                    <div class="card-body needs-validation">
                        <x-dashboard.form.language-multi-tab-card tab-id="blogs">
                            @foreach(config('translatable.supported_locales') as $localKey => $local)
                                <div @class(['tab-pane fade', 'active show' => $localKey == config('app.locale')])
                                     id="{{ 'blogs-'.$localKey }}" role="tabpanel"
                                     aria-labelledby="{{ 'blogs-'.$localKey }}-tab">
                                    <x-dashboard.form.input-text error-key="{{$localKey}}.title"
                                                                 name="{{$localKey}}[title]"
                                                                 :value="old($localKey.'.title', $blog->translateOrNew($localKey)->title)"
                                                                 id="{{$localKey}}-title" label-title="Title"/>

                                    <x-dashboard.form.input-text error-key="{{$localKey}}.tags"
                                                                 name="{{$localKey}}[tags]"
                                                                 :value="old($localKey.'.tags', $blog->translateOrNew($localKey)->tags)"
                                                                 class="tags-input"
                                                                 id="{{$localKey}}-tags" label-title="Tags"/>

                                                                        <x-dashboard.form.input-editor error-key="{{$localKey}}.description"
                                                                 name="{{$localKey}}[description]"
                                                                 :value="old($localKey.'.description', $blog->translateOrNew($localKey)->description)"
                                                                 id="{{$localKey}}-description"
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
                                                     :value="old('slug', $blog->slug)"
                                                     id="slug" label-title="Slug"
                                                     placeholder="Leave empty for automatic generation from title"/>

                        <x-dashboard.form.input-text error-key="display_order" name="display_order"
                        :value="old('display_order', $blog->display_order)" id="display_order" label-title="Display Order" />

                        <x-dashboard.form.input-select :options="$categories"
                                                       error-key="categories"
                                                       multible
                                                       name="categories[]"
                                                       :value="old('categories', $blog->categories->pluck('id')->toArray())"
                                                       id="categories"
                                                       label-title="Blog Categories"/>

                        <x-dashboard.form.related-tours :options="$blog->relatedTours->toArray()"
                                                        :value="old('related_tours', $blog->relatedTours->pluck('id')->toArray())" />



                        <x-dashboard.form.input-checkbox resource-name="Blog"
                                                         error-key="active" name="active" id="active"
                                                         :value="old('active', $blog->active)"
                                                         label-title="Active"/>

                        <x-dashboard.form.input-select :options="\App\Enums\BlogStatus::options()"
                                                       error-key="status"
                                                       name="status"
                                                       :value="old('status', $blog->status)"
                                                       id="status"
                                                       label-title="Status"/>

                        <x-dashboard.form.media title="Add Featured Image"
                                                :images="$blog->featured_image"
                                                name="featured_image"/>

                        <x-dashboard.form.media title="Add Gallery" :multiple="true"
                                                :images="$blog->gallery"
                                                name="gallery[]"/>

                        <x-dashboard.form.submit-button/>
                    </div>
                </div>

                <!--Start SEO-->
                <x-dashboard.form.seo-form :seo="$blog->seo"/>
                <!--End SEO-->

            </div>
        </div>
        <!-- Container-fluid Ends-->

    </form>
    <x-dashboard.partials.resource-translation model="Blog" :id="$blog->id"/>
@endsection


