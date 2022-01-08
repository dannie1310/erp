<template>
    <span>
        <div class="row">
            <div class="col-12">
                <div class="invoice p-3 mb-3">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <label for="id_solicitud">Seleccionar Solicitud de Compra:</label>
                                    <model-list-select
                                        :disabled="cargando"
                                        name="id_solicitud"
                                        option-value="id"
                                        v-model="id_solicitud"
                                        :custom-text="numeroFolioFormatAndObservaciones"
                                        :list="solicitudes"
                                        :placeholder="!cargando?'Seleccionar o buscar solicitud de compra por número de folio o observación':'Cargando...'"
                                        :isError="errors.has(`id_solicitud`)">
                                    </model-list-select>
                                <div style="display:block" class="invalid-feedback" v-show="errors.has('id_solicitud')">{{ errors.first('id_solicitud') }}</div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                <!-- <carga-layout v-if="contrato" :disabled="cargando" v-bind:id_contrato="id_contrato" /> -->
                                <button @click="descargar()" v-if="data" type="button" class="btn btn-outline-success pull-right mr-1 mt-2" title="Descargar Layout Asignación">
                                    <i class="fa fa-download"></i>Descargar Layout Excel
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" v-if="!continuar">
                        <button type="button" class="btn btn-secondary" v-on:click="salir">
                            <i class="fa fa-angle-left"></i>
                            Regresar</button>
                        <button type="button" :disabled="id_solicitud == ''" @click="continua" class="btn btn-primary">
                            Continuar
                            <i class="fa fa-angle-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <Create v-bind:data="data" v-bind:id_empresa="Object.keys(data.cotizaciones)[0]" v-if="data && continuar"></Create>
         
    </span>
    
</template>

<script>
import {ModelListSelect} from 'vue-search-select';
import Create from './Create';
export default {
    name: "asignacion-proveedor-seleccionar",
    components: {ModelListSelect, Create},
    data() {
        return {
            cargando: false,
            solicitudes:[],
            data:null,
            id_solicitud:'',
            id_empresa:'',
            justificar:false,
            partidas_justificacion:[],
            replicar_justificacion:false,
            continuar:false,
        }
    },
    mounted() {
        this.getSolicitudes();
    },
    computed: {

    },
    methods: {
        descargar(){
            this.cargando = true;
            return this.$store.dispatch('compras/solicitud-compra/descargaLayoutAsignacion', {id:this.id_solicitud})
                .then(() => {
                    this.$emit('success')
                    this.cargando = false;
                })
        },
        continua(){
            this.continuar = true;
        },
        numeroFolioFormatAndObservaciones(item){
            return `[${item.numero_folio_format}] - [${item.observaciones}]`
        },
        salir(){
            swal({
                title: "Salir de Asignación de Proveedores",
                text: "¿Está seguro de que quiere salir del registro de asignación de proveedores?",
                icon: "info",
                buttons: {
                    cancel: {
                        text: 'Cancelar',
                        visible: true
                    },
                    confirm: {
                        text: 'Si, Salir',
                        closeModal: true,
                    }
                }
            })
            .then((value) => {
                if (value) {
                    this.$router.push({name: 'asignacion-proveedor'});
                }
            });
        },
        getSolicitudes(){
            this.cargando = true;
            this.solicitudes = [];
            this.data = null;
            return this.$store.dispatch('compras/solicitud-compra/index', {
                params: {
                    scope: ['cotizacion', 'conComplemento', 'ultimoAnio'],
                    order: 'DESC',
                    sort: 'numero_folio'
                }
            })
            .then(data => {
                this.solicitudes = data.data;
                this.cargando = false;
            })

        },
        getCotizaciones(id){
            this.cargando = true;
            this.data = null;
            return this.$store.dispatch('compras/solicitud-compra/getCotizaciones', {
                id: id,
                params: {}
            })
            .then(data => {
                this.id_empresa = Object.keys(data.cotizaciones)[0];
                this.data = data;
            })
            .finally(() => {
                this.cargando = false;
            })
        },
    },
    watch:{
        id_solicitud(value){
            if(value != ''){
                this.continuar = false;
                this.getCotizaciones(value);
            }
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
