<?php

namespace App\Http\Controllers\Admin\Forum;

use App\Http\Controllers\Admin\CrudController;

class CategoryController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | BASIC CRUD INFORMATION
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel("App\Models\Forum\Category");
        $this->crud->setRoute(config('backpack.base.route_prefix', 'admin').'/forum/category');
        $this->crud->setEntityNameStrings('категория', 'Категории');

        // ------ CRUD COLUMNS
        $this->crud->addColumn([
                                'name' => 'name',
                                'label' => 'Название',
                            ]);
        $this->crud->addColumn([
                                'label' => 'Родительская категория',
                                'type' => 'select',
                                'name' => 'parent_id',
                                'entity' => 'parent',
                                'attribute' => 'name',
                                'model' => "App\Models\Forum\Category",
                            ]);
        $this->crud->addColumn([
            'name' => 'order',
            'label' => 'Сортировка',
        ]);

        //CRUD FIELDS

        $this->crud->addField([
            'name' => 'name',
            'label' => 'Название',
        ]);

        $this->crud->addField([
            'name' => 'slug',
            'label' => 'Ссылка (латиница)',
        ]);

        $this->crud->addField([
            'name' => 'description',
            'label' => 'Описание',
            'type' => 'text'
        ]);

        $this->crud->addField([
            'name' => 'icon',
            'label' => 'Иконка',
            'type' => 'select_from_array',
            'options' => $this->icons(),
            'allows_null' => true
        ]);

        $this->crud->addField([
            'label' => 'Родительская категория',
            'type' => 'select',
            'name' => 'parent_id',
            'entity' => 'parent',
            'attribute' => 'name',
            'model' => "App\Models\Forum\Category",
        ]);

        $this->crud->addField([
            'name' => 'order',
            'label' => 'Сортировка',
            'type' => 'number'
        ]);
    }
    
    public function icons(){
        $icons = array (
            'fa-500px' =>
                array (
                    'unicode' => '\f26e',
                    'name' => '500px',
                ),
            'fa-address-book' =>
                array (
                    'unicode' => '\f2b9',
                    'name' => 'Address book',
                ),
            'fa-address-book-o' =>
                array (
                    'unicode' => '\f2ba',
                    'name' => 'Address book o',
                ),
            'fa-address-card' =>
                array (
                    'unicode' => '\f2bb',
                    'name' => 'Address card',
                ),
            'fa-address-card-o' =>
                array (
                    'unicode' => '\f2bc',
                    'name' => 'Address card o',
                ),
            'fa-adjust' =>
                array (
                    'unicode' => '\f042',
                    'name' => 'Adjust',
                ),
            'fa-adn' =>
                array (
                    'unicode' => '\f170',
                    'name' => 'Adn',
                ),
            'fa-align-center' =>
                array (
                    'unicode' => '\f037',
                    'name' => 'Align center',
                ),
            'fa-align-justify' =>
                array (
                    'unicode' => '\f039',
                    'name' => 'Align justify',
                ),
            'fa-align-left' =>
                array (
                    'unicode' => '\f036',
                    'name' => 'Align left',
                ),
            'fa-align-right' =>
                array (
                    'unicode' => '\f038',
                    'name' => 'Align right',
                ),
            'fa-amazon' =>
                array (
                    'unicode' => '\f270',
                    'name' => 'Amazon',
                ),
            'fa-ambulance' =>
                array (
                    'unicode' => '\f0f9',
                    'name' => 'Ambulance',
                ),
            'fa-american-sign-language-interpreting' =>
                array (
                    'unicode' => '\f2a3',
                    'name' => 'American sign language interpreting',
                ),
            'fa-anchor' =>
                array (
                    'unicode' => '\f13d',
                    'name' => 'Anchor',
                ),
            'fa-android' =>
                array (
                    'unicode' => '\f17b',
                    'name' => 'Android',
                ),
            'fa-angellist' =>
                array (
                    'unicode' => '\f209',
                    'name' => 'Angellist',
                ),
            'fa-angle-double-down' =>
                array (
                    'unicode' => '\f103',
                    'name' => 'Angle double down',
                ),
            'fa-angle-double-left' =>
                array (
                    'unicode' => '\f100',
                    'name' => 'Angle double left',
                ),
            'fa-angle-double-right' =>
                array (
                    'unicode' => '\f101',
                    'name' => 'Angle double right',
                ),
            'fa-angle-double-up' =>
                array (
                    'unicode' => '\f102',
                    'name' => 'Angle double up',
                ),
            'fa-angle-down' =>
                array (
                    'unicode' => '\f107',
                    'name' => 'Angle down',
                ),
            'fa-angle-left' =>
                array (
                    'unicode' => '\f104',
                    'name' => 'Angle left',
                ),
            'fa-angle-right' =>
                array (
                    'unicode' => '\f105',
                    'name' => 'Angle right',
                ),
            'fa-angle-up' =>
                array (
                    'unicode' => '\f106',
                    'name' => 'Angle up',
                ),
            'fa-apple' =>
                array (
                    'unicode' => '\f179',
                    'name' => 'Apple',
                ),
            'fa-archive' =>
                array (
                    'unicode' => '\f187',
                    'name' => 'Archive',
                ),
            'fa-area-chart' =>
                array (
                    'unicode' => '\f1fe',
                    'name' => 'Area chart',
                ),
            'fa-arrow-circle-down' =>
                array (
                    'unicode' => '\f0ab',
                    'name' => 'Arrow circle down',
                ),
            'fa-arrow-circle-left' =>
                array (
                    'unicode' => '\f0a8',
                    'name' => 'Arrow circle left',
                ),
            'fa-arrow-circle-o-down' =>
                array (
                    'unicode' => '\f01a',
                    'name' => 'Arrow circle o down',
                ),
            'fa-arrow-circle-o-left' =>
                array (
                    'unicode' => '\f190',
                    'name' => 'Arrow circle o left',
                ),
            'fa-arrow-circle-o-right' =>
                array (
                    'unicode' => '\f18e',
                    'name' => 'Arrow circle o right',
                ),
            'fa-arrow-circle-o-up' =>
                array (
                    'unicode' => '\f01b',
                    'name' => 'Arrow circle o up',
                ),
            'fa-arrow-circle-right' =>
                array (
                    'unicode' => '\f0a9',
                    'name' => 'Arrow circle right',
                ),
            'fa-arrow-circle-up' =>
                array (
                    'unicode' => '\f0aa',
                    'name' => 'Arrow circle up',
                ),
            'fa-arrow-down' =>
                array (
                    'unicode' => '\f063',
                    'name' => 'Arrow down',
                ),
            'fa-arrow-left' =>
                array (
                    'unicode' => '\f060',
                    'name' => 'Arrow left',
                ),
            'fa-arrow-right' =>
                array (
                    'unicode' => '\f061',
                    'name' => 'Arrow right',
                ),
            'fa-arrow-up' =>
                array (
                    'unicode' => '\f062',
                    'name' => 'Arrow up',
                ),
            'fa-arrows' =>
                array (
                    'unicode' => '\f047',
                    'name' => 'Arrows',
                ),
            'fa-arrows-alt' =>
                array (
                    'unicode' => '\f0b2',
                    'name' => 'Arrows alt',
                ),
            'fa-arrows-h' =>
                array (
                    'unicode' => '\f07e',
                    'name' => 'Arrows h',
                ),
            'fa-arrows-v' =>
                array (
                    'unicode' => '\f07d',
                    'name' => 'Arrows v',
                ),
            'fa-assistive-listening-systems' =>
                array (
                    'unicode' => '\f2a2',
                    'name' => 'Assistive listening systems',
                ),
            'fa-asterisk' =>
                array (
                    'unicode' => '\f069',
                    'name' => 'Asterisk',
                ),
            'fa-at' =>
                array (
                    'unicode' => '\f1fa',
                    'name' => 'At',
                ),
            'fa-audio-description' =>
                array (
                    'unicode' => '\f29e',
                    'name' => 'Audio description',
                ),
            'fa-backward' =>
                array (
                    'unicode' => '\f04a',
                    'name' => 'Backward',
                ),
            'fa-balance-scale' =>
                array (
                    'unicode' => '\f24e',
                    'name' => 'Balance scale',
                ),
            'fa-ban' =>
                array (
                    'unicode' => '\f05e',
                    'name' => 'Ban',
                ),
            'fa-bandcamp' =>
                array (
                    'unicode' => '\f2d5',
                    'name' => 'Bandcamp',
                ),
            'fa-bar-chart' =>
                array (
                    'unicode' => '\f080',
                    'name' => 'Bar chart',
                ),
            'fa-barcode' =>
                array (
                    'unicode' => '\f02a',
                    'name' => 'Barcode',
                ),
            'fa-bars' =>
                array (
                    'unicode' => '\f0c9',
                    'name' => 'Bars',
                ),
            'fa-bath' =>
                array (
                    'unicode' => '\f2cd',
                    'name' => 'Bath',
                ),
            'fa-battery-empty' =>
                array (
                    'unicode' => '\f244',
                    'name' => 'Battery empty',
                ),
            'fa-battery-full' =>
                array (
                    'unicode' => '\f240',
                    'name' => 'Battery full',
                ),
            'fa-battery-half' =>
                array (
                    'unicode' => '\f242',
                    'name' => 'Battery half',
                ),
            'fa-battery-quarter' =>
                array (
                    'unicode' => '\f243',
                    'name' => 'Battery quarter',
                ),
            'fa-battery-three-quarters' =>
                array (
                    'unicode' => '\f241',
                    'name' => 'Battery three quarters',
                ),
            'fa-bed' =>
                array (
                    'unicode' => '\f236',
                    'name' => 'Bed',
                ),
            'fa-beer' =>
                array (
                    'unicode' => '\f0fc',
                    'name' => 'Beer',
                ),
            'fa-behance' =>
                array (
                    'unicode' => '\f1b4',
                    'name' => 'Behance',
                ),
            'fa-behance-square' =>
                array (
                    'unicode' => '\f1b5',
                    'name' => 'Behance square',
                ),
            'fa-bell' =>
                array (
                    'unicode' => '\f0f3',
                    'name' => 'Bell',
                ),
            'fa-bell-o' =>
                array (
                    'unicode' => '\f0a2',
                    'name' => 'Bell o',
                ),
            'fa-bell-slash' =>
                array (
                    'unicode' => '\f1f6',
                    'name' => 'Bell slash',
                ),
            'fa-bell-slash-o' =>
                array (
                    'unicode' => '\f1f7',
                    'name' => 'Bell slash o',
                ),
            'fa-bicycle' =>
                array (
                    'unicode' => '\f206',
                    'name' => 'Bicycle',
                ),
            'fa-binoculars' =>
                array (
                    'unicode' => '\f1e5',
                    'name' => 'Binoculars',
                ),
            'fa-birthday-cake' =>
                array (
                    'unicode' => '\f1fd',
                    'name' => 'Birthday cake',
                ),
            'fa-bitbucket' =>
                array (
                    'unicode' => '\f171',
                    'name' => 'Bitbucket',
                ),
            'fa-bitbucket-square' =>
                array (
                    'unicode' => '\f172',
                    'name' => 'Bitbucket square',
                ),
            'fa-black-tie' =>
                array (
                    'unicode' => '\f27e',
                    'name' => 'Black tie',
                ),
            'fa-blind' =>
                array (
                    'unicode' => '\f29d',
                    'name' => 'Blind',
                ),
            'fa-bluetooth' =>
                array (
                    'unicode' => '\f293',
                    'name' => 'Bluetooth',
                ),
            'fa-bluetooth-b' =>
                array (
                    'unicode' => '\f294',
                    'name' => 'Bluetooth b',
                ),
            'fa-bold' =>
                array (
                    'unicode' => '\f032',
                    'name' => 'Bold',
                ),
            'fa-bolt' =>
                array (
                    'unicode' => '\f0e7',
                    'name' => 'Bolt',
                ),
            'fa-bomb' =>
                array (
                    'unicode' => '\f1e2',
                    'name' => 'Bomb',
                ),
            'fa-book' =>
                array (
                    'unicode' => '\f02d',
                    'name' => 'Book',
                ),
            'fa-bookmark' =>
                array (
                    'unicode' => '\f02e',
                    'name' => 'Bookmark',
                ),
            'fa-bookmark-o' =>
                array (
                    'unicode' => '\f097',
                    'name' => 'Bookmark o',
                ),
            'fa-braille' =>
                array (
                    'unicode' => '\f2a1',
                    'name' => 'Braille',
                ),
            'fa-briefcase' =>
                array (
                    'unicode' => '\f0b1',
                    'name' => 'Briefcase',
                ),
            'fa-btc' =>
                array (
                    'unicode' => '\f15a',
                    'name' => 'Btc',
                ),
            'fa-bug' =>
                array (
                    'unicode' => '\f188',
                    'name' => 'Bug',
                ),
            'fa-building' =>
                array (
                    'unicode' => '\f1ad',
                    'name' => 'Building',
                ),
            'fa-building-o' =>
                array (
                    'unicode' => '\f0f7',
                    'name' => 'Building o',
                ),
            'fa-bullhorn' =>
                array (
                    'unicode' => '\f0a1',
                    'name' => 'Bullhorn',
                ),
            'fa-bullseye' =>
                array (
                    'unicode' => '\f140',
                    'name' => 'Bullseye',
                ),
            'fa-bus' =>
                array (
                    'unicode' => '\f207',
                    'name' => 'Bus',
                ),
            'fa-buysellads' =>
                array (
                    'unicode' => '\f20d',
                    'name' => 'Buysellads',
                ),
            'fa-calculator' =>
                array (
                    'unicode' => '\f1ec',
                    'name' => 'Calculator',
                ),
            'fa-calendar' =>
                array (
                    'unicode' => '\f073',
                    'name' => 'Calendar',
                ),
            'fa-calendar-check-o' =>
                array (
                    'unicode' => '\f274',
                    'name' => 'Calendar check o',
                ),
            'fa-calendar-minus-o' =>
                array (
                    'unicode' => '\f272',
                    'name' => 'Calendar minus o',
                ),
            'fa-calendar-o' =>
                array (
                    'unicode' => '\f133',
                    'name' => 'Calendar o',
                ),
            'fa-calendar-plus-o' =>
                array (
                    'unicode' => '\f271',
                    'name' => 'Calendar plus o',
                ),
            'fa-calendar-times-o' =>
                array (
                    'unicode' => '\f273',
                    'name' => 'Calendar times o',
                ),
            'fa-camera' =>
                array (
                    'unicode' => '\f030',
                    'name' => 'Camera',
                ),
            'fa-camera-retro' =>
                array (
                    'unicode' => '\f083',
                    'name' => 'Camera retro',
                ),
            'fa-car' =>
                array (
                    'unicode' => '\f1b9',
                    'name' => 'Car',
                ),
            'fa-caret-down' =>
                array (
                    'unicode' => '\f0d7',
                    'name' => 'Caret down',
                ),
            'fa-caret-left' =>
                array (
                    'unicode' => '\f0d9',
                    'name' => 'Caret left',
                ),
            'fa-caret-right' =>
                array (
                    'unicode' => '\f0da',
                    'name' => 'Caret right',
                ),
            'fa-caret-square-o-down' =>
                array (
                    'unicode' => '\f150',
                    'name' => 'Caret square o down',
                ),
            'fa-caret-square-o-left' =>
                array (
                    'unicode' => '\f191',
                    'name' => 'Caret square o left',
                ),
            'fa-caret-square-o-right' =>
                array (
                    'unicode' => '\f152',
                    'name' => 'Caret square o right',
                ),
            'fa-caret-square-o-up' =>
                array (
                    'unicode' => '\f151',
                    'name' => 'Caret square o up',
                ),
            'fa-caret-up' =>
                array (
                    'unicode' => '\f0d8',
                    'name' => 'Caret up',
                ),
            'fa-cart-arrow-down' =>
                array (
                    'unicode' => '\f218',
                    'name' => 'Cart arrow down',
                ),
            'fa-cart-plus' =>
                array (
                    'unicode' => '\f217',
                    'name' => 'Cart plus',
                ),
            'fa-cc' =>
                array (
                    'unicode' => '\f20a',
                    'name' => 'Cc',
                ),
            'fa-cc-amex' =>
                array (
                    'unicode' => '\f1f3',
                    'name' => 'Cc amex',
                ),
            'fa-cc-diners-club' =>
                array (
                    'unicode' => '\f24c',
                    'name' => 'Cc diners club',
                ),
            'fa-cc-discover' =>
                array (
                    'unicode' => '\f1f2',
                    'name' => 'Cc discover',
                ),
            'fa-cc-jcb' =>
                array (
                    'unicode' => '\f24b',
                    'name' => 'Cc jcb',
                ),
            'fa-cc-mastercard' =>
                array (
                    'unicode' => '\f1f1',
                    'name' => 'Cc mastercard',
                ),
            'fa-cc-paypal' =>
                array (
                    'unicode' => '\f1f4',
                    'name' => 'Cc paypal',
                ),
            'fa-cc-stripe' =>
                array (
                    'unicode' => '\f1f5',
                    'name' => 'Cc stripe',
                ),
            'fa-cc-visa' =>
                array (
                    'unicode' => '\f1f0',
                    'name' => 'Cc visa',
                ),
            'fa-certificate' =>
                array (
                    'unicode' => '\f0a3',
                    'name' => 'Certificate',
                ),
            'fa-chain-broken' =>
                array (
                    'unicode' => '\f127',
                    'name' => 'Chain broken',
                ),
            'fa-check' =>
                array (
                    'unicode' => '\f00c',
                    'name' => 'Check',
                ),
            'fa-check-circle' =>
                array (
                    'unicode' => '\f058',
                    'name' => 'Check circle',
                ),
            'fa-check-circle-o' =>
                array (
                    'unicode' => '\f05d',
                    'name' => 'Check circle o',
                ),
            'fa-check-square' =>
                array (
                    'unicode' => '\f14a',
                    'name' => 'Check square',
                ),
            'fa-check-square-o' =>
                array (
                    'unicode' => '\f046',
                    'name' => 'Check square o',
                ),
            'fa-chevron-circle-down' =>
                array (
                    'unicode' => '\f13a',
                    'name' => 'Chevron circle down',
                ),
            'fa-chevron-circle-left' =>
                array (
                    'unicode' => '\f137',
                    'name' => 'Chevron circle left',
                ),
            'fa-chevron-circle-right' =>
                array (
                    'unicode' => '\f138',
                    'name' => 'Chevron circle right',
                ),
            'fa-chevron-circle-up' =>
                array (
                    'unicode' => '\f139',
                    'name' => 'Chevron circle up',
                ),
            'fa-chevron-down' =>
                array (
                    'unicode' => '\f078',
                    'name' => 'Chevron down',
                ),
            'fa-chevron-left' =>
                array (
                    'unicode' => '\f053',
                    'name' => 'Chevron left',
                ),
            'fa-chevron-right' =>
                array (
                    'unicode' => '\f054',
                    'name' => 'Chevron right',
                ),
            'fa-chevron-up' =>
                array (
                    'unicode' => '\f077',
                    'name' => 'Chevron up',
                ),
            'fa-child' =>
                array (
                    'unicode' => '\f1ae',
                    'name' => 'Child',
                ),
            'fa-chrome' =>
                array (
                    'unicode' => '\f268',
                    'name' => 'Chrome',
                ),
            'fa-circle' =>
                array (
                    'unicode' => '\f111',
                    'name' => 'Circle',
                ),
            'fa-circle-o' =>
                array (
                    'unicode' => '\f10c',
                    'name' => 'Circle o',
                ),
            'fa-circle-o-notch' =>
                array (
                    'unicode' => '\f1ce',
                    'name' => 'Circle o notch',
                ),
            'fa-circle-thin' =>
                array (
                    'unicode' => '\f1db',
                    'name' => 'Circle thin',
                ),
            'fa-clipboard' =>
                array (
                    'unicode' => '\f0ea',
                    'name' => 'Clipboard',
                ),
            'fa-clock-o' =>
                array (
                    'unicode' => '\f017',
                    'name' => 'Clock o',
                ),
            'fa-clone' =>
                array (
                    'unicode' => '\f24d',
                    'name' => 'Clone',
                ),
            'fa-cloud' =>
                array (
                    'unicode' => '\f0c2',
                    'name' => 'Cloud',
                ),
            'fa-cloud-download' =>
                array (
                    'unicode' => '\f0ed',
                    'name' => 'Cloud download',
                ),
            'fa-cloud-upload' =>
                array (
                    'unicode' => '\f0ee',
                    'name' => 'Cloud upload',
                ),
            'fa-code' =>
                array (
                    'unicode' => '\f121',
                    'name' => 'Code',
                ),
            'fa-code-fork' =>
                array (
                    'unicode' => '\f126',
                    'name' => 'Code fork',
                ),
            'fa-codepen' =>
                array (
                    'unicode' => '\f1cb',
                    'name' => 'Codepen',
                ),
            'fa-codiepie' =>
                array (
                    'unicode' => '\f284',
                    'name' => 'Codiepie',
                ),
            'fa-coffee' =>
                array (
                    'unicode' => '\f0f4',
                    'name' => 'Coffee',
                ),
            'fa-cog' =>
                array (
                    'unicode' => '\f013',
                    'name' => 'Cog',
                ),
            'fa-cogs' =>
                array (
                    'unicode' => '\f085',
                    'name' => 'Cogs',
                ),
            'fa-columns' =>
                array (
                    'unicode' => '\f0db',
                    'name' => 'Columns',
                ),
            'fa-comment' =>
                array (
                    'unicode' => '\f075',
                    'name' => 'Comment',
                ),
            'fa-comment-o' =>
                array (
                    'unicode' => '\f0e5',
                    'name' => 'Comment o',
                ),
            'fa-commenting' =>
                array (
                    'unicode' => '\f27a',
                    'name' => 'Commenting',
                ),
            'fa-commenting-o' =>
                array (
                    'unicode' => '\f27b',
                    'name' => 'Commenting o',
                ),
            'fa-comments' =>
                array (
                    'unicode' => '\f086',
                    'name' => 'Comments',
                ),
            'fa-comments-o' =>
                array (
                    'unicode' => '\f0e6',
                    'name' => 'Comments o',
                ),
            'fa-compass' =>
                array (
                    'unicode' => '\f14e',
                    'name' => 'Compass',
                ),
            'fa-compress' =>
                array (
                    'unicode' => '\f066',
                    'name' => 'Compress',
                ),
            'fa-connectdevelop' =>
                array (
                    'unicode' => '\f20e',
                    'name' => 'Connectdevelop',
                ),
            'fa-contao' =>
                array (
                    'unicode' => '\f26d',
                    'name' => 'Contao',
                ),
            'fa-copyright' =>
                array (
                    'unicode' => '\f1f9',
                    'name' => 'Copyright',
                ),
            'fa-creative-commons' =>
                array (
                    'unicode' => '\f25e',
                    'name' => 'Creative commons',
                ),
            'fa-credit-card' =>
                array (
                    'unicode' => '\f09d',
                    'name' => 'Credit card',
                ),
            'fa-credit-card-alt' =>
                array (
                    'unicode' => '\f283',
                    'name' => 'Credit card alt',
                ),
            'fa-crop' =>
                array (
                    'unicode' => '\f125',
                    'name' => 'Crop',
                ),
            'fa-crosshairs' =>
                array (
                    'unicode' => '\f05b',
                    'name' => 'Crosshairs',
                ),
            'fa-css3' =>
                array (
                    'unicode' => '\f13c',
                    'name' => 'Css3',
                ),
            'fa-cube' =>
                array (
                    'unicode' => '\f1b2',
                    'name' => 'Cube',
                ),
            'fa-cubes' =>
                array (
                    'unicode' => '\f1b3',
                    'name' => 'Cubes',
                ),
            'fa-cutlery' =>
                array (
                    'unicode' => '\f0f5',
                    'name' => 'Cutlery',
                ),
            'fa-dashcube' =>
                array (
                    'unicode' => '\f210',
                    'name' => 'Dashcube',
                ),
            'fa-database' =>
                array (
                    'unicode' => '\f1c0',
                    'name' => 'Database',
                ),
            'fa-deaf' =>
                array (
                    'unicode' => '\f2a4',
                    'name' => 'Deaf',
                ),
            'fa-delicious' =>
                array (
                    'unicode' => '\f1a5',
                    'name' => 'Delicious',
                ),
            'fa-desktop' =>
                array (
                    'unicode' => '\f108',
                    'name' => 'Desktop',
                ),
            'fa-deviantart' =>
                array (
                    'unicode' => '\f1bd',
                    'name' => 'Deviantart',
                ),
            'fa-diamond' =>
                array (
                    'unicode' => '\f219',
                    'name' => 'Diamond',
                ),
            'fa-digg' =>
                array (
                    'unicode' => '\f1a6',
                    'name' => 'Digg',
                ),
            'fa-dot-circle-o' =>
                array (
                    'unicode' => '\f192',
                    'name' => 'Dot circle o',
                ),
            'fa-download' =>
                array (
                    'unicode' => '\f019',
                    'name' => 'Download',
                ),
            'fa-dribbble' =>
                array (
                    'unicode' => '\f17d',
                    'name' => 'Dribbble',
                ),
            'fa-dropbox' =>
                array (
                    'unicode' => '\f16b',
                    'name' => 'Dropbox',
                ),
            'fa-drupal' =>
                array (
                    'unicode' => '\f1a9',
                    'name' => 'Drupal',
                ),
            'fa-edge' =>
                array (
                    'unicode' => '\f282',
                    'name' => 'Edge',
                ),
            'fa-eercast' =>
                array (
                    'unicode' => '\f2da',
                    'name' => 'Eercast',
                ),
            'fa-eject' =>
                array (
                    'unicode' => '\f052',
                    'name' => 'Eject',
                ),
            'fa-ellipsis-h' =>
                array (
                    'unicode' => '\f141',
                    'name' => 'Ellipsis h',
                ),
            'fa-ellipsis-v' =>
                array (
                    'unicode' => '\f142',
                    'name' => 'Ellipsis v',
                ),
            'fa-empire' =>
                array (
                    'unicode' => '\f1d1',
                    'name' => 'Empire',
                ),
            'fa-envelope' =>
                array (
                    'unicode' => '\f0e0',
                    'name' => 'Envelope',
                ),
            'fa-envelope-o' =>
                array (
                    'unicode' => '\f003',
                    'name' => 'Envelope o',
                ),
            'fa-envelope-open' =>
                array (
                    'unicode' => '\f2b6',
                    'name' => 'Envelope open',
                ),
            'fa-envelope-open-o' =>
                array (
                    'unicode' => '\f2b7',
                    'name' => 'Envelope open o',
                ),
            'fa-envelope-square' =>
                array (
                    'unicode' => '\f199',
                    'name' => 'Envelope square',
                ),
            'fa-envira' =>
                array (
                    'unicode' => '\f299',
                    'name' => 'Envira',
                ),
            'fa-eraser' =>
                array (
                    'unicode' => '\f12d',
                    'name' => 'Eraser',
                ),
            'fa-etsy' =>
                array (
                    'unicode' => '\f2d7',
                    'name' => 'Etsy',
                ),
            'fa-eur' =>
                array (
                    'unicode' => '\f153',
                    'name' => 'Eur',
                ),
            'fa-exchange' =>
                array (
                    'unicode' => '\f0ec',
                    'name' => 'Exchange',
                ),
            'fa-exclamation' =>
                array (
                    'unicode' => '\f12a',
                    'name' => 'Exclamation',
                ),
            'fa-exclamation-circle' =>
                array (
                    'unicode' => '\f06a',
                    'name' => 'Exclamation circle',
                ),
            'fa-exclamation-triangle' =>
                array (
                    'unicode' => '\f071',
                    'name' => 'Exclamation triangle',
                ),
            'fa-expand' =>
                array (
                    'unicode' => '\f065',
                    'name' => 'Expand',
                ),
            'fa-expeditedssl' =>
                array (
                    'unicode' => '\f23e',
                    'name' => 'Expeditedssl',
                ),
            'fa-external-link' =>
                array (
                    'unicode' => '\f08e',
                    'name' => 'External link',
                ),
            'fa-external-link-square' =>
                array (
                    'unicode' => '\f14c',
                    'name' => 'External link square',
                ),
            'fa-eye' =>
                array (
                    'unicode' => '\f06e',
                    'name' => 'Eye',
                ),
            'fa-eye-slash' =>
                array (
                    'unicode' => '\f070',
                    'name' => 'Eye slash',
                ),
            'fa-eyedropper' =>
                array (
                    'unicode' => '\f1fb',
                    'name' => 'Eyedropper',
                ),
            'fa-facebook' =>
                array (
                    'unicode' => '\f09a',
                    'name' => 'Facebook',
                ),
            'fa-facebook-official' =>
                array (
                    'unicode' => '\f230',
                    'name' => 'Facebook official',
                ),
            'fa-facebook-square' =>
                array (
                    'unicode' => '\f082',
                    'name' => 'Facebook square',
                ),
            'fa-fast-backward' =>
                array (
                    'unicode' => '\f049',
                    'name' => 'Fast backward',
                ),
            'fa-fast-forward' =>
                array (
                    'unicode' => '\f050',
                    'name' => 'Fast forward',
                ),
            'fa-fax' =>
                array (
                    'unicode' => '\f1ac',
                    'name' => 'Fax',
                ),
            'fa-female' =>
                array (
                    'unicode' => '\f182',
                    'name' => 'Female',
                ),
            'fa-fighter-jet' =>
                array (
                    'unicode' => '\f0fb',
                    'name' => 'Fighter jet',
                ),
            'fa-file' =>
                array (
                    'unicode' => '\f15b',
                    'name' => 'File',
                ),
            'fa-file-archive-o' =>
                array (
                    'unicode' => '\f1c6',
                    'name' => 'File archive o',
                ),
            'fa-file-audio-o' =>
                array (
                    'unicode' => '\f1c7',
                    'name' => 'File audio o',
                ),
            'fa-file-code-o' =>
                array (
                    'unicode' => '\f1c9',
                    'name' => 'File code o',
                ),
            'fa-file-excel-o' =>
                array (
                    'unicode' => '\f1c3',
                    'name' => 'File excel o',
                ),
            'fa-file-image-o' =>
                array (
                    'unicode' => '\f1c5',
                    'name' => 'File image o',
                ),
            'fa-file-o' =>
                array (
                    'unicode' => '\f016',
                    'name' => 'File o',
                ),
            'fa-file-pdf-o' =>
                array (
                    'unicode' => '\f1c1',
                    'name' => 'File pdf o',
                ),
            'fa-file-powerpoint-o' =>
                array (
                    'unicode' => '\f1c4',
                    'name' => 'File powerpoint o',
                ),
            'fa-file-text' =>
                array (
                    'unicode' => '\f15c',
                    'name' => 'File text',
                ),
            'fa-file-text-o' =>
                array (
                    'unicode' => '\f0f6',
                    'name' => 'File text o',
                ),
            'fa-file-video-o' =>
                array (
                    'unicode' => '\f1c8',
                    'name' => 'File video o',
                ),
            'fa-file-word-o' =>
                array (
                    'unicode' => '\f1c2',
                    'name' => 'File word o',
                ),
            'fa-files-o' =>
                array (
                    'unicode' => '\f0c5',
                    'name' => 'Files o',
                ),
            'fa-film' =>
                array (
                    'unicode' => '\f008',
                    'name' => 'Film',
                ),
            'fa-filter' =>
                array (
                    'unicode' => '\f0b0',
                    'name' => 'Filter',
                ),
            'fa-fire' =>
                array (
                    'unicode' => '\f06d',
                    'name' => 'Fire',
                ),
            'fa-fire-extinguisher' =>
                array (
                    'unicode' => '\f134',
                    'name' => 'Fire extinguisher',
                ),
            'fa-firefox' =>
                array (
                    'unicode' => '\f269',
                    'name' => 'Firefox',
                ),
            'fa-first-order' =>
                array (
                    'unicode' => '\f2b0',
                    'name' => 'First order',
                ),
            'fa-flag' =>
                array (
                    'unicode' => '\f024',
                    'name' => 'Flag',
                ),
            'fa-flag-checkered' =>
                array (
                    'unicode' => '\f11e',
                    'name' => 'Flag checkered',
                ),
            'fa-flag-o' =>
                array (
                    'unicode' => '\f11d',
                    'name' => 'Flag o',
                ),
            'fa-flask' =>
                array (
                    'unicode' => '\f0c3',
                    'name' => 'Flask',
                ),
            'fa-flickr' =>
                array (
                    'unicode' => '\f16e',
                    'name' => 'Flickr',
                ),
            'fa-floppy-o' =>
                array (
                    'unicode' => '\f0c7',
                    'name' => 'Floppy o',
                ),
            'fa-folder' =>
                array (
                    'unicode' => '\f07b',
                    'name' => 'Folder',
                ),
            'fa-folder-o' =>
                array (
                    'unicode' => '\f114',
                    'name' => 'Folder o',
                ),
            'fa-folder-open' =>
                array (
                    'unicode' => '\f07c',
                    'name' => 'Folder open',
                ),
            'fa-folder-open-o' =>
                array (
                    'unicode' => '\f115',
                    'name' => 'Folder open o',
                ),
            'fa-font' =>
                array (
                    'unicode' => '\f031',
                    'name' => 'Font',
                ),
            'fa-font-awesome' =>
                array (
                    'unicode' => '\f2b4',
                    'name' => 'Font awesome',
                ),
            'fa-fonticons' =>
                array (
                    'unicode' => '\f280',
                    'name' => 'Fonticons',
                ),
            'fa-fort-awesome' =>
                array (
                    'unicode' => '\f286',
                    'name' => 'Fort awesome',
                ),
            'fa-forumbee' =>
                array (
                    'unicode' => '\f211',
                    'name' => 'Forumbee',
                ),
            'fa-forward' =>
                array (
                    'unicode' => '\f04e',
                    'name' => 'Forward',
                ),
            'fa-foursquare' =>
                array (
                    'unicode' => '\f180',
                    'name' => 'Foursquare',
                ),
            'fa-free-code-camp' =>
                array (
                    'unicode' => '\f2c5',
                    'name' => 'Free code camp',
                ),
            'fa-frown-o' =>
                array (
                    'unicode' => '\f119',
                    'name' => 'Frown o',
                ),
            'fa-futbol-o' =>
                array (
                    'unicode' => '\f1e3',
                    'name' => 'Futbol o',
                ),
            'fa-gamepad' =>
                array (
                    'unicode' => '\f11b',
                    'name' => 'Gamepad',
                ),
            'fa-gavel' =>
                array (
                    'unicode' => '\f0e3',
                    'name' => 'Gavel',
                ),
            'fa-gbp' =>
                array (
                    'unicode' => '\f154',
                    'name' => 'Gbp',
                ),
            'fa-genderless' =>
                array (
                    'unicode' => '\f22d',
                    'name' => 'Genderless',
                ),
            'fa-get-pocket' =>
                array (
                    'unicode' => '\f265',
                    'name' => 'Get pocket',
                ),
            'fa-gg' =>
                array (
                    'unicode' => '\f260',
                    'name' => 'Gg',
                ),
            'fa-gg-circle' =>
                array (
                    'unicode' => '\f261',
                    'name' => 'Gg circle',
                ),
            'fa-gift' =>
                array (
                    'unicode' => '\f06b',
                    'name' => 'Gift',
                ),
            'fa-git' =>
                array (
                    'unicode' => '\f1d3',
                    'name' => 'Git',
                ),
            'fa-git-square' =>
                array (
                    'unicode' => '\f1d2',
                    'name' => 'Git square',
                ),
            'fa-github' =>
                array (
                    'unicode' => '\f09b',
                    'name' => 'Github',
                ),
            'fa-github-alt' =>
                array (
                    'unicode' => '\f113',
                    'name' => 'Github alt',
                ),
            'fa-github-square' =>
                array (
                    'unicode' => '\f092',
                    'name' => 'Github square',
                ),
            'fa-gitlab' =>
                array (
                    'unicode' => '\f296',
                    'name' => 'Gitlab',
                ),
            'fa-glass' =>
                array (
                    'unicode' => '\f000',
                    'name' => 'Glass',
                ),
            'fa-glide' =>
                array (
                    'unicode' => '\f2a5',
                    'name' => 'Glide',
                ),
            'fa-glide-g' =>
                array (
                    'unicode' => '\f2a6',
                    'name' => 'Glide g',
                ),
            'fa-globe' =>
                array (
                    'unicode' => '\f0ac',
                    'name' => 'Globe',
                ),
            'fa-google' =>
                array (
                    'unicode' => '\f1a0',
                    'name' => 'Google',
                ),
            'fa-google-plus' =>
                array (
                    'unicode' => '\f0d5',
                    'name' => 'Google plus',
                ),
            'fa-google-plus-official' =>
                array (
                    'unicode' => '\f2b3',
                    'name' => 'Google plus official',
                ),
            'fa-google-plus-square' =>
                array (
                    'unicode' => '\f0d4',
                    'name' => 'Google plus square',
                ),
            'fa-google-wallet' =>
                array (
                    'unicode' => '\f1ee',
                    'name' => 'Google wallet',
                ),
            'fa-graduation-cap' =>
                array (
                    'unicode' => '\f19d',
                    'name' => 'Graduation cap',
                ),
            'fa-gratipay' =>
                array (
                    'unicode' => '\f184',
                    'name' => 'Gratipay',
                ),
            'fa-grav' =>
                array (
                    'unicode' => '\f2d6',
                    'name' => 'Grav',
                ),
            'fa-h-square' =>
                array (
                    'unicode' => '\f0fd',
                    'name' => 'H square',
                ),
            'fa-hacker-news' =>
                array (
                    'unicode' => '\f1d4',
                    'name' => 'Hacker news',
                ),
            'fa-hand-lizard-o' =>
                array (
                    'unicode' => '\f258',
                    'name' => 'Hand lizard o',
                ),
            'fa-hand-o-down' =>
                array (
                    'unicode' => '\f0a7',
                    'name' => 'Hand o down',
                ),
            'fa-hand-o-left' =>
                array (
                    'unicode' => '\f0a5',
                    'name' => 'Hand o left',
                ),
            'fa-hand-o-right' =>
                array (
                    'unicode' => '\f0a4',
                    'name' => 'Hand o right',
                ),
            'fa-hand-o-up' =>
                array (
                    'unicode' => '\f0a6',
                    'name' => 'Hand o up',
                ),
            'fa-hand-paper-o' =>
                array (
                    'unicode' => '\f256',
                    'name' => 'Hand paper o',
                ),
            'fa-hand-peace-o' =>
                array (
                    'unicode' => '\f25b',
                    'name' => 'Hand peace o',
                ),
            'fa-hand-pointer-o' =>
                array (
                    'unicode' => '\f25a',
                    'name' => 'Hand pointer o',
                ),
            'fa-hand-rock-o' =>
                array (
                    'unicode' => '\f255',
                    'name' => 'Hand rock o',
                ),
            'fa-hand-scissors-o' =>
                array (
                    'unicode' => '\f257',
                    'name' => 'Hand scissors o',
                ),
            'fa-hand-spock-o' =>
                array (
                    'unicode' => '\f259',
                    'name' => 'Hand spock o',
                ),
            'fa-handshake-o' =>
                array (
                    'unicode' => '\f2b5',
                    'name' => 'Handshake o',
                ),
            'fa-hashtag' =>
                array (
                    'unicode' => '\f292',
                    'name' => 'Hashtag',
                ),
            'fa-hdd-o' =>
                array (
                    'unicode' => '\f0a0',
                    'name' => 'Hdd o',
                ),
            'fa-header' =>
                array (
                    'unicode' => '\f1dc',
                    'name' => 'Header',
                ),
            'fa-headphones' =>
                array (
                    'unicode' => '\f025',
                    'name' => 'Headphones',
                ),
            'fa-heart' =>
                array (
                    'unicode' => '\f004',
                    'name' => 'Heart',
                ),
            'fa-heart-o' =>
                array (
                    'unicode' => '\f08a',
                    'name' => 'Heart o',
                ),
            'fa-heartbeat' =>
                array (
                    'unicode' => '\f21e',
                    'name' => 'Heartbeat',
                ),
            'fa-history' =>
                array (
                    'unicode' => '\f1da',
                    'name' => 'History',
                ),
            'fa-home' =>
                array (
                    'unicode' => '\f015',
                    'name' => 'Home',
                ),
            'fa-hospital-o' =>
                array (
                    'unicode' => '\f0f8',
                    'name' => 'Hospital o',
                ),
            'fa-hourglass' =>
                array (
                    'unicode' => '\f254',
                    'name' => 'Hourglass',
                ),
            'fa-hourglass-end' =>
                array (
                    'unicode' => '\f253',
                    'name' => 'Hourglass end',
                ),
            'fa-hourglass-half' =>
                array (
                    'unicode' => '\f252',
                    'name' => 'Hourglass half',
                ),
            'fa-hourglass-o' =>
                array (
                    'unicode' => '\f250',
                    'name' => 'Hourglass o',
                ),
            'fa-hourglass-start' =>
                array (
                    'unicode' => '\f251',
                    'name' => 'Hourglass start',
                ),
            'fa-houzz' =>
                array (
                    'unicode' => '\f27c',
                    'name' => 'Houzz',
                ),
            'fa-html5' =>
                array (
                    'unicode' => '\f13b',
                    'name' => 'Html5',
                ),
            'fa-i-cursor' =>
                array (
                    'unicode' => '\f246',
                    'name' => 'I cursor',
                ),
            'fa-id-badge' =>
                array (
                    'unicode' => '\f2c1',
                    'name' => 'Id badge',
                ),
            'fa-id-card' =>
                array (
                    'unicode' => '\f2c2',
                    'name' => 'Id card',
                ),
            'fa-id-card-o' =>
                array (
                    'unicode' => '\f2c3',
                    'name' => 'Id card o',
                ),
            'fa-ils' =>
                array (
                    'unicode' => '\f20b',
                    'name' => 'Ils',
                ),
            'fa-imdb' =>
                array (
                    'unicode' => '\f2d8',
                    'name' => 'Imdb',
                ),
            'fa-inbox' =>
                array (
                    'unicode' => '\f01c',
                    'name' => 'Inbox',
                ),
            'fa-indent' =>
                array (
                    'unicode' => '\f03c',
                    'name' => 'Indent',
                ),
            'fa-industry' =>
                array (
                    'unicode' => '\f275',
                    'name' => 'Industry',
                ),
            'fa-info' =>
                array (
                    'unicode' => '\f129',
                    'name' => 'Info',
                ),
            'fa-info-circle' =>
                array (
                    'unicode' => '\f05a',
                    'name' => 'Info circle',
                ),
            'fa-inr' =>
                array (
                    'unicode' => '\f156',
                    'name' => 'Inr',
                ),
            'fa-instagram' =>
                array (
                    'unicode' => '\f16d',
                    'name' => 'Instagram',
                ),
            'fa-internet-explorer' =>
                array (
                    'unicode' => '\f26b',
                    'name' => 'Internet explorer',
                ),
            'fa-ioxhost' =>
                array (
                    'unicode' => '\f208',
                    'name' => 'Ioxhost',
                ),
            'fa-italic' =>
                array (
                    'unicode' => '\f033',
                    'name' => 'Italic',
                ),
            'fa-joomla' =>
                array (
                    'unicode' => '\f1aa',
                    'name' => 'Joomla',
                ),
            'fa-jpy' =>
                array (
                    'unicode' => '\f157',
                    'name' => 'Jpy',
                ),
            'fa-jsfiddle' =>
                array (
                    'unicode' => '\f1cc',
                    'name' => 'Jsfiddle',
                ),
            'fa-key' =>
                array (
                    'unicode' => '\f084',
                    'name' => 'Key',
                ),
            'fa-keyboard-o' =>
                array (
                    'unicode' => '\f11c',
                    'name' => 'Keyboard o',
                ),
            'fa-krw' =>
                array (
                    'unicode' => '\f159',
                    'name' => 'Krw',
                ),
            'fa-language' =>
                array (
                    'unicode' => '\f1ab',
                    'name' => 'Language',
                ),
            'fa-laptop' =>
                array (
                    'unicode' => '\f109',
                    'name' => 'Laptop',
                ),
            'fa-lastfm' =>
                array (
                    'unicode' => '\f202',
                    'name' => 'Lastfm',
                ),
            'fa-lastfm-square' =>
                array (
                    'unicode' => '\f203',
                    'name' => 'Lastfm square',
                ),
            'fa-leaf' =>
                array (
                    'unicode' => '\f06c',
                    'name' => 'Leaf',
                ),
            'fa-leanpub' =>
                array (
                    'unicode' => '\f212',
                    'name' => 'Leanpub',
                ),
            'fa-lemon-o' =>
                array (
                    'unicode' => '\f094',
                    'name' => 'Lemon o',
                ),
            'fa-level-down' =>
                array (
                    'unicode' => '\f149',
                    'name' => 'Level down',
                ),
            'fa-level-up' =>
                array (
                    'unicode' => '\f148',
                    'name' => 'Level up',
                ),
            'fa-life-ring' =>
                array (
                    'unicode' => '\f1cd',
                    'name' => 'Life ring',
                ),
            'fa-lightbulb-o' =>
                array (
                    'unicode' => '\f0eb',
                    'name' => 'Lightbulb o',
                ),
            'fa-line-chart' =>
                array (
                    'unicode' => '\f201',
                    'name' => 'Line chart',
                ),
            'fa-link' =>
                array (
                    'unicode' => '\f0c1',
                    'name' => 'Link',
                ),
            'fa-linkedin' =>
                array (
                    'unicode' => '\f0e1',
                    'name' => 'Linkedin',
                ),
            'fa-linkedin-square' =>
                array (
                    'unicode' => '\f08c',
                    'name' => 'Linkedin square',
                ),
            'fa-linode' =>
                array (
                    'unicode' => '\f2b8',
                    'name' => 'Linode',
                ),
            'fa-linux' =>
                array (
                    'unicode' => '\f17c',
                    'name' => 'Linux',
                ),
            'fa-list' =>
                array (
                    'unicode' => '\f03a',
                    'name' => 'List',
                ),
            'fa-list-alt' =>
                array (
                    'unicode' => '\f022',
                    'name' => 'List alt',
                ),
            'fa-list-ol' =>
                array (
                    'unicode' => '\f0cb',
                    'name' => 'List ol',
                ),
            'fa-list-ul' =>
                array (
                    'unicode' => '\f0ca',
                    'name' => 'List ul',
                ),
            'fa-location-arrow' =>
                array (
                    'unicode' => '\f124',
                    'name' => 'Location arrow',
                ),
            'fa-lock' =>
                array (
                    'unicode' => '\f023',
                    'name' => 'Lock',
                ),
            'fa-long-arrow-down' =>
                array (
                    'unicode' => '\f175',
                    'name' => 'Long arrow down',
                ),
            'fa-long-arrow-left' =>
                array (
                    'unicode' => '\f177',
                    'name' => 'Long arrow left',
                ),
            'fa-long-arrow-right' =>
                array (
                    'unicode' => '\f178',
                    'name' => 'Long arrow right',
                ),
            'fa-long-arrow-up' =>
                array (
                    'unicode' => '\f176',
                    'name' => 'Long arrow up',
                ),
            'fa-low-vision' =>
                array (
                    'unicode' => '\f2a8',
                    'name' => 'Low vision',
                ),
            'fa-magic' =>
                array (
                    'unicode' => '\f0d0',
                    'name' => 'Magic',
                ),
            'fa-magnet' =>
                array (
                    'unicode' => '\f076',
                    'name' => 'Magnet',
                ),
            'fa-male' =>
                array (
                    'unicode' => '\f183',
                    'name' => 'Male',
                ),
            'fa-map' =>
                array (
                    'unicode' => '\f279',
                    'name' => 'Map',
                ),
            'fa-map-marker' =>
                array (
                    'unicode' => '\f041',
                    'name' => 'Map marker',
                ),
            'fa-map-o' =>
                array (
                    'unicode' => '\f278',
                    'name' => 'Map o',
                ),
            'fa-map-pin' =>
                array (
                    'unicode' => '\f276',
                    'name' => 'Map pin',
                ),
            'fa-map-signs' =>
                array (
                    'unicode' => '\f277',
                    'name' => 'Map signs',
                ),
            'fa-mars' =>
                array (
                    'unicode' => '\f222',
                    'name' => 'Mars',
                ),
            'fa-mars-double' =>
                array (
                    'unicode' => '\f227',
                    'name' => 'Mars double',
                ),
            'fa-mars-stroke' =>
                array (
                    'unicode' => '\f229',
                    'name' => 'Mars stroke',
                ),
            'fa-mars-stroke-h' =>
                array (
                    'unicode' => '\f22b',
                    'name' => 'Mars stroke h',
                ),
            'fa-mars-stroke-v' =>
                array (
                    'unicode' => '\f22a',
                    'name' => 'Mars stroke v',
                ),
            'fa-maxcdn' =>
                array (
                    'unicode' => '\f136',
                    'name' => 'Maxcdn',
                ),
            'fa-meanpath' =>
                array (
                    'unicode' => '\f20c',
                    'name' => 'Meanpath',
                ),
            'fa-medium' =>
                array (
                    'unicode' => '\f23a',
                    'name' => 'Medium',
                ),
            'fa-medkit' =>
                array (
                    'unicode' => '\f0fa',
                    'name' => 'Medkit',
                ),
            'fa-meetup' =>
                array (
                    'unicode' => '\f2e0',
                    'name' => 'Meetup',
                ),
            'fa-meh-o' =>
                array (
                    'unicode' => '\f11a',
                    'name' => 'Meh o',
                ),
            'fa-mercury' =>
                array (
                    'unicode' => '\f223',
                    'name' => 'Mercury',
                ),
            'fa-microchip' =>
                array (
                    'unicode' => '\f2db',
                    'name' => 'Microchip',
                ),
            'fa-microphone' =>
                array (
                    'unicode' => '\f130',
                    'name' => 'Microphone',
                ),
            'fa-microphone-slash' =>
                array (
                    'unicode' => '\f131',
                    'name' => 'Microphone slash',
                ),
            'fa-minus' =>
                array (
                    'unicode' => '\f068',
                    'name' => 'Minus',
                ),
            'fa-minus-circle' =>
                array (
                    'unicode' => '\f056',
                    'name' => 'Minus circle',
                ),
            'fa-minus-square' =>
                array (
                    'unicode' => '\f146',
                    'name' => 'Minus square',
                ),
            'fa-minus-square-o' =>
                array (
                    'unicode' => '\f147',
                    'name' => 'Minus square o',
                ),
            'fa-mixcloud' =>
                array (
                    'unicode' => '\f289',
                    'name' => 'Mixcloud',
                ),
            'fa-mobile' =>
                array (
                    'unicode' => '\f10b',
                    'name' => 'Mobile',
                ),
            'fa-modx' =>
                array (
                    'unicode' => '\f285',
                    'name' => 'Modx',
                ),
            'fa-money' =>
                array (
                    'unicode' => '\f0d6',
                    'name' => 'Money',
                ),
            'fa-moon-o' =>
                array (
                    'unicode' => '\f186',
                    'name' => 'Moon o',
                ),
            'fa-motorcycle' =>
                array (
                    'unicode' => '\f21c',
                    'name' => 'Motorcycle',
                ),
            'fa-mouse-pointer' =>
                array (
                    'unicode' => '\f245',
                    'name' => 'Mouse pointer',
                ),
            'fa-music' =>
                array (
                    'unicode' => '\f001',
                    'name' => 'Music',
                ),
            'fa-neuter' =>
                array (
                    'unicode' => '\f22c',
                    'name' => 'Neuter',
                ),
            'fa-newspaper-o' =>
                array (
                    'unicode' => '\f1ea',
                    'name' => 'Newspaper o',
                ),
            'fa-object-group' =>
                array (
                    'unicode' => '\f247',
                    'name' => 'Object group',
                ),
            'fa-object-ungroup' =>
                array (
                    'unicode' => '\f248',
                    'name' => 'Object ungroup',
                ),
            'fa-odnoklassniki' =>
                array (
                    'unicode' => '\f263',
                    'name' => 'Odnoklassniki',
                ),
            'fa-odnoklassniki-square' =>
                array (
                    'unicode' => '\f264',
                    'name' => 'Odnoklassniki square',
                ),
            'fa-opencart' =>
                array (
                    'unicode' => '\f23d',
                    'name' => 'Opencart',
                ),
            'fa-openid' =>
                array (
                    'unicode' => '\f19b',
                    'name' => 'Openid',
                ),
            'fa-opera' =>
                array (
                    'unicode' => '\f26a',
                    'name' => 'Opera',
                ),
            'fa-optin-monster' =>
                array (
                    'unicode' => '\f23c',
                    'name' => 'Optin monster',
                ),
            'fa-outdent' =>
                array (
                    'unicode' => '\f03b',
                    'name' => 'Outdent',
                ),
            'fa-pagelines' =>
                array (
                    'unicode' => '\f18c',
                    'name' => 'Pagelines',
                ),
            'fa-paint-brush' =>
                array (
                    'unicode' => '\f1fc',
                    'name' => 'Paint brush',
                ),
            'fa-paper-plane' =>
                array (
                    'unicode' => '\f1d8',
                    'name' => 'Paper plane',
                ),
            'fa-paper-plane-o' =>
                array (
                    'unicode' => '\f1d9',
                    'name' => 'Paper plane o',
                ),
            'fa-paperclip' =>
                array (
                    'unicode' => '\f0c6',
                    'name' => 'Paperclip',
                ),
            'fa-paragraph' =>
                array (
                    'unicode' => '\f1dd',
                    'name' => 'Paragraph',
                ),
            'fa-pause' =>
                array (
                    'unicode' => '\f04c',
                    'name' => 'Pause',
                ),
            'fa-pause-circle' =>
                array (
                    'unicode' => '\f28b',
                    'name' => 'Pause circle',
                ),
            'fa-pause-circle-o' =>
                array (
                    'unicode' => '\f28c',
                    'name' => 'Pause circle o',
                ),
            'fa-paw' =>
                array (
                    'unicode' => '\f1b0',
                    'name' => 'Paw',
                ),
            'fa-paypal' =>
                array (
                    'unicode' => '\f1ed',
                    'name' => 'Paypal',
                ),
            'fa-pencil' =>
                array (
                    'unicode' => '\f040',
                    'name' => 'Pencil',
                ),
            'fa-pencil-square' =>
                array (
                    'unicode' => '\f14b',
                    'name' => 'Pencil square',
                ),
            'fa-pencil-square-o' =>
                array (
                    'unicode' => '\f044',
                    'name' => 'Pencil square o',
                ),
            'fa-percent' =>
                array (
                    'unicode' => '\f295',
                    'name' => 'Percent',
                ),
            'fa-phone' =>
                array (
                    'unicode' => '\f095',
                    'name' => 'Phone',
                ),
            'fa-phone-square' =>
                array (
                    'unicode' => '\f098',
                    'name' => 'Phone square',
                ),
            'fa-picture-o' =>
                array (
                    'unicode' => '\f03e',
                    'name' => 'Picture o',
                ),
            'fa-pie-chart' =>
                array (
                    'unicode' => '\f200',
                    'name' => 'Pie chart',
                ),
            'fa-pied-piper' =>
                array (
                    'unicode' => '\f2ae',
                    'name' => 'Pied piper',
                ),
            'fa-pied-piper-alt' =>
                array (
                    'unicode' => '\f1a8',
                    'name' => 'Pied piper alt',
                ),
            'fa-pied-piper-pp' =>
                array (
                    'unicode' => '\f1a7',
                    'name' => 'Pied piper pp',
                ),
            'fa-pinterest' =>
                array (
                    'unicode' => '\f0d2',
                    'name' => 'Pinterest',
                ),
            'fa-pinterest-p' =>
                array (
                    'unicode' => '\f231',
                    'name' => 'Pinterest p',
                ),
            'fa-pinterest-square' =>
                array (
                    'unicode' => '\f0d3',
                    'name' => 'Pinterest square',
                ),
            'fa-plane' =>
                array (
                    'unicode' => '\f072',
                    'name' => 'Plane',
                ),
            'fa-play' =>
                array (
                    'unicode' => '\f04b',
                    'name' => 'Play',
                ),
            'fa-play-circle' =>
                array (
                    'unicode' => '\f144',
                    'name' => 'Play circle',
                ),
            'fa-play-circle-o' =>
                array (
                    'unicode' => '\f01d',
                    'name' => 'Play circle o',
                ),
            'fa-plug' =>
                array (
                    'unicode' => '\f1e6',
                    'name' => 'Plug',
                ),
            'fa-plus' =>
                array (
                    'unicode' => '\f067',
                    'name' => 'Plus',
                ),
            'fa-plus-circle' =>
                array (
                    'unicode' => '\f055',
                    'name' => 'Plus circle',
                ),
            'fa-plus-square' =>
                array (
                    'unicode' => '\f0fe',
                    'name' => 'Plus square',
                ),
            'fa-plus-square-o' =>
                array (
                    'unicode' => '\f196',
                    'name' => 'Plus square o',
                ),
            'fa-podcast' =>
                array (
                    'unicode' => '\f2ce',
                    'name' => 'Podcast',
                ),
            'fa-power-off' =>
                array (
                    'unicode' => '\f011',
                    'name' => 'Power off',
                ),
            'fa-print' =>
                array (
                    'unicode' => '\f02f',
                    'name' => 'Print',
                ),
            'fa-product-hunt' =>
                array (
                    'unicode' => '\f288',
                    'name' => 'Product hunt',
                ),
            'fa-puzzle-piece' =>
                array (
                    'unicode' => '\f12e',
                    'name' => 'Puzzle piece',
                ),
            'fa-qq' =>
                array (
                    'unicode' => '\f1d6',
                    'name' => 'Qq',
                ),
            'fa-qrcode' =>
                array (
                    'unicode' => '\f029',
                    'name' => 'Qrcode',
                ),
            'fa-question' =>
                array (
                    'unicode' => '\f128',
                    'name' => 'Question',
                ),
            'fa-question-circle' =>
                array (
                    'unicode' => '\f059',
                    'name' => 'Question circle',
                ),
            'fa-question-circle-o' =>
                array (
                    'unicode' => '\f29c',
                    'name' => 'Question circle o',
                ),
            'fa-quora' =>
                array (
                    'unicode' => '\f2c4',
                    'name' => 'Quora',
                ),
            'fa-quote-left' =>
                array (
                    'unicode' => '\f10d',
                    'name' => 'Quote left',
                ),
            'fa-quote-right' =>
                array (
                    'unicode' => '\f10e',
                    'name' => 'Quote right',
                ),
            'fa-random' =>
                array (
                    'unicode' => '\f074',
                    'name' => 'Random',
                ),
            'fa-ravelry' =>
                array (
                    'unicode' => '\f2d9',
                    'name' => 'Ravelry',
                ),
            'fa-rebel' =>
                array (
                    'unicode' => '\f1d0',
                    'name' => 'Rebel',
                ),
            'fa-recycle' =>
                array (
                    'unicode' => '\f1b8',
                    'name' => 'Recycle',
                ),
            'fa-reddit' =>
                array (
                    'unicode' => '\f1a1',
                    'name' => 'Reddit',
                ),
            'fa-reddit-alien' =>
                array (
                    'unicode' => '\f281',
                    'name' => 'Reddit alien',
                ),
            'fa-reddit-square' =>
                array (
                    'unicode' => '\f1a2',
                    'name' => 'Reddit square',
                ),
            'fa-refresh' =>
                array (
                    'unicode' => '\f021',
                    'name' => 'Refresh',
                ),
            'fa-registered' =>
                array (
                    'unicode' => '\f25d',
                    'name' => 'Registered',
                ),
            'fa-renren' =>
                array (
                    'unicode' => '\f18b',
                    'name' => 'Renren',
                ),
            'fa-repeat' =>
                array (
                    'unicode' => '\f01e',
                    'name' => 'Repeat',
                ),
            'fa-reply' =>
                array (
                    'unicode' => '\f112',
                    'name' => 'Reply',
                ),
            'fa-reply-all' =>
                array (
                    'unicode' => '\f122',
                    'name' => 'Reply all',
                ),
            'fa-retweet' =>
                array (
                    'unicode' => '\f079',
                    'name' => 'Retweet',
                ),
            'fa-road' =>
                array (
                    'unicode' => '\f018',
                    'name' => 'Road',
                ),
            'fa-rocket' =>
                array (
                    'unicode' => '\f135',
                    'name' => 'Rocket',
                ),
            'fa-rss' =>
                array (
                    'unicode' => '\f09e',
                    'name' => 'Rss',
                ),
            'fa-rss-square' =>
                array (
                    'unicode' => '\f143',
                    'name' => 'Rss square',
                ),
            'fa-rub' =>
                array (
                    'unicode' => '\f158',
                    'name' => 'Rub',
                ),
            'fa-safari' =>
                array (
                    'unicode' => '\f267',
                    'name' => 'Safari',
                ),
            'fa-scissors' =>
                array (
                    'unicode' => '\f0c4',
                    'name' => 'Scissors',
                ),
            'fa-scribd' =>
                array (
                    'unicode' => '\f28a',
                    'name' => 'Scribd',
                ),
            'fa-search' =>
                array (
                    'unicode' => '\f002',
                    'name' => 'Search',
                ),
            'fa-search-minus' =>
                array (
                    'unicode' => '\f010',
                    'name' => 'Search minus',
                ),
            'fa-search-plus' =>
                array (
                    'unicode' => '\f00e',
                    'name' => 'Search plus',
                ),
            'fa-sellsy' =>
                array (
                    'unicode' => '\f213',
                    'name' => 'Sellsy',
                ),
            'fa-server' =>
                array (
                    'unicode' => '\f233',
                    'name' => 'Server',
                ),
            'fa-share' =>
                array (
                    'unicode' => '\f064',
                    'name' => 'Share',
                ),
            'fa-share-alt' =>
                array (
                    'unicode' => '\f1e0',
                    'name' => 'Share alt',
                ),
            'fa-share-alt-square' =>
                array (
                    'unicode' => '\f1e1',
                    'name' => 'Share alt square',
                ),
            'fa-share-square' =>
                array (
                    'unicode' => '\f14d',
                    'name' => 'Share square',
                ),
            'fa-share-square-o' =>
                array (
                    'unicode' => '\f045',
                    'name' => 'Share square o',
                ),
            'fa-shield' =>
                array (
                    'unicode' => '\f132',
                    'name' => 'Shield',
                ),
            'fa-ship' =>
                array (
                    'unicode' => '\f21a',
                    'name' => 'Ship',
                ),
            'fa-shirtsinbulk' =>
                array (
                    'unicode' => '\f214',
                    'name' => 'Shirtsinbulk',
                ),
            'fa-shopping-bag' =>
                array (
                    'unicode' => '\f290',
                    'name' => 'Shopping bag',
                ),
            'fa-shopping-basket' =>
                array (
                    'unicode' => '\f291',
                    'name' => 'Shopping basket',
                ),
            'fa-shopping-cart' =>
                array (
                    'unicode' => '\f07a',
                    'name' => 'Shopping cart',
                ),
            'fa-shower' =>
                array (
                    'unicode' => '\f2cc',
                    'name' => 'Shower',
                ),
            'fa-sign-in' =>
                array (
                    'unicode' => '\f090',
                    'name' => 'Sign in',
                ),
            'fa-sign-language' =>
                array (
                    'unicode' => '\f2a7',
                    'name' => 'Sign language',
                ),
            'fa-sign-out' =>
                array (
                    'unicode' => '\f08b',
                    'name' => 'Sign out',
                ),
            'fa-signal' =>
                array (
                    'unicode' => '\f012',
                    'name' => 'Signal',
                ),
            'fa-simplybuilt' =>
                array (
                    'unicode' => '\f215',
                    'name' => 'Simplybuilt',
                ),
            'fa-sitemap' =>
                array (
                    'unicode' => '\f0e8',
                    'name' => 'Sitemap',
                ),
            'fa-skyatlas' =>
                array (
                    'unicode' => '\f216',
                    'name' => 'Skyatlas',
                ),
            'fa-skype' =>
                array (
                    'unicode' => '\f17e',
                    'name' => 'Skype',
                ),
            'fa-slack' =>
                array (
                    'unicode' => '\f198',
                    'name' => 'Slack',
                ),
            'fa-sliders' =>
                array (
                    'unicode' => '\f1de',
                    'name' => 'Sliders',
                ),
            'fa-slideshare' =>
                array (
                    'unicode' => '\f1e7',
                    'name' => 'Slideshare',
                ),
            'fa-smile-o' =>
                array (
                    'unicode' => '\f118',
                    'name' => 'Smile o',
                ),
            'fa-snapchat' =>
                array (
                    'unicode' => '\f2ab',
                    'name' => 'Snapchat',
                ),
            'fa-snapchat-ghost' =>
                array (
                    'unicode' => '\f2ac',
                    'name' => 'Snapchat ghost',
                ),
            'fa-snapchat-square' =>
                array (
                    'unicode' => '\f2ad',
                    'name' => 'Snapchat square',
                ),
            'fa-snowflake-o' =>
                array (
                    'unicode' => '\f2dc',
                    'name' => 'Snowflake o',
                ),
            'fa-sort' =>
                array (
                    'unicode' => '\f0dc',
                    'name' => 'Sort',
                ),
            'fa-sort-alpha-asc' =>
                array (
                    'unicode' => '\f15d',
                    'name' => 'Sort alpha asc',
                ),
            'fa-sort-alpha-desc' =>
                array (
                    'unicode' => '\f15e',
                    'name' => 'Sort alpha desc',
                ),
            'fa-sort-amount-asc' =>
                array (
                    'unicode' => '\f160',
                    'name' => 'Sort amount asc',
                ),
            'fa-sort-amount-desc' =>
                array (
                    'unicode' => '\f161',
                    'name' => 'Sort amount desc',
                ),
            'fa-sort-asc' =>
                array (
                    'unicode' => '\f0de',
                    'name' => 'Sort asc',
                ),
            'fa-sort-desc' =>
                array (
                    'unicode' => '\f0dd',
                    'name' => 'Sort desc',
                ),
            'fa-sort-numeric-asc' =>
                array (
                    'unicode' => '\f162',
                    'name' => 'Sort numeric asc',
                ),
            'fa-sort-numeric-desc' =>
                array (
                    'unicode' => '\f163',
                    'name' => 'Sort numeric desc',
                ),
            'fa-soundcloud' =>
                array (
                    'unicode' => '\f1be',
                    'name' => 'Soundcloud',
                ),
            'fa-space-shuttle' =>
                array (
                    'unicode' => '\f197',
                    'name' => 'Space shuttle',
                ),
            'fa-spinner' =>
                array (
                    'unicode' => '\f110',
                    'name' => 'Spinner',
                ),
            'fa-spoon' =>
                array (
                    'unicode' => '\f1b1',
                    'name' => 'Spoon',
                ),
            'fa-spotify' =>
                array (
                    'unicode' => '\f1bc',
                    'name' => 'Spotify',
                ),
            'fa-square' =>
                array (
                    'unicode' => '\f0c8',
                    'name' => 'Square',
                ),
            'fa-square-o' =>
                array (
                    'unicode' => '\f096',
                    'name' => 'Square o',
                ),
            'fa-stack-exchange' =>
                array (
                    'unicode' => '\f18d',
                    'name' => 'Stack exchange',
                ),
            'fa-stack-overflow' =>
                array (
                    'unicode' => '\f16c',
                    'name' => 'Stack overflow',
                ),
            'fa-star' =>
                array (
                    'unicode' => '\f005',
                    'name' => 'Star',
                ),
            'fa-star-half' =>
                array (
                    'unicode' => '\f089',
                    'name' => 'Star half',
                ),
            'fa-star-half-o' =>
                array (
                    'unicode' => '\f123',
                    'name' => 'Star half o',
                ),
            'fa-star-o' =>
                array (
                    'unicode' => '\f006',
                    'name' => 'Star o',
                ),
            'fa-steam' =>
                array (
                    'unicode' => '\f1b6',
                    'name' => 'Steam',
                ),
            'fa-steam-square' =>
                array (
                    'unicode' => '\f1b7',
                    'name' => 'Steam square',
                ),
            'fa-step-backward' =>
                array (
                    'unicode' => '\f048',
                    'name' => 'Step backward',
                ),
            'fa-step-forward' =>
                array (
                    'unicode' => '\f051',
                    'name' => 'Step forward',
                ),
            'fa-stethoscope' =>
                array (
                    'unicode' => '\f0f1',
                    'name' => 'Stethoscope',
                ),
            'fa-sticky-note' =>
                array (
                    'unicode' => '\f249',
                    'name' => 'Sticky note',
                ),
            'fa-sticky-note-o' =>
                array (
                    'unicode' => '\f24a',
                    'name' => 'Sticky note o',
                ),
            'fa-stop' =>
                array (
                    'unicode' => '\f04d',
                    'name' => 'Stop',
                ),
            'fa-stop-circle' =>
                array (
                    'unicode' => '\f28d',
                    'name' => 'Stop circle',
                ),
            'fa-stop-circle-o' =>
                array (
                    'unicode' => '\f28e',
                    'name' => 'Stop circle o',
                ),
            'fa-street-view' =>
                array (
                    'unicode' => '\f21d',
                    'name' => 'Street view',
                ),
            'fa-strikethrough' =>
                array (
                    'unicode' => '\f0cc',
                    'name' => 'Strikethrough',
                ),
            'fa-stumbleupon' =>
                array (
                    'unicode' => '\f1a4',
                    'name' => 'Stumbleupon',
                ),
            'fa-stumbleupon-circle' =>
                array (
                    'unicode' => '\f1a3',
                    'name' => 'Stumbleupon circle',
                ),
            'fa-subscript' =>
                array (
                    'unicode' => '\f12c',
                    'name' => 'Subscript',
                ),
            'fa-subway' =>
                array (
                    'unicode' => '\f239',
                    'name' => 'Subway',
                ),
            'fa-suitcase' =>
                array (
                    'unicode' => '\f0f2',
                    'name' => 'Suitcase',
                ),
            'fa-sun-o' =>
                array (
                    'unicode' => '\f185',
                    'name' => 'Sun o',
                ),
            'fa-superpowers' =>
                array (
                    'unicode' => '\f2dd',
                    'name' => 'Superpowers',
                ),
            'fa-superscript' =>
                array (
                    'unicode' => '\f12b',
                    'name' => 'Superscript',
                ),
            'fa-table' =>
                array (
                    'unicode' => '\f0ce',
                    'name' => 'Table',
                ),
            'fa-tablet' =>
                array (
                    'unicode' => '\f10a',
                    'name' => 'Tablet',
                ),
            'fa-tachometer' =>
                array (
                    'unicode' => '\f0e4',
                    'name' => 'Tachometer',
                ),
            'fa-tag' =>
                array (
                    'unicode' => '\f02b',
                    'name' => 'Tag',
                ),
            'fa-tags' =>
                array (
                    'unicode' => '\f02c',
                    'name' => 'Tags',
                ),
            'fa-tasks' =>
                array (
                    'unicode' => '\f0ae',
                    'name' => 'Tasks',
                ),
            'fa-taxi' =>
                array (
                    'unicode' => '\f1ba',
                    'name' => 'Taxi',
                ),
            'fa-telegram' =>
                array (
                    'unicode' => '\f2c6',
                    'name' => 'Telegram',
                ),
            'fa-television' =>
                array (
                    'unicode' => '\f26c',
                    'name' => 'Television',
                ),
            'fa-tencent-weibo' =>
                array (
                    'unicode' => '\f1d5',
                    'name' => 'Tencent weibo',
                ),
            'fa-terminal' =>
                array (
                    'unicode' => '\f120',
                    'name' => 'Terminal',
                ),
            'fa-text-height' =>
                array (
                    'unicode' => '\f034',
                    'name' => 'Text height',
                ),
            'fa-text-width' =>
                array (
                    'unicode' => '\f035',
                    'name' => 'Text width',
                ),
            'fa-th' =>
                array (
                    'unicode' => '\f00a',
                    'name' => 'Th',
                ),
            'fa-th-large' =>
                array (
                    'unicode' => '\f009',
                    'name' => 'Th large',
                ),
            'fa-th-list' =>
                array (
                    'unicode' => '\f00b',
                    'name' => 'Th list',
                ),
            'fa-themeisle' =>
                array (
                    'unicode' => '\f2b2',
                    'name' => 'Themeisle',
                ),
            'fa-thermometer-empty' =>
                array (
                    'unicode' => '\f2cb',
                    'name' => 'Thermometer empty',
                ),
            'fa-thermometer-full' =>
                array (
                    'unicode' => '\f2c7',
                    'name' => 'Thermometer full',
                ),
            'fa-thermometer-half' =>
                array (
                    'unicode' => '\f2c9',
                    'name' => 'Thermometer half',
                ),
            'fa-thermometer-quarter' =>
                array (
                    'unicode' => '\f2ca',
                    'name' => 'Thermometer quarter',
                ),
            'fa-thermometer-three-quarters' =>
                array (
                    'unicode' => '\f2c8',
                    'name' => 'Thermometer three quarters',
                ),
            'fa-thumb-tack' =>
                array (
                    'unicode' => '\f08d',
                    'name' => 'Thumb tack',
                ),
            'fa-thumbs-down' =>
                array (
                    'unicode' => '\f165',
                    'name' => 'Thumbs down',
                ),
            'fa-thumbs-o-down' =>
                array (
                    'unicode' => '\f088',
                    'name' => 'Thumbs o down',
                ),
            'fa-thumbs-o-up' =>
                array (
                    'unicode' => '\f087',
                    'name' => 'Thumbs o up',
                ),
            'fa-thumbs-up' =>
                array (
                    'unicode' => '\f164',
                    'name' => 'Thumbs up',
                ),
            'fa-ticket' =>
                array (
                    'unicode' => '\f145',
                    'name' => 'Ticket',
                ),
            'fa-times' =>
                array (
                    'unicode' => '\f00d',
                    'name' => 'Times',
                ),
            'fa-times-circle' =>
                array (
                    'unicode' => '\f057',
                    'name' => 'Times circle',
                ),
            'fa-times-circle-o' =>
                array (
                    'unicode' => '\f05c',
                    'name' => 'Times circle o',
                ),
            'fa-tint' =>
                array (
                    'unicode' => '\f043',
                    'name' => 'Tint',
                ),
            'fa-toggle-off' =>
                array (
                    'unicode' => '\f204',
                    'name' => 'Toggle off',
                ),
            'fa-toggle-on' =>
                array (
                    'unicode' => '\f205',
                    'name' => 'Toggle on',
                ),
            'fa-trademark' =>
                array (
                    'unicode' => '\f25c',
                    'name' => 'Trademark',
                ),
            'fa-train' =>
                array (
                    'unicode' => '\f238',
                    'name' => 'Train',
                ),
            'fa-transgender' =>
                array (
                    'unicode' => '\f224',
                    'name' => 'Transgender',
                ),
            'fa-transgender-alt' =>
                array (
                    'unicode' => '\f225',
                    'name' => 'Transgender alt',
                ),
            'fa-trash' =>
                array (
                    'unicode' => '\f1f8',
                    'name' => 'Trash',
                ),
            'fa-trash-o' =>
                array (
                    'unicode' => '\f014',
                    'name' => 'Trash o',
                ),
            'fa-tree' =>
                array (
                    'unicode' => '\f1bb',
                    'name' => 'Tree',
                ),
            'fa-trello' =>
                array (
                    'unicode' => '\f181',
                    'name' => 'Trello',
                ),
            'fa-tripadvisor' =>
                array (
                    'unicode' => '\f262',
                    'name' => 'Tripadvisor',
                ),
            'fa-trophy' =>
                array (
                    'unicode' => '\f091',
                    'name' => 'Trophy',
                ),
            'fa-truck' =>
                array (
                    'unicode' => '\f0d1',
                    'name' => 'Truck',
                ),
            'fa-try' =>
                array (
                    'unicode' => '\f195',
                    'name' => 'Try',
                ),
            'fa-tty' =>
                array (
                    'unicode' => '\f1e4',
                    'name' => 'Tty',
                ),
            'fa-tumblr' =>
                array (
                    'unicode' => '\f173',
                    'name' => 'Tumblr',
                ),
            'fa-tumblr-square' =>
                array (
                    'unicode' => '\f174',
                    'name' => 'Tumblr square',
                ),
            'fa-twitch' =>
                array (
                    'unicode' => '\f1e8',
                    'name' => 'Twitch',
                ),
            'fa-twitter' =>
                array (
                    'unicode' => '\f099',
                    'name' => 'Twitter',
                ),
            'fa-twitter-square' =>
                array (
                    'unicode' => '\f081',
                    'name' => 'Twitter square',
                ),
            'fa-umbrella' =>
                array (
                    'unicode' => '\f0e9',
                    'name' => 'Umbrella',
                ),
            'fa-underline' =>
                array (
                    'unicode' => '\f0cd',
                    'name' => 'Underline',
                ),
            'fa-undo' =>
                array (
                    'unicode' => '\f0e2',
                    'name' => 'Undo',
                ),
            'fa-universal-access' =>
                array (
                    'unicode' => '\f29a',
                    'name' => 'Universal access',
                ),
            'fa-university' =>
                array (
                    'unicode' => '\f19c',
                    'name' => 'University',
                ),
            'fa-unlock' =>
                array (
                    'unicode' => '\f09c',
                    'name' => 'Unlock',
                ),
            'fa-unlock-alt' =>
                array (
                    'unicode' => '\f13e',
                    'name' => 'Unlock alt',
                ),
            'fa-upload' =>
                array (
                    'unicode' => '\f093',
                    'name' => 'Upload',
                ),
            'fa-usb' =>
                array (
                    'unicode' => '\f287',
                    'name' => 'Usb',
                ),
            'fa-usd' =>
                array (
                    'unicode' => '\f155',
                    'name' => 'Usd',
                ),
            'fa-user' =>
                array (
                    'unicode' => '\f007',
                    'name' => 'User',
                ),
            'fa-user-circle' =>
                array (
                    'unicode' => '\f2bd',
                    'name' => 'User circle',
                ),
            'fa-user-circle-o' =>
                array (
                    'unicode' => '\f2be',
                    'name' => 'User circle o',
                ),
            'fa-user-md' =>
                array (
                    'unicode' => '\f0f0',
                    'name' => 'User md',
                ),
            'fa-user-o' =>
                array (
                    'unicode' => '\f2c0',
                    'name' => 'User o',
                ),
            'fa-user-plus' =>
                array (
                    'unicode' => '\f234',
                    'name' => 'User plus',
                ),
            'fa-user-secret' =>
                array (
                    'unicode' => '\f21b',
                    'name' => 'User secret',
                ),
            'fa-user-times' =>
                array (
                    'unicode' => '\f235',
                    'name' => 'User times',
                ),
            'fa-users' =>
                array (
                    'unicode' => '\f0c0',
                    'name' => 'Users',
                ),
            'fa-venus' =>
                array (
                    'unicode' => '\f221',
                    'name' => 'Venus',
                ),
            'fa-venus-double' =>
                array (
                    'unicode' => '\f226',
                    'name' => 'Venus double',
                ),
            'fa-venus-mars' =>
                array (
                    'unicode' => '\f228',
                    'name' => 'Venus mars',
                ),
            'fa-viacoin' =>
                array (
                    'unicode' => '\f237',
                    'name' => 'Viacoin',
                ),
            'fa-viadeo' =>
                array (
                    'unicode' => '\f2a9',
                    'name' => 'Viadeo',
                ),
            'fa-viadeo-square' =>
                array (
                    'unicode' => '\f2aa',
                    'name' => 'Viadeo square',
                ),
            'fa-video-camera' =>
                array (
                    'unicode' => '\f03d',
                    'name' => 'Video camera',
                ),
            'fa-vimeo' =>
                array (
                    'unicode' => '\f27d',
                    'name' => 'Vimeo',
                ),
            'fa-vimeo-square' =>
                array (
                    'unicode' => '\f194',
                    'name' => 'Vimeo square',
                ),
            'fa-vine' =>
                array (
                    'unicode' => '\f1ca',
                    'name' => 'Vine',
                ),
            'fa-vk' =>
                array (
                    'unicode' => '\f189',
                    'name' => 'Vk',
                ),
            'fa-volume-control-phone' =>
                array (
                    'unicode' => '\f2a0',
                    'name' => 'Volume control phone',
                ),
            'fa-volume-down' =>
                array (
                    'unicode' => '\f027',
                    'name' => 'Volume down',
                ),
            'fa-volume-off' =>
                array (
                    'unicode' => '\f026',
                    'name' => 'Volume off',
                ),
            'fa-volume-up' =>
                array (
                    'unicode' => '\f028',
                    'name' => 'Volume up',
                ),
            'fa-weibo' =>
                array (
                    'unicode' => '\f18a',
                    'name' => 'Weibo',
                ),
            'fa-weixin' =>
                array (
                    'unicode' => '\f1d7',
                    'name' => 'Weixin',
                ),
            'fa-whatsapp' =>
                array (
                    'unicode' => '\f232',
                    'name' => 'Whatsapp',
                ),
            'fa-wheelchair' =>
                array (
                    'unicode' => '\f193',
                    'name' => 'Wheelchair',
                ),
            'fa-wheelchair-alt' =>
                array (
                    'unicode' => '\f29b',
                    'name' => 'Wheelchair alt',
                ),
            'fa-wifi' =>
                array (
                    'unicode' => '\f1eb',
                    'name' => 'Wifi',
                ),
            'fa-wikipedia-w' =>
                array (
                    'unicode' => '\f266',
                    'name' => 'Wikipedia w',
                ),
            'fa-window-close' =>
                array (
                    'unicode' => '\f2d3',
                    'name' => 'Window close',
                ),
            'fa-window-close-o' =>
                array (
                    'unicode' => '\f2d4',
                    'name' => 'Window close o',
                ),
            'fa-window-maximize' =>
                array (
                    'unicode' => '\f2d0',
                    'name' => 'Window maximize',
                ),
            'fa-window-minimize' =>
                array (
                    'unicode' => '\f2d1',
                    'name' => 'Window minimize',
                ),
            'fa-window-restore' =>
                array (
                    'unicode' => '\f2d2',
                    'name' => 'Window restore',
                ),
            'fa-windows' =>
                array (
                    'unicode' => '\f17a',
                    'name' => 'Windows',
                ),
            'fa-wordpress' =>
                array (
                    'unicode' => '\f19a',
                    'name' => 'Wordpress',
                ),
            'fa-wpbeginner' =>
                array (
                    'unicode' => '\f297',
                    'name' => 'Wpbeginner',
                ),
            'fa-wpexplorer' =>
                array (
                    'unicode' => '\f2de',
                    'name' => 'Wpexplorer',
                ),
            'fa-wpforms' =>
                array (
                    'unicode' => '\f298',
                    'name' => 'Wpforms',
                ),
            'fa-wrench' =>
                array (
                    'unicode' => '\f0ad',
                    'name' => 'Wrench',
                ),
            'fa-xing' =>
                array (
                    'unicode' => '\f168',
                    'name' => 'Xing',
                ),
            'fa-xing-square' =>
                array (
                    'unicode' => '\f169',
                    'name' => 'Xing square',
                ),
            'fa-y-combinator' =>
                array (
                    'unicode' => '\f23b',
                    'name' => 'Y combinator',
                ),
            'fa-yahoo' =>
                array (
                    'unicode' => '\f19e',
                    'name' => 'Yahoo',
                ),
            'fa-yelp' =>
                array (
                    'unicode' => '\f1e9',
                    'name' => 'Yelp',
                ),
            'fa-yoast' =>
                array (
                    'unicode' => '\f2b1',
                    'name' => 'Yoast',
                ),
            'fa-youtube' =>
                array (
                    'unicode' => '\f167',
                    'name' => 'Youtube',
                ),
            'fa-youtube-play' =>
                array (
                    'unicode' => '\f16a',
                    'name' => 'Youtube play',
                ),
            'fa-youtube-square' =>
                array (
                    'unicode' => '\f166',
                    'name' => 'Youtube square',
                ),
        );

        $ret = [];
        foreach ($icons as $icon => $info){
            $ret['fa ' . $icon] = $info['name'];
        }
        return $ret;
    }
}
