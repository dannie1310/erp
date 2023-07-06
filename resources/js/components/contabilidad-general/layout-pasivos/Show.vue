<template>
    <span>
        <div class="row" >
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <layout-partial-show v-bind:id ="this.id"></layout-partial-show>
                    </div>
                    <div class="card-footer">
                        <div class="pull-right">
                            <button type="button" class="btn btn-secondary " v-on:click="regresar"><i class="fa fa-angle-left"></i>Regresar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
import PDFPoliza from "../poliza/partials/PDFPoliza.vue";
import PolizaPartialShow from "../poliza/partials/PartialShow.vue";
import LayoutPartialShow from "./partials/PartialShow.vue";

export default {
    name: "layout-pasivo-show",
    props : ['id'],
    components: {LayoutPartialShow},
    methods :{
        regresar() {
            this.$router.push({name: 'layouts-pasivos'});
        },
    },
    watch:{

    }
}
</script>
<style scoped></style>
