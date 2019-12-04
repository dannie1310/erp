<template>
    <div class="card">
        <div class="card-header">
            <h6 class="card-title">Información Semanal de Prepólizas</h6>
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
        name: "poliza-graph-semanal",

        mounted() {
            axios.get('/api/chart/prepolizas-semanal')
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