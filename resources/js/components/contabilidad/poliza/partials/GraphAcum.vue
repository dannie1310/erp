<template>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Acumulados de Prepólizas</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div>
        <div class="card-body">
            <div class="chart">
                <canvas id="acumulado" width="762" height="500" ></canvas>
            </div>
        </div>
        <!-- /.card-body -->
    </div>
</template>

<script>
    export default {
        name: "poliza-graph-acum",
        mounted() {
            let self = this;
            axios.get('/api/chart/prepolizas-acumulado')
                .then(r => r.data)
                .then(data => {
                    let acum = $("#acumulado")[0].getContext("2d");
                    let dona = new Chart (acum, {
                        type: 'doughnut',
                        data: data,
                        options: {
                            responsive: true,
                            legend: {
                                position: 'top',
                            },
                            title: {
                                display: false,
                            },
                            animation: {
                                animateScale: true,
                                animateRotate: true
                            }
                        }
                    })
                    $("#acumulado").on('click', function (e) {
                        var activePoints = dona.getElementAtEvent(e);
                        if( activePoints[0]) {
                            var estatu = activePoints[0]._chart.config.data.estatus;
                            console.log(activePoints[0], estatu );
                            var url = '/contabilidad/poliza?estatus=' + estatu[activePoints[0]._index];
                            self.$router.push(url);
                        }
                    })
                })
        }
    }
</script>