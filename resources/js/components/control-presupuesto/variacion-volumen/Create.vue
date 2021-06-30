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
                                        <label for="id_almacen" class="col-sm-2 col-form-label">Conceptos: </label>
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
            <div class="row" v-if="concepto">
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
                                        <th class="text-center" width="60%">Descripción</th>
                                        <th class="text-center">Unidad</th>
                                        <th class="text-center">Volumen</th>
                                        <th class="text-center">Volumen del Cambio</th>
                                        <th class="text-center">Importe</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{ concepto.descripcion}}</td>
                                            <td class="text-center">{{ concepto.unidad}}</td>
                                            <td class="text-center">{{ concepto.cantidad_presupuestada_format}}</td>
                                            <td>
                                                <input type="text"
                                                    class="form-control"
                                                    name="variacion_volumen"
                                                    data-vv-as="Volumen del Cambio"
                                                    v-model="concepto.variacion_volumen"
                                                    v-validate="{required:true, min_value:concepto.cantidad_presupuestada *-1, decimal:4}"
                                                    :class="{'is-invalid': errors.has('variacion_volumen')}"
                                                    id="variacion_volumen">
                                                <div class="invalid-feedback" v-show="errors.has(`variacion_volumen`)">{{ errors.first(`variacion_volumen`) }}</div>
                                            </td>
                                            <td class="text-right">{{ concepto.monto_presupuestado_format}}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" class="text-right"><b>SUBTOTAL</b></td>
                                            <td class="text-right"><b>{{concepto.monto_presupuestado_format}}</b></td>
                                            
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
                                        v-model="concepto.motivo"
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
                                        v-model="concepto.area_solicitante"
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
            tarjetas:[],
            concepto:null,
        }
    },
    methods: {
        getTarjetas(){
            this.cargando = true;
            return this.$store.dispatch('control-presupuesto/tarjeta/index', {
                params: {}
            })
            .then(data => {
                this.tarjetas =  data;
            })
            .finally(() => {
                this.cargando = false;
            })
        },
        getConcepto(id){
            this.concepto = [];
            this.concepto = this.tarjetas[id];
        },
        validate() {
            this.$validator.validate().then(result => {
                if (result) {
                    this.store()
                }
            });
        },
        store() {
            this.cargando = true;
            return this.$store.dispatch('control-presupuesto/variacion-volumen/store', this.concepto)
            .then(data => {
                this.$router.push({name: 'variacion-volumen'});
            })
            .finally(() => {
                this.cargando = false;
            })
        }
    },
    computed: {
        total : function () {
            var res = 0;

            if(this.concepto.variacion_volumen){
                res = (parseFloat(this.concepto.variacion_volumen) * parseFloat(this.concepto.precio_unitario));
            }
            return res;
        }
    },
    mounted() {
        this.getTarjetas();
    },
    watch:{
        id_tarjeta(value){
            if(value != ''){
                this.getConcepto(value);
            }
        }
    }
}
</script>

<style>

</style>