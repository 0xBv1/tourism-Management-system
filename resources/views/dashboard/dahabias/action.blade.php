@php
    $editPermission = 'edit-dahabias';
    $deletePermission = 'delete-dahabias';
@endphp

<div class="btn-group" role="group">
    @can($editPermission)
        <a href="{{ route('dashboard.dahabias.edit', $id) }}" class="btn btn-primary btn-sm">
            <i class="fas fa-edit"></i>
        </a>
    @endcan
    
    @can($editPermission)
        <a href="{{ route('dashboard.dahabias.show', $id) }}" class="btn btn-info btn-sm">
            <i class="fas fa-eye"></i>
        </a>
    @endcan
    
    @can($deletePermission)
        <form action="{{ route('dashboard.dahabias.destroy', $id) }}" method="POST" 
              onsubmit="return confirm('Are you sure you want to delete this dahabia?');" style="display: inline;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm">
                <i class="fas fa-trash"></i>
            </button>
        </form>
    @endcan
</div>
