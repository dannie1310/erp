<template>
    <small class="badge" :class="{'badge-danger': value.estado == -1, 'badge-success': value.estado == 1, 'badge-dark': value.estado == -2 , 'badge-warning': value.estado == 0}">
        {{ value.descripcion }}
    </small>
</template>

<script>
    export default {
        name: "partida-estatus",
        props: ['value']
    }
</script>
