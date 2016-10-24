<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.2.0/styles/default.min.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.2.0/highlight.min.js"></script>
<script>hljs.initHighlightingOnLoad();</script>

<div class="uk-grid">
    <div class="uk-width-9-10 uk-container-center">
    <h1 class="uk-text-primary">Introduction to Webiness</h1>
    <ol>
        <li>
            <h3 class="uk-text-primary">Naming convention</h3>
            <h5 class="uk-text-primary">database tables</h5>
            For best practice, we propose the following naming conventions for
            database tables and columns. Note that they are not required by
            Webiness.
            <br/>
            <br/>
            <ul>
                <li>
                    both database tables and columns are named in lower case.
                </li>
                <li>
                    words in a name should be separated using underscores
                    (e.g. product_order).
                </li>
                <li>
                    table names may be prefixed with a common token such as
                    tbl_. This is especially useful when the tables of an
                    application coexist in the same database with the tables of
                    another application. The two sets of tables can be readily
                    separate by using different table name prefixes.
                </li>
            </ul>
            <br/>
            <h5 class="uk-text-primary">code</h5>
            <ul>
                <li>
                    Webiness recommends naming variables, functions and class
                    types in camel case which capitalizes the first letter of
                    each word in the name and joins them.
                    Variable and function names should have their first word all
                    in lower-case, in order to differentiate from class names
                    (e.g. $basePath, runController(), LinkPager). For private
                    class member variables, it is recommended to prefix their
                    names with an underscore character (e.g. $_actionList).
                </li>
                <li>
                    Because namespace is not supported prior to PHP 5.3.0, it is
                    recommended that classes be named in some unique way to
                    avoid name conflict with third-party classes. For this
                    reason, all Webiness framework classes are prefixed with
                    "Ws_".
                </li>
                <li>
                    A special rule for model and controller class names is that
                    they must be appended with the words Model and Controller.
                    The controller ID is then defined as the class name with
                    first letter in lower case and the word Controller
                    truncated. For example, the PageController class will have
                    the ID page. This rule makes the application more secure.
                    It also makes the URLs related with controllers a bit
                    cleaner (e.g. /index.php?r=page/index instead of
                    /index.php?r=PageController/index).
                </li>
            </ul>
            <br/>
            <h5 class="uk-text-primary">files</h5>
            <ul>
                <li>
                    Class files should be named after the public class they
                    contain. For example, the SiteController class is in the
                    SiteController.php file. A public class is a class that may
                    be used by any other classes. Each class file should contain
                    at most one public class. Private classes (classes that are
                    only used by a single public class) may reside in the same
                    file with the public class
                </li>
                <li>
                    View files should be named after the view name. For example,
                    the index view is in the index.php file. A view file is a
                    PHP script file that contains HTML and PHP code mainly for
                    presentational purpose.
                </li>
            </ul>
        </li>
        <li>
            <h3 class="uk-text-primary">Webiness directory structure</h3>
            After unpacking <a href="<?php echo WsUrl::link('site', 'downloads'); ?>">
            webiness archive</a>, into your Web-accessible folder, you will see
            next directory structure:
            <br/>
            <br/>
            <img src="<?php echo WsUrl::asset('img/guide/dir_structure.png'); ?>"/>
            <h4 class="uk-text-success">application directory</h4>
            This directory contains application logic of your application.
            Becouse webiness framework follows Model-View-Controller (MVC)
            design pattern, in toplevel of this directory you will find three
            additional directories:
            <br/>
            <br/>
            <ul>
                <li>
                    <span class="uk-text-error">controllers: </span>
                    contains controller classes
                </li>
                <li>
                    <span class="uk-text-error">models: </span>
                    contains model classes
                </li>
                <li>
                    <span class="uk-text-error">views: </span>
                    stores controller actions view scripts
                </li>
            </ul>
            <div class="uk-alert uk-alert-warning">
                <strong>Note: </strong>
                for each controller class you must create sub-directory
                under "views" directory and put coresponding view files under
                that directory.
            </div>
            <h4 class="uk-text-success">public directory</h4>
            contains all files that are shared between web pages
            like: css, images, layout file, javascript files, etc.
            <h4 class="uk-text-success">lang directory</h4>
            contains translation text files which are used to
            translate messages from your application into web browser's
            default language.
            <h4 class="uk-text-success">runtime directory</h4>
            stores dynamically generated files.
            <div class="uk-alert uk-alert-warning">
                <strong>Note: </strong>
                Web server must have write permission on this directory
            </div>
            <h4 class="uk-text-success">protected and doc directories</h4>
            these are framework's internal directories. protected directory
            contains framework classes and javascript libraryes and doc contains
            framework's class reference documentation.
            <br/>
            <br/>
        </li>

        <li>
            <h3 class="uk-text-primary">Webiness configuration file</h3>
            Under
            <span style="color: #A52A2A;">
                protected/config</span> directory, there is file named
            <span style="color: #A52A2A;">
                config-example.php</span>. That file contains basic config
            options needed by Webiness.
            <br/><br/>
            All config options are set by calling
            static function <strong>WsConfig::set('config_option', 'config_value')</strong>
            and can be accessed by calling static function
            <strong>WsConfig::get('config_option')</strong>.

            <br/><br/>Config options are divided in two categories:
            <ol>
                <li>
                    reserved options for Webiness framework
                    <ul>
                        <li>
                            <strong>db_driver</strong>
                            - database driver / type of db server
                        </li>
                        <li>
                            <strong>db_host</strong>
                            - TCP/IP address of database server
                        </li>
                        <li>
                            <strong>db_port</strong>
                            - TCP/IP port of database server
                        </li>
                        <li>
                            <strong>db_name</strong>
                            - database name
                        </li>
                        <li>
                            <strong>db_user</strong>
                            - database user
                        </li>
                        <li>
                            <strong>db_password</strong>
                            - password for database user
                        </li>
                        <li>
                            <strong>app_name</strong>
                            - application name
                        </li>
                        <li>
                            <strong>app_stage</strong>
                            - development stage of application (verbosity of error messages)
                        </li>
                        <li>
                            <strong>app_tz</strong>
                            - timezone used for PHP date/time functions
                        </li>
                        <li>
                            <strong>html_layout</strong>
                            - name of default layout file
                        </li>
                        <li>
                            <strong>auth_admin</strong>
                            - email address for site administrator
                        </li>
                        <li>
                            <strong>pretty_urls</strong>
                            - enable or disable use of sementic (pretty) URLs in web application
                        </li>
                    </ul>
                </li>
                <li>
                    other, user defined options
                </li>
            </ol>
            <br/><br/>
            Before you even start to develop with Webiness, you must copy
            (or move/rename) <span style="color: #A52A2A;">
            protected/config/config-example.php</span> file
            to <span style="color: #A52A2A;">protected/config/config.php</span>
            and edit config options to suit your needs.
        </li>
    </ol>
    <br/><br/><br/>
    Read our <a href="<?php echo WsUrl::link('site', 'guide'); ?>">guide</a>
    to learn how to make basic MVC application in Webiness.
    </div>
</div>



