<template>
    <small class="badge" :class="{'badge-danger': value.estado==-1, 'badge-success': value.estado!=-1}">
        {{ value.descripcion }}
    </small>
</template>

<script>
    export default {
        name: "distribuir-estatus",
        props: ['value']
    }
</script>