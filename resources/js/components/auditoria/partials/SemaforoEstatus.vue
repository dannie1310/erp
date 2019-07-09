<template>
    <small class="badge" :class="{'badge-success': value<=950000, 'badge-warning': value>=950001 && value<=2000000, 'badge-danger': value>=2000001}">
        {{ value<=950000 ? 'Estatus 1' : ''}}
        {{ value>=950001 && value<=2000000 ? 'Estatus 2' : ''}}
        {{ value>=2000001 ? 'Estatus 3' : ''}}
    </small>
</template>

<script>
    export default {
        name: "semaforo-estatus",
        props: ['value']
    }
</script>