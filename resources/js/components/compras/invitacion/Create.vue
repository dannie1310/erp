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
                        <tabla-datos-solicitud-compra v-bind:solicitud_compra="solicitud"></tabla-datos-solicitud-compra>
                    </div>
                </div>
                <hr>
                <div class="form-group row" v-if="solicitud">
                    <div class="col-md-12">
                        <!--<div class="form-check form-check-inline">
                            <label class="form-check-label" style="cursor:pointer" >
                                <input class="form-check-input" type="checkbox" name="proveedor_en_catalogo" v-model="proveedor_en_catalogo" value="1" >
                            </label>
                        </div>-->
                        <i class="fa fa-check-square"></i>
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
                                            :class="{'is-invalid': errors.has('fecha')}"
                                />
                                <div style="display:block" class="invalid-feedback" v-show="errors.has('contacto')">{{ errors.first('contacto') }}</div>
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
                    <div class="col-md-6">
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
                <div class="row" v-if="solicitud && id_sucursal>0">
                    <div class="col-md-12">
                        <div class="form-group">
                        <label for="direccion_entrega">Dirección de Entrega:</label>
                            <textarea
                                name="direccion_entrega"
                                id="direccion_entrega"
                                v-model="direccion_entrega"
                                type="text"
                                class="form-control"
                            />
                            <div style="display:block" class="invalid-feedback" v-show="errors.has('direccion_entrega')">{{ errors.first('direccion_entrega') }}</div>
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
                            <button type="submit" class="btn btn-primary" v-on:click="enviar" :disabled="errors.count() > 0"><i class="fa fa-envelope"></i>
                                Enviar</button>
                        </div>
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
export default {
    name: "CreateInvitacionCompra.vue",
    components: {Datepicker,es,TablaDatosSolicitudCompra, ModelListSelect},
    props: ['id_solicitud'],
    data(){
        return {
            cargando : false,
            proveedores : [],
            sucursales: [],
            es:es,
            post: {},
            id_proveedor : null,
            id_sucursal : null,
            proveedor_en_catalogo : 1,
            correo : '',
            contacto : '',
            observaciones:'',
            mas_invitaciones:1,
            fecha_cierre : new Date(),
            direccion_entrega : '',
            fechasDeshabilitadas: {}
        }
    },
    mounted() {
        if(!this.solicitud)
        {
            this.find();
        }else{
            this.direccion_entrega = solicitud.direccion_entrega;
            this.getProveedores();
        }

        this.fecha_cierre = new Date();
        this.fechasDeshabilitadas.to = new Date();
    },
    methods:{
        find() {
            return this.$store.dispatch('compras/solicitud-compra/find', {
                id: this.id_solicitud,
                params:{include: [
                        ],
                    }
            }).then(data => {
                this.$store.commit('compras/solicitud-compra/SET_SOLICITUD', data);
                this.direccion_entrega = data.direccion_entrega;
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
                    this.cargando = false;
                })
        },
        razonSocialRFC (item)
        {
            return `[${item.razon_social}] - [ ${item.rfc} ]`;
        },
        salir()
        {
            this.$router.push({name: 'invitacion-compra-selecciona-solicitud'});
        },
        enviar()
        {
            let _self = this;
            this.$validator.validate().then(result => {
                if (result) {
                    _self.post.id_transaccion = _self.id_solicitud;
                    _self.post.id_proveedor = _self.id_proveedor;
                    _self.post.id_sucursal = _self.id_sucursal;
                    _self.post.observaciones = _self.observaciones;
                    _self.post.proveedor_en_catalogo = _self.proveedor_en_catalogo;
                    _self.post.correo = _self.correo;
                    _self.post.contacto = _self.contacto;
                    _self.post.fecha_cierre = _self.fecha_cierre;
                    _self.post.direccion_entrega = _self.direccion_entrega;
                    return this.$store.dispatch('compras/invitacion/store', _self.post)
                        .then((data) => {
                            if(_self.mas_invitaciones == true){
                                this.$router.go();
                            } else {
                                this.$router.push({name: 'invitacion-compra'});
                            }
                        });
                }
            });
        },
        formatoFecha(date){
            return moment(date).format('DD/MM/YYYY');
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
    }
}
</script>

<style scoped>

</style>
