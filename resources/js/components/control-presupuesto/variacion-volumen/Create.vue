<template>
    <span>
        <form role="form" @submit.prevent="validate">

            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <label>
                                Seleccione el concepto al que le desea aplicar un cambio de volumen:
                            </label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <model-list-select
                                :disabled="cargando"
                                name="id_subcontrato"
                                v-model="id_tarjeta"
                                option-value="id"
                                :custom-text="claveDescripcion"
                                :list="tarjetas"
                                :placeholder="!cargando?'Seleccionar o buscar por clave o descripción':'Cargando...'"
                                :isError="errors.has(`id_tarjeta`)">
                            </model-list-select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row" v-if="concepto">
                <div class="col-12">
                    <div class="invoice p-3 mb-3">
                        <div class="col-12">
                            <h4>
                                <i class="fa fa-list"></i> Editar
                            </h4>
                        </div>
                        <div class="modal-body">
                            <div class="col-12 table-responsive-xl">
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th class="text-center" >Descripción</th>
                                        <th class="text-center cantidad_input">Unidad</th>
                                        <th class="text-center cantidad_input">Volumen</th>
                                        <th class="text-center cantidad_input">Importe</th>
                                        <th class="text-center cantidad_input">Volumen del Cambio</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{ concepto.descripcion}}</td>
                                            <td class="text-center">{{ concepto.unidad}}</td>
                                            <td class="text-center">{{ concepto.cantidad_presupuestada_format}}</td>
                                            <td class="text-right">{{ concepto.monto_presupuestado_format}}</td>
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
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="text-right"><b>Subtotal:</b></td>
                                            <td class="text-right"><b>{{concepto.monto_presupuestado_format}}</b></td>

                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row error-content">
                                        <label for="motivo" class="col-sm-2 col-form-label">Motivo: </label>
                                        <textarea
                                            name="motivo"
                                            id="motivo"
                                            class="form-control"
                                            data-vv-as="Motivo"
                                            v-model="concepto.motivo"
                                            :class="{'is-invalid': errors.has('motivo')}"
                                        ></textarea>
                                        <div class="invalid-feedback" v-show="errors.has('motivo')">{{ errors.first('motivo') }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group row error-content">
                                        <label for="area_solicitante" class="col-form-label">Área Solicitante: </label>
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
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary float-right" type="submit" @click="validate"
                                                :disabled="errors.count() > 0 || !concepto">
                                <i class="fa fa-save"></i>
                                Guardar
                            </button>
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
        claveDescripcion(item){
            return `[${item.clave_concepto}] - [${item.descripcion}]`
        },
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
            //this.concepto = this.tarjetas.find(x=>x.id === id);

            this.$store.dispatch('control-presupuesto/tarjeta/find', {
                id: id,
                params: {

                }
            })
                .then(data => {
                    this.concepto = data;
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
