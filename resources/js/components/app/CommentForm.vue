<template>
    <div>
        <b-modal v-model="showModal" title="Comentário" @shown="onShow()">
            <template>
                <app-textarea
                    name="text"
                    label="Comentário"
                    v-model="form.fields.text"
                    :form="form"
                ></app-textarea>
            </template>

            <template slot="modal-footer">
                <button
                    @click="saveAndClose()"
                    class="btn btn-outline-gray btn-sm"
                >
                    <i v-if="busy" class="fas fa-compact-disc fa-spin"></i>

                    Gravar
                </button>

                <button @click="close()" class="btn btn-success btn-sm">
                    Fechar
                </button>
            </template>
        </b-modal>
    </div>
</template>

<script>
import crud from '../../views/mixins/crud'
import entries from '../../views/mixins/entries'
import { mapActions } from 'vuex'

const service = {
    name: 'entryComments',

    uri:
        'congressmen/{congressmen.selected.id}/budgets/{congressmanBudgets.selected.id}/entries/{entries.selected.id}/comments',
}

export default {
    mixins: [crud, entries],

    props: ['show'],

    data() {
        return {
            busy: false,

            service: service,
        }
    },

    methods: {
        ...mapActions(service.name, ['clearForm', 'clearErrors']),
        close() {
            this.showModal = false
        },

        saveAndClose() {
            this.busy = true

            this.saveModel(() => {
                this.closeModal(true)
            })
        },

        closeModal(clearForm = false) {
            this.showModal = false

            if (clearForm) {
                this.clearErrors()
                this.clearForm()
            }
        },

        onShow() {
            this.busy = false
        },
    },

    computed: {
        showModal: {
            get() {
                return this.show
            },

            set(showModal) {
                this.$emit('update:show', showModal)
            },
        },
    },
}
</script>
