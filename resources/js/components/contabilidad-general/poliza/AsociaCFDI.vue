<template>
    <span>
        <div class="row" >
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <poliza-partial-show v-bind:id="this.id" v-bind:id_empresa="this.id_empresa"></poliza-partial-show>
                        <br>
                        <div class="row" v-if="!cargando">
                            <hr>
                            <div class="col-md-12 table-responsive" v-if="poliza && poliza.posibles_cfdi.data.length > 0">
                                <strong>
                                    Lista de posibles CFDI
                                </strong>
                                <table class="table table-sm table-fs-sm">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th style="width: 15px">T</th>
                                            <th style="width: 70px">Fecha</th>
                                            <th style="width: 180px">Razón Social</th>
                                            <th style="width: 200px">UUID</th>
                                            <th>Serie</th>
                                            <th>Folio</th>
                                            <th>Conceptos</th>
                                            <th>IVA</th>
                                            <th>Monto</th>
                                            <th style="width: 15px"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(cfdi, i) in poliza.posibles_cfdi.data" :style="cfdi.grado_coincidencia ==3?`background-color : lightgreen`:``">
                                            <td>{{parseInt(i)+1}}</td>
                                            <td>{{cfdi.tipo_comprobante}}</td>
                                            <td>{{cfdi.fecha_cfdi}}</td>
                                            <td>{{cfdi.razon_social}}</td>
                                            <td>{{cfdi.uuid}}</td>
                                            <td>{{cfdi.serie}}</td>
                                            <td>{{cfdi.folio}}</td>
                                            <td>{{cfdi.conceptos_txt}}</td>
                                            <td style="text-align: right">{{parseFloat(cfdi.importe_iva).formatMoney(2)}}</td>
                                            <td style="text-align: right">{{parseFloat(cfdi.total).formatMoney(2)}}</td>
                                            <td style="text-align: center">
                                                <div class="form-check form-check-inline">
                                                    <label class="form-check-label" style="cursor:pointer" >
                                                        <input class="form-check-input" type="checkbox" name="enviar" :value="cfdi.seleccionado" @change="updateSeleccionado(cfdi)">
                                                    </label>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                            </div>
                            <div v-else>
                                <div class="row">
                                    <div class="col-md-12">
                                        Sin CFDI detectados.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="pull-right">
                            <button @click="asociar"  class="btn btn-danger  pull-right">
                                <i class="fa fa-share-alt"></i> Asociar
                            </button>
                            <button type="button" class="btn btn-secondary " v-on:click="regresar"><i class="fa fa-angle-left"></i>Regresar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
import CFDI from "../../fiscal/cfd/cfd-sat/CFDI";
import PolizaPartialShow from "./partials/PartialShow";
import PDFPoliza from "./partials/PDFPoliza";
import ListaCfdiAsociar from "./ListaCFDI.vue";

export default {
    name: "poliza-asocia-cfdi",
    props : ['id', 'id_empresa'],
    components: {ListaCfdiAsociar, PDFPoliza, PolizaPartialShow, CFDI},
    data() {
        return {
            cargando :false,
            cfdi_store : [],

        }
    },
    mounted() {
        //this.find();
    },
    methods :{
        regresar() {
            this.$router.push({name: 'poliza-contpaq', params: {id_empresa: this.id_empresa}});
        },
        asociar()
        {
            var item_a_guardar = 0;
            let _self = this;

            _self.cfdi_store = [];

            this.poliza.posibles_cfdi.data.forEach(function(element) {
                if(element.seleccionado === true || element.seleccionado === 1)
                {
                    item_a_guardar = item_a_guardar + 1;
                    _self.cfdi_store.push(element.id);
                }
            });
            if(item_a_guardar > 0)
            {
                return this.$store.dispatch('contabilidadGeneral/poliza/asociarCFDI',
                    {"cfdi":_self.cfdi_store,
                        "id_poliza":_self.id,
                        "id_empresa":_self.id_empresa}
                ).then((data) => {
                    this.$store.commit('contabilidadGeneral/poliza/SET_POLIZA', data);
                    this.$emit('success')
                }).finally(() => {
                    //this.getPolizasPorAsociar();
                });
            }
        },

        updateSeleccionado(cfdi) {
            this.$store.commit('contabilidadGeneral/poliza/SET_POSIBLE_CFDI', cfdi);

            let new_value = false;


            if(cfdi.seleccionado === true){
                new_value = false;
            } else {
                new_value = true;
            }
            this.$store.commit('contabilidadGeneral/poliza/UPDATE_ATTRIBUTE_POSIBLE_CFDI', {attribute: 'seleccionado', value: new_value});
            //this.$store.commit('contabilidadGeneral/poliza/UPDATE_POSIBLE_CFDI', {seleccionado:new_value});




            //posibles_cfdi
            //this.$store.commit('contabilidadGeneral/poliza/UPDATE_ATTRIBUTE', {attribute: $(e.target).attr('name'), value: new_value});
        },
    },
    watch: {
        checkbox_toggle(value){
            if(value == 1){
                this.cfdis.forEach(function(element) {
                    element.seleccionado = 1;
                });
            } else {
                this.cfdis.forEach(function(element) {
                    element.seleccionado = 0;
                });
            }
        },
    },
    computed: {
        poliza(){
            return this.$store.getters['contabilidadGeneral/poliza/currentPoliza'];
        },
    },
}
</script>
<style scoped>

table.table-fs-sm{
    font-size: 10px;
}

</style>
