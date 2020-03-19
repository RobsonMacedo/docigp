<template>
    <app-table-panel
        :title="'Documentos (' + pagination.total + ')'"
        titleCollapsed="Documento"
        :per-page="perPage"
        :filter-text="filterText"
        @input-filter-text="filterText = $event.target.value"
        @set-per-page="perPage = $event"
        :collapsedLabel="selected.name"
        :is-selected="selected.id !== null"
        :subTitle="
            entries.selected.object + ' - ' + entries.selected.value_formatted
        "
    >
        <template slot="buttons">
            <button
                v-if="
                    can('entry-documents:buttons') ||
                        can('entry-documents:store')
                "
                :disabled="
                    !can('entry-documents:store') || congressmanBudgetsClosedAt
                "
                class="btn btn-primary btn-sm pull-right"
                @click="createDocument()"
                title="Novo documento"
            >
                <i class="fa fa-plus"></i>
            </button>
        </template>

        <app-table
            :pagination="pagination"
            @goto-page="gotoPage($event)"
            :columns="getTableColumns()"
        >
            <tr
                @click="selectEntryDocument(document)"
                v-for="document in entryDocuments.data.rows"
                :class="{
                    'cursor-pointer': true,
                    'bg-primary-lighter text-white': isCurrent(
                        document,
                        selected,
                    ),
                }"
            >
                <!--                <td class="align-middle">-->
                <!--                    {{ getEntryDocumentState(document).name }}-->
                <!--                </td>-->

                <td class="align-middle">
                    {{ document.attached_file.original_name }}
                </td>

                <td
                    v-if="can('entry-documents:show')"
                    class="align-middle text-center"
                >
                    <app-active-badge
                        :value="document.verified_at"
                        :labels="['sim', 'não']"
                    ></app-active-badge>
                </td>

                <td
                    v-if="can('entry-documents:show')"
                    class="align-middle text-center"
                >
                    <app-active-badge
                        :value="document.analysed_at"
                        :labels="['sim', 'não']"
                    ></app-active-badge>
                </td>

                <td
                    v-if="can('entry-documents:show')"
                    class="align-middle text-center"
                >
                    <app-active-badge
                        :value="document.published_at"
                        :labels="['documento público', 'documento privado']"
                    ></app-active-badge>
                </td>

                <td class="align-middle text-right">
                    <button
                        v-if="
                            getEntryDocumentState(document).buttons.verify
                                .visible
                        "
                        :disabled="
                            getEntryDocumentState(document).buttons.verify
                                .disabled
                        "
                        class="btn btn-sm btn-micro btn-primary"
                        @click="verify(document)"
                        :title="
                            getEntryDocumentState(document).buttons.verify.title
                        "
                    >
                        <i class="fa fa-check"></i> verificar
                    </button>

                    <button
                        v-if="
                            getEntryDocumentState(document).buttons.unverify
                                .visible
                        "
                        :disabled="
                            getEntryDocumentState(document).buttons.unverify
                                .disabled
                        "
                        class="btn btn-sm btn-micro btn-warning"
                        @click="unverify(document)"
                        :title="
                            getEntryDocumentState(document).buttons.unverify
                                .title
                        "
                    >
                        <i class="fa fa-ban"></i> verificação
                    </button>

                    <button
                        v-if="
                            getEntryDocumentState(document).buttons.analyse
                                .visible
                        "
                        :disabled="
                            getEntryDocumentState(document).buttons.analyse
                                .disabled
                        "
                        class="btn btn-sm btn-micro btn-primary"
                        @click="analyse(document)"
                        :title="
                            getEntryDocumentState(document).buttons.analyse
                                .title
                        "
                    >
                        <i class="fa fa-check"></i> analisado
                    </button>

                    <button
                        v-if="
                            getEntryDocumentState(document).buttons.unanalyse
                                .visible
                        "
                        :disabled="
                            getEntryDocumentState(document).buttons.unanalyse
                                .disabled
                        "
                        class="btn btn-sm btn-micro btn-danger"
                        @click="unanalyse(document)"
                        :title="
                            getEntryDocumentState(document).buttons.unanalyse
                                .title
                        "
                    >
                        <i class="fa fa-ban"></i> analisado
                    </button>

                    <button
                        v-if="
                            getEntryDocumentState(document).buttons.publish
                                .visible
                        "
                        :disabled="
                            getEntryDocumentState(document).buttons.publish
                                .disabled
                        "
                        class="btn btn-sm btn-micro btn-danger"
                        @click="publish(document)"
                        :title="
                            getEntryDocumentState(document).buttons.publish
                                .title
                        "
                    >
                        <i class="fa fa-check"></i> tornar público
                    </button>

                    <button
                        v-if="
                            getEntryDocumentState(document).buttons.unpublish
                                .visible
                        "
                        :disabled="
                            getEntryDocumentState(document).buttons.unpublish
                                .disabled
                        "
                        class="btn btn-sm btn-micro btn-success"
                        @click="unpublish(document)"
                        :title="
                            getEntryDocumentState(document).buttons.unpublish
                                .title
                        "
                    >
                        <i class="fa fa-ban"></i> tornar privado
                    </button>

                    <a
                        :href="document.url"
                        target="_blank"
                        title="Visualizar documento"
                        class="btn btn-sm btn-micro btn-warning cursor-pointer"
                    >
                        <i class="fa fa-eye"></i>
                    </a>

                    <button
                        v-if="
                            getEntryDocumentState(document).buttons.delete
                                .visible
                        "
                        :disabled="
                            getEntryDocumentState(document).buttons.delete
                                .disabled
                        "
                        class="btn btn-sm btn-micro btn-danger"
                        @click="trash(document)"
                        :title="
                            getEntryDocumentState(document).buttons.delete.title
                        "
                    >
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>
        </app-table>

        <app-document-form :show.sync="showModal"></app-document-form>
    </app-table-panel>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import crud from '../../views/mixins/crud'
import entries from '../../views/mixins/entries'
import permissions from '../../views/mixins/permissions'
import entryDocuments from '../../views/mixins/entryDocuments'
import congressmanBudgets from '../../views/mixins/congressmanBudgets'

const service = {
    name: 'entryDocuments',

    uri:
        'congressmen/{congressmen.selected.id}/budgets/{congressmanBudgets.selected.id}/entries/{entries.selected.id}/documents',
}

export default {
    mixins: [crud, entryDocuments, permissions, entries, congressmanBudgets],

    data() {
        return {
            service: service,

            showModal: false,
        }
    },

    computed: {
        ...mapGetters({
            congressmanBudgetsClosedAt: 'congressmanBudgets/selectedClosedAt',
            getEntryDocumentState: 'entryDocuments/getEntryDocumentState',
        }),

        // return this.$store.dispatch('congressmanBudgets/changePercentage', {
        //     congressmanBudget: congressmanBudget,
        //     percentage: value
        // });
    },

    methods: {
        ...mapActions(service.name, ['selectEntryDocument']),

        getTableColumns() {
            let columns = ['Nome do arquivo']

            if (can('entry-documents:show')) {
                columns.push({
                    type: 'label',
                    title: 'Verificado',
                    trClass: 'text-center',
                })

                columns.push({
                    type: 'label',
                    title: 'Analisado',
                    trClass: 'text-center',
                })

                columns.push({
                    type: 'label',
                    title: 'Publicidade',
                    trClass: 'text-center',
                })
            }

            columns.push('')

            return columns
        },

        trash(document) {
            this.$swal({
                title: 'Deseja realmente DELETAR este documento?',
                icon: 'warning',
            }).then(result => {
                if (result.value) {
                    this.$store.dispatch('entryDocuments/delete', document)
                }
            })
        },

        verify(entry) {
            this.$swal({
                title:
                    'Confirma a marcação deste lançamento como "VERIFICADO"?',
                icon: 'warning',
            }).then(result => {
                if (result.value) {
                    this.$store.dispatch('entryDocuments/verify', entry)
                }
            })
        },

        unverify(entry) {
            this.$swal({
                title:
                    'O status de "VERIFICADO" será removido deste lançamento, confirma?',
                icon: 'warning',
            }).then(result => {
                if (result.value) {
                    this.$store.dispatch('entryDocuments/unverify', entry)
                }
            })
        },

        analyse(document) {
            this.$swal({
                title: 'Este documento foi ANALISADO?',
                icon: 'warning',
            }).then(result => {
                if (result.value) {
                    this.$store.dispatch('entryDocuments/analyse', document)
                }
            })
        },

        unanalyse(document) {
            this.$swal({
                title: 'Deseja remover o status "ANALISADO" deste lançamento?',
                icon: 'warning',
            }).then(result => {
                if (result.value) {
                    this.$store.dispatch('entryDocuments/unanalyse', document)
                }
            })
        },

        publish(document) {
            this.$swal({
                title: 'Confirma a PUBLICAÇÃO deste documento?',
                icon: 'warning',
            }).then(result => {
                if (result.value) {
                    this.$store.dispatch('entryDocuments/publish', document)
                }
            })
        },

        unpublish(document) {
            this.$swal({
                title: 'Confirma a DESPUBLICAÇÃO deste documento?',
                icon: 'warning',
            }).then(result => {
                if (result.value) {
                    this.$store.dispatch('entryDocuments/unpublish', document)
                }
            })
        },

        createDocument() {
            this.showModal = true
        },
    },
}
</script>
