<footer class="main-footer">
    <div class="footer-left d-flex align-items-center flex-wrap">
        <strong class="text-slate-800 me-1">
            <span class="text-cyan"><i class="bi bi-shield-plus mr-1"></i></span>
            {{ $enterprise->nombre_comercial ?? config('global.site_name', 'NeumoLav') }}
        </strong>
        <span class="mx-2 text-muted">&copy; {{ date('Y') }}</span>
        <span class="d-none d-md-inline text-muted">&bull; Todos los derechos reservados.</span>
    </div>

    <div class="footer-right d-flex align-items-center flex-wrap">
        <span class="footer-system-pill d-none d-sm-inline-flex align-items-center mr-2">
            <span class="spa-status-dot online mr-1"></span>
            <span>Sistema Operativo</span>
        </span>
        <span class="footer-system-pill">
            <i class="bi bi-lightning-charge text-info mr-1"></i>
            <span>v3.3.0 &bull; SPA</span>
        </span>
    </div>
</footer>

<script>
    $(function () {
        if (typeof $.fn.summernote !== 'undefined') {
            $('.summernote').summernote();
        }
    });
</script>