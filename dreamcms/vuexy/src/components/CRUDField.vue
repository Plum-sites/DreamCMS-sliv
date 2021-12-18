<template>
    <div>
        <b-form-group v-if="field.type === 'email'" :label="field.label">
            <b-form-input v-model="curValue" required autocomplete="off"/>
        </b-form-group>

        <b-form-group v-if="field.type === 'text'" :label="field.label">
            <b-form-input v-model="curValue" required autocomplete="off"/>
        </b-form-group>

        <b-form-group v-if="field.type === 'password'" :label="field.label">
            <b-form-input type="password" v-model="curValue" required autocomplete="off"/>
        </b-form-group>

        <b-form-group v-if="field.type === 'number'" :label="field.label">
            <b-form-input type="number" v-model="curValue" required autocomplete="off"/>
        </b-form-group>

        <b-form-group v-if="field.type === 'checkbox'" :label="field.label">
            <b-form-checkbox v-model="curValue">
                {{ field.label }}
            </b-form-checkbox>
        </b-form-group>


        <b-form-group v-if="field.type === 'select2' || field.type === 'select'" :label="field.label">
            <!--        <v-select-->
            <!--            v-model="value"-->
            <!--            :dir="$store.state.appConfig.isRTL ? 'rtl' : 'ltr'"-->
            <!--            :label="field.label"-->
            <!--            :options="field.all_values"-->
            <!--        />-->
            <v-select item-value="id" v-model="curValue" :options="field.all_values" :label="field.attribute" :reduce="item => item.id"></v-select>
        </b-form-group>

        <b-form-group :label="field.label"
                      v-if="field.type === 'select2_multiple' || field.type === 'checklist' || field.type === 'select_multiple'">
            <v-select multiple item-value="id" v-model="curValue" :options="field.all_values" :label="field.attribute"
                      :reduce="item => item.id"></v-select>
        </b-form-group>

        <b-form-group :label="field.label" v-if="field.type === 'enum'">
            <v-select v-model="curValue" :options="field.all_values" :label="field.attribute"></v-select>
        </b-form-group>

        <b-form-group :label="field.label" v-if="field.type === 'ckeditor'">
            <quill-editor v-model="curValue" :options="quillOptions"></quill-editor>
        </b-form-group>

        <b-form-group :label="field.label" v-if="field.type === 'textarea'">
            <b-form-textarea
                v-model="curValue"
                rows="6"
            />
        </b-form-group>

        <b-form-group :label="field.label" v-if="field.type === 'date'">
            <flat-pickr
                v-model="curValue"
                class="form-control"
            />
        </b-form-group>

        <b-form-group :label="field.label" v-if="field.type === 'date_range'">
            <flat-pickr
                v-model="curValue"
                class="form-control"
            />
        </b-form-group>

        <b-form-group :label="field.label" v-if="field.type === 'table'">
            <table width="100%">
                <tbody>
                <tr v-for="(value, key) in table" :key="key">
                    <td v-for="(name, prop) in field.columns">
                        <b-form-input :placeholder="name" v-model="value[prop]" required autocomplete="off" @input="tableSetValue($event, key, prop)"></b-form-input>
                    </td>
                    <td>
                        <b-button size="sm" variant="danger" @click="removeTableRow(key)">
                            <feather-icon icon="MinusIcon"></feather-icon>
                        </b-button>
                    </td>
                </tr>
                </tbody>
            </table>
            <b-button size="sm" variant="success" @click="addTableRow">
                <feather-icon icon="PlusIcon"></feather-icon>
            </b-button>
        </b-form-group>

        <b-form-group :label="field.label" v-if="field.type === 'inherit_table'">
            <table width="100%">
                <tbody>
                    <tr v-for="(value, key) in table" :key="key">
                        <td v-for="column in field.columns">
                            <c-r-u-d-field :field="column" v-model="value[column.name]" @input="tableSetValue($event, key, column.name)" direct="true"></c-r-u-d-field>
                        </td>
                        <td>
                            <b-button size="sm" variant="danger" @click="removeTableRow(key)">
                                <feather-icon icon="MinusIcon"></feather-icon>
                            </b-button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <b-button size="sm" variant="success" @click="addTableRow">
                <feather-icon icon="PlusIcon"></feather-icon>
            </b-button>
        </b-form-group>
    </div>
</template>

<script>
import 'quill/dist/quill.core.css'
import 'quill/dist/quill.snow.css'
import 'quill/dist/quill.bubble.css'

import vSelect from 'vue-select'
import flatPickr from 'vue-flatpickr-component'
import {BForm, BFormGroup, BFormCheckbox, BFormInput, BFormTextarea, BButton} from 'bootstrap-vue';
import { quillEditor } from 'vue-quill-editor'

export default {
    name: "CRUDField",
    props: ['field', 'value', 'direct'],
    components: {
        quillEditor,
        flatPickr,
        vSelect,
        BForm, BFormGroup, BFormCheckbox, BFormInput, BFormTextarea, BButton
    },
    data() {
        return {
            curValue: null,
            emailRules: [
                v => !!v || "Укажите E-mail!",
                v =>
                    /^\w+([.-]?\w+)*@\w+([.-]?\w+)*(\.\w{2,3})+$/.test(v) ||
                    "E-mail должен быть валиден!"
            ],
            dateOptions: {
                locale: 'ru',
                //format: 'YYYY-MM-DD HH:MM:SS',
                useCurrent: false,
                showTodayButton: true,
                inline: false,
                sideBySide: false,
                keepOpen: true,
                icons: {
                    time: 'zmdi zmdi-time',
                    date: 'zmdi zmdi-calendar-alt',
                    up: 'zmdi zmdi-chevron-up',
                    down: 'zmdi zmdi-chevron-down',
                    previous: 'zmdi zmdi-chevron-left',
                    next: 'zmdi zmdi-chevron-right',
                    today: 'zmdi zmdi-timer',
                    clear: 'zmdi zmdi-delete',
                    close: 'zmdi zmdi-close'
                }
            },
            quillOptions: {
                placeholder: 'Введите текст...'
            },
            table: []
        }
    },
    created() {
        this.curValue = this.value;

        if (this.field.type === 'table' && this.field.value) {
            for (const [key, value] of Object.entries(this.field.value)) {
                for (const [prop, name] of Object.entries(this.field.columns)) {
                    if (!this.table[key]) {
                        this.table[key] = {};
                    }
                    this.table[key][prop] = value[prop];
                }
            }
        }

        if (this.field.type === 'inherit_table' && this.value) {
            console.log("Direct: " + this.direct);

            for (const [key, value] of Object.entries(this.value)) {
                for (const column of this.field.columns) {
                    if (!this.table[key]) {
                        this.table[key] = {};
                    }
                    this.table[key][column.name] = value[column.name];
                }
            }

            if (!this.direct) {
                console.log(this.table);
            }
        }
    },
    methods: {
        removeTableRow(key) {
            this.table = this.table.filter(function (val, index){
                return index !== key;
            });
        },
        addTableRow() {
            var obj = {};

            if (this.field.type === 'inherit_table') {
                for (const column of this.field.columns) {
                    obj[column.name] = "";
                }
            } else {
                for (const [prop, name] of Object.entries(this.field.columns)) {
                    obj[prop] = "";
                }
            }

            this.table.push(obj);
        },
        tableSetValue(e, key, prop) {
            if (!this.table[key]) {
                this.table[key] = {};
            }
            this.table[key][prop] = e;

            if (this.field.type === 'inherit_table') {
                console.log(e);
                console.log(key);
                console.log(prop);
            }

            if (this.direct) {
                this.$emit('input', this.table);
            } else {
                this.$emit('input', JSON.stringify(this.table));
            }
        }
    },
    watch: {
        curValue: function (val) {
            this.$emit('input', val);
        }
    }
}
</script>

<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>