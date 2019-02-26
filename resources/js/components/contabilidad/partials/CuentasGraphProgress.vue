<template>
    <div class="card" v-if="config">
        <div class="card-header">
            <h3 class="card-title">Información de Cuentas Contables</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <p><b>Cuentas de Almacénes</b></p>

            <div class="progress">
                <div class="progress-bar bg-primary progress-bar-striped" role="progressbar" :style="`width: ${almacen}%`">
                    <span>{{ `${almacen} %` }}</span>
                </div>
            </div>

            <p><b>Cuentas de Conceptos</b></p>

            <div class="progress">
                <div class="progress-bar bg-success progress-bar-striped" role="progressbar" :style="`width: ${concepto}%`">
                    <span>{{ `${concepto} %` }}</span>
                </div>
            </div>

            <p><b>Cuentas de Empresas</b></p>

            <div class="progress">
                <div class="progress-bar bg-info progress-bar-striped" role="progressbar" :style="`width: ${empresa}%`">
                    <span>{{ `${empresa} %` }}</span>
                </div>
            </div>

            <p><b>Cuentas de Fondos</b></p>

            <div class="progress">
                <div class="progress-bar bg-gray progress-bar-striped" role="progressbar" :style="`width: ${fondo}%`">
                    <span>{{ `${fondo} %` }}</span>
                </div>
            </div>

            <p><b>Cuentas de Materiales</b></p>

            <div class="progress">
                <div class="progress-bar bg-warning progress-bar-striped" role="progressbar" :style="`width: ${material}%`">
                    <span>{{ `${material} %` }}</span>
                </div>
            </div>

        </div>
        <!-- /.card-body -->
    </div>
</template>

<script>
    export default {
        name: "cuentas-graph-progress",
        data() {
            return {
                config: null
            }
        },

        mounted() {
            axios.get('/api/chart/avance-cuentas-contables')
                .then(r => r.data)
                .then(data => {
                    this.config = data;
                })
        },

        computed: {
            almacen() {
                return Math.round(this.config.almacen.con_cuenta * 100 / this.config.almacen.total)
            },
            concepto() {
                return Math.round(this.config.concepto.con_cuenta * 100 / this.config.concepto.total)
            },
            empresa() {
                return Math.round(this.config.empresa.con_cuenta * 100 / this.config.empresa.total)
            },
            material() {
                return Math.round(this.config.material.con_cuenta * 100 / this.config.material.total)
            },
            fondo() {
                return Math.round(this.config.fondo.con_cuenta * 100 / this.config.fondo.total)
            }
        }
    }
</script>