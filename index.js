panel.plugin('jan-herman/button-field', {
    fields: {
        button: {
            props: {
                label: String,
                options: Array,
                fields: [Object, Array],
                value: Object,
            },
            emits: ['input'],
            data() {
                return {
                    object: this.value,
                };
            },
            computed: {
                hasFields() {
                    return this.$helper.object.length(this.fields) > 0;
                },
            },
            methods: {
                reset() {
                    this.object = {
                        link: '',
                        text: '',
                    };
                    this.save();
                },
                openPopup() {
                    this.$panel.dialog.open({
                        component: 'k-form-dialog',
                        props: {
                            fields: this.fields,
                            value: this.getSettings(),
                        },
                        on: {
                            submit: (value) => {
                                this.object = { ...this.object, ...value };
                                this.save();
                                this.$panel.dialog.close();
                            },
                        },
                    });
                },
                getSettings() {
                    const value = { ...this.value };
                    delete value.link;
                    delete value.text;

                    return value;
                },
                setKey(name, value) {
                    this.$set(this.object, name, value);
                    this.save();
                },
                save() {
                    this.$emit('input', this.object);
                },
            },
            template: `
                <k-field :label="label" class="jh-button-field">

                    <template #options>
                        <k-button-group layout="collapsed">
                            <k-button
                                :text="$t('jan-herman.button-field.settings')"
                                icon="settings"
                                size="xs"
                                variant="filled"
                                @click="openPopup"
                                v-if="hasFields"
                            />
                            <k-button
                                variant="filled"
                                icon="dots"
                                size="xs"
                                @click="$refs.dropdown.toggle()"
                            />
                            <k-dropdown-content ref="dropdown" align-x="end">
                                <k-dropdown-item icon="trash" @click="reset">{{ $t('jan-herman.button-field.delete') }}</k-dropdown-item>
                            </k-dropdown-content>
                        </k-button-group>
                    </template>

                    <k-grid variant="fields" style="row-gap: var(--spacing-2); --columns: 2">

                        <k-link-field
                            :value="value.link"
                            :options="options"
                            @input="setKey('link', $event)"
                        />

                        <k-input
                            type="text"
                            :placeholder="$t('jan-herman.button-field.text')"
                            icon="title"
                            :value="value.text"
                            @input="setKey('text', $event)"
                        >

                    </k-grid>

                </k-field>
            `,
        },
    },
});
