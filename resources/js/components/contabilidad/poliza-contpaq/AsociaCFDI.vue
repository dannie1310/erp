<template>
    <span>
        <div class="row" >
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <poliza-partial-show v-bind:id="this.id" v-bind:id_empresa="this.idEmpresaContabilidad"></poliza-partial-show>
                        <br>
                        <div class="row" v-if="!cargando">
                            <hr>
                            <div class="col-md-12 table-responsive" v-if="poliza && poliza.posibles_cfdi">
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
                                    <tbody v-if="poliza.posibles_cfdi.data.length > 0">
                                        <tr v-for="(cfdi, i) in poliza.posibles_cfdi.data" :style="cfdi.grado_coincidencia ==3?`background-color : lightgreen`:cfdi.grado_coincidencia ==2?`background-color : lightyellow`:``">
                                            <td>{{parseInt(i)+1}}</td>
                                            <td>{{cfdi.tipo_comprobante}}</td>
                                            <td>{{cfdi.fecha_cfdi}}</td>
                                            <td>{{cfdi.razon_social}}</td>
                                            <td>
                                                <CFDI v-if="cfdi" v-bind:txt="cfdi.uuid" v-bind:id="cfdi.id" @click="cfdi.id" ></CFDI>
                                            <td>{{cfdi.serie}}</td>
                                            <td>{{cfdi.folio}}</td>
                                            <td>{{cfdi.conceptos_txt}}</td>
                                            <td style="text-align: right">{{parseFloat(cfdi.importe_iva).formatMoney(2)}}</td>
                                            <td style="text-align: right">{{parseFloat(cfdi.total).formatMoney(2)}}</td>
                                            <td style="text-align: center">
                                                <!--<div class="custom-control custom-checkbox mb-3">
                                                    <input type="checkbox" class="custom-control-input" :id="cfdi.id" :name="cfdi.id" :value="cfdi.seleccionado" @change="updateSeleccionado(cfdi)">
                                                    <label class="custom-control-label" :for="cfdi.id"></label>
                                                </div>-->
                                                <div class="form-check form-check-inline">
                                                    <label class="form-check-label" style="cursor:pointer" >
                                                        <input class="form-check-input" type="checkbox" name="enviar" :value="cfdi.seleccionado" @change="updateSeleccionado(cfdi)">
                                                    </label>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tbody v-else>
                                    <tr style="background-color: lightgrey">
                                        <td colspan="11" style="text-align: center; font-size: 12px; font-style: italic">

                                            <strong>Sin CFDI Detectados</strong>

                                        </td>
                                    </tr>

                                    </tbody>
                                </table>

                            </div>

                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="pull-right">
                            <button type="button" class="btn btn-secondary " v-on:click="regresar"><i class="fa fa-angle-left"></i>Regresar</button>
                            <button @click="asociar"  class="btn btn-danger">
                                <i class="fa fa-share-alt"></i> Asociar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
import CFDI from "../../fiscal/cfd/cfd-sat/CFDI";
import PolizaPartialShow from "../../contabilidad-general/poliza/partials/PartialShow";
import PDFPoliza from "../../contabilidad-general/poliza/partials/PDFPoliza";
import ListaCfdiAsociar from "../../contabilidad-general/poliza/ListaCFDI.vue";

export default {
    name: "poliza-asocia-cfdi",
    props : ['id'],
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
            this.$router.push({name: 'poliza-contpaq-en-sao', params: {id_empresa: this.idEmpresaContabilidad}});
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
                        "id_empresa":_self.idEmpresaContabilidad}
                ).then((data) => {
                    this.$store.commit('contabilidadGeneral/poliza/SET_POLIZA', data);
                    this.$emit('success');
                    this.$router.push({name: 'poliza-contpaq-en-sao-show', params: {id: this.id, id_empresa: this.idEmpresaContabilidad}});
                }).finally(() => {
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
        idEmpresaContabilidad() {
            return this.$store.getters['auth/idEmpresaContabilidad']
        }
    },
}
</script>
<style scoped>

table.table-fs-sm{
    font-size: 10px;
}

</style>
