<template>
    <span>
        <div class="row">
            <div class="col-md-9"></div>
            <div class="col-md-3">

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
            <div class="row">
                <div class="col-12">
                    <button @click="descargar" class="btn btn-app btn-secondary float-right" title="Descargar" v-if="$root.can('descargar-cfdi-pendientes-carga-add')">
                        <i class="fa fa-download"></i> Descargar
                    </button>
                </div>
            </div>
            <div class="card ">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <span style="margin-right: 5px">Total de CFDI: <b>{{total}}</b></span>
                            <span v-if="sin_poliza_contpaq>0" style="color: #f7b900; margin-right: 5px">CFDI sin correspondencia de Póliza en Contpaq: <b>{{sin_poliza_contpaq}}</b></span>
                            <span v-if="sin_cfdi_sat>0" style="color: #ff0000; margin-right: 5px">CFDI sin correspondencia en Repositorio General: <b>{{sin_cfdi_sat}}</b></span>
                        </div>
                    </div>
                    <div class="row col-md-12">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th rowspan="2">#</th>
                                    <th colspan="5">CFDI</th>
                                    <th colspan="2">Póliza SAO</th>
                                    <th colspan="3">Póliza Contpaq</th>
                                </tr>
                                <tr>
                                    <th>Tipo </th>
                                    <th>Folio </th>
                                    <th>Fecha </th>
                                    <th>Emisor </th>
                                    <th>Total </th>
                                    <th>Folio</th>
                                    <th>Fecha </th>
                                    <th>Tipo </th>
                                    <th>Fecha </th>
                                    <th>Folio </th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(cfdi, i) in cfdis">
                                    <td>{{i+1}}</td>
                                    <td style="text-align: center">{{cfdi.tipo_cfdi}}</td>
                                    <template v-if="cfdi.fecha_cfdi">
                                        <td>{{cfdi.folio_cfdi}}</td>
                                        <td style="text-align: center; min-width: 100px; max-width: 110px">{{cfdi.fecha_cfdi}}</td>
                                        <td >{{cfdi.proveedor_cfdi}}</td>
                                        <td style="text-align: right">{{cfdi.total_cfdi}}</td>
                                    </template>
                                    <template v-else>
                                        <td colspan="2">{{cfdi.uuid}}</td>
                                        <td>{{cfdi.proveedor_cfdi}}</td>
                                        <td></td>
                                    </template>

                                    <td >
                                        <enlace-consulta-poliza v-bind:folio="cfdi.folio_poliza_sao" v-bind:id="cfdi.id_poliza_sao"></enlace-consulta-poliza>
                                    </td>
                                    <td style="text-align: center">{{cfdi.fecha_poliza_sao}}</td>
                                    <td >{{cfdi.tipo_poliza_contpaq}}</td>
                                    <td style="text-align: center">{{cfdi.fecha_poliza_contpaq}}</td>
                                    <td >
                                    <enlace-consulta-poliza-contpaq
                                        v-bind:folio="cfdi.folio_poliza_contpaq"
                                        v-bind:id="cfdi.id_poliza_contpaq"
                                        v-bind:id_empresa="cfdi.id_empresa_poliza_contpaq">
                                    </enlace-consulta-poliza-contpaq>
                                    </td>
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

    import EnlaceConsultaPoliza from "../poliza/partials/EnlaceConsultaPoliza";
    import EnlaceConsultaPolizaContpaq from "../../contabilidad-general/poliza/partials/EnlaceConsultaPolizaContpaq";
    export default {
        name: "asociar-poliza-index",
        components: {EnlaceConsultaPolizaContpaq, EnlaceConsultaPoliza },
        data() {
            return {
                cargando: true,
                descargando:false,
                datos_poliza : null,
                sin_cfdi_sat : 0,
                sin_poliza_contpaq : 0,
                total : 0,
            }
        },

        mounted() {
            this.getCFDIPorCargar()
        },

        methods: {
            getCFDIPorCargar() {
                return this.$store.dispatch('contabilidad/cfdi-poliza/getCFDIPorCargar', { params: this.query })
                    .then(data => {
                        this.$store.commit('contabilidad/cfdi-poliza/SET_CFDIS', data.cfdi_pendientes);
                        this.sin_cfdi_sat = data.sin_cfdi_sat;
                        this.sin_poliza_contpaq = data.sin_poliza_contpaq;
                        this.total = data.total;
                        this.datos_cfdi = data.cfdi_pendientes.map((cfdi, i) => (
                            cfdi.uuid
                        ));
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            },
            descargar(){
                this.descargando = true;
                return this.$store.dispatch('contabilidad/cfdi-poliza/descargar',
                    {
                    })
                    .then(data => {
                        this.$emit('success');
                    }).finally(() => {
                        this.descargando = false;
                    });
            },
        },
        computed: {
            cfdis(){
                return this.$store.getters['contabilidad/cfdi-poliza/cfdis'];
            },
        },
    }
</script>
