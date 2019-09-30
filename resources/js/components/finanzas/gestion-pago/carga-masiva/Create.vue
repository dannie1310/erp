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
                                     <label for="carga_layout" class="col-lg-12 col-form-label">Cargar Layout</label>
                                    <div class="col-lg-12">
                                        <input type="file" class="form-control" id="carga_layout"
                                               @change="onFileChange"
                                               row="3"
                                               v-validate="{ ext: ['csv']}"
                                               name="carga_layout"
                                               data-vv-as="Layout"
                                               ref="carga_layout"
                                               :class="{'is-invalid': errors.has('carga_layout')}"
                                        >
                                        <div class="invalid-feedback" v-show="errors.has('carga_layout')">{{ errors.first('carga_layout') }} (txt)</div>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="row" >
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
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr v-for="(pago, i) in pagos">
                                                                <td>{{i+1}}</td>
                                                                <td>{{pago.referencia_factura}}</td>
                                                                <td>{{pago.monto_factura}}</td>
                                                                <td>{{pago.moneda_factura}}</td>
                                                                <td>{{pago.beneficiario}}</td>
                                                                <td>
                                                                      <select
                                                                              class="form-control"
                                                                              :name="`id_cuenta_cargo[${i}]`"
                                                                              v-model="pago.cuenta_cargo.id"
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
                                                                <td>{{pago.fecha_pago}}</td>
                                                                <td>{{pago.referencia_pago}}</td>
                                                                <td>{{pago.tipo_cambio}}</td>
                                                                <td>{{pago.monto_pagado}}</td>
                                                                <td>{{pago.pago_a_generar}}</td>
                                                               <td class="text-center">
                                                                    <small class="badge" :class="{'badge-danger': pago.estado.estado == 0, 'badge-warning': pago.estado.estado != 1,  'badge-success': pago.estado.estado == 1}">
                                                                        {{ pago.estado.descripcion }}
                                                                    </small>
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
                            <button type="button" class="btn btn-secondary" @click="salir">
                                <span v-if="cargando">
                                    <i class="fa fa-spin fa-spinner"></i>
                                </span>
                                <span v-else>
                                    Cerrar
                                </span>
                            </button>
                            <button type="button" class="btn btn-primary" @click="store">Registrar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "carga-masiva-create",
        data() {
            return {
                cargando: false,
                pagos:[],
                resumen:[],
                file_pagos : null
            }
        },
        methods: {
            cargarLayout(){
                this.cargando = true;
                var formData = new FormData();
                formData.append('pagos',  this.file_pagos);
                return this.$store.dispatch('finanzas/carga-masiva-pago/cargarLayout',
                    {
                        data: formData,
                        config: {
                            params: { _method: 'POST'}
                        }
                    })
                    .then(data => {
                        console.log(data.resumen);
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
                       // this.$validator.errors.clear();
                       // this.bitacora = [];
                       // swal('¡Error!', 'Archivo de bitácora no válido.', 'warning')
                    }
                });
            },
        }
    }
</script>

<style scoped>

</style>