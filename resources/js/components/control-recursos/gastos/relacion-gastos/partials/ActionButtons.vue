<template>
    <div class="btn-group">
        <router-link  :to="{ name: 'relacion-gasto-show', params: {id: value.id}}" type="button" class="btn btn-sm btn-outline-secondary" title="Consultar">
            <i class="fa fa-eye"></i>
        </router-link>
        <router-link v-if="value.edit" :to="{ name: 'relacion-gasto-edit', params: {id: value.id}}" type="button" class="btn btn-sm btn-outline-info" title="Editar">
            <i class="fa fa-pencil"></i>
        </router-link>
        <button v-if="value.cerrar" @click="cerrar" type="button" class="btn btn-sm btn-outline-warning" title="Cerrar">
            <i class="fa fa-lock"></i>
        </button>
        <button v-if="value.abrir" @click="abrir" type="button" class="btn btn-sm btn-outline-info" title="Abrir">
            <i class="fa fa-unlock"></i>
        </button>
        <PDF v-bind:id="value.id" v-if="value.pdf"></PDF>
        <router-link :to="{ name: 'relacion-gasto-delete', params: {id: value.id}}" v-if="value.delete" type="button" class="btn btn-sm btn-outline-danger" title="Eliminar">
            <i class="fa fa-trash"></i>
        </router-link>
        <router-link :to="{ name: 'relacion-gasto-reembolso-x-solicitud', params: {id: value.id}}" v-if="value.solicitar_reembolso" type="button" class="btn btn-sm btn-outline-info" title="Solicitar Reembolso X Solicitud">
            <i class="fa fa-file-o"></i>
        </router-link>
        <router-link :to="{ name: 'reembolso-x-solicitud', params: {id: value.id_documento}}" v-if="value.reembolso_x_solicitud" type="button" class="btn btn-sm btn-outline-dark" title="Reembolso X Solicitud">
            <i class="fa fa-file-o"></i>
        </router-link>
        <router-link :to="{ name: 'relacion-gasto-reembolso-x-caja', params: {id: value.id}}" v-if="value.solicitar_reembolso_caja" type="button" class="btn btn-sm btn-outline-info" title="Solicitar Reembolso X Caja">
            <i class="fa fa-archive"></i>
        </router-link>
        <router-link :to="{ name: 'reembolso-x-caja', params: {id: value.id_documento}}" v-if="value.reembolso_x_solicitud_caja" type="button" class="btn btn-sm btn-outline-dark" title="Reembolso X Caja Chica">
            <i class="fa fa-archive"></i>
        </router-link>
        <router-link :to="{ name: '  relacion-gasto-pago-a-proveedor', params: {id: value.id}}" v-if="value.solicitud_pago_a_proveedor" type="button" class="btn btn-sm btn-outline-info" title="Solicitar Pago A Proveedor">
            <i class="fa fa-upload"></i>
        </router-link>
        <router-link :to="{ name: 'reembolso-pago-a-proveedor', params: {id: value.id_documento}}" v-if="value.reembolso_pago_a_proveedor" type="button" class="btn btn-sm btn-outline-dark" title="Reembolso X Caja Chica">
            <i class="fa fa-upload"></i>
        </router-link>




    </div>
</template>

<script>
    import PDF from "../FormatoRelacionGasto";
    export default {
        name: "relacion-gastos-action-buttons",
        components: { PDF },
        props: ['value'],
        methods: {
            cerrar() {
                return this.$store.dispatch('controlRecursos/relacion-gasto/close', {
                    id: this.value.id
                })
                    .then(data => {
                        this.$store.commit('controlRecursos/relacion-gasto/UPDATE_RELACION', data);
                    })
            },
            abrir() {
                return this.$store.dispatch('controlRecursos/relacion-gasto/open', {
                    id: this.value.id
                })
                    .then(data => {
                        this.find();
                    })
            },
            find() {
                return this.$store.dispatch('controlRecursos/relacion-gasto/find', {
                    id: this.value.id,
                    params:{include: []}
                }).then(data => {
                    this.$store.commit('controlRecursos/relacion-gasto/UPDATE_RELACION', data);
                })
            },
        }
    }
</script>
