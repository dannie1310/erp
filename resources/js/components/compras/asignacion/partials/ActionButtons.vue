<template>
    <div class="btn-group">
        <button @click="show" type="button" class="btn btn-sm btn-outline-secondary" title="Ver">
            <i class="fa fa-eye"></i>
        </button>
        <PDF v-bind:id="value.id"></PDF>
        <button @click="edit" type="button" v-if="value.editar" class="btn btn-sm btn-outline-success" title="Editar">
            <i class="fa fa-pencil"></i>
        </button>
        <Eliminar v-if="value.eliminar" v-bind:id="value.id"></Eliminar>
    </div>
</template>

<script>
    import PDF from "../FormatoAsignacion";
    import Eliminar from '../Delete';
    export default {
        name: "action-buttons",
        components: {PDF, Eliminar},
        props: ['value'],
        data() {
            return {
                cargando: false,
            }
        },
        methods:{
            show(){
                this.$router.push({name: 'asignacion-proveedor-show', params: { id: this.value.id }});
            },
            edit(){
                this.$router.push({name: 'asignacion-proveedor-edit', params: { id: this.value.id }});
            },
        }
    }

</script>
