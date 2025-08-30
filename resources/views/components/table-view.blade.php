<table class="table table-hover table-condensed">
    <thead>
        <tr>
            <th>Fecha</th>
            <th>DNI</th>
            <th>Opciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $row)
            <tr>
                <td>{{ $row->fecha }}</td>
                <td>{{ $row->dni }}</td>
                <td>
                    @if($table == 'controles')
                        <button type="button" class="btn btn-info view-appointment btn-xs" value="{{ $row->id }}">
                            <i class="fa fa-eye"></i> Ver receta
                        </button>
                    @else
                        <button type="button" class="btn btn-info view-exam btn-xs" value="{{ $row->id }}">
                            <i class="fa fa-eye"></i> Ver receta
                        </button>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>