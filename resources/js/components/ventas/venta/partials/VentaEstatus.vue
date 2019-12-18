<template>
    <small class="badge" :class="{
                     'badge-danger': value.estado.id == '-1',
                     'badge-primary': value.estado.id == '0',
                     'badge-success': value.estado.id == '1'}">
        {{ value.estado.descripcion }} </small>
</template>

<script>
    export default {
        name: "venta-estatus",
        props: ['value']
    }
</script>
