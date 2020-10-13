<template>
    <div class="btn-group">
        <Show v-bind:value="value"></Show>
    </div>
</template>

<script>
    import Show from '../ShowModal'
    export default {
        name: "ActionButtons",
        components: {Show},
        props: ['value'],
    }
</script>
