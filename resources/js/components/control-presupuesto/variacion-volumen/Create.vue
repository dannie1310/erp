<template>
    <span>
        <form role="form" @submit.prevent="validate">
            <div class="row">
                <div class="col-12">
                    <div class="invoice p-3 mb-3">
                        <div class="row">
                            <div class="col-12">
                                <h4>
                                    <i class="fa fa-list"></i> Seleccionar Tarjeta
                                </h4>
                            </div>
                        </div>
                        
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group row error-content">
                                            <label for="id_almacen" class="col-sm-2 col-form-label">tarjetas: </label>
                                            <div class="col-sm-10">
                                                <model-list-select
                                                    :disabled="cargando"
                                                    name="id_tarjeta"
                                                    v-model="id_tarjeta"
                                                    option-value="id"
                                                    option-text="descripcion"
                                                    :list="tarjetas"
                                                    :placeholder="!cargando?'Seleccionar o buscar tarjeta por descripcion':'Cargando...'"
                                                    :isError="errors.has(`id_tarjeta`)">
                                                </model-list-select>
                                                <div class="invalid-feedback" v-show="errors.has('id_tarjeta')">{{ errors.first('id_tarjeta') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        
                    </div>
                </div>
            </div>
            <div class="row" v-if="conceptos_tarjeta.length > 0">
                <div class="col-12">
                    <div class="invoice p-3 mb-3">
                        <div class="col-12">
                            <h4>
                                <i class="fa fa-list"></i> Editar Tarjeta
                            </h4>
                        </div>
                        <div class="modal-body">
                            <div class="col-12 table-responsive-xl">
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th class="text-center" width="60%">Descripción</th>
                                        <th class="text-center">Unidad</th>
                                        <th class="text-center">Volumen</th>
                                        <th class="text-center">Volumen del Cambio</th>
                                        <th class="text-center">Importe</th>
                                        <th class="text-center" width="5%">-</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(concepto, i) in conceptos_tarjeta">
                                            <td class="text-center">{{ i + 1}}</td>
                                            <td>{{ concepto.concepto.descripcion}}</td>
                                            <td class="text-center">{{ concepto.concepto.unidad}}</td>
                                            <td class="text-center">{{ concepto.concepto.cantidad_presupuestada_format}}</td>
                                            <td>
                                                <input type="text"
                                                    class="form-control"
                                                    :name="`variacion_volumen[${i}]`"
                                                    data-vv-as="Volumen del Cambio"
                                                    v-model="concepto.variacion_volumen"
                                                    v-validate="{required: concepto.checkVariacion == true ? true:false, min_value:concepto.concepto.cantidad_presupuestada *-1}"
                                                    :class="{'is-invalid': errors.has(`variacion_volumen[${i}]`)}"
                                                    :id="`variacion_volumen[${i}]`">
                                                <div class="invalid-feedback" v-show="errors.has(`variacion_volumen[${i}]`)">{{ errors.first(`variacion_volumen[${i}]`) }}</div>
                                            </td>
                                            <td class="text-right">{{ concepto.concepto.monto_presupuestado_format}}</td>
                                            <td class="text-center">
                                                <input type="checkbox"
                                                name="checkVariacion"
                                                class="form-check-input"
                                                data-vv-as="Variación"
                                                v-model="concepto.checkVariacion"
                                                id="checkVariacion"
                                                v-on:click=" ! concepto.checkVariacion">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="5" class="text-right"><b>SUBTOTAL</b></td>
                                            <td class="text-right"><b>$ {{ subtotal.formatMoney(2, ',', '.') }}</b></td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group row error-content">
                                    <label for="motivo" class="col-sm-2 col-form-label">Motivo: </label>
                                    <input type="text"
                                        class="form-control"
                                        name="motivo"
                                        data-vv-as="Motivo"
                                        v-model="motivo"
                                        v-validate="{required: true}"
                                        :class="{'is-invalid': errors.has('motivo')}"
                                        id="motivo">
                                    <div class="invalid-feedback" v-show="errors.has('motivo')">{{ errors.first('motivo') }}</div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group row error-content">
                                    <label for="area_solicitante" class="col-sm-2 col-form-label">Area Solicitante: </label>
                                    <input type="text"
                                        class="form-control"
                                        name="area_solicitante"
                                        data-vv-as="Area Solicitante"
                                        v-model="area_solicitante"
                                        v-validate="{required: true}"
                                        :class="{'is-invalid': errors.has('area_solicitante')}"
                                        id="area_solicitante">
                                    <div class="invalid-feedback" v-show="errors.has('area_solicitante')">{{ errors.first('area_solicitante') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary">Cerrar</button>
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </span>
</template>

<script>
import {ModelListSelect} from 'vue-search-select';
export default {
    name: "variacion-volumen-create",
    components: {ModelListSelect},
    props: [],
    data() {
        return {
            cargando: false,
            id_tarjeta: '',
            motivo:'',
            area_solicitante:'',
            conceptos_tarjeta:[],
        }
    },
    methods: {
        getTarjetas(){
            this.cargando = true;
            return this.$store.dispatch('control-presupuesto/tarjeta/index', {
                params: {}
            })
            .then(data => {
                this.$store.commit('control-presupuesto/tarjeta/SET_TARJETAS', data);
            })
            .finally(() => {
                this.cargando = false;
            })
        },
        getConceptosTarjeta(id){
            this.cargando = true;
            return this.$store.dispatch('control-presupuesto/concepto-tarjeta/find', {
                id: id
            })
            .then(data => {
                this.conceptos_tarjeta =  data.data;
            })
            .finally(() => {
                this.cargando = false;
            })
        },
        validate() {
            this.$validator.validate().then(result => {
                if (result) {
                    this.store()
                }
            });
        },
        store() {
            
        }
    },
    computed: {
        tarjetas(){
            return this.$store.getters['control-presupuesto/tarjeta/tarjetas'];
        },
        subtotal : function () {
            var res = 0;

            this.conceptos_tarjeta.forEach(function (partida) {
                res += parseFloat(partida.concepto.monto_presupuestado);
            });
            return res;
        }
    },
    mounted() {
        this.getTarjetas();
    },
    watch:{
        id_tarjeta(value){
            if(value != ''){
                this.getConceptosTarjeta(value);
            }
        }
    }
}
</script>

<style>

</style>