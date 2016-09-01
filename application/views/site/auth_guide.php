<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.2.0/styles/default.min.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.2.0/highlight.min.js"></script>
<script>hljs.initHighlightingOnLoad();</script>

<div class="row">
    <div class="column column-8 column-offset-2">
        User authentication and authorization are required for a Web page that
        should be limited to certain users.
        <br/><br/>
        Authentication is about verifying whether someone is who they claim to
        be. That, for instance, involve a user email address and password.
        <br/><br/>
        Authorization is finding out if the person, once identified
        (i.e. authenticated), is permitted to manipulate specific resources.
        This is usually determined by finding out if that person is of a
        particular role that has access to the resources.
        <br/><br/>
        Webiness has integrated user authentication and authorization system
        which features:
        <ol>
            <li>
                <a href="doc/class-WsAuth.html" target="_blank">
                    WsAuth
                </a> class which handles user login, logout, creation of new
                accounts, cheking permissions of current user, ...
            </li>
            <li>
                registration form for new users
            </li>
            <li>
                administration interface for user accounts
            </li>
            <li>
                administration interface for role based access (RBAC) managment
            </li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="column column-8 column-offset-2">
        <h1 class="text-primary">1. the basic principles</h1>
        Integrated authentication and authorization system, to be functional,
        need to have access to database server. How to configure database access
        is explained in our <a href="index.php?request=site/guide/">
        basic guide</a>.
        <br><br>
        For accessing administration interfaces, webiness uses special user
        account. Email address for that account is defined in
        <span style="color: #A52A2A;">protected/config/config.php</span> file,
        with option <strong>auth_admin</strong>.
        <pre>
            <code class="php">
                // site administrators email address - needed for WsAuth
                WsConfig::set('auth_admin', 'your_name@your_domain');
            </code>
        </pre>
        Change that code in the way that you put valid email address there.
        Default password for selected email address would be set to
        <strong>admin</strong>. When you login with selected email address,
        which will be explained later, you will always have all grants and also
        you will never be able to delete that account from framework it self.
        <br/>
        <br/>
        When you run first call to
        <a href="doc/class-WsAuth.html" target="_blank">
            WsAuth
        </a> class in your code, it will create all necessary tables in
        database and it will add admin user as it's described above. Internaly
        in database server, created tables will have prefix <strong>ws_</strong>.
    </div>
</div>

<div class="row">
    <div class="column column-8 column-offset-2">
        <h1 class="text-primary">2. checking is user logged in and get basic user informations</h1>
        First thing that we would like to do with our authentication module is
        check if any user is currently logged in session. To do that, we will
        use public method <strong>checkSession()</strong> from
        <a href="doc/class-WsAuth.html" target="_blank">
            WsAuth
        </a> class. For example we may want to allow access to specific
        controller action only for registrated users:
        <pre>
            <code class="php">

                class ExpenseController extends WsController
                {
                    public $auth;

                    public function __construct()
                    {
                        parent::__construct();
                        // initialize user authentication
                        $this->auth = new WsAuth();
                    }


                    /*
                    .
                    .
                    more code here
                    .
                    .
                    */


                    public function expense_report()
                    {
                        // only loggedin users may access to this action
                        if (!$this->auth->checkSession()) {
                            trigger_error('Access Forbiden', E_USER_ERROR);
                            return;
                        }

                        // view response to registrated users
                        $this->render('expense_report');
                    }
                }
            </code>
        </pre>
        You can retrive id and email address of current logged in user using
        public methods <strong>currentUserID()</strong> and
        <strong>currentUser()</strong>:
        <pre>
            <code class="php">
                // initilaize auth object (if it's not initialized before)
                $auth = new WsAuth();

                if ($auth->checkSession()) {
                    // get user ID
                    $id = $auth->currentUserID();
                    // get user email address
                    $email = $auth->currentUser();

                    echo 'user with email address: '.$email.' has ID: '.$id.PHP_EOL;
                }
            </code>
        </pre>
    </div>
</div>

<div class="row">
    <div class="column column-8 column-offset-2">
        <h1 class="text-primary">3. register, login, edit and logout</h1>
        Webiness has builtin actions for user registration, user login,
        edit account details and logout. You can create links to them by
        adding something like this to your view:
        <pre>
            <code class="html">
                &lt;!-- link to registration form --&gt;
                &lt;a href="&lt;?php echo WsUrl::link('wsauth', 'register'); ?&gt;"&gt;
                    register new user
                &lt;/a&gt;

                &lt;!-- link to login form --&gt;
                &lt;a href="&lt;?php echo WsUrl::link('wsauth', 'login'); ?&gt;"&gt;
                    login
                &lt;/a&gt;

                &lt;!-- link to edit form --&gt;
                &lt;a href="&lt;?php echo WsUrl::link('wsauth', 'edit'); ?&gt;"&gt;
                    edit account details for current user
                &lt;/a&gt;

                &lt;!-- logout link --&gt;
                &lt;a href="&lt;?php echo WsUrl::link('wsauth', 'logout'); ?&gt;"&gt;
                    logout current user
                &lt;/a&gt;
            </code>
        </pre>
        <div class="text-center">
        <?php
            $img = new WsImage();
            $img->read('public/img/guide/register_form.png');
            $img->showThumbnail(200, 200, 'register form');
            $img->read('public/img/guide/login_form.png');
            $img->showThumbnail(200, 200, 'login form');
        ?>
        </div>
        When registration process is activated, it will send verification
        mail to entered email address. New user will not be abble to login until
        it clicks on verification link in that message. If, for any reason,
        server is unable to send mail, then admin user must activate account
        manualy. How to do that will be axplain in the next chapter.
        <br/><br/>
        After successful login and logout you will be redirected to site/index
        page.
        <br/><br/>
        In most cases you will wish to add login and logout links to right side
        of page navigation bar. To make this even fancier we will show register
        and login link if there is no logged in user. If we have current user
        then show link his/her email address which will open account settings
        and in submenu show logout link. Open your layout file (by default it
        is: <span style="color: #A52A2A;">public/layouts/webiness.php</span>)
        and add next code:
        <pre>
            <code class="html">
                &lt;!-- HEADER --&gt;
                &lt;div class="row"&gt;
                &lt;!-- original code in default layout --&gt;
                    &lt;div class="column column-8 column-offset-2 ws-header"&gt;
                        &lt;label for="show-menu" class="show-menu"&gt;
                            &lt;?php echo WsConfig::get('app_name'); ?&gt;
                        &lt;/label&gt;
                        &lt;input type="checkbox" id="show-menu" role="button"&gt;
                        &lt;!-- LEFT NAVIGATION BLOCK --&gt;
                        &lt;ul&gt;
                            &lt;li&gt;
                                &lt;a href="&lt;?php echo WsUrl::link('site','index'); ?&gt;"&gt;
                                    index
                                &lt;/a&gt;
                            &lt;/li&gt;
                        &lt;/ul&gt;
                        &lt;!-- END OF LEFT NAVIGATION BLOCK --&gt;
                &lt;!-- end of original code in default layout --&gt;

                        &lt;!-- this is our added code --&gt;
                        &lt;ul class="right"&gt;
                        &lt;?php
                            if ($auth-&gt;checkSession()) {
                            ?&gt;
                            &lt;li class="right"&gt;
                                &lt;a href="&lt;?php echo WsUrl::link('wsauth','edit') ?&gt;"&gt;
                                    &lt;?php echo $auth-&gt;currentUser() ?&gt;
                                &lt;/a&gt;
                                &lt;ul&gt;
                                    &lt;li&gt;
                                        &lt;a href="&lt;?php echo WsUrl::link('wsauth','logout') ?&gt;"&gt;
                                            &lt;?php echo WsLocalize::msg('logout') ?&gt;
                                        &lt;/a&gt;
                                    &lt;/li&gt;
                                    &lt;?php
                                    if ($auth-&gt;hasPermission('admin') != true) {
                                    ?&gt;
                                    &lt;li&gt;
                                        &lt;a href="&lt;?php echo WsUrl::link('wsauth','admin') ?&gt;"&gt;
                                            &lt;?php echo WsLocalize::msg('User Accounts') ?&gt;
                                        &lt;/a&gt;
                                    &lt;/li&gt;
                                    &lt;li&gt;
                                        &lt;a href="&lt;?php echo WsUrl::link('wsauth','userRoles') ?&gt;"&gt;
                                            &lt;?php echo WsLocalize::msg('User Roles') ?&gt;
                                        &lt;/a&gt;
                                    &lt;/li&gt;
                                    &lt;li&gt;
                                        &lt;a href="&lt;?php echo WsUrl::link('wsauth','rolePerms') ?&gt;"&gt;
                                            &lt;?php echo WsLocalize::msg('Role Permissions') ?&gt;
                                        &lt;/a&gt;
                                    &lt;/li&gt;
                                    &lt;?php
                                    }
                                    ?&gt;
                                &lt;/ul&gt;
                            &lt;/li&gt;
                            &lt;?php
                            } else {
                            ?&gt;
                            &lt;li class="right"&gt;
                                &lt;a href="&lt;?php echo WsUrl::link('wsauth','login') ?&gt;"&gt;
                                    &lt;?php echo WsLocalize::msg('login') ?&gt;
                                &lt;/a&gt;
                            &lt;/li&gt;
                            &lt;li class="right"&gt;
                                &lt;a href="&lt;?php echo WsUrl::link('wsauth','register') ?&gt;"&gt;
                                    &lt;?php echo WsLocalize::msg('register') ?&gt;
                                &lt;/a&gt;
                            &lt;/li&gt;
                            &lt;?php
                            }
                            ?&gt;
                        &lt;/ul&gt;
                        &lt;!-- end of added code --&gt;
                    &lt;/div&gt;
                &lt;/div&gt;
            </code>
        </pre>
    </div>
</div>


<div class="row">
    <div class="column column-8 column-offset-2">
        <h1 class="text-primary">4. administrating user accounts, roles and permissions</h1>
        Role based access control (RBAC) is a model in which roles are created
        for various job functions, and permissions to perform certain operations
        are then tied to roles. A user can be assigned one or multiple roles
        which restricts their system access to the permissions for which they
        have been authorized.
        <br/><br/>
        Webiness has integrated system for managing user accounts, roles and
        permissions and also provides interface for managing connections
        between them.
        <br/><br/>
        If you look again in previous code example, you see next code snippet:
        <pre>
            <code class="html">
                &lt;?php
                if ($auth-&gt;hasPermission('admin') != true) {
                ?&gt;
                    &lt;li&gt;
                        &lt;a href="&lt;?php echo WsUrl::link('wsauth','admin') ?&gt;"&gt;
                            &lt;?php echo WsLocalize::msg('User Accounts') ?&gt;
                        &lt;/a&gt;
                    &lt;/li&gt;
                    &lt;li&gt;
                        &lt;a href="&lt;?php echo WsUrl::link('wsauth','userRoles') ?&gt;"&gt;
                            &lt;?php echo WsLocalize::msg('User Roles') ?&gt;
                        &lt;/a&gt;
                    &lt;/li&gt;
                    &lt;li&gt;
                        &lt;a href="&lt;?php echo WsUrl::link('wsauth','rolePerms') ?&gt;"&gt;
                            &lt;?php echo WsLocalize::msg('Role Permissions') ?&gt;
                        &lt;/a&gt;
                    &lt;/li&gt;
                &lt;?php
                }
                ?&gt;
            </code>
        </pre>
        In this code we introduce new public function
        <strong>hasPermission(permission_name)</strong>. That function basically
        say: "if current user has named permission then do something". In our
        case it checks for administrators privilages and if it founds them then
        show links to administrators pages.
        <br/>
        <br/>
        In our example, we create three roles (supervisor, operator and
        statistician) and two types of permissions (add item and see report).
        Users that have (that are in) operator role can add new report,
        users that have statistician role can see reports and users with
        supervisor role can do both. As you can see from screenshots (click to
        enlarge) administration UI is very simple and easy to use.
        <div class="text-center">
        <?php
            $img = new WsImage();
            $img->read('public/img/guide/auth_admin.png');
            $img->showThumbnail(200, 200, 'users, roles and permissions');
            $img->read('public/img/guide/auth_user_role.png');
            $img->showThumbnail(200, 200, 'user roles');
            $img->read('public/img/guide/auth_role_perm.png');
            $img->showThumbnail(200, 200, 'role permissions');
        ?>
        </div>
        Now, lets go back to code in our basic tutorial and allow acces to
        report view only to users with 'supervisor' or 'statistician' roles.
        <pre>
            <code class="php">

                class ExpenseController extends WsController
                {
                    public $auth;

                    public function __construct()
                    {
                        parent::__construct();
                        // initialize user authentication
                        $this->auth = new WsAuth();
                    }


                    /*
                    .
                    .
                    more code here
                    .
                    .
                    */


                    public function expense_report()
                    {
                        // this has changed
                        if (!$this->auth->hasPermission('see report') == true) {
                            trigger_error('Access Forbiden', E_USER_ERROR);
                            return;
                        }

                        // view response to registrated users
                        $this->render('expense_report');
                    }
                }
            </code>
        </pre>
        <br/><br/><br/>
        Now you know how to use Webiness authentication and authorization
        system.
    </div>
</div>
