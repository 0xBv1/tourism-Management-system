@if(admin()->can('guides.show'))
    <a href="{{ route('dashboard.guides.show', $id) }}">
        <i class="fa fa-eye"></i>
    </a>
@endif

@if(admin()->can('guides.edit'))
    <a href="{{ route('dashboard.guides.edit', $id) }}">
        <i class="fa fa-edit"></i>
    </a>
@endif

@if(admin()->can('guides.delete'))
    <a data-delete-url="{{ route('dashboard.guides.destroy', $id) }}" href="javascript:;"
       type="button" class="btn-delete-resource-modal" data-bs-toggle="modal" data-bs-target="#deleteResourceModal">
        <i class="fa fa-trash"></i>
    </a>
@endif




