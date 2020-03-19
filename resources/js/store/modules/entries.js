import Form from '../../classes/Form'

import * as mutationsMixin from './mixins/mutations.js'
import * as actionsMixin from './mixins/actions.js'
import * as statesMixin from './mixins/states.js'
import * as gettersMixin from './mixins/getters.js'

const __emptyModel = {
    id: null,
    date: null,
    document_number: null,
    value: null,
    value_abs: null,
    object: null,
    to: null,
    provider_id: null,
    provider_name: null,
    provider_cpf_cnpj: null,
    cost_center_id: null,
}

let state = merge_objects(
    {
        selectedRole: __emptyModel,

        form: new Form(clone(__emptyModel)),

        emptyForm: clone(__emptyModel),

        mode: null,

        model: {
            name: 'entry',

            table: 'entries',

            class: { singular: 'Entry', plural: 'Entries' },
        },
    },

    statesMixin.common,
)

let actions = merge_objects(actionsMixin, {
    subscribeToModelEvents(context, payload) {
        context.dispatch('leaveModelChannel', payload)

        if (context.state.model) {
            subscribePublicChannel(
                'entries.' + payload.id,
                '.App\\Events\\' + 'EntryDocumentsChanged',
                event => {
                    context.dispatch('entryDocuments/load', payload, {
                        root: true,
                    })
                },
            )

            subscribePublicChannel(
                'entries.' + payload.id,
                '.App\\Events\\' + 'EntryCommentsChanged',
                event => {
                    context.dispatch('entryComments/load', payload, {
                        root: true,
                    })
                },
            )
        }
    },

    selectEntry(context, payload) {
        const performLoad =
            !context.state.selected || context.state.selected.id != payload.id

        context.dispatch('entries/select', payload, { root: true })
        context.commit('mutateFormData', payload)

        if (performLoad) {
            context.dispatch('entryDocuments/setCurrentPage', 1, { root: true })
            context.commit(
                'entryDocuments/mutateSetSelected',
                { id: null },
                { root: true },
            )
            context.dispatch('entryComments/setCurrentPage', 1, { root: true })
            context.commit(
                'entryComments/mutateSetSelected',
                { id: null },
                { root: true },
            )
        }
    },

    verify(context, payload) {
        post(makeDataUrl(context) + '/' + payload.id + '/verify')
    },

    unverify(context, payload) {
        post(makeDataUrl(context) + '/' + payload.id + '/unverify')
    },

    analyse(context, payload) {
        post(makeDataUrl(context) + '/' + payload.id + '/analyse')
    },

    unanalyse(context, payload) {
        post(makeDataUrl(context) + '/' + payload.id + '/unanalyse')
    },

    publish(context, payload) {
        post(makeDataUrl(context) + '/' + payload.id + '/publish')
    },

    unpublish(context, payload) {
        post(makeDataUrl(context) + '/' + payload.id + '/unpublish')
    },

    delete(context, payload) {
        post(makeDataUrl(context) + '/' + payload.id + '/delete')
    },

    fillFormForRefund(context) {
        const url = buildApiUrl(context.state.service.uri, context.rootState)

        get(url + '/empty-refund-form', {}).then(response => {
            context.commit('mutateFormData', response.data)
        })
    },
})

let mutations = mutationsMixin

let getters = merge_objects(gettersMixin, {
    currentSummaryLabel(state, getters) {
        if (!!state.selected.id) {
            return (
                state.selected.date_formatted +
                ' - ' +
                state.selected.object +
                ' - ' +
                state.selected.value_formatted
            )
        } else {
            return ''
        }
    },

    getSelectedState: (state, getters) => {
        return getters.getEntryState(getters.getSelected)
    },

    getEntryState: (state, getters, rootState, rootGetters) => entry => {
        const congressmanBudgetClosedAt =
            rootGetters['congressmanBudgets/selectedClosedAt']

        const closedTitle = 'O orçamento mensal está fechado'

        if (entry.published_at) {
            return {
                name: 'Publicável',
                buttons: {
                    unpublish: {
                        visible: can('entries:publish'),
                        disabled: !can('entries:publish'),
                        title: 'Remover do Portal da Transparência',
                    },
                    publish: {
                        visible: false,
                        disabled: true,
                        title:
                            'O lançamento já está publicado no Portal da Transparência',
                    },
                    unanalyse: {
                        visible: can('entries:analyse'),
                        disabled: true,
                        title:
                            "Não é possível cancelar marcação de 'analisado' pois o lançamento está publicado",
                    },
                    analyse: {
                        visible: false,
                        disabled: true,
                        title: 'O lançamento já está analisado',
                    },
                    unverify: {
                        visible:
                            can('entries:buttons') || can('entries:verify'),
                        disabled: true,
                        title: !congressmanBudgetClosedAt
                            ? 'Não é possível cancelar a marcação de verificado pois o documento está publicado'
                            : closedTitle,
                    },
                    verify: {
                        visible: false,
                        disabled: true,
                        title: !congressmanBudgetClosedAt
                            ? 'O lançamento já está verificado'
                            : closedTitle,
                    },
                    edit: {
                        visible:
                            can('entries:buttons') || can('entries:update'),
                        disabled: !can('entries:buttons'),
                        title: can('entries:update')
                            ? !congressmanBudgetClosedAt
                                ? 'Não é possível alterar o lançamento pois ele já está publicado'
                                : closedTitle
                            : 'Visualizar lançamento',
                    },
                    delete: {
                        visible:
                            can('entries:buttons') || can('entries:delete'),
                        disabled:
                            !can('entries:delete') ||
                            entry.analysed_at ||
                            entry.verified_at,
                        title: !congressmanBudgetClosedAt
                            ? 'Não é possível apagar o lançamento pois ele já está publicado'
                            : closedTitle,
                    },
                },
            }
        } else if (entry.analysed_at) {
            return {
                name: 'Analisado',
                buttons: {
                    unpublish: {
                        visible: false,
                        disabled: false,
                        title: 'Remover do Portal da Transparência',
                    },
                    publish: {
                        visible: can('entries:publish'),
                        disabled: !can('entries:publish'),
                        title:
                            'Publicar o lançamento no Portal da Transparência',
                    },
                    unanalyse: {
                        visible: can('entries:analyse'),
                        disabled: !can('entries:analyse'),
                        title: "Cancelar marcação de 'analisado'",
                    },
                    analyse: {
                        visible: false,
                        disabled: true,
                        title: 'O lançamento já está analisado',
                    },
                    unverify: {
                        visible:
                            can('entries:buttons') || can('entries:verify'),
                        disabled: true,
                        title: !congressmanBudgetClosedAt
                            ? 'Não é possível cancelar a marcação de verificado pois o documento está analisado'
                            : closedTitle,
                    },
                    verify: {
                        visible: false,
                        disabled: true,
                        title: !congressmanBudgetClosedAt
                            ? 'O lançamento já está verificado'
                            : closedTitle,
                    },
                    edit: {
                        visible:
                            can('entries:buttons') || can('entries:update'),
                        disabled: !can('entries:buttons'),
                        title: can('entries:update')
                            ? !congressmanBudgetClosedAt
                                ? 'Não é possível alterar o lançamento pois ele já está analisado'
                                : closedTitle
                            : 'Visualizar lançamento',
                    },
                    delete: {
                        visible:
                            can('entries:buttons') || can('entries:delete'),
                        disabled: true,
                        title: !congressmanBudgetClosedAt
                            ? 'Não é possível apagar o lançamento pois ele já está analisado'
                            : closedTitle,
                    },
                },
            }
        } else if (entry.verified_at) {
            return {
                name: 'Verificado',
                buttons: {
                    unpublish: {
                        visible: false,
                        disabled: false,
                        title: 'Remover do Portal da Transparência',
                    },
                    publish: {
                        visible: can('entries:publish'),
                        disabled: true,
                        title:
                            'Não é possível publicar o lançamento pois ele não está analisado',
                    },
                    unanalyse: {
                        visible: false,
                        disabled: true,
                        title: "Cancelar marcação de 'analisado'",
                    },
                    analyse: {
                        visible: can('entries:analyse'),
                        disabled: !can('entries:analyse'),
                        title: "Marcar orçamento como 'analisado'",
                    },
                    unverify: {
                        visible:
                            can('entries:buttons') || can('entries:verify'),
                        disabled:
                            !can('entries:verify') || congressmanBudgetClosedAt,
                        title: !congressmanBudgetClosedAt
                            ? "Cancelar marcação de 'verificado'"
                            : closedTitle,
                    },
                    verify: {
                        visible: false,
                        disabled: true,
                        title: !congressmanBudgetClosedAt
                            ? 'O lançamento já está verificado'
                            : closedTitle,
                    },
                    edit: {
                        visible:
                            can('entries:buttons') || can('entries:update'),
                        disabled: !can('entries:buttons'),
                        title: can('entries:update')
                            ? !congressmanBudgetClosedAt
                                ? 'Não é possível alterar o lançamento pois ele já está verificado'
                                : closedTitle
                            : 'Visualizar lançamento',
                    },
                    delete: {
                        visible:
                            can('entries:buttons') || can('entries:delete'),
                        disabled: true,
                        title: !congressmanBudgetClosedAt
                            ? 'Não é possível apagar o lançamento pois ele já está verificado'
                            : closedTitle,
                    },
                },
            }
        } else {
            return {
                name: 'Salvo',
                buttons: {
                    unpublish: {
                        visible: false,
                        disabled: false,
                        title: 'Remover do Portal da Transparência',
                    },
                    publish: {
                        visible: can('entries:publish'),
                        disabled: true,
                        title:
                            'Não é possível publicar o lançamento pois ele não está analisado',
                    },
                    unanalyse: {
                        visible: false,
                        disabled: true,
                        title: "Cancelar marcação de 'analisado'",
                    },
                    analyse: {
                        visible: can('entries:analyse'),
                        disabled: true,
                        title:
                            'Não é possível analisar o lançamento pois ele não está verificado',
                    },
                    unverify: {
                        visible: false,
                        disabled: true,
                        title: "'Cancelar marcação de 'verificado'",
                    },
                    verify: {
                        visible:
                            can('entries:buttons') || can('entries:verify'),
                        disabled:
                            !can('entries:verify') || congressmanBudgetClosedAt,
                        title: !congressmanBudgetClosedAt
                            ? "Marcar orçamento como 'verificado'"
                            : closedTitle,
                    },
                    edit: {
                        visible:
                            can('entries:buttons') || can('entries:update'),
                        disabled: false,
                        title: can('entries:update')
                            ? !congressmanBudgetClosedAt
                                ? 'Editar lançamento'
                                : closedTitle
                            : 'Visualizar lançamento',
                    },
                    delete: {
                        visible:
                            can('entries:buttons') || can('entries:delete'),
                        disabled:
                            congressmanBudgetClosedAt || !can('entries:delete'),
                        title: !congressmanBudgetClosedAt
                            ? 'Apagar lançamento'
                            : closedTitle,
                    },
                },
            }
        }
    },
})

export default {
    state,
    actions,
    mutations,
    getters,
    namespaced: true,
}
