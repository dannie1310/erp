<template>
    <div class="btn-group">
        <Edit v-bind:empresa="value.empresa" v-if="value.edit"></Edit>
    </div>
</template>

<script>
import Edit from '../Edit';
export default {
    name: "action-buttons",
    components: {Edit},
    props: ['value'],
    methods: {
    },
}
</script>

<style>

</style>