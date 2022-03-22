<template>
    <span>
        <div class="card">
            <div class="card-body">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-4" v-if="cuentas">
                            <div class="form-group">
                                <label for="id_cuenta">Cuenta Bancaria:</label>
                                <select
                                    class="form-control"
                                    name="id_cuenta"
                                    data-vv-as="Cuenta"
                                    id="id_cuenta"
                                    v-model="id_cuenta"
                                    v-validate="{required: true}"
                                    :class="{'is-invalid': errors.has('id_cuenta')}">
                                    <option value>-- Cuenta --</option>
                                    <option v-for="(item, index) in cuentas" :value="item.id">
                                        {{ `${item.numero} ${item.abreviatura } (${item.empresa.razon_social})` }}
                                    </option>
                                </select>
                                <div style="display:block" class="invalid-feedback" v-show="errors.has('id_cuenta')">{{ errors.first('id_cuenta') }}</div>
                            </div>
                        </div>
                        <div class="col-md-1"></div>
                        <div class="col-md-1">
                            <label>Periodo</label>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group error-content">
                                <datepicker v-model = "fecha_inicial"
                                            name = "fecha_inicial"
                                            :format = "formatoFecha"
                                            :language = "es"
                                            :bootstrap-styling = "true"
                                            class = "form-control"
                                            v-validate="{required: true}"
                                            :disabled-dates="fechasDeshabilitadas"
                                            :class="{'is-invalid': errors.has('fecha_inicial')}"
                                ></datepicker>
                                <div class="invalid-feedback" v-show="errors.has('fecha_inicial')">{{ errors.first('fecha_inicial') }}</div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group error-content">
                                <datepicker v-model = "fecha_final"
                                            name = "fecha_final"
                                            :format = "formatoFecha"
                                            :language = "es"
                                            :bootstrap-styling = "true"
                                            class = "form-control"
                                            v-validate="{required: true}"
                                            :disabled-dates="fechasDeshabilitadas"
                                            :class="{'is-invalid': errors.has('fecha_final')}"
                                ></datepicker>
                                <div class="invalid-feedback" v-show="errors.has('fecha_final')">{{ errors.first('fecha_final') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="row" v-if="cargando">
                        <div class="col-md-12">
                            <div class="spinner-border text-success" role="status">
                                <span class="sr-only">Cargando...</span>
                            </div>
                        </div>
                    </div>
                    <div class="row" v-if="pagos != [] && id_cuenta != ''">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th class="encabezado">
                                            #
                                        </th>
                                        <th class="encabezado">
                                            Conciliado
                                        </th>
                                        <th class="encabezado">
                                            Fecha
                                        </th>
                                        <th class="encabezado">
                                            Importe
                                        </th>
                                        <th class="encabezado">
                                            Movimiento
                                        </th>
                                        <th class="encabezado">
                                            Número
                                        </th>
                                        <th class="encabezado">
                                            Beneficiario
                                        </th>
                                        <th class="encabezado">
                                            Concepto
                                        </th>
                                    </tr>
                                </thead>
                                <tbody v-for="(pago, i) in pagos">
                                    <tr>
                                        <td style="text-align: center">
                                            {{i + 1}}
                                        </td>
                                        <td style="text-align: right" >
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="true" :id="pago.conciliado" v-model="pago.conciliado" v-if="pago.conciliado" checked @click="conciliar(pago)">
                                                <input class="form-check-input" type="checkbox" value="false" :id="pago.conciliado" v-model="pago.conciliado" v-else @click="conciliar(pago)">
                                            </div>
                                        </td>
                                        <td>
                                            {{pago.fecha_format}}
                                        </td>
                                        <td>
                                            {{pago.importe_cadeco}}
                                        </td>
                                        <td>
                                            Cheque
                                        </td>
                                        <td>
                                            [{{pago.referencia}}]
                                        </td>
                                        <td>
                                            {{pago.empresa_nombre ? pago.empresa_nombre : pago.destino}}
                                        </td>
                                        <td>
                                            {{pago.observaciones}}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    import datepicker from 'vuejs-datepicker';
    import {es} from 'vuejs-datepicker/dist/locale';
    export default {
        name: "conciliacion-bancaria-index",
        components: {datepicker, es},
        data() {
            return {
                es: es,
                cargando: false,
                cuentas: [],
                pagos: [],
                id_cuenta: '',
                fecha_inicial: '',
                fecha_final: '',
                fechasDeshabilitadas:{},
                datos: {}
            }
        },
        mounted() {
            this.fecha_inicial = new Date();
            this.fecha_final = new Date();
            this.fechasDeshabilitadas.from= new Date();
            this.getCuentas();
        },
        methods: {
            formatoFecha(date){
                return moment(date).format('DD/MM/YYYY');
            },
            getPagos() {
                this.cargando = true;
                this.pagos = [];
                this.datos.fecha_inicial = moment(this.fecha_inicial).format('YYYY-MM-DD 00:00:00');
                this.datos.fecha_final = moment(this.fecha_final).format('YYYY-MM-DD 00:00:00');
                this.datos.id_cuenta = this.id_cuenta;
                return this.$store.dispatch('finanzas/pago/porConciliar', {
                    'fecha_inicial' : moment(this.fecha_inicial).format('YYYY-MM-DD'),
                    'fecha_final' : moment(this.fecha_final).format('YYYY-MM-DD'),
                    'id_cuenta' : this.id_cuenta
                })
                    .then(data => {
                        this.pagos = data.data;
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            },
            getCuentas() {
                return this.$store.dispatch('cadeco/cuenta/index', {
                    params:{include:['empresa']}
                }).then(data => {
                    this.cuentas = data.data;
                });
            },
            conciliar(pago) {
                console.log(pago);
                return this.$store.dispatch('finanzas/pago/conciliar', {
                    'pago' : pago
                })
                .then(data => {
                    console.log(pago);
                   // this.pagos = data.data;
                })
            }
        },
        watch: {
            cargando(val) {
                $('tbody').css({
                    '-webkit-filter': val ? 'blur(2px)' : '',
                    'pointer-events': val ? 'none' : ''
                });
            },
            id_cuenta(value){
                if(value){
                   this.getPagos();
                }
            },
            fecha_inicial(value){
                if(value && this.id_cuenta != ''){
                    this.getPagos();
                }
            },
            fecha_final(value){
                if(value && this.id_cuenta != ''){
                    this.getPagos();
                }
            }
        }
    }
</script>
<style scoped>
    .sortable tr {
        cursor: pointer;
    }
    .encabezado{
         text-align: center;
         background-color: #f2f4f5
     }
</style>

