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
                <div class="row" v-if="cargando == false">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="carta_terminos">Carta de Términos y Condiciones FIRMADA:</label>
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
                post : {},
            }
        },
        mounted() {

        },
        methods : {
            salir() {
                this.$router.push({name: 'cotizacion-proveedor'});
            },
            cargaFinalizada(id_cotizacion)
            {
                this.cargando = false;
                this.id_cotizacion = id_cotizacion;
            },
            onFileChange(e){
                this.file = null;
                var files = e.target.files || e.dataTransfer.files;
                if (!files.length)
                    return;

                if(e.target.id == 'carta_terminos') {
                    this.nombre_archivo_carta_terminos_condiciones = files[0].name;
                }
                this.createImage(files[0], e.target.id);
            },
            createImage(file, tipo) {
                var reader = new FileReader();
                var vm = this;

                reader.onload = (e) => {
                    if(tipo == "carta_terminos")
                    {
                        vm.archivo_carta_terminos_condiciones = e.target.result;
                    }
                };
                reader.readAsDataURL(file);
            },
            enviar() {
                let _self = this;
                this.$validator.validate().then(result => {
                    if (result) {
                        _self.post.id_invitacion = _self.id_invitacion;
                        _self.post.id_cotizacion = _self.id_cotizacion;
                        _self.post.archivo_carta_terminos_condiciones = _self.archivo_carta_terminos_condiciones;
                        _self.post.nombre_archivo_carta_terminos_condiciones = _self.nombre_archivo_carta_terminos_condiciones;

                        return this.$store.dispatch('compras/cotizacion/enviarCotizacion', _self.post)
                        .then((data) => {
                            this.$router.push({name: 'cotizacion-proveedor'});
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

