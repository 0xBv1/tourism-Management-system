<a title="View Supplier" href="{{ route('dashboard.suppliers.show', $supplier) }}">
    <i class="fa fa-eye"></i>
</a>

<a title="Edit Supplier" href="{{ route('dashboard.suppliers.edit', $supplier) }}">
    <i class="fa fa-edit"></i>
</a>

<a data-delete-url="{{ route('dashboard.suppliers.destroy', $supplier) }}" href="javascript:;"
   type="button" class="btn-delete-resource-modal" data-bs-toggle="modal" data-bs-target="#deleteResourceModal"
   title="Delete Supplier">
    <i class="fa fa-trash"></i>
</a>


