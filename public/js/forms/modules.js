let manageModulesTable;

$(document).ready(function () {
    // ─── DataTable ────────────────────────────────────────────────────────────
    manageModulesTable = $('#module_data').DataTable({
        ajax: `${API_BASE_URL}/modules/list`,
        order: [],
        processing: true,
        columns: [
            { width: '10px' },  // #
            null,               // Descripción
            { width: '80px' },  // Ícono
            null,               // Submódulos
            { width: '130px' }, // Permisos
            { width: '160px' }, // Fecha
            { width: '130px' }, // Opciones
        ],
    });

    // ─── Modal Módulo — Abrir (crear) ─────────────────────────────────────────
    $('#btn-add-module').on('click', function (e) {
        e.preventDefault();
        $('.form-control').removeClass('is-valid is-invalid');
        $('#moduleForm').trigger('reset');
        $('#moduleForm').find('.text-danger').remove();
        $('#moduleId').val('');
        $('#modalModule .modal-title').text('Agregar Módulo');
        $('#modalModule').modal('show');
    });

    // ─── Modal Submódulo — Abrir (crear) ──────────────────────────────────────
    $('#btn-add-submodule').on('click', function (e) {
        e.preventDefault();
        $('.form-control').removeClass('is-valid is-invalid');
        $('#submoduleForm').trigger('reset');
        $('#submoduleForm').find('.text-danger').remove();
        $('#submoduleId').val('');
        $('#modalSubmodule .modal-title').text('Agregar Submódulo');
        $('#modalSubmodule').modal('show');
    });

    // ─── Guardar Módulo ───────────────────────────────────────────────────────
    $('#moduleForm').on('submit', async function (e) {
        e.preventDefault();
        $('.text-danger').remove();
        $('.form-control').removeClass('is-invalid is-valid');
        const submitBtn = $(this).find('button[type="submit"]');
        const origText  = submitBtn.html();
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Cargando...');
        try {
            const response = await axios.post(`${API_BASE_URL}/modules/storeModule`, $(this).serialize());
            if (response.data.status) {
                $(this).trigger('reset');
                $('#modalModule').modal('hide');
                manageModulesTable.ajax.reload();
                alertNotify(response.data.type, response.data.messages);
            } else {
                alertNotify(response.data.type, response.data.messages);
            }
        } catch (error) {
            if (error.response?.data?.errors) {
                $.each(error.response.data.errors, function (key, value) {
                    $(`[name="${key}"]`).after(`<span class="text-danger">${value[0]}</span>`).addClass('is-invalid');
                });
            } else {
                alertNotify('error', 'Ocurrió un error al procesar la solicitud.');
            }
        } finally {
            submitBtn.prop('disabled', false).html(origText);
        }
    });

    // ─── Guardar Submódulo ────────────────────────────────────────────────────
    $('#submoduleForm').on('submit', async function (e) {
        e.preventDefault();
        $('.text-danger').remove();
        $('.form-control').removeClass('is-invalid is-valid');
        const submitBtn = $(this).find('button[type="submit"]');
        const origText  = submitBtn.html();
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Cargando...');
        try {
            const response = await axios.post(`${API_BASE_URL}/modules/storeSubmodule`, $(this).serialize());
            if (response.data.status) {
                $(this).trigger('reset');
                $('#modalSubmodule').modal('hide');
                manageModulesTable.ajax.reload();
                alertNotify(response.data.type, response.data.messages);
            } else {
                alertNotify(response.data.type, response.data.messages);
            }
        } catch (error) {
            if (error.response?.data?.errors) {
                $.each(error.response.data.errors, function (key, value) {
                    $(`[name="${key}"]`).after(`<span class="text-danger">${value[0]}</span>`).addClass('is-invalid');
                });
            } else {
                alertNotify('error', 'Ocurrió un error al procesar la solicitud.');
            }
        } finally {
            submitBtn.prop('disabled', false).html(origText);
        }
    });

    // ─── Editar Módulo ────────────────────────────────────────────────────────
    $(document).on('click', '.update-row-module', async function (e) {
        e.preventDefault();
        const id = $(this).attr('value');
        try {
            const response = await axios.get(`${API_BASE_URL}/modules/module/${id}`);
            if (response.status === 200) {
                $('#modalModule .modal-title').text('Actualizar Módulo');
                $('.text-danger').remove();
                $('.form-control').removeClass('is-invalid is-valid');
                $('#descripcion').val(response.data.descripcion);
                $('#detalle').val(response.data.detalle);
                $('#icono').val(response.data.icono);
                $('#moduleId').val(response.data.id);
                $('#modalModule').modal('show');
            }
        } catch (error) {
            console.error(error);
        }
    });

    // ─── Editar Submódulo ─────────────────────────────────────────────────────
    $(document).on('click', '.update-row-submodule', async function (e) {
        e.preventDefault();
        const id = $(this).attr('value');
        try {
            const response = await axios.get(`${API_BASE_URL}/modules/submodule/${id}`);
            if (response.status === 200) {
                $('#modalSubmodule .modal-title').text('Actualizar Submódulo');
                $('.text-danger').remove();
                $('.form-control').removeClass('is-invalid is-valid');
                $('#module_id').val(response.data.module_id);
                $('#sm_descripcion').val(response.data.descripcion);
                $('#sm_nombre').val(response.data.nombre);
                $('#sm_detalle').val(response.data.detalle);
                $('#sm_icono').val(response.data.icono);
                $('#submoduleId').val(response.data.id);
                $('#modalSubmodule').modal('show');
            }
        } catch (error) {
            console.error(error);
        }
    });

    // ─── Eliminar Módulo ──────────────────────────────────────────────────────
    $(document).on('click', '.delete-module', async function (e) {
        e.preventDefault();
        const id = $(this).attr('value');
        try {
            const result = await swal.fire({
                title: '¿Estás seguro?',
                text: 'Se eliminará el módulo y todos sus submódulos',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Sí, eliminar',
            });
            if (result.isConfirmed) {
                const response = await axios.delete(`${API_BASE_URL}/modules/delete/${id}`);
                if (response.status === 200) {
                    alertNotify(response.data.type, response.data.message);
                    manageModulesTable.ajax.reload();
                }
            }
        } catch (error) {
            console.error(error);
        }
    });

    // ─── Eliminar Submódulo ───────────────────────────────────────────────────
    $(document).on('click', '.delete-submodule', async function (e) {
        e.preventDefault();
        const id = $(this).attr('value');
        try {
            const result = await swal.fire({
                title: '¿Estás seguro?',
                text: 'Este submódulo será eliminado',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Sí, eliminar',
            });
            if (result.isConfirmed) {
                const response = await axios.delete(`${API_BASE_URL}/submodules/delete/${id}`);
                if (response.status === 200 && response.data.status) {
                    alertNotify(response.data.type, response.data.message);
                    manageModulesTable.ajax.reload();
                }
            }
        } catch (error) {
            console.error(error);
        }
    });

    //  MODULE PERMISSIONS — Dual-panel
    let modSelectedAvailable = [];
    let modSelectedAssigned  = [];

    // Open module permissions modal
    $(document).on('click', '.btn-module-permissions', async function () {
        const id   = $(this).attr('value');
        const name = $(this).data('name');

        $('#modulePermId').val(id);
        $('#modulePermName').text(name);
        modSelectedAvailable = [];
        modSelectedAssigned  = [];
        modToggleSelectionButtons();

        showLoading('#modAvailableBody', '#modAssignedBody');
        $('#modalModulePermissions').modal('show');

        try {
            const response = await axios.get(`${API_BASE_URL}/modules/${id}/permissions`);
            modPopulatePanels(response.data.available, response.data.assigned);
        } catch (error) {
            alertNotify('error', 'No se pudo cargar los permisos del módulo.');
        }
    });

    // Reset state when module permissions modal is hidden
    $('#modalModulePermissions').on('hidden.bs.modal', function () {
        modSelectedAvailable = [];
        modSelectedAssigned  = [];
        modToggleSelectionButtons();
        $('#modAvailableSearch').val('');
        $('#modAssignedSearch').val('');
    });

    // Search — available
    $('#modAvailableSearch').on('input', function () {
        modFilterRows('#modAvailableBody', $(this).val());
    });
    $('#clearModAvailableSearch').on('click', function () {
        $('#modAvailableSearch').val('');
        modFilterRows('#modAvailableBody', '');
    });

    // Search — assigned
    $('#modAssignedSearch').on('input', function () {
        modFilterRows('#modAssignedBody', $(this).val());
    });
    $('#clearModAssignedSearch').on('click', function () {
        $('#modAssignedSearch').val('');
        modFilterRows('#modAssignedBody', '');
    });

    // Row click — select/deselect in module panels
    $(document).on('click', '#modAvailableBody tr', function () {
        const id = parseInt($(this).data('id'));
        if (!id) return;
        modSelectedAvailable = modToggleSelection(modSelectedAvailable, id, $(this));
        modToggleSelectionButtons();
    });
    $(document).on('click', '#modAssignedBody tr', function () {
        const id = parseInt($(this).data('id'));
        if (!id) return;
        modSelectedAssigned = modToggleSelection(modSelectedAssigned, id, $(this));
        modToggleSelectionButtons();
    });

    // Add all
    $('#modAddAll').on('click', function () {
        modMoveAll('#modAvailableBody', '#modAssignedBody', 'remove');
        modSelectedAvailable = [];
        modSelectedAssigned  = [];
        modToggleSelectionButtons();
    });

    // Remove all
    $('#modRemoveAll').on('click', function () {
        modMoveAll('#modAssignedBody', '#modAvailableBody', 'add');
        modSelectedAvailable = [];
        modSelectedAssigned  = [];
        modToggleSelectionButtons();
    });

    // Add selected
    $('#modAddSelected').on('click', function () {
        modSelectedAvailable.forEach(id => {
            const row = $(`#modAvailableBody tr[data-id="${id}"]`);
            modMoveRow(row, 'remove');
        });
        modSelectedAvailable = [];
        modToggleSelectionButtons();
    });

    // Remove selected
    $('#modRemoveSelected').on('click', function () {
        modSelectedAssigned.forEach(id => {
            const row = $(`#modAssignedBody tr[data-id="${id}"]`);
            modMoveRow(row, 'add');
        });
        modSelectedAssigned = [];
        modToggleSelectionButtons();
    });

    // Individual add button (inside row)
    $(document).on('click', '.mod-btn-add', function (e) {
        e.stopPropagation();
        const row = $(this).closest('tr');
        modMoveRow(row, 'remove');
        modSelectedAvailable = modSelectedAvailable.filter(i => i !== parseInt(row.data('id')));
        modToggleSelectionButtons();
    });

    // Individual remove button (inside row)
    $(document).on('click', '.mod-btn-remove', function (e) {
        e.stopPropagation();
        const row = $(this).closest('tr');
        modMoveRow(row, 'add');
        modSelectedAssigned = modSelectedAssigned.filter(i => i !== parseInt(row.data('id')));
        modToggleSelectionButtons();
    });

    // Save module permissions
    $('#btn-save-module-permissions').on('click', async function () {
        const moduleId = $('#modulePermId').val();
        const ids = [];
        $('#modAssignedBody tr').each(function () {
            const id = parseInt($(this).data('id'));
            if (id) ids.push(id);
        });

        const btn     = $(this);
        const origHtml = btn.html();
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');

        try {
            const response = await axios.post(`${API_BASE_URL}/modules/${moduleId}/permissions/sync`, { permissions: ids });
            if (response.data.status) {
                alertNotify('success', response.data.messages);
                $('#modalModulePermissions').modal('hide');
                manageModulesTable.ajax.reload();
            } else {
                alertNotify('error', response.data.messages);
            }
        } catch (error) {
            alertNotify('error', 'No se pudo guardar los permisos.');
        } finally {
            btn.prop('disabled', false).html(origHtml);
        }
    });
    // SUBMODULE PERMISSIONS — Dual-panel
    let smSelectedAvailable = [];
    let smSelectedAssigned  = [];

    // Open submodule permissions modal
    $(document).on('click', '.btn-submodule-permissions', async function () {
        const id   = $(this).attr('value');
        const name = $(this).data('name');

        $('#submodulePermId').val(id);
        $('#submodulePermName').text(name);
        $('#submoduleParentName').text('...');
        smSelectedAvailable = [];
        smSelectedAssigned  = [];
        smToggleSelectionButtons();

        showLoading('#smAvailableBody', '#smAssignedBody');
        $('#modalSubmodulePermissions').modal('show');

        try {
            const response = await axios.get(`${API_BASE_URL}/submodules/${id}/permissions`);
            const data     = response.data;
            $('#submoduleParentName').text(data.module ? data.module.descripcion : '—');
            smPopulatePanels(data.available, data.assigned);
        } catch (error) {
            alertNotify('error', 'No se pudo cargar los permisos del submódulo.');
        }
    });

    // Reset state when submodule permissions modal is hidden
    $('#modalSubmodulePermissions').on('hidden.bs.modal', function () {
        smSelectedAvailable = [];
        smSelectedAssigned  = [];
        smToggleSelectionButtons();
    });

    // Row click — select/deselect in submodule panels
    $(document).on('click', '#smAvailableBody tr', function () {
        const id = parseInt($(this).data('id'));
        if (!id) return;
        smSelectedAvailable = modToggleSelection(smSelectedAvailable, id, $(this));
        smToggleSelectionButtons();
    });
    $(document).on('click', '#smAssignedBody tr', function () {
        const id = parseInt($(this).data('id'));
        if (!id) return;
        smSelectedAssigned = modToggleSelection(smSelectedAssigned, id, $(this));
        smToggleSelectionButtons();
    });

    // Bulk transfer buttons — submodule
    $('#smAddAll').on('click', function () {
        modMoveAll('#smAvailableBody', '#smAssignedBody', 'remove');
        smSelectedAvailable = [];
        smSelectedAssigned  = [];
        smToggleSelectionButtons();
    });
    $('#smRemoveAll').on('click', function () {
        modMoveAll('#smAssignedBody', '#smAvailableBody', 'add');
        smSelectedAvailable = [];
        smSelectedAssigned  = [];
        smToggleSelectionButtons();
    });
    $('#smAddSelected').on('click', function () {
        smSelectedAvailable.forEach(id => {
            const row = $(`#smAvailableBody tr[data-id="${id}"]`);
            smMoveRow(row, 'remove');
        });
        smSelectedAvailable = [];
        smToggleSelectionButtons();
    });
    $('#smRemoveSelected').on('click', function () {
        smSelectedAssigned.forEach(id => {
            const row = $(`#smAssignedBody tr[data-id="${id}"]`);
            smMoveRow(row, 'add');
        });
        smSelectedAssigned = [];
        smToggleSelectionButtons();
    });

    // Individual row buttons — submodule
    $(document).on('click', '.sm-btn-add', function (e) {
        e.stopPropagation();
        const row = $(this).closest('tr');
        smMoveRow(row, 'remove');
        smSelectedAvailable = smSelectedAvailable.filter(i => i !== parseInt(row.data('id')));
        smToggleSelectionButtons();
    });
    $(document).on('click', '.sm-btn-remove', function (e) {
        e.stopPropagation();
        const row = $(this).closest('tr');
        smMoveRow(row, 'add');
        smSelectedAssigned = smSelectedAssigned.filter(i => i !== parseInt(row.data('id')));
        smToggleSelectionButtons();
    });

    // Save submodule permissions
    $('#btn-save-submodule-permissions').on('click', async function () {
        const submoduleId = $('#submodulePermId').val();
        const ids = [];
        $('#smAssignedBody tr').each(function () {
            const id = parseInt($(this).data('id'));
            if (id) ids.push(id);
        });

        const btn      = $(this);
        const origHtml  = btn.html();
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');

        try {
            const response = await axios.post(`${API_BASE_URL}/submodules/${submoduleId}/permissions/sync`, { permissions: ids });
            if (response.data.status) {
                alertNotify('success', response.data.messages);
                $('#modalSubmodulePermissions').modal('hide');
                manageModulesTable.ajax.reload();
            } else {
                alertNotify('error', response.data.messages);
            }
        } catch (error) {
            alertNotify('error', 'No se pudo guardar los permisos.');
        } finally {
            btn.prop('disabled', false).html(origHtml);
        }
    });

    function showLoading(...bodySelectors) {
        bodySelectors.forEach(sel => {
            $(sel).html('<tr><td colspan="3" class="text-center"><i class="fas fa-spinner fa-spin"></i> Cargando...</td></tr>');
        });
    }

    function buildPermRow(perm, num, action, prefix) {
        const btnClass = action === 'add' ? `btn-primary ${prefix}-btn-add` : `btn-danger ${prefix}-btn-remove`;
        const btnIcon  = action === 'add' ? '<i class="fas fa-plus"></i>' : '<i class="fas fa-minus"></i>';
        const btnTitle = action === 'add' ? 'Agregar' : 'Quitar';
        const label    = perm.description ? `${perm.name} <small class="text-muted">(${perm.description})</small>` : perm.name;
        return `<tr data-id="${perm.id}" data-name="${escapeHtml(perm.name)}" style="cursor:pointer;">
                    <td>${num}</td>
                    <td>${label}</td>
                    <td class="action-buttons">
                        <button type="button" class="btn btn-xs ${btnClass}" title="${btnTitle}">${btnIcon}</button>
                    </td>
                </tr>`;
    }

    // ── Module panel helpers ──────────────────────────────────────────────────

    function modPopulatePanels(available, assigned) {
        const $avail    = $('#modAvailableBody');
        const $assigned = $('#modAssignedBody');
        $avail.empty();
        $assigned.empty();

        available.forEach((p, i) => $avail.append(buildPermRow(p, i + 1, 'add', 'mod')));
        assigned.forEach((p, i) => $assigned.append(buildPermRow(p, i + 1, 'remove', 'mod')));

        modUpdateCounters();
    }

    function modUpdateCounters() {
        $('#modAvailableCount').text($('#modAvailableBody tr').length);
        $('#modAssignedCount').text($('#modAssignedBody tr').length);
    }

    function modToggleSelectionButtons() {
        $('#modAddSelected').toggle(modSelectedAvailable.length > 0);
        $('#modRemoveSelected').toggle(modSelectedAssigned.length > 0);
    }

    function modFilterRows(bodySelector, query) {
        const q = query.toLowerCase();
        $(bodySelector + ' tr').each(function () {
            const name = ($(this).data('name') || '').toLowerCase();
            $(this).toggle(name.includes(q));
        });
    }

    function modMoveAll(fromSel, toSel, action) {
        const $rows = $(fromSel + ' tr').filter(':visible');
        const $to   = $(toSel);
        let count   = $to.find('tr').length;
        $rows.each(function () {
            const perm = { id: $(this).data('id'), name: $(this).data('name'), description: null };
            $to.append(buildPermRow(perm, ++count, action, 'mod'));
            $(this).remove();
        });
        modUpdateCounters();
    }

    function modMoveRow(row, action) {
        const id   = row.data('id');
        const name = row.data('name');
        const perm = { id, name, description: null };
        const target = action === 'remove' ? '#modAssignedBody' : '#modAvailableBody';
        const count  = $(target + ' tr').length + 1;
        $(target).append(buildPermRow(perm, count, action, 'mod'));
        row.remove();
        modUpdateCounters();
    }

    // ── Submodule panel helpers ───────────────────────────────────────────────

    function smPopulatePanels(available, assigned) {
        const $avail    = $('#smAvailableBody');
        const $assigned = $('#smAssignedBody');
        $avail.empty();
        $assigned.empty();

        available.forEach((p, i) => $avail.append(buildPermRow(p, i + 1, 'add', 'sm')));
        assigned.forEach((p, i) => $assigned.append(buildPermRow(p, i + 1, 'remove', 'sm')));

        smUpdateCounters();
    }

    function smUpdateCounters() {
        $('#smAvailableCount').text($('#smAvailableBody tr').length);
        $('#smAssignedCount').text($('#smAssignedBody tr').length);
    }

    function smToggleSelectionButtons() {
        $('#smAddSelected').toggle(smSelectedAvailable.length > 0);
        $('#smRemoveSelected').toggle(smSelectedAssigned.length > 0);
    }

    function smMoveAll(fromSel, toSel, action) {
        const $rows = $(fromSel + ' tr').filter(':visible');
        const $to   = $(toSel);
        let count   = $to.find('tr').length;
        $rows.each(function () {
            const perm = { id: $(this).data('id'), name: $(this).data('name'), description: null };
            $to.append(buildPermRow(perm, ++count, action, 'sm'));
            $(this).remove();
        });
        smUpdateCounters();
    }

    function smMoveRow(row, action) {
        const id   = row.data('id');
        const name = row.data('name');
        const perm = { id, name, description: null };
        const target = action === 'remove' ? '#smAssignedBody' : '#smAvailableBody';
        const count  = $(target + ' tr').length + 1;
        $(target).append(buildPermRow(perm, count, action, 'sm'));
        row.remove();
        smUpdateCounters();
    }

    // ── Generic helpers ───────────────────────────────────────────────────────

    function modToggleSelection(arr, id, $row) {
        if (arr.includes(id)) {
            $row.removeClass('row-highlight');
            return arr.filter(i => i !== id);
        } else {
            $row.addClass('row-highlight');
            return [...arr, id];
        }
    }

    function escapeHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }
});