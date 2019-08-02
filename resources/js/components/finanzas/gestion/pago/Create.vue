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
                                <div class="col-md-8">
                                     <label for="carga_bitacora" class="col-lg-12 col-form-label">Cargar Bitácora</label>
                                    <div class="col-lg-12">
                                        <input type="file" class="form-control" id="carga_bitacora"
                                               @change="onFileChange"
                                               row="3"
                                               v-validate="{ ext: ['txt']}"
                                               name="carga_bitacora"
                                               data-vv-as="Bitácora"
                                               ref="carga_bitacora"
                                               :class="{'is-invalid': errors.has('carga_bitacora')}"
                                        >
                                        <div class="invalid-feedback" v-show="errors.has('carga_bitacora')">{{ errors.first('carga_bitacora') }} (txt)</div>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="row" v-if="bitacora.length > 0">
                                <div class="col-12">
                                    <div class="invoice p-3 mb-3">
                                        <div class="row">
                                            <div class="col-9">
                                                <h3>Pagos</h3>
                                            </div>
                                            <div class="col-3">
                                                <h6 align="right">Total: {{bitacora.length}}</h6>
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
                                                            <th>Origen</th>
                                                            <th>Tipo de Transacción Pagada</th>
                                                            <th>Referencia de Transacción Pagada</th>
                                                            <th>Tipo de Pago a Generar</th>
                                                            <th></th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr v-for="(pago, i) in bitacora">
                                                                <td>{{i+1}}</td>
                                                                <td>{{pago.concepto.substr(0,30)}}</td>
                                                                <td>{{pago.beneficiario}}</td>
                                                                <td class="text-right">$ {{parseFloat(pago.monto).formatMoney(2, '.', ',')}}</td>
                                                                <td>{{pago.cuenta_cargo.numero}} ({{pago.cuenta_cargo.abreviatura}})</td>
                                                                <td>{{pago.cuenta_abono.numero}} ({{pago.cuenta_abono.abreviatura}})</td>
                                                                <td class="text-center">
                                                                    <small class="badge" :class="{'badge-warning': pago.estado.estado != 1,  'badge-success': pago.estado.estado == 1}">
                                                                        {{ pago.estado.descripcion }}
                                                                    </small>
                                                                </td>
                                                                <td>{{pago.origen_docto}}</td>
                                                                <td>{{pago.id_transaccion_tipo}}</td>
                                                                <td>{{pago.referencia_docto}}</td>
                                                                <td>{{pago.pago_a_generar}}</td>
                                                                <td>
                                                                    <span v-if="pago.pagable">
                                                                        <i class="fa fa-check"></i>
                                                                    </span>
                                                                    <span>

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
                            <button type="button" class="btn btn-secondary" @click="salir">Cerrar</button>
                            <button type="button" class="btn btn-primary" @click="validate" v-if="bitacora.length === 0">Cargar</button>
                            <button type="button" class="btn btn-primary" @click="validate" v-else>Registrar</button>
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
                if(e.target.id == 'carga_bitacora') {
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
                        if(this.$refs.carga_bitacora.value === ''){
                            swal('¡Error!', 'Seleccione un archivo.', 'warning')
                        }else{
                            this.cargarBitacora()
                        }
                        //this.cargarLayout()
                    }else{
                        if(this.$refs.carga_bitacora.value !== ''){
                            this.$refs.carga_bitacora.value = '';
                            this.file_interbancario = null;
                        }
                        this.$validator.errors.clear();
                        swal('¡Error!', 'Archivo de entrada no válido.', 'warning')
                    }
                });
            },
            cargarBitacora(){
                var formData = new FormData();
                formData.append('bitacora',  this.file_interbancario);
                return this.$store.dispatch('finanzas/gestion-pago/cargarBitacora',
                    {
                        data: formData,
                        config: {
                            params: { _method: 'POST'}
                        }
                    })
                    .then(data => {
                         this.bitacora = data;
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
