<template>
    <span>
        <div class="d-flex flex-row-reverse" v-if="!cargando">
           <div class="p-2">
                <button type="button" @click="autorizar()" v-if="$root.can('autorizar_variacion_volumen') && solicitud && solicitud.id_estatus == 1" :disabled="cargando" class="btn btn-primary float-right" >
                    Autorizar
                </button>
            </div>
           <div class="p-2">
                <RechazarVariacionVolumen @created="find()" v-if="solicitud && solicitud.id_estatus == 1" v-bind:id="id" ></RechazarVariacionVolumen>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="invoice p-3 mb-3">
                    <div class="row">
                        <div class="col-10">
                            <h4><i class="fa fa-spin fa-spinner" v-if="cargando"></i>
                                <i class="fa fa-list-alt" v-else></i>  DETALLE DE LA SOLICITUD </h4>
                        </div>
                        <div class="col-2">
                            <PdfVariacion v-bind:id="id"></PdfVariacion>
                        </div>
                    </div>
                    <div class="modal-body" v-if="solicitud">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><b>Tipo de Solicitud de Cambio: </b></label>
                                    {{ solicitud.tipo_orden }}
                                </div>
                            </div>
                             <div class="col-md-2">
                                <div class="form-group">
                                    <label><b>Número de Folio: </b></label>
                                    # {{ solicitud.numero_folio }}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label><b>Usuario que Solicita: </b></label>
                                    {{ solicitud.usuario.nombre }}
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label><b>Fecha de solicitud: </b></label>
                                    {{ solicitud.fecha_solicitud }}
                                </div>
                            </div>
                            
                           
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><b>Area Solicitante: </b></label>
                                    {{ solicitud.area_solicitante }}
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label><b>Motivo: </b></label>
                                    {{ solicitud.motivo }}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label><b>Estatus: </b></label>
                                    {{ solicitud.estatus }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" v-if="solicitud">
            <div class="col-12">
                <div class="invoice p-3 mb-3">
                    <div class="modal-body">
                        <div class="col-12 table-responsive-xl">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th class="text-center">Descripción</th>
                                        <th class="text-center">Unidad</th>
                                        <th class="text-center">Precio Unitario</th>
                                        <th class="text-center">Volumen Original</th>
                                        <th class="text-center">Volumen del Cambio</th>
                                        <th class="text-center">Volumen Actualizado</th>
                                        <th class="text-center">Importe Original</th>
                                        <th class="text-center">Importe del Cambio</th>
                                        <th class="text-center">Importe Actualizado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(partida, i) in solicitud.partidas.data">
                                        <td>{{i + 1}}</td>
                                        <td :title="partida.descripcion">{{partida.descripcion_format}}</td>
                                        <td>{{partida.unidad}}</td>
                                        <td class="text-right">{{partida.precio_unitario_original_format}}</td>
                                        <td>{{partida.cantidad_presupuestada_original_format}}</td>
                                        <td>{{partida.variacion_volumen_format}}</td>
                                        <td>{{partida.cantidad_presupuestada_nueva_format}}</td>
                                        <td class="text-right">{{partida.importe_original_format}}</td>
                                        <td class="text-right">{{partida.importe_cambio_format}}</td>
                                        <td class="text-right">{{partida.importe_actualizado_format}}</td>
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
import PdfVariacion from './partials/FormatoVariacionVolumen';
import RechazarVariacionVolumen from './partials/RechazarVariacionVolumen';
export default {
    name: "variacion-volumen-show",
    components: {PdfVariacion, RechazarVariacionVolumen},
    props: ['id'],
    data() {
        return {
            cargando:false,
        }
    },
    methods: {
        find() {
            this.cargando = true;
            this.$store.commit('control-presupuesto/variacion-volumen/SET_VARIACION', null);
            return this.$store.dispatch('control-presupuesto/variacion-volumen/find', {
                id: this.id,
                params: {
                    include: ['partidas'],
                }
            }).then(data => {
                this.$store.commit('control-presupuesto/variacion-volumen/SET_VARIACION', data);
            }) .finally(() => {
                this.cargando = false;
            })
        },
        autorizar(){
            return this.$store.dispatch('control-presupuesto/variacion-volumen/autorizar', {
                id: this.id,
                params: {
                    include: ['partidas'],
                }
            }).then(data => {
                this.find();
            }) .finally(() => {
                this.cargando = false;
            })
        },
    },
    computed: {
        solicitud() {
            return this.$store.getters['control-presupuesto/variacion-volumen/currentVariacion']
        },
    },
    mounted() {
        this.$Progress.start();
        this.find()
        .finally(() => {
            this.$Progress.finish();
        })
    }
}
</script>

<style>

</style>