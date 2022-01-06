<template>
    <span>
        <div class="card" v-if="!contrato">
            <div class="card-body">
                <div class="row" >
                    <div class="col-md-12">
                        <div class="spinner-border text-success" role="status">
                           <span class="sr-only">Cargando...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card" v-else>
            <div class="card-body">
                <div class="row" v-if="contrato">
                    <div class="col-md-12">
                        <encabezado-contrato-proyectado v-bind:contrato_proyectado="contrato"></encabezado-contrato-proyectado>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <span><i class="fa fa-envelope"></i>Destinatarios de Invitación</span>
                        <table class="table  table-sm table-bordered">
                            <tr>
                                <th class="encabezado index_corto">
                                    #
                                </th>
                                <th class="encabezado c70">
                                    ¿En Catálogo?
                                </th>
                                <th class="encabezado c350">
                                    Proveedor
                                </th>
                                <th class="encabezado c250" >
                                    Sucursal
                                </th>
                                <th class="encabezado" >
                                    Correo
                                </th>
                                <th class="encabezado" >
                                    Contacto
                                </th>
                                <th class="encabezado icono">
                                    <button type="button" class="btn btn-sm btn-outline-success" @click="agregarDestinatario" :disabled="cargando">
                                        <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
                                        <i class="fa fa-plus" v-else></i>
                                    </button>
                                </th>
                            </tr>

                            <tr v-for="(destinatario, i) in this.destinatarios">
                                <td>{{i+1}}</td>
                                <td style="text-align: center">
                                    <div class="form-check form-check-inline">
                                        <label class="form-check-label" style="cursor:pointer" >
                                            <input class="form-check-input" type="checkbox" name="proveedor_en_catalogo" v-model="destinatario.en_catalogo" value="1" @change="toggleEnCatalogo(destinatario)">
                                        </label>
                                    </div>
                                </td>
                                <td>
                                    <span v-if="destinatario.en_catalogo">
                                        <proveedor-contratista-select-autocomplete
                                            v-if="destinatario.proveedor == null"
                                            :value="destinatario.proveedor"
                                            :key="`id_proveedor_${i}`"
                                            :id="`id_proveedor_${i}`"
                                            :name="`proveedor[${i}]`"
                                            v-model="destinatario.proveedor"
                                            :async="true"
                                            v-validate="{required: true}"
                                            :data-vv-as="`'Proveedor ${i + 1}'`"
                                            :flatten-search-results="true"
                                            :class="{'is-invalid': errors.has(`proveedor[${i}]`)}"
                                            placeholder="Realice la búsqueda por razón social o RFC"

                                        />
                                        <span v-else>
                                            {{ destinatario.proveedor.customLabel }}
                                        </span>
                                         <div style="display:block" class="invalid-feedback" v-show="errors.has(`proveedor[${i}]`)">{{ errors.first(`proveedor[${i}]`) }}</div>
                                    </span>
                                    <span v-else >
                                        <input type="text" value="NO APLICA" disabled="disabled" class="form-control">
                                    </span>

                                </td>
                                <td>
                                    <span v-if="destinatario.en_catalogo">
                                        <select :disabled="destinatario.id_proveedor==''"
                                                class="form-control"
                                                :name="`id_sucursal[${i}]`"
                                                :data-vv-as="`'Sucursal ${i + 1}'`"
                                                v-model="destinatario.id_sucursal"
                                                v-validate="{required: true}"
                                                :error="errors.has(`id_sucursal[${i}]`)"
                                                :id="`'id_sucursal_${i}'`"
                                                @change="cambiaSucursal(destinatario)"
                                                :class="{'is-invalid': errors.has(`id_sucursal[${i}]`)}"
                                        >
                                            <option value >-- Seleccionar--</option>
                                            <option v-for="sucursal in destinatario.sucursales" :value="sucursal.id" >{{ sucursal.descripcion}}</option>
                                        </select>
                                        <div style="display:block" class="invalid-feedback" v-show="errors.has(`id_sucursal[${i}]`)">{{ errors.first(`id_sucursal[${i}]`) }}</div>
                                    </span>
                                    <span v-else>
                                        <input type="text" value="NO APLICA" disabled="disabled" class="form-control">
                                    </span>
                                </td>
                                <td>
                                    <input
                                        :disabled="destinatario.id_sucursal==''"
                                        :name="`correo_${i}`"
                                        :id="`correo_${i}`"
                                        v-model="destinatario.correo"
                                        type="text"
                                        class="form-control"
                                        v-validate="{ required: true, email:true }"
                                        :class="{'is-invalid': errors.has(`correo_${i}`)}"
                                    />
                                    <div style="display:block" class="invalid-feedback" v-show="errors.has(`correo_${i}`)">{{ errors.first(`correo_${i}`) }}</div>
                                </td>
                                <td>
                                    <input
                                        :disabled="destinatario.id_sucursal==''"
                                        :name="`contacto_${i}`"
                                        :id="`contacto_${i}`"
                                        v-model="destinatario.contacto"
                                        type="text"
                                        class="form-control"
                                        v-validate="{ required: true }"
                                        :class="{'is-invalid': errors.has(`contacto_${i}`)}"
                                    />
                                    <div style="display:block" class="invalid-feedback" v-show="errors.has(`contacto_${i}`)">{{ errors.first(`contacto_${i}`) }}</div>
                                </td>
                                <td style="text-align: center">
                                    <button type="button" class="btn btn-sm btn-outline-danger" @click="quitarDestinatario(i)" :disabled="destinatarios.length == 1" >
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </table>

                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                        <label for="fecha_cierre">Fecha de Cierre:</label>
                            <datepicker v-model = "fecha_cierre"
                                        name = "Fecha de Cierre"
                                        id = "fecha_cierre"
                                        :format = "formatoFecha"
                                        :language = "es"
                                        :bootstrap-styling = "true"
                                        class = "form-control"
                                        v-validate="{required: true}"
                                        :disabled-dates="fechasDeshabilitadas"
                                        :class="{'is-invalid': errors.has('fecha_cierre')}"
                            />
                            <div style="display:block" class="invalid-feedback" v-show="errors.has('fecha_cierre')">{{ errors.first('fecha_cierre') }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                        <label for="observaciones">Observaciones:</label>
                            <textarea
                                name="observaciones"
                                id="observaciones"
                                v-model="observaciones"
                                type="text"
                                class="form-control"
                            />
                            <div style="display:block" class="invalid-feedback" v-show="errors.has('observaciones')">{{ errors.first('observaciones') }}</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                        <label for="direccion_entrega">Dirección:</label>
                            <textarea
                                name="direccion_entrega"
                                id="direccion_entrega"
                                v-model="direccion_entrega"
                                type="text"
                                class="form-control"
                                v-validate="{required:true}"
                            />
                            <div style="display:block" class="invalid-feedback" v-show="errors.has('direccion_entrega')">{{ errors.first('direccion_entrega') }}</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                        <label for="ubicacion_entrega_plataforma_digital">Ubicación en Plataforma Digital:</label>
                            <input
                                name="ubicacion_entrega_plataforma_digital"
                                id="ubicacion_entrega_plataforma_digital"
                                v-model="ubicacion_entrega_plataforma_digital"
                                type="text"
                                class="form-control"
                                placeholder="https://goo.gl/maps/yrVG5u7RwdUJFgU47"
                                data-vv-as="Ubicación de Entrega en Plataforma Digital"
                                v-validate="{required:true, regex:/^http[s]?:\/\/[\w]+([\.]+[\w]+)\/maps\/[a-zA-Z]/}"
                            >
                            <div style="display:block" class="invalid-feedback" v-show="errors.has('ubicacion_entrega_plataforma_digital')">{{ errors.first('ubicacion_entrega_plataforma_digital') }}</div>
                        </div>
                    </div>
                </div>

                <div class="row" v-if="contrato">
                    <div class="col-md-12">
                        <ckeditor v-model="cuerpo_correo" ></ckeditor>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-12">
                        <div >
                            <div>
                                <label class="col-form-label"><span><i class="fa fa-files-o"></i>Archivos Adjuntos</span></label>
                            </div>
                            <div>
                                <div class="form-group error-content" >
                                    <input type="file" class="form-control" id="archivo" @change="onFileChange" multiple="multiple"
                                           row="3"
                                           v-validate="{ }"
                                           name="archivos"
                                           data-vv-as="Archivos a Enviar"
                                           ref="archivos"
                                           :class="{'is-invalid': errors.has('archivos')}"
                                    >
                                    <div class="invalid-feedback" v-show="errors.has('archivos')">{{ errors.first('archivos') }}</div>
                                </div>
                            </div>
                        </div>


                        <table class="table  table-sm table-bordered" v-if="names.length>0">
                            <tr>
                                <th class="encabezado index_corto">
                                    #
                                </th>

                                <th class="encabezado">
                                    Nombre de Archivo
                                </th>
                                <th class="encabezado c300" >
                                    Tipo
                                </th>
                                <th class="encabezado c250" >
                                    Observaciones
                                </th>
                                <th class="encabezado icono">
                                    <button type="button" class="btn btn-sm btn-outline-success" @click="agregarArchivo" :disabled="cargando">
                                        <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
                                        <i class="fa fa-plus" v-else></i>
                                    </button>
                                </th>
                            </tr>

                            <tr v-for="(archivo, i) in this.archivos">
                                <td>{{i+1}}</td>

                                <td>
                                    {{archivo.nombre}}
                                </td>
                                <td>
                                    <model-list-select
                                        :id="`tipo_archivo_${i}`"
                                        :name="`tipo_archivo_${i}`"
                                        option-value="id"
                                        option-text="descripcion"
                                        v-model="archivo.tipo"
                                        v-validate="{required: true}"
                                        :list="tipos_archivo_enviar"
                                        :isError="errors.has(`tipo_archivo_${i}`)">
                                        :placeholder="!cargando?'Seleccionar tipo de archivo':'Cargando...'">
                                    </model-list-select>

                                </td>
                                <td>
                                    <textarea
                                        :id="`observaciones_${i}`"
                                        :name="`observaciones_${i}`"
                                        class="form-control"
                                        v-model="archivo.observaciones"
                                        v-validate="{required: (archivo.tipo == 14) ? true:false}"
                                        :data-vv-as="`Observaciones ${i+1}`"
                                        :class="{'is-invalid': errors.has(`observaciones_${i}`)}"
                                    ></textarea>
                                </td>
                                <td style="text-align: center">
                                    <button type="button" class="btn btn-sm btn-outline-danger" @click="quitarDestinatario(i)" :disabled="destinatarios.length == 1" >
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </table>

                    </div>
                </div>

                <br>

                <div class="row" v-if="contrato">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="carta_terminos">Carta de Términos y Condiciones:</label>
                            <input type="file" class="form-control" id="carta_terminos"
                               @change="onFileChange"
                               v-validate="{required:true, ext: ['pdf'],  size: 102400}"
                               name="carta_terminos"
                               data-vv-as="Carta de Términos y Condiciones"
                               ref="carta_terminos"
                               :class="{'is-invalid': errors.has('carta_terminos')}"
                            >
                            <div class="invalid-feedback" v-show="errors.has('carta_terminos')">{{ errors.first('carta_terminos') }} (pdf)</div>
                        </div>
                    </div>
                     <div class="col-md-6">
                         <div class="form-group">
                            <label for="carta_terminos">Formato de Presupuesto:</label>
                            <input type="file" class="form-control" id="formato_cotizacion"
                                   @change="onFileChange"
                                   v-validate="{ext: ['docx'],  size: 102400}"
                                   name="formato_cotizacion"
                                   data-vv-as="Formato de Cotización"
                                   ref="formato_cotizacion"
                                   :class="{'is-invalid': errors.has('formato_cotizacion')}"
                            >
                            <div class="invalid-feedback" v-show="errors.has('formato_cotizacion')">{{ errors.first('formato_cotizacion') }} (docx)</div>
                         </div>
                    </div>
                 </div>
            </div>
            <div class="card-footer">
                <div class="row" v-if="contrato">
                    <div class="col-md-8">

                    </div>
                    <div class="col-md-4">
                        <div class="pull-right">
                            <button type="button" class="btn btn-secondary" v-on:click="salir"><i class="fa fa-angle-left"></i>
                                Regresar</button>
                            <button type="button" class="btn btn-primary" v-on:click="enviar" :disabled="errors.count() > 0"><i class="fa fa-envelope"></i>
                                Enviar</button>
                        </div>
                    </div>
                </div>

            </div>

        </div>
        <div class="modal fade" ref="modal_usuarios" role="dialog" data-backdrop="static" data-keyboard="false" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"><i class="fa fa-list"></i> Seleccionar proveedores preexistentes</h5>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="callout callout-warning">
                                    <h6><i class="fa fa-info-circle"></i>Atención</h6>
                                    Los correos que ha ingresado tienen coincidencia con proveedores del catálogo, favor de seleccionar alguno.
                                </div>
                            </div>
                        </div>
                        <template v-if="usuarios.length>0" v-for="(usuario, i) in this.usuarios">
                            <div class="card">
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <h6>{{usuario.correo}}</h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row" >
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label :for="`id_usuario_${i}`">Proveedores:</label>
                                                    <model-list-select
                                                        :id="`id_usuario_${i}`"
                                                        :name="`id_usuario_${i}`"
                                                        option-value="idusuario"
                                                        v-model="usuario.id_usuario"
                                                        :custom-text="usuarioNombre"
                                                        :list="usuario.coincidencias"
                                                        :data-vv-as="`Usuario ${i}`"
                                                        v-validate ="{ required: usuario.sin_coincidencia_proveedor == 0 ? true : false}"
                                                        :isError="errors.has(`id_usuario_${i}`)"
                                                        :placeholder="!cargando?'Seleccionar un proveedor preexistente en la el catálogo':'Cargando...'">
                                                     </model-list-select>
                                                <div style="display:block" class="invalid-feedback" v-show="errors.has(`id_usuario_${i}`)">{{ errors.first(`id_usuario_${i}`) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-check form-check-inline">
                                                <label class="form-check-label" style="cursor:pointer" >
                                                    <input class="form-check-input" type="checkbox" name="sin_coincidencia_proveedor"  v-model="usuario.sin_coincidencia_proveedor"
                                                           value="1"
                                                           @change="toggleSinCoincidenciaProveedor(usuario)">
                                                </label>
                                            </div>
                                            <label>No deseo seleccionar un proveedor de la lista</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </template>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="cerrarModal"><i class="fa fa-close"></i>Cerrar</button>
                        <button type="button" class="btn btn-primary" @click="enviar" :disabled="errors.count() > 0"><i class="fa fa-envelope"></i> Enviar</button>
                    </div>
                </div>
             </div>
        </div>
    </span>
</template>

<script>
import {ModelListSelect} from 'vue-search-select';
import Datepicker from 'vuejs-datepicker';
import {es} from 'vuejs-datepicker/dist/locale';

import EncabezadoContratoProyectado from "../proyectado/Encabezado";
import ProveedorContratistaSelectAutocomplete
    from "../../catalogos/empresa/proveedor-contratista/partials/SelectAutocomplete";
export default {
    name: "CreateInvitacionCompra.vue",
    components: {
        ProveedorContratistaSelectAutocomplete,
        EncabezadoContratoProyectado,
         Datepicker,es, ModelListSelect},
    props: ['id_contrato'],
    data(){
        return {
            cargando : false,
            proveedores : [],
            sucursales: [],
            usuarios : [],
            es:es,
            post: {},
            id_proveedor : null,
            id_usuario : null,
            id_sucursal : null,
            proveedor_en_catalogo : 1,
            correo : '',
            contacto : '',
            observaciones:'',
            mas_invitaciones:1,
            fecha_cierre : new Date(),
            direccion_entrega : '',
            ubicacion_entrega_plataforma_digital : '',
            fechasDeshabilitadas: {},
            archivo_carta_terminos_condiciones:'',
            nombre_archivo_carta_terminos_condiciones:'',
            archivo_formato_cotizacion:'',
            nombre_archivo_formato_cotizacion:'',
            cuerpo_correo:'',
            usuarios_cargados : 0,
            sin_coincidencia_proveedor : 0,
            requiere_fichas_tecnicas : 1,
            destinatarios : [
                {
                    'id_proveedor' : '',
                    'id_sucursal' : '',
                    'correo' : '',
                    'contacto' : '',
                    'en_catalogo' : 1,
                    'sucursales' : [],
                    'sucursales_cargadas' : 0,
                    'id_proveedor_seleccionado' : '',
                    'id_sucursal_seleccionada' : '',
                    'proveedor' : null
                }
            ],
            files : [],
            names : [],
            archivos :[],
            tipos_archivo_enviar : [],
            tipos_archivo_enviados : [],
            tipos_archivo_solicitar : []
        }
    },
    mounted() {
        if(!this.contrato)
        {
            this.find();
        }else{
            this.direccion_entrega = this.contrato.direccion_entrega;
            this.ubicacion_entrega_plataforma_digital = this.contrato.ubicacion_entrega_plataforma_digital;
            this.getCuerpoCorreo();
        }

        this.fecha_cierre = new Date();
        this.fechasDeshabilitadas.to = new Date();
    },
    methods:{
        find() {
            this.cargando = true;
            return this.$store.dispatch('contratos/contrato-proyectado/find', {
                id: this.id_contrato,
                params:{include: [
                        ],
                    }
            }).then(data => {
                this.$store.commit('contratos/contrato-proyectado/SET_CONTRATO', data);
                this.direccion_entrega = data.direccion_entrega;
                this.ubicacion_entrega_plataforma_digital = data.ubicacion_entrega_plataforma_digital;
            }).finally(()=>{
                this.getCuerpoCorreo();
            });
        },
        getCuerpoCorreo(){
            this.cargando = true;
            return this.$store.dispatch('contratos/contrato-proyectado/getCuerpoCorreo', {
                id:this.contrato.id
            })
            .then(data => {
                this.cuerpo_correo = data;
            })
            .finally(()=>{
                this.getTiposArchivoEnviar();
            })
        },
        getTiposArchivoEnviar(){
            this.cargando = true;
            return this.$store.dispatch('contratos/invitacion/getTiposArchivo', {
                params:{
                    tipo : [1,3],
                    area: [2,3]
                }
            })
            .then(data => {
                this.tipos_archivo_enviar = data;
            })
            .finally(()=>{
                this.getTiposArchivoSolicitar();
            })
        },
        getTiposArchivoSolicitar(){
            this.cargando = true;
            return this.$store.dispatch('contratos/invitacion/getTiposArchivo', {
                params:{
                    tipo : [2,3],
                    area: [2,3]
                }
            })
                .then(data => {
                    this.tipos_archivo_solicitar = data;
                })
                .finally(()=>{
                    this.cargando = false;
                })
        },
        agregarDestinatario(){
            var array = {
                'id_proveedor' : '',
                'id_sucursal' : '',
                'correo' : '',
                'contacto' : '',
                'en_catalogo' : 1,
                'sucursales' : [],
                'sucursales_cargadas' : 0,
                'id_proveedor_seleccionado' : '',
                'id_sucursal_seleccionada' : '',
                'proveedor' : null
            }
            this.destinatarios.push(array);
        },
        agregarArchivo(){
            var array = {
                'tipo_archivo' : '',
                'nombre':'',
            }
            this.archivos.push(array);
        },
        quitarDestinatario(index){
            this.destinatarios.splice(index, 1);
        },
        razonSocialRFC (item)
        {
            return `[${item.razon_social}] - [ ${item.rfc} ]`;
        },
        usuarioNombre(item)
        {
            return `[${item.usuario}] - [ ${item.nombre} ${item.apaterno} ${item.amaterno} ]`;
        },
        salir()
        {
            this.$router.push({name: 'invitacion-compra-selecciona-contrato'});
        },
        cerrarModal()
        {
            this.usuarios = [];
            this.usuarios_cargados = 0;
            this.id_usuario = '';
            this.sin_coincidencia_proveedor = 0;
            $(this.$refs.modal_usuarios).modal('hide');
        },
        limpiar()
        {
            this.post.id_transaccion = null;
            this.post.id_proveedor = null;
            this.post.id_usuario = null;
            this.post.id_sucursal = null;
            this.post.observaciones = null;
            this.post.proveedor_en_catalogo = null;
            this.post.correo = null;
            this.post.contacto = null;
            this.post.fecha_cierre = null;
            this.post.direccion_entrega = null;
            this.post.ubicacion_entrega_plataforma_digital = null;
            this.post.archivo_carta_terminos_condiciones = null;
            this.post.nombre_archivo_carta_terminos_condiciones = null;
            this.post.archivo_formato_cotizacion = null;
            this.post.nombre_archivo_formato_cotizacion = null;
            this.post.requerir_fichas_tecnicas = null;


            //this.id_contrato;
            this.id_proveedor = null;
            this.id_sucursal = null;
            this.observaciones = null;

            this.correo = null;
            this.contacto = null;
            this.sin_coincidencia_proveedor = 0;
            this.usuarios = [];
            this.destinatarios = [
                {
                    'id_proveedor' : '',
                    'id_sucursal' : '',
                    'correo' : '',
                    'contacto' : '',
                    'en_catalogo' : 1,
                    'sucursales' : [],
                    'sucursales_cargadas' : 0,
                    'id_proveedor_seleccionado' : '',
                    'id_sucursal_seleccionada' : '',
                    'proveedor' : null
                }
            ];
            this.id_usuario = '';
            this.usuarios_cargados = 0;
            this.requiere_fichas_tecnicas = 1;

            this.archivo_carta_terminos_condiciones = null;
            this.nombre_archivo_carta_terminos_condiciones = null;
            this.archivo_formato_cotizacion = null;
            this.nombre_archivo_formato_cotizacion = null;
            this.$refs.carta_terminos.value = '';
            this.$refs.formato_cotizacion.value = '';
        },
        enviar()
        {
            let _self = this;
            this.$validator.validate().then(result => {
                if (result) {

                    let correos = [];
                    _self.destinatarios.forEach(function (destinatario, i) {
                        if(destinatario.en_catalogo == 0){
                            correos.push(destinatario.correo);
                        }
                    });

                    if (correos.length > 0 && _self.usuarios_cargados == 0) {
                        return this.$store.dispatch('igh/usuario/findPorCorreos', {
                            config: {sort: 'usuario',  order: 'asc'},
                            data: {correos: correos},
                        })
                            .then((data) => {
                                _self.usuarios_cargados = 1;
                                _self.usuarios = data;
                                if (_self.usuarios.length > 0) {
                                    $(this.$refs.modal_usuarios).appendTo('body')
                                    $(this.$refs.modal_usuarios).modal('show');
                                } else {
                                    _self.usuarios_cargados = 0;
                                    this.store();
                                }
                            });

                    }else {
                        this.store();
                    }
                }
            });
        },
        store()
        {
            let _self = this;
            this.$validator.validate().then(result => {
                if (result) {
                    _self.post.id_transaccion = _self.id_contrato;
                    /*_self.post.id_proveedor = _self.id_proveedor;
                    _self.post.id_sucursal = _self.id_sucursal;
                    _self.post.id_usuario = _self.id_usuario;
                    _self.post.proveedor_en_catalogo = _self.proveedor_en_catalogo;
                    _self.post.correo = _self.correo;
                    _self.post.contacto = _self.contacto;*/
                    _self.post.observaciones = _self.observaciones;//
                    _self.post.fecha_cierre = _self.fecha_cierre;//
                    _self.post.direccion_entrega = _self.direccion_entrega;//
                    _self.post.ubicacion_entrega_plataforma_digital = _self.ubicacion_entrega_plataforma_digital;//
                    _self.post.archivo_carta_terminos_condiciones = _self.archivo_carta_terminos_condiciones;//
                    _self.post.nombre_archivo_carta_terminos_condiciones = _self.nombre_archivo_carta_terminos_condiciones;//
                    _self.post.archivo_formato_cotizacion = _self.archivo_formato_cotizacion;//
                    _self.post.nombre_archivo_formato_cotizacion = _self.nombre_archivo_formato_cotizacion;//
                    _self.post.cuerpo_correo = _self.cuerpo_correo;//
                    _self.post.requiere_fichas_tecnicas = _self.requiere_fichas_tecnicas;//
                    _self.post.destinatarios = _self.destinatarios;
                    _self.post.usuarios = _self.usuarios;

                    return this.$store.dispatch('compras/invitacion/store', _self.post)
                        .then((data) => {
                            $(this.$refs.modal_usuarios).modal('hide');
                            this.$router.push({name: 'invitacion-cotizar-contrato'});
                        });
                }
            });

        },
        formatoFecha(date){
            return moment(date).format('DD/MM/YYYY');
        },

        createImage(file) {
            var reader = new FileReader();
            var vm = this;

            reader.onload = (e) => {
                vm.archivo = e.target.result;
                vm.files.push(e.target.result);
                const unicos = vm.files.filter((valor, indice) => {
                    return vm.files.indexOf(valor) === indice;
                });
                vm.files = unicos;
            };
            reader.readAsDataURL(file);
        },

        /*createImage(file, tipo) {
            var reader = new FileReader();
            var vm = this;

            reader.onload = (e) => {
                if(tipo == "carta_terminos")
                {
                    vm.archivo_carta_terminos_condiciones = e.target.result;
                }
                if(tipo== 'formato_cotizacion')
                {
                    vm.archivo_formato_cotizacion = e.target.result;
                }
            };
            reader.readAsDataURL(file);
        },*/
        /*
        * onFileChange(e){
            //this.files = [];
            this.eliminarPartidasCFDI();
            this.archivo = null;
            var files = e.target.files || e.dataTransfer.files;
            if (!files.length)
                return;

            for(let i=0; i<files.length; i++) {
                this.archivo_name = files[i].name;
                this.createImage(files[i]);
                this.names.push(files[i].name);

                const unicos = this.names.filter((valor, indice) => {
                    return this.names.indexOf(valor) === indice;
                });
                this.names = unicos;

                if(files[i].type == "text/xml")
                {

                } else {
                    swal('Carga con XML', 'El archivo debe ser en formato XML', 'error')
                }
            }

            setTimeout(() => {
                this.cargarXML(1)
            }, 500);
        },
        * */
        onFileChange(e){
            //this.file = null;
            var files = e.target.files || e.dataTransfer.files;
            if (!files.length)
                return;
            let _self = this;

            for(let i=0; i<files.length; i++) {
                this.archivo_name = files[i].name;
                this.createImage(files[i]);
                this.names.push(files[i].name);

                const unicos = this.names.filter((valor, indice) => {
                    return this.names.indexOf(valor) === indice;
                });
                this.names = unicos;
            }

            this.names.forEach(function(name, i){
                _self.archivos.push({nombre:name, tipo:null, observaciones:""});
            });

            /*if(e.target.id == 'carta_terminos') {
                this.nombre_archivo_carta_terminos_condiciones = files[0].name;
            }
            if(e.target.id == 'formato_cotizacion')
            {
                this.nombre_archivo_formato_cotizacion = files[0].name;
            }
            this.createImage(files[0], e.target.id);*/
        },
        cambiaSucursal(destinatario)
        {
            if(destinatario.id_sucursal > 0 && destinatario.sucursales.length>1){
                destinatario.id_sucursal_seleccionada = destinatario.id_sucursal;
                if(destinatario.id_sucursal !== '' && destinatario.id_sucursal !== null && destinatario.id_sucursal !== undefined){
                    var busqueda_sucursal = destinatario.sucursales.find(x=>x.id === destinatario.id_sucursal);
                    if(busqueda_sucursal  != undefined){
                        destinatario.correo = busqueda_sucursal.email;
                        destinatario.contacto = busqueda_sucursal.contacto;
                    }
                }
            }
        },
        toggleEnCatalogo(destinatario){
            if(destinatario.en_catalogo == 1){

            } else {
                destinatario.id_sucursal = null;
                destinatario.id_proveedor = null;
                destinatario.correo = '';
                destinatario.contacto = '';
            }
        },
        toggleSinCoincidenciaProveedor(usuario){
            if(usuario.sin_coincidencia_proveedor == 1){
                usuario.id_usuario = null;
            }
        }
    },
    computed: {
        contrato(){
             return this.$store.getters['contratos/contrato-proyectado/currentContrato'];
        },
    },
    watch:{
        destinatarios: {
            handler(destinatarios) {
                let self = this
                destinatarios.map((destinatario, i) => {
                    if(destinatario.proveedor !== null){
                        //var busqueda = this.proveedores.find(x=>x.id === destinatario.id_proveedor);
                        destinatario.sucursales =  destinatario.proveedor.sucursales.data;
                        if(destinatario.sucursales.length == 1 && (destinatario.sucursales_cargadas == 0 || destinatario.id_proveedor != destinatario.id_proveedor_seleccionado)){
                            destinatario.id_proveedor = destinatario.proveedor.id;
                            destinatario.id_sucursal = destinatario.sucursales[0].id;
                            destinatario.correo = destinatario.sucursales[0].email;
                            destinatario.contacto = destinatario.sucursales[0].contacto;
                            destinatario.sucursales_cargadas = 1;
                            destinatario.id_sucursal_seleccionada = destinatario.sucursales[0].id;
                            destinatario.id_proveedor_seleccionado = destinatario.proveedor.id;
                        } else if(destinatario.sucursales.length > 1 && (destinatario.sucursales_cargadas == 0 || destinatario.id_proveedor != destinatario.id_proveedor_seleccionado)){
                            destinatario.id_proveedor = destinatario.proveedor.id;
                            destinatario.correo = '';
                            destinatario.contacto = '';
                            destinatario.id_proveedor_seleccionado = destinatario.proveedor.id;
                            destinatario.sucursales_cargadas = 1;
                        } /*else if(destinatario.sucursales.length > 1 &&  destinatario.id_sucursal != destinatario.id_sucursal_seleccionada){
                            var busqueda_sucursal = destinatario.sucursales.find(x=>x.id === destinatario.id_sucursal);
                            if(busqueda_sucursal  != undefined){
                                destinatario.correo = busqueda_sucursal.email+'6';
                                destinatario.contacto = busqueda_sucursal.contacto+'6';
                            }
                        }*/
                    }
                });
            },
            deep: true
        },
       /* id_proveedor(value){
            this.id_sucursal = null;
            if(value !== '' && value !== null && value !== undefined){
                var busqueda = this.proveedores.find(x=>x.id === value);
                this.sucursales = busqueda.sucursales.data;
                this.sucursal = (busqueda.sucursales.data.length) ? true : false;
                if(this.sucursales.length == 1){
                    this.id_sucursal = this.sucursales[0].id;
                    this.correo = busqueda.email;
                    this.contacto = busqueda.contacto;
                }
            }
        },
        id_sucursal(value){
            this.correo = '';
            this.contacto = '';
            if(value !== '' && value !== null && value !== undefined){
                var busqueda = this.sucursales.find(x=>x.id === value);
                this.correo = busqueda.email;
                this.contacto = busqueda.contacto;
            }
        },
        proveedor_en_catalogo(value){
            if(value == 1){

            } else {
                this.id_sucursal = null;
                this.id_proveedor = null;
            }
        },
        sin_coincidencia_proveedor(value){
            if(value == 1){
                this.id_usuario = '';
            } else {

            }
        },*/
    }
}
</script>

<style scoped>
.encabezado{
    background-color: #f2f4f5;
}

</style>
