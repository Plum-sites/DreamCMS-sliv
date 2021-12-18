<template>
    <div class="row error_page align-items-center justify-content-center">
        <div class="col-12 col-lg-9">
            <h1>
                <span>4</span>
                <span>0</span>
                <span>4</span>
            </h1>
            <p>С сожалением вынуждены сообщить, что запрашиваемой Вами страницы не существует.</p>
            <p class="mt-3"><router-link :to="{name: 'news'}">Вернуться на главную</router-link></p>
        </div>
    </div>
</template>

<script>
    export default {
        name: "ErrorNotFound"
    }
</script>