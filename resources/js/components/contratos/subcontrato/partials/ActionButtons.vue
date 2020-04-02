<template>
    <div class="btn-group">
        <Show v-bind:id="value.id" v-bind:show="value.show"></Show>
    </div>
</template>

<script>
    import Show from '../Show'
    export default {
        name: "ActionButtons",
        components: {Show},
        props: ['value'],
    }
</script>