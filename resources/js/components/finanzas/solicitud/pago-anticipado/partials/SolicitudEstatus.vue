<template>
    <small class="badge" :class="{'badge-danger': value==-2, 'badge-success': value==2, 'badge-warning': value==0, 'badge-info': value==1}">
        {{getEstado(value)}}
    </small>
</template>

<script>
    export default {
        name: "solicitud-estatus",
        props: ['value'],
        data(){
            return{

            }
        },
        methods: {
            getEstado(value){
                switch(value){
                    case -2:
                        return 'Cancelado';
                    case 0:
                        return 'Registrado'
                    case 1:
                        return 'Pago Parcial'
                    case 2:
                        return 'Pagado'
                }
            },
        },
    }
</script>
