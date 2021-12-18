<template>
    <div>
        <v-text-field v-if="field.type === 'email'" :label="field.label" v-model="value" :rules="emailRules" required
                      autocomplete="false"></v-text-field>
        <v-text-field v-if="field.type === 'text'" :label="field.label" v-model="value" required
                      autocomplete="false"></v-text-field>
        <v-text-field v-if="field.type === 'password'" type="password" :label="field.label" v-model="value" required
                      autocomplete="false"></v-text-field>
        <v-text-field v-if="field.type === 'number'" type="number" :label="field.label" v-model="value" required
                      autocomplete="false"></v-text-field>

        <v-checkbox v-if="field.type === 'checkbox'" :label="field.label" v-model="value"></v-checkbox>

        <v-select item-value="id" v-if="field.type === 'select2' || field.type === 'select'" v-model="value"
                  :items="field.all_values" :label="field.label" :item-text="field.attribute"></v-select>

        <v-select multiple item-value="id"
                  v-if="field.type === 'select2_multiple' || field.type === 'checklist' || field.type === 'select_multiple'"
                  v-model="value" :items="field.all_values" :label="field.label"
                  :item-text="field.attribute"></v-select>

        <v-select v-if="field.type === 'enum'" v-model="value" :items="field.all_values"
                  :label="field.label"></v-select>

        <v-layout row wrap v-if="field.type === 'ckeditor'">
            <v-flex xs12 sm6>
                <span class="small pt-4 d-block">{{ field.label }}</span>

                <quill-editor v-if="field.type === 'ckeditor'" v-model="value" :options="quillOptions">

                </quill-editor>
                <br>
                <br>
                <br>
            </v-flex>
        </v-layout>

        <!--        <v-list three-line subheader v-if="field.type === 'select_multiple'" :key="field.kkey">-->
        <!--            <v-subheader>{{ field.label }}</v-subheader>-->
        <!--            <v-list-tile avatar v-for="value in field.all_values" :key="value.kkey">-->
        <!--                <v-list-tile-action>-->
        <!--                    <v-checkbox :value="value.id" v-model="value"></v-checkbox>-->
        <!--                </v-list-tile-action>-->
        <!--                <v-list-tile-content>-->
        <!--                    <v-list-tile-sub-title>{{ value[field.attribute] }}</v-list-tile-sub-title>-->
        <!--                </v-list-tile-content>-->
        <!--            </v-list-tile>-->
        <!--        </v-list>-->

        <v-layout row wrap v-if="field.type === 'date'">
            <v-flex xs12 sm6>
                <span class="small pt-4 d-block">{{ field.label }}</span>
                <datetimepicker v-model="value" :config="dateOptions"></datetimepicker>
            </v-flex>
        </v-layout>

        <v-layout row wrap v-if="field.type === 'date_range'">
            <v-flex xs12 sm6>
                <span class="small pt-4 d-block">{{ field.label }}</span>
                <date-range-picker v-model="value" :id="'daterangepicker_' + field.name" time :start="value.start"
                                   :end="value.end"></date-range-picker>
            </v-flex>
        </v-layout>

        <v-layout row wrap v-if="field.type === 'table'">
            <v-flex xs12 sm6>
                <span class="small pt-4 d-block">{{ field.label }}</span>

                <table width="100%">
                    <tbody>
                    <tr v-for="(value, key) in table" :key="key">
                        <td v-for="(name, prop) in field.columns">
                            <v-text-field type="text" :label="name" v-model="value[prop]" required autocomplete="false"
                                          @input="tableSetValue($event, key, prop)"></v-text-field>
                        </td>
                    </tr>
                    </tbody>
                </table>

                <v-btn icon class="mx-0" @click="addTableRow">
                    <v-icon color="green lighten-1">add</v-icon>
                </v-btn>
            </v-flex>
        </v-layout>

        <v-layout row wrap v-if="field.type === 'inherit_table'">
            <v-flex xs12>
                <span class="small pt-4 d-block">{{ field.label }}</span>

                <table width="100%">
                    <tbody>
                    <tr v-for="(value, key) in table" :key="key">
                        <td v-for="column in field.columns">
                            <c-r-u-d-field :field="column" v-model="value[column.name]"
                                           @input="tableSetValue($event, key, column.name)"
                                           direct="true"></c-r-u-d-field>
                        </td>
                    </tr>
                    </tbody>
                </table>

                <v-btn icon class="mx-0" @click="addTableRow">
                    <v-icon color="green lighten-1">add</v-icon>
                </v-btn>
            </v-flex>
        </v-layout>

        <v-textarea v-if="field.type === 'textarea'" :label="field.label" v-model="value"></v-textarea>
    </div>
</template>

<script>
import 'pc-bootstrap4-datetimepicker/build/css/bootstrap-datetimepicker.css';

import datetimepicker from "vue-bootstrap-datetimepicker";
import DateRangePicker from "../components/DateRangePicker"

export default {
    name: "CRUDField",
    components: {
        datetimepicker, DateRangePicker
    },
    props: ['field', 'value', 'direct'],
    data() {
        return {
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

            if (!this.direct){
                console.log(this.table);
            }
        }
    },
    methods: {
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
        value: function (val) {
            this.$emit('input', val);
        }
    }
}
</script>

<style scoped>

</style>