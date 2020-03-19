<template>
    <span>
        <label :for="name" class="mb-0 mt-4">{{ label }}</label>

        <textarea
            v-bind:value="value"
            v-on:input="$emit('input', $event.target.value)"
            class="form-control"
            :id="name"
            :required="required"
            :rows="rows"
            :cols="cols"
        ></textarea>

        <small class="text-danger" v-if="form.errors.has(name)">
            {{ form.errors.get(name) }}
        </small>
    </span>
</template>

<script>
    export default {
        props: ['value', 'name', 'rows', 'cols', 'form', 'required', 'label'],
    }
</script>
