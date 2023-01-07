<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class MaterialAsset extends AssetBundle
{
    public $siteTitle='echoCTF.RED';
    public $logoMini='/images/logo-small.png';
    public $sidebarColor='';
    public $sidebarBackgroundColor='black';
    public $sidebarBackgroundImage='';
    public $basePath='@webroot';
    public $baseUrl='@web';

    public $jsOptions=[
    ];

    public $cssOptions=[
    ];

    public $css=[
        ['//fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons|Roboto+Mono|Orbitron&display=swap', 'async'=>'async', 'crossorigin'=>"anonymous"],
        ['css/all.min.css?v=0.20.3', 'defer'=>'defer'],
        'css/material-dashboard.css?v=0.20.0',
        'css/material.css?v=0.20.3',
    ];

    public $js=[
        '/js/core/popper.min.js?v=0.20.0',
        '/js/core/bootstrap-material-design.min.js?v=0.20.0',
        '/js/plugins/perfect-scrollbar.jquery.min.js?v=0.20.0',
        /* Plugin for the momentJs  */
        ['/js/plugins/moment.min.js','defer'=>'defer'],
        /*  Plugin for Sweet Alert */
        ['/js/plugins/sweetalert2.js','defer'=>'defer'],
        /* Plugin for the Wizard, full documentation here: https://github.com/VinceG/twitter-bootstrap-wizard */
        //'js/plugins/jquery.bootstrap-wizard.js',
        /*  Plugin for Select, full documentation here: http://silviomoreto.github.io/bootstrap-select */
//        'js/plugins/bootstrap-selectpicker.min.js', // XXX FIXME MOVE REGISTER TO profile/settings
        /*  Plugin for the DateTimePicker, full documentation here: https://eonasdan.github.io/bootstrap-datetimepicker/ */
        //'js/plugins/bootstrap-datetimepicker.min.js',
        /*  DataTables.net Plugin, full documentation here: https://datatables.net/  */
        //'js/plugins/jquery.dataTables.min.js',
        /*      Plugin for Tags, full documentation here: https://github.com/bootstrap-tagsinput/bootstrap-tagsinputs  */
        //'js/plugins/bootstrap-tagsinput.js',
        /* Plugin for Fileupload, full documentation here: http://www.jasny.net/bootstrap/javascript/#fileinput */
        //'js/plugins/jasny-bootstrap.min.js',
        /*  Full Calendar Plugin, full documentation here: https://github.com/fullcalendar/fullcalendar    */
        //'js/plugins/fullcalendar.min.js',
        /* Vector Map plugin, full documentation here: http://jvectormap.com/documentation/ */
        //'js/plugins/jquery-jvectormap.js',
        /*  Plugin for the Sliders, full documentation here: http://refreshless.com/nouislider/ */
        //'js/plugins/nouislider.min.js',
        /* Library for adding dinamically elements */
        '/js/plugins/arrive.min.js',
        /* Chartist JS */
        //'js/plugins/chartist.min.js',
        /*  Notifications Plugin    */
        ['js/plugins/bootstrap-notify.min.js','defer'=>'defer'],
        /* Control Center for Material Dashboard: parallax effects, scripts for the example pages etc */
        /* Include a polyfill for ES6 Promises (optional) for IE11, UC Browser and Android browser support SweetAlert */
        //'//cdnjs.cloudflare.com/ajax/libs/core-js/2.4.1/core.min.js',
        /*  Google Maps Plugin   */
        // 'https://maps.googleapis.com/maps/api/js?key=YOUR_KEY_HERE',
        /******/
        //'/js/cookieconsent.min.js', // Move this to only the pages needing it.
        '/js/material-dashboard.js?v=0.20.3',
        ['/js/libechoctf.js?v=0.20.3','defer'=>'defer'],
        // 'js/superfish.js'
    ];

    public $depends=[
        'yii\web\YiiAsset',
        'yii\web\JqueryAsset',
    ];

    public function init()
    {
      parent::init();
    }
}
