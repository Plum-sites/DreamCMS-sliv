const state = {
        menu: [
            {
                title: 'Форум',
                to: {name: 'forum'}
            },
            {
                title: 'Наши сервера',
                cols: [
                    [
                        {
                            title: 'Сервера 1.7.10',
                            child: [
                                {title: 'SandBox', to: {name: 'page', params: {name: 'sandbox'}}},
                                {title: 'NanoTech', to: {name: 'page', params: {name: 'nanotech'}}},
                                {title: 'Galactic', to: {name: 'page', params: {name: 'galactic'}}},
                                {title: 'TechnoMagic', to: {name: 'page', params: {name: 'technomagic'}}},
                                {title: 'TechnoMagicSky', to: {name: 'page', params: {name: 'technomagicsky'}}},
                                {title: 'Magic', to: {name: 'page', params: {name: 'magic'}}},
                                {title: 'RPG', to: {name: 'page', params: {name: 'rpg'}}},
                                {title: 'Regrowth', to: {name: 'page', params: {name: 'regrowth'}}},
                                {title: 'GregTech', to: {name: 'page', params: {name: 'gregtech'}}}
                            ]
                        }
                    ],
                    [
                        {
                            title: 'Миниигры',
                            child: [
                                {title: 'MiniGames', to: {name: 'page', params: {name: 'minigames'}}},
                            ]
                        },
                        {
                            title: 'Сервера 1.12.2',
                            child: [
                                {title: 'SandBox', to: {name: 'page', params: {name: 'sandbox-new'}}},
                                {title: 'Magic', to: {name: 'page', params: {name: 'magic-new'}}},
                                {title: 'NanoTech', to: {name: 'page', params: {name: 'nanotech-new'}}},
                                {title: 'Pixelmon', to: {name: 'page', params: {name: 'pixelmon'}}},
                            ]
                        },
                        {
                            title: 'SurvivalMG',
                            child: [
                                {title: 'JediCraft', to: {name: 'page', params: {name: 'jedicraft'}}},
                                {title: 'Z.O.N.A.', to: {name: 'page', params: {name: 'zona'}}},
                            ]
                        }
                    ],
                ]
            },
            {
                title: 'Помощь',
                child: [
                    {title: 'Правила', to: {name: 'page', params: {name: 'rules'}}},
                    {title: 'Вопросы и ответы', to: {name: 'page', params: {name: 'help'}}},
                    {title: 'Скачать лаунчер', to: {name: 'page', params: {name: 'download'}}},
                    {title: 'Команды', to: {name: 'page', params: {name: 'commands'}}},
                    {title: 'Банлист', to: {name: 'banlist'}},
                    {title: 'Стать модератором', to: {name: 'moderentry'}},
                    {title: 'Команда проекта', to: {name: 'page', params: {name: 'team'}}}
                ]
            },
            {
                title: 'Донат',
                class: 'donate',
                to: {name: 'page', params: {name: 'donate'}}
            },
            {
                title: 'Магазин блоков',
                class: 'shop',
                to: {name: 'shop'}
            },
            {
                title: 'Кабинет',
                auth: true,
                to: {name: 'cabinet'}
            }
        ]
    };

const getters = {
    menu: state => state.menu,
};

const actions = {};

const mutations = {};

export default {
    state,
    getters,
    actions,
    mutations
}
