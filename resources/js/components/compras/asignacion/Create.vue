<template>
    <span>
        <div class="card" v-if="cargando">
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
        <div class="row" v-if="data">
            <div class="col-12">
                <div class="invoice p-3 mb-3">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <tabla-datos-solicitud-compra v-bind:solicitud_compra="solicitud_compra"></tabla-datos-solicitud-compra>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-12 table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th colspan="6" rowspan="4" class="text-left"><h5></h5></th>
                                            </tr>
                                            <tr class="bg-gray-light">
                                                <th colspan="6" >
                                                    <select
                                                        type="text"
                                                        name="id_empresa"
                                                        data-vv-as="Razón Social"
                                                        class="form-control"
                                                        id="id_empresa"
                                                        v-model="id_empresa">
                                                        <option v-for="cotizacion in data.cotizaciones" :value="cotizacion.id_transaccion">[{{cotizacion.folio_format}}] {{ cotizacion.razon_social }}</option>
                                                    </select>
                                                </th>
                                            </tr>
                                            <tr class="bg-gray-light">
                                                <th colspan="6" >{{data.cotizaciones[id_empresa].sucursal}}</th>
                                            </tr>
                                            <tr class="bg-gray-light">
                                                <th colspan="6"  >{{data.cotizaciones[id_empresa].direccion}}</th>
                                            </tr>
                                            <tr>
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
                                        </thead>
                                        <tbody>
                                            <tr v-for="(item, i) in data.items" v-if="item.item_pendiente">
                                                <td>{{ i+1}}</td>
                                                <td :title="item.descripcion">{{item.descripcion_corta}}</td>
                                                <td>{{item.unidad}}</td>
                                                <td class="align_right">{{item.cantidad_solicitada}}</td>
                                                <td class="align_right">{{item.cantidad_asignada}}</td>
                                                <td class="align_right">{{item.cantidad_disponible}}</td>
                                                <td class="align_right" :class="data.cotizaciones[id_empresa].partidas[i].mejor_opcion?`mejor_opcion`:``" v-if="data.cotizaciones[id_empresa].partidas[i]">{{data.cotizaciones[id_empresa].partidas[i].precio_unitario_format}}</td><td v-else></td>
                                                <td class="align_right" v-if="data.cotizaciones[id_empresa].partidas[i]" :class="data.cotizaciones[id_empresa].partidas[i].mejor_opcion?`mejor_opcion`:``">{{data.cotizaciones[id_empresa].partidas[i].descuento}}</td><td v-else></td>
                                                <td class="align_right" :class="data.cotizaciones[id_empresa].partidas[i].mejor_opcion?`mejor_opcion`:``" v-if="data.cotizaciones[id_empresa].partidas[i]">${{data.cotizaciones[id_empresa].partidas[i].importe}}</td><td v-else></td>
                                                <td v-if="data.cotizaciones[id_empresa].partidas[i]" :class="data.cotizaciones[id_empresa].partidas[i].mejor_opcion?`mejor_opcion`:``">{{data.cotizaciones[id_empresa].partidas[i].moneda}}</td><td v-else></td>
                                                <td class="align_right" :class="data.cotizaciones[id_empresa].partidas[i].mejor_opcion?`mejor_opcion`:``" v-if="data.cotizaciones[id_empresa].partidas[i]">${{data.cotizaciones[id_empresa].partidas[i].importe_moneda_conversion}}</td><td v-else></td>
                                                <td>
                                                    <span  v-if="data.cotizaciones[id_empresa].partidas[i]">
                                                        <input
                                                            type="number" @change="recalcular(i)"
                                                            :disabled="item.cantidad_disponible == 0 && data.cotizaciones[id_empresa].partidas[i].cantidad_asignada == ''"

                                                            class="form-control"
                                                            :name="`cantidad_asignada[${item.id_material}]`"
                                                            data-vv-as="Cantidad Asignada"
                                                            v-model="data.cotizaciones[id_empresa].partidas[i].cantidad_asignada"
                                                            v-validate="{max_value:item.cantidad_base, min_value:0}"
                                                            :class="{'is-invalid': errors.has(`cantidad_asignada[${item.id_material}]`)}"
                                                            id="cantidad_asignada">
                                                        <div class="invalid-feedback" v-show="errors.has(`cantidad_asignada[${item.id_material}]`)">{{ errors.first(`cantidad_asignada[${item.id_material}]`) }}</div>
                                                    </span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <button type="button" @click="borrarVolumenes()" class="btn btn-default pull-right" style="margin-left:5px">Borrar los Volumenes del Proveedor</button>
                                <button type="button" @click="cargarVolumenes()" class="btn btn-default pull-right">Cargar Todos los Volumenes a Proveedor</button>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" v-on:click="salir">
                            <i class="fa fa-angle-left"></i>
                            Regresar
                        </button>
                        <button type="button" @click="validate()" class="btn btn-primary">
                            <i class="fa fa-save"></i>
                            Guardar
                        </button>
                    </div>
                </div>
            </div>
        </div>
            <div class="modal" ref="modalJustificacion" tabindex="-1" role="dialog" v-if="data">
                <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">
                                <i class="fa fa-pencil"></i>Justificar Asignaciones</h5>
                            <button type="button" class="close" @click="cerrar()" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <p><b>Las siguientes partidas no son las mejores opciones cotizadas, favor de describir la justificación de su asignación.</b></p>
                                </div>
                                <div class="col-md-4">
                                    <div class="custom-control custom-switch pull-right" >
                                        <input type="checkbox" class="custom-control-input" id="cotizaciones_completas" v-model="replicar_justificacion" >
                                        <label class="custom-control-label" for="cotizaciones_completas" >Replicar Justificación</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row" v-for="(cotizacion, id_empresa) in data.cotizaciones" v-if="cotizacion.justificar">
                                    <div class="col-sm-12">
                                        <div class="table-responsive">
                                            <table class="table table-striped table-sm">
                                                <tr>
                                                    <td colspan="6" style="border:none">
                                                        <b>[{{cotizacion.folio_format}}] {{cotizacion.razon_social}}</b>
                                                    </td>
                                                </tr>
                                                <tr class="encabezado">
                                                    <th class="th_c350">
                                                        Descripción
                                                    </th>
                                                    <th class="unidad">
                                                        Unidad
                                                    </th>
                                                    <th class="th_c100">
                                                        Importe Pesos (MXN) (Asignado)
                                                    </th>
                                                    <th class="th_c100">
                                                        Importe Pesos (MXN) (Mejor Opción)
                                                    </th>
                                                    <th class="th_c100">
                                                        Diferencia
                                                    </th>
                                                    <th class="th_c100">
                                                        Cantidad Asignada
                                                    </th>
                                                    <th>
                                                        Justificación
                                                    </th>
                                                </tr>
                                                <tr v-for="(item, i) in data.items" v-if="cotizacion.partidas[i] !== null && cotizacion.partidas[i].mejor_opcion == false && cotizacion.partidas[i].cantidad_asignada > 0">
                                                    <td>
                                                        {{item.descripcion}}
                                                    </td>
                                                    <td>
                                                        {{item.unidad}}
                                                    </td>
                                                    <td class="td_money">
                                                        ${{getAsignadoPrecioMC(cotizacion.partidas[i].precio_con_descuento_mn, cotizacion.partidas[i].cantidad_asignada)}}
                                                    </td>
                                                    <td class="td_money">
                                                        ${{getMejorOpcionPrecioMC(i, cotizacion.partidas[i].cantidad_asignada)}}
                                                    </td>
                                                    <td class="td_money">
                                                        {{getPorcentajeDiferencia(i, cotizacion.partidas[i].precio_con_descuento_mn)}} %
                                                    </td>
                                                    <td class="td_money">
                                                        {{parseFloat(cotizacion.partidas[i].cantidad_asignada).formatMoney(2,'.',',')}}
                                                    </td>
                                                    <td>
                                                        <textarea
                                                            :disabled="!validaPrimeraPartida(id_empresa,cotizacion.partidas[i].id_material)"
                                                            v-on:keyup="keyupReplicarjustificacion()"
                                                            name="justificacion"
                                                            id="justificacion"
                                                            class="form-control"
                                                            v-model="cotizacion.partidas[i].justificacion"
                                                            v-validate="{required: true, max:500}"
                                                            data-vv-as="Justificación"
                                                            :class="{'is-invalid': errors.has('justificacion')}"
                                                        ></textarea>
                                                        <div class="invalid-feedback" v-show="errors.has('justificacion')">{{ errors.first('justificacion') }}</div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="cerrar()"><i class="fa fa-close"></i>Cerrar</button>
                            <button type="button" class="btn btn-primary" @click="validateModal()"><i class="fa fa-save"></i>Guardar</button>
                        </div>
                    </div>
                </div>
            </div>
    </span>

</template>

<script>
import {ModelListSelect} from 'vue-search-select';
import TablaDatosSolicitudCompra from "../solicitud-compra/partials/TablaDatosSolicitudCompra";
export default {
    name: "asignacion-proveedor-create",
    components: {ModelListSelect, TablaDatosSolicitudCompra},
    props:['id_solicitud', 'solicitud_compra'],
    data() {
        return {
            cargando: false,
            solicitudes:[],
            data:null,
            id_empresa:'',
            justificar:false,
            partidas_justificacion:[],
            replicar_justificacion:false,
        }
    },
    mounted() {
        // this.id_empresa = Object.keys(data.cotizaciones)[0];
        this.getCotizaciones();
    },
    computed: {

    },
    methods: {
        cerrar(){
            this.$validator.reset();
            this.$validator.errors.clear();
            $(this.$refs.modalJustificacion).modal('hide');
        },
        numeroFolioFormatAndObservaciones(item){
            return `[${item.numero_folio_format}] - [${item.observaciones}]`
        },
        cargarVolumenes(){
            let self = this;
            self.data.items.forEach(function (item, i){
                if(item.cantidad_disponible > 0){
                    self.data.cotizaciones[self.id_empresa].partidas[i]? self.data.cotizaciones[self.id_empresa].partidas[i].cantidad_asignada = item.cantidad_disponible:'';
                    item.cantidad_disponible = parseFloat(0).toFixed(4);
                }
            });
        },
        borrarVolumenes(){
            let self = this;
            self.data.items.forEach(function (item, i){
                if(self.data.cotizaciones[self.id_empresa].partidas[i]){
                    if(self.data.cotizaciones[self.id_empresa].partidas[i].cantidad_asignada > 0){
                        self.data.cotizaciones[self.id_empresa].partidas[i].cantidad_asignada = '';
                        item.cantidad_disponible = parseFloat(item.cantidad_base).toFixed(4);
                    }
                }
            });
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
        // getSolicitudes(){
        //     this.cargando = true;
        //     this.solicitudes = [];
        //     this.data = null;
        //     return this.$store.dispatch('compras/solicitud-compra/index', {
        //         params: {
        //             scope: ['cotizacion', 'conComplemento', 'ultimoAnio'],
        //             order: 'DESC',
        //             sort: 'numero_folio'
        //         }
        //     })
        //     .then(data => {
        //         this.solicitudes = data.data;
        //         this.cargando = false;
        //     })

        // },
        getCotizaciones(){
            this.cargando = true;
            this.data = null;
            return this.$store.dispatch('compras/solicitud-compra/getCotizaciones', {
                id: this.id_solicitud,
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
        recalcular(i){
            let asignadas = 0.0;

            Object.values(this.data.cotizaciones).forEach(partida =>{
                if(partida.partidas[i] && partida.partidas[i].cantidad_asignada !== ''){
                    asignadas = +asignadas + +partida.partidas[i].cantidad_asignada;
                }
            });

            if(asignadas > this.data.items[i].cantidad_base){
                if(this.data.items[i].cantidad_disponible === 0){
                    swal('¡Aviso!', 'El insumo ya no tiene cantidad pendiente por asignar', 'warning');
                }else{
                    swal('¡Aviso!', 'La cantidad asignada es mayor a la pendiente por asignar.', 'warning');
                }
                asignadas = +asignadas - +this.data.cotizaciones[this.id_empresa].partidas[i].cantidad_asignada;
                this.data.cotizaciones[this.id_empresa].partidas[i].cantidad_asignada = '';
            }
            this.data.items[i].cantidad_disponible = parseFloat(this.data.items[i].cantidad_base - asignadas).toFixed(4);

        },
        store() {
            this.cargando = true;
            return this.$store.dispatch('compras/asignacion/store', {
                id_solicitud:this.id_solicitud,
                cotizaciones:this.data.cotizaciones
            })
            .then((data) => {
                $(this.$refs.modalJustificacion).modal('hide');
                this.$router.push({name: 'asignacion-proveedor'});
            })
            .finally(() => {
                this.cargando = false;
            })
        },
        validate() {
            this.justificar = false;
            this.validar_partidas_justificadas();
            this.$validator.validate().then(result => {
                if (result && !this.justificar){
                    this.store();
                }
            });
        },
        validateModal() {
            this.$validator.validate().then(result => {
                if (result){
                    this.store();
                }
            });
        },
        validar_partidas_justificadas(){
            let self = this;
            let cant = 0;

            Object.entries(this.data.cotizaciones).forEach(([id_empresa, cotizacion]) =>{
                cotizacion.justificar = false;
                Object.entries(cotizacion.partidas).forEach(([i, partida]) =>{
                    if(partida !== null){
                        if(parseFloat(partida.cantidad_asignada) > 0 && partida.mejor_opcion == false && (partida.justificacion == '' || partida.justificacion != '')){
                            self.partidas_justificacion.push({id_transaccion:id_empresa,id_material:partida.id_material,pos_partida:i});
                            self.justificar = true;
                            cotizacion.justificar = true;
                        }
                    }
                });
            });
            if(!self.justificar){
                $(this.$refs.modalJustificacion).modal('hide');
            }else{
                this.replicar_justificacion = false;
                $(this.$refs.modalJustificacion).modal('show');
            }
        },
        validaPrimeraPartida(id_cotizacion, id_material){
                return !this.replicar_justificacion || (this.partidas_justificacion[0].id_transaccion === id_cotizacion && this.partidas_justificacion[0].id_material === id_material);
        },
        keyupReplicarjustificacion(){
            if(this.replicar_justificacion){
                let cotizaciones = this.data.cotizaciones
                let p_justf = this.partidas_justificacion;
                for(let i = 1; i<p_justf.length; i++){
                    cotizaciones[p_justf[i].id_transaccion].partidas[p_justf[i].pos_partida].justificacion = cotizaciones[p_justf[0].id_transaccion].partidas[p_justf[0].pos_partida].justificacion;
                }
            }
        },
        getMejorOpcionPrecioMC(i, ca_asig){
            let pu_mo = 0;
            Object.values(this.data.cotizaciones).forEach(cotizacion => {
                if(cotizacion.partidas[i].mejor_opcion){
                    pu_mo = parseFloat(cotizacion.partidas[i].precio_con_descuento_mn) * parseFloat(ca_asig);
                }
            });
            return pu_mo;
        },
        getAsignadoPrecioMC(p_u, c_a){
            let pu_asig = parseFloat(p_u) * parseFloat(c_a);
            return parseFloat(pu_asig).formatMoney(2,'.',',');
        },
        getPorcentajeDiferencia(i, importe_asignado){
            let p_dif = 0;
            Object.values(this.data.cotizaciones).forEach(cotizacion => {
                if(cotizacion.partidas[i].mejor_opcion){
                    p_dif = ((importe_asignado - cotizacion.partidas[i].precio_con_descuento_mn) / cotizacion.partidas[i].precio_con_descuento_mn)*100;
                }
            });
            return parseFloat(p_dif).formatMoney(2,'.',',');
        },
    },
    watch:{
        // id_solicitud(value){
        //     if(value != ''){
        //         // this.getCotizaciones(value);
        //     }
        // },
        replicar_justificacion(value){
            if(value){
                this.keyupReplicarjustificacion();
            }
        }
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
