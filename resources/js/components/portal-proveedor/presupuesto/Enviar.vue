<template>
    <span>
        <div class="card" >
			<div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <presupuesto-proveedor-partial-show v-bind:id="id" @created="iniciar" />
                    </div>
                </div>
                <hr>
                <div class="row" v-if="cargando == false">
                    <div class="col-md-4" v-if="invitacion.formato_cotizacion">
                        <div class="form-group">
                            <label for="formato_cotizacion">Formato de Cotización:</label>
                            <input type="file" class="form-control" id="formato_cotizacion"
                                   @change="onFileChange"
                                   v-validate="{required:true, ext: ['pdf'],  size: 10240}"
                                   name="formato_cotizacion"
                                   data-vv-as="Formato de Cotización"
                                   ref="formato_cotizacion"
                                   :class="{'is-invalid': errors.has('formato_cotizacion')}"
                            >
                            <div class="invalid-feedback" v-show="errors.has('formato_cotizacion')">{{ errors.first('formato_cotizacion') }} (pdf)</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="carta_terminos">Carta de Términos y Condiciones FIRMADA*:</label>
                            <input type="file" class="form-control" id="carta_terminos"
                                   @change="onFileChange"
                                   v-validate="{required:true, ext: ['pdf'],  size: 10240}"
                                   name="carta_terminos"
                                   data-vv-as="Carta de Términos y Condiciones"
                                   ref="carta_terminos"
                                   :class="{'is-invalid': errors.has('carta_terminos')}"
                            >
                            <div class="invalid-feedback" v-show="errors.has('carta_terminos')">{{ errors.first('carta_terminos') }} (pdf)</div>
                        </div>
                    </div>
                    <div class="col-md-4" v-if="requiere_fichas_tecnicas">
                        <div class="form-group">
                            <label for="carta_terminos">Fichas Técnicas:</label>
                            <input type="file" class="form-control" id="fichas_tecnicas" multiple="multiple"
                                   @change="onFileChange"
                                   v-validate="{required:true, ext: ['pdf'],  size: 10240}"
                                   name="fichas_tecnicas"
                                   data-vv-as="Fichas Técnicas"
                                   ref="fichas_tecnicas"
                                   :class="{'is-invalid': errors.has('fichas_tecnicas')}"
                            >
                            <div class="invalid-feedback" v-show="errors.has('fichas_tecnicas')">{{ errors.first('fichas_tecnicas') }} (pdf)</div>
                        </div>
                    </div>
                 </div>
                <div class="row" v-if="cargando == false">
                    <div class="col-md-12">
                        <small><b style="font-style: italic; color: #00b44e">* Adjuntar un archivo en el campo para la carta de términos y condiciones firmada implica la aceptación tácita de los términos y condiciones.</b></small>
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
    import PresupuestoProveedorPartialShow from './partials/PartialShow'
    export default {
        name: "presupuesto-proveedor-enviar",
        components: {PresupuestoProveedorPartialShow},
        props: ['id'],
        data() {
            return {
                cargando : true,
                invitacion : '',
                id_presupuesto : '',
                requiere_fichas_tecnicas : '',
                archivos_fichas_tecnicas : [],
                nombres_archivos_fichas_tecnicas : [],
                post : {},
            }
        },
        mounted() {

        },
        methods : {
            salir() {
                this.$router.push({name: 'cotizacion-proveedor'});
            },
            iniciar(invitacion) {
                this.cargando = false;
                this.invitacion = invitacion;
                this.id_presupuesto = invitacion.cotizacion.id_transaccion;
                this.requiere_fichas_tecnicas = invitacion.requiere_fichas_tecnicas;
            },
            onFileChange(e){
                this.file = null;
                var files = e.target.files || e.dataTransfer.files;
                if (!files.length)
                    return;

                if(e.target.id == 'carta_terminos') {
                    this.nombre_archivo_carta_terminos_condiciones = files[0].name;
                    this.createImage(files[0], e.target.id);
                }else if(e.target.id == 'formato_cotizacion') {
                    this.nombre_archivo_formato_cotizacion = files[0].name;
                    this.createImage(files[0], e.target.id);
                }else if(e.target.id == 'fichas_tecnicas') {
                    for(let i=0; i<files.length; i++) {
                        this.createImage(files[i], e.target.id);
                        this.nombres_archivos_fichas_tecnicas[i] = {
                            nombre: files[i].name,
                        };
                    }
                }
            },
            createImage(file, tipo) {
                var reader = new FileReader();
                var vm = this;
                reader.onload = (e) => {
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
                reader.readAsDataURL(file);
            },
            enviar() {
                let _self = this;
                this.$validator.validate().then(result => {
                    if (result) {
                        _self.post.id_invitacion = _self.id;
                        _self.post.id = _self.id_presupuesto;
                        _self.post.archivo_carta_terminos_condiciones = _self.archivo_carta_terminos_condiciones;
                        _self.post.nombre_archivo_carta_terminos_condiciones = _self.nombre_archivo_carta_terminos_condiciones;
                        _self.post.archivo_formato_cotizacion = _self.archivo_formato_cotizacion;
                        _self.post.nombre_archivo_formato_cotizacion = _self.nombre_archivo_formato_cotizacion;
                        _self.post.archivos_fichas_tecnicas = _self.archivos_fichas_tecnicas;
                        _self.post.nombres_archivos_fichas_tecnicas = _self.nombres_archivos_fichas_tecnicas;

                        return this.$store.dispatch('contratos/presupuesto/enviarPresupuesto', _self.post)
                        .then((data) => {
                            this.$router.go(-1);
                        });
                    }
                });
            }
        },
        computed: {

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
</style>

