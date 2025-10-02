@php
    $editPermission = 'nile_cruises.edit';
    $deletePermission = 'nile_cruises.delete';
@endphp

<div class="btn-group" role="group">
    @can($editPermission)
        <a href="{{ route('dashboard.nile-cruises.edit', $id) }}" class="btn btn-primary btn-sm">
            <i class="fas fa-edit"></i>
        </a>
    @endcan
    
    @can($editPermission)
        <a href="{{ route('dashboard.nile-cruises.show', $id) }}" class="btn btn-info btn-sm">
            <i class="fas fa-eye"></i>
        </a>
    @endcan
    
    @can($deletePermission)
        <form action="{{ route('dashboard.nile-cruises.destroy', $id) }}" method="POST" 
              onsubmit="return confirm('Are you sure you want to delete this Nile cruise?');" style="display: inline;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm">
                <i class="fas fa-trash"></i>
            </button>
        </form>
    @endcan
</div>
