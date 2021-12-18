<template>
    <div v-if="loaded" v-html="page.content" @click="processLinks"></div>
    <loader v-else></loader>
</template>

<script>
    import api from '../api'
    import Loader from "../components/Loader";

    import * as skinview3d from "../skinview3d.module";
    import $ from "jquery";


    export default {
        name: "StaticPage",
        components: {Loader},
        mounted(){
            this.loadPage();
            this.checkRef();
        },
        data(){
            return {
                loaded: false,
                page: false
            }
        },
        methods:{
            processLinks(event) {
                if(event.target){
                    var link = event.target.closest('a');
                    if(link && link.pathname && link.hostname === window.location.hostname){
                        event.preventDefault();
                        console.log('Go to: ' + link.pathname);
                        this.$router.push({ path: link.pathname });
                    }
                }
            },
            loadPage(){
                this.loaded = false;

                api.get('page/' + this.$route.params.name)
                .then(response => {
                    this.page = response.data.page;
                    this.loaded = true;

                    this.$nextTick(function () {
                       this.loadSkins();
                    });
                }).catch(error => {
                    console.log(error);
                });
            },
            checkRef(){
                var ref = this.$route.query.ref;
                if (ref){
                    localStorage.setItem('refer', ref);
                }
            },
            loadSkins(){
                $(document).on('click ','.rules h3', function(e){
                    e.preventDefault();
                    let i = $(this),
                        c = 'checked',
                        b = i.parent().hasClass(c);

                    $('.rules .chapter').each(function(){
                        $(this).removeClass(c)
                    });
                    (!b) ? i.parent().addClass(c) : null;
                });

                if (this.$route.params.name === 'donate'){
                    $(document).on('click','.donate .switcher .case', function(e) {
                        e.preventDefault();
                        let switcher = $('.donate .switcher'),
                            i = $(this),
                            c = 'checked',
                            b = i.hasClass(c),
                            id = i.attr('href').slice(1),
                            groupment = $('.groupment');
                        switcher.find('.case').each(function(){
                            $(this).removeClass(c)
                        });
                        groupment.find('> li').each(function(){
                            $(this).hide(0);
                        });
                        (!b) ? i.addClass(c) : null;
                        groupment.find(`#${id}`).fadeIn(200);
                        $('html').animate({ scrollTop: groupment.offset().top }, 500);
                        $('#accountUpgrade').removeClass('d-none');
                    });

                    $(document).on('mouseenter','.donate .kitTrigger', function() {
                        let i = $(this),
                            c = 'checked',
                            id = i.attr('id');
                        $('.donate .kitTrigger').each(function(){
                            $(this).removeClass(c);
                        });
                        i.addClass(c);
                        $('.donate .kitWrap').each(function(){
                            i = $(this);
                            (i.hasClass(id)) ? i.fadeIn(150) : i.hide();
                        });
                    });
                    $(document).on('mouseleave','.donate .chapter', function() {
                        $('.donate .kitTrigger').each(function(){
                            $(this).removeClass('checked');
                        });
                        $('.donate .kitWrap').each(function(){
                            $(this).hide();
                        });
                    });
                }

                $(document).on('click ','.server .mods a', function(){
                    let attr = $(this).attr('data-value');
                    $(this).attr('href', `https://www.google.com/search?q=${(attr !== undefined) ? attr + ' ' : ''}${$(this).text()}+вики`);
                });

                $('.preview').each(function(){
                    let attr = this.getAttribute('data-value'),
                        path = (attr === null) ? '/skins/default.png' : `/skins/${attr}.png`;
                    let skinViewer = new skinview3d.SkinViewer({
                        domElement: this,
                        width: 512,
                        height: 1024,
                        skinUrl: path,
                        capeUrl: "img/cape.png",
                        static: true
                    });
                    let control = skinview3d.createOrbitControls(skinViewer);
                    control.enableRotate = false;
                    control.enableZoom = false;
                    control.enablePan = false;

                    skinViewer.animation = new skinview3d.CompositeAnimation();

                    let walk = skinViewer.animation.add(skinview3d.WalkingAnimation);

                    setTimeout(function(){
                        walk.paused = true;
                        skinViewer.playerObject.rotation._x = 0.15;
                        skinViewer.playerObject.quaternion._y = 0.1;
                    }, 200);
                });
            }
        },
        watch:{
            '$route' (to, from) {
                if(to.params.name !== from.params.name){
                    this.loadPage();
                }
            }
        }
    }
</script>

<style scoped>

</style>