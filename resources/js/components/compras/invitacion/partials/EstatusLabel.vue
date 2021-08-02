<template>
    <h6><span class="badge" :style="{'background-color': value.color, 'color': value.text_color}" >{{ value.descripcion }}</span></h6>
</template>
<script>
    export default {
        name: "estatus-label",
        props: ['value'],
    }
</script>
