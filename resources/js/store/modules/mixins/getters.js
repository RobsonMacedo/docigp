export function getQueryFilter(state, getters) {
    return {
        filter: state.data ? state.data.filter : null,
        pagination: state.data ? state.data.links.pagination : null,
        order: state.data ? state.data.order : null,
    }
}

export function getFullQueryFilter(state, getters) {
    return {
        filter: { text: null },
        pagination: { per_page: 0, current_page: 0 },
        order: {},
    }
}

export function getDataUrl(state, getters, rootState) {
    if (state.service && state.service.uri) {
        return buildApiUrl(state.service.uri, rootState)
    }
}

export function getStoreUrl(state, getters, rootState) {
    if (state.service && state.service.uri) {
        return buildApiUrl(state.service.uri, rootState)
    }
}

export function getUpdateUrl(state, getters, rootState) {
    if (state.service && state.service.uri) {
        return buildApiUrl(state.service.uri, rootState)
    }
}

export function getSelected(state, getters) {
    if (state.selected) {
        return state.selected
    }
}
