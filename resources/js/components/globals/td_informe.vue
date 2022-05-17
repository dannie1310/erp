<template>
    <td style="text-align: right" :style="value<0?`color : #ff0000`:``" >
        <template v-if="value<0">(</template>
        <span v-if="value!=0">{{  parseFloat(Math.abs(value)).formatMoney(0,'.',',') }}</span>
        <span v-else>-</span>
        <template v-if="value<0">)</template>
    </td>
</template>

<script>
export default {
    name: "td_informe",
    props: ['value'],
}
</script>

<style scoped>

</style>
