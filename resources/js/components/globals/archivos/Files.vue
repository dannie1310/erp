<template>
    <span>
        <div class="card">
            <div class="card-body">
                <span >
                    <div class="row">
                        <div class="col-md-12">
                            <Upload v-bind:id="id" v-bind:permiso="permiso"></Upload>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <List v-bind:id="id" v-bind:tipo="tipo" v-bind:relacionadas="relacionadas"></List>
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
    props: ['id','tipo','permiso','relacionadas'],
    components:{List, Upload},
}
</script>

<style>

</style>
