<div class="modal fade" id="modalAddOccupation" tabindex="-1" role="dialog" aria-labelledby="modalAddOccupationLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title font-weight-bold" id="modalAddOccupationLabel">
                    <i class="fas fa-briefcase mr-1 text-primary"></i> Registrar Nueva Ocupación
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formNewOccupation" novalidate>
                @csrf
                <div class="modal-body">
                    <div class="form-group mb-0">
                        <label for="new_occupation_desc">Descripción <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control text-uppercase"
                               id="new_occupation_desc"
                               name="descripcion"
                               placeholder="Ingrese el nombre de la ocupación"
                               maxlength="100"
                               autocomplete="off"
                               required>
                        <div class="invalid-feedback" id="newOccupationFeedback"></div>
                        <small class="form-text text-muted">La nueva ocupación se guardará y seleccionará automáticamente.</small>
                    </div>
                </div>
                <div class="modal-footer bg-light justify-content-between">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary" id="btnSaveNewOccupation">
                        <i class="fas fa-save mr-1"></i> Guardar Ocupación
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
