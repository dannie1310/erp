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
        <div class="card" v-else>
            <div class="card-body">
                <DatosContratoProyectado v-if="contrato" :contrato_proyectado="contrato"></DatosContratoProyectado>
                <div class="row">
                    <div class="col-md-12">
                        <span style="color: #6c757d" class="pull-right">{{data.cantidad_presupuestos}} Presupuestos registrados</span>
                    </div>
                    <div class="col-md-12" v-for="(presupuesto, id_transaccion) in data.presupuestos">
                        <div class=" table-responsive" v-if="presupuesto.partidas_asignadas">
                            
                            <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th colspan="15" class="text-left"><b>[{{presupuesto.numero_folio_format}}] {{ presupuesto.razon_social }}</b></th>
                                    </tr>
                                    <tr>
                                        <th>#</th>
                                        <th style="width: 18%;">Descripción</th>
                                        <th style="width: 18%;">Destino</th>
                                        <th style="width: 4%;">Unidad</th>
                                        <th style="width: 6%;">Cantidad Solicitada</th>
                                        <th style="width: 6%;">Cantidad Pendiente Asignar</th>

                                        <th class="bg-gray-light ">Precio Unitario Antes Descto.</th>
                                        <th class="bg-gray-light ">Precio Total Antes Descto.</th>
                                        <th class="bg-gray-light">% Descuento</th>
                                        <th class="bg-gray-light ">Precio Unitario</th>
                                        <th class="bg-gray-light ">Precio Total</th>
                                        <th class="bg-gray-light">Moneda</th>
                                        <th class="bg-gray-light ">Precio Total Moneda Conversión</th>
                                        <th style="width: 6%;" class="bg-gray-light">Observaciones</th>
                                        <th class="bg-gray-light th_c100">Cantidad Asignada</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, i) in data.items" v-if="item.item_pendiente && presupuesto.partidas[i] != null">
                                        <td>{{ i+1}}</td>
                                        <td :title="item.descripcion">{{item.descripcion_corta}}</td>
                                        <td :title="item.destino">{{item.destino_corto}}</td>
                                        <td>{{item.unidad}}</td>
                                        <td style="text-align: right">{{parseFloat(item.cantidad_solicitada).formatMoney(4,'.',',')}}</td>
                                        <td style="text-align: right">{{parseFloat(item.cantidad_disponible).formatMoney(4,'.',',')}}</td>
                                        <td style="text-align: right" :class="data.presupuestos[id_transaccion].partidas[i].mejor_opcion?`mejor_opcion`:``" v-if="data.presupuestos[id_transaccion].partidas[i]">{{data.presupuestos[id_transaccion].partidas[i].precio_unitario}}</td><td v-else></td>
                                        <td style="text-align: right" :class="data.presupuestos[id_transaccion].partidas[i].mejor_opcion?`mejor_opcion`:``" v-if="data.presupuestos[id_transaccion].partidas[i]">{{data.presupuestos[id_transaccion].partidas[i].precio_total_antes_desc}}</td><td v-else></td>
                                        <td style="text-align: right" :class="data.presupuestos[id_transaccion].partidas[i].mejor_opcion?`mejor_opcion`:``" v-if="data.presupuestos[id_transaccion].partidas[i]">{{data.presupuestos[id_transaccion].partidas[i].descuento}}</td><td v-else></td>
                                        <td style="text-align: right" :class="data.presupuestos[id_transaccion].partidas[i].mejor_opcion?`mejor_opcion`:``" v-if="data.presupuestos[id_transaccion].partidas[i]"> {{data.presupuestos[id_transaccion].partidas[i].precio_unitario_con_desc}}</td><td v-else></td>
                                        <td style="text-align: right" :class="data.presupuestos[id_transaccion].partidas[i].mejor_opcion?`mejor_opcion`:``" v-if="data.presupuestos[id_transaccion].partidas[i]"> {{data.presupuestos[id_transaccion].partidas[i].precio_total_con_desc}}</td><td v-else></td>
                                        <td :class="data.presupuestos[id_transaccion].partidas[i].mejor_opcion?`mejor_opcion`:``" v-if="data.presupuestos[id_transaccion].partidas[i]">{{data.presupuestos[id_transaccion].partidas[i].moneda}}</td><td v-else></td>
                                        <td style="text-align: right" :class="data.presupuestos[id_transaccion].partidas[i].mejor_opcion?`mejor_opcion`:``" v-if="data.presupuestos[id_transaccion].partidas[i]"> {{data.presupuestos[id_transaccion].partidas[i].importe_moneda_conversion}}</td><td v-else></td>
                                        <td style="text-align: right" :class="data.presupuestos[id_transaccion].partidas[i].mejor_opcion?`mejor_opcion`:``" v-if="data.presupuestos[id_transaccion].partidas[i]"> {{data.presupuestos[id_transaccion].partidas[i].observaciones}}</td><td v-else></td>
                                        <td style="text-align: right" :class="data.presupuestos[id_transaccion].partidas[i].mejor_opcion?`mejor_opcion`:``" v-if="data.presupuestos[id_transaccion].partidas[i]"> {{parseFloat(data.presupuestos[id_transaccion].partidas[i].cantidad_asignada).formatMoney(4,'.',',')}}</td><td v-else></td>
                                        
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="button" @click="validate()" class="btn btn-primary pull-right ml-1">
                    <i class="fa fa-save"></i>
                    Guardar
                </button>
                <button type="button" class="btn btn-secondary pull-right" v-on:click="salir">
                    <i class="fa fa-angle-left"></i>
                    Regresar
                </button>
            </div>
        </div>

        <!-- Modal Justificaciones -->
        <div class="modal" ref="modalJustificacion" tabindex="-1" role="dialog" v-if="data">
            <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">
                            <i class="fa fa-pencil"></i>Justificar Asignaciones</h5>
                        <button type="button" class="close" @click="cerrarModal()" aria-label="Close">
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
                        <div class="row" v-for="(presupuesto, id_transaccion) in data.presupuestos" v-if="presupuesto.justificar">
                            <div class="col-sm-12">
                                <table class="table table-striped table-sm">
                                    <tr>
                                        <td colspan="6" style="border:none">
                                            <b>[{{presupuesto.numero_folio_format}}] {{presupuesto.razon_social}}</b>
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
                                    <tr v-for="(item, i) in data.items" v-if="presupuesto.partidas[i] !== null && presupuesto.partidas[i].mejor_opcion == false && presupuesto.partidas[i].cantidad_asignada > 0">
                                        <td>
                                            {{item.descripcion}}
                                        </td>
                                        <td>
                                            {{item.unidad}}
                                        </td>
                                        <td class="td_money">
                                            ${{getAsignadoPrecioMC(presupuesto.partidas[i])}}
                                        </td>
                                        <td class="td_money">
                                            ${{getMejorOpcionPrecioMC(presupuesto.partidas[i].id_concepto, presupuesto.partidas[i].cantidad_asignada)}}
                                        </td>
                                        <td class="td_money">
                                            {{getPorcentajeDiferencia(presupuesto.partidas[i].id_concepto, presupuesto.partidas[i].precio_unitario_con_desc_sf)}} %
                                        </td>
                                        <td class="td_money">
                                            {{parseFloat(presupuesto.partidas[i].cantidad_asignada).formatMoney(2,'.',',')}}
                                        </td>
                                        <td>
                                            <textarea
                                                :disabled="validaPrimeraPartida(id_transaccion,presupuesto.partidas[i].id_concepto)"
                                                v-on:keyup="keyupReplicarjustificacion()"
                                                name="justificacion"
                                                id="justificacion"
                                                class="form-control"
                                                v-model="presupuesto.partidas[i].justificacion"
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
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="cerrarModal()"><i class="fa fa-close"></i>Cerrar</button>
                        <button type="button" class="btn btn-primary" @click="validateModal()"><i class="fa fa-save"></i>Guardar</button>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
import {ModelListSelect} from 'vue-search-select';
import DatosContratoProyectado from "../proyectado/partials/DatosContratoProyectado";
export default {
    name: "asignacion-contratistas-layout-create",
    props: ['id_contrato', 'data'],
    components: {DatosContratoProyectado, ModelListSelect},
    data() {
        return {
            cargando: true,
            contratos:[],
            id_transaccion:'',
            justificar: false,
            partidas_justificacion:[],
            replicar_justificacion:false,
        }
    },
    mounted() {
        this.find();
    },
    computed: {
        contrato(){
            return this.$store.getters['contratos/contrato-proyectado/currentContrato'];
        },
    },
    methods: {
        find() {
            this.cargando = true;
            this.$store.commit('contratos/contrato-proyectado/SET_CONTRATO', null);
            return this.$store.dispatch('contratos/contrato-proyectado/find', {
                id: this.id_contrato,
                params:{include: ['conceptos']}
            }).then(data => {
                this.$store.commit('contratos/contrato-proyectado/SET_CONTRATO', data);
                this.setCotizaciones();
                
            }).finally(()=>{
                
            });
        },
        setCotizaciones(){
            this.id_transaccion = Object.keys(this.data.presupuestos)[0];
            this.cargando = false;
        },
        store() {
            // this.cargando = true;
            return this.$store.dispatch('contratos/asignacion-contratista/store', {
                id_contrato:this.id_contrato,
                origen:this.data.origen,
                presupuestos:this.data.presupuestos
            })
            .then((data) => {
                $(this.$refs.modalJustificacion).modal('hide');
                this.$router.push({name: 'asignacion-contratista'});
            })
            .finally(() => {
                // this.cargando = false;
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
        salir(){
            swal({
                title: "Salir de Asignación de Contratista",
                text: "¿Está seguro de que quiere salir del registro de asignación de contratistas por layout?",
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
                    this.$router.push({name: 'asignacion-contratista-selecciona-contrato-proyectado'});
                }
            });
        },
        cerrarModal(){
            this.$validator.reset();
            this.$validator.errors.clear();
            $(this.$refs.modalJustificacion).modal('hide');
        },
        validar_partidas_justificadas(){
            let self = this;
            self.partidas_justificacion = [];
            Object.entries(this.data.presupuestos).forEach(([id_transaccion, presupuesto]) =>{
                presupuesto.justificar = false;
                Object.entries(presupuesto.partidas).forEach(([i, partida]) =>{
                    if(partida !== null){
                        if(parseFloat(partida.cantidad_asignada) > 0 && partida.mejor_opcion == false && (partida.justificacion == '' || partida.justificacion != '')){
                            self.partidas_justificacion.push({id_transaccion:id_transaccion,id_concepto:partida.id_concepto,pos_partida:i});
                            self.justificar = true;
                            presupuesto.justificar = true;
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
        getAsignadoPrecioMC(partida){
            let pu_asig = parseFloat(partida.precio_unitario_con_desc_sf) * parseFloat(partida.cantidad_asignada) * parseFloat(partida.tipo_cambio);
            return parseFloat(pu_asig).formatMoney(2,'.',',');
        },
        getMejorOpcionPrecioMC(i, ca_asig){
            let pu_mo = 0;
            pu_mo = parseFloat(this.data.precios_menores[i]) * parseFloat(ca_asig);
            return parseFloat(pu_mo).formatMoney(2,'.',',');
        },
        getPorcentajeDiferencia(i, importe_asignado){
            let p_dif = 0;
            p_dif = ((importe_asignado - this.data.precios_menores[i]) / this.data.precios_menores[i])*100;
            return parseFloat(p_dif).formatMoney(2,'.',',');
        },
        validaPrimeraPartida(id_transaccion, id_concepto){
            if (this.replicar_justificacion){
                return !(this.partidas_justificacion[0].id_transaccion === id_transaccion && this.partidas_justificacion[0].id_concepto === id_concepto);
            }
            return false;
        },
        keyupReplicarjustificacion(){
            if(this.replicar_justificacion){
                let presupuesto = this.data.presupuestos
                let p_justf = this.partidas_justificacion;
                for(let i = 1; i<p_justf.length; i++){
                    presupuesto[p_justf[i].id_transaccion].partidas[p_justf[i].pos_partida].justificacion = presupuesto[p_justf[0].id_transaccion].partidas[p_justf[0].pos_partida].justificacion;
                }
            }
        },
        validateModal() {
            this.$validator.validate().then(result => {
                if (result){
                    this.store();
                }
            });
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
