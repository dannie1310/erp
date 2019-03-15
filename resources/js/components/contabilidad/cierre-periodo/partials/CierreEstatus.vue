<template>
    <small class="badge" :class="{'badge-danger': !value, 'badge-success': value}">
        {{ value ? 'Abierto' : 'Cerrado'}}
    </small>
</template>

<script>
    export default {
        name: "cierre-estatus",
        props: ['value']
    }
</script>