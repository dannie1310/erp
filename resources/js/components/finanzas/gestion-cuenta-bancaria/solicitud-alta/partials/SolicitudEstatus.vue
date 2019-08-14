<template>
    <p>
        {{ value == 1 ? 'Registro' : 'Autorización'}}
    </p>
</template>

<script>
    export default {
        name: "solicitud-estatus",
        props: ['value']
    }
</script>