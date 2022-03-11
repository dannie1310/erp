<template>
    <span>
        <div class="row">
            <div class="col-12">
                <div class="invoice p-3 mb-3">
                    <div class="row">
                        <div class="col-12">
                            <h4>
                                <i class="fa fa-list"></i> Bitácora de Movimientos
                            </h4>
                        </div>
                    </div>
                    <form role="form" @submit.prevent="validate">

                        <div class="modal-body">
                            <div class="row justify-content-between">
                                <div class="col-md-5">
                                     <label for="carga_bitacora" class="col-lg-12 col-form-label">Cargar Bitácora</label>
                                    <div class="col-lg-12">
                                        <input type="file" class="form-control" id="carga_bitacora"
                                               @change="onFileChange"
                                               :disabled="bitacora.length > 0"
                                               row="3"
                                               v-validate="{required:true, ext: ['txt']}"
                                               name="carga_bitacora"
                                               data-vv-as="Bitácora"
                                               ref="carga_bitacora"
                                               :class="{'is-invalid': errors.has('carga_bitacora')}"
                                        >
                                        <div class="invalid-feedback" v-show="errors.has('carga_bitacora')">{{ errors.first('carga_bitacora') }} (txt)</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="dispersion" class="col-lg-12 col-form-label">Seleccione Dispersión de Recursos</label>
                                    <div class="col-lg-12">
                                        <select
                                            :disabled="bitacora.length > 0 || cargando"
                                                type="text"
                                                name="dispersion"
                                                data-vv-as="Dispersion de Recursos"
                                                v-validate="{required:true}"
                                                class="form-control"
                                                id="dispersion"
                                                ref="dispersion"
                                                v-model="id_dispersion"
                                                :class="{'is-invalid': errors.has('dispersion')}"
                                            >
                                                    <option value>-- Dispersion --</option>
                                                    <option v-for="dispersion in dispersiones" :value="dispersion.id" v-if="dispersiones">
                                                        Año: {{ dispersion.remesa_liberada.remesa.año}} Semana: {{dispersion.remesa_liberada.remesa.semana}} Remesa: {{dispersion.remesa_liberada.remesa.tipo}} ({{dispersion.remesa_liberada.remesa.folio}})</option>
                                            </select>
                                            <div class="invalid-feedback" v-show="errors.has('dispersion')">{{ errors.first('dispersion') }}</div>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <!-- <br/>
                                    <label for="botton1" class="col-lg-12 col-form-label"></label> -->
                                    <div class="col-lg-12" style="margin-top:35px">
                                    <button type="submit" class="btn btn-primary float-right" v-if="bitacora.length === 0"><i class="fa fa-spin fa-spinner" v-if="cargando"></i>Validar</button>
                                    <button type="button"  id="botton1" class="btn btn-secondary float-right" @click="limpiar" v-else>Limpiar</button>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="row" v-if="bitacora.length > 0">
                                <div class="col-12">
                                    <div class="invoice p-3 mb-3">
                                        <div class="row">
                                            <div class="col-9">
                                                <h6>Pagos</h6>
                                            </div>
                                            <div class="col-3">
                                                <h6 align="right">Total: {{bitacora.length}}</h6>
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
                                                            <th>Concepto</th>
                                                            <th>Beneficiario</th>
                                                            <th>Importe</th>
                                                            <th>Cuenta Cargo</th>
                                                            <th>Cuenta Abono</th>
                                                            <th>Estado</th>
                                                            <!-- <th>Origen</th> -->
                                                            <th>Tipo de Transacción Pagada</th>
                                                            <!-- <th>Referencia de Transacción Pagada</th> -->
                                                            <th>Tipo de Pago a Generar</th>
                                                            <th></th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr v-for="(pago, i) in bitacora">
                                                                <td>{{i+1}}</td>
                                                                <td :title="`${pago.concepto}`">{{pago.concepto.substring(0, 30)}}</td>
                                                                <td>{{pago.beneficiario}}</td>
                                                                <td class="text-right">{{pago.monto_format}}</td>
                                                                <td>{{pago.cuenta_cargo.numero}} ({{pago.cuenta_cargo.abreviatura}})</td>
                                                                <td>{{pago.cuenta_abono.numero}} ({{pago.cuenta_abono.abreviatura}})</td>
                                                                <td class="text-center">
                                                                    <small class="badge" :class="{'badge-warning': pago.estado.estado != 1,  'badge-success': pago.estado.estado == 1}">
                                                                        {{ pago.estado.descripcion }}
                                                                    </small>
                                                                </td>
                                                                <!-- <td>{{pago.origen_docto}}</td> -->
                                                                <td>{{pago.id_transaccion_tipo}}</td>
                                                                <!-- <td>{{pago.referencia_docto}}</td> -->
                                                                <td>

                                                                    <span v-if="!pago.pagable" >
                                                                        <i class="fa fa-exclamation-triangle"style="color: red" title="Partida pagada previamente"></i>
                                                                        N/A
                                                                    </span>
                                                                    <span v-else-if="pago.select_transacciones">
                                                                        <select
                                                                            type="text"
                                                                            :name="`pago[${i}]`"
                                                                            data-vv-as="Pagos"
                                                                            v-validate="{required:true}"
                                                                            class="form-control"
                                                                            id="pago"
                                                                            v-model="pago.id_transaccion"
                                                                            :class="{'is-invalid': errors.has(`pago[${i}]`)}"
                                                                        >
                                                                            <option value=''>-- Seleccione --</option>
                                                                            <option v-for="transaccion in pago.select_transacciones" :value="transaccion.id">{{transaccion.folio}} {{transaccion.saldo}}</option>
                                                                            <option value=0>Aplicación Manual</option>
                                                                        </select>
                                                                        <div class="invalid-feedback" v-show="errors.has(`pago[${i}]`)">{{ errors.first(`pago[${i}]`) }}</div>
                                                                    </span>
                                                                    <span v-else>
                                                                        <span v-if="pago.aplicacion_manual" >
                                                                            <i class="fa fa-exclamation-triangle"style="color: orange" title="El pago requiere aplicación manual en CADECO"></i>
                                                                            {{pago.pago_a_generar}}
                                                                        </span>
                                                                        <span v-else>
                                                                            {{pago.pago_a_generar}}, Referencia: {{pago.referencia_docto}}, Folio:{{pago.folio}}, Saldo: {{pago.saldo}}
                                                                        </span>

                                                                    </span>
                                                                </td>
                                                                <td>
                                                                    <span v-if="pago.pagable">
                                                                        <i class="fa fa-check"></i>
                                                                    </span>
                                                                    <span v-else>
                                                                        <i class="fa fa-close"></i>
                                                                    </span>
                                                                </td>
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
                            <div class="col-md-12">
                                <button type="button" class="btn btn-secondary" v-on:click="salir">
                                    <i class="fa fa-angle-left"></i>Regresar
                                </button>
                                <button type="button" class="btn btn-primary float-right" style="margin-left:10px" @click="validate_pagos" v-if="bitacora.length > 0 && resumen.pagables > 0 && $root.can('registrar_pagos_bitacora')">Registrar</button>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "gestion-pago-create",
        data() {
            return {
                bitacora:[],
                dispersiones:[],
                id_dispersion:'',
                resumen:[],
                cargando: false,
                file_interbancario : null,
                file_interbancario_name : ''
            }
        },
        mounted() {
            this.getDispersiones();
        },
        computed: {
        },
        methods: {
            cargarBitacora(){
                this.cargando = true;
                var formData = new FormData();
                formData.append('bitacora',  this.file_interbancario);
                formData.append('bitacora_nombre',  this.file_interbancario_name);
                formData.append('id_dispersion',  this.id_dispersion);
                return this.$store.dispatch('finanzas/gestion-pago/cargarBitacora',
                    {
                        data: formData,
                        config: {
                            params: { _method: 'POST'}
                        }
                    })
                    .then(data => {
                        if(data.data.length > 0){
                            this.bitacora = data.data;
                            this.resumen = data.resumen;
                        }else{
                            if(this.$refs.carga_bitacora.value !== ''){
                                this.$refs.carga_bitacora.value = '';
                                this.file_interbancario = null;
                            }
                            this.bitacora = [];
                            swal('Cargar Bitácora', 'Archivo de bitácora sin pagos válidos.', 'warning')
                        }
                    }).finally(() => {
                        this.cargando = false;
                    });
            },

            createImage(file, tipo) {
                var reader = new FileReader();
                var vm = this;

                reader.onload = (e) => {
                    vm.file_interbancario = e.target.result;
                };
                reader.readAsDataURL(file);

            },
            limpiar(){
                this.bitacora = [];
                this.resumen = [];
                this.id_dispersion = '';
                this.$refs.carga_bitacora.value = '';
                this.$refs.dispersion.value = '';
                this.file_interbancario = null;
            },

            onFileChange(e){
                this.file_interbancario = null;
                var files = e.target.files || e.dataTransfer.files;
                if (!files.length)
                    return;
                if(e.target.id == 'carga_bitacora') {
                    this.file_interbancario_name = files[0].name;
                    this.createImage(files[0]);
                }
                // setTimeout(() => {
                //     this.validate()
                // }, 500);
            },

            salir(){
                return this.$store.dispatch('finanzas/gestion-pago/salir')
                    .then(() => {
                        this.$router.push({name: 'pago'});
                    });
            },

            store() {
                return this.$store.dispatch('finanzas/gestion-pago/store', this.$data)
                    .then((data) => {
                        this.$router.push({name: 'pago'});
                    });
            },

            validate() {
                this.$validator.validate().then(result => {
                    if (result){
                        this.cargarBitacora();
                    }
                });
            },
            validate_pagos() {
                this.$validator.validate().then(result => {
                    if (result){
                        this.store();
                    }
                });
            },
            getDispersiones(){
                this.cargando = true;
                let self = this
                return self.$store.dispatch('finanzas/distribuir-recurso-remesa/paginate', {
                    params: {
                        include: 'remesa_liberada',
                        scope: 'pendientes',
                        sort: 'id',
                        order: 'DESC'
                    }
                })
                    .then(data => {
                        // console.log(data);
                        this.dispersiones = data.data;
                    })
                    .finally(() => {
                        this.cargando = false;
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
                        // this.remesas = data.data;
                    })
                    .finally(() => {
                        // this.cargando = false;
                    });
            },


            getDocumentos(){

                // this.documentos = null;
                // this.original = null;
                // this.monto_distribuido_anteriormente = 0;
                // this.monto_total_remesa = 0;
                // this.cargando = true;
                // let self = this
                // return self.$store.dispatch('finanzas/remesa/find',{
                //     id: self.id_remesa,
                //     params: {
                //         include: ['documentosDisponibles', 'documentosDisponibles.empresa.cuentasBancariasProveedor.banco', 'documentosDisponibles.moneda', 'remesaLiberada', 'documentosDisponibles.fondo.empresa.cuentasBancariasProveedor.banco']
                //     }
                // })
                //     .then(data => {
                //         if(data.documentosDisponibles.data != 0) {
                //             this.documentos = [];
                //             this.original = [];
                //             this.monto_distribuido_anteriormente = data.remesaLiberada.monto_distribuido;
                //             this.monto_total_remesa = data.remesaLiberada.monto_total_remesa;
                //             this.original = JSON.parse(JSON.stringify(data.documentosDisponibles.data));
                //             this.documentos = JSON.parse(JSON.stringify(data.documentosDisponibles.data));
                //         }else{
                //             swal("La remesa seleccionada no tiene documentos disponibles para generar una distribución de recursos.", {
                //                 icon: "warning",
                //                 buttons: {
                //                     confirm: {
                //                         text: 'Aceptar'
                //                     }
                //                 }
                //             })
                //         }
                //     })
                //     .finally(() => {
                //         this.cargando = false;
                //         this.getCuentaCargo();
                //     });
            },



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
