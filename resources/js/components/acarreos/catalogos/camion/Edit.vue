<template>
    <span>
        <div v-if="cargando">
            <div class="row">
                <div class="col-md-12">
                    <div class="spinner-border text-success" role="status">
                        <span class="sr-only">Cargando...</span>
                    </div>
                </div>
            </div>
        </div>
        <div v-else>
            <div class="row">
                <div class="offset-md-8 col-md-4">
                    <span class="pull-right">
                        <div class="card">
                            <div class="card-header">
                                <h5>Camión: <b>{{camion.economico}} [{{camion.placa}}]</b></h5>
                            </div>
                        </div>
                    </span>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h5>Información Básica</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-md-1 col-form-label">Sindicato:</label>
                                <div class="col-md-5">
                                    <input disabled="true"
                                           type="text"
                                           id="sindicato"
                                           name="sindicato"
                                           class="form-control"
                                           v-model="camion.sindicato" />
                                </div>
                                <label class="col-md-1 col-form-label">Empresa:</label>
                                <div class="col-md-5">
                                    <input disabled="true"
                                           type="text"
                                           name="empresa"
                                           id="empresa"
                                           class="form-control"
                                           v-model="camion.empresa" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-md-1 col-form-label">Propietario:</label>
                                <div class="col-md-5">
                                    <input disabled="true"
                                           type="text"
                                           name="propietario"
                                           id="propietario"
                                           class="form-control"
                                           v-model="camion.propietario" />
                                </div>
                                <label class="col-md-1 col-form-label">Operador:</label>
                                <div class="col-md-5">
                                    <input disabled="true"
                                           type="text"
                                           id="operador"
                                           name="operador"
                                           class="form-control"
                                           v-model="camion.operador" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-md-1 col-form-label">Económico:</label>
                                <div class="col-md-3">
                                    <input disabled="true"
                                           type="text"
                                           id="economico"
                                           name="economico"
                                           class="form-control"
                                           v-model="camion.economico" />
                                </div>
                                <label class="col-md-1 col-form-label">Placas Camión:</label>
                                <div class="col-md-3">
                                    <input disabled="true"
                                           type="text"
                                           name="placa"
                                           id="placa"
                                           class="form-control"
                                           v-model="camion.placa" />
                                </div>
                                <label class="col-md-1 col-form-label">Placas Caja:</label>
                                <div class="col-md-3">
                                    <input disabled="true"
                                           type="text"
                                           name="placa_caja"
                                           id="placa_caja"
                                           class="form-control"
                                           v-model="camion.placa_caja" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-md-1 col-form-label">Marca:</label>
                                <div class="col-md-4">
                                    <input disabled="true"
                                           type="text"
                                           id="marca"
                                           name="marca"
                                           class="form-control"
                                           v-model="camion.marca" />
                                </div>
                                <label class="col-md-1 col-form-label">Modelo:</label>
                                <div class="col-md-4">
                                    <input disabled="true"
                                           type="text"
                                           name="modelo"
                                           id="modelo"
                                           class="form-control"
                                           v-model="camion.modelo" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h5>Información Fotográfica</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                             <div class="form-group row">
                                 <label class="col-md-1 col-form-label">Frente:</label>
                                 <div class="col-md-5">
                                    <!-- <vue-dropzone ref="myVueDropzone"
                                          id = "dropzone_1"
                                          :options="dropzoneOptions"
                                           />
                                           -->
                                 </div>
                                 <label class="col-md-2 col-form-label">Derecha:</label>
                                <div class="col-md-4">
                                    <vue-dropzone ref="imagen_frente"
                                                  id = "imagen_frente"
                                                  v-model="imagen_frente"
                                                  :options="dropzoneOptions"
                                       v-on:vdropzone-sending="sendingEvent"
                                                  v-validate="{required:true}"
                                                  :class="{'is-invalid': errors.has('imagen_frente')}"
                                    />
                                     <div class="invalid-feedback" v-show="errors.has('imagen_frente')">{{ errors.first('imagen_frente') }} <span>ERROR</span></div>
                                </div>
                             </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h5>Información de Seguro</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-md-1 col-form-label">Aseguradora:</label>
                                <div class="col-md-3">
                                    <input disabled="true"
                                           type="text"
                                           id="aseguradora"
                                           name="aseguradora"
                                           class="form-control"
                                           v-model="camion.aseguradora" />
                                </div>
                                <label class="col-md-1 col-form-label">Poliza:</label>
                                <div class="col-md-3">
                                    <input disabled="true"
                                           type="text"
                                           name="poliza_seguro"
                                           id="poliza_seguro"
                                           class="form-control"
                                           v-model="camion.poliza_seguro" />
                                </div>
                                <label class="col-md-1 col-form-label">Vigencia:</label>
                                <div class="col-md-3">
                                    <input disabled="true"
                                           type="text"
                                           name="vigencia_poliza"
                                           id="vigencia_poliza"
                                           class="form-control"
                                           v-model="camion.vigencia_poliza" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h5>Información de Cubicación</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-md-1 col-form-label">Ancho:</label>
                                <div class="col-md-2">
                                    <input disabled="true"
                                           type="text"
                                           id="ancho"
                                           name="ancho"
                                           class="form-control"
                                           v-model="camion.ancho" />
                                </div>
                                <label class="col-md-1 col-form-label">Largo:</label>
                                <div class="col-md-2">
                                    <input disabled="true"
                                           type="text"
                                           name="largo"
                                           id="largo"
                                           class="form-control"
                                           v-model="camion.largo" />
                                </div>
                                <label class="col-md-1 col-form-label">Alto:</label>
                                <div class="col-md-2">
                                    <input disabled="true"
                                           type="text"
                                           name="alto"
                                           id="alto"
                                           class="form-control"
                                           v-model="camion.alto" />
                                </div>
                                <label class="col-md-1 col-form-label">Gato:</label>
                                <div class="col-md-2">
                                    <input disabled="true"
                                           type="text"
                                           name="estapacio_gato"
                                           id="estapacio_gato"
                                           class="form-control"
                                           v-model="camion.estapacio_gato" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                 <div class="modal-footer">
                      <button @click="validate" type="button" class="btn btn-primary" :disabled="errors.count() > 0 || cargando == true">
                          <i class="fa fa-save"></i> Guardar
                      </button>
                     <button type="button" class="btn btn-secondary" @click="salir">Cerrar</button>
                 </div>
            </div>
        </div>
    </span>
</template>

<script>
    import vue2Dropzone from 'vue2-dropzone'
    import 'vue2-dropzone/dist/vue2Dropzone.min.css'
    export default {
        name: "camion-edit",
        props: ['id'],
        components: { vueDropzone: vue2Dropzone },
        data() {
            return {
                cargando : true,
                imagen_frente : '',
                dropzoneOptions: {
                    url: 'prueba',
                    chunking : false,
                    uploadMultiple:false,
                    autoProcessQueue:false,
                    thumbnailWidth: 200,
                    paramName: "prueba",
                    maxFiles : 1,
                    aceptedFiles: "image/*",
                    dictDefaultMessage: "<i class='fa fa-cloud-upload'></i><b>Selecciona ó arrastra una imagen"
                },
            }
        },
        mounted() {
            this.find();
        },
        methods: {
            salir() {
                this.$router.push({name: 'camion'});
            },
            find() {
                this.$store.commit('acarreos/camion/SET_CAMION', null);
                return this.$store.dispatch('acarreos/camion/find', {
                    id: this.id,
                    params: {include: 'imagenes'}
                }).then(data => {
                    this.$store.commit('acarreos/camion/SET_CAMION', data);
                }).finally(() => {
                    this.cargando = false;
                })
            },
            sendingEvent (file, xhr, formData) {
                console.log("BUSCARS", file)
                formData.delete();
                console.log("A", formData)
                formData.append('paramName', 'some value or other');
            },
            validate() {
                this.$refs.imagen_frente.processQueue()
                console.log(this.imagen_frente)
                this.$validator.validate().then(result => {
                    if (result) {
                        console.log(this.$refs)
                        this.$refs.prueba.processQueue()
                       console.log(this.imagen_frente)
                    }
                });
            },
        },
        computed: {
            camion() {
                return this.$store.getters['acarreos/camion/currentCamion']
            }
        }
    }
</script>

<style scoped>

</style>
