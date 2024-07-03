<template>
    <div class="btn-group">
        <router-link v-if="value.con_cfdi"  :to="{ name: 'factura-recurso-show', params: {id: value.id}}" type="button" class="btn btn-sm btn-outline-secondary" title="Consultar">
            <i class="fa fa-eye"></i>
        </router-link>
        <router-link v-else  :to="{ name: 'documento-recurso-show', params: {id: value.id}}" type="button" class="btn btn-sm btn-outline-secondary" title="Consultar">
            <i class="fa fa-eye"></i>
        </router-link>

        <router-link v-if="value.con_cfdi && value.edit" :to="{ name: 'factura-recurso-edit', params: {id: value.id}}" type="button" class="btn btn-sm btn-outline-info" title="Editar">
            <i class="fa fa-pencil"></i>
        </router-link>
        <router-link v-if="!value.con_cfdi && value.edit" :to="{ name: 'documento-recurso-edit', params: {id: value.id}}" type="button" class="btn btn-sm btn-outline-info" title="Editar">
            <i class="fa fa-pencil"></i>
        </router-link>

        <router-link v-if="value.con_cfdi && value.delete" :to="{ name: 'factura-recurso-delete', params: {id: value.id}}" type="button" class="btn btn-sm btn-outline-danger" title="Eliminar">
            <i class="fa fa-trash"></i>
        </router-link>
        <router-link v-if="!value.con_cfdi && value.delete" :to="{ name: 'documento-recurso-delete', params: {id: value.id}}" type="button" class="btn btn-sm btn-outline-danger" title="Eliminar">
            <i class="fa fa-trash"></i>
        </router-link>
        <DescargaXML v-if="value.xml_ifs" v-bind:id="value.id" />
        <button @click="correo" v-if="value.xml_ifs" type="button" class="btn btn-sm btn-outline-success" title="Envio Correo de XML">
            <i class="fa fa-envelope"></i>
        </button>
    </div>
</template>

<script>
    import DescargaXML from "../DescargaXML";
    export default {
        name: "documento-action-buttons",
        components: { DescargaXML },
        props: ['value'],
        methods: {
            correo() {
                return this.$store.dispatch('controlRecursos/documento/correo', {
                    id: this.value.id,
                    params: {}})
                    .then((data) => {
                    })
            },
        }
    }
</script>
