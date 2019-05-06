<template>
    <input type="text" name="daterange"/>
</template>

<script>
    export default {
        name: "daterange-picker",
        props: ['value'],
        mounted() {
            let self = this
            $('input[name="daterange"]').daterangepicker({
                opens: 'left',
                locale: {
                    format: 'yyyy-MM-dd',
                    applyLabel: 'Aplicar',
                    cancelLabel: 'Cancelar',
                    fromLabel: 'DE',
                    toLabel: 'A',
                }
            }).on('apply.daterangepicker', function(ev, picker) {
                self.$emit('input', picker);
            });
        },

        watch: {
            value: function (value) {
                $('input[name="daterange"]').val(value.startDate.format('yyyy-MM-dd') + ' - ' + value.endDate.format('yyyy-MM-dd'));
            }
        },

        destroyed: function () {
            $('input[name="daterange"]').daterangepicker('destroy');
        }
    }
</script>