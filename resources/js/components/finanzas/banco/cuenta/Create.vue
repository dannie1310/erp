<template>
   <span>
        <button @click="show" v-if="$root.can('registrar_cuenta_corriente')" class="btn btn-app btn-info pull-right">
            <i class="fa fa-plus"></i> Registrar Cuenta
        </button>
       <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
           <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
               <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalCuenta">Alta de Cuenta Bancaria</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="cleanData">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                   <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="form-group error-content">
                                            <label for="numero">Número:</label>
                                            <input type="number" class="form-control"
                                                   name="numero"
                                                   data-vv-as="Número"
                                                   v-model="numero"
                                                   v-validate="{required: true, min:9, max:18}"
                                                   :class="{'is-invalid': errors.has('numero')}"
                                                   id="numero"
                                                   placeholder="Número de Cuenta">
                                            <div class="invalid-feedback" v-show="errors.has('numero')">{{ errors.first('numero') }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="id_moneda">Moneda:</label>
                                            <select
                                                type="text"
                                                name="id_moneda"
                                                data-vv-as="Moneda"
                                                v-validate="{required: true}"
                                                class="form-control"
                                                id="id_moneda"
                                                v-model="id_moneda"
                                                :class="{'is-invalid': errors.has('id_moneda')}">
                                                <option value>-- SELECCIONE --</option>
                                                <option v-for="moneda in monedas" :value="moneda.id">{{moneda.nombre}} ({{moneda.abreviatura}})</option>
                                            </select>
                                        <div class="invalid-feedback" v-show="errors.has('id_moneda')">{{ errors.first('id_moneda') }}</div>
                                    </div>
                                </div>
                                <div class="col-md-12 mt-1 text-left" >
                                      <label class="text-secondary">Apertura </label>
                                       <hr style="color: #0056b2; margin-top:auto;" width="90%" size="10" />
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group error-content">
                                        <label><b>Fecha:</b></label>
                                            <datepicker v-model = "fecha"
                                                        name = "fecha"
                                                        :language = "es"
                                                        :format = "formatoFecha"
                                                        :bootstrap-styling = "true"
                                                        class = "form-control"
                                                        v-validate="{required: true}"
                                                        :class="{'is-invalid': errors.has('fecha')}"
                                            ></datepicker>
                                         <div class="invalid-feedback" v-show="errors.has('fecha')">{{ errors.first('fecha') }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group error-content">
                                        <label for="saldo_inicial">Saldo Inicial:</label>
                                        <input type="number" class="form-control"
                                               name="saldo_inicial"
                                               data-vv-as="Saldo Inicial"
                                               v-model="saldo_inicial"
                                               v-validate="{required: true, min_value:0, max_value: 999999999999999, decimal:4}"
                                               :class="{'is-invalid': errors.has('saldo_inicial')}"
                                               id="saldo_inicial">
                                        <div class="invalid-feedback" v-show="errors.has('saldo_inicial')">{{ errors.first('saldo_inicial') }}</div>
                                    </div>
                                </div>
                                <div class="col-md-12 mt-1 text-center" >
                                    <hr style="color: #0056b2; margin-top:auto;" width="90%" size="10" />
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group error-content">
                                        <label for="abreviatura">Abreviatura:</label>
                                        <input type="text" class="form-control"
                                               name="abreviatura"
                                               data-vv-as="Abreviatura"
                                               v-model="abreviatura"
                                               v-validate="{required: true}"
                                               :class="{'is-invalid': errors.has('abreviatura')}"
                                               id="abreviatura">
                                        <div class="invalid-feedback" v-show="errors.has('abreviatura')">{{ errors.first('abreviatura') }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <br>
                                        <input type="checkbox" class="form-check-imput" id="chequera" v-model="chk_chequera">
                                        <label class="form-check-label" for="chequera"><b>   Manejo de Chequera </b></label>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <div class="form-group error-content">
                                            <label for="abreviatura">Tipo de Cuenta:</label>
                                            <button type="button" @click="tipoCuenta(1)"
                                                    :class="{'btn btn-secondary': id_tipo_cuentas_obra != 1,'btn btn-primary': id_tipo_cuentas_obra == 1}">Pagadora</button>
                                            <button type="button" @click="tipoCuenta(2)"
                                                    :class="{'btn btn-secondary': id_tipo_cuentas_obra != 2,'btn btn-primary': id_tipo_cuentas_obra == 2}">Concentradora</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                       <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="cleanData">Cerrar</button>
                                <button type="submit" class="btn btn-primary" :disabled="errors.count() > 0 ">Guardar</button>
                         </div>
                   </form>
               </div>
           </div>
       </div>
   </span>
</template>


<script>
    import Datepicker from 'vuejs-datepicker';
    import {es} from 'vuejs-datepicker/dist/locale'
    export default {
        name: "cuenta-create",
        props:['id'],
        components: {Datepicker},
        data() {
            return {
                es: es,
                numero:'',
                id_moneda:'',
                fecha:'',
                fecha_inicial:'',
                saldo_inicial:0,
                chequera:0,
                chk_chequera:false,
                abreviatura:'',
                id_tipo_cuentas_obra:1,
                monedas:[]
            }
        },
        mounted(){
            this.getMonedas();
        },
        methods: {
            cleanData(){
                this.numero='';
                this.id_moneda='';
                this.fecha='';
                this.fecha_inicial='';
                this.saldo_inicial=0;
                this.chequera=0;
                this.chk_chequera=false;
                this.abreviatura='';
                this.id_tipo_cuentas_obra=1;
            },
            formatoFecha(date){
                return moment(date).format('YYYY-MM-DD');
            },
            getMonedas(){
                this.cargando = true;
                this.$store.commit('cadeco/moneda/SET_MONEDAS', null);
                return this.$store.dispatch('cadeco/moneda/index', {
                }).then(data => {
                    this.monedas = data.data;
                }).finally(()=>{
                    this.cargando=false;
                })
            },
            show() {
                $(this.$refs.modal).modal('show');
                this.$validator.reset();
            },
            store() {
                let datos = {
                    'id_empresa':this.id,
                    'id_moneda':this.$data.id_moneda,
                    'numero':this.$data.numero,
                    'saldo_inicial':this.$data.saldo_inicial,
                    'fecha_inicial':this.$data.fecha_inicial,
                    'chequera':this.$data.chequera,
                    'abreviatura':this.$data.abreviatura,
                    'id_tipo_cuentas_obra':this.$data.id_tipo_cuentas_obra
                };
                return this.$store.dispatch('cadeco/cuenta/store',  datos)
                    .then((data) => {
                        this.cleanData();
                        this.$emit('created', data);
                        $(this.$refs.modal).modal('hide');
                    })
            },
            tipoCuenta(tipo) {
                this.id_tipo_cuentas_obra = tipo;
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.store()
                    }
                });
            },
        },
        watch:{
            chk_chequera(value) {
                this.chequera = value?1:0;
            },
            fecha(value) {
                var d = 0;
                var m = 0;
                var y = 0;

                if(value){
                    var date =  new Date (value);
                    d = date.getDate();
                    m = date.getMonth() + 1;
                    y = date.getFullYear();
                    if (d < 10) {
                        d = '0' + d;
                    }
                    if (m < 10) {
                        m = '0' + m;
                    }
                    this.fecha_inicial = y+'-'+ m+'-'+d+' 00:00:00.000';
                }
            },
        }
    }

</script>

<style scoped>

</style>
