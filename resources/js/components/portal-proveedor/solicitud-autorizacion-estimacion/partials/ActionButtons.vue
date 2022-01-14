<template>
    <span>
        <div class="btn-group">
            <router-link  :to="{ name: 'solicitud-autorizacion-avance-show', params: {id: value.id, base: value.base}}" v-if="value.show" type="button" class="btn btn-sm btn-outline-secondary" title="Ver">
                <i class="fa fa-eye"></i>
            </router-link>
             <router-link  :to="{ name: 'solicitud-autorizacion-avance-edit', params: {id: value.id, base: value.base}}" v-if="value.edit" type="button" class="btn btn-sm btn-outline-info" title="Editar">
                <i class="fa fa-pencil"></i>
            </router-link>
            <router-link  :to="{ name: 'solicitud-autorizacion-avance-delete', params: {id: value.id, base: value.base}}" v-if="value.delete" type="button" class="btn btn-sm btn-outline-danger" title="Eliminar">
                <i class="fa fa-trash"></i>
            </router-link>
            <PDF v-bind:id="value.id" v-bind:base="value.base" v-if="value.show"></PDF>
        </div>
    </span>
</template>

<script>
    import PDF from '../Formato.vue';
    export default {
        name: "action-buttons",
        components: { PDF },
        props: ['value'],
        data() {
            return {

            }
        },
        methods: {

        }
    }
</script>
