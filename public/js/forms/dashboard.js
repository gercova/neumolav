$(document).ready(function(){

    async function hcCount(){
        try {
            const response = await axios.get(`${API_BASE_URL}/dashboard/hcCount/`);
            if(response.status == 200){
                console.log(response.data);
                $('#historiesCount').text(response.data.hc).append(`&nbsp;<small>historias</small>`);
                $('#examsCount').text(response.data.ex).append(`&nbsp;<small>exámenes</small>`);
                $('#appointmentsCount').text(response.data.ap).append(`&nbsp;<small>controles</small>`);
                $('#dailyQuotesCount').text(response.data.cd).append(`&nbsp;<small>pacientes</small>`);
            }
        } catch (error) {
            console.log(error);
        }
    }

    hcCount();

    const year = (new Date).getFullYear();
    //histories(year);
    loadHistoriesChart(year);
    loadExamsChart(year);
    loadAppointmentsChart(year);
    diagnosisByExams();
    drugByExams();
    historiesBySex();
    historiesBySmoking();
    historiesByBloodingGroup();
    historiesByDegreeIntruction();
    historiesByMaritalStatus();

    $('#year').on('change', function() {
        const year = $(this).val();
        if (year) {
            loadHistoriesChart(year);
            loadExamsChart(year);
            loadAppointmentsChart(year);
        }
    });

    async function loadHistoriesChart(year) {
        const monthNames = ['ENE', 'FEB', 'MAR', 'ABR', 'MAY', 'JUN', 'JUL', 'AGO', 'SEP', 'OCT', 'NOV', 'DIC'];
        
        try {
            const response = await axios.get(`${API_BASE_URL}/dashboard/histories/${year}`);
            
            if (response.data) {
                renderHistoriesChart({
                    categories: monthNames,
                    counts: response.data.counts,
                    year: year
                });
            }
        } catch (error) {
            console.error('Error loading histories data:', error);
            showErrorNotification('No se pudo cargar los datos de historias clínicas');
        }
    }
    
    function renderHistoriesChart({categories, counts, year}) {
        Highcharts.chart('histories', {
            chart: { type: 'column' },
            title: { text: 'Pacientes nuevos' },
            subtitle: { text: `Año: ${year}` },
            xAxis: {
                categories: categories,
                crosshair: true
            },
            yAxis: {
                min: 0,
                title: { text: 'Cantidad de pacientes' }
            },
            tooltip: {
                headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                pointFormat: `
                    <tr>
                        <td style="color:{series.color};padding:0">Cantidad: </td>
                        <td style="padding:0"><b>{point.y} pacientes</b></td>
                    </tr>
                `,
                footerFormat: '</table>',
                shared: true,
                useHTML: true
            },
            plotOptions: {
                column: {
                    pointPadding: 0.2,
                    borderWidth: 0
                },
                series: {
                    dataLabels: {
                        enabled: true,
                        formatter: function() {
                            return Highcharts.numberFormat(this.y, 0);
                        }
                    }
                }
            },
            series: [{
                name: 'Pacientes',
                data: counts
            }]
        });
    }
    
    async function loadExamsChart(year){
        const monthNames = ['ENE', 'FEB', 'MAR', 'ABR', 'MAY', 'JUN', 'JUL', 'AGO', 'SEP', 'OCT', 'NOV', 'DIC'];
        try {
            const response = await axios.get(`${API_BASE_URL}/dashboard/exams/${year}`);
            if(response.status == 200){
                renderExamsChart({
                    categories: monthNames, 
                    counts: response.data.counts, 
                    year: year
                });
            }
        } catch (error) {
            console.error('Error loading histories data:', error);
            showErrorNotification('No se pudo cargar los datos de historias clínicas');
        }
    }

    function renderExamsChart({categories, counts, year}){
        Highcharts.chart('exams', {
            chart: { type: 'column' },
            title: { text: 'Exámenes nuevos' },
            subtitle: { text: 'Año: ' + year },
            xAxis: {
                categories: categories,
                crosshair: true
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Cantidad de exámenes'
                }
            },
            tooltip: {
                headerFormat: `<span style="font-size:10px">{point.key}</span><table>`,
                pointFormat: `
                    <tr>
                        <td style="color:{series.color};padding:0">Cantidad: </td>
                        <td style="padding:0"><b>{point.y:f} exámenes</b></td>
                    </tr>
                `,
                footerFormat: '</table>',
                shared: true,
                useHTML: true
            },
            plotOptions: {
                column: {
                    pointPadding: 0.2,
                    borderWidth: 0
                },
                series:{
                    dataLabels:{
                        enabled:true,
                        formatter:function(){
                            return Highcharts.numberFormat(this.y, 0);
                        }
                    }
                }
            },
            series: [{
                name: 'Meses',
                data: counts
            }]
        });
    }

    async function loadAppointmentsChart(year){
        const monthNames = ['ENE', 'FEB', 'MAR', 'ABR', 'MAY', 'JUN', 'JUL', 'AGO', 'SEP', 'OCT', 'NOV', 'DIC'];
        try {
            const response = await axios.get(`${API_BASE_URL}/dashboard/appointments/${year}`);
            if(response.status == 200){
                renderAppointmentsChart({
                    categories: monthNames, 
                    counts: response.data.counts, 
                    year: year
                });
            }
        } catch (error) {
            console.error(error);
        }
    }

    function renderAppointmentsChart({categories, counts, year}){
        Highcharts.chart('appointments', {
            chart: { type: 'column' },
            title: { text: 'Citas nuevas' },
            subtitle: { text: 'Año:'+ year },
            xAxis: {
                categories: categories,
                crosshair: true
            },
            yAxis: {
                min: 0,
                title: { text: 'Cantidad de citas' }
            },
            tooltip: {
                headerFormat: `<span style="font-size:10px">{point.key}</span><table>`,
                pointFormat: `
                    <tr>
                        <td style="color:{series.color};padding:0">Cantidad: </td>
                        <td style="padding:0"><b>{point.y:f} citas</b></td>
                    </tr>
                `,
                footerFormat: '</table>',
                shared: true,
                useHTML: true
            },
            plotOptions: {
                column: { pointPadding: 0.2, borderWidth: 0 },
                series:{
                    dataLabels:{
                        enabled:true,
                        formatter:function(){
                            return Highcharts.numberFormat(this.y, 0);
                        }
                    }
                }
            },
            series: [{
                name: 'Meses',
                data: counts
            }]
        });
    }
    
    async function diagnosisByExams(){
        try {
            const response = await axios.get(`${API_BASE_URL}/dashboard/diagnosticsByExam`);
            if(response.status == 200){
                const values = new Array();
                for(const i in response.data){
                    const value = new Array(response.data[i].diagnostico, Number(response.data[i].cantidad));
                    values.push(value);
                }
                graphicDiagnosticByExams(values);
            }
        } catch (error) {
            console.error(error);
        }
    }

    function graphicDiagnosticByExams(values){
        Highcharts.chart('diagnosisByExams', {
            chart: {
                type: 'pie'
            },
            title: {
                text: 'Diagnósticos'
            },
            subtitle: {
                text: 'Diagnósticos de pacientes'
            },
            tooltip: {
                pointFormat: `{values.name} Cantidad: <b>{point.y}</b> <br>Porcentaje: <b>{point.percentage:.1f}%</b>`
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: false
                    },
                    showInLegend: true
                },
                series:{
                    dataLabels:{
                        enabled:true,
                    }
                }
            },
            series: [{
                name: 'Nro. de diagnósticos',
                colorByPoint: true,
                data: values
            }]
        });
    }

    async function drugByExams(){
        try {
            const response = await axios.get(`${API_BASE_URL}/dashboard/drugsByExam`);
            if(response.status == 200){
                const values = new Array();
                for(const i in response.data){
                    const value = new Array(response.data[i].droga, Number(response.data[i].cantidad));
                    values.push(value);
                }
                graphicDrugByExams(values)
            }
        } catch (error) {
            console.log(error);
        }
    }

    function graphicDrugByExams(values){
        Highcharts.chart('drugByExams', {
            chart: {
                type: 'pie'
            },
            title: {
                text: 'Fármacos y/o Tratamiento'
            },
            subtitle: {
                text: 'Recetas de pacientes'
            },
            tooltip: {
                pointFormat: `{values.name} Cantidad: <b>{point.y}</b> <br>Porcentaje: <b>{point.percentage:.1f}%</b>`
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: false
                    },
                    showInLegend: true
                },
                series:{
                    dataLabels:{
                        enabled:true,
                    }
                }
            },
            series: [{
                name: 'Nro. de diagnósticos',
                colorByPoint: true,
                data: values
            }]
        });   
    }

    async function historiesBySex(){
        try{
            const response = await axios.get(`${API_BASE_URL}/dashboard/historiesBySex`);
            if(response.status == 200){
                const series = new Array();
                for(const i in response.data){
                    const serie = new Array(response.data[i].sexo, Number(response.data[i].cantidad));
                    series.push(serie);
                }
                graphicHistoriesBySex(series);
            }
        } catch (error) {
            console.error(error);
        }
    }

    function graphicHistoriesBySex(series){
        Highcharts.chart('historiesBySex', {
            chart: {
                type: 'pie'
            },
            title: {
                text: 'Pacientes por sexo'
            },
            subtitle: {
                text: 'Sexo de pacientes'
            },
            tooltip: {
                pointFormat: `{series.name}: {point.y} <b>{point.percentage:.1f}%</b>`
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: false
                    },
                    showInLegend: true
                },
                series:{
                    dataLabels:{
                        enabled:true,
                    }
                }
            },
            series: [{
                name: 'Nro. de pacientes',
                colorByPoint: true,
                data: series
            }]
        });
    }

    async function historiesByDegreeIntruction(){
        try {
            const response = await axios.get(`${API_BASE_URL}/dashboard/historiesByDegreeIntruction`);
            if(response.status == 200){
                const values = [];
                for(const i in response.data){
                    const indice = new Array(response.data[i].grado_instruccion, Number(response.data[i].cantidad));
                    values.push(indice);
                }
                graphicHistoriesByDegreeIntruction(values);
            }
        } catch (error) {
            console.error(error);
        }
    }

    function graphicHistoriesByDegreeIntruction(values){
        Highcharts.chart('historiesByDegreeIntruction', {
            chart: {
                type: 'pie'
            },
            title: {
                text: 'Nivel de instrucción'
            },
            subtitle: {
                text: 'Data Base Neumotar'
            },
            tooltip: {
                pointFormat: '{series.name}: {point.y} <b>{point.percentage:.1f}%</b>'
            }, 
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: false
                    },
                    showInLegend: true
                },
                series:{
                    dataLabels:{
                        enabled:true,
                    }
                }
            },
            series: [{
                name: 'Cantidad',
                colorByPoint: true,
                data: values
            }]
        });
    }

    async function historiesByMaritalStatus(){
        try {
            const response = await axios.get(`${API_BASE_URL}/dashboard/historiesByMaritalStatus`);
            if(response.status == 200){
                const values = [];
                for(const i in response.data){
                    const indice = new Array(response.data[i].estado_civil, Number(response.data[i].cantidad));
                    values.push(indice);
                }
                graphicHistoriesByMaritalStatus(values);
            }
        } catch (error) {
            console.error(error);
        }
    }

    function graphicHistoriesByMaritalStatus(values){
        Highcharts.chart('historiesByMaritalStatus', {
            chart: {
                type: 'pie'
            },
            title: {
                text: 'Estado civil'
            },
            subtitle: {
                text: 'Data Base Neumotar'
            },
            tooltip: { 
                pointFormat: '{series.name}: {point.y} <b>{point.percentage:.1f}%</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: false
                    },
                    showInLegend: true
                },
                series:{
                    dataLabels:{
                        enabled:true,
                    }
                }
            },
            series: [{
                name: 'Cantidad',
                colorByPoint: true,
                data: values
            }]
        });
    }

    async function historiesByBloodingGroup(){
        try {
            const response = await axios.get(`${API_BASE_URL}/dashboard/historiesByBloodingGroup`);
            if(response.status == 200){
                const values = [];
                for(const i in response.data){
                    const indice = new Array(response.data[i].grupo_sanguineo, Number(response.data[i].cantidad));
                    values.push(indice);
                }
                graphicHistoriesByBloodingGroup(values);
            }
        } catch (error) {
            console.error(error);   
        }
    }

    function graphicHistoriesByBloodingGroup(values){
        Highcharts.chart('historiesByBloodingGroup', {
            chart: {
                type: 'pie'
            },
            title: {
                text: 'Grupos sanguíneos de pacientes'
            },
            subtitle: {
                text: 'Data Base Neumotar'
            },
            tooltip: {
                pointFormat: '{series.name}: {point.y} <b>{point.percentage:.1f}%</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: false
                    },
                    showInLegend: true
                },
                series:{
                    dataLabels:{
                        enabled:true,
                    }
                }
            },
            series: [{
                name: 'Cantidad',
                colorByPoint: true,
                data: values
            }]
        });
    }

    async function historiesBySmoking(){
        try {
            const response = await axios.get(`${API_BASE_URL}/dashboard/historiesBySmoking`);
            if(response.status == 200){
                const values = new Array();    
                for(const i in response.data){
                    const serie = new Array(response.data[i].tabaquismo, Number(response.data[i].cantidad));
                    values.push(serie);
                }
                graphicHistoriesBySmoking(values);
            };
        } catch (error) {
            console.error(error);   
        }
    }

    function graphicHistoriesBySmoking(values){
        Highcharts.chart('historiesBySmoking', {
            chart: {
                type: 'column'
            },
            title: {
                text: 'Tipo de Tabaquismo'
            },
            subtitle: {
                text: 'Data Base Neumotar'
            },
            xAxis: {
                type: 'category',
                labels: {
                    rotation: -45,
                    style: {
                        fontSize: '13px',
                        fontFamily: 'Verdana, sans-serif'
                    }
                }
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Datos recolectados'
                }
            },
            legend: {
                enabled: false
            },
            tooltip: {
                pointFormat: 'Nro de pacientes: {point.y}'
            },
            series: [{
                name: 'Population',
                data: values,
                dataLabels: {
                    enabled: true,
                    rotation: -90,
                    color: '#FFFFFF',
                    align: 'right',
                    format: '{point.y:f}', // one decimal
                    y: 10, // 10 pixels down from the top
                    style: {
                        fontSize: '13px',
                        fontFamily: 'Verdana, sans-serif'
                    }
                }
            }]
        });
    }
});