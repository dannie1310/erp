<template>
    <span>
        <button @click="show" type="button" class="btn btn-sm btn-outline-primary" title="Editar Cuenta">
            <i class="fa fa-pencil"></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-th"></i> EDICIÓN DE CUENTA BANCARIA</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body" v-if="cuenta">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="form-group error-content">
                                            <label for="numero">Número:</label>
                                            <input
                                                type="number"
                                                name="numero"
                                                data-vv-as="Número de Cuenta"
                                                v-validate="{required: true,  min:9, max:18}"
                                                class="form-control"
                                                id="numero"
                                                placeholder="Cuenta Contable"
                                                v-model="cuenta.numero"
                                                :class="{'is-invalid': errors.has('numero')}">
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
                            </div>
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
        name: "cuenta-edit",
        props: ['id'],
        components: {Datepicker},
        data() {
            return {
                es: es,
                cuenta:{
                    numero:'',
                    id_moneda:'',
                    fecha_inicial:'',
                    saldo_inicial:0,
                    chequera:0,
                    abreviatura:'',
                    id_tipo_cuentas_obra:1,
                },
                fecha:'',
                chk_chequera:false,
                monedas:[]
            }
        },
        computed: {
        },
        mounted(){
            this.getMonedas();
        },
        methods:{
            getMonedas(){
                this.cargando = true;
                this.$store.commit('cadeco/moneda/SET_MONEDAS', null);
                return this.$store.dispatch('cadeco/moneda/index', {
                }).then(data => {
                    this.monedas = data.data;
                }).finally(()=>{

                })
            },
            loadData(data){
                this.cuenta.numero = data.numero;
                this.cuenta.id_moneda = data.id_moneda;
                this.cuenta.fecha_inicial = data.numero;
                this.cuenta.numero = data.numero;
                this.cuenta.numero = data.numero;
                this.cuenta.numero = data.numero;
            },
            show() {
                this.cargando = true;
                this.$store.commit('cadeco/cuenta/SET_CUENTA', null);
                return this.$store.dispatch('cadeco/cuenta/find', {
                    id: this.id,
                    params: { include: 'moneda,tiposCuentasObra' }
                }).then(data => {
                    this.cuenta= data;
                    this.cargando=false;
                    $(this.$refs.modal).modal('show')
                })
            },
            valChequera(value){
                return value == 1?'Si':'No'
            }
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
