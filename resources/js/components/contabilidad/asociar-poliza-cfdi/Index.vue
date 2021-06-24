<template>
    <span>
        <div class="row">
            <div class="col-md-9"></div>
            <div class="col-md-3">
                <Asociar @created="getPolizasPorAsociar()" v-bind:datos_poliza="datos_poliza" v-if="datos_poliza"/>
            </div>
        </div>
        <div v-if="cargando">
            <div class="card">
                <div class="card-body">
                    <div class="row col-md-12">
                        <div class="spinner-border text-success" role="status">
                            <span class="sr-only">Cargando...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-else>
            <div class="card ">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <span style="margin-right: 5px">Total de CFDI: <b>{{polizas.length}}</b></span>
                        </div>
                    </div>
                    <div class="row col-md-12">
                        <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Tipo Póliza</th>
                                <th>Tipo Póliza SAO</th>
                                <th>Folio Póliza (Contpaq)</th>
                                <th>Folio Póliza (SAO)</th>
                                <th>Fecha</th>
                                <th>Concepto</th>
                                <th>Total</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(poliza, i) in polizas">
                                <td>{{i+1}}</td>
                                <td>{{poliza.tipo_contpaq}}</td>
                                <td style="text-align: center">{{poliza.tipo_sao}}</td>
                                <td style="text-align: center">{{poliza.folio_contpaq}}</td>
                                <td style="text-align: center">{{poliza.folio_sao}}</td>
                                <td style="text-align: center">{{poliza.fecha}}</td>
                                <td >{{poliza.concepto}}</td>
                                <td style="text-align: right">{{poliza.total}}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    import Asociar from "./Asociar";
    export default {
        name: "asociar-poliza-index",
        components: {Asociar},
        data() {
            return {
                cargando: true,
                datos_poliza: null
            }
        },

        mounted() {
            this.getPolizasPorAsociar()
        },

        methods: {
            getPolizasPorAsociar() {
                return this.$store.dispatch('contabilidad/poliza/getPolizasPorAsociar', { params: this.query })
                    .then(data => {
                        this.$store.commit('contabilidad/poliza/SET_POLIZAS', data);
                        this.datos_poliza = data.map((poliza, i) => (
                            poliza.id_poliza_global
                        ));
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
