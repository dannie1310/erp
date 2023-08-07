<template>
    <span>
        <br>
        <div class="row" v-if="!cargando">
            <hr>
            <div class="col-md-12 table-responsive" v-if="poliza && poliza.posibles_cfdi">
                <strong>
                    Lista de posibles CFDI
                </strong>
                <table class="table table-sm table-fs-sm table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th style="width: 15px">T</th>
                            <th style="width: 65px">Fecha</th>
                            <th style="width: 250px">Razón Social</th>
                            <th style="width: 200px">UUID</th>
                            <th style="width: 20px">Serie</th>
                            <th style="width: 80px">Folio</th>
                            <th>Conceptos</th>
                            <th style="width: 50px">IVA</th>
                            <th style="width: 90px">Monto</th>
                            <th style="width: 15px"></th>
                        </tr>
                    </thead>
                    <tbody v-if="poliza.posibles_cfdi.data.length > 0">
                        <tr v-for="(cfdi, i) in poliza.posibles_cfdi.data" :style="cfdi.grado_coincidencia ==3?`background-color : lightgreen`:cfdi.grado_coincidencia ==2?`background-color : lightyellow`:``">
                           <tr v-for="(cfdi, i) in poliza.posibles_cfdi.data" :style="cfdi.grado_coincidencia ==3?`background-color : lightgreen`:cfdi.grado_coincidencia ==2?`background-color : lightyellow`:``">
                            <td>{{parseInt(i)+1}}</td>
                            <td>{{cfdi.tipo_comprobante}}</td>
                            <td>{{cfdi.fecha_cfdi}}</td>
                            <td>
                                <div class="row">
                                    <div class="col-md-1">
                                        <i class="fa fa-check-circle" style="color: green" v-if="cfdi.coincide_proveedor == true"></i>
                                        <i class="fa fa-times-circle" style="color: red" v-else></i>
                                    </div>
                                    <div class="col-md-11">
                                        {{cfdi.razon_social}}
                                    </div>
                                </div>
                            </td>
                            <td>
                                <CFDI v-if="cfdi" v-bind:txt="cfdi.uuid" v-bind:id="cfdi.id" @click="cfdi.id" ></CFDI>
                            <td>{{cfdi.serie}}</td>
                            <td>
                                <i class="fa fa-check-circle" style="color: green" v-if="cfdi.coincide_folio == true"></i>
                                <i class="fa fa-times-circle" style="color: red" v-else></i>
                                {{cfdi.folio}}</td>
                            <td>{{cfdi.conceptos_txt}}</td>
                            <td style="text-align: right">{{parseFloat(cfdi.importe_iva).formatMoney(2)}}</td>
                            <td style="text-align: right;">
                                <div class="row">
                                    <div class="col-md-1">
                                        <i class="fa fa-check-circle" style="color: green" v-if="cfdi.coincide_importe == true"></i>
                                        <i class="fa fa-times-circle" style="color: red" v-else></i>
                                    </div>
                                    <div class="col-md-10">
                                        {{parseFloat(cfdi.total).formatMoney(2)}}
                                    </div>
                                </div>
                            </td>
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
    </span>
</template>

<script>


import CFDI from "../../../../fiscal/cfd/cfd-sat/CFDI.vue";

export default {
    name: "poliza-contpaq-lista-posibles-cfdi",
    props : ['id'],
    components: {CFDI},
    data() {
        return {
            cargando :false,
            cfdi_store : [],

        }
    },
    mounted() {
    },
    methods :{
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
