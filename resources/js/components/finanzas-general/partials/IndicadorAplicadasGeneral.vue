<template>
    <span>
        <div class="row">

            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box" :class="indicador.color">
                    <span class="info-box-icon"><i class="fa fa-file-powerpoint"></i></span>

                    <div class="info-box-content">
                        <router-link :to="{name: 'detalle-indicador-solicitudes-pago'}" style="color: #FFFFFF; text-decoration: underline" >
                            <span class="info-box-text">

                                Solicitudes de Pago Anticipado Aplicadas

                            </span>
                        </router-link>
                        <span class="info-box-number">{{indicador.aplicados}} {{indicador.pendientes}}</span>

                        <div class="progress">
                            <div class="progress-bar"  :style="{'width': indicador.porcentaje+'%'}" ></div>
                        </div>
                        <span class="progress-description"> {{indicador.porcentaje}}% Aplicadas </span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>

        </div>

    </span>

</template>

<script>
    export default {
        name: "pago-anticipado-indicador-aplicadas-general",
        data() {
            return {
                indicador:null,
                cargando: false
            }
        },
        mounted() {
            this.find();
        },
        methods: {
            find() {
                return this.$store.dispatch('finanzas/solicitud-pago-anticipado/getIndicadorAplicadasGeneral', {
                    params: { }

                }).then(data => {
                    this.indicador = data;
                })
            }
        },
        computed: {

        }
    }
</script>
