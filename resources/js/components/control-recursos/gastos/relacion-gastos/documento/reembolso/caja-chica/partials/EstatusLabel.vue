<template>
    <span class="badge" :style="{'background-color': value.color}">{{ value.descripcion }}</span>
</template>
<script>
    export default {
        name: "estatus-label",
        props: ['value'],
    }
</script>
