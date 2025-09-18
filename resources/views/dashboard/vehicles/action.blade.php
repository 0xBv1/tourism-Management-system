<a href="{{ route('dashboard.vehicles.show', $id) }}">
    <i class="fa fa-eye"></i>
</a>

<a href="{{ route('dashboard.vehicles.edit', $id) }}">
    <i class="fa fa-edit"></i>
</a>

<a data-delete-url="{{ route('dashboard.vehicles.destroy', $id) }}" href="javascript:;"
   type="button" class="btn-delete-resource-modal" data-bs-toggle="modal" data-bs-target="#deleteResourceModal">
    <i class="fa fa-trash"></i>
</a>




