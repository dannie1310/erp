<template>
    <span>
        <div class="card" >
			<div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <cotizacion-proveedor-partial-show v-bind:id_invitacion="this.id_invitacion" v-on:cargaFinalizada="cargaFinalizada" > </cotizacion-proveedor-partial-show>
                    </div>
                </div>
                <hr v-if="invitacion">
                <div class="row" v-if="invitacion">
                    <div class="col-md-12 table-responsive">
                        <span><label><i class="fa fa-files-o"></i>Archivos Obligatorios (Requeridos Para Envío)</label></span>
                        <table class="table table-sm table-bordered">

                            <tr>
                                <th class="encabezado index_corto">
                                    #
                                </th>
                                <th class="encabezado c250" >
                                    Tipo
                                </th>
                                <th class="encabezado c250" >
                                    Observaciones
                                </th>
                                <th class="encabezado c250">
                                    Archivo
                                </th>
                            </tr>

                            <tbody>
                                <tr v-for="(archivo_requerido, i) in invitacion.archivos_requeridos.data">
                                    <td>{{i+1}}</td>
                                    <td >
                                        {{archivo_requerido.tipo_archivo_txt}}

                                    </td>
                                    <td>
                                        {{archivo_requerido.observaciones}}
                                    </td>
                                    <td>
                                        <div class="form-group error-content">
                                            <input type="file" class="form-control" id="cargar_file"
                                               @change="onFileChangeRequeridos"
                                               row="3"
                                               v-validate="{required:true, size: 102400}"
                                               :name="`archivo_requerido_${archivo_requerido.id}`"
                                               :id="`${archivo_requerido.id}`"
                                               data-vv-as="Cargar"
                                               :ref="`${archivo_requerido.id}`"
                                               :class="{'is-invalid': errors.has(`archivo_requerido_${archivo_requerido.id}`)}"
                                            >
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row" v-if="invitacion">
                    <div class="col-md-12 table-responsive">
                        <div >
                            <div>
                                <label class="col-form-label"><span><i class="fa fa-files-o"></i>Archivos Adicionales</span></label>
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
                                        :list="tipos_archivo_enviar"
                                        :isError="archivo.errores_tipo">
                                        :placeholder="!cargando?'Seleccionar tipo de archivo':'Cargando...'">
                                    </model-list-select>
                                </td>
                                <td>
                                    <textarea
                                        :id="`observaciones_${i}`"
                                        :name="`observaciones_${i}`"
                                        class="form-control"
                                        v-model="archivo.observaciones"
                                        :data-vv-as="`Observaciones ${i+1}`"
                                        :class="{'is-invalid': archivo.errores_observacion}"
                                        rows="1"
                                    ></textarea>
                                </td>
                                <td style="text-align: center">
                                    <button type="button" class="btn btn-sm btn-outline-danger" @click="quitarArchivo(i)" >
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </table>

                    </div>
                </div>
                <div class="row" v-if="cargando == false">
                    <div class="col-md-12">
                        <small><b style="font-style: italic; color: #00b44e">* Adjuntar un archivo de carta de términos y condiciones implica la aceptación tácita de los términos y condiciones.</b></small>
                    </div>
                </div>
			</div>
            <div class="card-footer">
                <div class="pull-right">
                    <button type="button" class="btn btn-secondary" v-on:click="salir"><i class="fa fa-angle-left"></i>Regresar</button>
                    <button type="button" class="btn btn-primary" v-on:click="enviar" :disabled="cargando"><i class="fa fa-send"></i>Enviar</button>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    import CotizacionProveedorPartialShow from "./partials/PartialShow";
    import {ModelListSelect} from 'vue-search-select';
    export default {
        name: "cotizacion-proveedor-enviar",
        components: {CotizacionProveedorPartialShow, ModelListSelect},
        props: ['id_invitacion'],
        data() {
            return {
                cargando : true,
                id_cotizacion : '',
                archivos_requeridos : [],
                files_requeridos : [],
                post : {},
                tipos_archivo_enviar : [],
                files : [],
                names : [],
                archivos :[],
            }
        },
        mounted() {
            this.getTiposArchivoEnviar();
        },
        methods : {
            salir() {
                this.$router.push({name: 'cotizacion-proveedor'});
            },
            cargaFinalizada(invitacion)
            {
                this.cargando = false;
                this.id_cotizacion = invitacion.cotizacion.id_transaccion;
                this.requiere_fichas_tecnicas = invitacion.requiere_fichas_tecnicas;
            },
            onFileChangeRequeridos(e){
                this.file = null;
                var files = e.target.files || e.dataTransfer.files;
                if (!files.length)
                    return;
                let id = null;
                let existe_id = false;

                id = e.target.id;
                this.archivo_name = files[0].name;

                this.archivos_requeridos = this.archivos_requeridos.map(ar =>{
                    if(ar.id === id)
                    {
                        existe_id = true;
                        return Object.assign({},ar,{
                            nombre : files[0].name,
                            id : id,
                            observaciones : "",
                            errores_tipo : false,
                            errores_observacion : false
                        });

                    }else{
                        return ar;
                    }
                });

                if(!existe_id)
                {
                    this.archivos_requeridos.push(
                        {
                            nombre : files[0].name,
                            id : id,
                            observaciones : "",
                            errores_tipo : false,
                            errores_observacion : false
                        }
                    );
                }
                this.createImageRequeridos(files[0],id);
            },
            createImageRequeridos(file, id) {
                var reader = new FileReader();
                var vm = this;

                reader.onload = (e) => {
                    vm.archivo = e.target.result;

                    let existe_id = false;
                    vm.files_requeridos = vm.files_requeridos.map(fr =>{
                        if(fr.id === id)
                        {
                            existe_id = true;
                            return Object.assign({},fr,{file: e.target.result, id: id});

                        }else{
                            return fr;
                        }
                    });

                    if(!existe_id)
                    {
                        vm.files_requeridos.push({file: e.target.result, id: id});
                    }
                };
                reader.readAsDataURL(file);
            },
            enviar() {
                let _self = this;
                let errores = 0;

                this.archivos.forEach(function(archivo, i) {
                    if(archivo.tipo == 14 && (archivo.observaciones) == "")
                    {
                        archivo.errores_observacion = true;
                        errores ++;
                    } else{
                        archivo.errores_observacion = false;
                    }
                    if(archivo.tipo == null){
                        archivo.errores_tipo = true;
                        errores ++;
                    }else{
                        archivo.errores_tipo = false;
                    }
                });
                this.$validator.validate().then(result => {
                    if (result && errores == 0) {
                        _self.post.id_invitacion = _self.id_invitacion;
                        _self.post.id_cotizacion = _self.id_cotizacion;
                        _self.post.cotizacion_completa = _self.invitacion.cotizacion_completa;
                        _self.post.archivos_requeridos = _self.archivos_requeridos;
                        _self.post.files_requeridos = _self.files_requeridos;
                        _self.post.archivos = _self.archivos;
                        _self.post.files = _self.files;

                        return this.$store.dispatch('compras/cotizacion/enviarCotizacion', _self.post)
                        .then((data) => {
                            this.$router.push({name: 'cotizacion-proveedor'});
                        });
                    }
                });
            },
            quitarArchivo(index){
                this.archivos.splice(index, 1);
                this.files.splice(index, 1);
                this.names.splice(index, 1);
            },
            getTiposArchivoEnviar(){
                this.cargando = true;
                return this.$store.dispatch('padronProveedores/invitacion/getTiposArchivo', {
                    params:{
                        tipo : [2,3],
                        area: [1,3]
                    }
                })
                    .then(data => {
                        this.tipos_archivo_enviar = data;
                    })
                    .finally(()=>{
                        this.cargando = false;
                    })
            },
            createImage(file) {
                var reader = new FileReader();
                var vm = this;

                reader.onload = (e) => {
                    vm.archivo = e.target.result;
                    vm.files.push(e.target.result);
                };
                reader.readAsDataURL(file);
            },
            onFileChange(e){
                var files = e.target.files || e.dataTransfer.files;
                if (!files.length)
                    return;
                let _self = this;

                for(let i=0; i<files.length; i++) {
                    if(!this.names.includes(files[i].name))
                    {
                        this.archivo_name = files[i].name;
                        this.createImage(files[i]);
                        this.names.push(files[i].name);
                        this.archivos.push({nombre:files[i].name, tipo:null, observaciones:"", errores_tipo: false, errores_observacion : false});
                    }
                }
                this.$refs.archivos.value = '';
            },
        },
        computed: {
            invitacion() {
                return this.$store.getters['padronProveedores/invitacion/currentInvitacion']
            }
        },
    }
</script>

<style scoped>
table#tabla-conceptos {
    word-wrap: unset;
    width: 100%;
    background-color: white;
    border-color: transparent;
    border-collapse: collapse;
    clear: both;
}

table#tabla-conceptos th, table#tabla-conceptos td {
    border: 1px solid #dee2e6;
}



table thead th
{
    padding: 0.2em;

    background-color: #f2f4f5;
    font-weight: bold;
    color: black;
    overflow: hidden;
    text-align: center;
}

table#tabla-conceptos td.sin_borde {
    border: none;
    padding: 2px 5px;
}

table thead th {
    text-align: center;
}
table tbody tr
{
    border-width: 0 1px 1px 1px;
    border-style: none solid solid solid;
    border-color: white #CCCCCC #CCCCCC #CCCCCC;
}
table tbody td,
table#tabla-conceptos table tbody th
{
    border-right: 1px solid #ccc;
    color: #242424;
    line-height: 20px;
    overflow: hidden;
    padding: 2px 5px;
    text-align: left;
    text-overflow: ellipsis;
    -o-text-overflow: ellipsis;
    -ms-text-overflow: ellipsis;
    white-space: nowrap;
}


table tbody td input.text
{
    border: none;
    padding: 0;
    margin: 0;
    width: 100%;
    background-color: transparent;
    font-family: inherit;
    font-size: inherit;
    font-weight: bold;
}

table .numerico
{
    text-align: right;
    padding-left: 0;
    white-space: normal;
}

.text.is-invalid {
    color: #dc3545;
}

table tbody td input.text {
    text-align: right;
}
.encabezado{
    background-color: #f2f4f5;
}
</style>

