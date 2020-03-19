<template>
    <app-table-panel
        :title="'Comentários'"
        titleCollapsed="Comentários"
        :per-page="perPage"
        :filter-text="filterText"
        @input-filter-text="filterText = $event.target.value"
        @set-per-page="perPage = $event"
        :collapsedLabel="selected.name"
        :is-selected="selected.id !== null"
        :subTitle="
            entries.selected.object + ' - ' + entries.selected.value_formatted
        "
        v-if="environment.user != null"
    >
        <template slot="buttons">
            <button
                v-if="can('entry-comments:store')"
                :disabled="!can('entry-comments:store')"
                class="btn btn-primary btn-sm pull-right"
                @click="createComment()"
                title="Novo Comentário"
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
                @click="selectEntryComment(comment)"
                v-for="comment in entryComments.data.rows"
                :class="{
                    'cursor-pointer': true,
                    'bg-primary-lighter text-white': isCurrent(
                        comment,
                        selected,
                    ),
                }"
            >
                <td class="align-middle">
                    {{ comment.text }}
                </td>

                <td class="align-middle text-left">
                    {{ comment.user.name }}
                </td>

                <td class="align-middle text-left">
                    {{ comment.formatted_created_at }}
                </td>

                <td class="align-middle text-right">
                    <button
                        :disabled="!can('entry-comments:delete')"
                        class="btn btn-sm btn-micro btn-primary"
                        @click="editComment(comment)"
                        title="editar comentário"
                    >
                        <i class="fa fa-edit"></i>
                    </button>
                    <button
                        :disabled="!can('entry-comments:delete')"
                        class="btn btn-sm btn-micro btn-danger"
                        @click="trash(comment)"
                        title="Deletar Comentário"
                    >
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>
        </app-table>

        <app-comment-form :show.sync="showModal"></app-comment-form>
    </app-table-panel>
</template>
<script>
import { mapActions, mapGetters } from 'vuex'
import crud from '../../views/mixins/crud'
import entries from '../../views/mixins/entries'
import permissions from '../../views/mixins/permissions'
import entryComments from '../../views/mixins/entryComments'
import congressmanBudgets from '../../views/mixins/congressmanBudgets'

const service = {
    name: 'entryComments',

    uri:
        'congressmen/{congressmen.selected.id}/budgets/{congressmanBudgets.selected.id}/entries/{entries.selected.id}/comments',
}

export default {
    mixins: [crud, entryComments, permissions, entries, congressmanBudgets],

    data() {
        return {
            service: service,

            showModal: false,
        }
    },

    computed: {
        ...mapGetters({
            congressmanBudgetsClosedAt: 'congressmanBudgets/selectedClosedAt',
        }),
    },

    methods: {
        ...mapActions(service.name, ['selectEntryComment']),

        getTableColumns() {
            let columns = ['Comentário', 'Autor', 'Criado em', '']

            return columns
        },

        trash(comment) {
            this.$swal({
                title: 'Deseja realmente DELETAR este comentário?',
                icon: 'warning',
            }).then(result => {
                if (result.value) {
                    this.$store.dispatch('entryComments/delete', comment)
                }
            })
        },

        editComment(comment) {
            this.showModal = true
        },

        createComment() {
            this.showModal = true
        },
    },
}
</script>
