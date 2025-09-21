@if(admin()->can('representatives.show'))
    <a href="{{ route('dashboard.representatives.show', $id) }}">
        <i class="fa fa-eye"></i>
    </a>
@endif

@if(admin()->can('representatives.edit'))
    <a href="{{ route('dashboard.representatives.edit', $id) }}">
        <i class="fa fa-edit"></i>
    </a>
@endif

@if(admin()->can('representatives.delete'))
    <a data-delete-url="{{ route('dashboard.representatives.destroy', $id) }}" href="javascript:;"
       type="button" class="btn-delete-resource-modal" data-bs-toggle="modal" data-bs-target="#deleteResourceModal">
        <i class="fa fa-trash"></i>
    </a>
@endif




