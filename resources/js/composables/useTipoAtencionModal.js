import axios from 'axios';

const escapeHtml = (text) => {
    if (!text) return '';
    return String(text)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
};

const getApi = () => (window.API_BASE_URL || '/admin').replace(/\/+$/, '');

export async function openChangeTipoAtencionModal(cita, onSuccess = null) {
    if (!cita || !cita.id) return;
    if (!window.Swal) {
        console.error('SweetAlert2 no está disponible en window.Swal');
        return;
    }

    const currentTipo = parseInt(cita.id_tipo_atencion) || 1;
    const patientName = cita.nombres || 'Paciente';
    const id = cita.id;

    const { value: selectedTipo } = await window.Swal.fire({
        title: 'Tipo de Atención',
        html: `
            <p class="text-muted small mb-3">Paciente: <b>${escapeHtml(patientName)}</b></p>
            <div class="text-left mb-2">
                <label class="font-weight-bold text-dark small mb-2">Seleccione el nuevo tipo de atención:</label>
                <div class="list-group" id="swal_tipo_atencion_group">
                    <label class="list-group-item list-group-item-action d-flex align-items-center py-2 px-3 border rounded mb-2 ${currentTipo === 1 ? 'active' : ''}" style="cursor:pointer; transition: all 0.15s ease;">
                        <input type="radio" name="swal_tipo_radio" value="1" ${currentTipo === 1 ? 'checked' : ''} class="mr-2">
                        <span class="badge badge-success py-1 px-2 mr-2"><i class="bi bi-person-plus-fill"></i> + Nuevo</span>
                        <small class="ml-auto ${currentTipo === 1 ? 'text-white' : 'text-muted'}">Primera consulta</small>
                    </label>
                    <label class="list-group-item list-group-item-action d-flex align-items-center py-2 px-3 border rounded mb-2 ${currentTipo === 2 ? 'active' : ''}" style="cursor:pointer; transition: all 0.15s ease;">
                        <input type="radio" name="swal_tipo_radio" value="2" ${currentTipo === 2 ? 'checked' : ''} class="mr-2">
                        <span class="badge badge-primary py-1 px-2 mr-2"><i class="bi bi-arrow-repeat"></i> Control</span>
                        <small class="ml-auto ${currentTipo === 2 ? 'text-white' : 'text-muted'}">Reevaluación / Seguimiento</small>
                    </label>
                    <label class="list-group-item list-group-item-action d-flex align-items-center py-2 px-3 border rounded mb-1 ${currentTipo === 3 ? 'active' : ''}" style="cursor:pointer; transition: all 0.15s ease;">
                        <input type="radio" name="swal_tipo_radio" value="3" ${currentTipo === 3 ? 'checked' : ''} class="mr-2">
                        <span class="badge badge-warning text-dark py-1 px-2 mr-2"><i class="bi bi-person-check-fill"></i> Continuador</span>
                        <small class="ml-auto ${currentTipo === 3 ? 'text-white' : 'text-muted'}">Tratamiento prolongado</small>
                    </label>
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonColor: '#2563eb',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '<i class="bi bi-check2 mr-1"></i> Guardar Cambio',
        cancelButtonText: 'Cancelar',
        didOpen: () => {
            const group = document.getElementById('swal_tipo_atencion_group');
            if (!group) return;
            const labels = group.querySelectorAll('label');
            labels.forEach((lbl) => {
                lbl.addEventListener('click', () => {
                    labels.forEach((l) => {
                        l.classList.remove('active');
                        const small = l.querySelector('small');
                        if (small) {
                            small.classList.remove('text-white');
                            small.classList.add('text-muted');
                        }
                    });
                    lbl.classList.add('active');
                    const small = lbl.querySelector('small');
                    if (small) {
                        small.classList.remove('text-muted');
                        small.classList.add('text-white');
                    }
                    const radio = lbl.querySelector('input[type="radio"]');
                    if (radio) radio.checked = true;
                });
            });
        },
        preConfirm: () => {
            const checked = document.querySelector('input[name="swal_tipo_radio"]:checked');
            if (!checked) {
                window.Swal.showValidationMessage('Debe seleccionar un tipo de atención.');
                return false;
            }
            return checked.value;
        },
    });

    if (selectedTipo) {
        try {
            const token = window.token || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const response = await axios.post(`${getApi()}/citas/${id}/tipo-atencion`, {
                id_tipo_atencion: selectedTipo,
                _token: token,
            });

            if (response.data && response.data.status) {
                window.alertNotify?.('success', response.data.messages || 'Tipo de atención actualizado');
                if (typeof onSuccess === 'function') {
                    await onSuccess();
                }
                window.__neumoEvents?.dispatchEvent(new CustomEvent('citas:refresh'));
            } else {
                window.Swal.fire('Atención', response.data?.messages || 'No se pudo actualizar.', 'warning');
            }
        } catch (error) {
            console.error('Error actualizando tipo de atención:', error);
            window.Swal.fire('Error', 'No se pudo actualizar el tipo de atención.', 'error');
        }
    }
}
