<template>
    <span>
        <button @click="cargar()" type="button" class="btn btn-outline-success pull-right mb-2" title="Cargar Layout">
            <i class="fa fa-upload"></i>Cargar Layout Excel
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-upload"></i> CARGAR LAYOUT DE ASIGNACIÓN</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row justify-content-between">
                            <div class="col-md-12">
                                    <label for="carga_layout" class="col-lg-12 col-form-label">Cargar Layout</label>
                                <div class="col-lg-12">
                                    <input type="file" class="form-control" id="carga_layout"
                                            @change="onFileChange"
                                            row="3"
                                            v-validate="{ ext: ['xlsx']}"
                                            name="carga_layout"
                                            data-vv-as="Layout"
                                            ref="carga_layout"
                                            :class="{'is-invalid': errors.has('carga_layout')}">
                                    <div class="invalid-feedback" v-show="errors.has('carga_layout')">{{ errors.first('carga_layout') }} (xlsx)</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="cerrarModal">
                            <i class="fa fa-times-circle"></i> Cerrar
                        </button>
                        <button type="button" class="btn btn-primary" @click="validate" :disabled="!file">
                            <i class="fa fa-upload"></i> Cargar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal partidas no válidas o con asignacion mayor a la pendiente -->
        <div class="modal" ref="modalInvalidas" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">
                            <i class="fa fa-pencil"></i>Resumen Carga de Layout con Errores</h5>
                        <button type="button" class="close" @click="cerrarModalInvalidas()" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" v-if="modalInvalidas">
                        <div class="row">
                            <div class="col-md-8">
                                <p><b>Las siguientes partidas no son válidas o exedieron la cantidad pendiente por asignar.</b></p>
                            </div>
                        </div>
                        <div class="row" v-for="(cotizacion, id_transaccion) in data.cotizaciones" v-if="cotizacion.partidas_no_validas">
                            <div class="col-sm-12">
                                <table class="table table-striped table-sm">
                                    <tr>
                                        <td colspan="6" style="border:none">
                                            <b>[{{cotizacion.folio_format}}] {{cotizacion.razon_social}}</b>
                                        </td>
                                    </tr>
                                    <tr class="encabezado">
                                        <th>#</th>
                                        <th style="width: 20%;">Descripción</th>
                                        <th style="width: 6%;">Unidad</th>
                                        <th style="width: 6%;">Cantidad Solicitada</th>
                                        <th style="width: 6%;">Cantidad Asignada Previamente</th>
                                        <th style="width: 6%;">Cantidad Pendiente Asignar</th>

                                        <th class="bg-gray-light ">Precio Unitario</th>
                                        <th class="bg-gray-light">% Descuento</th>
                                        <th class="bg-gray-light ">Importe</th>
                                        <th class="bg-gray-light">Moneda</th>
                                        <th class="bg-gray-light ">Importe Pesos (MXN)</th>
                                        <th class="bg-gray-light th_money_input">Cantidad Asignada</th>
                                    </tr>
                                    <tr v-for="(partida, i) in cotizacion.partidas" v-if="partida && (!partida.cantidad_valida || data.items[i].asignadas_mayor_disponible)">
                                        <td>{{i}}</td>
                                        <td :class="data.items[i].asignadas_mayor_disponible?`asignacion_mayor`:``" :title="data.items[i].descripcion">{{data.items[i].descripcion_corta}}</td>
                                        <td :class="data.items[i].asignadas_mayor_disponible?`asignacion_mayor`:``">{{data.items[i].unidad}}</td>
                                        <td :class="data.items[i].asignadas_mayor_disponible?`asignacion_mayor`:``" class="align_right">{{parseFloat(data.items[i].cantidad_solicitada).formatMoney(4,'.',',')}}</td>
                                        <td :class="data.items[i].asignadas_mayor_disponible?`asignacion_mayor`:``" class="align_right">{{parseFloat(data.items[i].cantidad_asignada).formatMoney(4,'.',',')}}</td>
                                        <td :class="data.items[i].asignadas_mayor_disponible?`asignacion_mayor`:``" class="align_right">{{parseFloat(data.items[i].cantidad_disponible).formatMoney(4,'.',',')}}</td>

                                        <td class="align_right" :class="!partida.cantidad_valida?`cantidad_invalida`:``">{{partida.precio_unitario_format}}</td>
                                        <td class="align_right" :class="!partida.cantidad_valida?`cantidad_invalida`:``">{{partida.descuento}}</td>
                                        <td class="align_right" :class="!partida.cantidad_valida?`cantidad_invalida`:``">${{partida.importe}}</td>
                                        <td :class="!partida.cantidad_valida?`cantidad_invalida`:``">{{partida.moneda}}</td>
                                        <td class="align_right" :class="!partida.cantidad_valida?`cantidad_invalida`:``">${{partida.importe_moneda_conversion}}</td>
                                        <td class="align_right" :class="!partida.cantidad_valida || data.items[i].asignadas_mayor_disponible?`cantidad_invalida`:``">{{parseFloat(partida.cantidad_asignada).formatMoney(4,'.',',')}}</td>
                                        
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="cerrarModalInvalidas()"><i class="fa fa-close"></i>Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
export default {
    name: "carga-layout-asignacion-proveedor",
    components: {},
    props:['id_solicitud'],
    data() {
        return {
            cargando: false,
            cotizaciones : [],
            file: null,
            nombre: '',
            data:null,
            modalInvalidas: false,
        }
    },
    mounted() {
        this.$validator.reset();
        this.file = null;
    },
    methods : {
        cargar() {
            this.file = null;
            this.$refs.carga_layout.value = '';
            this.$validator.errors.clear();
            $(this.$refs.modal).appendTo('body')
            $(this.$refs.modal).modal('show');
        },
        validate() {
            this.$validator.validate().then(result => {
                if (result){
                    this.cargarLayout()
                }else{
                    if(this.$refs.carga_layout.value !== ''){
                        this.$refs.carga_layout.value = '';
                        this.file = null;
                    }
                    this.$validator.errors.clear();
                    swal('¡Error!', 'Error archivos de entrada invalidos.', 'error')
                }
            });
        },
        createImage(file, tipo) {
            var reader = new FileReader();
            var vm = this;

            reader.onload = (e) => {
                vm.file = e.target.result;
            };
            reader.readAsDataURL(file);

        },
        onFileChange(e){
            this.file = null;
            var files = e.target.files || e.dataTransfer.files;
            if (!files.length)
                return;
                this.nombre = files[0].name;
            if(e.target.id == 'carga_layout') {
                this.createImage(files[0]);
            }
        },
        cargarLayout(){
            var formData = new FormData();
            formData.append('file',  this.file);
            formData.append('id',  this.id_solicitud);
            formData.append('name', this.nombre);

            return this.$store.dispatch('compras/solicitud-compra/cargaLayoutAsignacion',
                {
                    data: formData,
                    config: {
                        params: { _method: 'POST'}
                    }
                })
                .then(data => {
                    if(data.partidas_no_validas){
                        this.modalInvalidas = true;
                        this.data = data;
                        this.file = null;
                        this.$validator.errors.clear();
                        $(this.$refs.modalInvalidas).appendTo('body')
                        $(this.$refs.modalInvalidas).modal('show');
                    }else{
                        $(this.$refs.modal).modal('hide');
                        this.file = null;
                        this.file_name = '';
                        this.$validator.errors.clear();
                        this.$router.push({name: 'asignacion-proveedor-layout-create', params: {id_empresa: Object.keys(data.cotizaciones)[0], id_solicitud:this.id_solicitud, data:data}});
                        }
                }).finally(() => {
                    
                });
        },
        cerrarModal() {
            this.file = null;
            this.$refs.carga_layout.value = '';
            this.$validator.errors.clear();
            $(this.$refs.modal).modal('hide')
        },
        cerrarModalInvalidas(){
            this.file = null;
            this.$refs.carga_layout.value = '';
            this.$validator.errors.clear();
            $(this.$refs.modalInvalidas).modal('hide')
        },
    }


}
</script>

<style scoped>
table {
    word-wrap: unset;
    width: 100%;
    background-color: white;
    border-color: transparent;
    border-collapse: collapse;
    clear: both;
}
table.table-fs-sm{
    font-size: 10px;
}

table th,  table td {
    border: 1px solid #dee2e6;
}

table td.mejor_opcion {
    color: green;
}

table td.asignacion_mayor {
    color: red;
}

table td.cantidad_invalida {
    color: red;
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

table thead th.no_negrita
{
    padding: 0.2em;

    background-color: #f2f4f5;
    font-weight: normal;
    color: black;
    overflow: hidden;
    text-align: center;
}

table td.sin_borde {
    border: none;
    padding: 2px 5px;
}

table td.align_right {
    text-align: right;
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
table tbody th
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

.encabezado{
    text-align: center;
    background-color: #f2f4f5;
    font-weight: bold;
}
</style>
