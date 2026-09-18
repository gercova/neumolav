$(document).ready(function () {
    // DataTable
    const rolesTable = $('#roles_data').DataTable({
        ajax: {
            url: `${API_BASE_URL}/roles/list`,
            dataSrc: 'aaData',
        },
        columns: [
            { data: 0 },
            { data: 1 },
            { data: 2 },
            { data: 3 },
            { data: 4 },
            { data: 5 },
            { data: 6, orderable: false },
        ],
        order: [],
        processing: true,
        language: {
            processing: '<i class="fas fa-spinner fa-spin"></i> Cargando...',
            zeroRecords: 'No se encontraron roles',
            emptyTable: 'No hay roles registrados',
        },
    });

    // MODAL ROLE — Crear / Editar
    // Abrir modal para CREAR
    $('#btn-add-role').on('click', function () {
        resetRoleForm();
        $('#modalRoleLabel').text('Agregar Rol');
        $('#modalRole').modal('show');
    });

    // Abrir modal para EDITAR
    $(document).on('click', '.btn-edit-role', async function () {
        const id = $(this).data('id');
        try {
            const response = await axios.get(`${API_BASE_URL}/roles/${id}`);
            if (response.status === 200) {
                resetRoleForm();
                $('#modalRoleLabel').text('Editar Rol');
                $('#roleId').val(response.data.id);
                $('#role-name').val(response.data.name);
                $('#role-guard').val(response.data.guard_name);
                $('#modalRole').modal('show');
            }
        } catch (error) {
            alertNotify('error', 'No se pudo cargar el rol. Recargue la página.');
            console.error(error);
        }
    });

    // Enviar formulario de rol
    $('#roleForm').on('submit', async function (e) {
        e.preventDefault();
        clearFormErrors(this);

        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');

        try {
            const formData = $(this).serialize();
            const response = await axios.post(`${API_BASE_URL}/roles/store`, formData);

            if (response.data.status) {
                $('#modalRole').modal('hide');
                rolesTable.ajax.reload();
                alertNotify(response.data.type, response.data.messages);
            } else {
                alertNotify('error', response.data.messages);
            }
        } catch (error) {
            if (error.response && error.response.data.errors) {
                showFormErrors(error.response.data.errors);
            } else {
                alertNotify('error', 'Ocurrió un error al procesar la solicitud.');
            }
            console.error(error);
        } finally {
            submitBtn.prop('disabled', false).html(originalText);
        }
    });

    // Eliminar rol
    $(document).on('click', '.btn-delete-role', async function () {
        const id = $(this).data('id');
        const result = await Swal.fire({
            title: '¿Eliminar este rol?',
            text: 'Esta acción no se puede deshacer.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
        });

        if (result.isConfirmed) {
            try {
                const response = await axios.delete(`${API_BASE_URL}/roles/delete/${id}`);
                if (response.data.status) {
                    rolesTable.ajax.reload();
                    alertNotify(response.data.type, response.data.message);
                } else {
                    alertNotify('error', response.data.message);
                }
            } catch (error) {
                alertNotify('error', 'Error al eliminar el rol.');
                console.error(error);
            }
        }
    });

    // MODAL PERMISSIONS — Dual-panel
    let selectedAvailable = [];
    let selectedAssigned = [];

    // Abrir modal de permisos
    $(document).on('click', '.btn-assign-permissions', async function () {
        const id = $(this).data('id');
        const name = $(this).data('name');

        $('#permRoleId').val(id);
        $('#permRoleName').text(name);
        $('#availablePermissionsBody').html('<tr><td colspan="3" class="text-center"><i class="fas fa-spinner fa-spin"></i></td></tr>');
        $('#assignedPermissionsBody').html('');
        $('#selectedPermissions').val('');
        resetPermissionSelection();

        $('#modalPermissions').modal('show');

        try {
            const response = await axios.get(`${API_BASE_URL}/roles/${id}/permissions`);
            populatePermissionPanels(response.data.available, response.data.assigned);
        } catch (error) {
            $('#availablePermissionsBody').html('<tr><td colspan="3" class="text-center text-danger">Error al cargar permisos</td></tr>');
            console.error(error);
        }
    });

    // Guardar permisos del rol
    $('#btn-save-permissions').on('click', async function () {
        const roleId = $('#permRoleId').val();
        updateHiddenPermissionsInput();
        const permissions = $('#selectedPermissions').val();

        const btn = $(this);
        const originalText = btn.html();
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');

        try {
            const response = await axios.post(
                `${API_BASE_URL}/roles/${roleId}/syncPermissions`,
                `permissions=${encodeURIComponent(permissions)}`
            );

            if (response.data.status) {
                $('#modalPermissions').modal('hide');
                rolesTable.ajax.reload();
                alertNotify(response.data.type, response.data.messages);
            } else {
                alertNotify('error', response.data.messages);
            }
        } catch (error) {
            alertNotify('error', 'Error al guardar los permisos.');
            console.error(error);
        } finally {
            btn.prop('disabled', false).html(originalText);
        }
    });

    // Búsqueda en paneles
    $('#availableSearch').on('keyup', function () {
        filterTable('#availablePermissionsBody', $(this).val());
        updateCounters();
    });
    $('#assignedSearch').on('keyup', function () {
        filterTable('#assignedPermissionsBody', $(this).val());
        updateCounters();
    });
    $('#clearAvailableSearch').on('click', function () {
        $('#availableSearch').val('').trigger('keyup');
    });
    $('#clearAssignedSearch').on('click', function () {
        $('#assignedSearch').val('').trigger('keyup');
    });

    // Botones de transferencia
    $('#addAllPermissions').on('click', function () {
        $('#availablePermissionsBody tr:visible').each(function () {
            moveRow($(this), 'add');
        });
        updateCounters();
    });
    $('#removeAllPermissions').on('click', function () {
        $('#assignedPermissionsBody tr:visible').each(function () {
            moveRow($(this), 'remove');
        });
        updateCounters();
    });
    $('#addSelectedPermissions').on('click', function () {
        [...selectedAvailable].forEach(id => {
            const row = $(`#availablePermissionsBody tr[data-id="${id}"]`);
            if (row.length) moveRow(row, 'add');
        });
        selectedAvailable = [];
        toggleSelectionButtons();
        updateCounters();
    });
    $('#removeSelectedPermissions').on('click', function () {
        [...selectedAssigned].forEach(id => {
            const row = $(`#assignedPermissionsBody tr[data-id="${id}"]`);
            if (row.length) moveRow(row, 'remove');
        });
        selectedAssigned = [];
        toggleSelectionButtons();
        updateCounters();
    });

    // Selección de filas en paneles
    $(document).on('click', '#availablePermissionsBody tr', function (e) {
        if (!$(e.target).is('button, i')) {
            $(this).toggleClass('row-highlight');
            const id = $(this).data('id');
            toggleArrayItem(selectedAvailable, id);
            toggleSelectionButtons();
        }
    });
    $(document).on('click', '#assignedPermissionsBody tr', function (e) {
        if (!$(e.target).is('button, i')) {
            $(this).toggleClass('row-highlight');
            const id = $(this).data('id');
            toggleArrayItem(selectedAssigned, id);
            toggleSelectionButtons();
        }
    });

    // Botón individual — agregar
    $(document).on('click', '.btn-perm-add', function () {
        const row = $(this).closest('tr');
        moveRow(row, 'add');
        updateCounters();
    });
    // Botón individual — quitar
    $(document).on('click', '.btn-perm-remove', function () {
        const row = $(this).closest('tr');
        moveRow(row, 'remove');
        updateCounters();
    });

    // MODAL USERS — Ver usuarios del rol
    $(document).on('click', '.btn-view-users', async function () {
        const id = $(this).data('id');
        const name = $(this).data('name');

        $('#usersRoleName').text(name);
        $('#usersRoleBody').html('<tr><td colspan="3" class="text-center"><i class="fas fa-spinner fa-spin"></i> Cargando...</td></tr>');
        $('#modalUsers').modal('show');

        try {
            const response = await axios.get(`${API_BASE_URL}/roles/${id}/users`);
            const users = response.data.users;

            if (!users || users.length === 0) {
                $('#usersRoleBody').html('<tr><td colspan="3" class="text-center text-muted">Este rol no tiene usuarios asignados</td></tr>');
                return;
            }

            let rows = '';
            users.forEach((user, index) => {
                rows += `<tr>
                    <td>${index + 1}</td>
                    <td>${escapeHtml(user.name)}</td>
                    <td>${escapeHtml(user.email)}</td>
                </tr>`;
            });
            $('#usersRoleBody').html(rows);
        } catch (error) {
            $('#usersRoleBody').html('<tr><td colspan="3" class="text-center text-danger">Error al cargar los usuarios</td></tr>');
            console.error(error);
        }
    });

    // Helpers
    function populatePermissionPanels(available, assigned) {
        $('#availablePermissionsBody').empty();
        $('#assignedPermissionsBody').empty();

        available.forEach((perm, idx) => {
            $('#availablePermissionsBody').append(buildPermRow(perm, idx + 1, 'add'));
        });
        assigned.forEach((perm, idx) => {
            $('#assignedPermissionsBody').append(buildPermRow(perm, idx + 1, 'remove'));
        });

        updateCounters();
        updateHiddenPermissionsInput();
    }

    function buildPermRow(perm, num, action) {
        const btnClass = action === 'add' ? 'btn-primary btn-perm-add' : 'btn-danger btn-perm-remove';
        const btnIcon = action === 'add' ? '<i class="fas fa-plus"></i>' : '<i class="fas fa-minus"></i>';
        const btnTitle = action === 'add' ? 'Agregar' : 'Quitar';

        return `<tr data-id="${perm.id}" data-name="${escapeHtml(perm.name)}">
            <td>${num}</td>
            <td>${escapeHtml(perm.name)}</td>
            <td class="action-buttons">
                <button type="button" class="btn btn-xs btn-sm ${btnClass}" title="${btnTitle}">${btnIcon}</button>
            </td>
        </tr>`;
    }

    function moveRow(row, action) {
        const id = row.data('id');
        const name = row.data('name');

        // Remove highlight / selection
        row.removeClass('row-highlight');
        if (action === 'add') {
            toggleArrayItem(selectedAvailable, id, 'remove');
            // Change button to "remove"
            row.find('button')
                .removeClass('btn-primary btn-perm-add')
                .addClass('btn-danger btn-perm-remove')
                .attr('title', 'Quitar')
                .html('<i class="fas fa-minus"></i>');
            $('#assignedPermissionsBody').append(row);
        } else {
            toggleArrayItem(selectedAssigned, id, 'remove');
            // Change button to "add"
            row.find('button')
                .removeClass('btn-danger btn-perm-remove')
                .addClass('btn-primary btn-perm-add')
                .attr('title', 'Agregar')
                .html('<i class="fas fa-plus"></i>');
            $('#availablePermissionsBody').append(row);
        }

        renumberBody('#availablePermissionsBody');
        renumberBody('#assignedPermissionsBody');
        updateHiddenPermissionsInput();
        toggleSelectionButtons();
    }

    function renumberBody(selector) {
        $(`${selector} tr`).each(function (i) {
            $(this).find('td:first').text(i + 1);
        });
    }

    function updateCounters() {
        $('#availableCount').text($('#availablePermissionsBody tr:visible').length);
        $('#assignedCount').text($('#assignedPermissionsBody tr:visible').length);
    }

    function filterTable(bodySelector, query) {
        const q = query.toLowerCase().trim();
        $(`${bodySelector} tr`).each(function () {
            const text = $(this).find('td:nth-child(2)').text().toLowerCase();
            $(this).toggle(text.includes(q));
        });
    }

    function updateHiddenPermissionsInput() {
        const ids = [];
        $('#assignedPermissionsBody tr').each(function () {
            ids.push($(this).data('id'));
        });
        $('#selectedPermissions').val(ids.join(','));
    }

    function toggleArrayItem(arr, value, forceAction) {
        const idx = arr.indexOf(value);
        if (forceAction === 'remove') {
            if (idx > -1) arr.splice(idx, 1);
            return;
        }
        if (idx > -1) arr.splice(idx, 1);
        else arr.push(value);
    }

    function toggleSelectionButtons() {
        selectedAvailable.length > 0
            ? $('#addSelectedPermissions').show()
            : $('#addSelectedPermissions').hide();
        selectedAssigned.length > 0
            ? $('#removeSelectedPermissions').show()
            : $('#removeSelectedPermissions').hide();
    }

    function resetPermissionSelection() {
        selectedAvailable = [];
        selectedAssigned = [];
        toggleSelectionButtons();
    }

    function resetRoleForm() {
        $('#roleForm')[0].reset();
        $('#roleId').val('');
        $('#role-guard').val('web');
        clearFormErrors(document.getElementById('roleForm'));
    }

    function clearFormErrors(form) {
        $(form).find('.text-danger').remove();
        $(form).find('.is-invalid').removeClass('is-invalid');
    }

    function showFormErrors(errors) {
        $.each(errors, function (key, messages) {
            const input = $(`[name="${key}"]`);
            input.addClass('is-invalid').after(`<span class="text-danger">${messages[0]}</span>`);
        });
    }

    function escapeHtml(str) {
        return $('<div>').text(str).html();
    }
});
