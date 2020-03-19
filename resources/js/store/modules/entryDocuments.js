import Form from '../../classes/Form'

import * as mutationsMixin from './mixins/mutations.js'
import * as actionsMixin from './mixins/actions.js'
import * as statesMixin from './mixins/states.js'
import * as gettersMixin from './mixins/getters.js'

const __emptyModel = {
    id: null,
    name: null,
    description: null,
}

let state = merge_objects(
    {
        selectedRole: __emptyModel,

        form: new Form(clone(__emptyModel)),

        emptyForm: clone(__emptyModel),

        mode: null,

        model: {
            name: 'entryDocument',

            table: 'entry_documents',

            class: { singular: 'EntryDocument', plural: 'EntryDocuments' },
        },
    },

    statesMixin.common,
)

let actions = merge_objects(actionsMixin, {
    subscribeToModelEvents(context, payload) {
        //Doesn't listen to anything
    },

    selectEntryDocument(context, payload) {
        context.dispatch('entryDocuments/select', payload, { root: true })
    },

    verify(context, payload) {
        post(makeDataUrl(context) + '/' + payload.id + '/verify')
    },

    unverify(context, payload) {
        post(makeDataUrl(context) + '/' + payload.id + '/unverify')
    },

    publish(context, payload) {
        post(makeDataUrl(context) + '/' + payload.id + '/publish')
    },

    unpublish(context, payload) {
        post(makeDataUrl(context) + '/' + payload.id + '/unpublish')
    },

    analyse(context, payload) {
        post(makeDataUrl(context) + '/' + payload.id + '/analyse')
    },

    unanalyse(context, payload) {
        post(makeDataUrl(context) + '/' + payload.id + '/unanalyse')
    },

    delete(context, payload) {
        post(makeDataUrl(context) + '/' + payload.id + '/delete')
    },
})

let mutations = mutationsMixin
let getters = merge_objects(gettersMixin, {
    getSelectedState: (state, getters) => {
        return getters.getEntryDocumentState(getters.getSelected)
    },

    getEntryDocumentState: (
        state,
        getters,
        rootState,
        rootGetters,
    ) => document => {
        const congressmanBudgetClosedAt =
            rootGetters['congressmanBudgets/selectedClosedAt']

        const closedTitle = 'O orçamento mensal está fechado'

        if (document.analysed_at) {
            return {
                name: 'Analisado',
                buttons: {
                    unpublish: {
                        visible:
                            (can('entry-documents:buttons') ||
                                can('entry-documents:publish')) &&
                            document.published_at,
                        disabled: true,
                        title: !congressmanBudgetClosedAt
                            ? 'Não é possível tornar o documento privado pois ele já está verificado e analisado'
                            : closedTitle,
                    },
                    publish: {
                        visible:
                            (can('entry-documents:buttons') ||
                                can('entry-documents:publish')) &&
                            !document.published_at,
                        disabled: true,
                        title: !congressmanBudgetClosedAt
                            ? 'Não é possível tornar o documento público pois ele já está verificado e analisado'
                            : closedTitle,
                    },
                    unanalyse: {
                        visible: can('entry-documents:analyse'),
                        disabled: !can('entry-documents:analyse'),
                        title: "Cancelar marcação de 'analisado'",
                    },
                    analyse: {
                        visible: false,
                        disabled: true,
                        title: 'O documento já está analisado',
                    },
                    unverify: {
                        visible:
                            can('entry-documents:buttons') ||
                            can('entry-documents:verify'),
                        disabled: true,
                        title: !congressmanBudgetClosedAt
                            ? 'Não é possível cancelar a verificação pois o documento está analisado'
                            : closedTitle,
                    },
                    verify: {
                        visible: false,
                        disabled: true,
                        title: !congressmanBudgetClosedAt
                            ? 'O documento já está verificado'
                            : closedTitle,
                    },
                    delete: {
                        visible:
                            can('entry-documents:buttons') ||
                            can('entry-documents:delete'),
                        disabled: true,
                        title: !congressmanBudgetClosedAt
                            ? 'Não é possível apagar o documento pois ele já está verificado e analisado'
                            : closedTitle,
                    },
                },
            }
        } else if (document.verified_at) {
            return {
                name: 'Verificado',
                buttons: {
                    unpublish: {
                        visible:
                            (can('entry-documents:buttons') ||
                                can('entry-documents:publish')) &&
                            document.published_at,
                        disabled: true,
                        title: !congressmanBudgetClosedAt
                            ? 'Não é possível tornar o documento privado pois ele já está verificado'
                            : closedTitle,
                    },
                    publish: {
                        visible:
                            (can('entry-documents:buttons') ||
                                can('entry-documents:publish')) &&
                            !document.published_at,
                        disabled: true,
                        title: !congressmanBudgetClosedAt
                            ? 'Não é possível tornar o documento público pois ele já está verificado'
                            : closedTitle,
                    },
                    unanalyse: {
                        visible: false,
                        disabled: true,
                        title: "Cancelar marcação de 'analisado'",
                    },
                    analyse: {
                        visible: can('entry-documents:analyse'),
                        disabled: !can('entry-documents:analyse'),
                        title: "Marcar orçamento como 'analisado'",
                    },
                    unverify: {
                        visible:
                            can('entry-documents:buttons') ||
                            can('entry-documents:verify'),
                        disabled:
                            !can('entry-documents:verify') ||
                            congressmanBudgetClosedAt,
                        title: !congressmanBudgetClosedAt
                            ? "Cancelar marcação de 'verificado'"
                            : closedTitle,
                    },
                    verify: {
                        visible: false,
                        disabled: true,
                        title: !congressmanBudgetClosedAt
                            ? 'O documento já está verificado'
                            : closedTitle,
                    },
                    delete: {
                        visible:
                            can('entry-documents:buttons') ||
                            can('entry-documents:delete'),
                        disabled: true,
                        title: !congressmanBudgetClosedAt
                            ? 'Não é possível apagar o documento pois ele já está verificado'
                            : closedTitle,
                    },
                },
            }
        } else if (document.published_at) {
            return {
                name: 'Publicável',
                buttons: {
                    unpublish: {
                        visible:
                            can('entry-documents:buttons') ||
                            can('entry-documents:publish'),
                        disabled:
                            !can('entry-documents:publish') ||
                            congressmanBudgetClosedAt,
                        title: !congressmanBudgetClosedAt
                            ? 'Tornar documento privado'
                            : closedTitle,
                    },
                    publish: {
                        visible: false,
                        disabled: true,
                        title: !congressmanBudgetClosedAt
                            ? 'Tornar documento público'
                            : closedTitle,
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
                            'Não é possível analisar o documento pois ele não está verificado',
                    },
                    unverify: {
                        visible: false,
                        disabled: true,
                        title: "'Cancelar marcação de 'verificado'",
                    },
                    verify: {
                        visible:
                            can('entry-documents:buttons') ||
                            can('entry-documents:verify'),
                        disabled:
                            !can('entry-documents:verify') ||
                            congressmanBudgetClosedAt,
                        title: !congressmanBudgetClosedAt
                            ? "Marcar documento como 'verificado'"
                            : closedTitle,
                    },
                    delete: {
                        visible:
                            can('entry-documents:buttons') ||
                            can('entry-documents:delete'),
                        disabled:
                            congressmanBudgetClosedAt ||
                            !can('entry-documents:delete'),
                        title: !congressmanBudgetClosedAt
                            ? 'Apagar documento'
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
                        title: !congressmanBudgetClosedAt
                            ? 'Tornar documento privado'
                            : closedTitle,
                    },
                    publish: {
                        visible:
                            can('entry-documents:buttons') ||
                            can('entry-documents:publish'),
                        disabled:
                            !can('entry-documents:publish') ||
                            congressmanBudgetClosedAt,
                        title: !congressmanBudgetClosedAt
                            ? 'Tornar documento público'
                            : closedTitle,
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
                            'Não é possível analisar o documento pois ele não está verificado',
                    },
                    unverify: {
                        visible: false,
                        disabled: true,
                        title: "'Cancelar marcação de 'verificado'",
                    },
                    verify: {
                        visible:
                            can('entry-documents:buttons') ||
                            can('entry-documents:verify'),
                        disabled:
                            !can('entry-documents:verify') ||
                            congressmanBudgetClosedAt,
                        title: !congressmanBudgetClosedAt
                            ? "Marcar documento como 'verificado'"
                            : closedTitle,
                    },
                    delete: {
                        visible:
                            can('entry-documents:buttons') ||
                            can('entry-documents:delete'),
                        disabled:
                            congressmanBudgetClosedAt ||
                            !can('entry-documents:delete'),
                        title: !congressmanBudgetClosedAt
                            ? 'Apagar documento'
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
