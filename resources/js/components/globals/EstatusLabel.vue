<template>
    <h6><span class="badge" :class="value.color" >{{ value.descripcion }}</span></h6>
</template>
<script>
    export default {
        name: "estatus-label-global",
        props: ['value'],
    }
</script>
