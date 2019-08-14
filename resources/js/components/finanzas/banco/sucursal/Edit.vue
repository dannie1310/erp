<template>
    <span>
        <button @click="find(id)" v-if="$root.can('editar_sucursal_banco')"class="btn btn-sm btn-outline-info" :disabled="cargando">
            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
            <i class="fa fa-pencil" v-else></i>
        </button>

        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md" role="document">
                <div class="modal-content" v-if="sucursal">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Edición de Sucursal Bancaria</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
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
                                               @input="updateAttribute"
                                               v-validate="{required: true}"
                                               v-model="sucursal.descripcion"
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
                                                          v-model="sucursal.direccion"
                                                          @input="updateAttribute"
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
                                                   v-model="sucursal.ciudad"
                                                   @input="updateAttribute"
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
                                                   v-model="sucursal.codigo_postal"
                                                   @input="updateAttribute"
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
                                                   v-model="sucursal.estado"
                                                   @input="updateAttribute"
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
                                                   v-model="sucursal.voz"
                                                   @input="updateAttribute"
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
                                                   v-model="sucursal.fax"
                                                   @input="updateAttribute"
                                                   id="fax"
                                                   placeholder="Número de Fax" >

                                               </div>
                                        </div>
                         <!--Contacto-->
                                  <div class="col-md-9" >
                                               <div class="form-group error-content">
                                             <label for="contacto">Contacto</label>
                                            <input type="text" class="form-control"
                                                   name="contacto"
                                                   data-vv-as="Contacto"
                                                   :v-model="contacto"
                                                   v-model="sucursal.contacto"
                                                   @input="updateAttribute"
                                                   id="contacto"
                                                   placeholder="Nombre del Responsble" >
                                               </div>
                                        </div>
                     <!--Check Central-->
                                   <div class="col-md-3 mt-5">
                                    <div class="form-check" v-if="sucursal.casa_central === 'S'">
                                        <input type="checkbox"
                                               name="checkCentral"
                                               class="form-check-input"
                                               data-vv-as="Central"
                                               checked="checked"
                                               :value="this.checkCentral==true"
                                               v-model="checkCentral"
                                               @input="updateAttribute"
                                               id="checkCentral"
                                               v-on:click=" ! checkCentral">
                                        <label class="form-check-label" for="checkCentral">Central</label>
                                    </div>

                                             <div class="form-check" v-if="sucursal.casa_central === 'N'">
                                        <input type="checkbox"
                                               name="checkCentral"
                                               class="form-check-input"
                                               data-vv-as="Central"
                                               v-model="checkCentral"
                                               @input="updateAttribute"
                                               id="checkCentral"
                                               v-on:click=" ! checkCentral">
                                        <label class="form-check-label" for="checkCentral">Central</label>
                                    </div>


                                   </div>




                            </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                   </form>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    import SucursalIndex from "../Index";
    export default {
        name: "sucursal-edit",
        components: {SucursalIndex},
        props: ['id'],
        data() {
            return {
                sucursal: null,
                cargando: false,
                checkCentral: false,
                form:[],

            }
        },

        computed: {
            sucursal(){
                return (this.$store.getters['cadeco/sucursal/currentSucursal'] != null && this.$store.getters['cadeco/sucursal/currentSucursal'].id == this.id)? this.$store.getters['cadeco/sucursal/currentSucursal'] :null

            }
        },

        methods: {
            find(id) {
                this.cargando = true;
                return this.$store.dispatch('cadeco/sucursal/find', {
                    id: id
                })
                    .then(data => {
                        this.sucursal = data
                        $(this.$refs.modal).modal('show')
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            },
            update() {

                return this.$store.dispatch('cadeco/sucursal/update', {
                    id: this.sucursal.id,
                    data: {
                            id_sucursal: this.sucursal.id,
                            descripcion : this.sucursal.descripcion,
                            direccion: this.sucursal.direccion,
                            ciudad:this.sucursal.ciudad,
                            codigo_postal: this.sucursal.codigo_postal,
                            estado : this.sucursal.estado,
                            telefono: this.sucursal.voz,
                            fax: this.sucursal.fax,
                            contacto: this.sucursal.contacto,
                            checkCentral: this.checkCentral,

                    },

                })
                    .then(data => {
                        this.$store.commit('cadeco/sucursal/UPDATE_SUCURSAL', data);
                        $(this.$refs.modal).modal('hide');
                    })
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.update()
                    }
                });
            },
            updateAttribute(e) {
                return this.$store.commit('cadeco/sucursal/UPDATE_ATTRIBUTE', {attribute: $(e.target).attr('name'), value: e.target.value})
            }

        }
    }
</script>
