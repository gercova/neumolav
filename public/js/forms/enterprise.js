$(document).ready(function(){
    //crear nuevo logo
    $(".foto-representante").change(function(){
        let imagen = this.files[0];
        /*=============================================
        VALIDAMOS EL FORMATO DE LA IMAGEN SEA JPG O PNG
        =============================================*/
        if(imagen["type"] != "image/jpeg" && imagen["type"] != "image/png"){
            $(".mini-logo").val("");
            swal.fire({
                title: "Error al subir la imagen",
                text: "¡La imagen debe estar en formato JPG o PNG!",
                type: "error",
                confirmButtonText: "¡Cerrar!"
            });
        }else if(imagen["size"] > 2000000){
            $(".mini-logo").val("");
            swal.fire({
                title: "Error al subir la imagen",
                text: "¡La imagen no debe pesar más de 2MB!",
                type: "error",
                confirmButtonText: "¡Cerrar!"
            });
        }else{
            let datosImagen = new FileReader;
            datosImagen.readAsDataURL(imagen);
            $(datosImagen).on("load", function(event){
                let rutaImagen = event.target.result;
                $(".preview-representante").attr("src", rutaImagen);

            })
        }
    });
    //crear nuevo logo
    //crear nuevo logo
    $(".mini-logo").change(function(){
        let imagen = this.files[0];
        /*=============================================
        VALIDAMOS EL FORMATO DE LA IMAGEN SEA JPG O PNG
        =============================================*/
        if(imagen["type"] != "image/jpeg" && imagen["type"] != "image/png"){
            $(".mini-logo").val("");
            swal.fire({
                title: "Error al subir la imagen",
                text: "¡La imagen debe estar en formato JPG o PNG!",
                type: "error",
                confirmButtonText: "¡Cerrar!"
            });
        }else if(imagen["size"] > 2000000){
            $(".mini-logo").val("");
            swal.fire({
                title: "Error al subir la imagen",
                text: "¡La imagen no debe pesar más de 2MB!",
                type: "error",
                confirmButtonText: "¡Cerrar!"
            });
        }else{
            let datosImagen = new FileReader;
            datosImagen.readAsDataURL(imagen);
            $(datosImagen).on("load", function(event){
                let rutaImagen = event.target.result;
                $(".preview-mini-logo").attr("src", rutaImagen);
            })
        }
    });
    //crear nuevo logo
    $(".logo").change(function(){
        let imagen = this.files[0];
        /*=============================================
        VALIDAMOS EL FORMATO DE LA IMAGEN SEA JPG O PNG
        =============================================*/
        if(imagen["type"] != "image/jpeg" && imagen["type"] != "image/png"){
            $(".logo").val("");
            swal.fire({
                title: "Error al subir la imagen",
                text: "¡La imagen debe estar en formato JPG o PNG!",
                type: "error",
                confirmButtonText: "¡Cerrar!"
            });
        }else if(imagen["size"] > 2000000){
            $(".logo").val("");
            swal.fire({
                title: "Error al subir la imagen",
                text: "¡La imagen no debe pesar más de 2MB!",
                type: "error",
                confirmButtonText: "¡Cerrar!"
            });
        }else{
            let datosImagen = new FileReader;
            datosImagen.readAsDataURL(imagen);
            $(datosImagen).on("load", function(event){
                let rutaImagen = event.target.result;
                $(".preview-logo").attr("src", rutaImagen);
            })
        }
    });
    //crear nuevo logo
    $(".logo-receta").change(function(){
        let imagen = this.files[0];
        /*=============================================
        VALIDAMOS EL FORMATO DE LA IMAGEN SEA JPG O PNG
        =============================================*/
        if(imagen["type"] != "image/jpeg" && imagen["type"] != "image/png"){
            $(".logo-receta").val("");
            swal.fire({
                title: "Error al subir la imagen",
                text: "¡La imagen debe estar en formato JPG o PNG!",
                type: "error",
                confirmButtonText: "¡Cerrar!"
            });
        }else if(imagen["size"] > 2000000){
            $(".logo-receta").val("");
            swal.fire({
                title: "Error al subir la imagen",
                text: "¡La imagen no debe pesar más de 2MB!",
                type: "error",
                confirmButtonText: "¡Cerrar!"
            });
        }else{
            let datosImagen = new FileReader;
            datosImagen.readAsDataURL(imagen);
            $(datosImagen).on("load", function(event){
                let rutaImagen = event.target.result;
                $(".preview-logo-receta").attr("src", rutaImagen);
            })
        }
    })
    //Funcion para validar datos antes de ser enviados al controlador para guardar o actualizar un examen
    $('#enterpriseForm').submit(async function (e) {
        e.preventDefault();
        const submitButton = $('#button_send_data_etp');
        const originalButtonText = submitButton.html(); // Guardar el texto original del botón
        submitButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Cargando...');
        let formData = new FormData(this);
        try {
            const response = await axios.post(`${API_BASE_URL}/enterprise/store`, formData);

            if(response.status == 200 && response.data.status == true){
                alertNotify(response.data.type, response.data.messages);
                //window.location.href = `${API_BASE_URL}/enterprise`;
            } else if(response.data.status == false){
                alertNotify(response.data.type, response.data.messages);
            }
        } finally {
            // Restaurar el botón de envío a su estado original
            submitButton.prop('disabled', false).html(originalButtonText);
        }
    });
    
    $('#fotorepresentanteForm').submit(async function(e){
        e.preventDefault();
        const formDataLogo = new FormData(this);
        try {
            const response = await axios.post(`${API_BASE_URL}/enterprise/store`, formDataLogo);
            if(response.status == 200 && response.data.status == true){
                const result = await swal.fire({
                    icon: response.data.type,
                    title: response.data.messages,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Aceptar',
                });
                if (result.isConfirmed) {
                    window.location.href = `${API_BASE_URL}/enterprise`;
                };   
            }
        } catch (error) {
            console.error(error);
        }
    });

    $('#logoForm').submit(async function(e){
        e.preventDefault();
        const formDataLogo = new FormData(this);
        try {
            const response = await axios.post(`${API_BASE_URL}/enterprise/store`, formDataLogo);
            if(response.status == 200 && response.data.status == true){
                const result = await swal.fire({
                    icon: response.data.type,
                    title: response.data.messages,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Aceptar',
                });
                if (result.isConfirmed) {
                    window.location.href = `${API_BASE_URL}/enterprise`;
                    //$('#image-container').html('<img src="' + response.data.image_url + '" alt="Uploaded Image">');
                };   
            }
        } catch (error) {
            console.error(error);
        }
    });

    $('#logoMinForm').submit(async function(e){
        e.preventDefault();
        const formDataLogoMin = new FormData(this);
        try {
            const response = await axios.post(`${API_BASE_URL}/enterprise/store`, formDataLogoMin);
            if (response.status == 200 && response.data.status == true) {
                const result = await swal.fire({
                    icon: response.data.type,
                    title: response.data.messages,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Aceptar',
                });
                if(result.isConfirmed){
                    window.location.href = `${API_BASE_URL}/enterprise`;
                };   
            }
        } catch (error) {
            console.log(error);
        }
    });

    $('#logoBackgroundForm').submit(async function(e){
        e.preventDefault();
        const formDataLogoMin = new FormData(this);
        try {
            const response = await axios.post(`${API_BASE_URL}/enterprise/store`, formDataLogoMin);
            if (response.status == 200 && response.data.status == true) {
                const result = await swal.fire({
                    icon: response.data.type,
                    title: response.data.messages,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Aceptar',
                });
                if (result.isConfirmed) {
                    window.location.href = `${API_BASE_URL}/enterprise`;
                }
            }
        } catch (error) {
            console.log(error);
        }
    });

    async function loadImages(){
        try {
            const response = await axios.get(`${API_BASE_URL}/enterprise/images`);
            if (response.status == 200) {
                $('.preview-representante').attr("src", response.data.foto_representante);
                $('.preview-logo').attr("src", response.data.logo);
                $('.preview-mini-logo').attr("src", response.data.logo_mini);
                $('.preview-logo-receta').attr("src", response.data.logo_receta);
            }
        } catch (error) {
            console.log(error);
        }
    }

    loadImages();
});