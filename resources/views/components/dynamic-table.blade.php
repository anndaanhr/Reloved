@props(['data' => [], 'columns' => [], 'title' => 'Data Table', 'searchable' => true, 'filterable' => false, 'filterOptions' => []])

<div class="container mt-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold text-primary mb-1">
                        <i class="bi bi-table me-2"></i>{{ $title }}
                    </h2>
                    <p class="text-muted mb-0">Kelola data dengan mudah</p>
                </div>
                @if(isset($addButton) && $addButton)
                    <div>
                        <a href="{{ $addButton['url'] }}" class="btn btn-primary btn-lg shadow-sm">
                            <i class="bi bi-plus-circle me-2"></i>{{ $addButton['text'] }}
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Search and Filter Section -->
    @if($searchable || $filterable)
        <div class="row mb-4">
            @if($searchable)
                <div class="col-md-{{ $filterable ? '8' : '12' }}">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" class="form-control border-start-0 ps-0" 
                               placeholder="Cari data..." id="dynamicTableSearch">
                    </div>
                </div>
            @endif
            
            @if($filterable && !empty($filterOptions))
                <div class="col-md-4">
                    <select class="form-select" id="dynamicTableFilter">
                        <option value="">Semua Data</option>
                        @foreach($filterOptions as $option)
                            <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>
    @endif

    <!-- Table Section -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="dynamicTable">
                    <thead class="table-dark">
                        <tr>
                            @foreach($columns as $column)
                                <th class="px-4 py-3">
                                    @if(isset($column['sortable']) && $column['sortable'])
                                        <div class="d-flex align-items-center justify-content-between cursor-pointer" 
                                             onclick="sortTable('{{ $column['key'] }}')">
                                            {{ $column['label'] }}
                                            <i class="bi bi-arrow-down-up text-muted ms-2"></i>
                                        </div>
                                    @else
                                        {{ $column['label'] }}
                                    @endif
                                </th>
                            @endforeach
                            @if(isset($actions) && $actions)
                                <th class="px-4 py-3 text-center">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $index => $row)
                            <tr class="table-row" data-index="{{ $index }}">
                                @foreach($columns as $column)
                                    <td class="px-4 py-3" data-column="{{ $column['key'] }}">
                                        @if(isset($column['type']) && $column['type'] === 'badge')
                                            <span class="badge bg-{{ $column['badgeColor'] ?? 'primary' }} px-3 py-2 rounded-pill">
                                                @if(isset($column['icon']))
                                                    <i class="bi bi-{{ $column['icon'] }} me-1"></i>
                                                @endif
                                                {{ data_get($row, $column['key']) }}
                                            </span>
                                        @elseif(isset($column['type']) && $column['type'] === 'avatar')
                                            <div class="d-flex align-items-center">
                                                <div class="avatar bg-gradient-primary text-white rounded-circle me-3 d-flex align-items-center justify-content-center" 
                                                     style="width: 40px; height: 40px;">
                                                    <i class="bi bi-person-fill"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ data_get($row, $column['key']) }}</div>
                                                    @if(isset($column['subtitle']))
                                                        <small class="text-muted">{{ data_get($row, $column['subtitle']) }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        @elseif(isset($column['type']) && $column['type'] === 'number')
                                            <span class="fw-semibold text-primary">#{{ data_get($row, $column['key']) }}</span>
                                        @else
                                            {{ data_get($row, $column['key']) }}
                                        @endif
                                    </td>
                                @endforeach
                                
                                @if(isset($actions) && $actions)
                                    <td class="px-4 py-3 text-center">
                                        <div class="btn-group" role="group">
                                            @foreach($actions as $action)
                                                <button type="button" 
                                                        class="btn btn-{{ $action['variant'] ?? 'outline-primary' }} btn-sm"
                                                        @if(isset($action['onclick']))
                                                            onclick="{{ $action['onclick'] }}({{ $row->id ?? $index }})"
                                                        @endif
                                                        title="{{ $action['title'] ?? '' }}">
                                                    <i class="bi bi-{{ $action['icon'] }}"></i>
                                                </button>
                                            @endforeach
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($columns) + (isset($actions) ? 1 : 0) }}" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-inbox display-4 d-block mb-3"></i>
                                        <h5>Tidak ada data</h5>
                                        <p>Belum ada data untuk ditampilkan</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination (if needed) -->
    @if(isset($pagination) && $pagination)
        <div class="d-flex justify-content-between align-items-center mt-4">
            <div class="text-muted">
                Menampilkan {{ count($data) }} dari {{ $totalData ?? count($data) }} data
            </div>
            <nav>
                <!-- Add pagination links here if needed -->
            </nav>
        </div>
    @endif
</div>

<style>
.cursor-pointer {
    cursor: pointer;
}

.table-row {
    transition: all 0.2s ease;
}

.table-row:hover {
    background-color: rgba(13, 110, 253, 0.05);
}

.avatar {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.table th {
    border-bottom: 2px solid #dee2e6;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.875rem;
    letter-spacing: 0.5px;
}

.btn-group .btn {
    border-radius: 6px;
    margin: 0 2px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('dynamicTableSearch');
    const filterSelect = document.getElementById('dynamicTableFilter');
    const tableRows = document.querySelectorAll('.table-row');

    function filterTable() {
        const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
        const filterValue = filterSelect ? filterSelect.value : '';

        tableRows.forEach(row => {
            const rowText = row.textContent.toLowerCase();
            const matchesSearch = !searchTerm || rowText.includes(searchTerm);
            const matchesFilter = !filterValue || row.querySelector(`[data-column="${filterValue}"]`)?.textContent.includes(filterValue);

            if (matchesSearch && matchesFilter) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', filterTable);
    }
    
    if (filterSelect) {
        filterSelect.addEventListener('change', filterTable);
    }
});

function sortTable(columnKey) {
    const table = document.getElementById('dynamicTable');
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('.table-row'));
    
    // Simple sorting implementation
    rows.sort((a, b) => {
        const aValue = a.querySelector(`[data-column="${columnKey}"]`).textContent.trim();
        const bValue = b.querySelector(`[data-column="${columnKey}"]`).textContent.trim();
        
        if (!isNaN(aValue) && !isNaN(bValue)) {
            return parseFloat(aValue) - parseFloat(bValue);
        }
        
        return aValue.localeCompare(bValue);
    });
    
    rows.forEach(row => tbody.appendChild(row));
}
</script>
