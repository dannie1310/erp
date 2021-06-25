<template>
    <span class="badge" :class="value.color">{{ value.descripcion }}</span>
</template>
<script>
    export default {
        name: "estatus-label-global",
        props: ['value'],
    }
</script>
