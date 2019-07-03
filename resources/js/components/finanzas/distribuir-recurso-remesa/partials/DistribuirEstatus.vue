<template>
    <small class="badge" :class="{'badge-warning': value.estado == 0, 'badge-primary': value.estado == 1, 'badge-info': value.estado == 2, 'badge-success': value.estado == 3, 'badge-danger': value.estado == -1, 'badge-dark': value.estado == -2 }">
        {{ value.descripcion }}
    </small>
</template>

<script>
    export default {
        name: "distribuir-estatus",
        props: ['value']
    }
</script>
