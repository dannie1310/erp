<template>
    <span>
        <div class="card" >
			<div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <cotizacion-proveedor-partial-show v-bind:id_invitacion="this.id_invitacion" v-on:cargaFinalizada="cargaFinalizada" > </cotizacion-proveedor-partial-show>
                    </div>
                </div>
                <hr>
                <div class="row" v-if="invitacion">
                    <div class="col-md-12">
                        <span><label><i class="fa fa-files-o"></i>Archivos Requeridos Para Envío</label></span>
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
    export default {
        name: "cotizacion-proveedor-enviar",
        components: {CotizacionProveedorPartialShow},
        props: ['id_invitacion'],
        data() {
            return {
                cargando : true,
                id_cotizacion : '',
                archivos_requeridos : [],
                files_requeridos : [],
                post : {},
            }
        },
        mounted() {

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

                /*reader.onload = (e) => {
                    if(tipo == "carta_terminos")
                    {
                        vm.archivo_carta_terminos_condiciones = e.target.result;
                    }else if(tipo == "formato_cotizacion")
                    {
                        vm.archivo_formato_cotizacion = e.target.result;
                    }else if(tipo == "fichas_tecnicas")
                    {
                        vm.archivos_fichas_tecnicas.push({archivo: e.target.result});
                    }
                };
                */

                reader.readAsDataURL(file);
            },
            /*
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
            * */
            enviar() {
                let _self = this;
                this.$validator.validate().then(result => {
                    if (result) {
                        _self.post.id_invitacion = _self.id_invitacion;
                        _self.post.id_cotizacion = _self.id_cotizacion;
                        _self.post.cotizacion_completa = _self.invitacion.cotizacion_completa;
                        _self.post.archivos_requeridos = _self.archivos_requeridos;
                        _self.post.files_requeridos = _self.files_requeridos;

                        return this.$store.dispatch('compras/cotizacion/enviarCotizacion', _self.post)
                        .then((data) => {
                            this.$router.push({name: 'cotizacion-proveedor'});
                        });
                    }
                });
            }
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

