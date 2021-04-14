<template>
    <span>
        <div class="card" v-if="cfdi">
                <div class="card-header">
                    <h5>Soporte documental para {{cfdi.tipo_transaccion.descripcion}}</h5>
                </div>
                <div class="card-body">
                    <div class="row" >
                        <div class="col-md-12">
                            <add-proveedor v-bind:id_cfdi="id_cfdi" v-if="configuracion.agregar"></add-proveedor>
                            <add-tipo v-bind:id_cfdi="id_cfdi" v-if="configuracion.agregar_tipo"></add-tipo>
                        </div>
                    </div>
                    <br>
                    <div class="row"  >
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-striped" id="documentos" name="documentos">
                                    <thead>
                                        <tr>
                                            <th class="index_corto">#</th>
                                            <th class="index_corto"></th>
                                            <th class="th_c350">Tipo de Archivo</th>
                                            <th class="th_c100">Obligatorio</th>

                                            <th class="th_c350">Nombre de Archivo</th>
                                            <th >Observaciones</th>
                                            <th class="th_c100">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(archivo, i) in archivos" >
                                            <td>{{i+1}}</td>
                                            <td>
                                                <small class="label bg-success" v-if="archivo.estatus && archivo.obligatorio == 1" style="padding: 3px 2px 3px 5px">
                                                    <i class="fa fa-check"></i>
                                                </small>
                                                <small class="label bg-danger" v-else-if="archivo.obligatorio == 1" style="padding: 2px 2px 2px 5px">
                                                    <i class="fa fa-times"></i>
                                                </small>
                                            </td>

                                            <td>{{archivo.tipo_archivo.descripcion}}</td>
                                            <td style="text-align: center"><i class="fa fa-check" v-if="archivo.obligatorio == 1"></i></td>

                                            <td>{{archivo.nombre}}</td>
                                            <td>{{archivo.observaciones}}</td>
                                            <td>
                                                <div class="btn-group" >
                                                    <Documento v-bind:descripcion="archivo.tipo_archivo.descripcion" v-bind:url="url" v-bind:id="archivo.id" v-if="archivo.nombre && archivo.extension == 'pdf'"></Documento>
                                                    <button v-if="archivo.extension && archivo.extension != 'pdf'" type="button" class="btn btn-sm btn-outline-success" title="Ver" @click="modalImagen(archivo)" :disabled="cargando_imagenes == true">
                                                        <span v-if="cargando_imagenes == true && id_archivo == archivo.id">
                                                            <i class="fa fa-spin fa-spinner"></i>
                                                        </span>
                                                        <span v-else>
                                                            <i class="fa fa-picture-o"></i>
                                                        </span>
                                                    </button>
                                                    <replace-proveedor  v-if="archivo.nombre && configuracion.reemplazar" v-bind:archivo="archivo"></replace-proveedor>
                                                    <upload-proveedor v-else-if="configuracion.cargar" v-bind:archivo="archivo"></upload-proveedor>
                                                    <button @click="eliminar(archivo)" type="button" class="btn btn-sm btn-outline-danger" title="Eliminar" v-if="archivo.nombre && configuracion.eliminar" :disabled="eliminando">
                                                        <i class="fa fa-spin fa-spinner" v-if="eliminando"></i>
                                                        <i class="fa fa-trash" v-else></i>
                                                    </button>
                                                    <button @click="eliminarTipo(archivo)" type="button" class="btn btn-sm btn-outline-danger" title="Eliminar" v-else-if="archivo.obligatorio == 0 && configuracion.eliminar_tipo" :disabled="eliminando">
                                                        <i class="fa fa-spin fa-spinner" v-if="eliminando"></i>
                                                        <i class="fa fa-minus" v-else></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </span>

</template>

<script>
import CfdiShow from "../fiscal/cfd/cfd-sat/Show";
import CFDI from "../fiscal/cfd/cfd-sat/CFDI";
import AddProveedor from "../globals/archivos/AddProveedor";
import UploadProveedor from "../globals/archivos/UploadProveedor";
import ReplaceProveedor from "../globals/archivos/ReplaceProveedor";
import Documento from "../globals/archivos/Documento";
import AddTipo from "../globals/archivos/AddTipo";
export default {
    name: "SoporteDocumental",
    props: ["id_cfdi", "configuracion"],
    components: {AddTipo, AddProveedor, CFDI, CfdiShow, Documento, UploadProveedor, ReplaceProveedor},
    data() {
        return {
            url : '/api/entrega-cfdi/archivo/{id}/documento?access_token='+this.$session.get('jwt')+'&db=' + this.$session.get('db') + '&idobra=' + this.$session.get('id_obra'),
            cargando:true,
            cargado:false,
            cargando_archivo:false,
            eliminando : false,
            correo:'',
            contacto:'',
            observaciones:'',
            proyecto:null,
            id_cfdi_solicitar:'',
            obligatorios_completos: false,
        }
    },
    mounted() {
        this.find();
    },
    methods:{
        find(){
            this.cargado = false;
            this.id_cfdi_solicitar = this.id_cfdi;
            if(this.$store.getters['fiscal/cfd-sat/currentCFDSAT'] == null){
                this.$store.commit('fiscal/cfd-sat/SET_cCFDSAT', null);
                return this.$store.dispatch('fiscal/cfd-sat/find', {
                    id: this.id_cfdi,
                    params:{include: ['archivos']}
                }).then(data => {
                    this.$store.commit('fiscal/cfd-sat/SET_cCFDSAT', data);
                    this.$store.commit('entrega-cfdi/archivo/SET_ARCHIVOS', data.archivos.data);
                }).finally(()=>{
                    //this.getArchivos();
                    this.cargado = true;
                });
            } else {
                this.$store.commit('entrega-cfdi/archivo/SET_ARCHIVOS', this.cfdi.archivos.data);
                this.cargado = true;
            }
        },

        validate() {
            this.$validator.validate().then(result => {
                if (result) {
                    if(this.obligatorios_completos){
                        this.store()
                    } else {
                        swal(  'Archivos Faltantes', 'No puede realizar el registro de la solicitud si hay archivos obligatorios pendientes de cargar', 'error');
                    }
                }
            });
        },
        eliminar(archivo){
            this.eliminando = true;
            return this.$store.dispatch('entrega-cfdi/archivo/eliminar', {
                params: {id: archivo.id}
            }).then(data => {
                //this.$store.commit('documentacion/archivo/DELETE_ARCHIVO', data);
            }).finally( ()=>{
                this.eliminando = false;
            })
        },
        eliminarTipo(archivo){
            this.eliminando = true;
            return this.$store.dispatch('entrega-cfdi/archivo/eliminarTipo', {
                id: archivo.id,
                params: {}
            }).then(data => {
                //this.$store.commit('documentacion/archivo/DELETE_ARCHIVO', data);
            }).finally( ()=>{
                this.eliminando = false;
            })
        },
    },
    computed: {
        cfdi(){
            return this.$store.getters['fiscal/cfd-sat/currentCFDSAT'];
        },
        archivos(){
            return this.$store.getters['entrega-cfdi/archivo/archivos'];
        }
    },
}
</script>

<style scoped>

</style>
