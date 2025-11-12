<template>
    <span v-if="value.descripcion == null" class="badge" style="'background-color': #29be22">Registrado</span>
    <span v-else class="badge" :style="{'background-color': value.color}">{{ value.descripcion }}</span>
</template>
<script>
    export default {
        name: "estatus-label",
        props: ['value'],
    }
</script>
