<template>
    <span>
        <nav>
            <div class="row">
                <div class="col-12">
                    <div class="invoice p-3 mb-3">
                        <form role="form" @submit.prevent="validate">
                            <div class="modal-body">
                                <div class="row justify-content-between">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                        <label for="id_invitacion">Buscar Invitación:</label>
                                                 <model-list-select
                                                     id="id_invitacion"
                                                     name="id_invitacion"
                                                     option-value="id"
                                                     v-model="id_invitacion"
                                                     :custom-text="idFolioObservaciones"
                                                     :list="invitaciones"
                                                     :placeholder="!cargando?'Seleccionar o buscar por folio u observación':'Cargando...'">
                                                 </model-list-select>
                                            <div style="display:block" class="invalid-feedback" v-show="errors.has('id_invitacion')">{{ errors.first('id_invitacion') }}</div>
                                        </div>
                                    </div>
                                </div>
                                <DatosSolicitud :solicitud="solicitud"></DatosSolicitud>
                                <DatosContratoProyectado :contrato_proyectado="solicitud"></DatosContratoProyectado>
                            </div>

                             <div class="modal-footer">
                                 <button type="button" class="btn btn-secondary" v-on:click="salir">
                                        <i class="fa fa-angle-left"></i>
                                        Regresar</button>
                                    <button type="submit" :disabled="solicitud == null" class="btn btn-primary">
                                        Continuar
                                        <i class="fa fa-angle-right"></i>
                                    </button>
                             </div>
                        </form>
                    </div>
                </div>
            </div>
        </nav>
    </span>
</template>

<script>
    import {ModelListSelect} from 'vue-search-select';
    import DatosSolicitud from './partials/DatosSolicitud';
    import DatosContratoProyectado from '../presupuesto/partials/DatosContratoProyectado.vue';
    import invitacion from "../../../store/modules/padronProveedores/invitacion";
    export default {
        name: "cotizacion-proveedor-seleccionar-solicitud",
        components: {
            DatosSolicitud, ModelListSelect,DatosContratoProyectado},
        data() {
            return {
                cargando: false,
                id_invitacion: '',
                invitaciones : [],
                solicitud : null,
                invitacion : null,
            }
        },
        mounted() {
            this.$store.commit('padronProveedores/invitacion/SET_INVITACION', null);
            this.$validator.reset();
            this.getInvitaciones();
        },
        methods : {
            idFolioObservaciones (item)
            {
                return `[${item.numero_folio_format}]-[ ${item.observaciones} ]-[ ${item.transaccion.tipo.descripcion}]-[ ${item.transaccion.numero_folio_format} ]`;
            },
            salir()
            {
                this.$router.push({name: 'cotizacion-proveedor'});
            },
            find() {
                this.cargando = true;
                this.$store.commit('padronProveedores/invitacion/SET_INVITACION', null);
                return this.$store.dispatch('padronProveedores/invitacion/find', {
                    id: this.id_invitacion,
                    params:{include: ['solicitud_compra_cotizar']}
                }).then(data => {
                    this.invitacion = data;
                    this.solicitud = data.solicitud_compra
                    this.cargando = false;
                })
            },
            getInvitaciones(){
                this.invitaciones = [];
                this.cargando = true;

                return this.$store.dispatch('padronProveedores/invitacion/index', {
                    params:{ include: ['transaccion'], scope: ['invitadoAutenticado'], sort: 'id', order: 'desc'}

                })
                    .then(data => {
                        this.invitaciones = data;
                    })
                    .finally(()=>{
                        this.cargando = false;
                    })
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        if(this.solicitud.tipo_transaccion == 17){
                            if(this.invitacion.cotizacion){
                                this.$router.push({name: 'cotizacion-proveedor-edit', params: {id_invitacion: this.id_invitacion}});
                            }else{
                                this.$router.push({name: 'cotizacion-proveedor-create', params: {id_invitacion: this.id_invitacion}});
                            }
                        }else if(this.solicitud.tipo_transaccion == 49){
                            this.$router.push({name: 'presupuesto-proveedor-create', params: {id_solicitud: this.id_invitacion}});
                        }   
                    }
                });
            },
        },
        watch: {
            id_invitacion(value)
            {
                if(value !== '' && value !== null && value !== undefined)
                {
                    this.find();
                }
            },
        }
    }
</script>

<style scoped>

</style>
