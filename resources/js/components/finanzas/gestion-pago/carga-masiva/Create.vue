<template>
    <span>
        <div class="row">
            <div class="col-12">
                <div class="invoice p-3 mb-3">
                    <div class="row">
                        <div class="col-12">
                            <h4>
                                <i class="fa fa-list"></i> Carga de Pagos
                            </h4>
                        </div>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div class="row justify-content-between">
                                 <div class="col-md-8">
                                    <div class="col-lg-12">
                                        <input type="file" class="form-control" id="carga_layout"
                                               @change="onFileChange"
                                               row="3"
                                               v-validate="{ ext: ['csv','xlsx']}"
                                               name="carga_layout"
                                               data-vv-as="Layout"
                                               ref="carga_layout"
                                               :class="{'is-invalid': errors.has('carga_layout')}"
                                        >
                                        <div class="invalid-feedback" v-show="errors.has('carga_layout')">{{ errors.first('carga_layout') }} (csv)</div>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="row" v-if="pagos.length > 0">
                                <div class="col-12">
                                    <div class="invoice p-3 mb-3">
                                        <div class="row">
                                            <div class="col-9">
                                                <h3>Pagos</h3>
                                            </div>
                                            <div class="col-3">
                                                <h6 align="right">Total: {{pagos.length}}</h6>
                                                <h6 align="right">Pagables: {{resumen.pagables}}</h6>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="table-responsive">
                                                    <table class="table table-striped">
                                                        <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Documento</th>
                                                            <th>Fecha</th>
                                                            <th>Vencto.</th>
                                                            <th>Moneda</th>
                                                            <th>Importe</th>
                                                            <th>Saldo</th>
                                                            <th>Beneficiario</th>
                                                            <th>Cuenta Cargo</th>
                                                            <th>Fecha Pago</th>
                                                            <th>Referencia Pago</th>
                                                            <th class="money">Monto Pagado<br>(Moneda Documento)</th>
                                                            <th>Tipo Cambio</th>
                                                            <th class="money">Monto Pagado<br>(Moneda Cuenta)</th>
                                                            <th>Estado</th>
                                                            <th> </th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr v-for="(pago, i) in pagos">
                                                                <td>{{i+1}}</td>
                                                                <td>{{pago.referencia_documento}}</td>
                                                                <td>{{pago.fecha_documento}}</td>
                                                                <td>{{pago.vencimiento_documento}}</td>
                                                                <td>{{pago.moneda_documento}}</td>
                                                                <td style="text-align: right">{{pago.monto_documento_format}}</td>
                                                                <td style="text-align: right">{{pago.saldo_documento_format}}</td>
                                                                <td >{{pago.beneficiario}}</td>
                                                                <td >
                                                                    <div class="col-12" v-if="pago.estado.estado == 1 || pago.estado.estado == 10">
                                                                        <select
                                                                                v-on:change="changeCuenta(pago)"
                                                                            class="form-control"
                                                                            :name="`id_cuenta_cargo[${i}]`"
                                                                            v-model="pago.id_cuenta_cargo"
                                                                            v-validate="{required: true }"
                                                                            data-vv-as="Cuenta Cargo"
                                                                            :class="{'is-invalid': errors.has(`id_cuenta_cargo[${i}]`)}"
                                                                        >
                                                                                 <option v-for="cuenta in cuentas_cargo" :value="cuenta.id_cuenta">{{ cuenta.numero }} ({{cuenta.abreviatura}})</option>
                                                                            </select>
                                                                        <div class="invalid-feedback"
                                                                             v-show="errors.has(`id_cuenta_cargo[${i}]`)">{{ errors.first(`id_cuenta_cargo[${i}]`) }}
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td >
                                                                        <div class="form-group error-content" v-if="pago.estado.estado == 1 || pago.estado.estado == 10">
                                                                            <div class="form-group">
                                                                                <datepicker v-model = "pago.fecha_pago_s"
                                                                                            name = "fecha_pago"
                                                                                            :format = "formatoFecha"
                                                                                            :language = "es"
                                                                                            :bootstrap-styling = "true"
                                                                                            class = "form-control"
                                                                                            v-validate="{required: true}"
                                                                                            :class="{'is-invalid': errors.has('fecha_pago')}"
                                                                                            :disabled-dates="fechasDeshabilitadas"
                                                                                            value=""
                                                                                >
                                                                                </datepicker>
                                                                                 <div class="invalid-feedback" v-show="errors.has('fecha_pago')">{{ errors.first('fecha_pago') }}</div>
                                                                            </div>
                                                                        </div>
                                                                </td>
                                                                <td >
                                                                    <div class="col-12">
                                                                        <div class="form-group error-content" v-if="pago.estado.estado == 1 || pago.estado.estado == 10">
                                                                            <input
                                                                                    type="text"
                                                                                    data-vv-as="Referencia Pago"
                                                                                    v-validate="{required: true}"
                                                                                    class="form-control"
                                                                                    :name="`referencia_pago[${i}]`"
                                                                                    placeholder="Referencia Pago"
                                                                                    v-model="pago.referencia_pago"
                                                                                    :class="{'is-invalid': errors.has(`referencia_pago[${i}]`)}">
                                                                            <div class="invalid-feedback" v-show="errors.has(`referencia_pago[${i}]`)">{{ errors.first(`referencia_pago[${i}]`) }}</div>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td >
                                                                    <div class="col-12">
                                                                        <div class="form-group error-content" v-if="pago.estado.estado == 1 || pago.estado.estado == 10">
                                                                            <input
                                                                                    readonly="readonly"
                                                                                    type="number"
                                                                                    step="any"
                                                                                    data-vv-as="Monto Pagado Documento"
                                                                                    v-validate="{required: true, min_value:0.1, max_value:(parseFloat(pago.saldo_documento)+parseInt(1)), decimal:2}"
                                                                                    class="form-control"
                                                                                    :name="`monto_pagado_documento[${i}]`"
                                                                                    placeholder="Monto Pagado"
                                                                                    v-model="pago.monto_pagado_documento"
                                                                                    :class="{'is-invalid': errors.has(`monto_pagado_documento[${i}]`)}">
                                                                            <div class="invalid-feedback" v-show="errors.has(`monto_pagado_documento[${i}]`)">{{ errors.first(`monto_pagado_documento[${i}]`) }}</div>
                                                                            <div  v-if="parseFloat(pago.monto_pagado_documento) > (parseFloat(pago.saldo_documento) + parseFloat(0.99))" class="text-danger small">Supera el saldo de la transacción.</div>
                                                                        </div>
                                                                    </div>
                                                               </td>
                                                                <td >
                                                                    <div class="col-12">
                                                                        <div class="form-group error-content" v-if="pago.estado.estado == 1 || pago.estado.estado == 10">
                                                                            <input
                                                                                    v-on:keyup="calcula_montos(pago)"
                                                                                    type="number"
                                                                                    data-vv-as="Tipo Cambio"
                                                                                    v-validate="{required: true, min_value: 1}"
                                                                                    class="form-control"
                                                                                    :name="`tipo_cambio[${i}]`"
                                                                                    placeholder="Tipo Cambio"
                                                                                    v-model="pago.tipo_cambio"
                                                                                    :class="{'is-invalid': errors.has(`tipo_cambio[${i}]`)}">
                                                                            <div class="invalid-feedback" v-show="errors.has(`tipo_cambio[${i}]`)">{{ errors.first(`tipo_cambio[${i}]`) }}</div>
                                                                        </div>
                                                                    </div>
                                                                </td>

                                                                <td >
                                                                    <div class="col-12">
                                                                        <div class="form-group error-content" v-if="pago.estado.estado == 1 || pago.estado.estado == 10">
                                                                            <input
                                                                                    v-on:keyup="calcula_montos(pago)"
                                                                                    type="number"
                                                                                    step="any"
                                                                                    data-vv-as="Monto Pagado"
                                                                                    v-validate="{required: true, decimal:2}"
                                                                                    class="form-control"
                                                                                    :name="`monto_pagado[${i}]`"
                                                                                    placeholder="Monto Pagado"
                                                                                    v-model="pago.monto_pagado"
                                                                                    :class="{'is-invalid': errors.has(`monto_pagado[${i}]`)}">
                                                                            <div class="invalid-feedback" v-show="errors.has(`monto_pagado[${i}]`)">{{ errors.first(`monto_pagado[${i}]`) }}</div>
                                                                        </div>
                                                                    </div>
                                                               </td>
                                                               <td class="text-center" >
                                                                    <small :class="[pago.estado.clase_badge]">
                                                                        {{ pago.estado.descripcion }}
                                                                    </small>
                                                                    <i class="fa fa-exclamation-triangle" v-if="pago.estado.estado==10" style="color: orange" title="Transacción no autorizada en el módulo de control remesas"></i>
                                                                </td>

                                                                 <td></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="salir">
                                <span v-if="cargando">
                                    <i class="fa fa-spin fa-spinner"></i>
                                </span>
                                <span v-else>
                                    Cerrar
                                </span>
                            </button>
                            <button type="button" class="btn btn-primary" v-if="pagos.length > 0 && resumen.pagables > 0 || errors.count() > 0" @click="cambio">Registrar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    import Datepicker from 'vuejs-datepicker';
    import {es} from 'vuejs-datepicker/dist/locale';

    export default {
        name: "carga-masiva-create",
        components: {Datepicker},

        data() {
            return {
                es: es,
                cargando: false,
                pagos:[],
                resumen:[],
                cuentas_cargo:[],
                fechas_validacion:[],
                file_pagos : null,
                file_pagos_name : '',
                fechasDeshabilitadas:{}
            }
        },
        computed:{

        },
        methods: {
            changeCuenta(partida_pago){
                partida_pago.cuenta_cargo_obj = this.cuentas_cargo.find(x=>x.id_cuenta === partida_pago.id_cuenta_cargo);
                this.calcula_montos(partida_pago);
            },
            validaTC(partida_pago){
                if(parseInt(partida_pago.cuenta_cargo_obj.id_moneda) === parseInt( partida_pago.id_moneda_transaccion)){
                    partida_pago.tipo_cambio = 1;
                }
            },
            calcula_montos(partida_pago){
                this.validaTC(partida_pago);
                partida_pago.monto_pagado_documento = parseFloat(Math.round(partida_pago.monto_pagado * partida_pago.tipo_cambio * 100)/100).toFixed(2);
            },
            formatoFecha(date){
                return moment(date).format('DD-MM-YYYY');
            },
            cargarLayout(){
                this.cargando = true;
                var formData = new FormData();
                formData.append('pagos',  this.file_pagos);
                formData.append('nombre_archivo',  this.file_pagos_name);
                return this.$store.dispatch('finanzas/carga-masiva-pago/cargarLayout',
                    {
                        data: formData,
                        config: {
                            params: { _method: 'POST'}
                        }
                    })
                    .then(data => {
                        if(data.data.length > 0){
                            this.pagos = data.data;
                            this.cuentas_cargo = data.cuentas_cargo;
                            this.fechas_validacion = data.fechas_validacion;
                            this.resumen = data.resumen;
                            this.fechasDeshabilitadas.from=new Date(this.fechas_validacion.from);
                            this.fechasDeshabilitadas.to=new Date(this.fechas_validacion.to);

                        }else{
                            if(this.$refs.carga_layout.value !== ''){
                                this.$refs.carga_layout.value = '';
                                this.file_pagos = null;
                            }
                            this.pagos = [];
                            swal('Carga Masiva', 'Archivo de layout sin pagos válidos.', 'warning')
                        }
                    }).finally(() => {
                        this.cargando = false;
                    });
            },
            createImage(file, tipo) {
                var reader = new FileReader();
                var vm = this;

                reader.onload = (e) => {
                    vm.file_pagos = e.target.result;
                };
                reader.readAsDataURL(file);

            },

            onFileChange(e){
                this.file_pagos = null;
                var files = e.target.files || e.dataTransfer.files;
                if (!files.length)
                    return;
                this.file_pagos_name = files[0].name;
                this.createImage(files[0]);
                setTimeout(() => {
                    this.validate()
                }, 500);
            },
            salir(){
                return this.$store.dispatch('finanzas/carga-masiva-pago/salir')
                    .then(() => {
                        this.$router.push({name: 'carga-masiva'});
                    });
            },

            store() {
                return this.$store.dispatch('finanzas/carga-masiva-pago/store', this.$data)
                    .then((data) => {
                        this.$router.push({name: 'carga-masiva'});
                    });
            },

            cambio() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.store()
                    }
                });
            },

            validate() {
                this.$validator.validate().then(result => {
                    if (result){
                        if(this.$refs.carga_layout.value === ''){
                            swal('¡Error!', 'Seleccione un archivo.', 'warning')
                        }else{
                            this.cargarLayout()
                        }
                    }else{
                        if(this.$refs.carga_layout.value !== ''){
                            this.$refs.carga_layout.value = '';
                            this.file_pagos = null;
                        }
                        this.$validator.errors.clear();
                    }
                });
            }
        }
    }
</script>

<style scoped>
thead th {text-align: center}

th .money
{
    width: 150px;
    max-width: 150px;
    min-width: 150px;
    text-align: center;
}
td .money
{
    text-align: right;
}
</style>