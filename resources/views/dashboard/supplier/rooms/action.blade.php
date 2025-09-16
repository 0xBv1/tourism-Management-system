<a href="{{ route('supplier.rooms.edit', $id) }}">
    <i class="edit-icon" title="Edit Room">
        <i class="fa fa-edit"></i>
    </a>
</a>

<a data-delete-url="{{ route('supplier.rooms.destroy', $id) }}" href="javascript:;"
   type="button" class="btn-delete-resource-modal" data-bs-toggle="modal" data-bs-target="#deleteResourceModal">
    <i class="fa fa-trash"></i>
</a>
