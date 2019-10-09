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
                                                            <th>Referencia solicitud / factura</th>
                                                            <th>Monto solicitud / factura</th>
                                                            <th>Moneda solicitud / factura</th>
                                                            <th>Beneficiario</th>
                                                            <th>Cuenta Cargo</th>
                                                            <th>Fecha Pago</th>
                                                            <th>Referencia Pago</th>
                                                            <th>Tipo Cambio</th>
                                                            <th>Monto Pagado</th>
                                                            <th>Tipo de Transacción Pagada</th>
                                                            <th>Estado</th>
                                                            <th> </th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr v-for="(pago, i) in pagos">
                                                                <td>{{i+1}}</td>
                                                                <td v-if="pago.id_transaccion == null" class="text-danger">No se encontro la factura ó solicitud.</td>
                                                                <td v-else>{{pago.referencia_factura}}</td>
                                                                <td>${{(parseFloat(pago.monto_factura)).formatMoney(2,'.',',')}}</td>
                                                                <td>{{pago.moneda_factura}}</td>
                                                                <td v-if="pago.beneficiario">{{pago.beneficiario}}</td>
                                                                <td v-else class="text-center text-danger">No encontrado</td>
                                                                <td v-if="pago.cuenta_encontrada && pago.id_transaccion != null && pago.estado.estado != -1">{{pago.cuenta_cargo.numero}} ({{pago.cuenta_cargo.abreviatura}})</td>
                                                                <td v-else-if="pago.id_transaccion != null && pago.estado.estado != 2 && pago.estado.estado > 0">
                                                                    <select
                                                                        class="form-control"
                                                                        :name="`id_cuenta_cargo[${i}]`"
                                                                        v-model="pago.id_cuenta_cargo"
                                                                        v-validate="{required: true }"
                                                                        data-vv-as="Cuenta Cargo"
                                                                        :class="{'is-invalid': errors.has(`id_cuenta_cargo[${i}]`)}"
                                                                >

                                                                             <option v-for="cuenta in pago.cuenta_cargo" :value="cuenta.id">{{ cuenta.numero }} ({{cuenta.abreviatura}})</option>
                                                                        </select>
                                                                    <div class="invalid-feedback"
                                                                         v-show="errors.has(`id_cuenta_cargo[${i}]`)">{{ errors.first(`id_cuenta_cargo[${i}]`) }}
                                                                    </div>
                                                                </td>
                                                                <td v-else></td>
                                                                <td v-if="pago.fecha_pago == false && pago.datos_completos_correctos == 1 && pago.estado.estado != -1">
                                                                    <div class="col-md-12">
                                                                        <div class="form-group error-content">
                                                                            <div class="form-group">
                                                                                <datepicker v-model = "pago.fecha_pago_s"
                                                                                            name = "fecha_pago"
                                                                                            :format = "formatoFecha"
                                                                                            :bootstrap-styling = "true"
                                                                                            class = "form-control"
                                                                                            v-validate="{required: true}"
                                                                                            :class="{'is-invalid': errors.has('fecha_pago')}"
                                                                                            value=""
                                                                                ></datepicker>
                                                                                 <div class="invalid-feedback" v-show="errors.has('fecha_pago')">{{ errors.first('fecha_pago') }}</div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td v-else-if="pago.datos_completos_correctos == 0 || pago.estado.estado == -1"></td>
                                                                <td v-else>{{pago.fecha_pago}}</td>
                                                                <td v-if="pago.datos_completos_correctos == 1 && pago.estado.estado > 0">
                                                                    <div class="col-12">
                                                                        <div class="form-group error-content">
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
                                                                <td v-else></td>
                                                                <td v-if="pago.tipo_cambio == false">
                                                                    <div class="col-12">
                                                                        <div class="form-group error-content">
                                                                            <input
                                                                                    type="text"
                                                                                    data-vv-as="Tipo Cambio"
                                                                                    v-validate="{required: true}"
                                                                                    class="form-control"
                                                                                    :name="`tipo_cambio[${i}]`"
                                                                                    placeholder="Tipo Cambio"
                                                                                    v-model="pago.tipo_cambio"
                                                                                    :class="{'is-invalid': errors.has(`tipo_cambio[${i}]`)}">
                                                                            <div class="invalid-feedback" v-show="errors.has(`tipo_cambio[${i}]`)">{{ errors.first(`tipo_cambio[${i}]`) }}</div>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td v-else>{{pago.tipo_cambio}}</td>
                                                                <td v-if="pago.id_transaccion != null && pago.estado.estado != 2 && pago.estado.estado > 0 && pago.datos_completos_correctos==1">
                                                                    <div class="col-12">
                                                                        <div class="form-group error-content">
                                                                            <input
                                                                                    type="number"
                                                                                    step="any"
                                                                                    data-vv-as="Monto Pagado"
                                                                                    v-validate="{required: true, min_value:0.1, max_value:pago.monto_factura+1, decimal:3}"
                                                                                    class="form-control"
                                                                                    :name="`monto_pagado[${i}]`"
                                                                                    placeholder="Monto Pagado"
                                                                                    v-model="pago.monto_pagado"
                                                                                    :class="{'is-invalid': errors.has(`monto_pagado[${i}]`)}">
                                                                            <div class="invalid-feedback" v-show="errors.has(`monto_pagado[${i}]`)">{{ errors.first(`monto_pagado[${i}]`) }}</div>
                                                                            <div  v-if="pago.validar_monto == false && pago.monto_pagado != pago.monto_factura" class="text-danger small">No corresponde con el monto de la transacción.</div>
                                                                        </div>
                                                                    </div>
                                                               </td>
                                                               <td v-else-if= "pago.datos_completos_correctos==0" class="text-center text-danger">Formato incorrecto.</td>
                                                               <td v-else></td>
                                                               <td v-if="pago.datos_completos_correctos == 1">{{pago.pago_a_generar}}</td>
                                                               <td v-else class="text-center text-danger">Favor de visualizar</td>
                                                               <td class="text-center" v-if="pago.datos_completos_correctos == 1">
                                                                    <small class="badge" :class="{'badge-danger': pago.estado.estado == 0, 'badge-warning': pago.estado.estado == 2,  'badge-success': pago.estado.estado == 1, 'badge-info': pago.estado.estado == -1}">
                                                                        {{ pago.estado.descripcion }}
                                                                    </small>
                                                                </td>
                                                                <td v-else class="text-center text-danger">que no contenga comas extra.</td>
                                                                <td class="text-center" v-if="pago.estado.estado==0 && pago.datos_completos_correctos==1"><i class="fa fa-exclamation-triangle" style="color: red" title="No se encontro la transacción."></i></td>
                                                                <td class="text-center" v-else-if="pago.monto_pagado == 0 && pago.datos_completos_correctos==1"><i class="fa fa-exclamation-triangle" style="color: orange" title="El monto no puede ser cero."></i></td>
                                                                <td class="text-center" v-else-if="pago.estado.estado != -1 && !pago.cuenta_encontrada && pago.datos_completos_correctos==1"><i class="fa fa-exclamation-triangle" style="color: orange" title="No se encontro la cuenta cargo."></i></td>
                                                                 <td class="text-center" v-else-if="pago.datos_completos_correctos==0"><i class="fa fa-exclamation-triangle" style="color: red" title="Favor de validar que no contenga comas extras."></i></td>
                                                                <td v-else></td>
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
    export default {
        name: "carga-masiva-create",
        components: {Datepicker},
        data() {
            return {
                cargando: false,
                pagos:[],
                resumen:[],
                file_pagos : null,
                file_pagos_name : ''
            }
        },
        methods: {
            formatoFecha(date){
                return moment(date).format('DD/MM/YYYY');
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
                            this.resumen = data.resumen;
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
                        //this.cargarLayout()
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

</style>