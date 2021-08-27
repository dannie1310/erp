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
                    <br>
                     <div class="row">
                        <div class="col-md-12">

                            <button class="btn btn-primary float-right" type="button" @click="agregar"
                                    :disabled="!id_tarjeta > 0">
                                <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
                                <i class="fa fa-plus" v-else></i>
                                Agregar
                            </button>

                        </div>
                    </div>

                    <br>

                    <div class="row" v-if="conceptos_seleccionados.length > 0">
                        <div class="col-sm-12">
                            <table class="table table-striped table-sm">
                                <thead>
                                <tr>
                                    <th class="text-center" >Descripción</th>
                                    <th class="text-center cantidad_input">Unidad</th>
                                    <th class="text-center cantidad_input">Volumen</th>
                                    <th class="text-center cantidad_input">Importe</th>
                                    <th class="text-center cantidad_input">Volumen del Cambio</th>
                                    <th class="text-center cantidad_input">Importe del Cambio</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="concepto_seleccionado in conceptos_seleccionados">
                                        <td>{{ concepto_seleccionado.descripcion}}</td>
                                        <td class="text-center">{{ concepto_seleccionado.unidad}}</td>
                                        <td class="text-center">{{ concepto_seleccionado.cantidad_presupuestada_format}}</td>
                                        <td class="text-right">{{ concepto_seleccionado.monto_presupuestado_format}}</td>
                                        <td>
                                            <input type="text"
                                                   class="form-control"
                                                   name="variacion_volumen"
                                                   data-vv-as="Volumen del Cambio"
                                                   v-model="concepto_seleccionado.variacion_volumen"
                                                   v-validate="{required:true, min_value:concepto_seleccionado.cantidad_presupuestada *-1, decimal:4}"
                                                   :class="{'is-invalid': errors.has('variacion_volumen')}"
                                                   id="variacion_volumen">
                                            <div class="invalid-feedback" v-show="errors.has(`variacion_volumen`)">{{ errors.first(`variacion_volumen`) }}</div>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="text-right"><b>Subtotal:</b></td>
                                        <td class="text-right"><b>{{}}</b></td>
                                        <td></td>
                                        <td class="text-right"><b>{{}}</b></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
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
                                    v-model="motivo"
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
                                       v-model="area_solicitante"
                                       v-validate="{required: true}"
                                       :class="{'is-invalid': errors.has('area_solicitante')}"
                                       id="area_solicitante">
                                <div class="invalid-feedback" v-show="errors.has('area_solicitante')">{{ errors.first('area_solicitante') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn btn-primary float-right" type="submit" @click="validate"  :disabled="errors.count() > 0 || !concepto">
                        <i class="fa fa-save"></i>
                        Guardar
                    </button>
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
            conceptos_seleccionados : [],
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
        agregar(){
            var busqueda;
            this.cargando = true;

            busqueda = this.conceptos_seleccionados.find(x=>x.id === this.id_tarjeta);

            if(busqueda == undefined){
                this.$store.dispatch('control-presupuesto/tarjeta/find', {
                    id: this.id_tarjeta,
                    params: {

                    }
                })
                .then(data => {
                    this.conceptos_seleccionados.push(data);
                    this.concepto = data;
                })
                .finally(() => {
                    this.id_tarjeta='';
                    this.cargando = false;
                })
            } else {
                swal('Atención', 'Este concepto ya ha sido seleccionado previamente', 'warning')
                this.cargando = false;
            }
        },
        getConcepto(id){
            //this.concepto = this.tarjetas.find(x=>x.id === id);

            this.$store.dispatch('control-presupuesto/tarjeta/find', {
                id: id,
                params: {

                }
            })
                .then(data => {
                    this.conceptos_seleccionados.push(data);
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
    /*watch:{
        id_tarjeta(value){
            if(value != ''){
                this.getConcepto(value);
            }
        }
    }*/
}
</script>

<style>

</style>
