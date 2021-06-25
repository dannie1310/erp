<template>
    <span>
        <div class="btn-group">
            <router-link  :to="{ name: 'solicitud-cambio-aplicar', params: {id: value.id}}" v-if="$root.can('aplicar_solicitud_cambio_subcontrato') && value.estado == 0" type="button" class="btn btn-sm btn-outline-danger" title="Aplicar">
                <i class="fa fa-thumbs-o-up"></i>
            </router-link>
            <router-link  :to="{ name: 'solicitud-cambio-cancelar', params: {id: value.id}}" v-if="$root.can('cancelar_solicitud_cambio_subcontrato') && value.estado == 0" type="button" class="btn btn-sm btn-outline-danger" title="Cancelar">
                <i class="fa fa-ban"></i>
            </router-link>
            <router-link  :to="{ name: 'solicitud-cambio-show', params: {id: value.id}}" v-if="$root.can('consultar_solicitud_cambio_subcontrato')" type="button" class="btn btn-sm btn-outline-secondary" title="Ver">
                <i class="fa fa-eye"></i>
            </router-link>
            <button @click="eliminar" type="button" class="btn btn-sm btn-outline-danger " title="Eliminar" v-if="value.delete && (value.estado == 0) && 1==2"  v-bind:id="value.id">
                <i class="fa fa-trash"></i>
            </button>
            <PDF v-bind:id="value.id" v-if="$root.can('consultar_solicitud_cambio_subcontrato')"></PDF>
            <Relaciones v-bind:transaccion="value.transaccion"/>
            <router-link  :to="{ name: 'solicitud-cambio-documentos', params: {id: value.id}}" v-if="$root.can('consultar_solicitud_cambio_subcontrato') && $root.can('consultar_archivos_transaccion')" type="button" class="btn btn-sm btn-outline-primary" title="Ver Documentos">
                <i class="fa fa-folder-open"></i>
            </router-link>
        </div>
    </span>
</template>

<script>
    import PDF from '../Formato';
    import Relaciones from "../../../globals/ModalRelaciones";
    export default {
        name: "action-buttons",
        components: {PDF, Relaciones},
        props: ['value'],
        data() {
            return {
                aplicando: false,
                revirtiendo: false,
                guardando: false,
                configuracion : []
            }
        },

        mounted() {
            $(this.$refs.resumen).on('hidden.bs.modal', () => {
                this.aplicando = false;
                this.revirtiendo = false;
            })
        },

        methods: {
            resumen(opcion) {
                if (opcion == 'aprobar') {this.aplicando = true;}
                else {this.revirtiendo = true;}
                $(this.$refs.resumen).appendTo('body')
                $(this.$refs.resumen).modal('show');
                this.getConfiguraciones()
            },

            aprobar() {
                this.guardando = true;
                return this.$store.dispatch('contratos/solicitud_cambio/aprobar' ,{ id: this.value.id })
                    .then(() => {
                        this.$store.commit('contratos/solicitud_cambio/APROBAR_solicitud_cambio', this.value.id);
                    })
                    .finally(() => {
                        this.guardando = false;
                        $(this.$refs.resumen).modal('hide');
                    })
            },

            desaprobar() {
                this.guardando = true;
                return this.$store.dispatch('contratos/solicitud_cambio/revertirAprobacion', {id: this.value.id})
                    .then(() => {
                        this.$store.commit('contratos/solicitud_cambio/REVERTIR_APROBACION', this.value.id);
                    })
                    .finally(() => {
                        this.guardando = false;
                        $(this.$refs.resumen).modal('hide');
                    })
            },
            show(){
                this.$router.push({ name:'solicitud_cambio-show', params: {id: this.value.id}});
            },
            edit(){
                this.$router.push({ name:'solicitud_cambio-edit', params: {id: this.value.id}});
            },
            cancelar() {
                this.$router.push({name: 'solicitud_cambio-cancelar', params: {id: this.value.id}});
            },
            getConfiguraciones() {
                this.cargando = true;
                return this.$store.dispatch('finanzas/solicitud_cambio/index', { params: this.query1 } )
                    .then(data => {
                       this.configuracion = data.data[0]
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            },
        }
    }
</script>
