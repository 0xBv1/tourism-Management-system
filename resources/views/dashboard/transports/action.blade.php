@can('transports.list')
<a title="View Transport Details" href="{{ route('dashboard.transports.show', $id) }}" class="btn btn-sm btn-outline-primary">
    <i class="mdi mdi-eye"></i>
</a>
@endcan

@can('transports.edit')
<a title="Edit Transport" href="{{ route('dashboard.transports.edit', $id) }}" class="btn btn-sm btn-outline-warning">
    <i class="mdi mdi-pencil"></i>
</a>
@endcan

@can('transports.delete')
<form action="{{ route('dashboard.transports.destroy', $id) }}" method="POST" style="display:inline" onsubmit="return confirm('Are you sure you want to delete this transport?')">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Transport">
        <i class="mdi mdi-delete"></i>
    </button>
</form>
@endcan
