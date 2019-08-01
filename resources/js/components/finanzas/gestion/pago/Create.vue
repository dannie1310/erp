<template>
    <span>
        <div class="row">
            <div class="col-12">
                <div class="invoice p-3 mb-3">
                    <div class="row">
                        <div class="col-12">
                            <h4>
                                <i class="fa fa-list"></i> Recursos Autorizados
                            </h4>
                        </div>
                    </div>
                    <form role="form" @submit.prevent="validate">

                        <div class="modal-body">
                            <div class="row justify-content-between">
                                <div class="col-md-8">
                                     <label for="carga_layout_interbancario" class="col-lg-12 col-form-label">Layout Interbancario</label>
                                    <div class="col-lg-12">
                                        <input type="file" class="form-control" id="carga_layout_interbancario" @change="onFileChange"
                                               row="3"
                                               v-validate="{ ext: ['txt']}"
                                               name="carga_layout_interbancario"
                                               data-vv-as="Layout Interbancario"
                                               ref="carga_layout_interbancario"
                                               :class="{'is-invalid': errors.has('carga_layout_interbancario')}"
                                        >
                                        <div class="invalid-feedback" v-show="errors.has('carga_layout_interbancario')">{{ errors.first('carga_layout_interbancario') }} (doc)</div>
                                    </div>
                                </div>
                            </div>

                            <div class="row" v-if="documentos">
                                <div class="col-12">
                                    <div class="invoice p-3 mb-3">
                                        <div class="row">
                                            <div class="col-9">
                                                <h3>Documentos Liberados de la Remesa</h3>
                                            </div>
                                            <div class="col-3">
                                                <h6 align="right">Total: {{documentos.length}}</h6>
                                            </div>
                                        </div>
                                        <div class="row">
                                <div  class="col-12">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Concepto</th>
                                                <th>Beneficiario</th>
                                                <!-- <th>Importe Moneda Original</th>
                                                 <th>Moneda</th>
                                                 <th>Importe en Pesos</th>-->
                                                <th>Importe a Pagar en Pesos</th>
                                                <th>Cuenta Cargo</th>
                                                <th>Cuenta Abono</th>
                                                <th>Seleccionar</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(doc, i) in documentos">
                                                    <td>{{i+1}}</td>
                                                    <td>{{doc.concepto}}</td>
                                                    <td v-if="doc.beneficiario != null">{{doc.beneficiario}}</td>
                                                    <td class="text-danger" v-else>No registrado</td>
                                                    <!--<td class="text-right">{{doc.monto_total_format}}</td>-->
                                                    <!--<td>{{doc.moneda.abreviatura}}</td>-->
                                                    <!--<td class="text-right">{{doc.saldo_moneda_nacional_format}}</td>-->
                                                    <td class="text-right">${{parseFloat(doc.importe_total).formatMoney(2, '.', ',') }}</td>
                                                    <td style="width: 15%;">
                                                        <select
                                                            class="form-control"
                                                            :name="`id_cuenta_cargo[${i}]`"
                                                            v-model="doc.id_cuenta_cargo"
                                                            v-validate="{required: doc.selected == true ? true : false}"
                                                            data-vv-as="Cuenta Cargo"
                                                            :class="{'is-invalid': errors.has(`id_cuenta_cargo[${i}]`)}"
                                                        >
                                                             <option value>-- Selecciona una cuenta --</option>
                                                             <option v-for="cuenta in cuenta_cargo" :value="cuenta.id">{{ cuenta.abreviatura }} ({{cuenta.numero}})</option>
                                                        </select>
                                                        <div class="invalid-feedback"
                                                             v-show="errors.has(`id_cuenta_cargo[${i}]`)">{{ errors.first(`id_cuenta_cargo[${i}]`) }}
                                                        </div>
                                                    </td>
                                                    <td v-if = "doc.tipo_documento != 12 && doc.empresa && doc.empresa.cuentasBancariasProveedor.data.length > 0" style="width: 15%;">
                                                        <select class="form-control"
                                                                :name="`id_cuenta_abono[${i}]`"
                                                                v-model="doc.id_cuenta_abono"
                                                                v-validate="{required: doc.selected == true ? true:false}"
                                                                data-vv-as="Cuenta Abono"
                                                                :class="{'is-invalid': errors.has(`id_cuenta_abono[${i}]`)}"
                                                        >
                                                             <option value>-- Selecciona una cuenta --</option>
                                                             <option v-for="cuenta in doc.empresa.cuentasBancariasProveedor.data" :value="cuenta.id">{{getCuentaAbono(cuenta)}}</option>
                                                        </select>
                                                        <div class="invalid-feedback"
                                                             v-show="errors.has(`id_cuenta_abono[${i}]`)">{{ errors.first(`id_cuenta_abono[${i}]`) }}
                                                        </div>
                                                    </td>
                                                    <td v-else-if="doc.tipo_documento == 12 && doc.fondo && doc.fondo.empresa && doc.fondo.empresa.cuentasBancariasProveedor.data.length > 0 " style="width: 15%;">
                                                        <select class="form-control"
                                                                :name="`id_cuenta_abono[${i}]`"
                                                                v-model="doc.id_cuenta_abono"
                                                                v-validate="{required: doc.selected == true ? true:false}"
                                                                data-vv-as="Cuenta Abono"
                                                                :class="{'is-invalid': errors.has(`id_cuenta_abono[${i}]`)}"
                                                        >
                                                             <option value>-- Selecciona una cuenta --</option>
                                                             <option v-for="cuenta in doc.fondo.empresa.cuentasBancariasProveedor.data" :value="cuenta.id">{{getCuentaAbono(cuenta)}}</option>
                                                        </select>
                                                        <div class="invalid-feedback"
                                                             v-show="errors.has(`id_cuenta_abono[${i}]`)">{{ errors.first(`id_cuenta_abono[${i}]`) }}
                                                        </div>
                                                    </td>
                                                    <td class="text-danger" style="width: 15%;" v-else-if="doc.tipo_documento != 12 && doc.empresa && doc.empresa.cuentasBancariasProveedor.data.length == 0">Beneficiario sin cuentas bancarias registradas</td>
                                                    <td class="text-danger" style="width: 15%;" v-else-if="doc.tipo_documento == 12 && doc.fondo && doc.fondo.empresa && doc.fondo.empresa.cuentasBancariasProveedor.data.length == 0">Beneficiario de fondo sin cuentas bancarias registradas</td>
                                                    <td class="text-danger"  style="width: 15%;" v-else>Beneficiario no registrado en cátalogo de Empresas SAO</td>

                                                    <td class="text-center" v-if="doc.empresa && doc.empresa.cuentasBancariasProveedor.data.length > 0 && doc.tipo_cambio == 1 || doc.fondo && doc.fondo.empresa && doc.fondo.empresa.cuentasBancariasProveedor.data.length > 0 && doc.tipo_cambio == 1 "><input type="checkbox" :value="doc.id" v-model="doc.selected"></td>
                                                    <td class="text-center" v-else-if="doc.tipo_cambio != 1"><i class="fa fa-exclamation-triangle" style="color: orange" title="Partida en moneda extranjera no seleccionable por el momento."></i></td>
                                                    <td class="text-center" v-else><i class="fa fa-exclamation-triangle" style="color: red" title="No seleccionable por datos faltantes."></i></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                                        <div  v-if="documentos">
                                            <form role="form" @submit.prevent="validate">
                                                <div class="row" align="left">
                                                    <div class="table-responsive col-md-12">
                                                        <div class="col-12">
                                                            <div class="table-responsive">
                                                                <table class="table">
                                                                    <tbody>
                                                                        <tr>
                                                                            <th style="width:25%" class="bg-gray-light">Remesa Autorizada (MXP):</th>
                                                                            <td style="width:15%" class="bg-gray-light" align="right"><b>$&nbsp; {{(parseFloat(monto_total_remesa)).formatMoney(2,'.',',')}}</b></td>

                                                                            <th style="width:10%"></th>
                                                                            <td style="width:10%"></td>

                                                                            <th>Documentos Seleccionados a Pagar (MXP):</th>
                                                                            <td style="width:15%" align="right"> <b>$&nbsp;{{(parseFloat(sumaSeleccionImportes)).formatMoney(2,'.',',')}}</b></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th style="width:20%">Distribuciones Anteriores (MXP):</th>
                                                                            <td style="width:15%" align="right"><b>$&nbsp; {{(parseFloat(monto_distribuido_anteriormente)).formatMoney(2,'.',',')}}</b></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th class="bg-gray-light">Distribucion Actual (MXP):</th>
                                                                            <td align="right" class="bg-gray-light"> <b>$&nbsp;{{(parseFloat(sumaSeleccionImportes)).formatMoney(2,'.',',')}}</b></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>Restante por Distribuir (MXP):</th>
                                                                            <td align="right"> <b>$&nbsp;{{(parseFloat(monto_total_remesa-(sumaSeleccionImportes + monto_distribuido_anteriormente)).formatMoney(2,'.',','))}}</b></td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" v-on:click="salir">Cerrar</button>
                            <button type="button" class="btn btn-primary" @click="validate">Cargar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "distribuir-recurso-remesa-create",
        data() {
            return {
                bitacora_validada:[],
                cargando: false,
                file_interbancario : null
            }
        },
        mounted() {

        },
        computed: {
        },
        methods: {
            onFileChange(e){
                this.file_interbancario = null;
                var files = e.target.files || e.dataTransfer.files;
                if (!files.length)
                    return;
                if(e.target.id == 'carga_layout_interbancario') {
                    this.createImage(files[0]);
                }
            },
            createImage(file, tipo) {
                var reader = new FileReader();
                var vm = this;

                reader.onload = (e) => {
                    vm.file_interbancario = e.target.result;
                };
                reader.readAsDataURL(file);
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result){
                        if(this.$refs.carga_layout_interbancario.value === ''){
                            swal('¡Error!', 'Seleccione un archivo.', 'warning')
                        }else{
                            this.cargarLayout()
                        }
                        this.cargarLayout()
                    }else{
                        if(this.$refs.carga_layout_interbancario.value !== ''){
                            this.$refs.carga_layout_interbancario.value = '';
                            this.file_interbancario = null;
                        }
                        this.$validator.errors.clear();
                        swal('', 'Error archivos de entrada invalidos.', 'error')
                    }
                });
            },
            getRemesas() {
                this.cargando = true;
                let self = this
                return self.$store.dispatch('finanzas/remesa/index', {
                    params: {
                        scope: 'liberada',
                        sort: 'FechaHoraRegistro',
                        order: 'DESC'
                    }
                })
                    .then(data => {
                        this.remesas = data.data;
                    })
                    .finally(() => {
                        this.cargando = false;
                    });
            },


            getDocumentos(){
                this.documentos = null;
                this.original = null;
                this.monto_distribuido_anteriormente = 0;
                this.monto_total_remesa = 0;
                this.cargando = true;
                let self = this
                return self.$store.dispatch('finanzas/remesa/find',{
                    id: self.id_remesa,
                    params: {
                        include: ['documentosDisponibles', 'documentosDisponibles.empresa.cuentasBancariasProveedor.banco', 'documentosDisponibles.moneda', 'remesaLiberada', 'documentosDisponibles.fondo.empresa.cuentasBancariasProveedor.banco']
                    }
                })
                    .then(data => {
                        if(data.documentosDisponibles.data != 0) {
                            this.documentos = [];
                            this.original = [];
                            this.monto_distribuido_anteriormente = data.remesaLiberada.monto_distribuido;
                            this.monto_total_remesa = data.remesaLiberada.monto_total_remesa;
                            this.original = JSON.parse(JSON.stringify(data.documentosDisponibles.data));
                            this.documentos = JSON.parse(JSON.stringify(data.documentosDisponibles.data));
                        }else{
                            swal("La remesa seleccionada no tiene documentos disponibles para generar una distribución de recursos.", {
                                icon: "warning",
                                buttons: {
                                    confirm: {
                                        text: 'Aceptar'
                                    }
                                }
                            })
                        }
                    })
                    .finally(() => {
                        this.cargando = false;
                        this.getCuentaCargo();
                    });
            },


            salir(){
                // return this.$store.dispatch('finanzas/distribuir-recurso-remesa/salir')
                //     .then(() => {
                //         this.$router.push({name: 'distribuir-recurso-remesa'});
                //     });
            }
        },
        watch: {

        }
    }
</script>
<style scoped>
    .error-label {
        width: 100%;
        margin-top: 0.25rem;
        font-size: 80%;
        color: #dc3545;
    }
</style>
