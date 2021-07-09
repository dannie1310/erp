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
                                            <label for="id_contrato">Buscar Solicitud de Compra:</label>
                                                 <model-list-select
                                                     id="id_contrato"
                                                     name="id_contrato"
                                                     option-value="id"
                                                     v-model="id_solicitud"
                                                     :custom-text="idFolioObservaciones"
                                                     :list="solicitudes"
                                                     :placeholder="!cargando?'Seleccionar o buscar folio o concepto o descripcion':'Cargando...'">
                                                 </model-list-select>
                                            <div style="display:block" class="invalid-feedback" v-show="errors.has('id_solicitud')">{{ errors.first('id_solicitud') }}</div>
                                        </div>
                                    </div>
                                </div>
                                <DatosSolicitudCompra :solicitud_compra="solicitud"></DatosSolicitudCompra>
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
    import DatosSolicitudCompra from "../solicitud-compra/partials/DatosSolicitudCompra";
    //import DatosContratoProyectado from "../proyectado/partials/DatosContratoProyectado";
    export default {
        name: "selecciona-solicitud-compra",
        components: {
            DatosSolicitudCompra,
            /*DatosContratoProyectado,*/ ModelListSelect},
        data() {
            return {
                cargando: false,
                id_solicitud: '',
                solicitudes : [],
            }
        },
        mounted() {
            this.$store.commit('compras/solicitud-compra/SET_SOLICITUD', null);
            this.$validator.reset();
            this.getSolicitudes();

        },
        methods : {
            idFolioObservaciones (item)
            {
                return `[${item.numero_folio_format}]-[ ${item.concepto} ]-[ ${item.observaciones} ]`;
            },
            salir()
            {
                 this.$router.push({name: 'presupuesto'});
            },
            find() {
                this.cargando = true;
                this.$store.commit('compras/solicitud-compra/SET_SOLICITUD', null);
                return this.$store.dispatch('compras/solicitud-compra/find', {
                    id: this.id_solicitud,
                    params:{}
                }).then(data => {
                    this.$store.commit('compras/solicitud-compra/SET_SOLICITUD', data);

                    this.cargando = false;
                })
            },
            getSolicitudes() {
                this.solicitudes = [];
                this.cargando = true;
                return this.$store.dispatch('compras/solicitud-compra/index', {
                    params: {
                        scope: ['conItems','areasCompradorasAsignadas','conAutorizacion'],
                        order: 'DESC',
                        sort: 'numero_folio'
                    }
                })
                    .then(data => {
                        this.solicitudes = data.data;
                    })
                    .finally(()=>{
                        this.cargando = false;
                    })
            },
            validate() {

                this.$validator.validate().then(result => {
                    if (result) {

                        this.$router.push({name: 'cotizacion-create', params: {id_solicitud: this.solicitud.id}});
                    }

                });
            },
        },
        computed: {
            solicitud(){
                return this.$store.getters['compras/solicitud-compra/currentSolicitud'];
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
