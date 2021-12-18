<template>
    <v-select class="btn_common select" label="login" :filterable="false" :options="items" @search="onSearch" placeholder="Введите ник игрока" v-model="selected"></v-select>
</template>

<script>
    import api from '../api';
    import _ from "lodash";

    export default {
        name: "UserSelector",
        data: function () {
            return {
                items: [],
                selected: null,
            }
        },
        watch:{
            'selected': function (val) {
                this.$emit('input', val);
                this.$emit('selected', val);
            }
        },
        methods: {
            onSearch (search, loading) {
                loading(true);
                this.search(loading, search, this);
            },
            search: _.debounce((loading, search, vm) => {
                api.get('core/user/find?q=' + search)
                    .then(response => {
                        vm.items = response.data.data;
                        loading(false);
                    })
                    .catch(err => {
                        console.log(err);
                    });
            }, 350)
        }
    }
</script>