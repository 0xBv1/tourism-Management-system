<a href="{{ route('supplier.trips.edit', $trip) }}">
    <i class="fa fa-edit"></i>
</a>

<a data-delete-url="{{ route('supplier.trips.destroy', $trip) }}" href="javascript:;"
   type="button" class="btn-delete-resource-modal" data-bs-toggle="modal" data-bs-target="#deleteResourceModal">
    <i class="fa fa-trash"></i>
</a> 