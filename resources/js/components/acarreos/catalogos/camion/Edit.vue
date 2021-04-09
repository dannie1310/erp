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
                                    <select class="form-control"
                                            name="id_sindicato"
                                            data-vv-as="Sindicato"
                                            v-model="camion.id_sindicato"
                                            v-validate="{required: true}"
                                            :error="errors.has('id_sindicato')"
                                            id="id_sindicato">
                                                <option value>-- Seleccionar--</option>
                                                <option v-for="sindicato in sindicatos" :value="sindicato.id" >{{ sindicato.descripcion}}</option>
                                    </select>
                                    <div class="invalid-feedback" v-show="errors.has('id_sindicato')">{{ errors.first('id_sindicato') }}</div>
                                </div>
                                <label class="col-md-1 col-form-label">Empresa:</label>
                                <div class="col-md-5">
                                    <select class="form-control"
                                              name="id_empresa"
                                              data-vv-as="Empresa"
                                              v-model="camion.id_empresa"
                                              v-validate="{required: true}"
                                              :error="errors.has('id_empresa')"
                                              id="id_empresa">
                                                <option value>-- Seleccionar--</option>
                                                <option v-for="empresa in empresas" :value="empresa.id" >{{ empresa.razon_social}}</option>
                                    </select>
                                    <div class="invalid-feedback" v-show="errors.has('id_empresa')">{{ errors.first('id_empresa') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-md-1 col-form-label">Propietario:</label>
                                <div class="col-md-5">
                                    <input type="text"
                                           name="propietario"
                                           v-validate="{required: true}"
                                           id="propietario"
                                           class="form-control"
                                           :class="{'is-invalid': errors.has('propietario')}"
                                           v-model="camion.propietario" />
                                    <div class="invalid-feedback" v-show="errors.has('propietario')">{{ errors.first('propietario') }}</div>
                                </div>
                                <label class="col-md-1 col-form-label">Operador:</label>
                                <div class="col-md-5">
                                    <select class="form-control"
                                            name="id_operador"
                                            data-vv-as="Operador"
                                            v-model="camion.id_operador"
                                            v-validate="{required: true}"
                                            :error="errors.has('id_operador')"
                                            id="id_operador">
                                                <option value>-- Seleccionar--</option>
                                                <option v-for="operador in operadores" :value="operador.id" >{{ operador.nombre}}</option>
                                    </select>
                                    <div class="invalid-feedback" v-show="errors.has('id_operador')">{{ errors.first('id_operador') }}</div>
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
                                    <input type="text"
                                           name="placa_caja"
                                           id="placa_caja"
                                           v-validate="{required: true}"
                                           class="form-control"
                                           v-model="camion.placa_caja"
                                           :class="{'is-invalid': errors.has('placa_caja')}" />
                                    <div class="invalid-feedback" v-show="errors.has('placa_caja')">{{ errors.first('placa_caja') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-md-1 col-form-label">Marca:</label>
                                <div class="col-md-4">
                                    <select class="form-control"
                                            name="id_marca"
                                            data-vv-as="Operador"
                                            v-model="camion.id_marca"
                                            v-validate="{required: true}"
                                            :error="errors.has('id_marca')"
                                            id="id_marca">
                                                <option value>-- Seleccionar--</option>
                                                <option v-for="marca in marcas" :value="marca.id" >{{ marca.descripcion}}</option>
                                    </select>
                                    <div class="invalid-feedback" v-show="errors.has('id_marca')">{{ errors.first('id_marca') }}</div>
                                </div>
                                <label class="col-md-1 col-form-label">Modelo:</label>
                                <div class="col-md-4">
                                    <input disabled="true"
                                           type="text"
                                           name="modelo"
                                           v-validate="{required: true}"
                                           :class="{'is-invalid': errors.has('modelo')}"
                                           id="modelo"
                                           class="form-control"
                                           v-model="camion.modelo" />
                                    <div class="invalid-feedback" v-show="errors.has('modelo')">{{ errors.first('modelo') }}</div>
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
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6>Frente:
                                                <button type="button" @click="eliminarImagen(1)" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div v-if="bandera_frente">
                                                <img :src="imagenes['frente']" width="200" height="200">
                                            </div>
                                            <div v-else>
                                                <vue-dropzone ref="imagen_frente" id = "imagen_frente" :options="dropzoneOptions"/>
                                            </div>
                                            <hr>
                                            <button @click="eliminarImagen(1)" type="button" class="btn btn-sm btn-default float-right">
                                                <i class="fa fa-trash"></i> Eliminar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6>Derecha:
                                            <button type="button" @click="eliminarImagen(2)" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div v-if="bandera_derecha">
                                                <img :src="imagenes['derecha']" width="250" height="250">
                                            </div>
                                            <div v-else>
                                                <vue-dropzone ref="imagen_derecha"
                                                              id = "imagen_derecha"
                                                              :options="dropzoneOptions"/>
                                            </div>
                                            <hr>
                                            <button @click="eliminarImagen(2)" type="button" class="btn btn-sm btn-default float-right">
                                                <i class="fa fa-trash"></i> Eliminar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6>Atras:
                                                <button type="button" @click="eliminarImagen(3)" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div v-if="bandera_atras">
                                                <img :src="imagenes['atras']" width="200" height="200">
                                            </div>
                                            <div v-else>
                                                <vue-dropzone ref="imagen_atras" id = "imagen_atras" :options="dropzoneOptions"/>
                                            </div>
                                            <hr>
                                            <button @click="eliminarImagen(3)" type="button" class="btn btn-sm btn-default float-right">
                                                <i class="fa fa-trash"></i> Eliminar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6>Izquierda:
                                                <button type="button" @click="eliminarImagen(4)" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div v-if="bandera_izquierda">
                                                <img :src="imagenes['izquierda']" width="200" height="200">
                                            </div>
                                            <div v-else>
                                                <vue-dropzone ref="imagen_izquierda" id = "imagen_izquierda" :options="dropzoneOptions"/>
                                            </div>
                                            <hr>
                                            <button @click="eliminarImagen(4)" type="button" class="btn btn-sm btn-default float-right">
                                                <i class="fa fa-trash"></i> Eliminar
                                            </button>
                                        </div>
                                    </div>
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
                                    <input type="text"
                                           id="aseguradora"
                                           name="aseguradora"
                                           class="form-control"
                                           v-validate="{required: true}"
                                           :class="{'is-invalid': errors.has('aseguradora')}"
                                           v-model="camion.aseguradora" />
                                    <div class="invalid-feedback" v-show="errors.has('aseguradora')">{{ errors.first('aseguradora') }}</div>
                                </div>
                                <label class="col-md-1 col-form-label">Poliza:</label>
                                <div class="col-md-3">
                                    <input type="text"
                                           name="poliza_seguro"
                                           id="poliza_seguro"
                                           v-validate="{required: true}"
                                           :class="{'is-invalid': errors.has('poliza_seguro')}"
                                           class="form-control"
                                           v-model="camion.poliza_seguro" />
                                    <div class="invalid-feedback" v-show="errors.has('poliza_seguro')">{{ errors.first('poliza_seguro') }}</div>
                                </div>
                                <label class="col-md-1 col-form-label">Vigencia:</label>
                                <div class="col-md-3">
                                    <datepicker v-model = "camion.vigencia_poliza"
                                                name = "vigencia_poliza"
                                                :format = "formatoFecha"
                                                :language = "es"
                                                :bootstrap-styling = "true"
                                                class = "form-control"
                                                v-validate="{required: true}"
                                                :disabled-dates="fechasDeshabilitadas"
                                                :class="{'is-invalid': errors.has('vigencia_poliza')}"/>
                                    <div class="invalid-feedback" v-show="errors.has('vigencia_poliza')">{{ errors.first('vigencia_poliza') }}</div>
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
                                    <input type="number"
                                           min="0"
                                           step="1"
                                           class="form-control"
                                           :name="ancho"
                                           data-vv-as="Ancho"
                                           v-validate="{min_value:0}"
                                           :class="{'is-invalid': errors.has('ancho')}"
                                           v-model="ancho"/>
                                    <div class="invalid-feedback" v-show="errors.has('ancho')">{{ errors.first('ancho') }}</div>
                                </div>
                                <label class="col-md-1 col-form-label">Largo:</label>
                                <div class="col-md-2">
                                    <input type="number"
                                           min="0"
                                           step="1"
                                           class="form-control"
                                           :name="largo"
                                           data-vv-as="Largo"
                                           v-validate="{min_value:0}"
                                           :class="{'is-invalid': errors.has('largo')}"
                                           v-model="largo"/>
                                    <div class="invalid-feedback" v-show="errors.has('largo')">{{ errors.first('largo') }}</div>
                                </div>
                                <label class="col-md-1 col-form-label">Alto:</label>
                                <div class="col-md-2">
                                    <input type="number"
                                           min="0"
                                           step="1"
                                           class="form-control"
                                           :name="alto"
                                           data-vv-as="Alto"
                                           v-validate="{min_value:0}"
                                           :class="{'is-invalid': errors.has('alto')}"
                                           v-model="alto"/>
                                    <div class="invalid-feedback" v-show="errors.has('alto')">{{ errors.first('alto') }}</div>
                                </div>
                                <label class="col-md-1 col-form-label">Gato:</label>
                                <div class="col-md-2">
                                    <input type="number"
                                           min="0"
                                           step="1"
                                           class="form-control"
                                           :name="espacio_gato"
                                           data-vv-as="Espacio de gato"
                                           v-validate="{min_value:0}"
                                           :class="{'is-invalid': errors.has('espacio_gato')}"
                                           v-model="espacio_gato"/>
                                    <div class="invalid-feedback" v-show="errors.has('espacio_gato')">{{ errors.first('espacio_gato') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-md-1 col-form-label">Disminución:</label>
                                <div class="col-md-2">
                                    <input type="number"
                                           min="0"
                                           step="1"
                                           class="form-control"
                                           :name="disminucion"
                                           data-vv-as="Ancho"
                                           v-validate="{min_value:0}"
                                           :class="{'is-invalid': errors.has('disminucion')}"
                                           v-model="disminucion"/>
                                    <div class="invalid-feedback" v-show="errors.has('disminucion')">{{ errors.first('disminucion') }}</div>
                                </div>
                                <label class="col-md-1 col-form-label">Extensión:</label>
                                <div class="col-md-2">
                                    <input type="number"
                                           min="0"
                                           step="1"
                                           class="form-control"
                                           :name="altura_extension"
                                           data-vv-as="Altura Extensión"
                                           v-validate="{min_value:0}"
                                           :class="{'is-invalid': errors.has('altura_extension')}"
                                           v-model="altura_extension"/>
                                    <div class="invalid-feedback" v-show="errors.has('altura_extension')">{{ errors.first('altura_extension') }}</div>
                                </div>
                                <label class="col-md-1 col-form-label">Cubicación Real:</label>
                                <div class="col-md-2">
                                    <input type="number"
                                           min="0"
                                           step="0.01"
                                           disabled="true"
                                           class="form-control"
                                           :name="cubicacion_real"
                                           data-vv-as="Cubicación Real"
                                           v-model="cubicacion_real"/>
                                </div>
                                <label class="col-md-1 col-form-label">Cubicación para Pago:</label>
                                <div class="col-md-2">
                                    <input disabled="true"
                                        type="number"
                                        min="0"
                                        step="1"
                                        class="form-control"
                                        :name="cubicacion_pago"
                                        data-vv-as="Cubicación para pago"
                                        v-validate="{min_value:0}"
                                        :class="{'is-invalid': errors.has('cubicacion_pago')}"
                                        v-model="cubicacion_pago"/>
                                    <div class="invalid-feedback" v-show="errors.has('cubicacion_pago')">{{ errors.first('cubicacion_pago') }}</div>
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
    import Datepicker from 'vuejs-datepicker';
    import {es} from 'vuejs-datepicker/dist/locale';
    export default {
        name: "camion-edit",
        props: ['id'],
        components: {vueDropzone: vue2Dropzone, Datepicker, es},
        data() {
            return {
                es:es,
                cargando: true,
                imagenes: {},
                bandera_frente: false,
                bandera_derecha: false,
                bandera_atras: false,
                bandera_izquierda: false,
                camion: null,
                sindicatos: [],
                empresas: [],
                marcas: [],
                operadores: [],
                fechasDeshabilitadas:{},
                fecha_requisicion : '',
                fecha_hoy : '',
                ancho : 0,
                largo : 0,
                alto : 0,
                espacio_gato : 0,
                altura_extension : 0,
                disminucion : 0,
                cubicacion_real : 0,
                cubicacion_pago : 0,
                dropzoneOptions: {
                    url: 'prueba',
                    chunking: false,
                    uploadMultiple: false,
                    //  autoProcessQueue:false,
                    autoQueue: false,
                    thumbnailWidth: 200,
                    thumbnailHeight: 200,
                    paramName: "prueba",
                    maxFiles: 1,
                    aceptedFiles: "image/*",
                    dictDefaultMessage: "<i class='fa fa-cloud-upload'></i><b>Selecciona ó arrastra</b> una imagen",
                    dictInvalidFileType: "Archivo Erroneo",
                    dictMaxFilesExceeded: "Solo se permite un archivo"
                },
            }
        },
        mounted() {
            this.$validator.reset()
            this.fecha_hoy = new Date();
            this.fechasDeshabilitadas.to = new Date();
            this.find();
        },
        methods: {
            formatoFecha(date){
                return moment(date).format('DD/MM/YYYY');
            },
            salir() {
                this.$router.push({name: 'camion'});
            },
            find() {
                return this.$store.dispatch('acarreos/camion/find', {
                    id: this.id,
                    params: {include: 'imagenes'}
                }).then(data => {
                    this.inicializarImagenes(data.imagenes.data)
                    this.camion = data
                    this.getEmpresas()
                    this.getMarcas()
                    this.getOperadores()
                    this.getSindicatos()
                    this.ancho = data.ancho
                    this.largo = data.largo
                    this.alto = data.alto
                    this.espacio_gato = data.espacio_gato
                    this.altura_extension = data.altura_extension
                    this.disminucion = data.disminucion
                    this.cubicacion_real = data.cubicacion_real
                    this.cubicacion_pago = data.cubicacion_pago
                }).finally(() => {
                    this.cargando = false;
                })
            },
            inicializarImagenes(imagenes) {
                for (let imagen of imagenes) {
                    switch (imagen.tipo) {
                        case 't':
                            this.bandera_atras = true
                            this.imagenes['atras'] = imagen.imagen
                            this.imagenes['tipo_atras'] = imagen.tipo
                            this.imagenes['id_atras'] = imagen.id
                            break;

                        case 'f':
                            this.bandera_frente = true
                            this.imagenes['frente'] = imagen.imagen
                            this.imagenes['tipo_frente'] = imagen.tipo
                            this.imagenes['id_frente'] = imagen.id
                            break;

                        case 'd':
                            this.bandera_derecha = true
                            this.imagenes['derecha'] = imagen.imagen
                            this.imagenes['tipo_derecha'] = imagen.tipo
                            this.imagenes['id_derecha'] = imagen.id
                            break;

                        case 'i':
                            this.bandera_izquierda = true
                            this.imagenes['izquierda'] = imagen.imagen
                            this.imagenes['tipo_izquierda'] = imagen.tipo
                            this.imagenes['id_izquierda'] = imagen.id
                            break;
                    }
                }
            },
            obtenerArrayImagenes() {
                if (this.$refs.imagen_frente != undefined && this.$refs.imagen_frente.dropzone.files.length != 0) {
                    this.imagenes['frente'] = this.$refs.imagen_frente.dropzone.files[0]['dataURL']
                    this.imagenes['tipo_frente'] = this.$refs.imagen_frente.dropzone.files[0]['type']
                }
                if (this.$refs.imagen_derecha != undefined && this.$refs.imagen_derecha.dropzone.files.length != 0) {
                    this.imagenes['derecha'] = this.$refs.imagen_derecha.dropzone.files[0]['dataURL']
                    this.imagenes['tipo_derecha'] = this.$refs.imagen_derecha.dropzone.files[0]['type']
                }
                if (this.$refs.imagen_atras != undefined && this.$refs.imagen_atras.dropzone.files.length != 0) {
                    this.imagenes['atras'] = this.$refs.imagen_atras.dropzone.files[0]['dataURL']
                    this.imagenes['tipo_atras'] = this.$refs.imagen_atras.dropzone.files[0]['type']
                }
                if (this.$refs.imagen_izquierda != undefined && this.$refs.imagen_izquierda.dropzone.files.length != 0) {
                    this.imagenes['izquierda'] = this.$refs.imagen_izquierda.dropzone.files[0]['dataURL']
                    this.imagenes['tipo_izquierda'] = this.$refs.imagen_izquierda.dropzone.files[0]['type']
                }
            },
            eliminarImagen(tipo) {
                switch (tipo) {
                    case 1:
                        console.log(this.$refs.imagen_frente, this.imagenes)
                        if (this.$refs.imagen_frente != undefined && this.$refs.imagen_frente.dropzone.files.length != 0) {
                            this.$refs.imagen_frente.removeAllFiles()
                        }
                        if (this.imagenes['id_frente'] != undefined) {
                            this.bandera_frente = false
                            this.imagenes['id_frente'] = ''
                        }
                        this.imagenes['frente'] = ''
                        this.imagenes['tipo_frente'] = ''
                        break;
                    case 2:
                        if (this.$refs.imagen_derecha != undefined && this.$refs.imagen_derecha.dropzone.files.length != 0) {
                            this.$refs.imagen_derecha.removeAllFiles()
                        }
                        if (this.imagenes['id_derecha'] != undefined) {
                            this.bandera_derecha = false
                            this.imagenes['id_derecha'] = ''
                        }
                        this.imagenes['derecha'] = ''
                        this.imagenes['tipo_derecha'] = ''
                        break;

                    case 3:
                        if (this.$refs.imagen_atras != undefined && this.$refs.imagen_atras.dropzone.files.length != 0) {
                            this.$refs.imagen_atras.removeAllFiles()
                        }
                        if (this.imagenes['id_atras'] != undefined) {
                            this.bandera_atras = false
                            this.imagenes['id_atras'] = ''
                        }
                        this.imagenes['atras'] = ''
                        this.imagenes['tipo_atras'] = ''
                        break;

                    case 4:
                        if (this.$refs.imagen_izquierda != undefined && this.$refs.imagen_izquierda.dropzone.files.length != 0) {
                            this.$refs.imagen_izquierda.removeAllFiles()
                        }
                        if (this.imagenes['id_izquierda'] != undefined) {
                            this.bandera_izquierda = false
                            this.imagenes['id_izquierda'] = ''
                        }
                        this.imagenes['izquierda'] = ''
                        this.imagenes['tipo_izquierda'] = ''
                        break;
                }
            },
            getEmpresas() {
                return this.$store.dispatch('acarreos/empresa/index', {
                    params: {sort: 'razonSocial', order: 'asc'}
                })
                    .then(data => {
                        this.empresas = data.data;
                    })
            },
            getMarcas() {
                return this.$store.dispatch('acarreos/marca/index', {
                    params: {sort: 'descripcion', order: 'asc'}
                })
                    .then(data => {
                        this.marcas = data.data;
                    })
            },
            getOperadores() {
                return this.$store.dispatch('acarreos/operador/index', {
                    params: {sort: 'nombre', order: 'asc'}
                })
                    .then(data => {
                        this.operadores = data.data;
                    })
            },
            getSindicatos() {
                return this.$store.dispatch('acarreos/sindicato/index', {
                    params: {sort: 'descripcion', order: 'asc'}
                })
                    .then(data => {
                        this.sindicatos = data.data;
                    })
            },
            validate() {
                this.obtenerArrayImagenes();
                this.$validator.validate().then(result => {
                    if (result) {

                    }
                });
            },
            calculaCubicacion(){
                this.cubicacion_pago = this.ancho * this.largo * this.alto - this.espacio_gato - this.disminucion + this.altura_extension
            }
        },
        watch: {
            ancho(value) {
                if(value){
                    this.ancho = parseInt(value)
                    this.calculaCubicacion();
                }
            },
            largo(value) {
                if(value){
                    this.largo = parseInt(value)
                    this.calculaCubicacion();
                }
            },
            alto(value) {
                if(value){
                    this.alto = parseInt(value)
                    this.calculaCubicacion();
                }
            },
            espacio_gato(value) {
                if(value){
                    this.espacio_gato = parseInt(value)
                    this.calculaCubicacion();
                }
            },
            disminucion(value) {
                if(value){
                    this.disminucion = parseInt(value)
                    this.calculaCubicacion();
                }
            },
            altura_extension(value) {
                if(value){
                    this.altura_extension = parseInt(value)
                    this.calculaCubicacion();
                }
            },
            cubicacion_pago(value)
            {
                if(value){
                    this.cubicacion_real = parseFloat(value).formatMoney(2,'.',',')
                }
            }
        }
    }
</script>

<style scoped>

</style>
