<template>
    <span class="badge" v-if="value.id == 7" :style="{'background-color': '#00a65a'}">{{ value.descripcion }}</span>
</template>
<script>
    export default {
        name: "estatus-label",
        props: ['value'],
    }
</script>
