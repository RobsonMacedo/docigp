<template>
    
        <button
            :disabled="disabled"
            :class="classes"
            :title="title"
            @click="editPercentage(model)"
        >
            <pulse-loader
                v-if="loading"
                color="white"
                :loading="true" :size="'0.4em'"
            >
            </pulse-loader>
            <span v-else :class="icon"> {{label}}</span>
            
        </button>
    
</template>

<script>
export default {
    props: [
        'label',
        'disabled',
        'icon',
        'classes',
        'title',
        'color',
        'model',
        'store',
        'method',
        
    ],

    data() {
        return {
            loading: false,
        }
    },

    methods: {
        editPercentage(model) {
            const $this = this

            $this.$swal({
                icon: 'warning',
                title: 'Novo percentual',
                input: 'text',
                inputPlaceholder: 'Digite um percentual',
                inputAttributes: {
                    dusk: 'input-percentage',
                },
                inputValidator: value => {
                    if (
                        !is_number(value) ||
                        to_number(value) < 0 ||
                        to_number(value) > 100
                    ) {
                        return 'Você precisa digitar um número entre 0 e 100'
                    }
                },
            }).then(value => {
                if (value.value) {

                    $this.loading = true

                    $this.changePercentage(model, value.value)

                    .then(response => {
                        $this.loading = false

                        $this.$swal({
                                        toast: true,
                                        position: 'top-end',
                                        showConfirmButton: false,
                                        showCancelButton: false,
                                        timer: 2000,
                                        icon: 'success',
                                        title: 'Salvo com sucesso',
                                    })
                                })
                }
            })
        },

        changePercentage: function(model, value) {
            return this.$store.dispatch('congressmanBudgets/changePercentage', {
                congressmanBudget: model,
                percentage: value,
            })
        },
    },
}
</script>
