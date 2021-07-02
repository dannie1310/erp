<template>
    <span>
        <div class="row">
            <div class="col-md-9"></div>
            <div class="col-md-3">
                <button @click="asociar"  class="btn btn-app pull-right">
                    <i class="fa fa-share-alt"></i> Asociar
                </button>
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
                            <span style="margin-right: 5px">Total de CFDI: <b>{{cfdis_pendientes.length}}</b></span>
                        </div>
                    </div>
                    <div class="row col-md-12">
                        <div class="table-responsive">
                        <table class="table table-striped table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th rowspan="2">#</th>
                                    <th colspan="5">CFDI</th>
                                    <th colspan="2">Póliza SAO</th>
                                    <th colspan="3">Póliza Contpaq</th>
                                    <th rowspan="2" style="width: 10px"></th>
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
                            <tr v-for="(cfdi, i) in cfdis_pendientes">
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
                                    <td>
                                        <div class="form-check form-check-inline">
                                            <label class="form-check-label" style="cursor:pointer" >
                                                <input class="form-check-input" type="checkbox" name="enviar" v-model="cfdi.seleccionado" value="1" >
                                            </label>
                                        </div>
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
    import Asociar from "./Asociar";
    import EnlaceConsultaPolizaContpaq from "../../contabilidad-general/poliza/partials/EnlaceConsultaPolizaContpaq";
    import EnlaceConsultaPoliza from "../poliza/partials/EnlaceConsultaPoliza";
    export default {
        name: "asociar-poliza-index",
        components: {EnlaceConsultaPoliza, EnlaceConsultaPolizaContpaq, Asociar},
        data() {
            return {
                cargando: true,
                datos_poliza: null,
                cfdis_pendientes : [],
                cfdi_store : [],
                datos_store : {}
            }
        },

        mounted() {
            this.getPolizasPorAsociar()
        },

        methods: {
            getPolizasPorAsociar() {
                return this.$store.dispatch('contabilidad/poliza/getPolizasPorAsociar', { params: this.query })
                    .then(data => {
                        this.cfdis_pendientes = data;
                        //this.$store.commit('contabilidad/poliza/SET_POLIZAS', data);
                        /*this.datos_poliza = data.map((poliza, i) => (
                            poliza.id_poliza_sao
                        ));*/
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            },
            asociar()
            {
                var item_a_guardar = 0;

                let _self = this;
                this.cfdis_pendientes.forEach(function(element) {
                    if(element.seleccionado == 1)
                    {
                        item_a_guardar = item_a_guardar + 1;
                        _self.cfdi_store.push(element.id_poliza_sao);
                    }
                });
                if(item_a_guardar > 0)
                {
                    this.datos_store["cfdi"] = _self.cfdi_store;
                    /*return this.$store.dispatch('contabilidad/cfdi-poliza/cargar',
                        this.datos_store)
                        .then(data => {
                            this.$emit('success');
                        }).finally(() => {
                            this.getCFDIPorCargar();
                        });*/

                    return this.$store.dispatch('contabilidad/poliza/asociarCFDI',
                        this.datos_store
                    ).then((data) => {
                        this.$emit('success')
                    }).finally(() => {
                        this.getPolizasPorAsociar();
                    });
                }
            }
        },
        computed: {
            /*polizas(){
                return this.$store.getters['contabilidad/poliza/polizas'];
            },*/
        },
    }
</script>
