<template>
    <span>
        <div class="progress">
            <div class="progress-bar" :class="value.color" role="progressbar" :aria-valuenow="value.porcentaje_avance"
               aria-valuemin="0" aria-valuemax="100" :style="{'width': value.porcentaje_avance+'%'}">
            {{value.porcentaje_avance}}%
            </div>
        </div>
        <div>{{value.divisor}}/{{value.dividendo}}</div>

    </span>
</template>

<script>
    export default {
        name: "ProgressBar",
        props: ['value'],
    }
</script>

<style scoped>

</style>