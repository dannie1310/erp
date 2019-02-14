<template>
    <input class="form-control"/>
</template>

<script>
    export default {
        props: ['scope', 'value'],
        name: "empresa-autocomplete",
        mounted() {
            let self = this;
            $(self.$el)
                .autocomplete({
                    source(request, response) {
                        $.ajax({
                            url: '/api/empresa',
                            dataType: 'json',
                            data: {
                                search: request.term,
                                scope: self.scope,
                                limit: 15
                            },
                            success(data) {
                                response(data.data.map(empresa => ({id: empresa.id, value: empresa.razon_social})));
                            }
                        });
                    },
                    minLength: 1,
                    select(event, ui) {
                        self.$emit('input', ui.item.id );
                    },
                    change(event, ui) {
                        if(ui.item == null) {
                            self.$emit('input', '');
                        }
                    },
                    appendTo: '.modal'
                });
        },

        watch: {
            value(value) {
                if (value == '') {
                    $(this.$el)
                        .val(null)
                }
            },
            destroyed: function () {
                $(this.$el).autocomplete( "destroy" );
            }
        }
    }
</script>
