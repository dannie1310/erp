<template>
    <span>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <label>
                        RFC:
                    </label>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                     <input class="form-control"
                            name="rfc"
                            data-vv-as="RFC"
                            v-model="proveedor.rfc"
                            :class="{'is-invalid':rfcValidate}"
                            v-validate="{ required: true, regex: /\.(js|ts)$/ }"
                            id="rfc"
                            placeholder="RFC" :maxlength="16"/>
                    <span class="text-danger" v-if="rfcValidate">RFC Inválido</span>
                </div>
            </div>
        </div>
    </div>
    </span>
</template>

<script>
    export default {
        name: "proveedor-edit"
    }
</script>

<style scoped>

</style>
