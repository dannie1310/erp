<template>
    <span>
        <div class="card">
            <div class="card-body">
                <span >
                    <div class="row">
                        <div class="col-md-12">
                            <Upload v-bind:id="id" @uploaded="find"></Upload>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <List v-bind:id="id"></List>
                        </div>
                    </div>
                </span>
            </div>
        </div>
    </span>
</template>

<script>
import Upload from "./Upload";
import List from "./List";
export default {
    name: "documentos",
    props: ['id'],
    components:{List, Upload},
    methods:{
        find(){

        }
    }
}
</script>

<style>

</style>
