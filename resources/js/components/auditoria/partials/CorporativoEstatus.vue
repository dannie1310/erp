<template>
    <p>
        {{ value != 1 ? 'No' : 'Si'}}
    </p>
</template>

<script>
    export default {
        name: "corporativo-estatus",
        props: ['value']
    }
</script>