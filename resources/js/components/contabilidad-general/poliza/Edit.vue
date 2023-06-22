<template>
    <span>
        <div class="card" v-if="cargando">
            <div class="card-body">
                <div class="row" >
                    <div class="col-md-12">
                        <div class="spinner-border text-success" role="status">
                           <span class="sr-only">Cargando...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" v-else>
            <div class="col-12">
                <form role="form" @submit.prevent="validate" v-if="!cargando && poliza" class="detalle_poliza">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-2">
                                    <div >
                                        <div class="form-group error-content">
                                            <label for="fecha" class="col-form-label">Fecha:</label>
                                            <datepicker v-model = "poliza.fecha_completa.date"
                                                        name = "fecha"
                                                        :format = "formatoFecha"
                                                        :language = "es"
                                                        :bootstrap-styling = "true"
                                                        class = "form-control"
                                                        v-validate="{required: true}"
                                                        :disabled-dates="fechasDeshabilitadas"
                                                        :class="{'is-invalid': errors.has('fecha')}"
                                            ></datepicker>
                                            <div class="invalid-feedback" v-show="errors.has('fecha')">{{ errors.first('fecha') }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group row error-content">
                                        <label for="numero_poliza" class="col-form-label">Folio de Poliza:</label>
                                        <input
                                            type="text"
                                            name="texto"
                                            class="form-control"
                                            id="numero_poliza"
                                            v-model="poliza.folio"
                                            v-validate="{required: true}"
                                            :class="{'is-invalid': errors.has('numero_poliza')}">
                                        <div class="invalid-feedback" v-show="errors.has('numero_poliza')">{{ errors.first('numero_poliza') }}</div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group error-content">
                                        <label for="tipo_poliza" class="col-form-label">Tipo de Poliza:</label>
                                        <select
                                            class="form-control"
                                            name="tipo_poliza"
                                            v-model="poliza.tipo.id"
                                            v-validate="{required: true}"
                                            data-vv-as="Tipo Poliza"
                                            :class="{'is-invalid': errors.has('tipo_poliza')}">
                                            <option value>-- Tipo -- </option>
                                            <option v-for="tipo in tipos" :value="tipo.id">{{ tipo.nombre }}</option>
                                        </select>
                                        <div class="invalid-feedback" v-show="errors.has('tipo_poliza')">
                                            {{ errors.first('tipo_poliza') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2" v-if="poliza.asociacion_cfdi">
                                    <label for="uuid_poliza" class="col-form-label">UUID:</label>
                                    <span v-if="poliza.asociacion_cfdi.data.length == 1">
                                        <div class="form-group error-content">

                                            <input
                                                type="text"
                                                name="texto"
                                                class="form-control"
                                                id="uuid_poliza"
                                                v-model="poliza.asociacion_cfdi.data[0].uuid"
                                                v-validate="{required: true}"
                                                :class="{'is-invalid': errors.has('uuid_poliza')}">
                                            <div class="invalid-feedback" v-show="errors.has('uuid_poliza')">
                                                {{ errors.first('uuid_poliza') }}
                                            </div>
                                        </div>
                                    </span>
                                    <span v-if="poliza.asociacion_cfdi.data.length > 1">
                                        <select multiple size="5" class="form-control">
                                            <option v-for="(uuid, i) in poliza.asociacion_cfdi.data">{{uuid.uuid}}</option>
                                        </select>

                                    </span>

                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row error-content">
                                        <label for="concepto" class="col-md-12 col-form-label">Concepto:</label>
                                        <textarea
                                            type="text"
                                            v-validate="{required: true, max:100}"
                                            name="concepto"
                                            class="form-control"
                                            id="concepto"
                                            v-model="poliza.concepto"
                                            placeholder="CONCEPTO DE PÓLIZA"
                                            :class="{'is-invalid': errors.has('concepto')}"
                                            v-on:keyup ="repiteConceptos()"
                                        ></textarea>
                                        <div class="invalid-feedback" v-show="errors.has('concepto')">{{ errors.first('concepto') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                             <div class="row">
                                 <div class="col-md-12">
                                     <div class="custom-control custom-checkbox">
                                         <input type="checkbox" class="custom-control-input" id="repetir_concepto" v-on:change="repiteConceptos()" v-model="repite_concepto" >
                                         <label for="repetir_concepto" class="custom-control-label" >Replicar concepto de póliza en concepto de movimientos</label>
                                     </div>
                                 </div>
                             </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <label ><i class="fa fa-th-list icon"></i>Movimientos</label>
                        </div>
                        <div class="card-body table-responsive">
                            <table class="table table-striped">
                            <thead>
                            <tr>
                                <th class="bg-gray-light index_corto">#</th>
                                <th class="bg-gray-light">Cuenta</th>
                                <th class="bg-gray-light">Tipo Cuenta</th>
                                <th class="bg-gray-light">Cargo</th>
                                <th class="bg-gray-light">Abono</th>
                                <th class="bg-gray-light">Referencia</th>
                                <th class="bg-gray-light">Concepto</th>
                                <th class="bg-gray-light">UUID</th>
                                <th class="bg-gray-light icono">
                                    <button type="button" class="btn btn-sm btn-outline-success" @click="agregar" :disabled="cargando">
                                        <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
                                        <i class="fa fa-plus" v-else></i>
                                    </button>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(movimiento, i) in poliza.movimientos_poliza.data" :class="{'bg-success': ! movimiento.id}">
                                <td class="index_corto">{{ i + 1 }}</td>
                                <td>
                                    <model-list-select
                                        :name="`id_cuenta[${i}]`"
                                        placeholder="Seleccionar o buscar por código o nombre de la cuenta"
                                        data-vv-as="Cuenta"
                                        v-model="movimiento.cuenta.id"
                                        option-value="id"
                                        v-validate="{required: true}"
                                        :custom-text="cuentaDescripcion"
                                        :list="cuentas"
                                        :class="{'is-invalid': errors.has(`id_cuenta[${i}]`)}"
                                    >
                                    </model-list-select>
                                    <div class="invalid-feedback"
                                         v-show="errors.has(`id_cuenta[${i}]`)">{{ errors.first(`id_cuenta[${i}]`) }}
                                    </div>
                                </td>
                                <td>
                                    <select
                                        class="form-control"
                                        :name="`id_tipo_movimiento_poliza[${i}]`"
                                        v-model="movimiento.tipo"
                                        v-validate="{required: true}"
                                        data-vv-as="Tipo"
                                        :class="{'is-invalid': errors.has(`id_tipo_movimiento_poliza[${i}]`)}"
                                        >
                                        <option value="0">Cargo</option>
                                        <option value="1">Abono</option>
                                    </select>
                                    <div class="invalid-feedback"
                                         v-show="errors.has(`id_tipo_movimiento_poliza[${i}]`)">{{ errors.first(`id_tipo_movimiento_poliza[${i}]`) }}
                                    </div>
                                </td>
                                <td class="money_input" v-if="movimiento.tipo == 0">
                                    <input
                                        type="number"
                                        step="any"
                                        class="form-control"
                                        :name="`importe[${i}]`"
                                        v-model="movimiento.importe"
                                        v-validate="{required: true, decimal: true, min_value: 0.1}"
                                        data-vv-as="Debe"
                                        :class="{'is-invalid': errors.has(`importe[${i}]`)}"
                                    />
                                    <div class="invalid-feedback" v-show="errors.has(`importe[${i}]`)">{{ errors.first(`importe[${i}]`) }}</div>
                                </td>
                                <td v-else></td>
                                <td class="money_input" v-if="movimiento.tipo == 1">
                                    <input
                                        type="number"
                                        step="any"
                                        class="form-control"
                                        :name="`importe[${i}]`"
                                        v-model="movimiento.importe"
                                        v-validate="{required: true, decimal: true}"
                                        data-vv-as="Debe"
                                        :class="{'is-invalid': errors.has(`importe[${i}]`)}"
                                        />
                                    <div class="invalid-feedback" v-show="errors.has(`importe[${i}]`)">{{ errors.first(`importe[${i}]`) }}</div>
                                </td>
                                <td v-else></td>
                                <td>
                                    <div class="form-group row error-content">
                                        <input
                                            type="text"
                                            v-validate="{required: true, max:30}"
                                            :name="`referencia[${i}]`"
                                            class="form-control"
                                            v-model="movimiento.referencia"
                                            placeholder="REFERENCIA DE MOVIMIENTO"
                                            :class="{'is-invalid': errors.has(`referencia[${i}]`)}"
                                        />
                                        <div class="invalid-feedback" v-show="errors.has(`referencia[${i}]`)">{{ errors.first(`referencia[${i}]`) }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group row error-content">
                                        <textarea
                                            type="text"
                                            v-validate="{required: true, max:100}"
                                            :name="`concepto_movto_edit[${i}]`"
                                            class="form-control"
                                            v-model="movimiento.concepto"
                                            placeholder="CONCEPTO DE MOVIMIENTO"
                                            :class="{'is-invalid': errors.has(`concepto_movto_edit[${i}]`)}"
                                        ></textarea>
                                        <div class="invalid-feedback" v-show="errors.has(`concepto_movto_edit[${i}]`)">{{ errors.first(`concepto_movto_edit[${i}]`) }}</div>
                                    </div>
                                </td>
                                <td class="referencia_input">
                                    <div class="form-group error-content">
                                        <input
                                            v-if="movimiento.asociacion_cfdi"
                                            type="text"
                                            class="form-control"
                                            v-model="movimiento.asociacion_cfdi.uuid"
                                            v-validate="{required: true}"
                                            :class="{'is-invalid': errors.has('uuid_poliza')}">
                                        <div class="invalid-feedback" v-show="errors.has('uuid_poliza')">
                                            {{ errors.first('uuid_poliza') }}
                                        </div>
                                    </div>

                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-danger" @click="remove(movimiento)">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            </tbody>
                            <tfoot>
                            <tr>
                                <th colspan="3" class="text-center" :class="color">
                                    <b>Sumas Iguales</b>
                                </th>
                                <th :class="color">
                                    <b>$&nbsp;{{(parseFloat(sumaDebe)).formatMoney(2,'.',',')}}</b>
                                </th>
                                <th :class="color">
                                    <b>$&nbsp;{{(parseFloat(sumaHaber)).formatMoney(2,'.',',')}}</b>
                                </th>
                                <th :class="color" colspan="3"></th>
                            </tr>
                            </tfoot>
                        </table>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="salir()"> Cerrar</button>
                            <button type="submit" class="btn btn-info" :disabled="errors.count() > 0 || !cuadrado || !cambio">Guardar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </span>
</template>

<script>
    import Datepicker from 'vuejs-datepicker';
    import {es} from 'vuejs-datepicker/dist/locale';
    import {ModelListSelect} from 'vue-search-select';
    export default {
        name: "poliza-edit",
        props: ['id','id_empresa'],
        components: {Datepicker,es, ModelListSelect},
        data(){
            return {
                repite_concepto : false,
                cargando : false,
                original: null,
                poliza : null,
                cuentas: [],
                tipos:[],
                es:es,
                fechasDeshabilitadas:{},
                fecha_hoy : '',
            }
        },
        mounted() {
            this.fecha_hoy = new Date();
            this.fechasDeshabilitadas.from = new Date();
            if(this.id_empresa === undefined) {
                swal("Error iniciar nuevamente el proceso", {
                    icon: "error",
                    buttons: {
                        confirm: {
                            text: 'OK',
                            closeModal: true,
                        }
                    }
                }).then(() => {
                    this.salir()
                })
            }else {
                this.find()
            }
        },
        methods: {
            cuentaDescripcion (item) {
                return `[${item.cuenta}]-[${item.descripcion}]`
            },
            formatoFecha(date){
                return moment(date).format('DD/MM/YYYY');
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.update();
                    }
                });
            },
            find() {
                this.cargando = true
                return this.$store.dispatch('contabilidadGeneral/poliza/find', {
                    id: this.id,
                    id_empresa: this.id_empresa,
                    params: {include: ['movimientos_poliza.asociacion_cfdi', 'tipo', 'asociacion_cfdi'], id_empresa: this.id_empresa}
                }).then(data => {
                    this.$store.commit('contabilidadGeneral/poliza/SET_POLIZA', data);
                }).finally(()=>{
                    this.getCuentas();
                })
            },
            update(){
                this.poliza.id_empresa = this.id_empresa
                this.poliza.abono_nuevo = this.sumaHaber
                this.poliza.cargo_nuevo = this.sumaDebe
                return this.$store.dispatch('contabilidadGeneral/poliza/update', {
                    id: this.id,
                    data: this.poliza,
                })
                    .then(data => {
                         this.salir();
                    })
            },
            repiteConceptos(){
                if(this.repite_concepto === true ){
                    let self = this;
                    this.poliza.movimientos_poliza.data.forEach(function(movimiento, i){
                        movimiento.concepto = self.poliza.concepto;
                    });
                }
            },
            salir(){
                this.$router.push({ name:'poliza-contpaq', params: {}});
            },
            getCuentas() {
                return this.$store.dispatch('contabilidadGeneral/cuenta/index', {
                    params: {scope:'afectableNumerico', id_empresa: this.id_empresa}
                })
                    .then(data => {
                        this.cuentas = data.data;
                    }).finally(()=>{
                        this.getTipos();
                    })
            },
            getTipos() {
                return this.$store.dispatch('contabilidadGeneral/tipo-poliza/index', {
                    params: {id_empresa: this.id_empresa}
                })
                    .then(data => {
                        this.cargando = false
                        this.tipos = data.data;
                    })
            },
            remove(movimiento) {
                swal({
                    title: "Quitar Movimiento",
                    text: "¿Estás seguro de que deseas quitar el movimiento de la Póliza?",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Quitar',
                            closeModal: true,
                        }
                    },
                    dangerMode: true,
                })
                    .then((willDelete) => {
                        if (willDelete) {
                            if(!movimiento.id) {
                                this.original.movimientos_poliza.data = this.poliza.movimientos_poliza.data.filter(function (m) {
                                    return JSON.stringify(movimiento) != JSON.stringify(m)
                                })
                            }
                            this.poliza.movimientos_poliza.data = this.poliza.movimientos_poliza.data.filter(function (m) {
                                return JSON.stringify(movimiento) != JSON.stringify(m)
                            })
                        }
                    });
            },
            agregar() {
                var array = {
                    'cuenta': {
                       'id' : ''
                    },
                    'concepto' : '',
                    'importe' : 0,
                    'referencia' : '',
                    'tipo': 2
                }
                this.poliza.movimientos_poliza.data.push(array);
            },
        },

        computed: {
            currentPoliza(){
                return this.$store.getters['contabilidadGeneral/poliza/currentPoliza'];
            },
            diff() {
                return diff(this.poliza, this.original)
            },
            sumaDebe() {
                let result = 0;
                this.poliza.movimientos_poliza.data.forEach(function (movimiento, i) {
                    if (movimiento.tipo == 0) {
                        result += parseFloat(movimiento.importe);
                    }
                })
                return result
            },
            sumaHaber() {
                let result = 0;
                this.poliza.movimientos_poliza.data.forEach(function (movimiento, i) {
                    if (movimiento.tipo == 1) {
                        result += parseFloat(movimiento.importe);
                    }
                })
                return result
            },
            cuadrado() {
                return Math.abs(this.sumaDebe - this.sumaHaber) <= 0.99;
            },
            color() {
                if (!this.cuadrado) {
                    return 'bg-danger'
                } else {
                    return 'bg-gray'
                }
            },
            cambio() {
                return JSON.stringify(this.poliza) != JSON.stringify(this.original) || this.nuevosMovimientos
            },

            nuevosMovimientos() {
                return !!this.original.movimientos_poliza.data.find(mov => {
                    return !mov.id
                })
            },
        },
        watch:{
            currentPoliza: {
                handler(poliza) {
                    if (poliza) {
                        this.poliza = JSON.parse(JSON.stringify(poliza));
                        this.original = JSON.parse(JSON.stringify(poliza));
                    }
                },
                deep: true
            }
        }
    }
</script>
<style scoped>
    textarea[name="concepto"] {
        resize: none;
    }
</style>
