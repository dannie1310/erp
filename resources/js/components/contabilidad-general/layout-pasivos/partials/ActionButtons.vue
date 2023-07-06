<template>
    <div class="btn-group">
        <router-link  :to="{ name: 'layouts-pasivos-show', params: {id: value.id}}" type="button" class="btn btn-sm btn-outline-secondary" title="Consultar">
            <i class="fa fa-eye"></i>
        </router-link>
        <!--<router-link  :to="{ name: 'layouts-pasivos-show', params: {id: value.id}}" type="button" class="btn btn-sm btn-outline-primary" title="Validar Layout">
            <i class="fa fa-check"></i>
        </router-link>-->
    </div>
</template>

<script>
    export default {
        name: "action-buttons",
        props: ['value'],
        methods: {

        },
    }
</script>

