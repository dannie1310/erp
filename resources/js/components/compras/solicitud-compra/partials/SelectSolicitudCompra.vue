<template>
    <span>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="id_solicitud">Buscar Solicitud de Compra:</label>
                         <model-list-select
                             id="id_solicitud"
                             name="id_solicitud"
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
        <div class="row">
            <div class="col-md-12">
                <DatosSolicitudCompra :solicitud_compra="solicitud"></DatosSolicitudCompra>
            </div>

        </div>
    </span>
</template>

<script>
    import {ModelListSelect} from 'vue-search-select';
    import DatosSolicitudCompra from "./DatosSolicitudCompra";
    export default {
        name: "select-solicitud-compra",
        components: {
            DatosSolicitudCompra,
            ModelListSelect},
        data() {
            return {
                cargando: false,
                id_solicitud: '',
                solicitudes : [],
            }
        },
        mounted() {
            //this.$store.commit('compras/solicitud-compra/SET_SOLICITUD', null);

            this.getSolicitudes();

        },
        methods : {
            idFolioObservaciones (item)
            {
                return `[${item.numero_folio_format}]-[ ${item.concepto} ]-[ ${item.observaciones} ]`;
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
                        scope: ['conItems','areasCompradorasAsignadas','conAutorizacion', 'ultimoAnio'],
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
