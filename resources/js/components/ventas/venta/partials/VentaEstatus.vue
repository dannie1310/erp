<template>
    <small class="badge" :class="{
                     'badge-danger': value.estado == 'CANCELADO',
                     'badge-primary': value.estado == 'CREADO',
                     'badge-success': value.estado == 'PAGADO'}">
        {{ value.estado }} </small>
</template>

<script>
    export default {
        name: "venta-estatus",
        props: ['value']
    }
</script>
