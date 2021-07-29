<template>
    <span>
        <div class="card" v-if="!solicitud">
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
                <div class="row" v-if="solicitud">
                    <div class="col-md-12">
                        <encabezado-solicitud-compra v-bind:solicitud_compra="solicitud"></encabezado-solicitud-compra>
                    </div>
                </div>
                <div class="form-group row" v-if="solicitud">
                    <div class="col-md-12">
                        <div class="form-check form-check-inline">
                            <label class="form-check-label" style="cursor:pointer" >
                                <input class="form-check-input" type="checkbox" name="proveedor_en_catalogo" v-model="proveedor_en_catalogo" value="1" >
                            </label>
                        </div>
                        <!--<i class="fa fa-check-square"></i>-->
                        <label>Enviar la invitación a un proveedor existente en el catálogo</label>
                    </div>
                </div>
                <template v-if="solicitud && proveedor_en_catalogo">
                    <div class="row" >
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="id_proveedor">Proveedor:</label>
                                    <model-list-select
                                        id="id_proveedor"
                                        name="proveedor"
                                        option-value="id"
                                        v-model="id_proveedor"
                                        v-validate="{required: true}"
                                        :custom-text="razonSocialRFC"
                                        :list="proveedores"
                                        :placeholder="!cargando?'Seleccionar o busca proveedor por razón social o RFC':'Cargando...'">
                                     </model-list-select>
                                <div style="display:block" class="invalid-feedback" v-show="errors.has('proveedor')">{{ errors.first('proveedor') }}</div>
                            </div>
                        </div>
                        <div class="col-md-4" v-if="id_proveedor">
                            <div class="form-group">
                                <label for="id_sucursal">Sucursal:</label>
                                <select class="form-control"
                                        name="id_sucursal"
                                        data-vv-as="Sucursal"
                                        v-model="id_sucursal"
                                        v-validate="{required: true}"
                                        :error="errors.has('id_sucursal')"
                                        id="id_sucursal">
                                    <option value >-- Seleccionar--</option>
                                    <option v-for="sucursal in sucursales" :value="sucursal.id" >{{ sucursal.descripcion}}</option>
                                </select>
                                <div style="display:block" class="invalid-feedback" v-show="errors.has('id_sucursal')">{{ errors.first('id_sucursal') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="row" v-if="id_sucursal>0">
                        <div class="col-md-3">
                            <div class="form-group">
                            <label for="correo">Correo electrónico:</label>
                                <input
                                    name="correo"
                                    id="correo"
                                    v-model="correo"
                                    type="text"
                                    class="form-control"
                                    v-validate="{ required: true, email:true }"
                                />
                                <div style="display:block" class="invalid-feedback" v-show="errors.has('correo')">{{ errors.first('correo') }}</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                            <label for="contacto">Contacto:</label>
                                <input
                                    name="contacto"
                                    id="contacto"
                                    v-model="contacto"
                                    type="text"
                                    class="form-control"
                                    v-validate="{ required: true }"
                                />
                                <div style="display:block" class="invalid-feedback" v-show="errors.has('contacto')">{{ errors.first('contacto') }}</div>
                            </div>
                        </div>
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
                    </div>
                </template>

                <div class="row" v-else-if="solicitud && !proveedor_en_catalogo">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="correo">Correo electrónico:</label>
                            <input
                                name="correo"
                                id="correo"
                                v-model="correo"
                                type="text"
                                class="form-control"
                                :class="{'is-invalid': errors.has('correo')}"
                                v-validate="{ required: true, email:true }"

                            />
                            <div style="display:block" class="invalid-feedback" v-show="errors.has('correo')">{{ errors.first('correo') }}</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                        <label for="contacto">Contacto:</label>
                            <input
                                name="contacto"
                                id="contacto"
                                v-model="contacto"
                                type="text"
                                class="form-control"
                                :class="{'is-invalid': errors.has('contacto')}"
                                v-validate="{ required: true }"
                            />
                            <div style="display:block" class="invalid-feedback" v-show="errors.has('contacto')">{{ errors.first('contacto') }}</div>
                        </div>
                    </div>
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
                </div>
                <div class="row" v-if="solicitud && (id_sucursal>0 || proveedor_en_catalogo == 0)">
                    <div class="col-md-6">
                        <div class="form-group">
                        <label for="direccion_entrega">Dirección de Entrega:</label>
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
                    <div class="col-md-6">
                        <div class="form-group">
                        <label for="ubicacion_entrega_plataforma_digital">Ubicación de Entrega en Plataforma Digital:</label>
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
                <div class="row" v-if="solicitud && (id_sucursal>0 || proveedor_en_catalogo == 0)">
                    <div class="col-md-12">
                        <ckeditor v-model="cuerpo_correo" ></ckeditor>
                    </div>
                </div>
                <br>
                <div class="row" v-if="solicitud && (id_sucursal>0 || proveedor_en_catalogo == 0)">
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
                            <label for="carta_terminos">Formato de Cotización:</label>
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
                <div class="row" v-if="solicitud">
                    <div class="col-md-8">
                        <div class="form-check form-check-inline">
                            <label class="form-check-label" style="cursor:pointer" >
                                <input class="form-check-input" type="checkbox" name="proveedor_en_catalogo" v-model="mas_invitaciones" value="1" >
                            </label>
                        </div>
                        <label>Generar mas invitaciones para esta solicitud</label>
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
                        <h5 class="modal-title" id="exampleModalLongTitle"><i class="fa fa-list"></i> Seleccionar un proveedor preexistente</h5>

                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="callout callout-warning">
                                    <h6><i class="fa fa-info-circle"></i>Atención</h6>
                                    Los siguientes proveedores tienen coincidencia con el correo que ha ingresado, favor de seleccionar alguno.
                                </div>
                            </div>
                        </div>
                        <div class="row" v-if="usuarios.length>0">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="id_usuario">Proveedores:</label>
                                        <model-list-select
                                            id="id_usuario"
                                            name="proveedor"
                                            option-value="id"
                                            v-model="id_usuario"
                                            :custom-text="usuarioNombre"
                                            :list="usuarios"
                                            v-validate ="{ required: this.sin_coincidencia_proveedor == 0 ? true : false}"
                                            :isError="errors.has('proveedor')"
                                            :placeholder="!cargando?'Seleccionar un usuario preexistente en la el catálogo':'Cargando...'">
                                         </model-list-select>
                                    <div style="display:block" class="invalid-feedback" v-show="errors.has('proveedor')">{{ errors.first('proveedor') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-check form-check-inline">
                                    <label class="form-check-label" style="cursor:pointer" >
                                        <input class="form-check-input" type="checkbox" name="sin_coincidencia_proveedor" v-model="sin_coincidencia_proveedor" >
                                    </label>
                                </div>
                                <label>No deseo seleccionar un proveedor de la lista</label>
                            </div>
                        </div>
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
import TablaDatosSolicitudCompra from "../solicitud-compra/partials/TablaDatosSolicitudCompra";
import {ModelListSelect} from 'vue-search-select';
import Datepicker from 'vuejs-datepicker';
import {es} from 'vuejs-datepicker/dist/locale';

import EncabezadoSolicitudCompra from "../solicitud-compra/partials/Encabezado";
export default {
    name: "CreateInvitacionCompra.vue",
    components: {EncabezadoSolicitudCompra, Datepicker,es,TablaDatosSolicitudCompra, ModelListSelect},
    props: ['id_solicitud'],
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
            sin_coincidencia_proveedor : 0
        }
    },
    mounted() {
        if(!this.solicitud)
        {
            this.find();
        }else{
            this.direccion_entrega = this.solicitud.direccion_entrega;
            this.ubicacion_entrega_plataforma_digital = this.solicitud.ubicacion_entrega_plataforma_digital;
            this.getProveedores();
        }

        this.fecha_cierre = new Date();
        this.fechasDeshabilitadas.to = new Date();
    },
    methods:{
        find() {
            this.cargando = true;
            return this.$store.dispatch('compras/solicitud-compra/find', {
                id: this.id_solicitud,
                params:{include: [
                        ],
                    }
            }).then(data => {
                this.$store.commit('compras/solicitud-compra/SET_SOLICITUD', data);
                this.direccion_entrega = data.direccion_entrega;
                this.ubicacion_entrega_plataforma_digital = data.ubicacion_entrega_plataforma_digital;
            }).finally(()=>{
                this.getProveedores();
            });
        },
        getProveedores() {
            this.cargando = true;
            return this.$store.dispatch('cadeco/empresa/index', {
                params: {sort: 'razon_social', order: 'asc', scope:'tipoEmpresa:1,3', include: 'sucursales' }
            })
            .then(data => {
                this.proveedores = data.data;
            })
            .finally(()=>{
                this.getCuerpoCorreo();
            })
        },
        getCuerpoCorreo(){
            this.cargando = true;
            return this.$store.dispatch('compras/solicitud-compra/getCuerpoCorreo', {
                id:this.solicitud.id
            })
            .then(data => {
                this.cuerpo_correo = data;
            })
            .finally(()=>{
                this.cargando = false;
            })
        },
        razonSocialRFC (item)
        {
            return `[${item.razon_social}] - [ ${item.rfc} ]`;
        },
        usuarioNombre(item)
        {
            return `[${item.usuario}] - [ ${item.nombre} ]`;
        },
        salir()
        {
            this.$router.push({name: 'invitacion-compra-selecciona-solicitud'});
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


            //this.id_solicitud;
            this.id_proveedor = null;
            this.id_sucursal = null;
            this.observaciones = null;

            this.correo = null;
            this.contacto = null;
            this.sin_coincidencia_proveedor = 0;
            this.usuarios = [];
            this.id_usuario = '';
            this.usuarios_cargados = 0;

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
                    if (_self.proveedor_en_catalogo == 0 && _self.usuarios_cargados == 0 && _self.correo != '') {
                        return this.$store.dispatch('igh/usuario/findPorCorreo', {
                            params: {sort: 'usuario',  order: 'asc'},
                            correo: _self.correo,
                        })
                            .then((data) => {
                                _self.usuarios_cargados = 1;
                                _self.usuarios = data.data;
                                if (_self.usuarios.length > 0) {
                                    $(this.$refs.modal_usuarios).appendTo('body')
                                    $(this.$refs.modal_usuarios).modal('show');
                                } else {
                                    _self.usuarios_cargados = 0;
                                    this.store();
                                }
                            });
                    } else {
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
                    _self.post.id_transaccion = _self.id_solicitud;
                    _self.post.id_proveedor = _self.id_proveedor;
                    _self.post.id_sucursal = _self.id_sucursal;
                    _self.post.id_usuario = _self.id_usuario;
                    _self.post.observaciones = _self.observaciones;
                    _self.post.proveedor_en_catalogo = _self.proveedor_en_catalogo;
                    _self.post.correo = _self.correo;
                    _self.post.contacto = _self.contacto;
                    _self.post.fecha_cierre = _self.fecha_cierre;
                    _self.post.direccion_entrega = _self.direccion_entrega;
                    _self.post.ubicacion_entrega_plataforma_digital = _self.ubicacion_entrega_plataforma_digital;
                    _self.post.archivo_carta_terminos_condiciones = _self.archivo_carta_terminos_condiciones;
                    _self.post.nombre_archivo_carta_terminos_condiciones = _self.nombre_archivo_carta_terminos_condiciones;
                    _self.post.archivo_formato_cotizacion = _self.archivo_formato_cotizacion;
                    _self.post.nombre_archivo_formato_cotizacion = _self.nombre_archivo_formato_cotizacion;
                    _self.post.cuerpo_correo = _self.cuerpo_correo;
                }
            });
            return this.$store.dispatch('compras/invitacion/store', _self.post)
                .then((data) => {
                    $(this.$refs.modal_usuarios).modal('hide');
                    if(_self.mas_invitaciones == true){
                        this.limpiar();
                    } else {
                        this.$router.push({name: 'invitacion-compra'});
                    }
                });
        },
        formatoFecha(date){
            return moment(date).format('DD/MM/YYYY');
        },
        createImage(file, tipo) {
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
        },
        onFileChange(e){
            this.file = null;
            var files = e.target.files || e.dataTransfer.files;
            if (!files.length)
                return;

            if(e.target.id == 'carta_terminos') {
                this.nombre_archivo_carta_terminos_condiciones = files[0].name;
            }
            if(e.target.id == 'formato_cotizacion')
            {
                this.nombre_archivo_formato_cotizacion = files[0].name;
            }
            this.createImage(files[0], e.target.id);
        },
    },
    computed: {
        solicitud(){
             return this.$store.getters['compras/solicitud-compra/currentSolicitud'];
        },
    },
    watch:{
        id_proveedor(value){
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
        },
    }
}
</script>

<style scoped>

</style>
