<template>
    <span>

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
                                <th class="text-center c150" >Clave</th>
                                <th class="text-center" >Descripción</th>
                                <th class="text-center cantidad_input">Unidad</th>
                                <th class="text-center cantidad_input">Volumen</th>
                                <th class="text-center cantidad_input">Importe</th>
                                <th class="text-center cantidad_input">Volumen del Cambio</th>
                                <th class="text-center cantidad_input">Importe del Cambio</th>
                                <th class="index_corto"></th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(concepto_seleccionado, i) in conceptos_seleccionados">
                                    <td>{{ concepto_seleccionado.clave_concepto}}</td>
                                    <td>{{ concepto_seleccionado.descripcion}}</td>
                                    <td class="text-center">{{ concepto_seleccionado.unidad}}</td>
                                    <td class="text-center">{{ concepto_seleccionado.cantidad_presupuestada_format}}</td>
                                    <td class="text-right">{{ concepto_seleccionado.monto_presupuestado_format}}</td>
                                    <td>
                                        <input type="text"
                                               class="form-control"
                                               name="variacion_volumen"
                                               data-vv-as="Volumen del Cambio"
                                               v-on:keyup="calcular"
                                               v-model="concepto_seleccionado.variacion_volumen"
                                               v-validate="{required:true, min_value:concepto_seleccionado.cantidad_presupuestada *-1, decimal:4}"
                                               :class="{'is-invalid': errors.has('variacion_volumen')}"
                                               style="text-align: right"
                                               id="variacion_volumen">
                                        <div class="invalid-feedback" v-show="errors.has(`variacion_volumen`)">{{ errors.first(`variacion_volumen`) }}</div>
                                    </td>
                                    <td style="text-align: right">${{parseFloat(concepto_seleccionado.importe_cambio).formatMoney(2,".",",")}}</td>
                                    <td >
                                        <button @click="eliminarPartida(i)" type="button" class="btn btn-sm btn-outline-danger pull-left" :disabled="!conceptos_seleccionados.length > 0" title="Eliminar">
                                            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
                                            <i class="fa fa-trash" v-else></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-right"><b>Subtotal:</b></td>
                                    <td class="text-right"><b>${{ suma_importe.formatMoney(2,'.',',') }}</b></td>
                                    <td></td>
                                    <td class="text-right"><b>${{ suma_importe_cambio.formatMoney(2,'.',',')  }}</b></td>
                                    <td ></td>
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
                <button class="btn btn-primary float-right" type="button" @click="validate"  :disabled="errors.count() > 0 || !conceptos_seleccionados.length>0">
                    <i class="fa fa-save"></i>
                    Guardar
                </button>
            </div>
        </div>

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
            suma_importe : 0,
            suma_importe_cambio : 0,
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
                    this.suma_importe += parseFloat(data.monto_presupuestado);
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

            var datos_solicitud_cambio = {
                'motivo' : this.motivo,
                'area_solicitante' : this.area_solicitante,
                'conceptos_cambio' : this.conceptos_seleccionados
            }

            return this.$store.dispatch('control-presupuesto/variacion-volumen/store', datos_solicitud_cambio)
            .then(data => {
                this.$router.push({name: 'variacion-volumen'});
            })
            .finally(() => {
                this.cargando = false;
            })
        },
        calcular() {

            let _self = this;
            _self.suma_importe_cambio = 0;
            this.conceptos_seleccionados.forEach(function (concepto_seleccionado, i) {
                _self.suma_importe_cambio += concepto_seleccionado.variacion_volumen * concepto_seleccionado.precio_unitario;
                concepto_seleccionado.importe_cambio = concepto_seleccionado.variacion_volumen * concepto_seleccionado.precio_unitario;
            });
        },
        eliminarPartida(index){
            this.conceptos_seleccionados.splice(index, 1);
        },
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
}
</script>

<style>

</style>
