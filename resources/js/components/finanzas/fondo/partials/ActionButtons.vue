<template>
        <div class="btn-group ">
            <FondoShow v-if="value.show" v-bind:id="value.id" />
        </div>
</template>

<script>
    import FondoShow from "../Show";
    export default {
        name: "action-buttons",
        components: { FondoShow },
        props: ['value']
    }
</script>
