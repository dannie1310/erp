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
                                            <label for="id_solicitud">Buscar Solicitud o Contrato:</label>
                                                 <model-list-select
                                                     id="id_solicitud"
                                                     name="id_solicitud"
                                                     option-value="id"
                                                     v-model="id_solicitud"
                                                     :custom-text="idFolioObservaciones"
                                                     :list="solicitudes"
                                                     :placeholder="!cargando?'Seleccionar o buscar folio o observación':'Cargando...'">
                                                 </model-list-select>
                                            <div style="display:block" class="invalid-feedback" v-show="errors.has('id_solicitud')">{{ errors.first('id_solicitud') }}</div>
                                        </div>
                                    </div>
                                </div>
                                <DatosSolicitud :solicitud="solicitud"></DatosSolicitud>
                            </div>

                             <div class="modal-footer">
                                 <button type="button" class="btn btn-secondary" v-on:click="salir">
                                        <i class="fa fa-angle-left"></i>
                                        Regresar</button>
                                    <button type="submit" :disabled="id_solicitud == ''" class="btn btn-primary">
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
    export default {
        name: "cotizacion-proveedor-seleccionar-solicitud",
        components: {
            DatosSolicitud, ModelListSelect},
        data() {
            return {
                cargando: false,
                id_solicitud: '',
                solicitudes : [],
                solicitud : null
            }
        },
        mounted() {
            this.$store.commit('padronProveedores/invitacion/SET_INVITACION', null);
            this.$validator.reset();
            this.getSolicitudes();
        },
        methods : {
            idFolioObservaciones (item)
            {
                return `[${item.numero_folio_format}]-[ ${item.observaciones} ]`;
            },
            salir()
            {
                this.$router.push({name: 'cotizacion-proveedor'});
            },
            find() {
                this.cargando = true;
                this.$store.commit('padronProveedores/invitacion/SET_INVITACION', null);
                return this.$store.dispatch('padronProveedores/invitacion/getSolicitud', {
                    id: this.id_solicitud,
                    params:{}
                }).then(data => {
                    this.solicitud = data
                    this.cargando = false;
                })
            },
            getSolicitudes() {
                this.solicitudes = [];
                this.cargando = true;
                return this.$store.dispatch('padronProveedores/invitacion/getSolicitudes', {
                    params: { }
                })
                    .then(data => {
                        this.solicitudes = data;
                    })
                    .finally(()=>{
                        this.cargando = false;
                    })
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.$router.push({name: 'cotizacion-proveedor-create', params: {id_solicitud: this.solicitud.id_invitacion}});
                    }
                });
            },
        },
        watch: {
            id_solicitud(value)
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
