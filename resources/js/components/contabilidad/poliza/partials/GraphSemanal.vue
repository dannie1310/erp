<template>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Información Semanal de Prepólizas</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div>
        <div class="card-body">
            <div class="chart">
                <canvas id="psg" width="1428" height="300" ></canvas>
            </div>
        </div>
        <!-- /.card-body -->
    </div>

</template>

<script>
    export default {
        name: "poliza-grap-semanal",

        mounted() {
            let self = this
            axios.get('/api/contabilidad/poliza/chartjs/semanal')
                .then(r => r.data)
                .then(data => {
                    let prepolizas = document.getElementById("psg").getContext("2d");
                    new Chart(prepolizas, {
                        type: 'line',
                        data: data,
                        options: {
                            responsive: true,
                            title:{
                                display:true,
                                text:`Del ${data.labels[0]} al ${data.labels[data.labels.length -1]}`
                            },
                            tooltips: {
                                mode: 'index',
                                intersect: false,
                            },
                            hover: {
                                mode: 'nearest',
                                intersect: true
                            },
                            scales: {
                                xAxes: [{
                                    display: true,
                                    scaleLabel: {
                                        display: true,
                                        labelString: 'Día'
                                    }
                                }],
                                yAxes: [{
                                    display: true,
                                    scaleLabel: {
                                        display: true,
                                        labelString: 'No. de Prepólizas'
                                    }
                                }]
                            }
                        }
                    });
                })
        }
    }
</script>

<style scoped>

</style>