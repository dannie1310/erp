<template>
    <div class="row">
        <div class="col-12">
            <Asociar @created="getPolizasPorAsociar()" v-bind:datos_poliza="datos_poliza" v-if="datos_poliza"/>
        </div>
        <div class="col-12">
            <div class="card">
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="table-responsive col-md-12">
                        <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th class="no_parte">No. de Parte</th>
                                    <th>Descripción</th>
                                    <th class="unidad">Unidad</th>
                                    <th class="no_parte">Cantidad</th>
                                    <th class="fecha">Fecha de Entrega</th>
                                    <th>Destino</th>
                                    <th>Observaciones</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(poliza, i) in polizas.data">
                                    <td>{{i+1}}</td>
                                    <td style="text-align: center"><b>{{poliza}}</b></td>
                                  <!--  <td style="text-align: center">{{partida.material.descripcion}}</td>
                                    <td style="text-align: center">{{partida.material.unidad}}</td>
                                    <td style="text-align: center">{{partida.cantidad}}</td>
                                    <td style="text-align: center">{{(partida.entrega) ? partida.entrega.fecha_format : '------------'}}</td>

                                    <td v-if="partida.entrega && partida.entrega.destino_path" :title="`${partida.entrega.destino_path}`"><u>{{partida.entrega.destino_descripcion}}</u></td>
                                    <td v-else-if="partida.entrega" >{{partida.entrega.destino_descripcion}}</td>
                                    <td v-else></td>

                                    <td style="text-align: left">{{(partida.complemento) ? partida.complemento.observaciones : '------------'}}</td>
                                    -->
                                </tr>
                                </tbody>
                            </table>
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->
    </div>
</template>

<script>
    import Asociar from "./Asociar";
    export default {
        name: "asociar-poliza-index",
        components: {Asociar},
        data() {
            return {
                query: {
                    sort: 'fecha',
                    order: 'desc',
                    scope: 'getAsociarCFDI'
                },
                cargando: false,
                datos_poliza: null
            }
        },

        mounted() {
            this.getPolizasPorAsociar()
        },

        methods: {
            getPolizasPorAsociar() {
                this.cargando = true;
                return this.$store.dispatch('contabilidad/poliza/getPolizasPorAsociar', { params: this.query })
                    .then(data => {
                        this.$store.commit('contabilidad/poliza/SET_POLIZAS', data.data);
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            },
        },
        computed: {
            polizas(){
                return this.$store.getters['contabilidad/poliza/polizas'];
            },
        },
    }
</script>
