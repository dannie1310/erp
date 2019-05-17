<template>
    <small class="badge" :class="{'badge-danger': value==-2, 'badge-success': value==0}">
        {{ value === 0 ? 'Registrado' : 'Cancelado'}}
    </small>
</template>

<script>
    export default {
        name: "solicitud-estatus",
        props: ['value']
    }
</script>