<template>
     <span>
    <button @click="init" v-if="$root.can('registrar_sucursal_proveedor')" class="btn btn-primary float-right">
        <i class="fa fa-plus"></i> Registrar
    </button>

        <div class="modal fade" ref="modalCreateSucursal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Registro de Sucursal</h5>
                        <button type="button" class="close" @click="closeModal()" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div class="row">
                                <!-- Descripción -->
                                <div class="col-md-12" >
                                    <div class="form-group error-content">
                                        <label for="descripcion">Descripción</label>
                                        <input type="text" class="form-control"
                                            name="descripcion"
                                            data-vv-as="Descripción"
                                            v-model="descripcion"
                                            v-validate="{required: true}"
                                            :class="{'is-invalid': errors.has('descripcion')}"
                                            id="descripcion"
                                            placeholder="Descripción de la Sucursal">
                                        <div class="invalid-feedback" v-show="errors.has('descripcion')">{{ errors.first('descripcion') }}</div>
                                    </div>
                                </div>
                                    <!--Ubicación -->
                                <div class="col-md-12 mt-2 text-left" >
                                      <label class="text-secondary ">Ubicación </label>
                                       <hr style="color: #0056b2; margin-top:auto;" width="90%" size="10" />
                                </div>
                                <!--Dirección-->
                                <div class="col-md-12" >
                                    <div class="form-group error-content">
                                        <label for="direccion">Dirección</label>
                                        <textarea row="7" class="form-control"
                                            name="direccion"
                                            data-vv-as="Dirección"
                                            v-model="direccion"
                                            id="direccion"
                                            placeholder="Dirección de la Sucursal"></textarea>
                                    </div>
                                </div>
                                <!--Ciudad-->
                                <div class="col-md-12" >
                                    <div class="form-group error-content">
                                        <label for="ciudad">Ciudad</label>
                                        <input type="text" class="form-control"
                                            name="ciudad"
                                            data-vv-as="Descripción"
                                            v-model="ciudad"
                                            id="ciudad"
                                            placeholder="Ciudad">
                                    </div>
                                </div>
                                <!--Código Postal-->
                                <div class="col-md-6" >
                                    <div class="form-group error-content">
                                        <label for="codigo_postal">Código Postal</label>
                                        <input type="text" class="form-control"
                                            name="codigo_postal"
                                            data-vv-as="Descripción"
                                            v-model="codigo_postal"
                                            id="codigo_postal"
                                            placeholder="Código Postal" :maxlength="5">
                                    </div>
                                </div>
                                <!--Estado-->
                                <div class="col-md-6" >
                                    <div class="form-group error-content">
                                        <label for="estado">Estado</label>
                                        <input type="text" class="form-control"
                                            name="estado"
                                            data-vv-as="Estado"
                                            v-model="estado"
                                            id="estado"
                                            placeholder="Estado" >
                                    </div>
                                </div>
                                <!--Teléfonos -->
                                <div class="col-md-12 mt-2 text-left" >
                                      <label class="text-secondary">Teléfonos</label>
                                       <hr style="color: #0056b2; margin-top:auto;" width="90%" size="10" />
                                </div>
                                <!--Voz-->
                                <div class="col-md-6" >
                                    <div class="form-group error-content">
                                        <label for="voz">Voz</label>
                                        <input type="number" class="form-control"
                                            name="voz"
                                            data-vv-as="Voz"
                                            v-model="telefono"
                                            id="voz"
                                            placeholder="Número de Teléfono" maxlength="10">
                                    </div>
                                </div>
                                <!--Fax-->
                                <div class="col-md-6" >
                                    <div class="form-group error-content">
                                        <label for="fax">Fax</label>
                                        <input type="text" class="form-control"
                                            name="fax"
                                            data-vv-as="Fax"
                                            v-model="fax"
                                            id="fax"
                                            placeholder="Número de Fax" >
                                    </div>
                                </div>
                                <!--Movil-->
                                <div class="col-md-6" >
                                    <div class="form-group error-content">
                                        <label for="movil">Móvil</label>
                                        <input type="text" class="form-control"
                                            name="movil"
                                            data-vv-as="Movil"
                                            v-model="telefono_movil"
                                            id="movil"
                                            placeholder="Número de móvil" >
                                    </div>
                                </div>
                                <div class="col-md-6" ></div>
                                <!--Contacto -->
                                <div class="col-md-12 mt-2 text-left" >
                                      <label class="text-secondary">Contacto</label>
                                       <hr style="color: #0056b2; margin-top:auto;" width="90%" size="10" />
                                </div>
                                <!--Contacto-->
                                <div class="col-md-12" >
                                    <div class="form-group error-content">
                                        <label for="contacto">Contacto</label>
                                        <input type="text" class="form-control"
                                            name="contacto"
                                            data-vv-as="Contacto"
                                            v-model="contacto"
                                            id="contacto"
                                            placeholder="Nombre del Responsble" >
                                    </div>
                                </div>
                                <!--Cargo-->
                                <div class="col-md-6" >
                                    <div class="form-group error-content">
                                        <label for="cargo">Cargo</label>
                                        <input type="text" class="form-control"
                                            name="cargo"
                                            data-vv-as="Cargo"
                                            v-model="cargo"
                                            id="cargo"
                                            placeholder="Cargo">
                                    </div>
                                </div>
                                <!--E-Mail-->
                                <div class="col-md-6" >
                                    <div class="form-group error-content">
                                        <label for="email">E-Mail</label>
                                        <input type="text" class="form-control"
                                            name="email"
                                            data-vv-as="E-Mail"
                                            v-model="email"
                                            v-validate="{email}"
                                            :class="{'is-invalid': errors.has('email')}"
                                            id="email"
                                            placeholder="E-Mail" >
                                            <div class="invalid-feedback" v-show="errors.has('email')">{{ errors.first('email') }}</div>
                                    </div>
                                </div>
                                <!--observaciones-->
                                <div class="col-md-12" >
                                    <div class="form-group error-content">
                                        <label for="observaciones">Observaciones</label>
                                        <textarea row="7" class="form-control"
                                            name="observaciones"
                                            data-vv-as="Observaciones"
                                            v-model="observaciones"
                                            id="observaciones"
                                            placeholder="Observaciones"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                           <button type="button" class="btn btn-secondary" @click="closeModal()">Cerrar</button>
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </div>
                    </form>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div>
     </span>
</template>


<script>
    export default {
        name: "create-proveedor-sucursal",
        props: ['id'],
        components: {},
        data() {
            return {

                descripcion:'',
                direccion:'',
                ciudad:'',
                codigo_postal:'',
                estado:'',
                telefono:'',
                fax:'',
                telefono_movil:'',
                cargo:'',
                email:'',
                contacto:'',
                observaciones:'',
            }
        },
        mounted() {
        },
        methods: {
            closeModal(){
                $(this.$refs.modalCreateSucursal).modal('hide');
            },
            init() {
                this.descripcion = '';
                this.direccion = '';
                this.ciudad = '';
                this.codigo_postal = '';
                this.estado = '';
                this.voz = '';
                this.fax = '';
                this.cargo = '';
                this.email = '';
                this.contacto = '';
                this.observaciones = '';
                this.telefono_movil = '';
                this.telefono = '';
                $(this.$refs.modalCreateSucursal).modal('show');
                this.$validator.reset();
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.store()
                    }
                });
            },
            store() {
                this.$data.id_empresa = this.id;
                return this.$store.dispatch('cadeco/proveedor-contratista-sucursal/storeSucursalProveedor',  this.$data )
                .then((data) => {
                    $(this.$refs.modalCreateSucursal).modal('hide');
                    this.$emit('created',data)
                })
            }
        },
        computed: {
        }
    }
</script>
