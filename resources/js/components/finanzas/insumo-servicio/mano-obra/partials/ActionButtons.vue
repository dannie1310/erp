<template>
    <span>
        <div class="btn-group">
            <Eliminar v-if="$root.can('eliminar_insumo_mano_obra')" v-bind:mano="value.id" v-bind:pagina="value.pagina" v-bind:borrar="value.borrar"></Eliminar>     
            <Edit v-if="$root.can('editar_insumo_mano_obra')" v-bind:mano="value.id" v-bind:pagina="value.pagina" v-bind:update="value.update"></Edit>       
        </div>
    </span>
</template>

<script>
import Edit from "../Edit";
import Eliminar from "../Eliminar";
export default {
    name: "ActionButtons",
    components: {Eliminar, Edit},
    props: ['value']
}
</script>

<style scoped>

</style>